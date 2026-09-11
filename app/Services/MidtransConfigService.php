<?php

namespace App\Services;

use Midtrans\Config;

class MidtransConfigService
{
    public function configure(): void
    {
        $environment = config('app.env');
        $serverKey = trim((string) config('midtrans.server_key', ''));
        $paymentMode = config('midtrans.payment_mode', 'live');
        $isSandboxKey = $serverKey !== '' && str_starts_with($serverKey, 'SB-Mid-server-');
        $isProductionKey = $serverKey !== '' && ! $isSandboxKey;

        if ($environment === 'local') {
            config()->set('midtrans.is_production', false);
            config()->set('midtrans.payment_mode', 'live');
        } elseif ($serverKey !== '' && $isSandboxKey) {
            config()->set('midtrans.payment_mode', 'live');
            config()->set('midtrans.is_production', false);
        } elseif ($paymentMode === 'live' && $isProductionKey) {
            config()->set('midtrans.is_production', true);
        } elseif ($paymentMode === 'simulation' && $serverKey === '') {
            config()->set('midtrans.is_production', false);
        }

        Config::$serverKey = $serverKey;
        Config::$isProduction = (bool) config('midtrans.is_production', $environment !== 'local');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        if ($environment === 'local') {
            Config::$curlOptions = [
                CURLOPT_HTTPHEADER => [],
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CONNECTTIMEOUT => 10,
            ];

            return;
        }

        Config::$curlOptions = [
            CURLOPT_HTTPHEADER => [],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
        ];
    }

    public function isConfigured(): bool
    {
        return ! empty(config('midtrans.server_key'));
    }
}
