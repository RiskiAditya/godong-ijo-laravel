# Technical Design Document

## Feature: Fix Pemesanan Null jadwal_id Bug

## Overview

This design addresses a critical data integrity issue in the Pemesanan (booking) system where `jadwal_id` can be null, causing relationship chain failures when accessing `paket_wisata` information through the `jadwal` relationship. The solution involves:

1. **Database Migration**: Add `paket_wisata_id` column and backfill null `jadwal_id` values
2. **Custom Exception Handling**: Implement `BookingException` for clear error reporting
3. **Webhook Transaction Safety**: Wrap payment webhook handlers in database transactions
4. **Data Validation**: Ensure referential integrity after migration
5. **Code Refactoring**: Use direct `paket_wisata` relationship instead of `jadwal->paket` chain

This fix ensures all `pemesanan` records have valid relationship chains, preventing null reference errors and improving system reliability.

## Architecture

### Component Structure

```
┌─────────────────────────────────────────────────────────────────┐
│                      Pemesanan System                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌──────────────────┐         ┌─────────────────────────┐       │
│  │  BookingException│         │   Migration Script       │       │
│  │   (New Class)    │         │  (Database Layer)        │       │
│  ├──────────────────┤         ├─────────────────────────┤       │
│  │ - message        │         │ + addPaketWisataId()    │       │
│  │ - context[]      │         │ + backfillJadwalId()    │       │
│  │ - recovery_hint  │         │ + createMissingJadwal() │       │
│  │ + __construct()  │         │ + validateIntegrity()   │       │
│  │ + getContext()   │         │ + outputSummary()       │       │
│  └──────────────────┘         └─────────────────────────┘       │
│                                                                   │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │          Webhook Handler (Enhanced)                       │   │
│  │       BookingController::notification()                   │   │
│  ├──────────────────────────────────────────────────────────┤   │
│  │ + notification(Request)                                   │   │
│  │   └─> DB::transaction {                                  │   │
│  │         updatePaymentStatus()                             │   │
│  │         updateBookingStatus()                             │   │
│  │         adjustQuota()                                     │   │
│  │       } catch -> rollback + log                           │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                   │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │               Pemesanan Model                             │   │
│  ├──────────────────────────────────────────────────────────┤   │
│  │ + paketWisata() : BelongsTo (NEW)                        │   │
│  │ + jadwal() : BelongsTo (EXISTING)                        │   │
│  │ + validateJadwalId() : void (NEW)                        │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
```

### Database Schema Changes

**Before:**
```
pemesanan
├── id
├── kode_booking
├── user_id (nullable)
├── jadwal_id (nullable) ← PROBLEM: Can be null
├── nama_lengkap
└── ... other fields

Access pattern: pemesanan->jadwal->paket (breaks if jadwal_id is null)
```

**After:**
```
pemesanan
├── id
├── kode_booking
├── user_id (nullable)
├── jadwal_id (non-null after migration) ← FIXED
├── paket_wisata_id (NEW - direct reference)
├── nama_lengkap
└── ... other fields

Access pattern: pemesanan->paketWisata (direct, always works)
Fallback pattern: pemesanan->jadwal->paket (still works, but not needed)
```

## Component Design

### 1. Database Migration

**File:** `database/migrations/YYYY_MM_DD_HHMMSS_add_paket_wisata_id_and_backfill_jadwal_id.php`

**Purpose:** Add `paket_wisata_id` column and backfill null `jadwal_id` values by creating or finding matching `jadwal` records.

**Key Operations:**

1. **Add Column:**
   - Add `paket_wisata_id` as unsigned big integer
   - Create foreign key constraint to `paket_wisata.id`
   - Set nullable initially to allow data backfill

2. **Backfill Strategy:**
   ```php
   // For each pemesanan with null jadwal_id:
   // 1. Find or create jadwal matching paket_wisata_id + tanggal_kunjungan
   // 2. Update pemesanan.jadwal_id with the jadwal.id
   // 3. Track statistics (backfilled count, created jadwal count)
   ```

3. **Validation:**
   - Verify zero pemesanan records have null jadwal_id
   - Verify all jadwal_id values reference existing jadwal records
   - Verify all created jadwal records have valid paket_id

**Error Handling:**
- Throw exception if validation fails after backfill
- Include detailed information about failed records
- Rollback transaction on any error

### 2. BookingException Class

**File:** `app/Exceptions/BookingException.php`

**Purpose:** Custom exception for booking-related errors with structured context and recovery hints.

**Interface:**
```php
<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class BookingException extends Exception
{
    protected array $context = [];
    
    public function __construct(
        string $message, 
        array $context = [], 
        int $code = 0, 
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
        $this->logException();
    }
    
    public function getContext(): array
    {
        return $this->context;
    }
    
    public function getRecoveryHint(): ?string
    {
        return $this->context['recovery_hint'] ?? null;
    }
    
    protected function logException(): void
    {
        Log::channel('booking_errors')->error($this->getMessage(), [
            'kode_booking' => $this->context['kode_booking'] ?? null,
            'pemesanan_id' => $this->context['pemesanan_id'] ?? null,
            'recovery_hint' => $this->getRecoveryHint(),
            'context' => $this->context,
            'trace' => $this->getTraceAsString(),
        ]);
    }
}
```

**Usage Patterns:**

1. **Null jadwal_id Error:**
   ```php
   throw new BookingException(
       "Booking {$kodeBooking} has null jadwal_id",
       [
           'kode_booking' => $kodeBooking,
           'pemesanan_id' => $pemesananId,
           'recovery_hint' => 'Run migration to backfill jadwal_id from paket_wisata_id and tanggal_kunjungan',
       ]
   );
   ```

2. **Null paket_wisata_id Error:**
   ```php
   throw new BookingException(
       "Booking {$kodeBooking} has null paket_wisata_id",
       [
           'kode_booking' => $kodeBooking,
           'pemesanan_id' => $pemesananId,
           'recovery_hint' => 'Contact administrator - paket_wisata_id is required and cannot be automatically recovered',
       ]
   );
   ```

### 3. Webhook Transaction Safety

**File:** `app/Http/Controllers/BookingController.php`

**Method:** `notification(Request $request)`

**Current Issues:**
- No database transaction wrapping
- Partial updates possible on errors
- Quota adjustments may not rollback with payment status

**Enhanced Implementation:**
```php
public function notification(Request $request)
{
    try {
        // Get notification data
        $notification = $request->all();
        $orderId = $notification['order_id'] ?? null;
        $transactionStatus = $notification['transaction_status'] ?? null;
        $fraudStatus = $notification['fraud_status'] ?? null;
        $paymentType = $notification['payment_type'] ?? null;
        $transactionId = $notification['transaction_id'] ?? null;

        // Find pembayaran record
        $pembayaran = Pembayaran::where('order_id', $orderId)->first();

        if (!$pembayaran) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        // Wrap all database operations in transaction
        DB::transaction(function () use (
            $pembayaran, 
            $transactionStatus, 
            $fraudStatus, 
            $paymentType, 
            $transactionId
        ) {
            // Update payment status based on transaction status
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'accept') {
                    $pembayaran->update([
                        'status' => 'success',
                        'payment_type' => $paymentType,
                        'transaction_id' => $transactionId,
                        'paid_at' => now(),
                    ]);
                    $pembayaran->pemesanan->update(['status' => 'paid']);
                    $this->sendPaymentSuccessEmail($pembayaran->pemesanan);
                }
            } elseif ($transactionStatus == 'settlement') {
                $pembayaran->update([
                    'status' => 'success',
                    'payment_type' => $paymentType,
                    'transaction_id' => $transactionId,
                    'paid_at' => now(),
                ]);
                $pembayaran->pemesanan->update(['status' => 'paid']);
                $this->sendPaymentSuccessEmail($pembayaran->pemesanan);
            } elseif ($transactionStatus == 'pending') {
                $pembayaran->update([
                    'status' => 'pending',
                    'payment_type' => $paymentType,
                    'transaction_id' => $transactionId,
                ]);
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $pembayaran->update([
                    'status' => 'failed',
                    'payment_type' => $paymentType,
                    'transaction_id' => $transactionId,
                ]);
                $pembayaran->pemesanan->update(['status' => 'cancelled']);
                
                // Restore quota - only if jadwal_id exists
                if ($pembayaran->pemesanan->jadwal_id) {
                    $pembayaran->pemesanan->jadwal->incrementKuota(
                        $pembayaran->pemesanan->jumlah_orang
                    );
                }
            }
        });

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        Log::error('Midtrans notification error', [
            'order_id' => $orderId ?? 'unknown',
            'transaction_status' => $transactionStatus ?? 'unknown',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}
```

**Key Improvements:**
1. All database operations wrapped in `DB::transaction()`
2. Automatic rollback on any exception
3. Enhanced error logging with order_id and transaction_status
4. Safe quota restoration (checks jadwal_id exists)

### 4. Pemesanan Model Enhancements

**File:** `app/Models/Pemesanan.php`

**New Relationship:**
```php
/**
 * Get paket wisata directly (NEW - preferred after migration)
 * Guaranteed non-null after migration execution
 */
public function paketWisata()
{
    return $this->belongsTo(PaketWisata::class, 'paket_wisata_id');
}
```

**New Validation Method:**
```php
/**
 * Validate jadwal_id is not null
 * Throws BookingException if null
 */
public function validateJadwalId(): void
{
    if (is_null($this->jadwal_id)) {
        throw new \App\Exceptions\BookingException(
            "Booking {$this->kode_booking} has null jadwal_id",
            [
                'kode_booking' => $this->kode_booking,
                'pemesanan_id' => $this->id,
                'recovery_hint' => 'Run migration to backfill jadwal_id from paket_wisata_id and tanggal_kunjungan',
            ]
        );
    }
}
```

**Updated Fillable:**
```php
protected $fillable = [
    'kode_booking',
    'user_id',
    'jadwal_id',
    'paket_wisata_id', // NEW
    'nama_lengkap',
    'email',
    'no_hp',
    'package_specific_data',
    'tanggal_kunjungan',
    'jam_kunjungan',
    'catatan',
    'jumlah_orang',
    'total_harga',
    'status',
];
```

### 5. Logging Configuration

**File:** `config/logging.php`

**New Log Channel:**
```php
'channels' => [
    // ... existing channels
    
    'booking_errors' => [
        'driver' => 'daily',
        'path' => storage_path('logs/booking-errors.log'),
        'level' => 'error',
        'days' => 30,
    ],
],
```

## Data Flow

### Migration Backfill Flow

```
START Migration
    ↓
[1] Add paket_wisata_id column to pemesanan table
    ↓
[2] Query all pemesanan records with NULL jadwal_id
    ↓
[3] For each pemesanan record:
    ↓
    [3a] Look for existing jadwal WHERE:
         - paket_id = pemesanan.paket_wisata_id (from jadwal->paket lookup)
         - tanggal = pemesanan.tanggal_kunjungan
    ↓
    [3b] IF jadwal exists:
         → Update pemesanan.jadwal_id = jadwal.id
         → backfill_count++
    ↓
    [3c] IF jadwal NOT exists:
         → Get paket_wisata record
         → Create new jadwal:
            * paket_id = paket_wisata.id
            * tanggal = pemesanan.tanggal_kunjungan
            * kuota_tersedia = paket_wisata.kuota
         → Update pemesanan.jadwal_id = new_jadwal.id
         → created_jadwal_count++
         → backfill_count++
    ↓
[4] Validate integrity:
    → Count pemesanan WHERE jadwal_id IS NULL
    → IF count > 0: THROW exception with details
    → Verify all jadwal_id reference existing jadwal records
    → Verify all created jadwal.paket_id reference existing paket
    ↓
[5] Output summary:
    → Total pemesanan processed
    → Backfill count
    → Created jadwal count
    → Validation results
    ↓
END Migration
```

### Webhook Payment Processing Flow (Enhanced)

```
START Webhook Notification
    ↓
[1] Parse notification data
    ↓
[2] Find pembayaran by order_id
    ↓
[3] BEGIN TRANSACTION
    ↓
    [4] Update pembayaran record (status, payment_type, transaction_id)
    ↓
    [5] Update pemesanan status
    ↓
    [6] IF payment failed/cancelled:
        → Restore quota (jadwal->incrementKuota)
        → Only if jadwal_id is not null
    ↓
    [7] IF payment successful:
        → Send email notification
    ↓
    [8] COMMIT TRANSACTION
    ↓
    SUCCESS Response
    ↓
CATCH Exception
    ↓
    ROLLBACK TRANSACTION
    ↓
    Log error with order_id, transaction_status, exception
    ↓
    ERROR Response
    ↓
END Webhook
```

## Error Handling

### Exception Hierarchy

```
Exception (PHP Base)
    ↓
BookingException (NEW)
    ├── Properties:
    │   ├── message: string
    │   ├── context: array
    │   └── recovery_hint: string (from context)
    │
    └── Methods:
        ├── __construct(message, context, code, previous)
        ├── getContext(): array
        ├── getRecoveryHint(): ?string
        └── logException(): void (auto-logs on construction)
```

### Error Scenarios and Recovery

| Scenario | Exception | Recovery Hint |
|----------|-----------|---------------|
| pemesanan.jadwal_id is null | BookingException | Run migration to backfill jadwal_id |
| pemesanan.paket_wisata_id is null | BookingException | Contact administrator - cannot auto-recover |
| Migration validation fails | Exception | Review migration logs for affected records |
| Webhook transaction fails | Exception (caught, logged, rolled back) | Check logs for order_id and retry |


## Migration Strategy

### Pre-Migration Checklist

1. ✅ Backup database
2. ✅ Identify count of pemesanan with null jadwal_id
3. ✅ Verify all pemesanan have tanggal_kunjungan
4. ✅ Verify paket_wisata records exist for all bookings
5. ✅ Test migration on staging/local environment

### Migration Execution Steps

1. **Create Migration File:**
   ```bash
   php artisan make:migration add_paket_wisata_id_and_backfill_jadwal_id
   ```

2. **Run Migration:**
   ```bash
   php artisan migrate
   ```

3. **Verify Results:**
   - Check migration output logs
   - Query: `SELECT COUNT(*) FROM pemesanan WHERE jadwal_id IS NULL;` (should be 0)
   - Verify foreign key constraints
   - Check booking_errors.log for any exceptions

### Rollback Plan

If migration fails or causes issues:

```bash
# Rollback migration
php artisan migrate:rollback --step=1

# Restore from backup if needed
# Review booking_errors.log for root cause
# Fix data issues
# Re-run migration
```

### Post-Migration Tasks

1. **Code Refactoring:**
   - Replace `$pemesanan->jadwal->paket` with `$pemesanan->paketWisata`
   - Remove null checks for `jadwal_id` in new booking code
   - Update documentation

2. **Monitoring:**
   - Monitor `storage/logs/booking-errors.log` for BookingException instances
   - Verify no null jadwal_id errors in production

3. **Performance:**
   - Add index on `paket_wisata_id` if needed
   - Monitor query performance for direct relationship access

## Testing Strategy

### Unit Tests


**Test: BookingException Construction**
- Verify exception message is set correctly
- Verify context array is stored
- Verify recovery_hint is accessible via getRecoveryHint()

**Test: BookingException Logging**
- Create exception with context
- Verify log entry in booking_errors channel
- Verify log contains kode_booking, pemesanan_id, recovery_hint

**Test: Pemesanan::validateJadwalId()**
- Create pemesanan with null jadwal_id
- Call validateJadwalId()
- Assert BookingException is thrown with correct message

**Test: Webhook Transaction Rollback**
- Mock exception during webhook processing
- Verify no database changes persist
- Verify error is logged with order_id

### Property-Based Tests

**Test: Migration Backfill Correctness**
- For any pemesanan with null jadwal_id, after backfill it should have a valid jadwal_id
- Generate random pemesanan records with various dates and paket IDs
- Run backfill logic
- Verify all jadwal_id values are non-null and reference existing jadwal records

**Test: Migration Data Preservation**
- For any pemesanan record, after backfill all fields except jadwal_id should remain unchanged
- Generate random pemesanan data
- Run backfill
- Compare before/after data (excluding jadwal_id)

**Test: Jadwal Creation Correctness**
- For any pemesanan where matching jadwal doesn't exist, backfill should create new jadwal
- New jadwal should have paket_id matching paket_wisata_id
- New jadwal should have tanggal matching tanggal_kunjungan
- New jadwal should have kuota_tersedia matching paket default kuota

**Test: Referential Integrity**
- For any backfilled jadwal_id, it should reference an existing jadwal record
- For any created jadwal record, its paket_id should reference an existing paket_wisata record

**Test: Webhook Transaction Atomicity**
- For any exception thrown during webhook processing, all database changes should be rolled back
- Generate random webhook scenarios
- Force exceptions at various points
- Verify database state is unchanged

**Test: BookingException Recovery Hints**
- For any BookingException thrown, it should include a recovery_hint in context
- Test various error scenarios
- Verify appropriate recovery hints are provided

### Integration Tests

**Test: End-to-End Migration**
1. Create test database with pemesanan records having null jadwal_id
2. Run migration
3. Verify all records have valid jadwal_id
4. Verify new jadwal records created where needed
5. Verify validation passes

**Test: Webhook Processing with Transactions**
1. Send payment notification webhook
2. Verify pembayaran status updated
3. Verify pemesanan status updated
4. Verify quota adjusted correctly
5. All within single transaction

**Test: BookingException in Production Flow**
1. Attempt to access pemesanan with null jadwal_id
2. Verify BookingException is thrown
3. Verify error is logged to booking_errors channel
4. Verify recovery hint is provided

## Security Considerations

### Data Integrity

1. **Foreign Key Constraints:**
   - Ensure `paket_wisata_id` has proper foreign key to `paket_wisata(id)`
   - Prevent orphaned records
   - Cascade rules for deletions

2. **Transaction Isolation:**
   - Webhook handlers use transactions to prevent race conditions
   - Quota adjustments are atomic with status updates

### Logging Security

1. **Sensitive Data:**
   - Do not log payment credentials or tokens
   - Log only order_id, transaction_id, status
   - Mask customer email/phone in logs if present

2. **Log Access:**
   - Restrict access to `storage/logs/booking-errors.log`
   - Set proper file permissions (0600)
   - Implement log rotation (30 days retention)

## Performance Considerations

### Migration Performance

- **Batch Processing:** Process pemesanan records in batches of 1000 to avoid memory issues
- **Indexing:** Add index on `paket_wisata_id` after backfill completes
- **Estimated Time:** ~1-2 seconds per 1000 records

### Query Optimization

**Before (Chained Relationship):**
```php
// 2 queries + potential N+1 problem
$paket = $pemesanan->jadwal->paket;
```

**After (Direct Relationship):**
```php
// 1 query, more efficient
$paket = $pemesanan->paketWisata;
```

### Database Indexing

```sql
-- Add index for faster lookups
CREATE INDEX idx_pemesanan_paket_wisata_id ON pemesanan(paket_wisata_id);

-- Existing index on jadwal_id
CREATE INDEX idx_pemesanan_jadwal_id ON pemesanan(jadwal_id);
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Migration Backfill Completeness

*For any* pemesanan record with null jadwal_id before migration, after migration execution it SHALL have a non-null jadwal_id that references an existing jadwal record matching its paket_wisata_id and tanggal_kunjungan.

**Validates: Requirements 1.2, 4.3**

### Property 2: Migration Data Preservation Invariant

*For any* pemesanan record processed by the migration, all field values except jadwal_id SHALL remain unchanged after the backfill operation completes.

**Validates: Requirements 1.5**


### Property 3: Jadwal Creation Correctness

*For any* pemesanan record where no matching jadwal exists (by paket_wisata_id and tanggal_kunjungan), the migration SHALL create a new jadwal record with paket_id equal to paket_wisata_id, tanggal equal to tanggal_kunjungan, and kuota_tersedia equal to the paket's default kuota.

**Validates: Requirements 1.3**

### Property 4: Referential Integrity Preservation

*For any* jadwal_id value backfilled or created by the migration, it SHALL reference an existing record in the jadwal table, and for any created jadwal record, its paket_id SHALL reference an existing record in the paket_wisata table.

**Validates: Requirements 4.3, 4.4**

### Property 5: Webhook Transaction Atomicity

*For any* exception thrown during webhook payment processing, all database changes attempted within the transaction block SHALL be rolled back, leaving the database in its pre-webhook state.

**Validates: Requirements 3.3**

### Property 6: BookingException Context Inclusion

*For any* BookingException instantiated with context data, the exception SHALL store the context and include it in the log entry with all provided fields accessible via getContext().

**Validates: Requirements 2.4, 2.5**

### Property 7: BookingException Recovery Hints

*For any* BookingException thrown, it SHALL include a recovery_hint field in its context that provides actionable guidance for resolving the error condition.

**Validates: Requirements 6.1**

## Implementation Notes

### Code Comments (Post-Migration Assumptions)

After migration execution, code should include comments documenting the guaranteed non-null constraint:

```php
// Post-migration guarantee: paket_wisata_id is always non-null
// Migration: add_paket_wisata_id_and_backfill_jadwal_id
// Direct access to paket is now preferred over jadwal->paket chain
$paket = $pemesanan->paketWisata;
```

### Backward Compatibility


While the direct `paketWisata` relationship is preferred, the existing `jadwal->paket` chain will continue to work for compatibility. Refactoring to use the direct relationship should be done incrementally.

### Migration Pseudo-Code

```php
public function up()
{
    // Step 1: Add column
    Schema::table('pemesanan', function (Blueprint $table) {
        $table->unsignedBigInteger('paket_wisata_id')->nullable();
        $table->foreign('paket_wisata_id')
              ->references('id')
              ->on('paket_wisata')
              ->onDelete('cascade');
    });
    
    // Step 2: Backfill logic
    $backfillCount = 0;
    $createdJadwalCount = 0;
    
    DB::transaction(function () use (&$backfillCount, &$createdJadwalCount) {
        $pemesananRecords = DB::table('pemesanan')
            ->whereNull('jadwal_id')
            ->get();
        
        foreach ($pemesananRecords as $pemesanan) {
            // Determine paket_wisata_id from existing data
            // (implementation depends on how to derive this)
            $paketWisataId = $this->derivePaketWisataId($pemesanan);
            
            // Find or create matching jadwal
            $jadwal = DB::table('jadwal')
                ->where('paket_id', $paketWisataId)
                ->where('tanggal', $pemesanan->tanggal_kunjungan)
                ->first();
            
            if (!$jadwal) {
                // Create new jadwal
                $paket = DB::table('paket_wisata')->find($paketWisataId);
                $jadwalId = DB::table('jadwal')->insertGetId([
                    'paket_id' => $paketWisataId,
                    'tanggal' => $pemesanan->tanggal_kunjungan,
                    'kuota_tersedia' => $paket->kuota,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $createdJadwalCount++;
            } else {
                $jadwalId = $jadwal->id;
            }
            
            // Update pemesanan
            DB::table('pemesanan')
                ->where('id', $pemesanan->id)
                ->update([
                    'jadwal_id' => $jadwalId,
                    'paket_wisata_id' => $paketWisataId,
                ]);
            
            $backfillCount++;
        }
    });
    
    // Step 3: Validate
    $nullCount = DB::table('pemesanan')->whereNull('jadwal_id')->count();
    if ($nullCount > 0) {
        throw new \Exception(
            "Migration validation failed: {$nullCount} pemesanan records still have null jadwal_id"
        );
    }
    
    // Step 4: Output summary
    echo "\n=== Migration Summary ===\n";
    echo "Pemesanan records backfilled: {$backfillCount}\n";
    echo "Jadwal records created: {$createdJadwalCount}\n";
    echo "Validation: PASSED (0 null jadwal_id)\n";
}
```

## Deployment Plan

### Phase 1: Preparation (Week 1)
- ✅ Create BookingException class
- ✅ Add booking_errors log channel
- ✅ Write migration file
- ✅ Test on local environment

### Phase 2: Staging Deployment (Week 2)
- ✅ Deploy BookingException and logging config to staging
- ✅ Run migration on staging database
- ✅ Verify backfill results
- ✅ Test webhook transaction handling
- ✅ Monitor logs for any issues

### Phase 3: Production Deployment (Week 3)
- ✅ Schedule maintenance window
- ✅ Backup production database
- ✅ Deploy code changes
- ✅ Run migration on production
- ✅ Verify results
- ✅ Monitor booking_errors.log
- ✅ Update documentation

### Phase 4: Code Refactoring (Week 4)
- ✅ Refactor controllers to use direct paketWisata relationship
- ✅ Remove unnecessary null checks
- ✅ Add code comments documenting guarantees
- ✅ Update team documentation

## Monitoring and Alerts


### Log Monitoring

**File to Monitor:** `storage/logs/booking-errors.log`

**Alert Conditions:**
- Any BookingException with "null jadwal_id" message (should not occur after migration)
- High frequency of webhook transaction rollbacks
- Any validation failures in migration logs

### Metrics to Track

1. **Migration Success:**
   - Count of backfilled records
   - Count of created jadwal records
   - Time taken for migration
   - Zero pemesanan with null jadwal_id post-migration

2. **Webhook Performance:**
   - Transaction success rate
   - Rollback frequency
   - Average processing time
   - Error rate by order_id

3. **Exception Frequency:**
   - BookingException count per day
   - Types of recovery hints triggered
   - Resolution time for reported issues

## Documentation Updates

### Developer Documentation

- **Migration Guide:** Document how to run and verify the migration
- **API Changes:** Document new `paketWisata` relationship
- **Error Handling:** Document BookingException usage and recovery hints
- **Code Comments:** Add comments explaining post-migration guarantees

### Operations Documentation

- **Monitoring Guide:** How to check booking_errors.log
- **Troubleshooting:** Common BookingException scenarios and resolutions
- **Rollback Procedure:** Steps to rollback migration if needed

## Risks and Mitigations

| Risk | Impact | Probability | Mitigation |
|------|--------|-------------|------------|
| Migration fails mid-process | High | Low | Use database transactions, test thoroughly on staging |
| Performance degradation during migration | Medium | Medium | Run during low-traffic window, use batch processing |
| Existing code breaks after migration | High | Low | Maintain backward compatibility, incremental refactoring |
| Webhook transaction deadlocks | Medium | Low | Set appropriate transaction timeout, monitor rollback logs |
| BookingException floods logs | Low | Low | Implement log rate limiting, fix root cause quickly |

## Success Criteria

1. ✅ Zero pemesanan records have null jadwal_id after migration
2. ✅ All pemesanan records have valid paket_wisata_id
3. ✅ BookingException is properly thrown and logged for data integrity issues
4. ✅ Webhook handlers use transactions and rollback on errors
5. ✅ No customer-facing errors related to null jadwal_id
6. ✅ booking_errors.log properly captures and categorizes exceptions
7. ✅ Direct paketWisata relationship is faster than jadwal->paket chain
8. ✅ All referential integrity constraints are enforced

## Appendix

### SQL Queries for Verification

**Check for null jadwal_id:**
```sql
SELECT COUNT(*) as null_count 
FROM pemesanan 
WHERE jadwal_id IS NULL;
-- Expected: 0 after migration
```

**Verify referential integrity:**
```sql
SELECT COUNT(*) as orphaned_count
FROM pemesanan p
LEFT JOIN jadwal j ON p.jadwal_id = j.id
WHERE p.jadwal_id IS NOT NULL AND j.id IS NULL;
-- Expected: 0
```

**Check paket_wisata_id coverage:**
```sql
SELECT COUNT(*) as missing_paket_count
FROM pemesanan
WHERE paket_wisata_id IS NULL;
-- Expected: 0 after migration
```

### Example Log Entries

**BookingException Log Entry:**
```json
{
    "timestamp": "2024-01-15 10:30:45",
    "level": "error",
    "message": "Booking BK20240115000123 has null jadwal_id",
    "context": {
        "kode_booking": "BK20240115000123",
        "pemesanan_id": 123,
        "recovery_hint": "Run migration to backfill jadwal_id from paket_wisata_id and tanggal_kunjungan"
    },
    "trace": "..."
}
```

**Webhook Error Log Entry:**
```json
{
    "timestamp": "2024-01-15 11:45:20",
    "level": "error",
    "message": "Midtrans notification error",
    "context": {
        "order_id": "BOOKING-456-1705314320",
        "transaction_status": "settlement",
        "exception": "Database connection lost",
        "trace": "..."
    }
}
```

### Related Documentation

- Laravel Database Transactions: https://laravel.com/docs/10.x/database#database-transactions
- Laravel Custom Exceptions: https://laravel.com/docs/10.x/errors#custom-exceptions
- Laravel Logging: https://laravel.com/docs/10.x/logging
- Laravel Migrations: https://laravel.com/docs/10.x/migrations
