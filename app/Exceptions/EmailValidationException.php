<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Custom exception for email template validation errors.
 * 
 * This exception is thrown when email template data validation fails,
 * specifically when required fields are missing before rendering email templates.
 * 
 * Automatically logs validation failures to the 'email_errors' channel
 * with details about missing fields and the affected email type.
 * 
 * @see Requirements 10.2, 10.3, 10.4
 */
class EmailValidationException extends Exception
{
    /**
     * Additional context data for the exception.
     * 
     * Expected context fields:
     * - email_type: Type of email being validated (booking_confirmation, payment_success, cancellation)
     * - missing_fields: Array of required fields that are missing
     * - recipient: Email address of the intended recipient
     * - booking_code: Associated booking code (if available)
     * 
     * @var array
     */
    protected array $context = [];
    
    /**
     * Create a new EmailValidationException instance.
     * 
     * Automatically logs the exception to the 'email_errors' channel
     * with full context including missing fields.
     * 
     * @param string $message The exception message describing the validation failure
     * @param array $context Additional context data (email_type, missing_fields, recipient, booking_code)
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
     * @return array The context array containing email type, missing fields, and other details
     */
    public function getContext(): array
    {
        return $this->context;
    }
    
    /**
     * Get the list of missing required fields.
     * 
     * @return array Array of field names that were missing during validation
     */
    public function getMissingFields(): array
    {
        return $this->context['missing_fields'] ?? [];
    }
    
    /**
     * Get the email type that failed validation.
     * 
     * @return string|null The email type (booking_confirmation, payment_success, cancellation)
     */
    public function getEmailType(): ?string
    {
        return $this->context['email_type'] ?? null;
    }
    
    /**
     * Get the recipient email address.
     * 
     * @return string|null The intended recipient's email address
     */
    public function getRecipient(): ?string
    {
        return $this->context['recipient'] ?? null;
    }
    
    /**
     * Get the associated booking code.
     * 
     * @return string|null The booking code related to this email
     */
    public function getBookingCode(): ?string
    {
        return $this->context['booking_code'] ?? null;
    }
    
    /**
     * Log the exception to the email_errors channel.
     * 
     * Automatically called during construction. Logs include:
     * - Error message
     * - Email type
     * - Missing fields list
     * - Recipient email
     * - Booking code
     * - Full context data
     * - Stack trace
     * 
     * @return void
     */
    protected function logException(): void
    {
        Log::channel('email_errors')->error($this->getMessage(), [
            'email_type' => $this->getEmailType(),
            'missing_fields' => $this->getMissingFields(),
            'recipient' => $this->getRecipient(),
            'booking_code' => $this->getBookingCode(),
            'context' => $this->context,
            'trace' => $this->getTraceAsString(),
        ]);
    }
}
