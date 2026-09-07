<?php

namespace App\Services;

class PackageCatalogService
{
    /**
     * Centralized package metadata to avoid duplicate hardcoded strings across controllers.
     */
    public static function catalog(): array
    {
        return [
            'Paket Kuliner Keluarga' => [
                'duration' => 'Fleksibel',
                'priceOriginal' => 100000,
                'priceDiscount' => 75000,
                'discountPercent' => 25,
                'priceUnit' => '/ ORANG',
                'rating' => 0.0,
                'included' => [
                    'Akses area The Waterfall Resto',
                    'WiFi gratis kecepatan tinggi',
                    'Area parkir luas + Buggy Cart',
                    'Akses Mini Zoo',
                    'Area Bermain Anak',
                    'Musholla bersih',
                    'Fasilitas ramah difabel',
                    'Spot foto instagramable',
                ],
                'features' => [
                    'Menu pilihan Eropa & Nusantara',
                    'Suasana Dine in Nature',
                    'Area bermain anak & Mini Zoo',
                    'WiFi gratis hingga 100 Mbps',
                    'Area parkir luas',
                    'Musholla tersedia',
                ],
                'popular' => true,
                'badge' => 'Paling Populer',
                'heroImage' => asset('images/The Waterfall Resto Images/Tentang-TWF-2048x769.webp'),
                'images' => [
                    asset('images/The Waterfall Resto Images/Tentang-TWF-2048x769.webp'),
                    asset('images/The Waterfall Resto Images/Menu-Western-The-Waterfall-Resto-1536x1150.webp'),
                    asset('images/The Waterfall Resto Images/Seni-Kuliner-dan-Hospitality-1536x1151.webp'),
                    asset('images/The Waterfall Resto Images/Fasilitas-Umum-1536x1151.webp'),
                ],
            ],
            'Paket Sport Fishing' => [
                'duration' => 'Per Jam/Harian',
                'priceOriginal' => 150000,
                'priceDiscount' => 120000,
                'discountPercent' => 20,
                'priceUnit' => '/ JAM',
                'rating' => 0.0,
                'included' => [
                    'Akses kolam Monster Fish',
                    'Briefing teknik mancing',
                    'Pendampingan staff',
                    'Spot foto dengan tangkapan',
                    'Lomba Galatama (akhir bulan)',
                    'Fasilitas toilet & musholla',
                    'Area parkir luas',
                ],
                'features' => [
                    'Kolam pemancingan eksklusif',
                    'Ikan berukuran raksasa',
                    'Peralatan tersedia (optional)',
                    'Spot foto menarik',
                    'Fasilitas lengkap',
                    'Cocok untuk pemula & profesional',
                ],
                'popular' => false,
                'badge' => 'Adrenalin',
                'heroImage' => asset('images/placeholders/Redtail-Catfish-Ikan-Predator-Amerika-Selatan-1536x853.webp'),
                'images' => [
                    asset('images/placeholders/Redtail-Catfish-Ikan-Predator-Amerika-Selatan-1536x853.webp'),
                    asset('images/placeholders/Redtail-Catfish-Ikan-Predator-Amerika-Selatan-1536x853.webp'),
                    asset('images/placeholders/Redtail-Catfish-Ikan-Predator-Amerika-Selatan-1536x853.webp'),
                    asset('images/placeholders/Redtail-Catfish-Ikan-Predator-Amerika-Selatan-1536x853.webp'),
                ],
            ],
            'Paket Private Event' => [
                'duration' => 'Full Day',
                'priceOriginal' => 5000000,
                'priceDiscount' => 4000000,
                'discountPercent' => 20,
                'priceUnit' => '/ ACARA',
                'rating' => 0.0,
                'included' => [
                    'Ruang ber-AC 100 orang',
                    'Meja & kursi premium',
                    'Setup fleksibel (U-Shape/Classroom)',
                    'Sound system & proyektor',
                    'WiFi gratis kecepatan tinggi',
                    'Dekorasi basic',
                    'Tim event profesional',
                    'Area parkir luas',
                    'Dokumentasi foto',
                ],
                'features' => [
                    'Kapasitas hingga 100 orang',
                    'Ruang ber-AC di Lantai 2',
                    'Paket catering tersedia',
                    'Dekorasi sesuai kebutuhan',
                    'Sound system & proyektor',
                    'Tim event profesional',
                ],
                'popular' => false,
                'badge' => 'Eksklusif',
                'heroImage' => asset('images/Private Images/BCA-Gathering-2048x1137.webp'),
                'images' => [
                    asset('images/Private Images/BCA-Gathering-2048x1137.webp'),
                    asset('images/Private Images/BCA-Gathering-2048x1137.webp'),
                    asset('images/Private Images/BCA-Gathering-2048x1137.webp'),
                    asset('images/Private Images/BCA-Gathering-2048x1137.webp'),
                ],
            ],
            'Paket Rekreasi Keluarga' => [
                'duration' => '1 Hari',
                'priceOriginal' => 400000,
                'priceDiscount' => 320000,
                'discountPercent' => 20,
                'priceUnit' => '/ KELUARGA (4 ORANG)',
                'rating' => 0.0,
                'included' => [
                    'Paket makan keluarga (4 orang)',
                    'Akses Mini Zoo seharian',
                    'Fishing 1 jam dengan alat',
                    'Welcome drink',
                    'Foto bersama satwa',
                    'WiFi gratis',
                    'Parkir gratis',
                ],
                'features' => [
                    'Makan di The Waterfall Resto',
                    'Akses Mini Zoo & Kids Area',
                    'Fishing experience (1 jam)',
                    'Foto bersama satwa',
                    'Area bermain anak aman',
                    'Parkir gratis',
                ],
                'popular' => false,
                'badge' => 'Family Package',
                'heroImage' => asset('images/placeholders/asset 4.png'),
                'images' => [
                    asset('images/placeholders/asset 4.png'),
                    asset('images/placeholders/hewan.jpg'),
                    asset('images/placeholders/asset 3.png'),
                    asset('images/placeholders/asset 2.png'),
                ],
            ],
        ];
    }

    public static function forName(?string $packageName): array
    {
        $catalog = self::catalog();

        return $catalog[$packageName] ?? self::default();
    }

    public static function default(): array
    {
        return [
            'duration' => 'Fleksibel',
            'priceOriginal' => 0,
            'priceDiscount' => 0,
            'discountPercent' => 0,
            'priceUnit' => '',
            'rating' => 0.0,
            'included' => [],
            'features' => [],
            'popular' => false,
            'badge' => null,
            'heroImage' => null,
            'images' => [],
        ];
    }

    public static function fallbackImageFor(?string $packageName): string
    {
        $name = strtolower((string) $packageName);

        if (str_contains($name, 'private')) {
            return asset('images/Private Images/BCA-Gathering-2048x1137.webp');
        }

        if (str_contains($name, 'fishing') || str_contains($name, 'sport')) {
            return asset('images/placeholders/Redtail-Catfish-Ikan-Predator-Amerika-Selatan-1536x853.webp');
        }

        if (str_contains($name, 'rekreasi') || str_contains($name, 'keluarga')) {
            return asset('images/placeholders/asset 4.png');
        }

        return asset('images/placeholders/Tentang-The-Waterfall-Resto-by-Godongijo-1-1536x1121.webp');
    }
}
