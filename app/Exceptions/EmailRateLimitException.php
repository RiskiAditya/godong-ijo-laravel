<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when email daily rate limit is exceeded.
 * 
 * This exception is triggered when the Email_System attempts to send
 * an email but has already reached the configured daily sending limit
 * (default: 100 emails per day for Gmail SMTP free tier).
 * 
 * The exception includes context about the current email count, the
 * configured limit, and the date for which the limit applies.
 * 
 * @see Requirement 7.3 - Rate Limit Exception Handling
 */
class EmailRateLimitException extends Exception
{
    /**
     * Current email count for the day.
     *
     * @var int
     */
    protected int $currentCount;
    
    /**
     * Maximum allowed emails per day.
     *
     * @var int
     */
    protected int $limit;
    
    /**
     * Date for which the rate limit applies (Y-m-d format).
     *
     * @var string
     */
    protected string $date;
    
    /**
     * Create a new EmailRateLimitException instance.
     *
     * @param int $currentCount Current number of emails sent today
     * @param int $limit Maximum emails allowed per day
     * @param string $date Date in Y-m-d format
     * @param int $code Exception code (default: 429 Too Many Requests)
     * @param \Throwable|null $previous Previous exception for chaining
     */
    public function __construct(
        int $currentCount,
        int $limit,
        string $date,
        int $code = 429,
        ?\Throwable $previous = null
    ) {
        $this->currentCount = $currentCount;
        $this->limit = $limit;
        $this->date = $date;
        
        $message = sprintf(
            'Email daily rate limit exceeded: %d/%d emails sent on %s',
            $currentCount,
            $limit,
            $date
        );
        
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * Get the current email count.
     *
     * @return int Number of emails sent today
     */
    public function getCurrentCount(): int
    {
        return $this->currentCount;
    }
    
    /**
     * Get the daily email limit.
     *
     * @return int Maximum emails allowed per day
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
    
    /**
     * Get the date for which the limit applies.
     *
     * @return string Date in Y-m-d format
     */
    public function getDate(): string
    {
        return $this->date;
    }
    
    /**
     * Get remaining quota (will be 0 or negative when exception is thrown).
     *
     * @return int Number of emails remaining (0 or negative)
     */
    public function getRemainingQuota(): int
    {
        return $this->limit - $this->currentCount;
    }
    
    /**
     * Get usage percentage.
     *
     * @return float Percentage of daily quota used (e.g., 100.0 or higher)
     */
    public function getUsagePercentage(): float
    {
        if ($this->limit === 0) {
            return 0.0;
        }
        
        return ($this->currentCount / $this->limit) * 100;
    }
}
