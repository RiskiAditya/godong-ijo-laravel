# Implementation Plan: Booking & Payment with Midtrans Integration

## Overview

This implementation plan converts the design for the booking and payment system with Midtrans Sandbox integration into actionable coding tasks. The system enables guest checkout directly from the landing page, integrates with Midtrans Snap API for payment processing, and handles webhook notifications for payment status updates. Implementation follows Laravel MVC architecture with proper validation, security, and error handling.

## Tasks

- [x] 1. Set up Midtrans SDK and configuration
  - Install `midtrans/midtrans-php` package via Composer
  - Create `config/midtrans.php` configuration file with server_key, client_key, and is_production settings
  - Add Midtrans credentials to `.env` file for sandbox mode
  - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5_

- [ ] 2. Update database schema for guest bookings and payments
  - [ ] 2.1 Create migration to update pemesanan table
    - Add columns: `nama_lengkap` (VARCHAR 255), `email` (VARCHAR 255), `no_hp` (VARCHAR 20)
    - Modify `user_id` to be nullable
    - Add `kode_booking` column (VARCHAR 255, unique)
    - Add indexes on `kode_booking`, `status`, and `created_at`
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5_
  
  - [x] 2.2 Create migration to populate pembayaran table structure
    - Add columns: `pemesanan_id` (foreign key), `order_id` (VARCHAR 255, unique), `transaction_id` (VARCHAR 255), `payment_type` (VARCHAR 50), `gross_amount` (DECIMAL 12,2), `status` (ENUM), `snap_token` (TEXT), `midtrans_response` (JSON)
    - Set up foreign key constraint with cascade delete
    - Add indexes on `order_id`, `transaction_id`, and `status`
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5, 8.6_
  
  - [ ] 2.3 Run migrations and verify schema changes
    - Execute `php artisan migrate`
    - Verify table structures match design specifications
    - _Requirements: 7.1, 8.1_

- [ ] 3. Create and configure Eloquent models
  - [ ] 3.1 Update Pemesanan model
    - Add fillable fields: `kode_booking`, `user_id`, `jadwal_id`, `nama_lengkap`, `email`, `no_hp`, `jumlah_orang`, `total_harga`, `status`
    - Add status enum casting
    - Define relationships: `belongsTo(User)`, `belongsTo(Jadwal)`, `hasOne(Pembayaran)`
    - Implement `generateKodeBooking()`, `markAsPaid()`, `markAsCancelled()` methods
    - _Requirements: 7.1, 7.3_
  
  - [ ] 3.2 Create Pembayaran model
    - Create model file with fillable fields: `pemesanan_id`, `order_id`, `transaction_id`, `payment_type`, `gross_amount`, `status`, `snap_token`, `midtrans_response`
    - Add status enum casting and JSON casting for `midtrans_response`
    - Add decimal casting for `gross_amount`
    - Define relationship: `belongsTo(Pemesanan)`
    - _Requirements: 8.1, 8.5_
  
  - [ ] 3.3 Update Jadwal model with quota management methods
    - Implement `decrementKuota(int $jumlah)` method
    - Implement `isAvailable(int $jumlah): bool` method
    - _Requirements: 2.5, 4.1_

- [ ] 4. Create MidtransService class
  - [ ] 4.1 Implement MidtransService with SDK initialization
    - Create `app/Services/MidtransService.php`
    - Initialize Midtrans configuration in constructor using config values
    - Set `isSanitized` to true and `is3ds` to true
    - _Requirements: 10.3, 10.4, 10.5_
  
  - [ ] 4.2 Implement createSnapToken method
    - Accept transaction parameters array
    - Call `\Midtrans\Snap::getSnapToken($params)`
    - Handle exceptions and throw descriptive error messages
    - Return snap token string
    - _Requirements: 4.3, 4.5_
  
  - [ ] 4.3 Implement getTransactionStatus method
    - Accept order_id parameter
    - Call `\Midtrans\Transaction::status($orderId)`
    - Return transaction status object
    - _Requirements: 6.3_

- [ ] 5. Implement BookingController
  - [ ] 5.1 Create BookingController with store method
    - Create `app/Http/Controllers/BookingController.php`
    - Inject MidtransService dependency
    - Implement validation rules for booking form inputs
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 11.2_
  
  - [ ] 5.2 Implement booking record creation logic
    - Find or create Jadwal record for selected date
    - Check quota availability using `isAvailable()` method
    - Calculate total price (quantity × package price)
    - Create Pemesanan record with guest details and "pending" status
    - Generate kode_booking using `generateKodeBooking()` method
    - Decrement quota using `decrementKuota()` method
    - _Requirements: 3.2, 4.1, 7.1, 7.3_
  
  - [ ] 5.3 Implement Snap token generation logic
    - Generate unique Order_ID in format `ORDER-{timestamp}-{random5digits}`
    - Prepare Midtrans parameters with customer_details, transaction_details, and item_details
    - Call MidtransService::createSnapToken with parameters
    - Handle Midtrans API errors and return appropriate error responses
    - _Requirements: 4.2, 4.3, 4.4, 4.6_
  
  - [ ] 5.4 Implement payment record creation and response
    - Create Pembayaran record with order_id, gross_amount, snap_token, and "pending" status
    - Return JSON response with success status, pemesanan_id, kode_booking, order_id, snap_token, and gross_amount
    - Handle database transaction rollback on errors
    - _Requirements: 4.5, 8.1, 8.4_
  
  - [ ]* 5.5 Write unit tests for BookingController
    - Test validation rules with valid and invalid inputs
    - Test successful booking flow with mocked Midtrans service
    - Test error handling for insufficient quota, invalid package, and Midtrans API failures
    - Mock database operations to isolate controller logic
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 4.1, 4.6_

- [ ] 6. Checkpoint - Verify booking creation flow
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 7. Implement WebhookController
  - [ ] 7.1 Create WebhookController with handle method
    - Create `app/Http/Controllers/WebhookController.php`
    - Receive POST request from Midtrans notification URL
    - Extract notification payload from request
    - _Requirements: 6.1, 6.3_
  
  - [ ] 7.2 Implement signature verification
    - Extract signature_key from notification payload
    - Compute expected signature using server_key, order_id, status_code, and gross_amount
    - Compare computed signature with provided signature_key
    - Return 403 Forbidden response if signatures don't match
    - _Requirements: 6.2_
  
  - [ ] 7.3 Implement payment status update logic
    - Find Pembayaran record by order_id
    - Extract transaction_status, transaction_id, and payment_type from payload
    - Map transaction_status to internal payment_status and booking_status
    - Update Pembayaran record with new status, transaction_id, payment_type, and complete midtrans_response JSON
    - Update related Pemesanan record status based on mapping
    - _Requirements: 6.3, 6.4, 6.5, 6.6, 6.7_
  
  - [ ] 7.4 Implement webhook response and error handling
    - Return HTTP 200 JSON response to acknowledge notification
    - Handle cases where order_id is not found (return 404)
    - Log all webhook notifications for monitoring
    - _Requirements: 6.8_
  
  - [ ] 7.5 Exclude webhook route from CSRF verification
    - Add webhook URL to `VerifyCsrfToken` middleware exceptions
    - _Requirements: 11.4_
  
  - [ ]* 7.6 Write unit tests for WebhookController
    - Test signature verification with valid and invalid signatures
    - Test status mapping for all Midtrans transaction statuses (capture, settlement, pending, deny, cancel, expire)
    - Test payment and booking record updates
    - Test error handling for invalid order_id and malformed payloads
    - _Requirements: 6.2, 6.4, 6.5, 6.6_

- [ ] 8. Create frontend booking modal component
  - [ ] 8.1 Create booking-modal Blade component
    - Create `resources/views/components/booking-modal.blade.php`
    - Implement modal overlay with backdrop blur
    - Add modal open/close toggle functionality using Alpine.js
    - Implement body scroll lock when modal is active
    - Add close button and backdrop click handler
    - _Requirements: 1.1, 1.2, 1.4, 1.5_
  
  - [ ] 8.2 Create booking form within modal
    - Add form fields: nama_lengkap, email, no_hp, paket_wisata_id (readonly), tanggal_kunjungan, jumlah_orang, total_harga (readonly)
    - Pre-fill package information when modal opens
    - Add CSRF token field
    - _Requirements: 1.2, 1.3, 2.1, 2.2, 2.3, 2.4, 2.5, 11.1_
  
  - [ ] 8.3 Implement Alpine.js form handling
    - Create Alpine.js data component for reactive form state
    - Implement real-time total price calculation (quantity × package price)
    - Format total price in Indonesian Rupiah format (Rp X.XXX.XXX)
    - Handle "Hubungi kami" for custom-priced packages
    - _Requirements: 3.1, 3.2, 3.3, 3.4_
  
  - [ ] 8.4 Implement client-side validation
    - Validate nama_lengkap (min 3 characters)
    - Validate email format (contains @ and domain)
    - Validate no_hp format (10-15 digits, numbers only)
    - Validate tanggal_kunjungan (not before today)
    - Validate jumlah_orang (1-100 range)
    - Display inline error messages for invalid fields
    - Enable/disable submit button based on validation state
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7_

- [ ] 9. Implement frontend AJAX submission and Midtrans integration
  - [ ] 9.1 Implement AJAX form submission
    - Handle form submit event with Alpine.js
    - Send POST request to `/api/booking/store` endpoint
    - Include CSRF token in request headers
    - Display loading spinner and "Memproses pemesanan..." message
    - Disable submit button during request
    - _Requirements: 9.1, 9.2, 11.1_
  
  - [ ] 9.2 Handle backend response and errors
    - Parse JSON response from backend
    - Display validation errors inline next to respective fields
    - Show error notifications for network failures, server errors, quota errors
    - Auto-dismiss error notifications after 5 seconds
    - _Requirements: 2.6, 9.4, 12.1, 12.2, 12.3, 12.4, 12.5_
  
  - [ ] 9.3 Integrate Midtrans Snap.js
    - Load Midtrans Snap.js library from CDN
    - Create JavaScript class to handle Snap initialization
    - Pass client_key from backend configuration to Snap
    - _Requirements: 5.1, 10.3_
  
  - [ ] 9.4 Implement Snap token trigger and payment callbacks
    - Trigger `snap.pay()` with snap_token from backend response
    - Change loading message to "Membuka halaman pembayaran..."
    - Implement success callback to display success notification with booking details
    - Implement pending callback to show pending payment message
    - Implement error callback to display error notification with retry option
    - Implement close callback to keep modal open with informational message
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 9.3, 9.5, 9.6_

- [ ] 10. Implement API routes
  - [ ] 10.1 Add booking store route
    - Add POST route `/api/booking/store` pointing to `BookingController@store`
    - Apply `web` middleware for CSRF protection
    - _Requirements: 11.1, 11.2_
  
  - [ ] 10.2 Add webhook notification route
    - Add POST route `/webhook/midtrans` pointing to `WebhookController@handle`
    - Exclude from CSRF verification (already handled in step 7.5)
    - _Requirements: 6.1, 11.4_
  
  - [ ] 10.3 Add optional booking status check route
    - Add GET route `/api/booking/status/{kode_booking}` for user confirmation page (optional feature)
    - Return booking and payment details as JSON
    - _Requirements: Design API section 3_

- [ ] 11. Integrate booking button on landing page
  - [ ] 11.1 Add "Pesan Sekarang" button to package cards
    - Add button with Alpine.js click handler
    - Pass package data (id, name, price) to modal open event
    - _Requirements: 1.1_
  
  - [ ] 11.2 Wire button to open booking modal
    - Dispatch `booking-modal:open` event with package data
    - Modal component listens for event and opens with pre-filled data
    - _Requirements: 1.1, 1.3_

- [ ] 12. Checkpoint - Test end-to-end booking flow
  - Ensure all tests pass, ask the user if questions arise.

- [ ]* 13. Write property-based tests for correctness properties
  - [ ]* 13.1 Write property test for input validation consistency
    - **Property 1: Input Validation Consistency**
    - **Validates: Requirements 2.1, 2.2, 2.3, 2.4, 2.5**
    - Generate random inputs with various validation states
    - Verify validation logic consistently accepts valid inputs and rejects invalid ones
    - Run minimum 100 iterations
  
  - [ ]* 13.2 Write property test for price calculation accuracy
    - **Property 2: Price Calculation Accuracy**
    - **Validates: Requirements 3.2**
    - Generate random quantities (1-100) and package prices
    - Verify total price always equals quantity × package price with correct decimal precision
    - Run minimum 100 iterations
  
  - [ ]* 13.3 Write property test for currency formatting
    - **Property 3: Currency Formatting**
    - **Validates: Requirements 3.3**
    - Generate random positive decimal amounts
    - Verify formatted output matches Indonesian Rupiah pattern with thousand separators
    - Run minimum 100 iterations
  
  - [ ]* 13.4 Write property test for Order ID format and uniqueness
    - **Property 4: Order ID Format and Uniqueness**
    - **Validates: Requirements 4.2**
    - Generate multiple Order IDs in sequence
    - Verify each matches format `ORDER-{timestamp}-{random}` and is unique
    - Run minimum 100 iterations
  
  - [ ]* 13.5 Write property test for Midtrans request payload completeness
    - **Property 5: Midtrans Request Payload Completeness**
    - **Validates: Requirements 4.4**
    - Generate various booking data combinations
    - Verify generated Midtrans payload contains all required fields with correct mappings
    - Run minimum 100 iterations
  
  - [ ]* 13.6 Write property test for signature verification correctness
    - **Property 6: Signature Verification Correctness**
    - **Validates: Requirements 6.2**
    - Generate webhook notifications with valid and tampered signatures
    - Verify signature verification returns true only for valid signatures
    - Run minimum 100 iterations
  
  - [ ]* 13.7 Write property test for webhook payload extraction
    - **Property 7: Webhook Payload Extraction**
    - **Validates: Requirements 6.3**
    - Generate various valid Midtrans notification payload structures
    - Verify handler successfully extracts all required fields without errors
    - Run minimum 100 iterations
  
  - [ ]* 13.8 Write property test for transaction status mapping
    - **Property 8: Transaction Status Mapping**
    - **Validates: Requirements 6.4, 6.5, 6.6**
    - Test all Midtrans transaction_status values
    - Verify correct mapping to internal payment_status and booking_status pairs
    - Run minimum 100 iterations
  
  - [ ]* 13.9 Write property test for JSON serialization round-trip
    - **Property 9: JSON Serialization Round-Trip**
    - **Validates: Requirements 6.7**
    - Generate various Midtrans response payloads
    - Verify serialize-then-deserialize produces equivalent data structure
    - Run minimum 100 iterations
  
  - [ ]* 13.10 Write property test for CSRF token validation
    - **Property 10: CSRF Token Validation**
    - **Validates: Requirements 11.1, 11.2, 11.3**
    - Generate POST requests with valid, invalid, expired, and missing CSRF tokens
    - Verify validation accepts only valid tokens and rejects all others
    - Run minimum 100 iterations

- [ ]* 14. Write integration tests
  - [ ]* 14.1 Write integration test for complete booking flow
    - Test full flow: form submission → booking creation → Snap token generation → payment record creation
    - Use Midtrans sandbox credentials
    - Verify database records are created correctly
    - _Requirements: All requirements 1-12_
  
  - [ ]* 14.2 Write integration test for webhook notification handling
    - Simulate Midtrans webhook notifications for different transaction statuses
    - Verify signature verification, status updates, and database changes
    - Test with real webhook payload samples from Midtrans documentation
    - _Requirements: Requirements 6.1-6.8_
  
  - [ ]* 14.3 Write integration test for error scenarios
    - Test insufficient quota scenario
    - Test Midtrans API failure handling
    - Test invalid webhook signature rejection
    - Test database constraint violations
    - _Requirements: Requirements 4.6, 6.2, 12.1-12.5_

- [ ] 15. Final checkpoint - Complete system verification
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP deployment
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation of core functionality
- Property tests validate universal correctness properties from the design document
- Unit tests validate specific examples, edge cases, and component integration
- Integration tests verify end-to-end flows with external dependencies (Midtrans API, database)
- The system uses Laravel's built-in CSRF protection for security
- Midtrans Sandbox mode is used for development and testing
- All monetary values use decimal precision for accurate calculations
- Webhook signature verification prevents malicious fake notifications
- Guest checkout is fully supported without user authentication requirements

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1"] },
    { "id": 1, "tasks": ["2.1", "2.2"] },
    { "id": 2, "tasks": ["2.3"] },
    { "id": 3, "tasks": ["3.1", "3.2", "3.3"] },
    { "id": 4, "tasks": ["4.1"] },
    { "id": 5, "tasks": ["4.2", "4.3"] },
    { "id": 6, "tasks": ["5.1"] },
    { "id": 7, "tasks": ["5.2"] },
    { "id": 8, "tasks": ["5.3"] },
    { "id": 9, "tasks": ["5.4", "5.5"] },
    { "id": 10, "tasks": ["7.1"] },
    { "id": 11, "tasks": ["7.2", "7.3"] },
    { "id": 12, "tasks": ["7.4", "7.5", "7.6"] },
    { "id": 13, "tasks": ["8.1"] },
    { "id": 14, "tasks": ["8.2", "8.3"] },
    { "id": 15, "tasks": ["8.4"] },
    { "id": 16, "tasks": ["9.1"] },
    { "id": 17, "tasks": ["9.2", "9.3"] },
    { "id": 18, "tasks": ["9.4"] },
    { "id": 19, "tasks": ["10.1", "10.2", "10.3"] },
    { "id": 20, "tasks": ["11.1", "11.2"] },
    { "id": 21, "tasks": ["13.1", "13.2", "13.3", "13.4", "13.5"] },
    { "id": 22, "tasks": ["13.6", "13.7", "13.8", "13.9", "13.10"] },
    { "id": 23, "tasks": ["14.1", "14.2", "14.3"] }
  ]
}
```
