# Implementation Plan: Dynamic Booking Form

## Overview

This plan implements a dynamic, package-type-aware booking modal system that adapts its fields based on three tourism packages: culinary (The Waterfall Resto), fishing (Monster Fish), and private events (Private Room). The implementation uses Alpine.js for reactive client-side behavior, Laravel for backend processing, and Tailwind CSS for styling.

## Tasks

- [x] 1. Extend database schema for package-specific booking data
  - [x] 1.1 Create migration to add dynamic booking fields to pemesanan table
    - Add JSON column `package_specific_data` to store dynamic field values
    - Add `tanggal_kunjungan` DATE column for booking date
    - Add `catatan` TEXT column for notes/special requests
    - Add index on `tanggal_kunjungan` for query performance
    - _Requirements: 2.4, 2.5, 3.1, 3.2, 3.3, 3.4, 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7_
  
  - [x] 1.2 Update Pemesanan model fillable array and casts
    - Add new fields to $fillable array
    - Cast `package_specific_data` to array
    - Cast `tanggal_kunjungan` to date
    - _Requirements: 13.2, 13.3_

- [ ] 2. Create server-side validation infrastructure
  - [x] 2.1 Create DynamicBookingRequest Form Request class
    - Implement rules() method with conditional validation based on package type
    - Validate common fields (name, phone, email, date, notes)
    - Add phone number format validation (08/+62/62 prefix, 10-15 digits)
    - Add email validation (optional but valid format when provided)
    - Add date validation (today or future, max 365 days ahead)
    - Implement messages() method for Indonesian error messages
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 7.1, 7.2, 7.3, 7.4, 7.5, 8.1, 8.2, 8.3, 8.4, 9.1, 9.2, 9.4, 11.1, 11.2, 11.3, 11.4, 11.5_
  
  - [ ] 2.2 Add package-specific validation rules to DynamicBookingRequest
    - Implement culinary package validation (number of people, time slot, dietary requirements, table preference)
    - Implement fishing package validation (fishing type, rod count, duration, equipment, bait, terms agreement)
    - Implement event package validation (event type, attendees, duration, setup, catering, decoration, AV equipment)
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7_
  
  - [ ] 2.3 Add input sanitization to DynamicBookingRequest
    - Strip HTML tags from text inputs except notes
    - Escape special characters in notes field
    - Trim whitespace from all text inputs
    - Normalize phone number to standard Indonesian format
    - Convert all inputs to UTF-8 encoding
    - _Requirements: 25.1, 25.2, 25.3, 25.4, 25.5_

- [ ] 3. Implement backend booking service and controller
  - [ ] 3.1 Add storeDynamicBooking method to BookingController
    - Inject DynamicBookingRequest for validation
    - Retrieve package data from paket_wisata table
    - Generate unique booking code in GOD-YYYYMMDD-XXXX format
    - Calculate total price based on package type and selections
    - Store common fields and package_specific_data in database transaction
    - Create associated pembayaran record with calculated amount
    - Return JSON response with booking details and snap_token
    - Handle validation errors with HTTP 422 status
    - Handle server errors with HTTP 500 status and user-friendly messages
    - _Requirements: 11.1, 11.2, 11.3, 11.4, 11.5, 12.1, 12.2, 12.3, 12.4, 12.5, 13.1, 13.2, 13.3, 13.4, 13.5, 20.1, 20.2, 20.3, 20.4, 20.5, 22.1, 22.2, 22.3, 22.4, 22.5, 23.1, 23.2, 23.3_
  
  - [ ] 3.2 Implement price calculation logic for each package type
    - For culinary: multiply base price by number of people
    - For fishing: calculate based on rod count, duration, equipment rental, and bait purchases
    - For events: calculate based on attendees, duration, catering, decoration, and AV equipment selections
    - Apply pricing rules (++ suffix for private_room, nett for resto/fishing, "calculated at weighing" for Mancing Kiloan)
    - Round all prices to nearest Rupiah (no decimals)
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 22.1, 22.2, 22.3, 22.4, 22.5_
  
  - [ ] 3.3 Add route for dynamic booking submission
    - Add POST route /booking/dynamic to web.php
    - Apply CSRF middleware protection
    - Map route to BookingController@storeDynamicBooking
    - _Requirements: 23.1, 23.2, 23.3, 23.4_

- [ ] 4. Checkpoint - Verify backend infrastructure
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 5. Create Alpine.js dynamic booking modal component
  - [ ] 5.1 Create booking-modal.blade.php component
    - Set up Alpine.js component with x-data for modal state management
    - Initialize reactive data properties (formData, errors, loading, priceEstimate, packageType)
    - Implement openModal() function to accept packageId and packageType parameters
    - Implement closeModal() function with form reset and error clearing
    - Add modal overlay with backdrop click and ESC key handlers
    - Add body scroll prevention when modal is open
    - Structure modal with header, dynamic form body, and footer with submit button
    - _Requirements: 16.1, 16.2, 16.3, 16.4, 16.5, 16.6, 16.7, 18.1, 18.2, 18.3, 18.4, 18.5_
  
  - [ ] 5.2 Implement common form fields in booking modal
    - Add Customer Name text input with validation
    - Add Phone Number text input with real-time format validation
    - Add Email text input with optional validation
    - Add Booking Date input with date picker widget
    - Add Notes textarea for additional information
    - Bind all fields to Alpine.js formData object
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5_
  
  - [ ] 5.3 Implement dynamic field rendering based on package type
    - Create x-show directives for culinary-specific fields (number of people, time slot, dietary requirements, table preference)
    - Create x-show directives for fishing-specific fields (fishing type, rod count, duration, equipment rental, bait options, terms checkbox)
    - Create x-show directives for event-specific fields (event type, attendees, duration, setup, catering, decoration, AV equipment)
    - Use conditional rendering to hide/show fields based on packageType value
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 3.1, 3.2, 3.3, 3.4, 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7_
  
  - [ ] 5.4 Implement client-side validation with Alpine.js
    - Create validateField() function for individual field validation
    - Create validateForm() function for complete form validation before submission
    - Display error messages below invalid fields
    - Remove error messages on field correction (within 300ms)
    - Prevent form submission if validation errors exist
    - Focus first invalid field when validation fails
    - Use Indonesian language for all error messages
    - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5_
  
  - [ ] 5.5 Implement real-time price calculation
    - Create calculatePrice() function triggered by x-on:input/change events
    - Calculate price based on package type and form selections
    - Update priceEstimate within 300ms of field changes
    - Format price in Indonesian Rupiah with Rp prefix and thousand separators
    - Append ++ (excluding tax and service) for private_room packages
    - Append nett for the_waterfall_resto and fishing_lake packages
    - Display "Final price calculated at weighing" for Mancing Kiloan fishing type
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5_
  
  - [ ] 5.6 Implement form submission with loading states
    - Create submitBooking() async function with fetch API
    - Include CSRF token in request headers
    - Disable submit button during submission
    - Display loading spinner and "Memproses..." text on submit button
    - Prevent modal closing during submission
    - Handle successful response by closing modal and opening confirmation modal
    - Handle error responses by displaying errors next to fields
    - Re-enable submit button if submission fails
    - _Requirements: 19.1, 19.2, 19.3, 19.4, 19.5, 19.6, 20.1, 20.2, 20.3, 20.4, 20.5_
  
  - [ ] 5.7 Implement dependent field logic
    - Update duration dropdown options when fishing type changes
    - Show/hide equipment quantity selector based on equipment rental checkbox
    - Show/hide bait selectors based on bait purchase checkbox
    - Show/hide custom duration input when event duration is "Custom"
    - Disable dependent fields until parent field has valid value
    - _Requirements: 24.1, 24.2, 24.3, 24.4, 24.5_

- [ ] 6. Create confirmation modal component
  - [ ] 6.1 Create booking-confirmation-modal.blade.php component
    - Set up Alpine.js component for confirmation display
    - Display booking code prominently in large text
    - Show all submitted booking details in formatted layout
    - Display estimated price with appropriate disclaimer (++ or nett)
    - Add "Close" button to dismiss modal
    - Add "Share via WhatsApp" button with WhatsApp icon
    - _Requirements: 14.1, 14.2, 14.3, 14.4, 14.5, 14.6, 14.7_
  
  - [ ] 6.2 Implement WhatsApp integration in confirmation modal
    - Create formatWhatsAppMessage() function to build message with booking details
    - Include booking code, package type, booking date, and customer info in message
    - Use WhatsApp Web URL format: https://wa.me/{venue_phone}?text={encoded_message}
    - URL-encode message content before opening WhatsApp
    - Open WhatsApp link in new browser tab when "Share via WhatsApp" clicked
    - _Requirements: 15.1, 15.2, 15.3, 15.4, 15.5_

- [ ] 7. Style modal components with Tailwind CSS
  - [ ] 7.1 Apply responsive design styles to booking modal
    - Full-screen width with padding on mobile (< 768px)
    - Centered modal with max-width 600px on tablet+ (≥ 768px)
    - Minimum 14px font size for readability
    - Vertical stacking of form fields
    - Mobile-optimized input controls with appropriate input types
    - _Requirements: 17.1, 17.2, 17.3, 17.4, 17.5_
  
  - [ ] 7.2 Apply accessibility styles and attributes
    - Add aria-label attributes to all form inputs
    - Associate error messages with fields using aria-describedby
    - Ensure logical tab order through tabindex attributes
    - Add visible focus indicators (ring classes) for all interactive elements
    - Ensure 4.5:1 color contrast ratio for text elements
    - Add aria-live region for modal opening announcements
    - Add aria-live region for validation error count announcements
    - _Requirements: 21.1, 21.2, 21.3, 21.4, 21.5, 21.6, 21.7_
  
  - [ ] 7.3 Style loading states and error messages
    - Style submit button with spinner animation during loading
    - Style error messages in red with appropriate spacing
    - Add smooth transitions for error message appearance/disappearance
    - Style disabled state for submit button
    - Add hover and focus states for buttons and inputs
    - _Requirements: 19.1, 19.2, 19.3, 10.1, 10.2_

- [ ] 8. Integrate modal with package cards
  - [ ] 8.1 Update package card "Pesan" buttons to trigger booking modal
    - Add x-on:click handler to "Pesan" buttons
    - Pass packageId and jenis_paket to openModal function
    - Ensure modal opens with appropriate package type pre-selected
    - Test modal opening from culinary, fishing, and event package cards
    - _Requirements: 1.1, 1.2, 1.3, 18.5_
  
  - [ ] 8.2 Include modal components in main layout
    - Add @include for booking-modal component in layouts/app.blade.php
    - Add @include for booking-confirmation-modal component in layouts/app.blade.php
    - Ensure Alpine.js is loaded before modal components
    - Ensure CSRF token meta tag is present in head
    - _Requirements: 23.1_

- [ ] 9. Checkpoint - Test end-to-end booking flow
  - Ensure all tests pass, ask the user if questions arise.

- [ ]* 10. Write integration tests for booking workflow
  - [ ]*10.1 Create DynamicBookingTest feature test
    - Test successful booking submission for each package type
    - Test validation errors for missing required fields
    - Test phone number validation edge cases
    - Test email validation edge cases
    - Test date validation (past dates, future dates, max range)
    - Test CSRF protection
    - Test booking code generation uniqueness
    - Test price calculation accuracy
    - Test database transaction rollback on errors
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 8.1, 8.2, 8.3, 8.4, 9.1, 9.2, 9.4, 11.1, 11.2, 11.3, 11.4, 12.1, 12.2, 12.3, 12.4, 12.5, 23.1, 23.2, 23.3_
  
  - [ ]*10.2 Create browser test for modal interactions
    - Test modal opening and closing
    - Test backdrop click closing
    - Test ESC key closing
    - Test form reset on close
    - Test dynamic field rendering for each package type
    - Test client-side validation error display
    - Test real-time price calculation updates
    - Test form submission with loading states
    - Test confirmation modal display after successful submission
    - Test WhatsApp integration link generation
    - _Requirements: 1.1, 1.2, 1.3, 16.1, 16.2, 16.3, 16.4, 16.5, 18.1, 18.2, 18.3, 10.1, 10.2, 10.3, 6.1, 19.1, 19.2, 14.1, 15.1_

- [ ] 11. Final checkpoint and documentation
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional testing tasks and can be skipped for faster MVP delivery
- Each task references specific requirements for traceability
- The implementation follows a layered approach: database → backend → frontend → integration
- Checkpoints ensure incremental validation of each layer before proceeding
- CSRF protection is built into Laravel and requires minimal configuration
- Price calculation logic should be consistent between client (preview) and server (final)
- The JSON `package_specific_data` column provides flexibility for future package types without schema changes
- All validation error messages are in Indonesian per requirements
- Accessibility compliance requires manual testing with screen readers for full WCAG 2.1 AA validation

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1.1"] },
    { "id": 1, "tasks": ["1.2", "2.1"] },
    { "id": 2, "tasks": ["2.2", "2.3"] },
    { "id": 3, "tasks": ["3.1", "3.2"] },
    { "id": 4, "tasks": ["3.3", "5.1"] },
    { "id": 5, "tasks": ["5.2", "5.3", "5.4", "5.5"] },
    { "id": 6, "tasks": ["5.6", "5.7", "6.1"] },
    { "id": 7, "tasks": ["6.2", "7.1", "7.2", "7.3"] },
    { "id": 8, "tasks": ["8.1", "8.2"] },
    { "id": 9, "tasks": ["10.1", "10.2"] }
  ]
}
```
