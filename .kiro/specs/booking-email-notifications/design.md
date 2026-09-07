# Design Document: Booking Email Notifications

## Overview

The Email Notification System provides automated, transactional email communications throughout the booking lifecycle for The Waterfall tourism platform. The system integrates with Laravel's existing BookingController to send confirmation emails, payment receipts with e-ticket attachments, and administrative alerts at key lifecycle events.

**Design Philosophy:**
- **Event-Driven Architecture**: Email sending is decoupled from booking logic via Laravel events
- **Graceful Degradation**: Email failures never block booking transactions
- **Queue-First Design**: Asynchronous email delivery by default to maintain system performance
- **Provider Agnostic**: Support multiple email providers (Gmail SMTP, SendGrid, Mailtrap) via configuration
- **Template-Based**: Consistent branding with reusable Blade email templates

**Key Design Decision:**
This design uses Laravel's built-in Mail facade and notification system rather than introducing external libraries. Email delivery leverages Laravel's queue system for reliability and performance. The system fires domain events (BookingCreated, PaymentConfirmed, BookingCancelled) that trigger email listeners, maintaining clean separation between booking business logic and notification concerns.

## Architecture

### System Components


```
┌─────────────────────────────────────────────────────────────────┐
│                      BookingController                          │
│  - store()   : Create booking                                   │
│  - notification() : Handle Midtrans webhook                     │
└──────────────┬──────────────────────────┬───────────────────────┘
               │                          │
               │ Dispatch Events          │
               ▼                          ▼
┌──────────────────────────┐   ┌──────────────────────────┐
│   BookingCreated         │   │  PaymentConfirmed        │
│   - $pemesanan           │   │  - $pemesanan            │
│                          │   │  - $pembayaran           │
└────────┬─────────────────┘   └──────────┬───────────────┘
         │                                 │
         │ Trigger Listeners               │
         ▼                                 ▼
┌──────────────────────────┐   ┌──────────────────────────┐
│ BookingCreatedListener   │   │PaymentConfirmedListener  │
│  implements ShouldQueue  │   │ implements ShouldQueue   │
└────────┬─────────────────┘   └──────────┬───────────────┘
         │                                 │
         │ Queue Email Jobs                │
         ▼                                 ▼
┌─────────────────────────────────────────────────────────┐
│                  Laravel Queue System                    │
│  - Database/Redis backed                                 │
│  - Retry logic with exponential backoff                  │
│  - Priority: high (customer), medium (admin)             │
└─────────────────────┬───────────────────────────────────┘
                      │
                      │ Process Jobs
                      ▼
┌─────────────────────────────────────────────────────────┐
│              Email Notification Mailable                 │
│  - BookingConfirmationMail                               │
│  - PaymentSuccessMail                                    │
│  - BookingCancellationMail                               │
│  - AdminBookingAlertMail                                 │
└─────────────────────┬───────────────────────────────────┘
                      │
                      │ Render Templates
                      ▼
┌─────────────────────────────────────────────────────────┐
│           Blade Email Templates                          │
│  - emails/booking-confirmation.blade.php                 │
│  - emails/payment-success.blade.php                      │
│  - emails/booking-cancellation.blade.php                 │
│  - emails/admin-booking-alert.blade.php                  │
│  - emails/layout.blade.php (master template)             │
└─────────────────────┬───────────────────────────────────┘
                      │
                      │ Send via Provider
                      ▼
┌─────────────────────────────────────────────────────────┐
│         Email Service Provider (Configurable)            │
│  - Gmail SMTP (production)                               │
│  - SendGrid API (optional)                               │
│  - Mailtrap (staging)                                    │
│  - Mailpit (local development)                           │
└─────────────────────────────────────────────────────────┘
```

### Event Flow Diagram

**Booking Creation Flow:**
```
Customer submits booking
    → BookingController::store()
    → Create Pemesanan + Pembayaran records
    → Dispatch BookingCreated event
    → BookingCreatedListener queues:
        - BookingConfirmationMail (customer)
        - AdminBookingAlertMail (admin)
    → Queue worker processes jobs
    → Emails sent via configured provider
```


**Payment Confirmation Flow:**
```
Midtrans webhook → BookingController::notification()
    → Update Pembayaran status to 'success'
    → Update Pemesanan status to 'paid'
    → Dispatch PaymentConfirmed event
    → PaymentConfirmedListener queues:
        - PaymentSuccessMail with E-Ticket (customer)
        - AdminPaymentReceivedMail (admin)
    → ETicketService generates PDF
    → Queue worker processes jobs
    → Email sent with PDF attachment
```

### Technology Stack

- **Framework**: Laravel 10.x
- **Email Driver**: Laravel Mail (Illuminate\Mail)
- **Queue System**: Laravel Queue (database or Redis)
- **Template Engine**: Blade
- **PDF Generation**: ETicketService (existing, uses barryvdh/laravel-dompdf)
- **Email Providers**: 
  - Gmail SMTP (production)
  - SendGrid API (optional)
  - Mailtrap (staging)
  - Mailpit (local development)

## Components and Interfaces

### 1. Domain Events


**File**: `app/Events/BookingCreated.php`

```php
namespace App\Events;

use App\Models\Pemesanan;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCreated
{
    use Dispatchable, SerializesModels;

    public Pemesanan $pemesanan;

    public function __construct(Pemesanan $pemesanan)
    {
        $this->pemesanan = $pemesanan;
    }
}
```

**File**: `app/Events/PaymentConfirmed.php`

```php
namespace App\Events;

use App\Models\Pemesanan;
use App\Models\Pembayaran;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmed
{
    use Dispatchable, SerializesModels;

    public Pemesanan $pemesanan;
    public Pembayaran $pembayaran;

    public function __construct(Pemesanan $pemesanan, Pembayaran $pembayaran)
    {
        $this->pemesanan = $pemesanan;
        $this->pembayaran = $pembayaran;
    }
}
```


**File**: `app/Events/BookingCancelled.php`

```php
namespace App\Events;

use App\Models\Pemesanan;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCancelled
{
    use Dispatchable, SerializesModels;

    public Pemesanan $pemesanan;
    public string $reason;

    public function __construct(Pemesanan $pemesanan, string $reason = 'Payment timeout')
    {
        $this->pemesanan = $pemesanan;
        $this->reason = $reason;
    }
}
```

### 2. Event Listeners

**File**: `app/Listeners/BookingCreatedListener.php`

```php
namespace App\Listeners;

use App\Events\BookingCreated;
use App\Mail\BookingConfirmationMail;
use App\Mail\AdminBookingAlertMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class BookingCreatedListener implements ShouldQueue
{
    public $queue = 'emails';
    public $tries = 3;
    public $backoff = [60, 180, 600]; // 1min, 3min, 10min

    public function handle(BookingCreated $event): void
    {
        $pemesanan = $event->pemesanan->load(['jadwal.paket', 'pembayaran']);

        // Send customer confirmation email
        try {
            Mail::to($pemesanan->email)
                ->queue(new BookingConfirmationMail($pemesanan));
        } catch (\Exception $e) {
            Log::error('Failed to queue booking confirmation email', [
                'kode_booking' => $pemesanan->kode_booking,
                'error' => $e->getMessage()
            ]);
        }

        // Send admin alert email
        $adminEmail = config('mail.admin_email');
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)
                    ->queue(new AdminBookingAlertMail($pemesanan));
            } catch (\Exception $e) {
                Log::error('Failed to queue admin booking alert', [
                    'kode_booking' => $pemesanan->kode_booking,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    public function failed(BookingCreated $event, \Throwable $exception): void
    {
        Log::critical('BookingCreatedListener failed after max retries', [
            'kode_booking' => $event->pemesanan->kode_booking,
            'error' => $exception->getMessage()
        ]);
    }
}
```


**File**: `app/Listeners/PaymentConfirmedListener.php`

```php
namespace App\Listeners;

use App\Events\PaymentConfirmed;
use App\Mail\PaymentSuccessMail;
use App\Mail\AdminPaymentReceivedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PaymentConfirmedListener implements ShouldQueue
{
    public $queue = 'emails';
    public $tries = 3;
    public $backoff = [60, 180, 600];

    public function handle(PaymentConfirmed $event): void
    {
        $pemesanan = $event->pemesanan->load(['jadwal.paket', 'pembayaran']);
        $pembayaran = $event->pembayaran;

        // Send customer payment success email with e-ticket
        try {
            Mail::to($pemesanan->email)
                ->queue(new PaymentSuccessMail($pemesanan, $pembayaran));
        } catch (\Exception $e) {
            Log::error('Failed to queue payment success email', [
                'kode_booking' => $pemesanan->kode_booking,
                'error' => $e->getMessage()
            ]);
        }

        // Send admin payment received notification
        $adminEmail = config('mail.admin_email');
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)
                    ->queue(new AdminPaymentReceivedMail($pemesanan, $pembayaran));
            } catch (\Exception $e) {
                Log::error('Failed to queue admin payment notification', [
                    'kode_booking' => $pemesanan->kode_booking,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    public function failed(PaymentConfirmed $event, \Throwable $exception): void
    {
        Log::critical('PaymentConfirmedListener failed after max retries', [
            'kode_booking' => $event->pemesanan->kode_booking,
            'error' => $exception->getMessage()
        ]);
        
        // Alert admin about critical failure
        $adminEmail = config('mail.admin_email');
        if ($adminEmail) {
            Mail::to($adminEmail)->send(
                new \App\Mail\CriticalEmailFailureMail($event->pemesanan, $exception)
            );
        }
    }
}
```


### 3. Mailable Classes

**File**: `app/Mail/BookingConfirmationMail.php`

```php
namespace App\Mail;

use App\Models\Pemesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Pemesanan $pemesanan;

    public function __construct(Pemesanan $pemesanan)
    {
        $this->pemesanan = $pemesanan;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Konfirmasi Booking - ' . $this->pemesanan->kode_booking,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-confirmation',
            with: [
                'pemesanan' => $this->pemesanan,
                'paket' => $this->pemesanan->jadwal->paket,
                'jadwal' => $this->pemesanan->jadwal,
                'pembayaran' => $this->pemesanan->pembayaran,
            ],
        );
    }
}
```


**File**: `app/Mail/PaymentSuccessMail.php`

```php
namespace App\Mail;

use App\Models\Pemesanan;
use App\Models\Pembayaran;
use App\Services\ETicketService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PaymentSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public Pemesanan $pemesanan;
    public Pembayaran $pembayaran;
    private ?string $eticketPath = null;

    public function __construct(Pemesanan $pemesanan, Pembayaran $pembayaran)
    {
        $this->pemesanan = $pemesanan;
        $this->pembayaran = $pembayaran;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pembayaran Berhasil - E-Ticket ' . $this->pemesanan->kode_booking,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-success',
            with: [
                'pemesanan' => $this->pemesanan,
                'pembayaran' => $this->pembayaran,
                'paket' => $this->pemesanan->jadwal->paket,
                'jadwal' => $this->pemesanan->jadwal,
            ],
        );
    }

    public function attachments(): array
    {
        try {
            $eticketService = new ETicketService();
            $this->eticketPath = $eticketService->generate($this->pemesanan->kode_booking);
            
            if ($this->eticketPath && Storage::exists($this->eticketPath)) {
                return [
                    Attachment::fromStorage($this->eticketPath)
                        ->as("E-Ticket-{$this->pemesanan->kode_booking}.pdf")
                        ->withMime('application/pdf')
                ];
            }
        } catch (\Exception $e) {
            Log::error('E-Ticket generation failed for email', [
                'kode_booking' => $this->pemesanan->kode_booking,
                'error' => $e->getMessage()
            ]);
        }

        return [];
    }

    public function __destruct()
    {
        // Clean up temporary e-ticket file after email sent
        if ($this->eticketPath && Storage::exists($this->eticketPath)) {
            Storage::delete($this->eticketPath);
        }
    }
}
```


### 4. Email Formatter Service

**File**: `app/Services/EmailFormatterService.php`

```php
namespace App\Services;

use Carbon\Carbon;

class EmailFormatterService
{
    /**
     * Format Indonesian Rupiah currency
     */
    public static function formatCurrency(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Format date in Indonesian locale
     */
    public static function formatDate($date): string
    {
        if (!$date instanceof Carbon) {
            $date = Carbon::parse($date);
        }
        
        return $date->locale('id')->isoFormat('dddd, D MMMM YYYY');
    }

    /**
     * Format datetime in Indonesian locale
     */
    public static function formatDateTime($datetime): string
    {
        if (!$datetime instanceof Carbon) {
            $datetime = Carbon::parse($datetime);
        }
        
        return $datetime->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm') . ' WIB';
    }

    /**
     * Format phone number for display
     */
    public static function formatPhone(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($cleaned) >= 10) {
            return preg_replace('/(\d{4})(\d{4})(\d+)/', '$1-$2-$3', $cleaned);
        }
        
        return $phone;
    }

    /**
     * Get payment method label in Indonesian
     */
    public static function getPaymentMethodLabel(?string $method): string
    {
        if (!$method) return 'Belum dibayar';

        $labels = [
            'credit_card' => 'Kartu Kredit',
            'bca_va' => 'BCA Virtual Account',
            'bni_va' => 'BNI Virtual Account',
            'bri_va' => 'BRI Virtual Account',
            'permata_va' => 'Permata Virtual Account',
            'gopay' => 'GoPay',
            'shopeepay' => 'ShopeePay',
            'qris' => 'QRIS',
            'bank_transfer' => 'Transfer Bank',
            'simulation' => 'Simulasi (Testing)',
        ];

        return $labels[$method] ?? ucwords(str_replace('_', ' ', $method));
    }

    /**
     * Escape HTML for email safety
     */
    public static function escapeHtml(?string $text): string
    {
        return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
    }
}
```

