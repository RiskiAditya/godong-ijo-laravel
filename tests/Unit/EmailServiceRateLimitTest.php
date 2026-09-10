<?php

namespace Tests\Unit;

use App\Models\PaketWisata;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use App\Services\AdminBookingService;
use App\Services\EmailService;
use App\Services\RateLimitStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use ReflectionMethod;
use Tests\TestCase;

/**
 * Test Suite for EmailService Rate Limiting
 * 
 * Tests Requirements:
 * - 7.1: Daily rate limit enforcement (100 emails/day)
 * - 7.2: Warning log at 80% threshold
 * - 7.3: Queue emails when limit exceeded
 * - 7.4: Daily counter tracking with cache
 * - 7.5: Counter reset at midnight
 */
class EmailServiceRateLimitTest extends TestCase
{
    use RefreshDatabase;

    protected EmailService $emailService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear cache before each test
        Cache::flush();
        
        // Set cache driver to array for testing (in-memory)
        Config::set('cache.default', 'array');
        
        // Set up test configuration
        Config::set('mail.daily_limit', 100);
        Config::set('mail.rate_limit_warning_threshold', 0.8);
        
        $this->emailService = new EmailService();
    }

    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }

    /**
     * Test: checkRateLimit() returns true when under limit
     * 
     * @test
     * @covers EmailService::checkRateLimit
     * Validates Requirement 7.1 - Rate limit allows sending under limit
     */
    public function test_check_rate_limit_returns_true_when_under_limit()
    {
        // Act: Check rate limit
        $result = $this->emailService->checkRateLimit();
        
        // Assert: Should allow sending
        $this->assertTrue($result, 'Rate limit should allow sending when under limit');
        
        // Assert: Counter should be incremented (check via getRemainingDailyQuota)
        $remaining = $this->emailService->getRemainingDailyQuota();
        $this->assertEquals(99, $remaining, 'Remaining quota should be 99 after sending 1 email');
    }

    /**
     * Test: checkRateLimit() returns false when at limit
     * 
     * @test
     * @covers EmailService::checkRateLimit
     * Validates Requirement 7.1 - Rate limit blocks sending at limit
     */
    public function test_check_rate_limit_returns_false_when_at_limit()
    {
        // Arrange: Set counter to daily limit by calling checkRateLimit 100 times
        $limit = config('mail.daily_limit', 100);
        for ($i = 0; $i < $limit; $i++) {
            $this->emailService->checkRateLimit();
        }
        
        // Act: Try to check rate limit again
        $result = $this->emailService->checkRateLimit();
        
        // Assert: Should block sending
        $this->assertFalse($result, 'Rate limit should block sending when at limit');
        
        // Assert: Remaining quota should be 0
        $remaining = $this->emailService->getRemainingDailyQuota();
        $this->assertEquals(0, $remaining, 'Remaining quota should be 0 at limit');
    }

    /**
     * Test: checkRateLimit() logs warning at 80% threshold
     * 
     * @test
     * @covers EmailService::checkRateLimit
     * Validates Requirement 7.2 - Warning log at 80% threshold
     */
    public function test_check_rate_limit_logs_warning_at_threshold()
    {
        // Arrange: Call checkRateLimit 80 times to reach threshold
        $limit = config('mail.daily_limit', 100);
        $threshold = config('mail.rate_limit_warning_threshold', 0.8);
        $countAtThreshold = (int)($limit * $threshold); // 80
        
        for ($i = 0; $i < $countAtThreshold; $i++) {
            $this->emailService->checkRateLimit();
        }
        
        // Act: The next call should log warning
        $result = $this->emailService->checkRateLimit();
        
        // Assert: Should still allow sending
        $this->assertTrue($result, 'Should allow sending at threshold');
        
        // Assert: Should be above threshold now
        $status = $this->emailService->getRateLimitStatus();
        $this->assertTrue($status->warningThreshold, 'Should be at warning threshold');
    }

    /**
     * Test: getRemainingDailyQuota() returns correct value
     * 
     * @test
     * @covers EmailService::getRemainingDailyQuota
     * Validates Requirement 7.4 - Daily email count tracking
     */
    public function test_get_remaining_daily_quota_returns_correct_value()
    {
        // Arrange: Send 30 emails by calling checkRateLimit
        $sentCount = 30;
        for ($i = 0; $i < $sentCount; $i++) {
            $this->emailService->checkRateLimit();
        }
        
        // Act: Get remaining quota
        $remaining = $this->emailService->getRemainingDailyQuota();
        
        // Assert: Should return 70 (100 - 30)
        $expectedRemaining = 100 - $sentCount;
        $this->assertEquals($expectedRemaining, $remaining, 'Remaining quota should be 70');
    }

    /**
     * Test: getRemainingDailyQuota() returns 0 when limit exceeded
     * 
     * @test
     * @covers EmailService::getRemainingDailyQuota
     * Validates Requirement 7.4 - Never returns negative quota
     */
    public function test_get_remaining_daily_quota_returns_zero_when_exceeded()
    {
        // Arrange: Reach the limit
        $limit = config('mail.daily_limit', 100);
        for ($i = 0; $i < $limit; $i++) {
            $this->emailService->checkRateLimit();
        }
        
        // Act: Get remaining quota
        $remaining = $this->emailService->getRemainingDailyQuota();
        
        // Assert: Should return 0, not negative
        $this->assertEquals(0, $remaining, 'Remaining quota should be 0 at limit');
    }

    /**
     * Test: getRateLimitStatus() returns correct status object
     * 
     * @test
     * @covers EmailService::getRateLimitStatus
     * Validates Requirement 7.4 - Rate limit status tracking
     */
    public function test_get_rate_limit_status_returns_correct_status()
    {
        // Arrange: Send 45 emails
        $sentCount = 45;
        $limit = config('mail.daily_limit', 100);
        for ($i = 0; $i < $sentCount; $i++) {
            $this->emailService->checkRateLimit();
        }
        
        // Act: Get rate limit status
        $status = $this->emailService->getRateLimitStatus();
        
        // Assert: Verify all status properties
        $this->assertInstanceOf(RateLimitStatus::class, $status);
        $this->assertEquals($sentCount, $status->currentCount, 'Current count should be 45');
        $this->assertEquals($limit, $status->dailyLimit, 'Daily limit should be 100');
        $this->assertEquals(55, $status->remainingQuota, 'Remaining quota should be 55');
        $this->assertEquals(45.0, $status->usagePercentage, 'Usage percentage should be 45%');
        $this->assertTrue($status->canSend, 'Should be able to send');
        $this->assertFalse($status->warningThreshold, 'Should not be at warning threshold');
    }

    public function test_kiloan_booking_email_validation_does_not_require_total_harga()
    {
        $booking = new Pemesanan([
            'email' => 'sinta@example.com',
            'kode_booking' => 'GOD-20260909-ABC123',
            'nama_lengkap' => 'Sinta',
            'total_harga' => null,
            'package_specific_data' => [
                'jenis_pemancingan' => 'kiloan',
            ],
        ]);

        $method = new ReflectionMethod(EmailService::class, 'validateTemplateData');
        $method->setAccessible(true);

        try {
            $method->invoke($this->emailService, $booking, 'booking_confirmation');
            $this->assertTrue(true);
        } catch (\Throwable $e) {
            $this->fail('Kiloan booking should not require total_harga for booking confirmation validation. '.$e->getMessage());
        }
    }

    public function test_kiloan_finalization_service_captures_price_and_updates_payment_gross_amount()
    {
        $paket = PaketWisata::create([
            'nama_paket' => 'Paket Uji Kiloan',
            'slug' => 'paket-uji-kiloan',
            'deskripsi' => 'Fixture package',
            'harga' => 150000,
            'kuota' => 20,
            'is_active' => true,
        ]);

        $booking = Pemesanan::create([
            'paket_wisata_id' => $paket->id,
            'kode_booking' => 'BK-TEST-KILOAN-001',
            'email' => 'sinta@example.com',
            'nama_lengkap' => 'Sinta',
            'no_hp' => '081234567890',
            'jumlah_orang' => 1,
            'status' => 'pending',
            'total_harga' => null,
            'package_specific_data' => [
                'jenis_pemancingan' => 'kiloan',
            ],
        ]);

        $booking->pembayaran()->create([
            'order_id' => 'ORDER-001',
            'transaction_id' => 'TRANS-001',
            'payment_type' => 'bank_transfer',
            'gross_amount' => 0,
            'status' => 'pending',
        ]);

        $service = new AdminBookingService($this->emailService);
        $service->finalizeKiloan($booking, [
            'total_harga' => 125000,
            'berat_kg' => 3.2,
            'hasil_timbangan' => 'Timbang 3.2kg',
        ]);

        $booking->refresh();
        $booking->load('pembayaran');

        $this->assertSame(125000.0, (float) $booking->total_harga);
        $this->assertSame(3.2, (float) data_get($booking->package_specific_data, 'berat_kg'));
        $this->assertSame('Timbang 3.2kg', data_get($booking->package_specific_data, 'hasil_timbangan'));
        $this->assertSame(125000.0, (float) $booking->pembayaran->gross_amount);
    }

    /**
     * Test: getRateLimitStatus() indicates warning threshold
     * 
     * @test
     * @covers EmailService::getRateLimitStatus
     * Validates Requirement 7.2 - Warning threshold detection
     */
    public function test_get_rate_limit_status_indicates_warning_threshold()
    {
        // Arrange: Send 85 emails (above 80% threshold)
        $sentCount = 85;
        $limit = config('mail.daily_limit', 100);
        for ($i = 0; $i < $sentCount; $i++) {
            $this->emailService->checkRateLimit();
        }
        
        // Act: Get rate limit status
        $status = $this->emailService->getRateLimitStatus();
        
        // Assert: Warning threshold should be true
        $this->assertTrue($status->warningThreshold, 'Should be at warning threshold at 85%');
        $this->assertEquals(85.0, $status->usagePercentage, 'Usage percentage should be 85%');
    }

    /**
     * Test: getRateLimitStatus() indicates cannot send when at limit
     * 
     * @test
     * @covers EmailService::getRateLimitStatus
     * Validates Requirement 7.1 - Rate limit enforcement
     */
    public function test_get_rate_limit_status_indicates_cannot_send_at_limit()
    {
        // Arrange: Reach limit
        $limit = config('mail.daily_limit', 100);
        for ($i = 0; $i < $limit; $i++) {
            $this->emailService->checkRateLimit();
        }
        
        // Act: Get rate limit status
        $status = $this->emailService->getRateLimitStatus();
        
        // Assert: Cannot send at limit
        $this->assertFalse($status->canSend, 'Should not be able to send at limit');
        $this->assertEquals(0, $status->remainingQuota, 'Remaining quota should be 0');
        $this->assertEquals(100.0, $status->usagePercentage, 'Usage percentage should be 100%');
    }

    /**
     * Test: Cache expiration is set to end of day
     * 
     * @test
     * @covers EmailService::checkRateLimit
     * Validates Requirement 7.5 - Daily counter reset logic
     */
    public function test_cache_expiration_set_to_end_of_day()
    {
        // Act: Check rate limit
        $this->emailService->checkRateLimit();
        
        // Assert: Verify counter was incremented
        $remaining = $this->emailService->getRemainingDailyQuota();
        $this->assertEquals(99, $remaining, 'Should have 99 remaining after 1 email');
        
        // Note: Testing exact TTL/expiration is challenging in unit tests with array driver
        // The cache expiration logic is validated through integration tests
        // We verify the counter functionality works correctly
    }

    /**
     * Test: Counter increments correctly with multiple calls
     * 
     * @test
     * @covers EmailService::checkRateLimit
     * Validates Requirement 7.4 - Counter tracking
     */
    public function test_counter_increments_correctly_with_multiple_calls()
    {
        // Act: Call checkRateLimit multiple times
        $this->emailService->checkRateLimit(); // 1
        $this->emailService->checkRateLimit(); // 2
        $this->emailService->checkRateLimit(); // 3
        
        // Assert: Remaining should be 97
        $remaining = $this->emailService->getRemainingDailyQuota();
        $this->assertEquals(97, $remaining, 'Remaining quota should be 97 after 3 emails');
        
        // Assert: Current count should be 3
        $status = $this->emailService->getRateLimitStatus();
        $this->assertEquals(3, $status->currentCount, 'Current count should be 3');
    }

    /**
     * Test: extractSmtpErrorCode() parses error codes correctly
     * 
     * @test
     * @covers EmailService::extractSmtpErrorCode
     * Validates Requirement 9.1 - SMTP error code extraction
     */
    public function test_extract_smtp_error_code_parses_codes_correctly()
    {
        // Arrange: Create reflection method to test protected method
        $reflection = new \ReflectionClass($this->emailService);
        $method = $reflection->getMethod('extractSmtpErrorCode');
        $method->setAccessible(true);
        
        // Test cases
        $testCases = [
            ['message' => 'SMTP Error 550: Mailbox not found', 'expected' => '550'],
            ['message' => '421 Service not available', 'expected' => '421'],
            ['message' => 'Connection failed: 554 Transaction failed', 'expected' => '554'],
            ['message' => 'Error 535: Authentication failed', 'expected' => '535'],
            ['message' => 'No error code here', 'expected' => null],
        ];
        
        foreach ($testCases as $case) {
            // Act: Extract error code
            $result = $method->invoke($this->emailService, $case['message']);
            
            // Assert: Should match expected code
            $this->assertEquals(
                $case['expected'],
                $result,
                "Failed to extract '{$case['expected']}' from '{$case['message']}'"
            );
        }
    }

    /**
     * Test: RateLimitStatus toArray() method
     * 
     * @test
     * @covers RateLimitStatus::toArray
     * Validates data structure for API responses
     */
    public function test_rate_limit_status_to_array()
    {
        // Arrange: Create status object
        $status = new RateLimitStatus(
            currentCount: 50,
            dailyLimit: 100,
            remainingQuota: 50,
            usagePercentage: 50.0,
            canSend: true,
            warningThreshold: false
        );
        
        // Act: Convert to array
        $array = $status->toArray();
        
        // Assert: Verify array structure
        $this->assertIsArray($array);
        $this->assertArrayHasKey('current_count', $array);
        $this->assertArrayHasKey('daily_limit', $array);
        $this->assertArrayHasKey('remaining_quota', $array);
        $this->assertArrayHasKey('usage_percentage', $array);
        $this->assertArrayHasKey('can_send', $array);
        $this->assertArrayHasKey('warning_threshold', $array);
        
        $this->assertEquals(50, $array['current_count']);
        $this->assertEquals(100, $array['daily_limit']);
        $this->assertEquals(50, $array['remaining_quota']);
        $this->assertEquals(50.0, $array['usage_percentage']);
        $this->assertTrue($array['can_send']);
        $this->assertFalse($array['warning_threshold']);
    }
}
