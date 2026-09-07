<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Custom exception for email authentication errors with SMTP configuration context.
 * 
 * This exception is thrown when SMTP authentication fails (typically error code 535)
 * and automatically logs the failure including SMTP configuration details (without
 * exposing sensitive credentials) for debugging purposes.
 * 
 * Used in the booking email deliverability system to track Gmail SMTP authentication
 * issues and ensure proper error reporting per requirements 1.5 and 17.6.
 */
class EmailAuthenticationException extends Exception
{
    /**
     * SMTP configuration context for the exception.
     * 
     * Expected context fields:
     * - host: SMTP server hostname
     * - port: SMTP server port
     * - encryption: Encryption method (tls, ssl)
     * - username: SMTP authentication username (email address)
     * - error_code: SMTP error code (e.g., 535)
     * - timestamp: When the error occurred
     * 
     * Note: Password is never included in context for security reasons.
     * 
     * @var array
     */
    protected array $context = [];
    
    /**
     * Create a new EmailAuthenticationException instance.
     * 
     * Automatically logs the exception to the application log
     * with full SMTP configuration context (excluding password).
     * 
     * @param string $message The exception message
     * @param array $context SMTP configuration context (host, port, username, etc.)
     * @param int $code The exception code (default: 535 for authentication failure)
     * @param Throwable|null $previous Previous exception for chaining
     */
    public function __construct(
        string $message = 'SMTP authentication failed',
        array $context = [],
        int $code = 535,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
        $this->logException();
    }
    
    /**
     * Get the exception context data.
     * 
     * @return array The context array containing SMTP configuration details
     */
    public function getContext(): array
    {
        return $this->context;
    }
    
    /**
     * Get the SMTP host from context.
     * 
     * @return string|null The SMTP hostname, or null if not provided
     */
    public function getSmtpHost(): ?string
    {
        return $this->context['host'] ?? null;
    }
    
    /**
     * Get the SMTP username from context.
     * 
     * @return string|null The SMTP username (email address), or null if not provided
     */
    public function getSmtpUsername(): ?string
    {
        return $this->context['username'] ?? null;
    }
    
    /**
     * Get the SMTP error code from context.
     * 
     * @return string|null The SMTP error code, or null if not provided
     */
    public function getSmtpErrorCode(): ?string
    {
        return $this->context['error_code'] ?? null;
    }
    
    /**
     * Log the exception to the application log.
     * 
     * Automatically called during construction. Logs include:
     * - Error message
     * - SMTP host
     * - SMTP port
     * - SMTP username
     * - SMTP encryption method
     * - SMTP error code
     * - Timestamp
     * - Stack trace
     * 
     * Password is NEVER logged for security reasons.
     * 
     * @return void
     */
    protected function logException(): void
    {
        Log::critical('SMTP authentication failure', [
            'message' => $this->getMessage(),
            'host' => $this->context['host'] ?? null,
            'port' => $this->context['port'] ?? null,
            'username' => $this->context['username'] ?? null,
            'encryption' => $this->context['encryption'] ?? null,
            'error_code' => $this->context['error_code'] ?? null,
            'timestamp' => $this->context['timestamp'] ?? now()->toIso8601String(),
            'context' => $this->context,
            'trace' => $this->getTraceAsString(),
        ]);
    }
}
