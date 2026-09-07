# Implementation Plan: Fix Pemesanan Null jadwal_id Bug

## Overview

This plan addresses a critical data integrity issue where `jadwal_id` can be null in the `pemesanan` table, causing relationship chain failures. The fix involves creating a database migration to add `paket_wisata_id` and backfill null `jadwal_id` values, implementing custom exception handling, wrapping webhook handlers in transactions, and refactoring code to use direct relationships. This ensures all bookings have valid relationship chains and prevents null reference errors.

## Tasks

- [x] 1. Create BookingException custom exception class
  - [x] 1.1 Implement BookingException class with context and recovery hints
    - Create `app/Exceptions/BookingException.php` extending PHP Exception
    - Add protected `$context` array property
    - Implement constructor accepting message, context array, code, and previous exception
    - Implement `getContext()` method returning context array
    - Implement `getRecoveryHint()` method extracting recovery_hint from context
    - Implement `logException()` method that automatically logs to booking_errors channel
    - Include kode_booking, pemesanan_id, recovery_hint, context, and stack trace in logs
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5_
  
  - [ ]* 1.2 Write unit tests for BookingException
    - Test exception message is set correctly
    - Test context array is stored and accessible via getContext()
    - Test recovery_hint is accessible via getRecoveryHint()
    - Test exception is logged to booking_errors channel with all required fields
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5_

- [x] 2. Configure booking errors log channel
  - [x] 2.1 Add booking_errors log channel to logging configuration
    - Edit `config/logging.php`
    - Add new channel 'booking_errors' with driver 'daily'
    - Set path to `storage/logs/booking-errors.log`
    - Set level to 'error'
    - Set retention days to 30
    - _Requirements: 6.4_

- [x] 3. Create database migration for paket_wisata_id and jadwal_id backfill
  - [x] 3.1 Implement migration file structure and column addition
    - Create migration file `database/migrations/YYYY_MM_DD_HHMMSS_add_paket_wisata_id_and_backfill_jadwal_id.php`
    - In `up()` method, add `paket_wisata_id` column as unsigned big integer nullable
    - Add foreign key constraint to `paket_wisata.id` with cascade delete
    - Add index on `paket_wisata_id` for performance
    - _Requirements: 1.1_
  
  - [x] 3.2 Implement backfill logic for null jadwal_id records
    - Query all pemesanan records where `jadwal_id IS NULL`
    - For each record, determine `paket_wisata_id` from existing data relationships
    - Look for existing jadwal matching `paket_id = paket_wisata_id` AND `tanggal = tanggal_kunjungan`
    - If jadwal exists, update `pemesanan.jadwal_id` with found `jadwal.id`
    - If jadwal doesn't exist, create new jadwal record with paket_id, tanggal, and default kuota
    - Update `pemesanan.jadwal_id` and `pemesanan.paket_wisata_id` in single query
    - Track backfill_count and created_jadwal_count statistics
    - Wrap all backfill operations in DB::transaction for atomicity
    - _Requirements: 1.2, 1.3, 1.5_
  
  - [x] 3.3 Implement migration validation and summary output
    - After backfill, query count of pemesanan records where `jadwal_id IS NULL`
    - If count > 0, throw exception with details of affected records
    - Verify all jadwal_id values reference existing records in jadwal table
    - Verify all created jadwal records have valid paket_id referencing paket_wisata
    - Output summary with total pemesanan processed, backfilled count, created jadwal count
    - Log validation results to console and Laravel log
    - _Requirements: 1.4, 4.1, 4.2, 4.3, 4.4, 4.5_
  
  - [x] 3.4 Implement migration rollback (down method)
    - Drop foreign key constraint on paket_wisata_id
    - Drop paket_wisata_id column from pemesanan table
    - Note: Cannot rollback jadwal_id backfill (data change, not schema)
    - _Requirements: 1.1_

- [x] 4. Enhance Pemesanan model with new relationship and validation
  - [x] 4.1 Add paketWisata relationship and validation method to Pemesanan model
    - Edit `app/Models/Pemesanan.php`
    - Add `paket_wisata_id` to fillable array
    - Implement `paketWisata()` belongsTo relationship to PaketWisata model
    - Implement `validateJadwalId()` method checking if jadwal_id is null
    - Throw BookingException if jadwal_id is null with appropriate context and recovery hint
    - Add code comment documenting post-migration guarantee: paket_wisata_id is always non-null
    - _Requirements: 2.2, 5.1, 5.2, 5.5_
  
  - [ ]* 4.2 Write unit tests for Pemesanan model enhancements
    - Test paketWisata relationship returns correct PaketWisata instance
    - Test validateJadwalId throws BookingException when jadwal_id is null
    - Test validateJadwalId does not throw when jadwal_id is set
    - Test BookingException contains correct kode_booking and recovery_hint
    - _Requirements: 2.2, 5.1_

- [x] 5. Wrap webhook payment handler in database transaction
  - [x] 5.1 Enhance BookingController notification method with transaction safety
    - Edit `app/Http/Controllers/BookingController.php`
    - Locate `notification(Request $request)` method
    - Wrap all database operations in `DB::transaction()` closure
    - Update pembayaran status (status, payment_type, transaction_id, paid_at)
    - Update pemesanan status based on transaction_status
    - Restore quota for failed/cancelled payments (check jadwal_id exists first)
    - Send payment success email within transaction for successful payments
    - Add try-catch block around entire handler
    - On exception, log error with order_id, transaction_status, exception message
    - Return JSON error response with 500 status on exception
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_
  
  - [ ]* 5.2 Write integration tests for webhook transaction safety
    - Test successful payment notification updates all records atomically
    - Test failed payment notification triggers rollback of all changes
    - Test quota restoration happens within transaction
    - Test exception during processing leaves database unchanged
    - Verify error logging includes order_id and transaction_status
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_

- [x] 6. Checkpoint - Run migration and verify results
  - Run `php artisan migrate` and review output logs
  - Verify zero pemesanan records have null jadwal_id: `SELECT COUNT(*) FROM pemesanan WHERE jadwal_id IS NULL;`
  - Check `storage/logs/booking-errors.log` for any exceptions
  - Verify foreign key constraints are in place
  - Ensure all tests pass, ask the user if questions arise

- [ ] 7. Refactor existing code to use direct paketWisata relationship
  - [ ] 7.1 Update controllers to use direct paketWisata relationship
    - Search for usage patterns: `$pemesanan->jadwal->paket`
    - Replace with: `$pemesanan->paketWisata`
    - Update BookingController methods accessing paket information
    - Update Admin\BookingController methods displaying paket details
    - Add code comments documenting the change and post-migration guarantee
    - Remove unnecessary null checks for jadwal_id in new booking creation code
    - _Requirements: 5.3, 5.4, 5.5_
  
  - [ ] 7.2 Update views and Blade templates using paket relationship
    - Search for Blade templates accessing `pemesanan->jadwal->paket`
    - Replace with `pemesanan->paketWisata`
    - Verify confirmation pages display correct paket information
    - Test booking modal displays correct package details
    - _Requirements: 5.3, 5.4_
  
  - [ ]* 7.3 Write integration tests for refactored code paths
    - Test booking creation stores paket_wisata_id correctly
    - Test booking display pages show correct paket information via direct relationship
    - Test admin booking list displays paket names correctly
    - Verify no N+1 query issues with new relationship usage
    - _Requirements: 5.3, 5.4_

- [ ] 8. Add error recovery documentation and monitoring
  - [ ] 8.1 Create developer documentation for BookingException handling
    - Document when BookingException is thrown and with what recovery hints
    - Document how to handle null jadwal_id scenarios in new code
    - Document the post-migration assumptions about paket_wisata_id
    - Create code examples showing proper usage of paketWisata relationship
    - Document monitoring procedures for `storage/logs/booking-errors.log`
    - _Requirements: 6.1, 6.2, 6.3, 6.5_

- [ ] 9. Final checkpoint - Comprehensive validation
  - Run all tests: `php artisan test`
  - Verify no BookingException instances in booking-errors.log after migration
  - Test complete booking flow from creation to payment confirmation
  - Verify webhook handling works correctly with transaction rollback
  - Test quota restoration for cancelled payments
  - Verify admin panel displays booking information correctly
  - Ensure all tests pass, ask the user if questions arise

## Notes

- Tasks marked with `*` are optional testing tasks and can be skipped for faster deployment
- Each task references specific requirements for traceability
- Migration should be tested on staging environment before production deployment
- Backup database before running migration in production
- Monitor `storage/logs/booking-errors.log` for any BookingException instances post-deployment
- The migration is a one-time operation that transforms legacy data
- After migration, all new booking code can assume `paket_wisata_id` is always present
- Direct `paketWisata` relationship is more efficient than chained `jadwal->paket` relationship
- Property-based testing is not applicable for this bugfix as it involves data migration and error handling rather than algorithmic correctness

## Task Dependency Graph

```json
{
  "waves": [
    {
      "id": 0,
      "tasks": ["1.1", "2.1"]
    },
    {
      "id": 1,
      "tasks": ["1.2", "3.1"]
    },
    {
      "id": 2,
      "tasks": ["3.2"]
    },
    {
      "id": 3,
      "tasks": ["3.3", "3.4"]
    },
    {
      "id": 4,
      "tasks": ["4.1", "5.1"]
    },
    {
      "id": 5,
      "tasks": ["4.2", "5.2"]
    },
    {
      "id": 6,
      "tasks": ["7.1", "7.2"]
    },
    {
      "id": 7,
      "tasks": ["7.3", "8.1"]
    }
  ]
}
```
