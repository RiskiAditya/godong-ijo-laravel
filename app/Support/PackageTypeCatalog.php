<?php

namespace App\Support;

class PackageTypeCatalog
{
    /**
     * Canonical values used across the app.
     */
    public const CANONICAL = [
        'the_waterfall_resto' => 'The Waterfall Resto',
        'fishing_lake' => 'Fishing Lake',
        'private_room' => 'Private Room',
    ];

    /**
     * Accepted aliases from routes, forms, and old values.
     */
    public const ALIASES = [
        'the waterfall resto' => 'the_waterfall_resto',
        'the waterfall' => 'the_waterfall_resto',
        'monster fish fishing lake' => 'fishing_lake',
        'fishing lake' => 'fishing_lake',
        'private room' => 'private_room',
        'the-waterfall-resto' => 'the_waterfall_resto',
        'private-room' => 'private_room',
        'fishing-lake' => 'fishing_lake',
    ];

    public static function normalize(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return $trimmed;
        }

        $normalized = strtolower($trimmed);
        $normalized = str_replace(['-', '_'], ' ', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        $normalized = trim($normalized);

        if (isset(self::CANONICAL[$normalized])) {
            return self::CANONICAL[$normalized];
        }

        if (isset(self::ALIASES[$normalized])) {
            return self::CANONICAL[self::ALIASES[$normalized]];
        }

        return trim($value);
    }

    public static function normalizeInternal(string $value): ?string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }

        $normalized = strtolower($trimmed);
        $normalized = str_replace(['-', '_'], ' ', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        $normalized = trim($normalized);

        if (isset(self::CANONICAL[$normalized])) {
            return $normalized;
        }

        if (isset(self::ALIASES[$normalized])) {
            return self::ALIASES[$normalized];
        }

        return null;
    }

    public static function isCanonical(string $value): bool
    {
        return in_array($value, array_values(self::CANONICAL), true);
    }

    public static function routeSlug(string $value): string
    {
        $canonical = self::normalize($value);

        return match ($canonical) {
            'The Waterfall Resto' => 'the-waterfall-resto',
            'Fishing Lake' => 'fishing-lake',
            'Private Room' => 'private-room',
            default => strtolower(str_replace(' ', '-', $canonical)),
        };
    }
}
