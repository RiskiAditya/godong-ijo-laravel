<?php

namespace App\Support;

class PrivateRoomPackageCatalog
{
    public static function cards(): array
    {
        return [
            [
                'key' => 'meeting',
                'name' => 'Meeting Package',
                'description' => 'Pilihan meeting half day, full day, dan VIP.',
                'image' => 'images/Private Images/Paket-Meeting-1536x701.webp',
                'options' => [
                    [
                        'key' => 'meeting_half_day',
                        'label' => 'Paket Half Day Meeting',
                        'price' => 250000,
                        'minimum' => 10,
                        'type' => 'per_person',
                        'event' => 'meeting',
                        'duration' => 'half_day',
                        'description' => 'IDR 250.000++ / Pax | Minimum 10 Pax',
                    ],
                    [
                        'key' => 'meeting_full_day',
                        'label' => 'Paket Full Day Meeting',
                        'price' => 400000,
                        'minimum' => 10,
                        'type' => 'per_person',
                        'event' => 'meeting',
                        'duration' => 'full_day',
                        'description' => 'IDR 400.000++ / Pax | Minimum 10 Pax',
                    ],
                    [
                        'key' => 'meeting_vip',
                        'label' => 'Paket VIP Meeting',
                        'price' => 600000,
                        'minimum' => 15,
                        'type' => 'per_person',
                        'event' => 'meeting',
                        'duration' => 'vip',
                        'description' => 'IDR 600.000++ / Pax | Minimum 15 Pax',
                    ],
                ],
            ],
            [
                'key' => 'gathering',
                'name' => 'Gathering Package',
                'description' => 'Paket buffet untuk gathering half day dan full day.',
                'image' => 'images/Private Images/Paket-Gathering-2048x934.webp',
                'options' => [
                    [
                        'key' => 'gathering_half_day',
                        'label' => 'Paket Half Day Gathering',
                        'price' => 210000,
                        'minimum' => 50,
                        'type' => 'per_person',
                        'event' => 'gathering',
                        'duration' => 'half_day',
                        'description' => 'Buffet Menu | IDR 210.000++ / Pax | Minimum 50 Pax',
                    ],
                    [
                        'key' => 'gathering_full_day',
                        'label' => 'Paket Full Day Gathering',
                        'price' => 250000,
                        'minimum' => 50,
                        'type' => 'per_person',
                        'event' => 'gathering',
                        'duration' => 'full_day',
                        'description' => 'Buffet Menu | IDR 250.000++ / Pax | Minimum 50 Pax',
                    ],
                ],
            ],
            [
                'key' => 'wedding_engagement',
                'name' => 'Wedding Package',
                'description' => 'Paket intimate untuk wedding dan engagement.',
                'image' => 'images/Private Images/Paket-Wedding-dan-Engagement-1536x701.webp',
                'options' => [
                    [
                        'key' => 'wedding_intimate',
                        'label' => 'Wedding Intimate Package',
                        'price' => 35000000,
                        'minimum' => 100,
                        'type' => 'package',
                        'event' => 'wedding',
                        'duration' => 'full_day',
                        'description' => 'IDR 35 JUTA Nett | Kapasitas 100 Pax',
                    ],
                    [
                        'key' => 'engagement_intimate',
                        'label' => 'Engagement Intimate Package',
                        'price' => 15000000,
                        'minimum' => 50,
                        'type' => 'package',
                        'event' => 'engagement',
                        'duration' => 'full_day',
                        'description' => 'IDR 15 JUTA Nett | Kapasitas 50 Pax',
                    ],
                ],
            ],
        ];
    }

    public static function option(string $key): ?array
    {
        foreach (self::cards() as $card) {
            foreach ($card['options'] as $option) {
                if ($option['key'] === $key) {
                    return $option;
                }
            }
        }

        return null;
    }
}