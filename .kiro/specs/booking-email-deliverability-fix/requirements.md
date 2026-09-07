# Requirements Document

## Introduction

The Booking Email Deliverability Fix feature optimizes transactional emails sent through Gmail SMTP to ensure high inbox placement rates and avoid spam folder filtering for booking confirmation emails, payment success emails, and booking cancellation emails. The system focuses on email content optimization, proper authentication headers, HTML structure improvements, and compliance with Gmail's email sending best practices.

This feature addresses deliverability challenges within the constraints of Gmail SMTP (no custom domain), maintaining synchronous email sending for critical booking notifications while implementing industry-standard email optimization techniques.

## Glossary

- **Email_System**: The Laravel-based email notification system that sends transactional emails via Gmail SMTP
- **Booking_Email**: Transactional email sent after booking creation containing booking details and payment instructions
- **Payment_Email**: Transactional email sent after successful payment containing receipt and e-ticket
- **Cancellation_Email**: Transactional email sent when booking is cancelled containing cancellation details
- **Gmail_SMTP**: Gmail's SMTP server (smtp.gmail.com) used as the email delivery provider
- **SPF**: Sender Policy Framework authentication record that validates email sender identity
- **DKIM**: DomainKeys Identified Mail authentication signature that verifies email integrity
- **DMARC**: Domain-based Message Authentication, Reporting & Conformance policy that enforces email authentication
- **Spam_Score**: Numerical rating used by email providers to determine if an email is spam
- **Inbox_Placement**: The rate at which emails reach the primary inbox folder instead of spam or promotions
- **Plain_Text_Version**: Alternative text-only email content for email clients that don't support HTML
- **Email_Header**: Metadata attached to email messages containing authentication and routing information
- **Unsubscribe_Link**: Required link in transactional emails allowing recipients to opt out of future communications
- **Email_Template**: Blade view file containing HTML structure and styling for email messages

## Requirements

### Requirement 1: Email Authentication Configuration

**User Story:** As the system administrator, I want proper email authentication configured for Gmail SMTP, so that emails pass SPF and DKIM validation checks.

#### Acceptance Criteria

1. THE Email_System SHALL verify Gmail SMTP sends emails with valid SPF records for the Gmail domain
2. THE Email_System SHALL verify Gmail SMTP sends emails with valid DKIM signatures for the Gmail domain
3. THE Email_System SHALL configure mail.from settings to use the Gmail sender address
4. THE Email_System SHALL configure mail.from_name to clearly identify The Waterfall Tourism brand
5. THE Email_System SHALL log authentication validation results during email send operations

### Requirement 2: Email Content Optimization

**User Story:** As the system administrator, I want optimized email content that reduces spam scoring, so that booking emails reach customer inboxes.

#### Acceptance Criteria

1. THE Email_System SHALL ensure all Email_Template files contain balanced text-to-image ratios (minimum 60% text content)
2. THE Email_System SHALL ensure all Email_Template files contain no spam trigger words in subject lines (free, winner, urgent, act now, guarantee)
3. THE Email_System SHALL ensure all Email_Template files contain no excessive capitalization (no more than 3 consecutive capitalized words)
4. THE Email_System SHALL ensure all Email_Template files contain no excessive exclamation marks (maximum 1 per email)
5. THE Email_System SHALL ensure all Email_Template files contain specific booking details (booking code, package name, visit date) in the first 100 words
6. THE Email_System SHALL ensure subject lines are descriptive and contain booking codes (e.g., "Konfirmasi Booking - BK20260125001")
7. THE Email_System SHALL ensure email body content uses natural, conversational language without marketing hype

### Requirement 3: HTML Email Structure Compliance

**User Story:** As a customer, I want properly structured HTML emails that render correctly across all email clients, so that I can read booking information without formatting issues.

#### Acceptance Criteria

1. THE Email_System SHALL ensure all Email_Template files use table-based layouts for email client compatibility
2. THE Email_System SHALL ensure all Email_Template files contain valid HTML5 structure (DOCTYPE, html, head, body tags)
3. THE Email_System SHALL ensure all Email_Template files use inline CSS styles instead of external stylesheets
4. THE Email_System SHALL ensure all Email_Template files specify explicit widths and heights for images
5. THE Email_System SHALL ensure all Email_Template files contain alt text attributes for all images
6. THE Email_System SHALL ensure all Email_Template files use web-safe fonts (Arial, Helvetica, sans-serif)
7. THE Email_System SHALL ensure all Email_Template files contain viewport meta tags for mobile responsiveness
8. THE Email_System SHALL ensure all Email_Template files do not use JavaScript or external scripts
9. THE Email_System SHALL ensure all Email_Template files contain UTF-8 charset declarations

### Requirement 4: Plain Text Email Version

**User Story:** As a customer using a text-only email client, I want plain text versions of booking emails, so that I can read booking information without HTML rendering.

#### Acceptance Criteria

1. WHEN THE Email_System sends a Booking_Email, THE Email_System SHALL generate a Plain_Text_Version containing all booking details
2. WHEN THE Email_System sends a Payment_Email, THE Email_System SHALL generate a Plain_Text_Version containing all payment details
3. WHEN THE Email_System sends a Cancellation_Email, THE Email_System SHALL generate a Plain_Text_Version containing all cancellation details
4. THE Email_System SHALL ensure Plain_Text_Version contains properly formatted line breaks and spacing
5. THE Email_System SHALL ensure Plain_Text_Version contains all critical information from HTML version (booking code, dates, amounts)

### Requirement 5: Email Header Optimization

**User Story:** As the system administrator, I want optimized email headers that improve deliverability, so that Gmail treats our emails as legitimate transactional messages.

#### Acceptance Criteria

1. THE Email_System SHALL set "Precedence: bulk" header for all transactional emails
2. THE Email_System SHALL set "X-Priority: 1" header for payment confirmation emails
3. THE Email_System SHALL set "X-Priority: 3" header for booking confirmation emails
4. THE Email_System SHALL set descriptive "X-Mailer" headers identifying Laravel framework
5. THE Email_System SHALL set "Auto-Submitted: auto-generated" header for all transactional emails
6. THE Email_System SHALL NOT include "List-Unsubscribe" headers for critical transactional emails (booking confirmation, payment receipt)
7. THE Email_System SHALL set proper "Message-ID" headers with unique identifiers
8. THE Email_System SHALL set "Content-Type: multipart/alternative" for emails with both HTML and plain text versions

### Requirement 6: Link and URL Configuration

**User Story:** As a customer, I want functional links in booking emails, so that I can access booking confirmation pages and download e-tickets.

#### Acceptance Criteria

1. THE Email_System SHALL ensure all Email_Template files contain absolute URLs with the application domain
2. THE Email_System SHALL ensure all Email_Template files use HTTPS protocol for all links
3. THE Email_System SHALL ensure all Email_Template files contain clickable booking confirmation links
4. THE Email_System SHALL ensure all Email_Template files contain visible text for all hyperlinks (no "click here" anchor text)
5. THE Email_System SHALL ensure all Email_Template files contain properly encoded URL parameters
6. THE Email_System SHALL ensure all Email_Template files do not contain URL shorteners or redirects

### Requirement 7: Email Sending Rate Limiting

**User Story:** As the system administrator, I want rate limiting for email sending, so that Gmail SMTP does not flag our account for sending too many emails too quickly.

#### Acceptance Criteria

1. WHEN THE Email_System sends multiple emails, THE Email_System SHALL enforce a maximum rate of 100 emails per day for Gmail SMTP free tier
2. WHEN THE Email_System reaches 80% of daily sending limit, THE Email_System SHALL log a warning message
3. WHEN THE Email_System exceeds daily sending limit, THE Email_System SHALL queue additional emails for next day delivery
4. THE Email_System SHALL track daily email count in cache storage with 24-hour expiration
5. THE Email_System SHALL reset daily email count at midnight UTC

### Requirement 8: Email Content Personalization

**User Story:** As a customer, I want personalized booking emails addressed to me, so that I know the email is legitimate and not spam.

#### Acceptance Criteria

1. THE Email_System SHALL ensure all Email_Template files address recipients by full name (nama_lengkap)
2. THE Email_System SHALL ensure all Email_Template files contain customer-specific booking codes
3. THE Email_System SHALL ensure all Email_Template files contain customer-specific visit dates
4. THE Email_System SHALL ensure all Email_Template files contain customer-specific package names
5. THE Email_System SHALL ensure all Email_Template files contain customer-specific payment amounts
6. THE Email_System SHALL ensure greeting text uses proper Indonesian language formality (e.g., "Halo [Nama]" not "Hai [Nama]")

### Requirement 9: Email Error Handling and Logging

**User Story:** As the system administrator, I want comprehensive email sending error logging, so that I can diagnose deliverability issues quickly.

#### Acceptance Criteria

1. WHEN THE Email_System fails to send an email, THE Email_System SHALL log the SMTP error code and message
2. WHEN THE Email_System fails to send an email, THE Email_System SHALL log the recipient email address
3. WHEN THE Email_System fails to send an email, THE Email_System SHALL log the booking code associated with the email
4. WHEN THE Email_System successfully sends an email, THE Email_System SHALL log confirmation with timestamp
5. WHEN THE Email_System encounters SMTP authentication failure, THE Email_System SHALL log credentials status (without exposing password)
6. THE Email_System SHALL log all email sending attempts with status (sent, failed, queued)
7. THE Email_System SHALL retain email logs for minimum 30 days for debugging purposes

### Requirement 10: Email Testing and Validation

**User Story:** As the system administrator, I want email testing capabilities, so that I can validate deliverability before sending to customers.

#### Acceptance Criteria

1. THE Email_System SHALL provide an artisan command to send test emails to specified addresses
2. THE Email_System SHALL validate email template HTML structure before sending emails
3. THE Email_System SHALL validate that all required variables (booking code, dates, amounts) are present before rendering templates
4. WHEN template variables are missing, THE Email_System SHALL log a validation error and not send the email
5. THE Email_System SHALL provide a test mode that sends emails to a configured test address instead of customer addresses

### Requirement 11: Booking Confirmation Email Lifecycle

**User Story:** As a customer, I want to receive a booking confirmation email immediately after booking, so that I have proof of my booking request.

#### Acceptance Criteria

1. WHEN a booking is created, THE Email_System SHALL send a Booking_Email to the customer email address within 30 seconds
2. THE Booking_Email SHALL contain the booking code, package name, visit date, number of people, and total amount
3. THE Booking_Email SHALL contain payment instructions with Midtrans payment link
4. THE Booking_Email SHALL contain contact information for customer support
5. THE Booking_Email SHALL have subject line format "Konfirmasi Booking - [kode_booking]"

### Requirement 12: Payment Success Email Lifecycle

**User Story:** As a customer, I want to receive a payment confirmation email immediately after successful payment, so that I have a receipt and e-ticket for my visit.

#### Acceptance Criteria

1. WHEN a payment is confirmed, THE Email_System SHALL send a Payment_Email to the customer email address within 30 seconds
2. THE Payment_Email SHALL contain the booking code, payment method, transaction ID, and payment date
3. THE Payment_Email SHALL contain all booking details (package name, visit date, number of people)
4. THE Payment_Email SHALL contain a link to view booking confirmation page
5. THE Payment_Email SHALL have subject line format "Pembayaran Berhasil - E-Ticket [kode_booking]"
6. THE Payment_Email SHALL attach an e-ticket PDF file generated by ETicketService

### Requirement 13: Booking Cancellation Email Lifecycle

**User Story:** As a customer, I want to receive a cancellation notification email when my booking is cancelled, so that I know the cancellation was processed.

#### Acceptance Criteria

1. WHEN a booking is cancelled, THE Email_System SHALL send a Cancellation_Email to the customer email address within 30 seconds
2. THE Cancellation_Email SHALL contain the booking code, package name, and visit date of the cancelled booking
3. THE Cancellation_Email SHALL contain the cancellation reason (payment timeout, manual cancellation)
4. THE Cancellation_Email SHALL contain refund information if applicable
5. THE Cancellation_Email SHALL contain contact information for rebooking assistance
6. THE Cancellation_Email SHALL have subject line format "Pembatalan Booking - [kode_booking]"

### Requirement 14: Email Content Encoding and Character Support

**User Story:** As a customer receiving emails in Indonesian language, I want proper character encoding, so that special characters display correctly.

#### Acceptance Criteria

1. THE Email_System SHALL set email charset to UTF-8 for all outgoing emails
2. THE Email_System SHALL properly encode Indonesian currency symbols (Rp) in email content
3. THE Email_System SHALL properly encode Indonesian date formats with special characters
4. THE Email_System SHALL properly encode customer names containing special characters (accents, diacritics)
5. THE Email_System SHALL use HTML entity encoding for special characters in email templates

### Requirement 15: Email Image Optimization

**User Story:** As a customer with slow internet connection, I want optimized email images, so that booking emails load quickly without affecting deliverability.

#### Acceptance Criteria

1. WHERE Email_Template files contain images, THE Email_System SHALL ensure image file sizes do not exceed 100KB per image
2. WHERE Email_Template files contain images, THE Email_System SHALL ensure total email size does not exceed 500KB
3. WHERE Email_Template files contain images, THE Email_System SHALL ensure images are hosted on reliable CDN or local server
4. WHERE Email_Template files contain images, THE Email_System SHALL ensure images use web-optimized formats (JPEG, PNG, WebP)
5. WHERE Email_Template files contain logos, THE Email_System SHALL use vector formats (SVG) with PNG fallback

### Requirement 16: Synchronous Email Sending

**User Story:** As a customer, I want immediate confirmation that my booking email was sent, so that I know the booking was successful.

#### Acceptance Criteria

1. WHEN a booking is created, THE Email_System SHALL send the Booking_Email synchronously before returning booking confirmation response
2. WHEN a payment is confirmed, THE Email_System SHALL send the Payment_Email synchronously before updating payment status
3. IF email sending fails, THE Email_System SHALL log the error but continue with booking transaction
4. THE Email_System SHALL set SMTP timeout to 10 seconds to prevent long delays
5. THE Email_System SHALL NOT use queue system for critical transactional emails (booking confirmation, payment receipt)

### Requirement 17: Email Provider Connection Reliability

**User Story:** As the system administrator, I want reliable Gmail SMTP connections, so that email sending succeeds consistently.

#### Acceptance Criteria

1. THE Email_System SHALL configure SMTP connection timeout to 10 seconds
2. THE Email_System SHALL configure SMTP read timeout to 15 seconds
3. WHEN Gmail SMTP connection fails, THE Email_System SHALL retry up to 2 times with 5-second delay between attempts
4. WHEN all retry attempts fail, THE Email_System SHALL log critical error with connection details
5. THE Email_System SHALL use TLS encryption for all Gmail SMTP connections
6. THE Email_System SHALL verify Gmail SMTP credentials before sending production emails

### Requirement 18: Email Spam Compliance

**User Story:** As the system administrator, I want CAN-SPAM compliant emails, so that our emails meet legal requirements and improve deliverability.

#### Acceptance Criteria

1. THE Email_System SHALL ensure all Email_Template files contain accurate sender information (company name and address)
2. THE Email_System SHALL ensure all Email_Template files clearly identify as automated transactional messages
3. THE Email_System SHALL ensure all Email_Template files do not contain deceptive subject lines
4. THE Email_System SHALL ensure subject lines accurately reflect email content
5. THE Email_System SHALL ensure all Email_Template files contain physical mailing address in footer
6. THE Email_System SHALL ensure marketing emails (if any) contain unsubscribe links, but transactional emails do not require them
