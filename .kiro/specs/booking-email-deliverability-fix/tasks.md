# Implementation Plan: Booking Email Deliverability Fix

## Overview

This implementation optimizes transactional email delivery through Gmail SMTP by implementing email best practices including proper email headers, HTML structure compliance, plain text alternatives, rate limiting, and comprehensive error handling. The solution focuses on improving inbox placement rates for booking confirmation, payment success, and cancellation notification emails.

## Tasks

- [x] 1. Set up email configuration and infrastructure
  - Create mail configuration updates in `config/mail.php`
  - Add new environment variables for rate limiting and SMTP settings
  - Configure email authentication headers and timeouts
  - Set up spam trigger word list and validation thresholds
  - _Requirements: 1.1, 1.3, 1.4, 7.1, 17.1, 17.2_

- [x] 2. Create EmailService for centralized email management
  - [x] 2.1 Create EmailService class with rate limiting logic
    - Create `app/Services/EmailService.php` with IEmailService interface
    - Implement rate limiting using Laravel Cache with daily counters
    - Add methods: `checkRateLimit()`, `getRemainingDailyQuota()`, `getRateLimitStatus()`
    - Implement 80% warning threshold logging
    - Implement daily counter reset logic with cache expiration
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5_
  
  - [x] 2.2 Implement email sending methods with error handling
    - Add `sendBookingConfirmation(Pemesanan $pemesanan): bool` method
    - Add `sendPaymentSuccess(Pemesanan $pemesanan): bool` method
    - Add `sendCancellationNotification(Pemesanan $pemesanan, string $reason): bool` method
    - Implement SMTP connection retry logic (2 attempts, 5-second delay)
    - Add template variable validation before email sending
    - _Requirements: 9.1, 9.2, 9.3, 17.3, 17.4_
  
  - [x] 2.3 Add email logging functionality
    - Implement `logEmailSent()` method with timestamp, recipient, booking code
    - Implement `logEmailFailed()` method with SMTP error codes
    - Add rate limit warning logs at 80% threshold
    - Log authentication validation results
    - _Requirements: 1.5, 9.1, 9.2, 9.3, 9.4, 9.5, 9.6_
  
  - [ ]* 2.4 Write unit tests for EmailService
    - Test rate limiting logic with cache
    - Test retry mechanism for temporary SMTP errors
    - Test validation of template variables
    - Test logging functionality for success and failure cases
    - _Requirements: 7.1, 7.2, 7.3, 17.3_

- [x] 3. Create optimized Mailable classes with proper headers
  - [x] 3.1 Create BookingConfirmationMail class
    - Create `app/Mail/BookingConfirmationMail.php` extending Mailable
    - Implement `build()` method with HTML and plain text views
    - Add email headers: X-Priority=3, Precedence=bulk, Auto-Submitted
    - Generate unique Message-ID with booking code
    - Set subject format: "Konfirmasi Booking - {kode_booking}"
    - _Requirements: 5.1, 5.3, 5.5, 5.7, 11.5_
  
  - [x] 3.2 Enhance PaymentSuccessMail class
    - Update existing `app/Mail/PaymentSuccessMail.php` with header configuration
    - Add email headers: X-Priority=1, Precedence=bulk, Auto-Submitted
    - Generate unique Message-ID with booking code
    - Add plain text view support with `text()` method
    - Verify subject format: "Pembayaran Berhasil - E-Ticket {kode_booking}"
    - _Requirements: 5.2, 5.4, 5.5, 5.7, 5.8, 12.5_
  
  - [x] 3.3 Create CancellationNotificationMail class
    - Create `app/Mail/CancellationNotificationMail.php` extending Mailable
    - Implement `build()` method with HTML and plain text views
    - Add email headers: X-Priority=3, Precedence=bulk, Auto-Submitted
    - Generate unique Message-ID with booking code
    - Set subject format: "Pembatalan Booking - {kode_booking}"
    - Pass cancellation reason and refund info to template
    - _Requirements: 5.1, 5.3, 5.5, 5.7, 13.6_
  
  - [ ]* 3.4 Write unit tests for Mailable classes
    - Test email header configuration for all Mailable classes
    - Test Message-ID generation uniqueness
    - Test subject line formatting with booking codes
    - Test plain text and HTML view rendering
    - _Requirements: 5.1, 5.2, 5.3, 5.7, 5.8_

- [x] 4. Checkpoint - Verify email service and mailable classes
  - Ensure all tests pass, ask the user if questions arise.

- [x] 5. Create optimized HTML email templates
  - [x] 5.1 Create booking confirmation HTML template
    - Create `resources/views/emails/booking-confirmation.blade.php`
    - Use table-based layout with valid HTML5 structure (DOCTYPE, meta tags)
    - Apply inline CSS styles for all elements
    - Include booking details: code, package, date, people, total amount
    - Add payment instructions with Midtrans link using absolute HTTPS URL
    - Include customer support contact information
    - Add alt text, width, height for all images
    - Use web-safe fonts (Arial, Helvetica, sans-serif)
    - Ensure text-to-image ratio >= 60%
    - _Requirements: 2.1, 2.6, 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 6.1, 6.2, 11.2, 11.3, 11.4, 15.1, 15.2_
  
  - [x] 5.2 Create booking confirmation plain text template
    - Create `resources/views/emails/booking-confirmation-text.blade.php`
    - Format with proper line breaks and spacing
    - Include all booking details (code, package, date, amount)
    - Add payment instructions with full URL
    - Include contact information
    - _Requirements: 4.1, 4.4, 4.5_
  
  - [x] 5.3 Optimize payment success HTML template
    - Update `resources/views/emails/payment-success.blade.php` for deliverability
    - Verify table-based layout and inline CSS compliance
    - Update booking confirmation link to use absolute HTTPS URL
    - Verify image optimization (alt text, dimensions, file size < 100KB)
    - Ensure no spam trigger words in content
    - Add company physical address in footer
    - _Requirements: 2.1, 2.2, 3.3, 6.1, 6.2, 12.2, 12.3, 12.4, 15.1, 15.2, 18.5_
  
  - [x] 5.4 Create payment success plain text template
    - Create `resources/views/emails/payment-success-text.blade.php`
    - Format with proper line breaks and spacing
    - Include all payment details (transaction ID, payment method, date)
    - Include all booking details (code, package, date, amount)
    - Add booking confirmation page URL
    - _Requirements: 4.2, 4.4, 4.5_
  
  - [x] 5.5 Create cancellation notification HTML template
    - Create `resources/views/emails/cancellation-notification.blade.php`
    - Use table-based layout with valid HTML5 structure
    - Apply inline CSS styles with warning/alert styling
    - Include cancelled booking details: code, package, date
    - Display cancellation reason clearly
    - Add refund information section (conditional)
    - Include rebooking assistance contact information
    - _Requirements: 3.1, 3.2, 3.3, 13.2, 13.3, 13.4, 13.5_
  
  - [x] 5.6 Create cancellation notification plain text template
    - Create `resources/views/emails/cancellation-notification-text.blade.php`
    - Format with proper line breaks
    - Include cancelled booking details and cancellation reason
    - Add refund information (if applicable)
    - Include contact information for rebooking
    - _Requirements: 4.3, 4.4, 4.5_
  
  - [ ]* 5.7 Write template validation tests
    - Test HTML structure validation (DOCTYPE, meta tags, table layout)
    - Test inline CSS compliance (no external stylesheets)
    - Test image attributes (alt, width, height)
    - Test URL validation (absolute HTTPS only)
    - Test text-to-image ratio calculation (>= 60%)
    - Test spam trigger word detection in subject and body
    - Test UTF-8 character encoding for Indonesian text
    - _Requirements: 2.1, 2.2, 2.3, 3.1, 3.3, 3.9, 6.1, 6.2, 14.1, 14.2_

- [ ] 6. Create email template validator service
  - [ ] 6.1 Create EmailTemplateValidator class
    - Create `app/Services/EmailTemplateValidator.php` with IEmailTemplateValidator interface
    - Implement `validateHtmlStructure(string $templatePath): ValidationResult` method
    - Implement `calculateTextToImageRatio(string $renderedHtml): float` method
    - Implement `detectSpamTriggers(string $content): array` method
    - Implement `validateUrls(string $renderedHtml): array` method
    - _Requirements: 2.2, 2.3, 6.1, 6.2, 6.6_
  
  - [x] 6.2 Add template validation before email sending
    - Integrate EmailTemplateValidator into EmailService
    - Validate template data completeness before rendering
    - Log validation errors with missing field details
    - Throw EmailValidationException for missing required fields
    - _Requirements: 10.2, 10.3, 10.4_
  
  - [ ]* 6.3 Write unit tests for EmailTemplateValidator
    - Test HTML structure validation logic
    - Test text-to-image ratio calculation accuracy
    - Test spam trigger word detection
    - Test absolute HTTPS URL validation
    - _Requirements: 2.1, 2.2, 2.3, 6.1, 6.2_

- [x] 7. Checkpoint - Verify email templates and validation
  - Ensure all tests pass, ask the user if questions arise.

- [x] 8. Integrate EmailService into booking workflow
  - [x] 8.1 Update BookingController for booking confirmation emails
    - Inject EmailService into `BookingController`
    - Add email sending after booking creation
    - Wrap email sending in try-catch for error handling
    - Log booking code and recipient on email failure
    - Continue booking transaction even if email fails
    - _Requirements: 9.1, 9.2, 9.3, 11.1, 16.1, 16.3_
  
  - [x] 8.2 Update payment webhook for payment success emails
    - Inject EmailService into payment webhook handler
    - Add email sending after payment confirmation
    - Ensure synchronous email sending before status update
    - Handle EmailRateLimitException by queuing for next day
    - Log SMTP errors with transaction details
    - _Requirements: 9.1, 9.2, 9.3, 12.1, 16.2, 16.3_
  
  - [x] 8.3 Update cancellation logic for notification emails
    - Inject EmailService into cancellation service/controller
    - Add email sending after booking cancellation
    - Pass cancellation reason to CancellationNotificationMail
    - Include refund information if applicable
    - Handle email sending errors gracefully
    - _Requirements: 13.1, 13.2, 13.3, 13.4, 13.5_
  
  - [ ]* 8.4 Write integration tests for booking workflow
    - Test booking confirmation email sent on booking creation
    - Test payment success email sent on payment confirmation
    - Test cancellation notification email sent on cancellation
    - Test error handling when email sending fails
    - Test rate limiting behavior when quota exceeded
    - Use Mail::fake() for email assertions
    - _Requirements: 11.1, 12.1, 13.1, 16.1, 16.2, 16.3_

- [x] 9. Create artisan command for email testing
  - [x] 9.1 Create SendTestEmail artisan command
    - Create `app/Console/Commands/SendTestEmail.php`
    - Accept email type argument (booking, payment, cancellation)
    - Accept recipient email option
    - Generate mock booking data for testing
    - Send test email using EmailService
    - Display success/failure message with details
    - _Requirements: 10.1_
  
  - [x] 9.2 Add test mode configuration
    - Add `MAIL_TEST_MODE` environment variable
    - Add `MAIL_TEST_ADDRESS` environment variable
    - Update EmailService to check test mode
    - Redirect all emails to test address when test mode enabled
    - Log original recipient when redirecting
    - _Requirements: 10.5_
  
  - [ ]* 9.3 Write tests for test email command
    - Test command execution with different email types
    - Test mock data generation
    - Test email redirection in test mode
    - _Requirements: 10.1, 10.5_

- [x] 10. Create custom exception classes
  - [x] 10.1 Create EmailRateLimitException
    - Create `app/Exceptions/EmailRateLimitException.php`
    - Extend base Exception class
    - Include current count, limit, and date in exception message
    - _Requirements: 7.3_
  
  - [x] 10.2 Create EmailValidationException
    - Create `app/Exceptions/EmailValidationException.php`
    - Extend base Exception class
    - Include missing fields information in exception message
    - _Requirements: 10.4_
  
  - [x] 10.3 Create EmailAuthenticationException
    - Create `app/Exceptions/EmailAuthenticationException.php`
    - Extend base Exception class
    - Include SMTP configuration context in exception
    - _Requirements: 1.5, 17.6_

- [x] 11. Update environment configuration
  - [x] 11.1 Add new environment variables to .env.example
    - Add MAIL_DAILY_LIMIT with default 100
    - Add MAIL_RATE_WARNING_THRESHOLD with default 0.8
    - Add MAIL_SMTP_TIMEOUT with default 10
    - Add MAIL_SMTP_READ_TIMEOUT with default 15
    - Add MAIL_SMTP_RETRY_ATTEMPTS with default 2
    - Add MAIL_SMTP_RETRY_DELAY with default 5
    - Add MAIL_VALIDATION_ENABLED with default true
    - Add MAIL_CHECK_SPAM_TRIGGERS with default true
    - Add MAIL_MIN_TEXT_RATIO with default 0.6
    - Add APP_DOMAIN for absolute URLs
    - Add MAIL_TEST_MODE and MAIL_TEST_ADDRESS
    - _Requirements: 1.3, 1.4, 7.1, 7.2, 17.1, 17.2, 17.3_
  
  - [x] 11.2 Verify Gmail SMTP credentials in .env
    - Verify MAIL_HOST=smtp.gmail.com
    - Verify MAIL_PORT=587
    - Verify MAIL_ENCRYPTION=tls
    - Verify MAIL_USERNAME is full Gmail address
    - Verify MAIL_PASSWORD is app-specific password (not regular password)
    - Verify MAIL_FROM_ADDRESS matches Gmail account
    - Verify MAIL_FROM_NAME is "The Waterfall Tourism"
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 17.5, 17.6_

- [x] 12. Checkpoint - Verify integration and configuration
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 13. Create documentation and testing guide
  - [ ] 13.1 Create EMAIL_DELIVERABILITY_GUIDE.md
    - Document email optimization best practices implemented
    - Document rate limiting configuration and behavior
    - Document email template structure requirements
    - Document spam trigger words to avoid
    - Document testing procedures with artisan command
    - Include troubleshooting guide for common SMTP errors
    - _Requirements: 1.1, 1.2, 1.3, 2.1, 2.2, 2.3, 7.1_
  
  - [ ] 13.2 Add inline documentation to email templates
    - Add comments explaining table-based layout requirements
    - Add comments for inline CSS necessity
    - Add comments for image optimization requirements
    - Add comments for absolute URL requirements
    - _Requirements: 3.1, 3.3, 3.4, 3.5, 6.1, 6.2_

- [ ] 14. Final validation and testing
  - [ ] 14.1 Send test emails to multiple email providers
    - Test with Gmail accounts (verify inbox placement)
    - Test with Yahoo accounts (verify inbox placement)
    - Test with Outlook accounts (verify inbox placement)
    - Document deliverability results for each provider
    - _Requirements: 1.1, 1.2, 2.1, 2.2_
  
  - [ ] 14.2 Validate email authentication
    - Send test email and check email headers
    - Verify SPF pass status in email headers
    - Verify DKIM signature present in email headers
    - Check Message-ID format and uniqueness
    - Verify all custom headers present (X-Priority, Precedence, Auto-Submitted)
    - _Requirements: 1.1, 1.2, 5.1, 5.2, 5.3, 5.4, 5.5, 5.7_
  
  - [ ] 14.3 Test rate limiting functionality
    - Send multiple test emails to approach rate limit
    - Verify warning log appears at 80% threshold
    - Verify email sending blocked at 100% limit
    - Verify daily counter resets at midnight
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5_
  
  - [ ] 14.4 Test error handling and retry logic
    - Simulate SMTP connection timeout
    - Verify retry attempts occur (2 times with 5s delay)
    - Verify error logging includes all required details
    - Test permanent error handling (no retries)
    - _Requirements: 9.1, 9.2, 9.3, 9.4, 17.3, 17.4_

- [ ] 15. Final checkpoint - Complete deliverability optimization
  - Ensure all tests pass, verify email deliverability improvements, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional testing tasks and can be skipped for faster MVP delivery
- All email templates must use table-based layouts with inline CSS for maximum email client compatibility
- Gmail SMTP automatically handles SPF and DKIM signing - no manual DNS configuration needed
- Rate limiting is set to 100 emails/day for Gmail free tier - adjust if using paid Google Workspace
- All URLs in email templates must use absolute HTTPS format to avoid spam scoring
- Email sending is synchronous for critical transactional emails (booking confirmation, payment receipt)
- Template validation happens before email sending to catch missing data early
- SMTP connection has 10-second timeout with 2 retry attempts to ensure reliability
- Each email type references specific requirements for traceability and validation
- Test mode allows safe testing by redirecting all emails to a configured test address

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1", "10.1", "10.2", "10.3"] },
    { "id": 1, "tasks": ["2.1", "3.1", "3.2", "3.3", "11.1", "11.2"] },
    { "id": 2, "tasks": ["2.2", "2.3", "2.4", "3.4", "5.1", "5.2", "6.1"] },
    { "id": 3, "tasks": ["5.3", "5.4", "5.5", "5.6", "6.2", "6.3"] },
    { "id": 4, "tasks": ["5.7", "8.1", "8.2", "8.3", "9.1", "9.2"] },
    { "id": 5, "tasks": ["8.4", "9.3", "13.1", "13.2"] },
    { "id": 6, "tasks": ["14.1", "14.2", "14.3", "14.4"] }
  ]
}
```
