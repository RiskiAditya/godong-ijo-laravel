# 🐛 Bug Analysis Report - Potential Issues
**Generated:** 2026-08-14
**Project:** Godong Ijo Web Application

---

## 🔴 CRITICAL BUGS

### 1. **Hardcoded WhatsApp Number Inconsistency**
**Severity:** HIGH  
**Impact:** Customer confusion, missed business opportunities

**Problem:**
WhatsApp number is hardcoded in multiple places with **inconsistent formats**:
- `6281111100566` (without +)
- `+62 811 1100 566` (with spaces)
- `081111100566` (without country code)

**Files Affected:**
- `resources/views/components/whatsapp-float.blade.php`
- `resources/views/components/footer.blade.php`
- `resources/views/pages/destination.blade.php`
- `resources/views/landing/index.blade.php`
- `app/Http/Controllers/DestinationController.php`

**Solution:**
Create a centralized config value and use it everywhere.

```php
// config/app.php
'whatsapp' => [
    'number' => '6281111100566', // International format
    'display' => '+62 811 1100 566',
],

// Usage everywhere:
config('app.whatsapp.number')
```

---

### 2. **N+1 Query Problem in Admin Dashboard**
**Severity:** HIGH  
**Impact:** Slow page load, database overload

**Problem:**
```php
// app/Http/Controllers/Admin/DashboardController.php
$recentBookings = Pemesanan::with(['paketWisata', 'jadwal'])
    ->latest()
    ->take(5)
    ->get();
```

This loads `jadwal` but `jadwal` doesn't eager load its `paket` relationship, causing N+1 queries.

**Solution:**
```php
$recentBookings = Pemesanan::with(['paketWisata', 'jadwal.paket'])
    ->latest()
    ->take(5)
    ->get();
```

---

### 3. **Missing CSRF Protection on Midtrans Webhook**
**Severity:** HIGH  
**Impact:** Security vulnerability

**Problem:**
```php
// app/Http/Middleware/VerifyCsrfToken.php
protected $except = [
    'midtrans/notification', // CSRF bypassed!
];
```

While webhooks need CSRF bypass, there's **no alternative authentication** implemented (signature verification).

**Solution:**
Add Midtrans signature verification:
```php
public function notification(Request $request)
{
    // Verify Midtrans signature
    $serverKey = config('midtrans.server_key');
    $hashed = hash('sha512', 
        $request->order_id . 
        $request->status_code . 
        $request->gross_amount . 
        $serverKey
    );
    
    if ($hashed !== $request->signature_key) {
        abort(403, 'Invalid signature');
    }
    
    // Continue processing...
}
```

---

## 🟠 HIGH PRIORITY BUGS

### 4. **Package Slug Generation Race Condition**
**Severity:** MEDIUM-HIGH  
**Impact:** Duplicate slugs, 404 errors

**Problem:**
```php
// app/Http/Controllers/PackageController.php
foreach ($packages as $package) {
    if (Str::slug($package->nama_paket) === $slug) {
        return $package;
    }
}
```

Slugs are generated on-the-fly. If two packages have the same name after slugification, only the first one is accessible.

**Solution:**
1. Add `slug` column to `paket_wisata` table
2. Generate slug on create/update with uniqueness check
3. Use slug column directly instead of generating on-the-fly

---

### 5. **Email Failures Silently Ignored**
**Severity:** MEDIUM  
**Impact:** Customers don't receive confirmations

**Problem:**
```php
try {
    $this->sendBookingConfirmationEmail($pemesanan);
} catch (\Exception $e) {
    $emailSent = false;
    Log::error('Failed to send booking confirmation email');
    // Transaction continues anyway!
}
```

Email failures are logged but user is never informed. They think email was sent.

**Solution:**
- Show warning to user: "Booking confirmed but email failed to send"
- Add email retry queue mechanism
- Display email in success response so user can verify

---

### 6. **Unsafe Request Data Access**
**Severity:** MEDIUM  
**Impact:** Security vulnerability

**Problem:**
```php
// app/Http/Controllers/BookingController.php
$notification = $request->all(); // Gets ALL input including unexpected fields
```

Using `$request->all()` can lead to mass assignment vulnerabilities.

**Solution:**
```php
$notification = $request->only([
    'order_id',
    'transaction_status',
    'fraud_status',
    'payment_type',
    'transaction_id'
]);
```

---

### 7. **Missing Index on Critical Columns**
**Severity:** MEDIUM  
**Impact:** Slow queries as data grows

**Problem:**
Queries like this will be slow without indexes:
```php
Pemesanan::where('kode_booking', $kodeBooking)->first();
Pemesanan::where('status', 'pending')->count();
Pemesanan::whereDate('created_at', today())->count();
```

**Solution:**
Add migration:
```php
Schema::table('pemesanan', function (Blueprint $table) {
    $table->index('kode_booking'); // Already has, verify
    $table->index('status');
    $table->index('created_at');
    $table->index(['status', 'created_at']); // Composite
});
```

---

## 🟡 MEDIUM PRIORITY BUGS

### 8. **No Validation on Phone Number Format**
**Severity:** MEDIUM  
**Impact:** Invalid data in database

**Problem:**
```php
$validated['no_hp'] = preg_replace('/[^0-9]/', '', $validated['no_hp']);
```

Phone is cleaned but no validation if it's actually a valid Indonesian number.

**Solution:**
```php
'no_hp' => [
    'required',
    'string',
    'regex:/^(08|62)[0-9]{8,13}$/', // Indonesian format
]
```

---

### 9. **Timezone Not Explicitly Set**
**Severity:** MEDIUM  
**Impact:** Wrong timestamps, scheduling issues

**Problem:**
`now()`, `today()`, `Carbon::now()` depend on server timezone which may differ from Indonesia.

**Solution:**
```php
// config/app.php
'timezone' => 'Asia/Jakarta',

// Verify in code:
Carbon::now('Asia/Jakarta')
```

---

### 10. **No Rate Limiting on Critical Endpoints**
**Severity:** MEDIUM  
**Impact:** Spam, abuse, DDoS

**Problem:**
```php
Route::post('midtrans/notification', ...); // No rate limit!
```

Webhook endpoint can be spammed.

**Solution:**
```php
Route::middleware('throttle:60,1')->group(function () {
    Route::post('midtrans/notification', ...);
});
```

---

### 11. **Missing Transaction Rollback on Notification Creation Failure**
**Severity:** MEDIUM  
**Impact:** Inconsistent state

**Problem:**
```php
DB::commit(); // Booking saved

try {
    $this->notificationService->createBookingNotification($pemesanan);
} catch (\Exception $e) {
    Log::error('Failed to create booking notification');
    // Booking still committed but notification failed!
}
```

**Solution:**
Move notification creation **inside** the transaction or use database transactions for notifications.

---

### 12. **Customer Controller Loads All Guest Bookings Without Pagination**
**Severity:** MEDIUM  
**Impact:** Memory issues, slow response

**Problem:**
```php
// app/Http/Controllers/Admin/CustomerController.php
$guestCustomers = $guestQuery->get(); // No pagination!
```

As bookings grow to thousands, this will crash.

**Solution:**
```php
$guestCustomers = $guestQuery->paginate(20);
```

---

## 🟢 LOW PRIORITY (Code Quality Issues)

### 13. **Inconsistent Error Response Format**
Some controllers return different JSON structures for errors.

### 14. **No Database Backup Strategy**
Missing scheduled database backup configuration.

### 15. **No Queue Configuration**
Email sending should be queued but no queue worker configured.

### 16. **Missing Input Sanitization**
XSS protection relies only on Blade `{{ }}` escaping.

### 17. **No API Versioning**
API endpoints have no version prefix (`/api/v1/...`).

---

## 📊 Bug Priority Summary

| Severity | Count | Action Required |
|----------|-------|-----------------|
| 🔴 Critical | 3 | Fix ASAP |
| 🟠 High | 6 | Fix before production |
| 🟡 Medium | 6 | Fix in next sprint |
| 🟢 Low | 5 | Technical debt |

---

## 🎯 Immediate Action Items

1. ✅ **Fix hardcoded WhatsApp number** → Use config
2. ✅ **Add Midtrans signature verification**
3. ✅ **Fix N+1 queries** → Add eager loading
4. ✅ **Add database indexes** → Status, created_at
5. ✅ **Add package slug column** → Prevent race conditions
6. ✅ **Add pagination** → Guest customers
7. ✅ **Move notifications inside transaction**
8. ✅ **Add phone number validation**
9. ✅ **Verify timezone** → Asia/Jakarta
10. ✅ **Add rate limiting** → Critical endpoints

---

## 📝 Testing Recommendations

1. **Load Testing:** Test with 10,000+ bookings
2. **Security Audit:** Penetration testing on payment flow
3. **Email Deliverability:** Test with various email providers
4. **Database Performance:** Analyze slow query log
5. **Error Handling:** Test failure scenarios (DB down, payment gateway timeout)

---

**End of Report**
