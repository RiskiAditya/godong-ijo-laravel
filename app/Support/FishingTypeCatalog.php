<?php

namespace App\Support;

final class FishingTypeCatalog
{
    public const DIRECT_TYPES = [
        'sewa_joran',
        'tarikan',
        'jackpot',
        'kiloan',
    ];

    public const DYNAMIC_TYPES = [
        'sewa_joran',
        'mancing_tarikan',
        'mancing_jackpot',
        'mancing_kiloan',
    ];

    private function __construct()
    {
    }
}
