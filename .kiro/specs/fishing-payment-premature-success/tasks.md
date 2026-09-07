# Implementation Plan

## Overview

This implementation plan follows the exploratory bugfix workflow for the Fishing Payment Premature Success bug. The approach is:
1. **Explore** - Write tests BEFORE fix to understand the bug (Bug Condition)
2. **Preserve** - Write tests for non-buggy behavior (Preservation Requirements)
3. **Implement** - Apply the fix with understanding (Expected Behavior)
4. **Validate** - Verify fix works and doesn't break anything

---

## Tasks

- [ ] 1. Write bug condition exploration test
  - **Property 1: Bug Condition** - Payment Popup Failure Shows Success
  - **CRITICAL**: This test MUST FAIL on unfixed code - failure confirms the bug exists
  - **DO NOT attempt to fix the test or the code when it fails**
  - **NOTE**: This test encodes the expected behavior - it will validate the fix when it passes after implementation
  - **GOAL**: Surface counterexamples that demonstrate the bug exists
  - **Scoped PBT Approach**: For deterministic bugs, scope the property to the concrete failing case(s) to ensure reproducibility
  - Test implementation details from Bug Condition in design:
    - Test Case 1: Simulate Midtrans onError callback without query parameters
    - Test Case 2: Simulate Midtrans onClose callback without query parameters
    - Test Case 3: Verify confirmation page displays "Booking Berhasil!" despite incomplete payment
  - The test assertions should match the Expected Behavior Properties from design:
    - ASSERT confirmation page does NOT display success when payment_failed=1
    - ASSERT confirmation page does NOT display success when payment_status=pending
    - ASSERT pending/error status indicators are shown for incomplete payments
  - Run test on UNFIXED code
  - **EXPECTED OUTCOME**: Test FAILS (this is correct - it proves the bug exists)
  - Document counterexamples found to understand root cause:
    - Counterexample 1: onError callback redirects without ?payment_failed=1 parameter
    - Counterexample 2: onClose callback redirects without ?payment_status=pending parameter
    - Counterexample 3: Confirmation page shows success hero regardless of payment status
  - Mark task complete when test is written, run, and failure is documented
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

- [ ] 2. Write preservation property tests (BEFORE implementing fix)
  - **Property 2: Preservation** - Existing Payment Flows Unchanged
  - **IMPORTANT**: Follow observation-first methodology
  - Observe behavior on UNFIXED code for non-buggy inputs:
    - Test Case 1: Successful payment completion (onSuccess with settlement)
    - Test Case 2: Kiloan fishing bookings (no upfront payment required)
    - Test Case 3: Simulation mode bookings (PAYMENT_MODE=simulation)
    - Test Case 4: Direct URL access to confirmation page
    - Test Case 5: Legitimate pending payments (VA method, not yet paid)
  - Write property-based tests capturing observed behavior patterns from Preservation Requirements:
    - ASSERT successful payments display "Booking Berhasil!" with green checkmark
    - ASSERT kiloan bookings show success without payment popup, display "Dihitung saat ditimbang"
    - ASSERT simulation mode generates SIMULATION- tokens and handles bookings correctly
    - ASSERT onSuccess callback redirects with ?from_payment=1 parameter
    - ASSERT confirmation page shows "Lunas" status for paid bookings
  - Property-based testing generates many test cases for stronger guarantees
  - Run tests on UNFIXED code
  - **EXPECTED OUTCOME**: Tests PASS (this confirms baseline behavior to preserve)
  - Mark task complete when tests are written, run, and passing on unfixed code
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6_

- [ ] 3. Fix for Fishing Payment Premature Success

  - [ ] 3.1 Update Midtrans callback query parameters in fishing-booking-modal.blade.php
    - Update onError callback (line ~862-867) to append ?payment_failed=1 to redirect URL
    - Update onClose callback (line ~868-873) to append ?payment_status=pending to redirect URL
    - Update onPending callback (line ~859-861) to append ?payment_status=pending to redirect URL
    - Preserve onSuccess callback (line ~854-858) with existing ?from_payment=1 parameter
    - Preserve kiloan booking redirect logic (lines ~876-886) without changes
    - _Bug_Condition: isBugCondition(X) where X.paymentPopupResult IN {error, closed, connection_failure} AND X.confirmationPageDisplay = "success"_
    - _Expected_Behavior: result.redirectUrl CONTAINS "?payment_status=pending" OR "?payment_failed=1" from design_
    - _Preservation: onSuccess callback continues with ?from_payment=1, kiloan bookings redirect directly without payment popup_
    - _Requirements: 2.2, 2.3, 2.6_

  - [ ] 3.2 Add pending payment alert section in confirmation.blade.php
    - Insert after existing success alert (after line ~19)
    - Add conditional check: @if(request()->has('payment_status') && request('payment_status') === 'pending')
    - Create alert-pending div with yellow/orange styling (warning theme)
    - Display warning clock icon SVG with stroke styling
    - Show "Menunggu Pembayaran" title
    - Show "Pembayaran Anda belum selesai. Silakan selesaikan pembayaran untuk mengkonfirmasi booking." description
    - _Bug_Condition: Addresses case where payment is incomplete but system shows success_
    - _Expected_Behavior: Display pending status indicators when ?payment_status=pending from design_
    - _Preservation: Existing success alert (with from_payment=1) continues to display for successful payments_
    - _Requirements: 2.3, 2.4_

  - [ ] 3.3 Add error payment alert section in confirmation.blade.php
    - Insert after pending alert section
    - Add conditional check: @if(request()->has('payment_failed'))
    - Create alert-error div with red styling (error theme)
    - Display error icon SVG with stroke styling
    - Show "Pembayaran Gagal" title
    - Show "Pembayaran tidak dapat diproses. Silakan hubungi kami melalui WhatsApp atau coba lagi nanti." description
    - _Bug_Condition: Addresses case where payment encounters error but system shows success_
    - _Expected_Behavior: Display error status indicators when ?payment_failed=1 from design_
    - _Preservation: Existing success alert continues to display for successful payments_
    - _Requirements: 2.2, 2.4, 2.6_

  - [ ] 3.4 Update hero section conditional rendering in confirmation.blade.php
    - Locate existing hero section (lines ~22-29)
    - Add error hero section conditional: @if(request()->has('payment_failed'))
    - Create confirmation-hero-error with red/orange gradient background
    - Display hero-icon-error with red gradient and error icon SVG
    - Show "Pembayaran Gagal" h1 title
    - Show "Terjadi kesalahan saat memproses pembayaran" description
    - Add pending hero section conditional: @elseif(request()->has('payment_status') && request('payment_status') === 'pending')
    - Create confirmation-hero-pending with yellow/orange gradient background
    - Display hero-icon-pending with orange gradient and clock icon SVG
    - Show "Menunggu Pembayaran" h1 title
    - Show "Booking Anda telah dibuat, silakan selesaikan pembayaran" description
    - Wrap existing success hero in @else block (preserves existing behavior)
    - _Bug_Condition: Current hero always shows success regardless of payment status_
    - _Expected_Behavior: Hero section displays appropriate status (error/pending/success) based on query parameters from design_
    - _Preservation: Success hero continues to display for successful payments and kiloan bookings_
    - _Requirements: 2.2, 2.3, 2.4, 2.5_

  - [ ] 3.5 Update success alert conditional in confirmation.blade.php
    - Locate existing success alert conditional (line ~8)
    - Change condition from @if(request()->has('from_payment')) to:
    - @if(request()->has('from_payment') && !request()->has('payment_failed') && request('payment_status') !== 'pending')
    - This ensures success alert only shows for truly successful payments
    - _Bug_Condition: Success alert currently shows even when payment_failed or payment_status=pending_
    - _Expected_Behavior: Success alert only displays when payment is actually successful_
    - _Preservation: Existing success alert behavior for successful payments remains unchanged_
    - _Requirements: 2.4, 2.5_

  - [ ] 3.6 Add CSS styles for pending payment state
    - Add to <style> section in confirmation.blade.php (after line ~243)
    - Define .alert-pending class with yellow/orange theme (#fffbeb background, #f59e0b border)
    - Define .alert-pending-inner class with flex layout
    - Define .alert-pending-icon class with orange gradient background (#fef3c7 bg, #d97706 color)
    - Define .alert-title and .alert-description styles for pending alert
    - Define .confirmation-hero-pending class with yellow/orange gradient (rgba(254, 243, 199, 0.95) to rgba(253, 230, 138, 0.95))
    - Define .hero-icon-pending class with orange gradient (#f59e0b to #fbbf24)
    - _Bug_Condition: No UI styling exists for pending payment state_
    - _Expected_Behavior: Pending state displays with yellow/orange warning theme from design_
    - _Preservation: Existing success state styling remains unchanged_
    - _Requirements: 2.4_

  - [ ] 3.7 Add CSS styles for error payment state
    - Add to <style> section in confirmation.blade.php
    - Define .alert-error class with red theme (#fef2f2 background, #ef4444 border)
    - Define .alert-error-inner class with flex layout
    - Define .alert-error-icon class with red gradient background (#fee2e2 bg, #dc2626 color)
    - Define .confirmation-hero-error class with red/orange gradient (rgba(254, 226, 226, 0.95) to rgba(252, 165, 165, 0.95))
    - Define .hero-icon-error class with red gradient (#ef4444 to #f87171)
    - _Bug_Condition: No UI styling exists for payment error state_
    - _Expected_Behavior: Error state displays with red error theme from design_
    - _Preservation: Existing success state styling remains unchanged_
    - _Requirements: 2.2, 2.4, 2.6_

  - [ ] 3.8 Add retry payment button for error state
    - Add to button grid section in confirmation.blade.php
    - Add conditional: @if(request()->has('payment_failed'))
    - Create new button grid with retry/contact button
    - Link to WhatsApp with pre-filled message including booking code
    - Use format: https://wa.me/62{phone}?text=Halo!%20Saya%20mengalami%20kendala%20pembayaran.%20Kode%20Booking:%20{kode_booking}
    - Style as .button.button-primary with WhatsApp icon
    - Show "Hubungi untuk Bantuan Pembayaran" text
    - Add @else block wrapping existing button grid (preserves normal buttons)
    - _Bug_Condition: Users have no clear path to retry or get help after payment failure_
    - _Expected_Behavior: Error state provides retry/contact options from design_
    - _Preservation: Existing buttons (print, back to home) continue to display for successful bookings_
    - _Requirements: 2.6_

  - [ ] 3.9 Verify bug condition exploration test now passes
    - **Property 1: Expected Behavior** - Correct Status Display After Payment Failure
    - **IMPORTANT**: Re-run the SAME test from task 1 - do NOT write a new test
    - The test from task 1 encodes the expected behavior
    - When this test passes, it confirms the expected behavior is satisfied
    - Run bug condition exploration test from step 1
    - Verify Test Case 1: onError callback now redirects with ?payment_failed=1
    - Verify Test Case 2: onClose callback now redirects with ?payment_status=pending
    - Verify Test Case 3: Confirmation page displays appropriate status (not success) for incomplete payments
    - **EXPECTED OUTCOME**: Test PASSES (confirms bug is fixed)
    - Document that counterexamples are resolved:
      - onError now appends ?payment_failed=1 parameter
      - onClose now appends ?payment_status=pending parameter
      - Confirmation page shows error/pending hero based on query parameters
    - _Requirements: 2.2, 2.3, 2.4, 2.5, 2.6_

  - [ ] 3.10 Verify preservation tests still pass
    - **Property 2: Preservation** - Existing Payment Flows Unchanged
    - **IMPORTANT**: Re-run the SAME tests from task 2 - do NOT write new tests
    - Run preservation property tests from step 2
    - Verify Test Case 1: Successful payments still display "Booking Berhasil!" with green checkmark
    - Verify Test Case 2: Kiloan bookings still show success without payment, display "Dihitung saat ditimbang"
    - Verify Test Case 3: Simulation mode still generates SIMULATION- tokens and works correctly
    - Verify Test Case 4: Direct URL access still displays booking details correctly
    - Verify Test Case 5: Legitimate pending payments (VA) display pending status appropriately
    - **EXPECTED OUTCOME**: Tests PASS (confirms no regressions)
    - Confirm all tests still pass after fix (no regressions)
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6_

- [ ] 4. Checkpoint - Ensure all tests pass
  - Run bug condition exploration test (task 1) - should now PASS
  - Run preservation property tests (task 2) - should still PASS
  - Manually test payment failure scenarios:
    - Test connection failure: throttle network, submit booking, verify error display
    - Test user closes popup: submit booking, close popup, verify pending display
    - Test payment method error: use invalid card, verify error display
  - Manually test preservation scenarios:
    - Test successful payment: complete payment, verify success display unchanged
    - Test kiloan booking: submit kiloan booking, verify success without payment
    - Test simulation mode: enable simulation, verify booking works
  - Verify no console errors in browser DevTools
  - Verify responsive design on mobile devices
  - Verify WhatsApp retry button works correctly
  - If any test fails, investigate and fix before marking complete
  - Ask the user if questions arise

---

## Implementation Notes

### File References

- **fishing-booking-modal.blade.php**: `resources/views/components/fishing-booking-modal.blade.php`
  - Lines 854-858: onSuccess callback (preserve with ?from_payment=1)
  - Lines 859-861: onPending callback (update to add ?payment_status=pending)
  - Lines 862-867: onError callback (update to add ?payment_failed=1)
  - Lines 868-873: onClose callback (update to add ?payment_status=pending)
  - Lines 876-886: Kiloan booking redirect (preserve, no changes)

- **confirmation.blade.php**: `resources/views/booking/confirmation.blade.php`
  - Lines 8-19: Existing success alert (update conditional)
  - After line 19: Insert pending payment alert
  - After pending alert: Insert error payment alert
  - Lines 22-29: Existing hero section (wrap in conditional)
  - Line 243+: Add CSS styles for pending and error states
  - Button grid section: Add retry button conditional

### Design Specifications Referenced

- **Bug Condition (Section: Bug Details)**: Payment popup fails/closes but confirmation shows success
- **Expected Behavior (Section: Expected Behavior)**: Display pending/error status with appropriate UI indicators
- **Preservation Requirements (Section: Expected Behavior)**: Successful payments, kiloan bookings, simulation mode unchanged
- **Correctness Properties (Section: Correctness Properties)**: 
  - Property 1: Payment Status Accuracy (validates requirements 2.2, 2.3, 2.4, 2.6)
  - Property 2: Successful Payment Display (validates requirement 3.1)
  - Property 3: Kiloan Booking Success (validates requirement 3.2)
  - Property 4: Simulation Mode (validates requirements 3.3, 3.6)

### Testing Approach

This implementation follows the **observation-first methodology** for preservation testing:
1. Run UNFIXED code with non-buggy inputs (successful payments, kiloan bookings)
2. Observe and document actual outputs
3. Write tests asserting those observed outputs
4. Verify tests pass on UNFIXED code before implementing fix
5. After fix, verify tests still pass (confirms no regressions)

For bug condition testing, we use **scoped property-based testing**:
- Scope properties to concrete failing cases for reproducibility
- Test should FAIL on unfixed code (confirms bug exists)
- Test should PASS after fix (confirms bug is resolved)
- Document counterexamples to understand root cause

### Query Parameter Design

The fix uses query parameters to communicate payment status:
- `?from_payment=1`: Existing parameter for successful payment (preserve)
- `?payment_status=pending`: New parameter for incomplete/pending payment
- `?payment_failed=1`: New parameter for payment error

This approach is:
- **Stateless**: No session data required
- **Bookmarkable**: URLs can be saved/shared
- **Simple**: Easy to implement and test
- **Conservative**: Only changes display when explicit status parameters present

### UI State Design

Three distinct UI states:
1. **Success** (green): Checkmark icon, "Booking Berhasil!", success gradient
2. **Pending** (yellow/orange): Clock icon, "Menunggu Pembayaran", warning gradient
3. **Error** (red): Error icon, "Pembayaran Gagal", error gradient

Each state includes:
- Alert banner at top (dismissable)
- Hero section with appropriate icon and colors
- Status-specific messaging and instructions
- Conditional action buttons (retry for error state)

### Browser Compatibility

Midtrans Snap is officially supported on:
- Chrome 60+
- Firefox 60+
- Safari 11+
- Edge 79+
- Mobile browsers (iOS Safari 11+, Chrome Mobile 60+)

Test payment flows across these browsers to ensure callback behavior is consistent.
