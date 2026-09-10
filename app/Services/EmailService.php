<?php

namespace App\Services;

use App\Models\Pemesanan;
use App\Exceptions\EmailRateLimitException;
use App\Exceptions\EmailValidationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Email Service for Booking Email Deliverability
 * 
 * Provides centralized email management with rate limiting, error handling,
 * and logging for all transactional booking emails.
 * 
 * Features:
 * - Rate limiting with Laravel Cache (100 emails/day default)
 * - Daily counter with automatic midnight reset
 * - 80% threshold warning logging
 * - Template variable validation
 * - Comprehensive error logging
 * 
 * @see Requirements 7.1, 7.2, 7.3, 7.4, 7.5
 */
class EmailService implements IEmailService
{
    /**
     * Send booking confirmation email
     * 
     * @param Pemesanan $pemesanan Booking record
     * @return bool True if sent successfully
     * @throws EmailRateLimitException If daily limit exceeded
     * @throws EmailValidationException If required data missing
     */
    public function sendBookingConfirmation(Pemesanan $pemesanan): bool
    {
        $this->validateTemplateData($pemesanan, 'booking_confirmation');

        // Check rate limit first
        if (!$this->checkRateLimit()) {
            $status = $this->getRateLimitStatus();
            throw new EmailRateLimitException(
                $status->currentCount,
                $status->dailyLimit,
                now()->format('Y-m-d')
            );
        }
        
        return $this->sendEmailWithRetry(
            $pemesanan->email,
            new \App\Mail\BookingConfirmationMail($pemesanan),
            'booking_confirmation',
            $pemesanan->kode_booking
        );
    }
    
    /**
     * Send payment success email with e-ticket
     * 
     * @param Pemesanan $pemesanan Booking record with confirmed payment
     * @return bool True if sent successfully
     */
    public function sendPaymentSuccess(Pemesanan $pemesanan): bool
    {
        $this->validateTemplateData($pemesanan, 'payment_success');

        // Check rate limit first
        if (!$this->checkRateLimit()) {
            $status = $this->getRateLimitStatus();
            throw new EmailRateLimitException(
                $status->currentCount,
                $status->dailyLimit,
                now()->format('Y-m-d')
            );
        }
        
        return $this->sendEmailWithRetry(
            $pemesanan->email,
            new \App\Mail\PaymentSuccessMail($pemesanan),
            'payment_success',
            $pemesanan->kode_booking
        );
    }
    
    /**
     * Send booking cancellation notification
     * 
     * @param Pemesanan $pemesanan Cancelled booking record
     * @param string $reason Cancellation reason
     * @return bool True if sent successfully
     */
    public function sendCancellationNotification(Pemesanan $pemesanan, string $reason): bool
    {
        $this->validateTemplateData($pemesanan, 'cancellation');

        // Check rate limit first
        if (!$this->checkRateLimit()) {
            $status = $this->getRateLimitStatus();
            throw new EmailRateLimitException(
                $status->currentCount,
                $status->dailyLimit,
                now()->format('Y-m-d')
            );
        }
        
        return $this->sendEmailWithRetry(
            $pemesanan->email,
            new \App\Mail\CancellationNotificationMail($pemesanan, $reason),
            'cancellation',
            $pemesanan->kode_booking
        );
    }

    /**
     * Notify the customer when an admin changes the booking status.
     */
    public function sendBookingStatusUpdate(Pemesanan $pemesanan, string $oldStatus): bool
    {
        if (empty($pemesanan->email) || $oldStatus === $pemesanan->status) {
            return false;
        }

        if (!$this->checkRateLimit()) {
            $status = $this->getRateLimitStatus();
            throw new EmailRateLimitException($status->currentCount, $status->dailyLimit, now()->format('Y-m-d'));
        }

        return $this->sendEmailWithRetry(
            $pemesanan->email,
            new \App\Mail\BookingStatusMail($pemesanan, $oldStatus),
            'booking_status_update',
            $pemesanan->kode_booking
        );
    }

    /**
     * Send the one-time H-1 visit reminder and mark it after delivery succeeds.
     */
    public function sendBookingReminder(Pemesanan $pemesanan, string $visitDate): bool
    {
        if ($pemesanan->reminder_sent_at) {
            return true;
        }

        if (empty($pemesanan->email)) {
            return false;
        }

        if (!$this->checkRateLimit()) {
            $status = $this->getRateLimitStatus();
            throw new EmailRateLimitException(
                $status->currentCount,
                $status->dailyLimit,
                now()->format('Y-m-d')
            );
        }

        $sent = $this->sendEmailWithRetry(
            $pemesanan->email,
            new \App\Mail\BookingReminderMail($pemesanan, $visitDate),
            'booking_reminder',
            $pemesanan->kode_booking
        );

        if ($sent) {
            $pemesanan->forceFill(['reminder_sent_at' => now()])->save();
        }

        return $sent;
    }

    /**
     * Send the one-time Google Review request after a completed visit.
     */
    public function sendBookingReviewRequest(Pemesanan $pemesanan): bool
    {
        if ($pemesanan->review_sent_at || empty($pemesanan->email) || !config('app.google_review_url')) {
            return false;
        }

        if (!$this->checkRateLimit()) {
            $status = $this->getRateLimitStatus();
            throw new EmailRateLimitException($status->currentCount, $status->dailyLimit, now()->format('Y-m-d'));
        }

        $sent = $this->sendEmailWithRetry(
            $pemesanan->email,
            new \App\Mail\BookingReviewMail($pemesanan),
            'booking_review',
            $pemesanan->kode_booking
        );

        if ($sent) {
            $pemesanan->forceFill(['review_sent_at' => now()])->save();
        }

        return $sent;
    }
    
    /**
     * Send email with retry logic for temporary SMTP errors
     * 
     * @param string $recipient Email address
     * @param \Illuminate\Mail\Mailable $mailable Mailable instance
     * @param string $type Email type
     * @param string $bookingCode Booking code
     * @return bool True if sent successfully
     */
    protected function sendEmailWithRetry(
        string $recipient,
        \Illuminate\Mail\Mailable $mailable,
        string $type,
        string $bookingCode
    ): bool {
        $maxAttempts = config('mail.smtp_retry_attempts', 2);
        $retryDelay = config('mail.smtp_retry_delay', 5);
        
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                \Illuminate\Support\Facades\Mail::to($recipient)->send($mailable);
                
                // Log success
                $this->logEmailSent($type, $recipient, $bookingCode);
                
                return true;
            } catch (\Exception $e) {
                // Check if it's a temporary error
                $errorCode = $this->extractSmtpErrorCode($e->getMessage());
                $isTemporaryError = in_array($errorCode, ['421', '450']);
                
                // If not last attempt and error is temporary, retry
                if ($attempt < $maxAttempts && $isTemporaryError) {
                    Log::warning('Temporary SMTP error, retrying', [
                        'attempt' => $attempt,
                        'max_attempts' => $maxAttempts,
                        'error_code' => $errorCode,
                        'booking_code' => $bookingCode,
                        'recipient' => $recipient,
                    ]);
                    
                    sleep($retryDelay);
                    continue;
                }
                
                // Log final failure
                $this->logEmailFailed($type, $recipient, $bookingCode, $e);
                
                // Don't throw exception - allow booking to proceed even if email fails
                return false;
            }
        }
        
        return false;
    }
    
    /**
     * Validate template data before sending
     * 
     * @param Pemesanan $pemesanan Booking record
     * @param string $emailType Email type
     * @throws EmailValidationException If required fields missing
     */
    protected function validateTemplateData(Pemesanan $pemesanan, string $emailType): void
    {
        $requiredFields = [
            'booking_confirmation' => ['email', 'kode_booking', 'nama_lengkap', 'total_harga'],
            'payment_success' => ['email', 'kode_booking', 'nama_lengkap', 'total_harga', 'pembayaran'],
            'cancellation' => ['email', 'kode_booking', 'nama_lengkap'],
        ];

        $required = $requiredFields[$emailType] ?? [];
        $isKiloan = ($pemesanan->package_specific_data['jenis_pemancingan'] ?? null) === 'kiloan';

        if ($isKiloan && $emailType === 'booking_confirmation') {
            $required = array_values(array_filter($required, fn ($field) => $field !== 'total_harga'));
        }

        $missing = [];

        foreach ($required as $field) {
            if ($field === 'pembayaran') {
                if (! $pemesanan->pembayaran) {
                    $missing[] = $field;
                }
            } elseif (empty($pemesanan->$field)) {
                $missing[] = $field;
            }
        }

        if (! empty($missing)) {
            throw new EmailValidationException(
                'Missing required template data: ' . implode(', ', $missing),
                [
                    'email_type' => $emailType,
                    'missing_fields' => $missing,
                    'recipient' => $pemesanan->email ?? 'unknown',
                    'booking_code' => $pemesanan->kode_booking ?? 'unknown',
                ]
            );
        }
    }
    
    /**
     * Check if email can be sent within rate limit
     * 
     * Checks current daily email count against configured limit (default: 100).
     * Logs warning at 80% threshold.
     * Increments counter if under limit.
     * 
     * @return bool True if under rate limit
     * @see Requirement 7.1 - Daily rate limit enforcement (100 emails/day)
     * @see Requirement 7.2 - Warning log at 80% threshold
     */
    public function checkRateLimit(): bool
    {
        $cacheKey = 'email_rate_limit:daily:' . now()->format('Y-m-d');
        $limit = config('mail.daily_limit', 100);

        // Reserve one slot atomically to avoid exceeding the limit in parallel requests.
        Cache::add($cacheKey, 0, now()->endOfDay());
        $currentCount = Cache::increment($cacheKey);

        if ($currentCount > $limit) {
            Log::warning('Email rate limit exceeded', [
                'current_count' => $currentCount,
                'limit' => $limit,
                'date' => now()->format('Y-m-d'),
                'timestamp' => now()->toIso8601String(),
            ]);
            return false;
        }
        
        // Check if approaching limit (80% threshold)
        $threshold = config('mail.rate_limit_warning_threshold', 0.8);
        if ($currentCount >= ($limit * $threshold)) {
            Log::warning('Email rate limit approaching', [
                'current_count' => $currentCount,
                'limit' => $limit,
                'percentage' => round(($currentCount / $limit) * 100, 2),
                'remaining' => $limit - $currentCount,
                'threshold' => $threshold,
                'date' => now()->format('Y-m-d'),
                'timestamp' => now()->toIso8601String(),
            ]);
        }
        
        return true;
    }
    
    /**
     * Get remaining daily email quota
     * 
     * @return int Number of emails remaining for today
     * @see Requirement 7.4 - Daily email count tracking
     */
    public function getRemainingDailyQuota(): int
    {
        $cacheKey = 'email_rate_limit:daily:' . now()->format('Y-m-d');
        $currentCount = Cache::get($cacheKey, 0);
        $limit = config('mail.daily_limit', 100);
        
        $remaining = $limit - $currentCount;
        return max(0, $remaining); // Never return negative
    }
    
    /**
     * Get current rate limit status
     * 
     * @return RateLimitStatus Current quota and usage information
     * @see Requirement 7.4 - Daily email count tracking
     */
    public function getRateLimitStatus(): RateLimitStatus
    {
        $cacheKey = 'email_rate_limit:daily:' . now()->format('Y-m-d');
        $currentCount = Cache::get($cacheKey, 0);
        $limit = config('mail.daily_limit', 100);
        $remaining = max(0, $limit - $currentCount);
        $usagePercentage = $limit > 0 ? round(($currentCount / $limit) * 100, 2) : 0.0;
        $canSend = $currentCount < $limit;
        $threshold = config('mail.rate_limit_warning_threshold', 0.8);
        $warningThreshold = $currentCount >= ($limit * $threshold);
        
        return new RateLimitStatus(
            currentCount: $currentCount,
            dailyLimit: $limit,
            remainingQuota: $remaining,
            usagePercentage: $usagePercentage,
            canSend: $canSend,
            warningThreshold: $warningThreshold
        );
    }
    
    /**
     * Log successful email sending
     * 
     * @param string $type Email type (booking_confirmation, payment_success, cancellation)
     * @param string $recipient Customer email address
     * @param string $bookingCode Booking reference code
     * @return void
     * @see Requirement 9.4 - Success logging with timestamp
     */
    protected function logEmailSent(string $type, string $recipient, string $bookingCode): void
    {
        Log::info('Email sent successfully', [
            'type' => $type,
            'recipient' => $recipient,
            'booking_code' => $bookingCode,
            'timestamp' => now()->toIso8601String(),
            'date' => now()->format('Y-m-d'),
        ]);
    }
    
    /**
     * Log failed email sending attempt
     * 
     * @param string $type Email type (booking_confirmation, payment_success, cancellation)
     * @param string $recipient Customer email address
     * @param string $bookingCode Booking reference code
     * @param \Exception $e Exception that caused the failure
     * @return void
     * @see Requirement 9.1 - SMTP error code and message logging
     * @see Requirement 9.2 - Recipient email logging
     * @see Requirement 9.3 - Booking code logging
     */
    protected function logEmailFailed(string $type, string $recipient, string $bookingCode, \Exception $e): void
    {
        // Extract SMTP error code if available
        $errorCode = $this->extractSmtpErrorCode($e->getMessage());
        
        Log::error('Email sending failed', [
            'type' => $type,
            'recipient' => $recipient,
            'booking_code' => $bookingCode,
            'error_code' => $errorCode,
            'error_message' => $e->getMessage(),
            'exception_class' => get_class($e),
            'timestamp' => now()->toIso8601String(),
            'date' => now()->format('Y-m-d'),
            'trace' => $e->getTraceAsString(),
        ]);
    }
    
    /**
     * Extract SMTP error code from exception message
     * 
     * Parses SMTP error codes (421, 450, 535, 550, 554) from exception messages.
     * 
     * @param string $message Exception message
     * @return string|null SMTP error code or null if not found
     */
    protected function extractSmtpErrorCode(string $message): ?string
    {
        // Match common SMTP error codes (3-digit numbers)
        if (preg_match('/\b([45]\d{2})\b/', $message, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
}

/**
 * Email Service Interface
 * 
 * Defines contract for email sending operations with rate limiting
 * and status tracking capabilities.
 */
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

/**
 * Rate Limit Status Data Transfer Object
 * 
 * Contains current email rate limit status and usage statistics.
 */
class RateLimitStatus
{
    /**
     * Create a new RateLimitStatus instance
     * 
     * @param int $currentCount Emails sent today
     * @param int $dailyLimit Maximum emails per day (100)
     * @param int $remainingQuota Emails remaining for today
     * @param float $usagePercentage Percentage of quota used
     * @param bool $canSend Whether sending is allowed
     * @param bool $warningThreshold Whether at 80% threshold
     */
    public function __construct(
        public int $currentCount,
        public int $dailyLimit,
        public int $remainingQuota,
        public float $usagePercentage,
        public bool $canSend,
        public bool $warningThreshold
    ) {}
    
    /**
     * Convert to array representation
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'current_count' => $this->currentCount,
            'daily_limit' => $this->dailyLimit,
            'remaining_quota' => $this->remainingQuota,
            'usage_percentage' => $this->usagePercentage,
            'can_send' => $this->canSend,
            'warning_threshold' => $this->warningThreshold,
        ];
    }
}
