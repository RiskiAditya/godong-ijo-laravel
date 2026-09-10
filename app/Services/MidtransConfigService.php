<?php

namespace App\Services;

use Midtrans\Config;

class MidtransConfigService
{
    public function configure(): void
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        if (config('app.env') === 'local') {
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
