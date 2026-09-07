# Bugfix Requirements Document

## Introduction

This bugfix addresses a critical issue in the Fishing Lake booking payment flow where the Midtrans payment popup experiences connection failures and closes prematurely, yet the system incorrectly displays "Booking Berhasil!" (Booking Successful) even though the payment was never completed. This creates confusion for users who are unsure whether they need to pay, and results in inconsistent payment data with potential unpaid bookings appearing as successful.

**Impact:**
- Users are confused about payment status
- Bookings may appear successful without valid payment
- Data inconsistency between booking status and payment status
- Poor user experience during critical payment flow

**Affected Components:**
- Fishing Lake booking form (FishingBookingRequest.php)
- Booking controller payment flow (BookingController::storeFishingBooking)
- Frontend Midtrans Snap integration (fishing-booking-modal.blade.php)
- Confirmation page display (confirmation.blade.php)

## Bug Analysis

### Current Behavior (Defect)

1.1 WHEN user submits Fishing Lake booking form with valid data THEN Midtrans Snap payment popup fails with connection error

1.2 WHEN Midtrans payment popup fails to load or encounters connection issues THEN the popup closes automatically before user can complete payment

1.3 WHEN Midtrans payment popup closes due to connection failure or user closure without successful payment THEN the system redirects to confirmation page showing "Booking Berhasil!" message

1.4 WHEN user is redirected to confirmation page after failed/incomplete payment THEN the page displays success indicators (green checkmark, "Booking Berhasil!" title) regardless of actual payment status

1.5 WHEN payment fails or is not completed THEN the booking record remains in "pending" status but user sees success messaging

### Expected Behavior (Correct)

2.1 WHEN user submits Fishing Lake booking form with valid data THEN Midtrans Snap payment popup SHALL open successfully without connection failures

2.2 WHEN Midtrans payment popup encounters connection issues or errors THEN the system SHALL display a clear error message and allow retry without showing premature success

2.3 WHEN Midtrans payment popup closes without successful payment (onClose, onError callbacks) THEN the system SHALL redirect to confirmation page showing "pending payment" status, not "booking successful"

2.4 WHEN user is redirected to confirmation page after incomplete/failed payment THEN the page SHALL display pending payment indicators (warning icon, "Menunggu Pembayaran" status) with clear instructions to complete payment

2.5 WHEN payment is truly successful (onSuccess callback with settlement status) THEN and only then SHALL the system display "Booking Berhasil!" with green success indicators

2.6 WHEN payment encounters errors (onError callback) THEN the system SHALL log the error details and display user-friendly error messaging with retry options

### Unchanged Behavior (Regression Prevention)

3.1 WHEN user completes Midtrans payment successfully and receives settlement confirmation THEN the system SHALL CONTINUE TO display "Booking Berhasil!" with success indicators

3.2 WHEN booking is for "kiloan" fishing type (price determined after weighing) THEN the system SHALL CONTINUE TO show booking success without requiring payment first

3.3 WHEN user's booking is in simulation mode (PAYMENT_MODE=simulation) THEN the system SHALL CONTINUE TO handle bookings with simulated payment tokens

3.4 WHEN user views confirmation page for a paid booking (pembayaran.status === 'success') THEN the system SHALL CONTINUE TO display "Lunas" (Paid) status badge

3.5 WHEN booking record is successfully created in database THEN the system SHALL CONTINUE TO generate unique booking code in format GOD-YYYYMMDD-XXXXXX

3.6 WHEN Midtrans is not configured (missing server key) and not in simulation mode THEN the system SHALL CONTINUE TO log warning and fall back to simulation mode

## Bug Condition and Property Specification

### Bug Condition Function

The bug condition identifies inputs/scenarios where the payment flow incorrectly shows success despite incomplete payment:

```pascal
FUNCTION isBugCondition(X)
  INPUT: X of type PaymentFlowState
    WHERE X = {
      bookingCreated: boolean,
      snapTokenGenerated: boolean,
      paymentPopupResult: enum {success, pending, error, closed, connection_failure},
      redirectTarget: string,
      confirmationPageDisplay: string
    }
  OUTPUT: boolean
  
  // Bug occurs when booking created, popup fails/closes, 
  // yet confirmation shows success
  RETURN (
    X.bookingCreated = true AND
    X.snapTokenGenerated = true AND
    X.paymentPopupResult IN {error, closed, connection_failure} AND
    X.confirmationPageDisplay = "success"
  )
END FUNCTION
```

### Fix Checking Property

For all buggy payment flows, the fixed system should show appropriate pending/error status:

```pascal
// Property: Fix Checking - Correct Status Display After Failed Payment
FOR ALL X WHERE isBugCondition(X) DO
  result ← handlePaymentFlow'(X)
  
  ASSERT result.redirectUrl CONTAINS "?payment_status=pending" OR "?payment_failed=1"
  ASSERT result.confirmationPageDisplay IN {"pending", "error", "requires_payment"}
  ASSERT result.userMessage CONTAINS "menunggu pembayaran" OR "gagal" OR "error"
  ASSERT result.showSuccessIndicators = false
  ASSERT result.showPendingIndicators = true OR result.showErrorIndicators = true
END FOR
```

### Preservation Checking Property

For all non-buggy payment flows (successful payments and special cases), behavior must remain unchanged:

```pascal
// Property: Preservation Checking - Maintain Correct Existing Behaviors
FOR ALL X WHERE NOT isBugCondition(X) DO
  // X includes successful payments, kiloan bookings, simulation mode
  ASSERT handlePaymentFlow(X) = handlePaymentFlow'(X)
END FOR

// Specific preservation checks:

// Successful payment flows
FOR ALL X WHERE X.paymentPopupResult = success AND X.paymentStatus = "settlement" DO
  result ← handlePaymentFlow'(X)
  ASSERT result.confirmationPageDisplay = "success"
  ASSERT result.userMessage CONTAINS "Booking Berhasil"
  ASSERT result.showSuccessIndicators = true
END FOR

// Kiloan type (no upfront payment)
FOR ALL X WHERE X.bookingType = "kiloan" AND X.totalHarga = null DO
  result ← handlePaymentFlow'(X)
  ASSERT result.confirmationPageDisplay = "success"
  ASSERT result.userMessage CONTAINS "Dihitung saat ditimbang"
END FOR

// Simulation mode
FOR ALL X WHERE X.paymentMode = "simulation" DO
  result ← handlePaymentFlow'(X)
  ASSERT result.snapToken STARTS_WITH "SIMULATION-"
  ASSERT result.handlesBookingCorrectly = true
END FOR
```

### Example Counterexample

**Concrete example demonstrating the bug:**

```javascript
// User submits fishing booking form
POST /booking/fishing
{
  nama_lengkap: "Budi Santoso",
  no_hp: "08123456789",
  tanggal_kunjungan: "2026-09-15",
  jam_kunjungan: "10:00",
  jenis_pemancingan: "tarikan",
  jumlah_joran: 2,
  durasi: "2",
  setuju_aturan: true
}

// Backend response: booking created, snap token generated
Response: 200 OK
{
  success: true,
  snap_token: "abc-123-xyz",
  kode_booking: "GOD-20260904-ABCD12",
  redirect_url: "/booking/confirmation/GOD-20260904-ABCD12"
}

// Frontend opens Midtrans popup
window.snap.pay("abc-123-xyz", {
  onError: (result) => {
    // BUG: Even though payment failed, still redirects to success page
    window.location.href = "/booking/confirmation/GOD-20260904-ABCD12"
  },
  onClose: () => {
    // BUG: Even though user closed without paying, still shows success
    window.location.href = "/booking/confirmation/GOD-20260904-ABCD12"
  }
})

// Confirmation page loads
GET /booking/confirmation/GOD-20260904-ABCD12

// BUG: Page displays:
// - "Booking Berhasil!" (should be "Menunggu Pembayaran")
// - Green checkmark icon (should be warning/pending icon)
// - Success styling (should be pending styling)

// Database reality:
// pemesanan.status = "pending" ✓ (correct)
// pembayaran.status = "pending" ✓ (correct)
// But UI shows success ✗ (incorrect)
```

**Expected correct behavior after fix:**

```javascript
// Same frontend flow, but callbacks updated:
window.snap.pay("abc-123-xyz", {
  onError: (result) => {
    // FIXED: Redirect with error indicator
    window.location.href = "/booking/confirmation/GOD-20260904-ABCD12?payment_failed=1"
  },
  onClose: () => {
    // FIXED: Redirect with pending indicator
    window.location.href = "/booking/confirmation/GOD-20260904-ABCD12?payment_status=pending"
  }
})

// Confirmation page detects query parameter
GET /booking/confirmation/GOD-20260904-ABCD12?payment_status=pending

// FIXED: Page displays:
// - "Menunggu Pembayaran" (correct pending status)
// - Warning/clock icon (appropriate pending indicator)
// - Yellow/orange styling (pending state styling)
// - Clear instruction: "Silakan selesaikan pembayaran"
// - Retry payment button/link
```

## Key Definitions

- **F**: Original (unfixed) payment flow - `storeFishingBooking()` and `fishing-booking-modal.blade.php` before the fix
- **F'**: Fixed payment flow - after implementing proper error handling and status differentiation
- **Bug Condition C(X)**: Payment popup fails/closes but confirmation page shows success
- **Property P(result)**: For buggy flows, corrected system shows pending/error status with appropriate UI indicators
- **Preservation ¬C(X)**: Successful payments, kiloan bookings, and simulation mode continue working exactly as before
