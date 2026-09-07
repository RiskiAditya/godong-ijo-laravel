<?php

namespace Tests\Unit;

use App\Http\Controllers\BookingController;
use Midtrans\Config;
use Tests\TestCase;

class MidtransConfigurationTest extends TestCase
{
    public function test_booking_controller_initializes_midtrans_curl_headers(): void
    {
        config()->set('app.env', 'local');
        config()->set('midtrans.server_key', 'Mid-server-test');
        config()->set('midtrans.client_key', 'Mid-client-test');
        config()->set('midtrans.is_production', false);
        config()->set('midtrans.is_sanitized', true);
        config()->set('midtrans.is_3ds', true);

        // Mock EmailService to satisfy BookingController constructor dependency
        $emailService = $this->createMock(\App\Services\EmailService::class);
        $notificationService = $this->createMock(\App\Services\NotificationService::class);
        new BookingController($emailService, $notificationService);

        $this->assertArrayHasKey(CURLOPT_HTTPHEADER, Config::$curlOptions);
        $this->assertSame([], Config::$curlOptions[CURLOPT_HTTPHEADER]);
    }
}
