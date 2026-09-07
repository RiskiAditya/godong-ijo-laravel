# Design Document: Booking Email Deliverability Fix

## Overview

The Booking Email Deliverability Fix optimizes transactional email delivery through Gmail SMTP by implementing email best practices that improve inbox placement rates. The solution focuses on template optimization, proper email headers, authentication validation, rate limiting, and comprehensive error handling while maintaining synchronous email delivery for critical booking notifications.

This design addresses deliverability challenges within Gmail SMTP constraints by applying industry-standard email optimization techniques: HTML structure compliance, content scoring, plain text alternatives, and proper email headers that signal legitimate transactional messaging to spam filters.

## Programming Language

**PHP 8.x with Laravel 10.x Framework**

The existing codebase uses Laravel's email system with Blade templating. All email functionality will be implemented using:
- Laravel Mailable classes for email composition
- Blade templates for HTML/plain text rendering
- Laravel Mail facade for sending operations
- Laravel Cache for rate limiting
- Laravel Log for error tracking

## Architecture

### System Components

```
┌─────────────────────────────────────────────────────────────────┐
│                     Email Sending Flow                           │
└─────────────────────────────────────────────────────────────────┘

BookingController           PaymentWebhook           CancellationService
       │                           │                           │
       ├───────────────────────────┴───────────────────────────┤
       │                                                        │
       ▼                                                        ▼
┌──────────────────────────────────────────────────────────────────┐
│              EmailService (New Service Layer)                     │
│  ┌────────────────────────────────────────────────────────────┐  │
│  │  - Rate Limiting Check (Cache-based Counter)               │  │
│  │  - Template Variable Validation                            │  │
│  │  - Email Queue Decision (sync vs queued)                   │  │
│  └────────────────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────────────────────────────────┐
│                 Mailable Classes                                  │
│  ┌──────────────┐  ┌─────────────────┐  ┌───────────────────┐   │
│  │  Booking     │  │  Payment        │  │  Cancellation     │   │
│  │  Confirmation│  │  Success        │  │  Notification     │   │
│  │  Mail        │  │  Mail           │  │  Mail             │   │
│  └──────────────┘  └─────────────────┘  └───────────────────┘   │
│         │                   │                      │              │
│         └───────────────────┴──────────────────────┘              │
│                             │                                     │
│                    ┌────────▼────────┐                           │
│                    │ Email Header    │                           │
│                    │ Configuration   │                           │
│                    │ (withHeaders)   │                           │
│                    └────────┬────────┘                           │
└─────────────────────────────┼──────────────────────────────────┘
                              │
                              ▼
┌──────────────────────────────────────────────────────────────────┐
│                  Blade Email Templates                            │
│  ┌──────────────────────────────────────────────────────────┐    │
│  │  HTML Version (emails/*.blade.php)                       │    │
│  │  - Table-based layout                                    │    │
│  │  - Inline CSS                                            │    │
│  │  - Web-safe fonts                                        │    │
│  │  - Absolute HTTPS URLs                                   │    │
│  │  - Optimized images with alt text                       │    │
│  └──────────────────────────────────────────────────────────┘    │
│  ┌──────────────────────────────────────────────────────────┐    │
│  │  Plain Text Version (emails/*.blade.php - text section)  │    │
│  │  - Formatted line breaks                                 │    │
│  │  - All critical information                              │    │
│  └──────────────────────────────────────────────────────────┘    │
└──────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌──────────────────────────────────────────────────────────────────┐
│               Laravel Mail System                                 │
│  ┌────────────────────────────────────────────────────────────┐  │
│  │  SwiftMailer / Symfony Mailer                              │  │
│  │  - MIME assembly (multipart/alternative)                   │  │
│  │  - Content encoding (UTF-8)                                │  │
│  │  - Header attachment                                       │  │
│  └────────────────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌──────────────────────────────────────────────────────────────────┐
│                    Gmail SMTP Server                              │
│  - smtp.gmail.com:587 (TLS)                                      │
│  - Automatic SPF/DKIM signing                                    │
│  - Connection timeout: 10s                                       │
│  - Read timeout: 15s                                             │
│  - Retry: 2 attempts with 5s delay                              │
└──────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌──────────────────────────────────────────────────────────────────┐
│                  Email Logging System                             │
│  - Success logs (timestamp, recipient, booking code)             │
│  - Failure logs (SMTP error, booking code, recipient)            │
│  - Rate limit warnings (at 80% threshold)                        │
│  - Authentication validation logs                                │
└──────────────────────────────────────────────────────────────────┘
```

### Component Responsibilities

#### 1. EmailService (New Service Class)

**Location:** `app/Services/EmailService.php`

**Responsibilities:**
- Rate limiting enforcement using Laravel Cache
- Email send attempt tracking (daily counter)
- Template variable validation before rendering
- SMTP connection retry logic
- Error logging and monitoring
- Queue decision logic (sync vs queued)

**Public Methods:**
```php
public function sendBookingConfirmation(Pemesanan $pemesanan): bool
public function sendPaymentSuccess(Pemesanan $pemesanan): bool
public function sendCancellationNotification(Pemesanan $pemesanan, string $reason): bool
public function checkRateLimit(): bool
public function getRemainingDailyQuota(): int
protected function logEmailSent(string $type, string $recipient, string $bookingCode): void
protected function logEmailFailed(string $type, string $recipient, string $bookingCode, \Exception $e): void
```


#### 2. Mailable Classes

**Existing:** `app/Mail/PaymentSuccessMail.php`

**New Classes:**
- `app/Mail/BookingConfirmationMail.php`
- `app/Mail/CancellationNotificationMail.php`

**Enhanced Responsibilities:**
- Email header configuration via `withHeaders()` method
- Plain text view rendering via `text()` method
- Subject line formatting with booking codes
- Variable passing to Blade templates
- Priority configuration (X-Priority header)

**Example Structure:**
```php
class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;
    
    public Pemesanan $pemesanan;
    
    public function build()
    {
        return $this->subject('Konfirmasi Booking - ' . $this->pemesanan->kode_booking)
                    ->view('emails.booking-confirmation')
                    ->text('emails.booking-confirmation-text')
                    ->withHeaders([
                        'X-Priority' => '3',
                        'X-Mailer' => 'Laravel/' . app()->version(),
                        'Precedence' => 'bulk',
                        'Auto-Submitted' => 'auto-generated',
                        'Message-ID' => $this->generateMessageId(),
                    ]);
    }
    
    protected function generateMessageId(): string
    {
        return sprintf(
            '<%s.%s@%s>',
            uniqid(),
            $this->pemesanan->kode_booking,
            config('app.domain', 'thewaterfall.com')
        );
    }
}
```

#### 3. Email Templates

**Location:** `resources/views/emails/`

**Files:**
- `booking-confirmation.blade.php` (HTML version - NEW)
- `booking-confirmation-text.blade.php` (Plain text version - NEW)
- `payment-success.blade.php` (HTML version - EXISTS, needs optimization)
- `payment-success-text.blade.php` (Plain text version - NEW)
- `cancellation-notification.blade.php` (HTML version - NEW)
- `cancellation-notification-text.blade.php` (Plain text version - NEW)

**HTML Template Structure:**
```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $emailTitle }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <!-- Email content using table-based layout -->
        <!-- All styles must be inline -->
        <!-- All URLs must be absolute HTTPS -->
        <!-- All images must have alt text, width, height -->
    </table>
</body>
</html>
```

**Plain Text Template Structure:**
```
=================================================
{{ $emailTitle }}
=================================================

Halo {{ $pemesanan->nama_lengkap }},

[Email content in plain text format]

Kode Booking: {{ $pemesanan->kode_booking }}
Paket Wisata: {{ $paket->nama_paket }}
Tanggal: {{ $formattedDate }}

[Additional details]

Terima kasih,
The Waterfall Tourism

=================================================
Alamat: Jl. Raya Waterfall No. 123, Bandung
Email: info@thewaterfall.com
Telepon: (022) 1234-5678
=================================================
```


#### 4. Rate Limiting System

**Implementation:** Laravel Cache with daily counter

**Cache Key Structure:**
```php
email_rate_limit:daily:{date}  // Key for daily email count
```

**Logic Flow:**
```php
public function checkRateLimit(): bool
{
    $cacheKey = 'email_rate_limit:daily:' . now()->format('Y-m-d');
    $currentCount = Cache::get($cacheKey, 0);
    $limit = config('mail.daily_limit', 100);
    
    if ($currentCount >= $limit) {
        Log::warning('Email rate limit exceeded', [
            'current_count' => $currentCount,
            'limit' => $limit,
            'date' => now()->format('Y-m-d'),
        ]);
        return false;
    }
    
    if ($currentCount >= ($limit * 0.8)) {
        Log::warning('Email rate limit approaching', [
            'current_count' => $currentCount,
            'limit' => $limit,
            'percentage' => ($currentCount / $limit) * 100,
        ]);
    }
    
    Cache::put($cacheKey, $currentCount + 1, now()->endOfDay());
    return true;
}
```

#### 5. Email Header Configuration

**Headers Applied to All Emails:**
```php
[
    'Precedence' => 'bulk',
    'Auto-Submitted' => 'auto-generated',
    'X-Mailer' => 'Laravel/' . app()->version(),
    'Message-ID' => '<unique-id.booking-code@domain>',
    'Content-Type' => 'multipart/alternative',
]
```

**Priority-Specific Headers:**
```php
// Payment emails (high priority)
['X-Priority' => '1']

// Booking confirmation (normal priority)
['X-Priority' => '3']

// Cancellation notifications (normal priority)
['X-Priority' => '3']
```

**Headers NOT Included:**
- `List-Unsubscribe` (omitted for critical transactional emails)


## Data Models

### Email Sending Context

```php
class EmailContext
{
    public string $type;              // 'booking_confirmation', 'payment_success', 'cancellation'
    public string $recipient;         // Customer email address
    public string $bookingCode;       // Booking reference code
    public array $templateData;       // Data passed to Blade template
    public int $priority;             // 1 (high) or 3 (normal)
    public bool $requiresETicket;     // Whether to attach PDF e-ticket
    public ?string $cancellationReason; // For cancellation emails only
}
```

### Rate Limit Status

```php
class RateLimitStatus
{
    public int $currentCount;         // Emails sent today
    public int $dailyLimit;           // Maximum emails per day (100)
    public int $remainingQuota;       // Emails remaining for today
    public float $usagePercentage;    // Percentage of quota used
    public bool $canSend;             // Whether sending is allowed
    public bool $warningThreshold;    // Whether at 80% threshold
}
```

### Email Log Entry

```php
class EmailLogEntry
{
    public string $timestamp;         // ISO 8601 format
    public string $type;              // Email type
    public string $status;            // 'sent', 'failed', 'queued'
    public string $recipient;         // Customer email
    public string $bookingCode;       // Associated booking
    public ?string $errorCode;        // SMTP error code (if failed)
    public ?string $errorMessage;     // Error description (if failed)
    public int $retryCount;           // Number of retry attempts
}
```

## Interfaces

### IEmailService

```php
interface IEmailService
{
    /**
     * Send booking confirmation email
     * 
     * @param Pemesanan $pemesanan Booking record
     * @return bool True if sent successfully
     * @throws EmailRateLimitException If daily limit exceeded
     * @throws EmailValidationException If required data missing
     */
    public function sendBookingConfirmation(Pemesanan $pemesanan): bool;
    
    /**
     * Send payment success email with e-ticket
     * 
     * @param Pemesanan $pemesanan Booking record with confirmed payment
     * @return bool True if sent successfully
     */
    public function sendPaymentSuccess(Pemesanan $pemesanan): bool;
    
    /**
     * Send booking cancellation notification
     * 
     * @param Pemesanan $pemesanan Cancelled booking record
     * @param string $reason Cancellation reason
     * @return bool True if sent successfully
     */
    public function sendCancellationNotification(Pemesanan $pemesanan, string $reason): bool;
    
    /**
     * Check if email can be sent within rate limit
     * 
     * @return bool True if under rate limit
     */
    public function checkRateLimit(): bool;
    
    /**
     * Get current rate limit status
     * 
     * @return RateLimitStatus Current quota and usage
     */
    public function getRateLimitStatus(): RateLimitStatus;
}
```

### IEmailTemplateValidator

```php
interface IEmailTemplateValidator
{
    /**
     * Validate HTML email template structure
     * 
     * @param string $templatePath Path to Blade template
     * @return ValidationResult Validation results with issues
     */
    public function validateHtmlStructure(string $templatePath): ValidationResult;
    
    /**
     * Calculate text-to-image ratio in template
     * 
     * @param string $renderedHtml Rendered email HTML
     * @return float Ratio of text content to image content (0.0-1.0)
     */
    public function calculateTextToImageRatio(string $renderedHtml): float;
    
    /**
     * Check for spam trigger words in content
     * 
     * @param string $content Email subject or body
     * @return array List of detected spam trigger words
     */
    public function detectSpamTriggers(string $content): array;
    
    /**
     * Validate all links use absolute HTTPS URLs
     * 
     * @param string $renderedHtml Rendered email HTML
     * @return array List of invalid URLs (relative or non-HTTPS)
     */
    public function validateUrls(string $renderedHtml): array;
}
```


## Error Handling

### Error Categories

#### 1. SMTP Connection Errors

**Error Codes:**
- `421`: Service not available (temporary)
- `450`: Mailbox unavailable (temporary)
- `550`: Mailbox not found (permanent)
- `554`: Transaction failed (permanent)

**Handling Strategy:**
```php
try {
    Mail::to($recipient)->send($mailable);
} catch (Swift_TransportException $e) {
    $errorCode = $this->extractSmtpErrorCode($e->getMessage());
    
    if ($this->isTemporaryError($errorCode)) {
        // Retry up to 2 times with 5-second delay
        $this->retryWithDelay($mailable, $recipient, $retries = 2, $delay = 5);
    } else {
        // Permanent error - log and skip
        Log::error('Permanent SMTP error', [
            'error_code' => $errorCode,
            'recipient' => $recipient,
            'booking_code' => $bookingCode,
            'message' => $e->getMessage(),
        ]);
    }
}
```

#### 2. Rate Limit Exceeded

**Exception:** `EmailRateLimitException`

**Handling:**
```php
if (!$this->checkRateLimit()) {
    // Log rate limit breach
    Log::critical('Email rate limit exceeded', [
        'current_count' => $this->getCurrentCount(),
        'limit' => config('mail.daily_limit'),
    ]);
    
    // Queue email for next day delivery
    dispatch(new SendEmailJob($mailable, $recipient))->delay(now()->addDay()->startOfDay());
    
    throw new EmailRateLimitException('Daily email limit exceeded');
}
```

#### 3. Template Validation Errors

**Exception:** `EmailValidationException`

**Validation Checks:**
```php
public function validateTemplateData(array $data, string $emailType): void
{
    $requiredFields = [
        'booking_confirmation' => ['pemesanan', 'paket', 'jadwal'],
        'payment_success' => ['pemesanan', 'pembayaran', 'paket'],
        'cancellation' => ['pemesanan', 'reason'],
    ];
    
    $required = $requiredFields[$emailType] ?? [];
    $missing = array_diff($required, array_keys($data));
    
    if (!empty($missing)) {
        Log::error('Email template validation failed', [
            'email_type' => $emailType,
            'missing_fields' => $missing,
        ]);
        
        throw new EmailValidationException(
            'Missing required template data: ' . implode(', ', $missing)
        );
    }
}
```

#### 4. Authentication Failures

**Error:** SMTP authentication rejected (535)

**Handling:**
```php
catch (Swift_TransportException $e) {
    if (str_contains($e->getMessage(), '535')) {
        Log::critical('SMTP authentication failure', [
            'host' => config('mail.mailers.smtp.host'),
            'username' => config('mail.mailers.smtp.username'),
            'port' => config('mail.mailers.smtp.port'),
            'encryption' => config('mail.mailers.smtp.encryption'),
        ]);
        
        // Do not retry authentication failures
        throw new EmailAuthenticationException('SMTP authentication failed');
    }
}
```

### Error Logging Format

```php
// Success log
Log::info('Email sent successfully', [
    'type' => 'payment_success',
    'recipient' => 'customer@example.com',
    'booking_code' => 'BK20260125001',
    'timestamp' => now()->toIso8601String(),
    'message_id' => '<unique-id@domain>',
]);

// Failure log
Log::error('Email sending failed', [
    'type' => 'booking_confirmation',
    'recipient' => 'customer@example.com',
    'booking_code' => 'BK20260125001',
    'error_code' => '550',
    'error_message' => 'Mailbox not found',
    'retry_count' => 2,
    'timestamp' => now()->toIso8601String(),
]);

// Rate limit warning
Log::warning('Email rate limit approaching', [
    'current_count' => 82,
    'daily_limit' => 100,
    'percentage' => 82.0,
    'remaining' => 18,
]);
```


## Configuration

### Mail Configuration Updates

**File:** `config/mail.php`

**New Configuration Keys:**
```php
return [
    // Existing configuration...
    
    'daily_limit' => env('MAIL_DAILY_LIMIT', 100),
    'rate_limit_warning_threshold' => env('MAIL_RATE_WARNING_THRESHOLD', 0.8),
    
    'smtp_timeout' => env('MAIL_SMTP_TIMEOUT', 10),
    'smtp_read_timeout' => env('MAIL_SMTP_READ_TIMEOUT', 15),
    'smtp_retry_attempts' => env('MAIL_SMTP_RETRY_ATTEMPTS', 2),
    'smtp_retry_delay' => env('MAIL_SMTP_RETRY_DELAY', 5),
    
    'validation' => [
        'enabled' => env('MAIL_VALIDATION_ENABLED', true),
        'check_spam_triggers' => env('MAIL_CHECK_SPAM_TRIGGERS', true),
        'minimum_text_ratio' => env('MAIL_MIN_TEXT_RATIO', 0.6),
    ],
    
    'spam_trigger_words' => [
        'free', 'winner', 'urgent', 'act now', 'guarantee', 'limited time',
        'exclusive', 'congratulations', 'claim now', 'click here',
    ],
    
    // From address configuration
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'thewaterfall.tourism@gmail.com'),
        'name' => env('MAIL_FROM_NAME', 'The Waterfall Tourism'),
    ],
];
```

### Environment Variables

**File:** `.env`

```env
# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=thewaterfall.tourism@gmail.com
MAIL_PASSWORD=your-app-specific-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=thewaterfall.tourism@gmail.com
MAIL_FROM_NAME="The Waterfall Tourism"

# Rate Limiting
MAIL_DAILY_LIMIT=100
MAIL_RATE_WARNING_THRESHOLD=0.8

# SMTP Connection
MAIL_SMTP_TIMEOUT=10
MAIL_SMTP_READ_TIMEOUT=15
MAIL_SMTP_RETRY_ATTEMPTS=2
MAIL_SMTP_RETRY_DELAY=5

# Validation
MAIL_VALIDATION_ENABLED=true
MAIL_CHECK_SPAM_TRIGGERS=true
MAIL_MIN_TEXT_RATIO=0.6

# Application Domain (for absolute URLs in emails)
APP_URL=https://thewaterfall.com
APP_DOMAIN=thewaterfall.com
```

### SMTP Configuration

**Gmail SMTP Settings:**
- **Host:** smtp.gmail.com
- **Port:** 587
- **Encryption:** TLS
- **Authentication:** Required
- **Username:** Full Gmail address
- **Password:** App-specific password (not regular Gmail password)

**Connection Timeouts:**
- **Connection Timeout:** 10 seconds
- **Read Timeout:** 15 seconds
- **Retry Attempts:** 2
- **Retry Delay:** 5 seconds between attempts


## Email Content Optimization Guidelines

### Subject Line Best Practices

**DO:**
- Include booking code for easy identification
- Use descriptive, specific language
- Keep under 50 characters
- Use proper Indonesian capitalization

**DON'T:**
- Use all caps (SPAM TRIGGER)
- Use multiple exclamation marks
- Use spam trigger words (free, winner, urgent, act now, guarantee)
- Use generic subjects like "Notification" or "Update"

**Examples:**
```
✓ Konfirmasi Booking - BK20260125001
✓ Pembayaran Berhasil - E-Ticket BK20260125001
✓ Pembatalan Booking - BK20260125001
✗ KONFIRMASI BOOKING ANDA!!!
✗ Urgent: Act Now - Winner Selected
✗ Free Booking Confirmation
```

### HTML Structure Requirements

**Required Elements:**
```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Title</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <!-- Content -->
    </table>
</body>
</html>
```

**CSS Constraints:**
- All styles MUST be inline (`style="..."`)
- NO external stylesheets (`<link>` tags)
- NO `<style>` blocks in `<head>`
- Use web-safe fonts: Arial, Helvetica, sans-serif, Georgia, Times New Roman

**Image Requirements:**
- Maximum size: 100KB per image
- Total email size: Maximum 500KB
- Required attributes: `width`, `height`, `alt`
- Use absolute HTTPS URLs
- Formats: JPEG, PNG (WebP with PNG fallback)

**Link Requirements:**
- All URLs must be absolute: `https://thewaterfall.com/booking/...`
- NO relative URLs: `~/booking/...` or `/booking/...`
- NO URL shorteners: `bit.ly`, `tinyurl.com`, etc.
- Descriptive anchor text (no "click here")

### Text-to-Image Ratio

**Target:** Minimum 60% text content

**Calculation:**
```php
$textLength = strlen(strip_tags($html));
$imageCount = substr_count($html, '<img');
$estimatedImageBytes = $imageCount * 50000; // Assume 50KB average per image
$htmlBytes = strlen($html);

$textRatio = $textLength / ($textLength + $estimatedImageBytes);
// Should be >= 0.6
```

### Content Structure

**First 100 Words Must Include:**
- Customer name (personalized greeting)
- Booking code
- Package name OR visit date
- Clear purpose of email

**Example:**
```
Halo Budi Santoso,

Terima kasih atas booking Anda! Booking dengan kode BK20260125001 
telah berhasil dibuat untuk paket Edukasi Air Terjun pada tanggal 
30 Januari 2026. Berikut adalah detail lengkap pemesanan Anda...
```


## Email Templates Specification

### 1. Booking Confirmation Email

**Template:** `resources/views/emails/booking-confirmation.blade.php`

**Variables Required:**
- `$pemesanan` (Pemesanan model)
- `$paket` (PaketWisata model)
- `$jadwal` (Jadwal model)
- `$paymentLink` (string - Midtrans payment URL)

**Content Sections:**
1. Header with booking icon and title
2. Personalized greeting with customer name
3. Booking details table (code, package, date, people, amount)
4. Payment instructions with Midtrans link
5. Important information bullets
6. Contact information footer

**Subject:** `Konfirmasi Booking - {{ $pemesanan->kode_booking }}`

**Plain Text Version:** `resources/views/emails/booking-confirmation-text.blade.php`

### 2. Payment Success Email

**Template:** `resources/views/emails/payment-success.blade.php` (EXISTS - needs optimization)

**Variables Required:**
- `$pemesanan` (Pemesanan model)
- `$pembayaran` (Pembayaran model)
- `$paket` (PaketWisata model)

**Content Sections:**
1. Success checkmark header
2. Personalized greeting
3. Payment confirmation message
4. Booking details table (code, package, date, payment method, transaction ID)
5. Total payment amount highlighted
6. Link to booking confirmation page
7. E-ticket information
8. Contact information footer

**Subject:** `Pembayaran Berhasil - E-Ticket {{ $pemesanan->kode_booking }}`

**Plain Text Version:** `resources/views/emails/payment-success-text.blade.php`

### 3. Cancellation Notification Email

**Template:** `resources/views/emails/cancellation-notification.blade.php`

**Variables Required:**
- `$pemesanan` (Pemesanan model)
- `$paket` (PaketWisata model)
- `$reason` (string - cancellation reason)
- `$refundInfo` (string - refund details if applicable)

**Content Sections:**
1. Warning/alert header
2. Personalized greeting
3. Cancellation notification message
4. Cancelled booking details (code, package, date)
5. Cancellation reason
6. Refund information (if applicable)
7. Rebooking assistance contact information
8. Footer

**Subject:** `Pembatalan Booking - {{ $pemesanan->kode_booking }}`

**Plain Text Version:** `resources/views/emails/cancellation-notification-text.blade.php`

### Template Color Scheme

**Primary Colors:**
- Header background: `linear-gradient(135deg, #667eea 0%, #764ba2 100%)`
- Success color: `#10b981`
- Warning color: `#f59e0b`
- Error color: `#ef4444`
- Text primary: `#111827`
- Text secondary: `#6b7280`
- Background: `#f4f7fa`
- Card background: `#ffffff`

**Font Specifications:**
- Primary font: `Arial, Helvetica, sans-serif`
- Heading size: `24px` (H1), `18px` (H2)
- Body text: `14px`
- Small text: `13px`
- Footer text: `12px`


## Testing Strategy

### Email Template Validation Tests

**Test Type:** Unit Tests + Static Analysis

**Test Cases:**
1. HTML structure validation (DOCTYPE, meta tags, table layout)
2. Inline CSS compliance (no external styles)
3. Image attribute validation (alt, width, height)
4. URL validation (absolute HTTPS only)
5. Text-to-image ratio calculation
6. Spam trigger word detection
7. Subject line format validation
8. Character encoding (UTF-8)
9. Web-safe font usage
10. Template variable completeness

**Example Test:**
```php
public function test_booking_confirmation_template_has_valid_html_structure()
{
    $html = $this->renderTemplate('emails.booking-confirmation', $this->mockData());
    
    $this->assertStringContainsString('<!DOCTYPE html>', $html);
    $this->assertStringContainsString('<meta charset="UTF-8">', $html);
    $this->assertStringContainsString('<meta name="viewport"', $html);
    $this->assertMatchesRegularExpression('/<table[^>]*>/', $html);
}

public function test_email_template_has_minimum_text_to_image_ratio()
{
    $validator = new EmailTemplateValidator();
    $html = $this->renderTemplate('emails.payment-success', $this->mockData());
    
    $ratio = $validator->calculateTextToImageRatio($html);
    
    $this->assertGreaterThanOrEqual(0.6, $ratio, 
        'Email template must have at least 60% text content');
}
```

### Email Sending Integration Tests

**Test Type:** Integration Tests with Mail Fake

**Test Cases:**
1. Booking confirmation email sent on booking creation
2. Payment success email sent on payment confirmation
3. Cancellation email sent on booking cancellation
4. Email headers are correctly set
5. Plain text version is included
6. Rate limiting enforcement
7. SMTP retry logic on connection failure
8. Error logging on send failure
9. Email sent within 30 seconds (timing test)

**Example Test:**
```php
public function test_payment_success_email_includes_correct_headers()
{
    Mail::fake();
    
    $pemesanan = Pemesanan::factory()->create();
    $service = new EmailService();
    $service->sendPaymentSuccess($pemesanan);
    
    Mail::assertSent(PaymentSuccessMail::class, function ($mail) {
        return $mail->hasHeader('X-Priority', '1')
            && $mail->hasHeader('Precedence', 'bulk')
            && $mail->hasHeader('Auto-Submitted', 'auto-generated')
            && $mail->hasHeader('X-Mailer')
            && $mail->hasHeader('Message-ID');
    });
}
```

### Rate Limiting Tests

**Test Type:** Unit Tests

**Test Cases:**
1. Email counter increments correctly
2. Rate limit warning logged at 80% threshold
3. Email sending blocked at 100% limit
4. Emails queued when limit exceeded
5. Daily counter resets at midnight
6. Cache expiration set correctly

**Example Test:**
```php
public function test_rate_limit_prevents_sending_after_daily_limit()
{
    $service = new EmailService();
    
    // Send 100 emails (at limit)
    for ($i = 0; $i < 100; $i++) {
        $this->assertTrue($service->checkRateLimit());
    }
    
    // 101st email should be blocked
    $this->assertFalse($service->checkRateLimit());
    
    Log::assertLogged('warning', function ($log) {
        return str_contains($log['message'], 'rate limit exceeded');
    });
}
```


## Artisan Commands

### Email Testing Command

**Command:** `php artisan email:test`

**Purpose:** Send test emails to verify deliverability and template rendering

**Usage:**
```bash
# Send booking confirmation test
php artisan email:test booking-confirmation recipient@example.com

# Send payment success test
php artisan email:test payment-success recipient@example.com

# Send cancellation test
php artisan email:test cancellation recipient@example.com

# Test all email types
php artisan email:test all recipient@example.com
```

**Implementation:**
```php
class EmailTestCommand extends Command
{
    protected $signature = 'email:test 
                            {type : Email type (booking-confirmation, payment-success, cancellation, all)}
                            {recipient : Recipient email address}
                            {--booking-code= : Use specific booking code}';
    
    protected $description = 'Send test emails for deliverability testing';
    
    public function handle()
    {
        $type = $this->argument('type');
        $recipient = $this->argument('recipient');
        $bookingCode = $this->option('booking-code');
        
        // Create test pemesanan data
        $testData = $this->createTestData($bookingCode);
        
        $service = new EmailService();
        
        try {
            match($type) {
                'booking-confirmation' => $service->sendBookingConfirmation($testData),
                'payment-success' => $service->sendPaymentSuccess($testData),
                'cancellation' => $service->sendCancellationNotification($testData, 'Test cancellation'),
                'all' => $this->sendAllTypes($service, $testData, $recipient),
            };
            
            $this->info("Test email sent successfully to {$recipient}");
        } catch (\Exception $e) {
            $this->error("Failed to send test email: " . $e->getMessage());
        }
    }
}
```

### Email Template Validation Command

**Command:** `php artisan email:validate`

**Purpose:** Validate all email templates for compliance

**Usage:**
```bash
# Validate all email templates
php artisan email:validate

# Validate specific template
php artisan email:validate --template=payment-success

# Output validation report
php artisan email:validate --report
```

**Validation Checks:**
- HTML structure compliance
- Inline CSS usage
- Image attributes (alt, width, height)
- URL format (absolute HTTPS)
- Text-to-image ratio
- Spam trigger word detection
- Subject line format
- Character encoding

**Output:**
```
Validating email templates...

✓ booking-confirmation.blade.php
  - HTML structure: PASS
  - Inline CSS: PASS
  - Image attributes: PASS
  - URL format: PASS
  - Text ratio: 68% (PASS)
  - Spam triggers: PASS
  
✗ payment-success.blade.php
  - HTML structure: PASS
  - Inline CSS: PASS
  - Image attributes: FAIL (1 image missing alt text)
  - URL format: PASS
  - Text ratio: 55% (FAIL - minimum 60%)
  - Spam triggers: PASS
  
Validation Summary:
- Passed: 1/2
- Failed: 1/2
- Issues: 2
```


## Implementation Flow

### Flow 1: Booking Confirmation Email

```
┌─────────────────────────────────────────────────────────────┐
│ 1. BookingController creates new Pemesanan                  │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ 2. EmailService::sendBookingConfirmation() called           │
│    - Validate template data                                 │
│    - Check rate limit                                       │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ 3. BookingConfirmationMail created                          │
│    - Set subject with booking code                          │
│    - Configure headers (X-Priority: 3, Precedence, etc.)    │
│    - Set HTML view and plain text view                      │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ 4. Laravel Mail system sends email                          │
│    - Render Blade templates (HTML + text)                   │
│    - Assemble multipart/alternative MIME                    │
│    - Connect to Gmail SMTP with retry logic                 │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ 5. Log email sending result                                 │
│    - Success: Log with timestamp and message ID             │
│    - Failure: Log with SMTP error code and retry count      │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ 6. Increment rate limit counter in cache                    │
└─────────────────────────────────────────────────────────────┘
```

### Flow 2: Payment Success Email with E-Ticket

```
┌─────────────────────────────────────────────────────────────┐
│ 1. Midtrans webhook confirms payment                        │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ 2. BookingController updates Pembayaran status              │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ 3. ETicketService generates PDF e-ticket                    │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ 4. EmailService::sendPaymentSuccess() called                │
│    - Validate template data                                 │
│    - Check rate limit                                       │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ 5. PaymentSuccessMail created                               │
│    - Set subject with booking code                          │
│    - Configure headers (X-Priority: 1, high priority)       │
│    - Set HTML view and plain text view                      │
│    - Attach e-ticket PDF                                    │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ 6. Send email via Gmail SMTP                                │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ 7. Log success and increment counter                        │
└─────────────────────────────────────────────────────────────┘
```

### Flow 3: Rate Limit Enforcement

```
┌─────────────────────────────────────────────────────────────┐
│ Email send request received                                 │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ Check cache for today's email count                         │
│ Key: email_rate_limit:daily:2026-01-25                      │
└────────────────────┬────────────────────────────────────────┘
                     │
          ┌──────────┴──────────┐
          │                     │
          ▼                     ▼
    Count < 80         Count >= 80 && < 100
          │                     │
          ▼                     ▼
   Send normally      Log warning + send
          │                     │
          └──────────┬──────────┘
                     │
                     ▼
          ┌─────────────────────┐
          │  Count >= 100?      │
          └──────┬──────────────┘
                 │
        ┌────────┴────────┐
        │                 │
        ▼                 ▼
      YES               NO
        │                 │
        ▼                 ▼
   Queue for       Send and increment
   next day          counter in cache
        │                 │
        └────────┬────────┘
                 │
                 ▼
         Return status
```


## Security Considerations

### 1. Email Content Injection Prevention

**Threat:** Malicious user input in booking data could inject harmful content into emails

**Mitigation:**
```php
// Blade templates automatically escape output
{{ $pemesanan->nama_lengkap }}  // Escaped
{!! $pemesanan->nama_lengkap !!}  // NOT escaped - avoid

// Additional validation in EmailService
public function sanitizeEmailData(array $data): array
{
    return [
        'nama_lengkap' => strip_tags($data['nama_lengkap']),
        'email' => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
        'kode_booking' => preg_replace('/[^A-Z0-9]/', '', $data['kode_booking']),
    ];
}
```

### 2. Email Header Injection Prevention

**Threat:** Malicious input could inject additional email headers

**Mitigation:**
```php
// Validate email addresses before use
if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
    throw new EmailValidationException('Invalid recipient email address');
}

// Remove newline characters from subject
$subject = str_replace(["\r", "\n"], '', $subject);

// Use Laravel's Mailable headers - framework handles escaping
$this->withHeaders([
    'X-Booking-Code' => preg_replace('/[^A-Z0-9]/', '', $bookingCode),
]);
```

### 3. Gmail App Password Security

**Best Practices:**
- Use Gmail App-Specific Password (not regular password)
- Store in `.env` file (never commit to version control)
- Rotate password every 90 days
- Use separate Gmail account for sending (not personal account)
- Enable 2FA on Gmail account

**Environment Variable:**
```env
MAIL_PASSWORD=your-16-character-app-password
```

### 4. Rate Limiting as DDoS Protection

**Threat:** Attacker could trigger mass email sending

**Mitigation:**
- Hard limit of 100 emails per day (Gmail free tier constraint)
- Additional per-user limit (max 5 bookings per day per email)
- IP-based rate limiting on booking endpoints
- CAPTCHA on booking form

### 5. Sensitive Data in Email Logs

**Threat:** Email logs could expose sensitive customer data

**Mitigation:**
```php
// Log only necessary information
Log::info('Email sent', [
    'type' => 'payment_success',
    'recipient' => $this->maskEmail($recipient),  // john***@example.com
    'booking_code' => $bookingCode,
    // DO NOT log: payment amount, full name, phone number
]);

protected function maskEmail(string $email): string
{
    [$username, $domain] = explode('@', $email);
    $masked = substr($username, 0, 2) . str_repeat('*', strlen($username) - 2);
    return $masked . '@' . $domain;
}
```


## Performance Considerations

### 1. Synchronous Email Sending Impact

**Challenge:** Synchronous email sending blocks HTTP response

**Mitigation:**
- Set aggressive SMTP timeouts (10s connection, 15s read)
- Implement retry logic in background (log failure, retry later)
- Use Laravel's queue system for non-critical emails only
- Monitor average response time for booking endpoints

**Acceptable Timing:**
```
Booking creation: < 2 seconds total
  - Database insert: ~100ms
  - Email send: ~500-1500ms
  - Response generation: ~100ms
```

### 2. Template Rendering Performance

**Optimization:**
- Cache compiled Blade templates (enabled by default)
- Minimize database queries in template (eager load relationships)
- Pre-calculate formatted dates/currency before passing to template

**Example:**
```php
// In EmailService before rendering
$templateData = [
    'pemesanan' => $pemesanan,
    'paket' => $pemesanan->jadwal->paket,  // Eager loaded
    'formatted_date' => Carbon::parse($pemesanan->tanggal_kunjungan)
                              ->locale('id')
                              ->isoFormat('dddd, D MMMM YYYY'),  // Pre-calculated
    'formatted_amount' => 'Rp ' . number_format($pemesanan->total_harga, 0, ',', '.'),
];
```

### 3. Cache Performance for Rate Limiting

**Storage:** Use Redis for cache (better performance than file cache)

**Configuration:**
```php
// config/cache.php
'default' => env('CACHE_DRIVER', 'redis'),

'stores' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'cache',
    ],
],
```

**Cache Key Expiration:**
- Daily counter: Expires at end of day (automatic cleanup)
- No manual cleanup required

### 4. SMTP Connection Pooling

**Laravel Optimization:**
- Symfony Mailer (used by Laravel) maintains connection during script execution
- Multiple emails sent in same request reuse connection
- Connection closed at end of request lifecycle

**No Action Required** - handled by framework

