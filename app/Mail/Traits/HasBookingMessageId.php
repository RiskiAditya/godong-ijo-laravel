<?php

namespace App\Mail\Traits;

/**
 * Provides unique Message-ID generation for booking-related emails.
 * 
 * Ensures each email has a unique, RFC-compliant Message-ID that includes
 * the booking code for tracing and threading purposes.
 */
trait HasBookingMessageId
{
    /**
     * Generate unique Message-ID with booking code
     * 
     * Format: <uniqid.kode_booking@domain>
     * Domain falls back to mail.domain config, then app domain parsing
     * 
     * @return string RFC-compliant Message-ID header value
     */
    protected function generateMessageId(): string
    {
        return sprintf(
            '<%s.%s@%s>',
            uniqid(),
            $this->pemesanan->kode_booking,
            config('mail.domain', parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost')
        );
    }
}
