# Requirements Document

## Introduction

The Dynamic Booking Form feature replaces the existing generic booking modal with an intelligent form system that adapts its fields based on the selected package type. The system must present appropriate booking fields for three distinct package categories: culinary/restaurant experiences (The Waterfall Resto), fishing activities (Monster Fish), and private event spaces (Private Room). The form provides real-time price calculation, comprehensive validation, booking confirmation, and WhatsApp integration for customer communication.

## Glossary

- **Booking_Form**: The dynamic modal component that displays package-specific booking fields
- **Package_Type**: The categorization of tourism packages stored in `jenis_paket` column ('the_waterfall_resto', 'fishing_lake', 'private_room')
- **Booking_System**: The backend Laravel application managing booking data
- **Customer**: A user attempting to make a reservation through the website
- **Booking_Code**: A unique identifier for each reservation formatted as GOD-YYYYMMDD-XXXX
- **Price_Calculator**: The component that computes estimated booking costs based on selections
- **Validation_Engine**: The dual-layer validation system (client-side Alpine.js and server-side Laravel)
- **Confirmation_Modal**: The success screen displaying booking details after successful submission
- **WhatsApp_Integration**: The feature enabling customers to send booking details via WhatsApp

## Requirements

### Requirement 1: Dynamic Form Field Rendering

**User Story:** As a customer, I want to see booking fields relevant to my chosen package type, so that I can provide appropriate information for my reservation.

#### Acceptance Criteria

1. WHEN a Customer clicks "Pesan" on a package card with `jenis_paket='the_waterfall_resto'`, THE Booking_Form SHALL display culinary-specific fields
2. WHEN a Customer clicks "Pesan" on a package card with `jenis_paket='fishing_lake'`, THE Booking_Form SHALL display fishing-specific fields
3. WHEN a Customer clicks "Pesan" on a package card with `jenis_paket='private_room'`, THE Booking_Form SHALL display event-specific fields
4. THE Booking_Form SHALL display common fields (Customer Name, Phone Number, Email, Booking Date, Notes) for all Package_Type values
5. THE Booking_Form SHALL hide fields not applicable to the selected Package_Type

### Requirement 2: Common Field Collection

**User Story:** As a customer, I want to provide my basic contact information, so that the venue can confirm my booking.

#### Acceptance Criteria

1. THE Booking_Form SHALL collect Customer Name as a required text field
2. THE Booking_Form SHALL collect Phone Number as a required text field
3. THE Booking_Form SHALL collect Email as an optional text field
4. THE Booking_Form SHALL collect Booking Date as a required date field with date picker widget
5. THE Booking_Form SHALL collect Notes as an optional textarea field
6. THE Booking_Form SHALL display all common fields regardless of Package_Type

### Requirement 3: Culinary Package Field Collection

**User Story:** As a customer booking a restaurant experience, I want to specify dining preferences, so that the venue can prepare appropriate seating and accommodations.

#### Acceptance Criteria

1. WHERE `jenis_paket='the_waterfall_resto'`, THE Booking_Form SHALL collect Number of People as a required numeric field with minimum value 1
2. WHERE `jenis_paket='the_waterfall_resto'`, THE Booking_Form SHALL collect Preferred Time Slot as a required dropdown with options "Lunch 11:00-15:00" and "Dinner 18:00-21:00"
3. WHERE `jenis_paket='the_waterfall_resto'`, THE Booking_Form SHALL collect Special Dietary Requirements as an optional textarea field
4. WHERE `jenis_paket='the_waterfall_resto'`, THE Booking_Form SHALL collect Table Preference as an optional dropdown with options "Indoor", "Outdoor", and "Near Waterfall"

### Requirement 4: Fishing Package Field Collection

**User Story:** As a customer booking a fishing experience, I want to specify fishing type and equipment needs, so that the venue can prepare the appropriate setup.

#### Acceptance Criteria

1. WHERE `jenis_paket='fishing_lake'`, THE Booking_Form SHALL collect Fishing Type as a required radio button group with options "Sewa Joran saja", "Mancing Tarikan", "Mancing Jackpot", and "Mancing Kiloan"
2. WHERE `jenis_paket='fishing_lake'`, THE Booking_Form SHALL collect Number of Rods as a required numeric field with minimum value 1
3. WHERE `jenis_paket='fishing_lake'`, THE Booking_Form SHALL collect Duration as a required dropdown with options based on Fishing Type selected
4. WHERE `jenis_paket='fishing_lake'`, THE Booking_Form SHALL collect Equipment Rental Needed as a boolean checkbox
5. WHERE `jenis_paket='fishing_lake'`, THE Booking_Form SHALL collect Bait Purchase preferences as checkboxes with quantity fields for "Anak Ikan Komet" and "Umpan Jadi"
6. WHERE `jenis_paket='fishing_lake'`, THE Booking_Form SHALL display a required checkbox for Terms Agreement with text "I agree to fishing rules - no prohibited baits, operating hours 09:00-21:00 WIB"

### Requirement 5: Event Package Field Collection

**User Story:** As a customer booking a private event space, I want to specify event details and requirements, so that the venue can prepare appropriate facilities and services.

#### Acceptance Criteria

1. WHERE `jenis_paket='private_room'`, THE Booking_Form SHALL collect Event Type as a required dropdown with options "Gathering", "Meeting", "Wedding", "Engagement", and "Other"
2. WHERE `jenis_paket='private_room'`, THE Booking_Form SHALL collect Expected Attendees as a required numeric field with minimum value 10
3. WHERE `jenis_paket='private_room'`, THE Booking_Form SHALL collect Event Duration as a required dropdown with options "Half Day", "Full Day", and "Custom"
4. WHERE `jenis_paket='private_room'`, THE Booking_Form SHALL collect Setup Preference as a required dropdown with options "Theater", "U-Shape", "Classroom", and "Banquet"
5. WHERE `jenis_paket='private_room'`, THE Booking_Form SHALL collect Catering Required as a required radio button group with options "Yes" and "No"
6. WHERE `jenis_paket='private_room'`, THE Booking_Form SHALL collect Decoration Required as a required radio button group with options "Yes" and "No"
7. WHERE `jenis_paket='private_room'`, THE Booking_Form SHALL collect AV Equipment Needed as optional checkboxes with options "Projector", "Sound System", "Microphone", and "Whiteboard"

### Requirement 6: Real-Time Price Calculation

**User Story:** As a customer, I want to see the estimated price update as I fill the form, so that I understand the cost before submitting my booking.

#### Acceptance Criteria

1. WHEN a Customer changes any price-affecting field, THE Price_Calculator SHALL recompute the estimated total within 300 milliseconds
2. WHEN displaying prices for `jenis_paket='private_room'`, THE Price_Calculator SHALL append "++ (excluding tax and service)" to the price display
3. WHEN displaying prices for `jenis_paket='the_waterfall_resto'` or `jenis_paket='fishing_lake'`, THE Price_Calculator SHALL append "nett" to the price display
4. WHEN Fishing Type is "Mancing Kiloan", THE Price_Calculator SHALL display "Final price calculated at weighing" instead of a numeric total
5. THE Price_Calculator SHALL display the estimated price in Indonesian Rupiah format with thousand separators

### Requirement 7: Phone Number Validation

**User Story:** As a system operator, I want to ensure phone numbers are in valid Indonesian format, so that we can reliably contact customers.

#### Acceptance Criteria

1. WHEN a Customer enters a Phone Number, THE Validation_Engine SHALL verify it starts with "08" or "+62" or "62"
2. WHEN a Customer enters a Phone Number, THE Validation_Engine SHALL verify it contains only numeric characters after the prefix
3. WHEN a Customer enters a Phone Number with length less than 10 digits, THE Validation_Engine SHALL reject it with error message "Nomor telepon minimal 10 digit"
4. WHEN a Customer enters a Phone Number with length greater than 15 digits, THE Validation_Engine SHALL reject it with error message "Nomor telepon maksimal 15 digit"
5. IF Phone Number validation fails, THEN THE Booking_Form SHALL prevent form submission

### Requirement 8: Email Validation

**User Story:** As a system operator, I want to validate email addresses when provided, so that we can send confirmation emails successfully.

#### Acceptance Criteria

1. WHEN a Customer provides an Email value, THE Validation_Engine SHALL verify it matches standard email format (local@domain)
2. IF Email validation fails, THEN THE Validation_Engine SHALL display error message "Format email tidak valid"
3. WHEN Email field is empty, THE Validation_Engine SHALL allow form submission
4. THE Validation_Engine SHALL reject Email values exceeding 255 characters

### Requirement 9: Date Validation

**User Story:** As a system operator, I want to ensure booking dates are valid and in the future, so that we prevent invalid reservations.

#### Acceptance Criteria

1. WHEN a Customer selects a Booking Date, THE Validation_Engine SHALL verify the date is today or in the future
2. IF Booking Date is in the past, THEN THE Validation_Engine SHALL display error message "Tanggal booking harus hari ini atau setelahnya"
3. THE Booking_Form SHALL display a date picker widget for Booking Date field
4. THE Validation_Engine SHALL reject Booking Date values more than 365 days in the future with error message "Booking maksimal 1 tahun ke depan"

### Requirement 10: Client-Side Validation

**User Story:** As a customer, I want immediate feedback on form errors, so that I can correct them before submitting.

#### Acceptance Criteria

1. WHEN a Customer leaves a required field empty, THE Validation_Engine SHALL display error message below the field
2. WHEN a Customer corrects an invalid field, THE Validation_Engine SHALL remove the error message within 300 milliseconds
3. THE Validation_Engine SHALL validate all fields using Alpine.js before allowing form submission
4. IF any validation error exists, THEN THE Booking_Form SHALL prevent form submission and focus the first invalid field
5. THE Validation_Engine SHALL display validation errors in Indonesian language

### Requirement 11: Server-Side Validation

**User Story:** As a system operator, I want server-side validation to prevent malicious or malformed data, so that our database remains consistent.

#### Acceptance Criteria

1. WHEN the Booking_System receives a form submission, THE Validation_Engine SHALL validate all required fields are present
2. WHEN the Booking_System receives a form submission, THE Validation_Engine SHALL validate all field values match expected data types
3. WHEN the Booking_System receives a form submission, THE Validation_Engine SHALL validate Package_Type exists in `paket_wisata` table
4. IF server-side validation fails, THEN THE Booking_System SHALL return HTTP 422 status with validation error messages in JSON format
5. THE Booking_System SHALL implement validation using Laravel Form Request class

### Requirement 12: Booking Code Generation

**User Story:** As a system operator, I want unique booking codes for each reservation, so that we can track and reference bookings easily.

#### Acceptance Criteria

1. WHEN the Booking_System creates a new booking, THE Booking_System SHALL generate a Booking_Code in format "GOD-YYYYMMDD-XXXX"
2. THE Booking_System SHALL set YYYYMMDD portion of Booking_Code to the current date
3. THE Booking_System SHALL set XXXX portion of Booking_Code to a sequential number starting from 0001 for each day
4. THE Booking_System SHALL ensure Booking_Code values are unique across all records in `pemesanan` table
5. WHEN generating sequential number, THE Booking_System SHALL pad with leading zeros to maintain 4-digit format

### Requirement 13: Booking Persistence

**User Story:** As a system operator, I want booking data saved to the database, so that we can manage reservations.

#### Acceptance Criteria

1. WHEN validation passes, THE Booking_System SHALL insert a new record into `pemesanan` table
2. THE Booking_System SHALL set `status` field to 'pending' for new bookings
3. THE Booking_System SHALL store all common fields and package-specific fields in the appropriate columns
4. THE Booking_System SHALL set `created_at` timestamp to current server time
5. THE Booking_System SHALL commit the database transaction before displaying confirmation

### Requirement 14: Confirmation Modal Display

**User Story:** As a customer, I want to see confirmation of my booking details, so that I know my reservation was recorded successfully.

#### Acceptance Criteria

1. WHEN booking submission succeeds, THE Booking_System SHALL close the Booking_Form
2. WHEN booking submission succeeds, THE Booking_System SHALL display the Confirmation_Modal
3. THE Confirmation_Modal SHALL display the generated Booking_Code prominently
4. THE Confirmation_Modal SHALL display all submitted booking details in a formatted layout
5. THE Confirmation_Modal SHALL display estimated price with appropriate disclaimer (++ or nett)
6. THE Confirmation_Modal SHALL provide a "Close" button to dismiss the modal
7. THE Confirmation_Modal SHALL display a "Share via WhatsApp" button

### Requirement 15: WhatsApp Integration

**User Story:** As a customer, I want to send my booking details via WhatsApp, so that I can communicate with the venue directly.

#### Acceptance Criteria

1. WHEN a Customer clicks "Share via WhatsApp" in Confirmation_Modal, THE WhatsApp_Integration SHALL open WhatsApp with pre-filled message
2. THE WhatsApp_Integration SHALL format the message to include Booking_Code, Package_Type, Booking Date, and customer contact information
3. THE WhatsApp_Integration SHALL use WhatsApp Web URL format "https://wa.me/{venue_phone}?text={encoded_message}"
4. THE WhatsApp_Integration SHALL open WhatsApp in a new browser tab
5. THE WhatsApp_Integration SHALL URL-encode the message content before opening WhatsApp

### Requirement 16: Modal Behavior

**User Story:** As a customer, I want intuitive modal interactions, so that I can easily open and close the booking form.

#### Acceptance Criteria

1. WHEN a Customer clicks "Pesan" button, THE Booking_Form SHALL open with smooth fade-in animation within 300 milliseconds
2. WHEN Booking_Form is open, THE Booking_Form SHALL display a semi-transparent backdrop behind the modal
3. WHEN a Customer clicks the "X" close button, THE Booking_Form SHALL close with smooth fade-out animation
4. WHEN a Customer clicks the backdrop area outside the modal, THE Booking_Form SHALL close
5. WHEN a Customer presses ESC key while Booking_Form is open, THE Booking_Form SHALL close
6. WHEN Booking_Form opens, THE Booking_Form SHALL prevent body scrolling
7. WHEN Booking_Form closes, THE Booking_Form SHALL restore body scrolling

### Requirement 17: Responsive Design

**User Story:** As a customer on a mobile device, I want the booking form to work properly on my screen, so that I can make reservations from any device.

#### Acceptance Criteria

1. WHEN viewport width is less than 768 pixels, THE Booking_Form SHALL occupy full screen width with padding
2. WHEN viewport width is 768 pixels or greater, THE Booking_Form SHALL display as a centered modal with maximum width 600 pixels
3. THE Booking_Form SHALL use mobile-optimized input controls on devices with touch screens
4. THE Booking_Form SHALL maintain readable text size across all viewport sizes with minimum 14px font size
5. THE Booking_Form SHALL stack form fields vertically on all screen sizes for optimal mobile usability

### Requirement 18: Form Reset Behavior

**User Story:** As a customer, I want the form to reset when I close it without submitting, so that I start fresh when opening it again.

#### Acceptance Criteria

1. WHEN a Customer closes Booking_Form without submitting, THE Booking_Form SHALL clear all field values
2. WHEN a Customer closes Booking_Form without submitting, THE Booking_Form SHALL remove all validation error messages
3. WHEN a Customer closes Booking_Form without submitting, THE Price_Calculator SHALL reset to zero
4. WHEN Booking_Form opens, THE Booking_Form SHALL focus the Customer Name field
5. WHEN Booking_Form opens for a specific package, THE Booking_Form SHALL pre-load the Package_Type but keep all other fields empty

### Requirement 19: Loading States

**User Story:** As a customer, I want visual feedback during form submission, so that I know the system is processing my request.

#### Acceptance Criteria

1. WHEN a Customer clicks the submit button, THE Booking_Form SHALL disable the submit button
2. WHEN form submission is in progress, THE Booking_Form SHALL display a loading spinner on the submit button
3. WHEN form submission is in progress, THE Booking_Form SHALL display text "Memproses..." on the submit button
4. WHEN form submission is in progress, THE Booking_Form SHALL prevent modal from being closed
5. IF form submission fails, THEN THE Booking_Form SHALL re-enable the submit button and display error message
6. THE Booking_Form SHALL complete submission within 5 seconds under normal network conditions

### Requirement 20: Error Handling

**User Story:** As a customer, I want clear error messages when something goes wrong, so that I understand what happened and what to do next.

#### Acceptance Criteria

1. IF server returns validation errors, THEN THE Booking_Form SHALL display errors next to corresponding fields
2. IF server returns HTTP 500 error, THEN THE Booking_Form SHALL display message "Terjadi kesalahan server. Silakan coba lagi."
3. IF network request fails, THEN THE Booking_Form SHALL display message "Koneksi gagal. Periksa internet Anda dan coba lagi."
4. IF request timeout occurs after 30 seconds, THEN THE Booking_Form SHALL display message "Permintaan timeout. Silakan coba lagi."
5. THE Booking_Form SHALL log all errors to browser console for debugging purposes

### Requirement 21: Accessibility Compliance

**User Story:** As a customer using assistive technology, I want the booking form to be accessible, so that I can complete reservations independently.

#### Acceptance Criteria

1. THE Booking_Form SHALL provide aria-label attributes for all form inputs
2. THE Booking_Form SHALL associate error messages with form fields using aria-describedby
3. THE Booking_Form SHALL maintain logical tab order through all interactive elements
4. THE Booking_Form SHALL provide visible focus indicators for all interactive elements
5. THE Booking_Form SHALL ensure color contrast ratio of at least 4.5:1 for all text elements
6. THE Booking_Form SHALL announce modal opening to screen readers using aria-live region
7. WHEN validation errors occur, THE Booking_Form SHALL announce error count to screen readers

### Requirement 22: Price Data Source

**User Story:** As a system operator, I want price calculations to use accurate data from the database, so that customers see correct pricing.

#### Acceptance Criteria

1. THE Price_Calculator SHALL retrieve base price from `paket_wisata` table for the selected package
2. THE Price_Calculator SHALL apply quantity multipliers based on Number of People, Number of Rods, or Expected Attendees
3. THE Price_Calculator SHALL add costs for optional services when selected (equipment rental, catering, decoration, AV equipment)
4. THE Price_Calculator SHALL format price output according to Indonesian Rupiah conventions with "Rp" prefix
5. THE Price_Calculator SHALL round all calculated prices to nearest Rupiah (no decimal places)

### Requirement 23: CSRF Protection

**User Story:** As a system operator, I want CSRF protection on form submissions, so that our application is secure against cross-site request forgery attacks.

#### Acceptance Criteria

1. THE Booking_Form SHALL include Laravel CSRF token in form submission
2. THE Booking_System SHALL verify CSRF token on every POST request
3. IF CSRF token is invalid or missing, THEN THE Booking_System SHALL return HTTP 419 status with error message
4. THE Booking_Form SHALL regenerate CSRF token after successful submission
5. THE Booking_Form SHALL handle CSRF token mismatch gracefully with user-friendly error message

### Requirement 24: Form Field Dependencies

**User Story:** As a customer, I want form fields to update appropriately when I change related selections, so that I only see relevant options.

#### Acceptance Criteria

1. WHEN a Customer selects Fishing Type, THE Booking_Form SHALL update Duration dropdown options to match available durations for that type
2. WHEN a Customer enables Equipment Rental checkbox, THE Booking_Form SHALL display equipment quantity selector
3. WHEN a Customer enables Bait Purchase checkbox, THE Booking_Form SHALL display bait type and quantity selectors
4. WHEN a Customer selects Event Duration as "Custom", THE Booking_Form SHALL display custom duration input field
5. THE Booking_Form SHALL disable dependent fields until their parent field has a valid value

### Requirement 25: Input Sanitization

**User Story:** As a system operator, I want user inputs sanitized before storage, so that we prevent XSS attacks and data corruption.

#### Acceptance Criteria

1. THE Booking_System SHALL strip HTML tags from all text input fields except Notes
2. THE Booking_System SHALL escape special characters in Notes field for safe database storage
3. THE Booking_System SHALL trim whitespace from beginning and end of all text inputs
4. THE Booking_System SHALL normalize Phone Number format to standard Indonesian format before storage
5. THE Booking_System SHALL convert all text inputs to UTF-8 encoding before storage
