<?php

namespace Tests\Feature;

use App\Services\PackageCatalogService;
use Tests\TestCase;

class PackageCatalogServiceTest extends TestCase
{
    public function test_it_returns_package_metadata_for_known_packages(): void
    {
        $metadata = PackageCatalogService::forName('Paket Kuliner Keluarga');

        $this->assertSame('Paling Populer', $metadata['badge']);
        $this->assertSame('Fleksibel', $metadata['duration']);
        $this->assertNotEmpty($metadata['features']);
    }

    public function test_it_returns_safe_fallback_image_for_unknown_or_mismatched_names(): void
    {
        $this->assertStringContainsString('Redtail-Catfish', PackageCatalogService::fallbackImageFor('Paket Sport Fishing'));
        $this->assertStringContainsString('Tentang-The-Waterfall-Resto', PackageCatalogService::fallbackImageFor('Unknown Package'));
    }
}
