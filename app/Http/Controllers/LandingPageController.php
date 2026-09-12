<?php

namespace App\Http\Controllers;

use App\Models\PaketWisata;
use App\Services\NavigationService;
use App\Services\BreadcrumbService;
use App\Services\SEOService;
use App\Services\PackageCatalogService;
use App\Support\PackageTypeCatalog;
use App\Support\PrivateRoomPackageCatalog;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    /**
     * Navigation service instance
     */
    protected NavigationService $navigationService;
    
    /**
     * Breadcrumb service instance
     */
    protected BreadcrumbService $breadcrumbService;
    
    /**
     * SEO service instance
     */
    protected SEOService $seoService;
    
    /**
     * Constructor - inject dependencies
     */
    public function __construct(
        NavigationService $navigationService,
        BreadcrumbService $breadcrumbService,
        SEOService $seoService
    ) {
        $this->navigationService = $navigationService;
        $this->breadcrumbService = $breadcrumbService;
        $this->seoService = $seoService;
    }
    
    /**
     * Display The Waterfall landing page.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $this->ensureCorePackagesExist();

        // Ambil data paket dari database
        $pakets_db = PaketWisata::where('is_active', true)->get();
        
        // Merge data database dengan data static (badge, features, dll)
        $packages = $this->mergePaketsWithStaticData($pakets_db);
        $packages = $this->replacePrivateRoomLandingCards($packages, $pakets_db);
        
        $pageData = [
            'seoData' => $this->getSEOMetadata(),
            'heroImages' => $this->getHeroImages(),
            'heroDestinationCards' => $this->getHeroDestinationCards(),
            'navigation' => $this->navigationService->getMainNavigation(),
            'cta' => $this->navigationService->getCTA(),
            'destinations' => $this->getDestinations(),
            'packages' => $packages, // Data gabungan database + static
            'pakets_db' => $pakets_db, // Tetap kirim untuk booking modal
            'testimonials' => $this->getTestimonials(),
            'statistics' => $this->getStatistics(),
            'sustainability' => $this->getSustainabilityFeatures(),
        ];

        return view('landing.index', $pageData);
    }

    private function ensureCorePackagesExist(): void
    {
        $corePackages = [
            [
                'nama_paket' => 'Paket Kuliner Keluarga',
                'jenis_paket' => 'The Waterfall Resto',
                'deskripsi' => 'Nikmati pengalaman kuliner ekologis di The Waterfall Resto dengan menu Eropa dan Nusantara di tengah suasana air terjun mini yang asri.',
                'harga' => 75000,
                'kuota' => 100,
                'foto' => 'images/placeholders/asset 3.webp',
            ],
            [
                'nama_paket' => 'Paket Sport Fishing',
                'jenis_paket' => 'Fishing Lake',
                'deskripsi' => 'Tantangan memancing ikan raksasa di Monster Fish Fishing Lake dengan pengalaman sport fishing yang tak terlupakan.',
                'harga' => 0,
                'kuota' => 50,
                'foto' => 'images/placeholders/Redtail-Catfish-Ikan-Predator-Amerika-Selatan-1536x853.webp',
            ],
            [
                'nama_paket' => 'Paket Rekreasi Keluarga',
                'jenis_paket' => 'The Waterfall Resto',
                'deskripsi' => 'Paket lengkap untuk liburan keluarga: makan di The Waterfall Resto, Mini Zoo, Kids Area, dan fishing experience.',
                'harga' => 150000,
                'kuota' => 80,
                'foto' => 'images/placeholders/hewan.jpg',
            ],
        ];

        foreach ($corePackages as $package) {
            $existing = PaketWisata::where('nama_paket', $package['nama_paket'])->first();

            if ($existing) {
                $updates = [];

                if (! $existing->is_active) {
                    $updates['is_active'] = true;
                }

                foreach (['jenis_paket', 'deskripsi', 'harga', 'kuota', 'foto'] as $field) {
                    if (empty($existing->{$field}) && ! empty($package[$field])) {
                        $updates[$field] = $package[$field];
                    }
                }

                if ($existing->jenis_paket === null || $existing->jenis_paket === '') {
                    $updates['jenis_paket'] = $package['jenis_paket'];
                }

                if ($existing->foto === null || $existing->foto === '') {
                    $updates['foto'] = $package['foto'];
                }

                if (! empty($updates)) {
                    $existing->update($updates);
                }

                continue;
            }

            PaketWisata::create($package + ['is_active' => true]);
        }
    }

    /**
     * Merge database paket data with static features and badges.
     *
     * @param \Illuminate\Database\Eloquent\Collection $pakets_db
     * @return array
     */
    private function mergePaketsWithStaticData($pakets_db): array
    {
        $staticData = PackageCatalogService::catalog();

        $packages = [];

        foreach ($pakets_db as $paket) {
            $static = $staticData[$paket->nama_paket] ?? [
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

            $packagePhoto = $this->resolvePackageImage($paket->foto, $paket->nama_paket, $static['heroImage'] ?? null);
            $packageImages = $this->resolvePackageImages($paket, $static['images'] ?? []);

            if ($paket->harga > 0) {
                $price = 'Mulai Rp ' . number_format($paket->harga, 0, ',', '.');
                $pricePerPerson = '/ orang';
            } else {
                $price = 'Hubungi Kami';
                $pricePerPerson = '';
            }

            $packages[] = [
                'id' => $paket->id,
                'name' => $paket->nama_paket,
                'jenis_paket' => $paket->jenis_paket,
                'priceValue' => (float) $paket->final_price,
                'duration' => $static['duration'],
                'price' => $price,
                'pricePerPerson' => $pricePerPerson,
                'priceOriginal' => $static['priceOriginal'],
                'priceDiscount' => $static['priceDiscount'],
                'discountPercent' => $static['discountPercent'],
                'priceUnit' => $static['priceUnit'],
                'rating' => $static['rating'],
                'included' => $static['included'],
                'description' => $paket->deskripsi,
                'features' => $static['features'],
                'image' => $packagePhoto,
                'heroImage' => $packagePhoto,
                'images' => $packageImages,
                'alt' => 'Paket ' . $paket->nama_paket,
                'popular' => $static['popular'],
                'badge' => $static['badge'],
            ];
        }

        return $packages;
    }

    private function replacePrivateRoomLandingCards(array $packages, $pakets_db): array
    {
        $privateRoomTargets = $pakets_db->where('jenis_paket', 'Private Room');

        if ($privateRoomTargets->isEmpty()) {
            return $packages ?: $this->defaultLandingPackageFallback();
        }

        $privateRoomCards = collect(PrivateRoomPackageCatalog::cards())->map(function (array $card) use ($privateRoomTargets) {
            $privateRoomTarget = $privateRoomTargets->firstWhere('nama_paket', $card['name'])
                ?? $privateRoomTargets->first();

            return [
                'id' => $privateRoomTarget->id,
                'name' => $card['name'],
                'jenis_paket' => 'Private Room',
                'duration' => 'Pilih durasi di form',
                'price' => 'Pilih paket',
                'pricePerPerson' => '',
                'priceOriginal' => 0,
                'priceDiscount' => 0,
                'discountPercent' => 0,
                'priceUnit' => '',
                'rating' => 0.0,
                'included' => [],
                'description' => $card['description'],
                'features' => [],
                'image' => asset($card['image']),
                'heroImage' => asset($card['image']),
                'images' => [asset($card['image'])],
                'alt' => $card['name'],
                'popular' => false,
                'badge' => null,
                'privateRoomOptions' => $card['options'],
            ];
        })->all();

        $filteredPackages = array_values(array_filter(
            $packages,
            static fn (array $package) => $package['jenis_paket'] !== 'Private Room'
        ));

        $mergedPackages = array_values(array_merge($filteredPackages, $privateRoomCards));

        return $mergedPackages ?: $this->defaultLandingPackageFallback();
    }

    private function defaultLandingPackageFallback(): array
    {
        return [
            [
                'id' => null,
                'name' => 'Paket Kuliner Keluarga',
                'jenis_paket' => 'The Waterfall Resto',
                'duration' => 'Fleksibel',
                'price' => 'Mulai Rp 75.000',
                'pricePerPerson' => '/ orang',
                'priceOriginal' => 100000,
                'priceDiscount' => 75000,
                'discountPercent' => 25,
                'priceUnit' => '/ ORANG',
                'rating' => 0.0,
                'included' => [
                    'Akses area The Waterfall Resto',
                    'WiFi gratis kecepatan tinggi',
                    'Area parkir luas + Buggy Cart',
                ],
                'description' => 'Nikmati pengalaman kuliner ekologis di The Waterfall Resto dengan menu Eropa dan Nusantara di tengah suasana air terjun mini yang asri.',
                'features' => [
                    'Menu pilihan Eropa & Nusantara',
                    'Suasana Dine in Nature',
                ],
                'image' => asset('images/The Waterfall Resto Images/Tentang-TWF-2048x769.webp'),
                'heroImage' => asset('images/The Waterfall Resto Images/Tentang-TWF-2048x769.webp'),
                'images' => [asset('images/The Waterfall Resto Images/Tentang-TWF-2048x769.webp')],
                'alt' => 'Paket Kuliner Keluarga',
                'popular' => true,
                'badge' => 'Paling Populer',
            ],
            [
                'id' => null,
                'name' => 'Paket Sport Fishing',
                'jenis_paket' => 'Fishing Lake',
                'duration' => 'Per Jam/Harian',
                'price' => 'Hubungi Kami',
                'pricePerPerson' => '',
                'priceOriginal' => 150000,
                'priceDiscount' => 120000,
                'discountPercent' => 20,
                'priceUnit' => '/ JAM',
                'rating' => 0.0,
                'included' => [
                    'Akses kolam Monster Fish',
                    'Briefing teknik mancing',
                ],
                'description' => 'Tantangan memancing ikan raksasa di Monster Fish Fishing Lake dengan pengalaman sport fishing yang tak terlupakan.',
                'features' => [
                    'Kolam pemancingan eksklusif',
                    'Ikan berukuran raksasa',
                ],
                'image' => asset('images/placeholders/Redtail-Catfish-Ikan-Predator-Amerika-Selatan-1536x853.webp'),
                'heroImage' => asset('images/placeholders/Redtail-Catfish-Ikan-Predator-Amerika-Selatan-1536x853.webp'),
                'images' => [asset('images/placeholders/Redtail-Catfish-Ikan-Predator-Amerika-Selatan-1536x853.webp')],
                'alt' => 'Paket Sport Fishing',
                'popular' => false,
                'badge' => 'Adrenalin',
            ],
        ];
    }

    private function resolvePackageImages($paket, array $staticImages = []): array
    {
        if (!empty($paket->foto) && $this->isValidAssetPath($paket->foto)) {
            return [$this->normalizeAssetUrl($paket->foto)];
        }

        if (!empty($staticImages)) {
            return $staticImages;
        }

        return [PackageCatalogService::fallbackImageFor($paket->nama_paket ?? '')];
    }

    private function resolvePackageImage(?string $foto, ?string $packageName = null, $defaultImage = null): ?string
    {
        $candidates = [];

        if ($foto) {
            $candidates[] = $foto;
        }

        if ($defaultImage) {
            $candidates[] = $defaultImage;
        }

        foreach ($candidates as $candidate) {
            if ($this->isValidAssetPath($candidate)) {
                return $this->normalizeAssetUrl($candidate);
            }
        }

        return PackageCatalogService::fallbackImageFor($packageName ?? '');
    }

    private function isValidAssetPath($path): bool
    {
        if (empty($path)) {
            return false;
        }

        if (is_string($path) && (str_starts_with($path, 'http://') || str_starts_with($path, 'https://'))) {
            return true;
        }

        $normalized = ltrim((string) $path, '/');

        if ($normalized === '') {
            return false;
        }

        return file_exists(public_path($normalized));
    }

    private function normalizeAssetUrl($path): string
    {
        if (empty($path)) {
            return '';
        }

        if (is_string($path) && (str_starts_with($path, 'http://') || str_starts_with($path, 'https://'))) {
            return $path;
        }

        return asset((string) $path);
    }

    /**
     * Get hero bento grid images.
     *
     * @return array
     */
    private function getHeroImages(): array
    {
        return [
            [
                'src' => asset('images/placeholders/asset 5.png'),
                'alt' => 'Keajaiban alam waterfall',
                'class' => 'bento-1',
            ],
            [
                'src' => asset('images/placeholders/asset 2.png'),
                'alt' => 'Waterfall Gedong Ijo - Surga tersembunyi',
                'class' => 'bento-2',
            ],
            [
                'src' => asset('images/placeholders/asset 3.png'),
                'alt' => 'Pemandangan air terjun yang eksotis',
                'class' => 'bento-3',
            ],
            [
                'src' => asset('images/placeholders/asset 4.png'),
                'alt' => 'Wisata alam air terjun',
                'class' => 'bento-4',
            ],
            [
                'src' => asset('images/placeholders/asset 5.png'),
                'alt' => 'Keajaiban alam waterfall',
                'class' => 'bento-5',
            ],
            [
                'src' => asset('images/placeholders/asset 3.png'),
                'alt' => 'Destinasi wisata air terjun',
                'class' => 'bento-6',
            ],
            [
                'src' => asset('images/placeholders/asset 6.png'),
                'alt' => 'Pengalaman wisata waterfall yang tak terlupakan',
                'class' => 'bento-7',
            ],
            [
                'src' => asset('images/placeholders/asset 1.jpg'),
                'alt' => 'Godong Ijo Ecotainment',
                'class' => 'bento-8',
            ],
        ];
    }

    /**
     * Get hero destination cards for modern hero section.
     *
     * @return array
     */
    private function getHeroDestinationCards(): array
    {
        return [
            [
                'images' => [asset('images/placeholders/asset 3.png')],
                'title' => 'The Waterfall Resto',
                'description' => 'Kuliner premium dengan konsep Dine in Nature di tengah air terjun mini yang asri dan menenangkan.',
                'layout' => 'single',
            ],
            [
                'images' => [
                    asset('images/placeholders/Tentang-The-Waterfall-Resto-by-Godongijo-1-1536x1121.webp'),
                    asset('images/The Waterfall Resto Images/Menu-Western-The-Waterfall-Resto-1536x1150.webp'),
                    asset('images/The Waterfall Resto Images/Seni-Kuliner-dan-Hospitality-1536x1151.webp'),
                    asset('images/The Waterfall Resto Images/Fasilitas-Umum-1536x1151.webp'),
                ],
                'title' => 'Monster Fish Lake',
                'description' => 'Tantangan seru memancing ikan monster dengan fasilitas lengkap dan pemandangan indah yang memukau.',
                'layout' => 'grid',
            ],
            [
                'images' => [asset('images/placeholders/asset 6.png')],
                'title' => 'Wisata Edukasi',
                'description' => 'Program edukasi interaktif untuk sekolah dengan berbagai aktivitas menarik dan edukatif untuk anak-anak.',
                'layout' => 'single',
            ],
        ];
    }



    /**
     * Get featured destinations data.
     *
     * @return array
     */
    private function getDestinations(): array
    {
        // Get first package for each jenis_paket
        $restoPackage = PaketWisata::where('jenis_paket', PackageTypeCatalog::normalize('the_waterfall_resto'))->where('is_active', true)->first();
        $fishingPackage = PaketWisata::where('jenis_paket', PackageTypeCatalog::normalize('fishing_lake'))->where('is_active', true)->first();
        $privatePackage = PaketWisata::where('jenis_paket', PackageTypeCatalog::normalize('private_room'))->where('is_active', true)->first();
        
        return [
            [
                'name' => 'The Waterfall Resto',
                'location' => 'Dine in Nature',
                'description' => 'Kuliner premium dengan konsep Dine in Nature di tengah suasana air terjun mini.',
                'tag' => 'Culinary',
                'image' => asset('images/placeholders/Tentang-The-Waterfall-Resto-by-Godongijo-1-1536x1121.webp'),
                'alt' => 'The Waterfall Resto dengan konsep Dine in Nature',
                'package_id' => $restoPackage?->id,
                'price' => $restoPackage?->harga ?? 0,
                'explore_link' => route('packages.category', ['category' => 'the-waterfall-resto']),
            ],
            [
                'name' => 'Monster Fish Fishing Lake',
                'location' => 'Sport Fishing',
                'description' => 'Sport fishing eksklusif dengan tantangan ikan-ikan berukuran raksasa.',
                'tag' => 'Recreation',
                'image' => asset('images/placeholders/Redtail-Catfish-Ikan-Predator-Amerika-Selatan-1536x853.webp'),
                'alt' => 'Monster Fish Fishing Lake untuk tantangan memancing',
                'package_id' => $fishingPackage?->id,
                'price' => $fishingPackage?->harga ?? 0,
                'explore_link' => route('packages.category', ['category' => 'fishing-lake']),
            ],
            [
                'name' => 'Private Room',
                'location' => 'Event Space',
                'description' => 'Ruang privat elegan untuk meeting, gathering, dan acara spesial hingga 100 orang.',
                'tag' => 'Events',
                'image' => asset('images/placeholders/asset 3.png'),
                'alt' => 'Private Room untuk acara spesial',
                'package_id' => $privatePackage?->id,
                'price' => $privatePackage?->harga ?? 0,
                'explore_link' => route('packages.category', ['category' => 'private-room']),
            ],
            [
                'name' => 'Mini Zoo & Kids Area',
                'location' => 'Family Friendly',
                'description' => 'Area bermain dan Mini Zoo edukatif untuk pengalaman keluarga yang menyenangkan.',
                'tag' => 'Family',
                'image' => asset('images/placeholders/hewan.jpg'),
                'alt' => 'Mini Zoo dan Kids Area untuk rekreasi keluarga',
                'package_id' => null, // Mini Zoo tidak memiliki paket booking terpisah
                'price' => 0,
                'explore_link' => '#contact',
            ],
        ];
    }

    /**
     * Get tour packages data.
     *
     * @return array
     */
    private function getTourPackages(): array
    {
        return [
            [
                'name' => 'Paket Kuliner Keluarga',
                'duration' => 'Fleksibel',
                'price' => 'Mulai Rp 75.000',
                'pricePerPerson' => '/ orang',
                'description' => 'Nikmati pengalaman kuliner ekologis di The Waterfall Resto dengan menu Eropa dan Nusantara di tengah suasana air terjun mini yang asri.',
                'features' => [
                    'Menu pilihan Eropa & Nusantara',
                    'Suasana Dine in Nature',
                    'Area bermain anak & Mini Zoo',
                    'WiFi gratis hingga 100 Mbps',
                    'Area parkir luas',
                    'Musholla tersedia',
                ],
                'image' => asset('images/placeholders/asset 1.jpg'),
                'alt' => 'Paket kuliner keluarga di The Waterfall Resto',
                'popular' => true,
                'badge' => 'Paling Populer',
            ],
            [
                'name' => 'Paket Sport Fishing',
                'duration' => 'Per Jam/Harian',
                'price' => 'Hubungi Kami',
                'pricePerPerson' => '',
                'description' => 'Tantangan memancing ikan raksasa di Monster Fish Fishing Lake. Pengalaman sport fishing yang tak terlupakan untuk pecinta mancing.',
                'features' => [
                    'Kolam pemancingan eksklusif',
                    'Ikan berukuran raksasa',
                    'Peralatan tersedia (optional)',
                    'Spot foto menarik',
                    'Fasilitas lengkap',
                    'Cocok untuk pemula & profesional',
                ],
                'image' => asset('images/placeholders/asset 2.png'),
                'alt' => 'Paket sport fishing di Monster Fish Lake',
                'popular' => false,
                'badge' => 'Adrenalin',
            ],
            [
                'name' => 'Paket Private Event',
                'duration' => 'Full Day',
                'price' => 'Custom',
                'pricePerPerson' => '',
                'description' => 'Ruang privat elegan di Lantai 2 untuk acara spesial Anda. Ideal untuk seminar, gathering, arisan, meeting, hingga pesta pernikahan.',
                'features' => [
                    'Kapasitas hingga 100 orang',
                    'Ruang ber-AC di Lantai 2',
                    'Paket catering tersedia',
                    'Dekorasi sesuai kebutuhan',
                    'Sound system & proyektor',
                    'Tim event profesional',
                ],
                'image' => asset('images/placeholders/asset 3.png'),
                'alt' => 'Paket private event untuk acara spesial',
                'popular' => false,
                'badge' => 'Eksklusif',
            ],
            [
                'name' => 'Paket Rekreasi Keluarga',
                'duration' => '1 Hari',
                'price' => 'Paket Hemat',
                'pricePerPerson' => '',
                'description' => 'Paket lengkap untuk liburan keluarga: makan di resto, bermain di Mini Zoo, dan fishing. Satu destinasi untuk semua kebutuhan rekreasi.',
                'features' => [
                    'Makan di The Waterfall Resto',
                    'Akses Mini Zoo & Kids Area',
                    'Fishing experience (1 jam)',
                    'Foto bersama satwa',
                    'Area bermain anak aman',
                    'Parkir gratis',
                ],
                'image' => asset('images/placeholders/asset 4.png'),
                'alt' => 'Paket rekreasi keluarga lengkap',
                'popular' => false,
                'badge' => 'Family Package',
            ],
        ];
    }

    /**
     * Get customer testimonials data.
     *
     * @return array
     */
    private function getTestimonials(): array
    {
        return [
            [
                'quote' => 'Tempat makan favorit keluarga! Suasana air terjun mini-nya bikin makan jadi lebih nikmat. Anak-anak senang bisa main di Mini Zoo sambil tunggu pesanan. Recommended!',
                'name' => 'Keluarga Budi Santoso',
                'role' => 'Pelanggan Setia, Jakarta Selatan',
                'avatar' => '🌺',
                'rating' => 5,
            ],
            [
                'quote' => 'The Waterfall Resto memang beda! Konsep Dine in Nature-nya bikin rileks. Menu Eropa dan Nusantara-nya enak semua. WiFi kencang juga, cocok buat kerja sambil makan.',
                'name' => 'Rina Kusuma',
                'role' => 'Food Blogger, Depok',
                'avatar' => '🍽️',
                'rating' => 5,
            ],
            [
                'quote' => 'Pertama kali mancing ikan sebesar ini! Monster Fish-nya bener-bener gede dan kuat. Challenge banget tapi seru. Tempatnya bersih dan fasilitasnya lengkap.',
                'name' => 'Andi Wijaya',
                'role' => 'Penggemar Mancing, Tangerang',
                'avatar' => '🎣',
                'rating' => 5,
            ],
            [
                'quote' => 'Sempurna untuk acara gathering kantor. Private room-nya luas, ber-AC, dan pelayanannya profesional. Tim kami puas dengan catering dan suasananya.',
                'name' => 'PT. Maju Bersama',
                'role' => 'Corporate Event, Jakarta',
                'avatar' => '🏢',
                'rating' => 5,
            ],
            [
                'quote' => 'Satu tempat untuk semua! Ayah bisa mancing, ibu dan anak makan di resto, anak-anak main di Mini Zoo. Parkir luas, harga terjangkau. Pasti balik lagi!',
                'name' => 'Keluarga Permata',
                'role' => 'Keluarga Wisatawan, Bekasi',
                'avatar' => '👨‍👩‍👧‍👦',
                'rating' => 5,
            ],
            [
                'quote' => 'Lokasi strategis, hanya 15 menit dari pintu tol. Suasana asri banget di tengah kota. Cocok untuk weekend escape yang tidak jauh dari rumah.',
                'name' => 'Dimas Prasetyo',
                'role' => 'Karyawan Swasta, Cinere',
                'avatar' => '🚗',
                'rating' => 5,
            ],
        ];
    }

    /**
     * Get statistics data.
     *
     * @return array
     */
    private function getStatistics(): array
    {
        return [
            [
                'number' => 100,
                'suffix' => '',
                'label' => 'Kapasitas Event',
            ],
            [
                'number' => 100,
                'suffix' => ' Mbps',
                'label' => 'WiFi Gratis',
            ],
            [
                'number' => 4.8,
                'suffix' => '/5',
                'label' => 'Rating Pelanggan',
            ],
        ];
    }

    /**
     * Get sustainability features.
     *
     * @return array
     */
    private function getSustainabilityFeatures(): array
    {
        return [
            [
                'icon' => '🌿',
                'title' => 'Konsep Ekologis',
                'description' => 'Destinasi kuliner dan rekreasi paling ekologis di Indonesia dengan desain contemporary yang menyatu dengan alam.',
            ],
            [
                'icon' => '💚',
                'title' => 'Lingkungan Asri',
                'description' => 'Dikelilingi pepohonan rindang dan air terjun mini yang menciptakan suasana sejuk dan menenangkan.',
            ],
            [
                'icon' => '🦜',
                'title' => 'Mini Zoo Edukatif',
                'description' => 'Area Mini Zoo yang edukatif untuk mengenalkan anak-anak pada satwa dan pentingnya menjaga kelestarian alam.',
            ],
            [
                'icon' => '🌱',
                'title' => 'Fasilitas Ramah Keluarga',
                'description' => 'Lengkap dengan musholla, area parkir luas, WiFi gratis, dan fasilitas yang memastikan kenyamanan seluruh keluarga.',
            ],
        ];
    }

    /**
     * Get comprehensive SEO metadata for the landing page.
     *
     * @return array
     */
    private function getSEOMetadata(): array
    {
        return [
            'title' => 'Godong Ijo — The Waterfall Resto & Monster Fish Fishing Lake | Depok',
            'description' => 'Destinasi kuliner & rekreasi paling ekologis di Indonesia. The Waterfall Resto dengan konsep Dine in Nature, Monster Fish Fishing Lake, Private Room, dan Mini Zoo di Depok, Jawa Barat.',
            'keywords' => [
                'Godong Ijo',
                'The Waterfall Resto',
                'Monster Fish Fishing Lake',
                'restoran Depok',
                'tempat mancing Depok',
                'private room event',
                'mini zoo Depok',
                'restoran air terjun',
                'dine in nature',
                'tempat makan keluarga Depok',
            ],
            'og' => [
                'title' => 'Godong Ijo — The Waterfall Resto & Monster Fish Fishing Lake',
                'description' => 'Destinasi kuliner dan rekreasi paling ekologis di Indonesia dengan konsep Dine in Nature. Nikmati hidangan premium, sport fishing, dan fasilitas event lengkap di Depok.',
                'image' => asset('images/og-image.svg'),
                'url' => url('/'),
                'type' => 'website',
            ],
            'structuredData' => [
                '@context' => 'https://schema.org',
                '@type' => 'Restaurant',
                'name' => 'Godong Ijo - The Waterfall Resto',
                'description' => 'Destinasi kuliner paling ekologis di Indonesia dengan konsep Dine in Nature, dilengkapi Monster Fish Fishing Lake, Private Room, dan Mini Zoo.',
                'image' => [
                    asset('images/placeholders/asset 1.jpg'),
                    asset('images/placeholders/asset 2.png'),
                    asset('images/placeholders/asset 3.png'),
                ],
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => 'Jalan Cinangka Raya Km 10 No. 60',
                    'addressLocality' => 'Serua, Bojongsari',
                    'addressRegion' => 'Jawa Barat',
                    'postalCode' => '16517',
                    'addressCountry' => 'ID',
                ],
                'telephone' => '+622174710678',
                'openingHours' => 'Mo-Su 09:00-21:00',
            ],
        ];
    }
}
