<?php

namespace Tests\Feature;

use App\Models\PaketWisata;
use App\Services\SEOService;
use App\Support\PackageTypeCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackagePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_package_page_loads_for_valid_slug(): void
    {
        $package = PaketWisata::create([
            'nama_paket' => 'Paket Kuliner Keluarga',
            'jenis_paket' => 'The Waterfall Resto',
            'deskripsi' => 'Paket test untuk halaman detail paket.',
            'harga' => 75000,
            'kuota' => 50,
            'is_active' => true,
            'foto' => 'images/placeholders/asset 3 (3).jpg',
        ]);

        $response = $this->get(route('package.show', ['slug' => $package->slug]));

        $response->assertStatus(200);
        $response->assertViewIs('pages.package');
    }

    public function test_homepage_uses_only_uploaded_photo_for_package_card(): void
    {
        PaketWisata::create([
            'nama_paket' => 'Paket Kuliner Keluarga',
            'jenis_paket' => 'The Waterfall Resto',
            'deskripsi' => 'Paket test untuk foto homepage.',
            'harga' => 75000,
            'kuota' => 50,
            'is_active' => true,
            'foto' => 'images/The Waterfall Resto Images/Tentang-TWF-2048x769.webp',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('packages', function ($packages): bool {
            $package = collect($packages)->firstWhere('name', 'Paket Kuliner Keluarga');

            return $package !== null
                && count($package['images']) === 1
                && $package['images'][0] === asset('images/The Waterfall Resto Images/Tentang-TWF-2048x769.webp');
        });
    }

    public function test_package_slug_updates_when_name_changes(): void
    {
        $package = PaketWisata::create([
            'nama_paket' => 'Paket Kuliner Keluarga',
            'jenis_paket' => 'The Waterfall Resto',
            'deskripsi' => 'Paket test untuk slug update.',
            'harga' => 75000,
            'kuota' => 50,
            'is_active' => true,
            'foto' => 'images/placeholders/asset 3 (3).jpg',
        ]);

        $this->assertSame('paket-kuliner-keluarga', $package->slug);

        $package->update(['nama_paket' => 'Paket Kuliner Keluarga Baru']);
        $package->refresh();

        $this->assertSame('paket-kuliner-keluarga-baru', $package->slug);
    }

    public function test_category_cache_is_cleared_when_package_type_changes(): void
    {
        $package = PaketWisata::create([
            'nama_paket' => 'Paket Kuliner Keluarga',
            'jenis_paket' => 'The Waterfall Resto',
            'deskripsi' => 'Paket test untuk cache invalidation.',
            'harga' => 75000,
            'kuota' => 50,
            'is_active' => true,
            'foto' => 'images/placeholders/asset 3 (3).jpg',
        ]);

        $oldKey = 'packages.category.' . md5('The Waterfall Resto');
        $newKey = 'packages.category.' . md5('Private Room');

        \Cache::put($oldKey, collect([['id' => 99, 'nama_paket' => 'Cached stale old data']]), now()->addMinutes(5));
        \Cache::put($newKey, collect([['id' => 98, 'nama_paket' => 'Cached stale new data']]), now()->addMinutes(5));

        $package->update(['jenis_paket' => 'Private Room']);

        $this->assertNull(\Cache::get($oldKey));
        $this->assertNull(\Cache::get($newKey));
    }

    public function test_package_type_catalog_normalizes_all_values_to_canonical_names(): void
    {
        $this->assertSame('The Waterfall Resto', PackageTypeCatalog::normalize('the_waterfall_resto'));
        $this->assertSame('The Waterfall Resto', PackageTypeCatalog::normalize('the-waterfall-resto'));
        $this->assertSame('Fishing Lake', PackageTypeCatalog::normalize('fishing_lake'));
        $this->assertSame('Private Room', PackageTypeCatalog::normalize('private_room'));
        $this->assertSame('The Waterfall Resto', PackageTypeCatalog::normalize('The Waterfall Resto'));
    }

    public function test_private_room_booking_modal_has_mobile_responsive_css(): void
    {
        $html = view('components.private-room-booking-modal')->render();

        $this->assertStringContainsString('@media (max-width: 768px)', $html);
        $this->assertStringContainsString('.private-room-grid { grid-template-columns: 1fr; }', $html);
    }

    public function test_seo_canonical_url_uses_correct_route_parameter_name_for_category_pages(): void
    {
        $seo = new SEOService();

        $this->assertSame(
            url('/paket/the-waterfall-resto'),
            $seo->getCanonicalUrl('packages.category', 'the-waterfall-resto')
        );
    }
}
