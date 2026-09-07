<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Custom exception for booking-related errors with structured context and recovery hints.
 * 
 * This exception automatically logs to the 'booking_errors' channel upon instantiation,
 * including all context data and stack traces for debugging.
 * 
 * Post-migration guarantee: This exception helps identify and resolve data integrity
 * issues in the booking system, particularly null jadwal_id or paket_wisata_id scenarios.
 */
class BookingException extends Exception
{
    /**
     * Additional context data for the exception.
     * 
     * Expected context fields:
     * - kode_booking: The booking code
     * - pemesanan_id: The pemesanan record ID
     * - recovery_hint: Actionable guidance for resolving the error
     * 
     * @var array
     */
    protected array $context = [];
    
    /**
     * Create a new BookingException instance.
     * 
     * Automatically logs the exception to the 'booking_errors' channel
     * with full context and stack trace.
     * 
     * @param string $message The exception message
     * @param array $context Additional context data (kode_booking, pemesanan_id, recovery_hint, etc.)
     * @param int $code The exception code (default: 0)
     * @param Throwable|null $previous Previous exception for chaining
     */
    public function __construct(
        string $message,
        array $context = [],
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
        $this->logException();
    }
    
    /**
     * Get the exception context data.
     * 
     * @return array The context array containing booking details and recovery hints
     */
    public function getContext(): array
    {
        return $this->context;
    }
    
    /**
     * Extract recovery hint from context.
     * 
     * Recovery hints provide actionable guidance for resolving the error:
     * - "Run migration to backfill jadwal_id from paket_wisata_id and tanggal_kunjungan"
     * - "Contact administrator - paket_wisata_id is required and cannot be automatically recovered"
     * 
     * @return string|null The recovery hint, or null if not provided
     */
    public function getRecoveryHint(): ?string
    {
        return $this->context['recovery_hint'] ?? null;
    }
    
    /**
     * Log the exception to the booking_errors channel.
     * 
     * Automatically called during construction. Logs include:
     * - Error message
     * - Booking code (kode_booking)
     * - Pemesanan ID (pemesanan_id)
     * - Recovery hint
     * - Full context data
     * - Stack trace
     * 
     * @return void
     */
    protected function logException(): void
    {
        Log::channel('booking_errors')->error($this->getMessage(), [
            'kode_booking' => $this->context['kode_booking'] ?? null,
            'pemesanan_id' => $this->context['pemesanan_id'] ?? null,
            'recovery_hint' => $this->getRecoveryHint(),
            'context' => $this->context,
            'trace' => $this->getTraceAsString(),
        ]);
    }
}
