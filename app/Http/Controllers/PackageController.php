<?php

namespace App\Http\Controllers;

use App\Models\PaketWisata;
use App\Services\NavigationService;
use App\Services\BreadcrumbService;
use App\Services\SEOService;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PackageController extends Controller
{
    /**
     * Navigation service instance
     *
     * @var NavigationService
     */
    private NavigationService $navigationService;
    
    /**
     * Breadcrumb service instance
     *
     * @var BreadcrumbService
     */
    private BreadcrumbService $breadcrumbService;
    
    /**
     * SEO service instance
     *
     * @var SEOService
     */
    private SEOService $seoService;
    
    /**
     * Constructor
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
     * Display package page
     *
     * @param string $slug Package slug
     * @return View
     */
    public function show(string $slug): View
    {
        // Get package from database by slug
        $package = $this->getPackageBySlug($slug);
        
        // Handle missing package gracefully
        if (!$package) {
            abort(404, 'Paket wisata tidak ditemukan.');
        }
        
        // Get static data for this package (duration, features, itinerary, etc.)
        $staticData = $this->getPackageStaticData($package->nama_paket);
        
        // Format package data for view
        $packageData = $this->formatPackageForView($package, $staticData);
        
        // Generate breadcrumbs
        $breadcrumbs = $this->breadcrumbService->generate(
            'package.show',
            $slug,
            ['name' => $package->nama_paket]
        );
        
        // Generate SEO metadata
        $seoData = $this->seoService->generateMetadata('package', [
            'name' => $package->nama_paket,
            'description' => $package->deskripsi,
            'image' => asset($package->foto),
            'price' => $package->harga,
            'url' => route('package.show', ['slug' => $slug]),
            'route' => 'package.show',
            'slug' => $slug,
            'breadcrumbs' => $breadcrumbs,
        ]);
        
        // Get navigation data
        $navigation = $this->navigationService->getMainNavigation();
        $cta = $this->navigationService->getCTA();
        
        // Prepare view data
        $viewData = [
            'package' => $packageData,
            'navigation' => $navigation,
            'cta' => $cta,
            'breadcrumbs' => $breadcrumbs,
            'seoData' => $seoData,
            'currentRoute' => 'package.show',
        ];
        
        return view('pages.package', $viewData);
    }
    
    /**
     * Get package by slug from database
     *
     * @param string $slug Package slug
     * @return PaketWisata|null Package model or null if not found
     */
    private function getPackageBySlug(string $slug): ?PaketWisata
    {
        try {
            return PaketWisata::where('is_active', true)
                ->where('slug', $slug)
                ->first();
        } catch (\Exception $e) {
            \Log::error('PackageController: Failed to fetch package by slug', [
                'slug' => $slug,
                'error' => $e->getMessage(),
            ]);
            
            return null;
        }
    }
    
    /**
     * Get static data for a package (duration, features, itinerary, etc.)
     *
     * @param string $packageName Package name
     * @return array Static package data
     */
    private function getPackageStaticData(string $packageName): array
    {
        // Static configuration mapping package names to additional data
        $staticData = [
            'Paket Kuliner Keluarga' => [
                'tagline' => 'Destinasi Kuliner Ekologis dengan Gastronomi Premium',
                'duration' => 'Fleksibel',
                'hero_image' => asset('images/The Waterfall Resto Images/Tentang-TWF-2048x769.webp'),
                'description_short' => 'Selamat datang di The Waterfall Resto by Godongijo, destinasi kuliner yang bukan sekadar tempat makan, melainkan perwujudan visi kami sebagai destinasi kuliner ekologis di Indonesia dengan gastronomi premium dan pelayanan prima.',
                'features' => [
                    'Menu Eropa & Nusantara Premium',
                    'Head Chef berpengalaman 20+ tahun',
                    'Konsep Elegan Kontemporer terintegrasi Alam',
                    'Bahan Baku Premium & Segar',
                    'Area 2,9 Hektar dengan Air Terjun',
                    'WiFi Gratis Kecepatan Tinggi',
                    'Area Bermain Anak & Mini Zoo',
                    'Ramah Difabel dengan Buggy Cart',
                ],
                'itinerary' => [
                    [
                        'time' => 'Sesampainya',
                        'title' => 'Welcome & Seating',
                        'description' => 'Tim hospitality berpengalaman 15+ tahun menyambut dan mengantar ke area makan dengan pemandangan air terjun dan vegetasi subur',
                    ],
                    [
                        'time' => 'Selama Makan',
                        'title' => 'Dining Experience',
                        'description' => 'Nikmati menu a la carte, buffet, atau tasting menu yang diolah dengan metode kuliner profesional dan peralatan dapur modern',
                    ],
                    [
                        'time' => 'Setelah Makan',
                        'title' => 'Free Exploration',
                        'description' => 'Jelajahi Mini Zoo, area bermain anak, spot foto instagramable, dan nikmati suasana alam hijau yang menenangkan',
                    ],
                ],
                'included' => [
                    'Akses area restoran dengan pemandangan air terjun',
                    'WiFi gratis kecepatan tinggi',
                    'Area parkir luas (depan & belakang) + Buggy Cart',
                    'Akses Mini Zoo & Area Bermain Anak',
                    'Musholla bersih',
                    'Fasilitas ramah difabel',
                    'Spot foto instagramable',
                ],
                'excluded' => [
                    'Biaya makanan dan minuman',
                    'Transport menuju lokasi',
                    'Biaya pribadi lainnya',
                ],
                'gallery' => [
                    asset('images/The Waterfall Resto Images/Menu-Western-The-Waterfall-Resto-1536x1150.webp'),
                    asset('images/The Waterfall Resto Images/Seni-Kuliner-dan-Hospitality-1536x1151.webp'),
                    asset('images/The Waterfall Resto Images/Fasilitas-Umum-1536x1151.webp'),
                    asset('images/placeholders/Tentang-The-Waterfall-Resto-by-Godongijo-1-1536x1121.webp'),
                    asset('images/placeholders/asset 1.jpg'),
                    asset('images/placeholders/asset 5.png'),
                ],
                'info_images' => [],
            ],
            'Paket Sport Fishing' => [
                'tagline' => 'Monster Fish Fishing Lake - Tantangan Memancing Ikan Raksasa',
                'duration' => 'Per Jam/Harian',
                'hero_image' => asset('images/placeholders/Redtail-Catfish-Ikan-Predator-Amerika-Selatan-1536x853.webp'),
                'description_short' => 'Masuki dunia rekreasi mancing yang tak tertandingi di Monster Fish Fishing Lake by Godongijo. Destinasi outdoor premium yang menawarkan pengalaman memancing mendebarkan, dikelilingi vegetasi alami yang asri.',
                'features' => [
                    'Jenghis Khan Catfish hingga 60 kg',
                    'Redtail Catfish dari Amazon',
                    'Peacock Bass & Patin Raksasa Mekong',
                    'Lomba Mancing Galatama Rutin',
                    'Catch and Release System',
                    'Peralatan pancing tersedia (sewa)',
                    'Spot foto dengan hasil tangkapan',
                    'Buka 09:00-21:00 WIB',
                ],
                'fish_species' => [
                    [
                        'name' => 'Jenghis Khan Catfish',
                        'alias' => 'Paroon Shark, Hiu Air Tawar',
                        'weight' => 'Hingga 60 kg',
                        'origin' => 'Sungai Chao Phraya & Mekong',
                        'description' => 'Dikenal sebagai Hiu Air Tawar karena bentuk tubuhnya yang aerodinamis seperti torpedo. Primadona bagi pemancing profesional.',
                    ],
                    [
                        'name' => 'Redtail Catfish',
                        'alias' => 'Catfish Ekor Merah',
                        'weight' => 'Puluhan kilogram',
                        'origin' => 'Amazon, Amerika Selatan',
                        'description' => 'Ikan predator eksotis dengan sirip ekor berwarna merah menyala. Menjanjikan pertarungan yang seru.',
                    ],
                    [
                        'name' => 'Peacock Bass',
                        'alias' => 'Bass Merak',
                        'weight' => 'Belasan kilogram',
                        'origin' => 'Amazon, Amerika Selatan',
                        'description' => 'Tubuh hijau dihiasi motif seperti ekor burung merak. Sangat diminati karena daya tariknya saat melawan pancing.',
                    ],
                    [
                        'name' => 'Patin Raksasa Mekong',
                        'alias' => 'Pangasianodon gigas',
                        'weight' => 'Raksasa',
                        'origin' => 'Sungai Mekong',
                        'description' => 'Salah satu ikan raksasa yang menjadi incaran utama para pemancing sport.',
                    ],
                ],
                'itinerary' => [
                    [
                        'time' => 'Check-in',
                        'title' => 'Registrasi & Briefing',
                        'description' => 'Registrasi peserta, pilih paket (harian/kiloan/lomba), penjelasan syarat & ketentuan, safety procedure, dan tips teknik memancing',
                    ],
                    [
                        'time' => 'Sesi Fishing',
                        'title' => 'Monster Fish Fishing Session',
                        'description' => 'Nikmati pengalaman memancing ikan monster seperti Redtail Catfish, Jenghis Khan, Peacock Bass, dan spesies raksasa lainnya di kolam eksklusif dengan pendampingan staff',
                    ],
                    [
                        'time' => 'Photo Session',
                        'title' => 'Dokumentasi Catch',
                        'description' => 'Foto bersama hasil tangkapan di spot foto menarik sebelum catch and release',
                    ],
                ],
                'included' => [
                    'Akses kolam pemancingan Monster Fish',
                    'Briefing teknik dan safety procedure',
                    'Pendampingan staff berpengalaman',
                    'Spot foto dengan hasil tangkapan',
                    'Lomba Mancing Galatama (setiap akhir bulan)',
                    'Fasilitas toilet dan musholla',
                    'Area parkir luas',
                ],
                'excluded' => [
                    'Peralatan pancing (tersedia untuk disewa)',
                    'Umpan khusus',
                    'Konsumsi makanan dan minuman',
                    'Transport menuju lokasi',
                ],
                'gallery' => [
                    asset('images/placeholders/Redtail-Catfish-Ikan-Predator-Amerika-Selatan-1536x853.webp'),
                    asset('images/placeholders/asset 2.png'),
                    asset('images/placeholders/asset 4.png'),
                    asset('images/placeholders/asset 3 (3).jpg'),
                    asset('images/placeholders/hewan.jpg'),
                    asset('images/placeholders/asset 1.jpg'),
                ],
                'info_images' => [
                    [
                        'url' => asset('images/fish paket images/Daftar-Harga-Pemancingan-4-April-2026.jpg'),
                        'title' => 'Daftar Harga Pemancingan',
                        'description' => 'Harga per jam mulai dari Rp 75.000',
                    ],
                    [
                        'url' => asset('images/fish paket images/Jenis-Paket-Mancing-4-April-2026.jpg'),
                        'title' => 'Jenis Paket Mancing',
                        'description' => 'Paket Harian, Kiloan, dan Lomba',
                    ],
                    [
                        'url' => asset('images/fish paket images/Syarat-dan-Ketentuan-Lomba-Manicng-Tarikan.webp'),
                        'title' => 'Syarat & Ketentuan Lomba',
                        'description' => 'Aturan dan regulasi lomba mancing',
                    ],
                ],
            ],
            'Paket Private Event' => [
                'tagline' => 'Ruang Event Eksklusif di Tengah Alam',
                'duration' => 'Full Day',
                'hero_image' => asset('images/Private Images/Paket-Wedding-dan-Engagement-1536x701.webp'),
                'description_short' => 'Ciptakan momen spesial dan pertemuan penting Anda di Private Room The Waterfall Resto. Dengan kapasitas luas hingga 100 orang, ruangan dirancang dengan konsep elegan kontemporer yang unik dan terintegrasi penuh dengan alam.',
                'features' => [
                    'Kapasitas hingga 100 orang',
                    'Ruang ber-AC Lantai 2 dengan Dinding Kaca',
                    'Konsep Elegan Kontemporer + Alam',
                    'Paket Meeting, Wedding, Gathering',
                    'Dekorasi fleksibel (Klasik/Minimalis/Rustic)',
                    'Sound system, proyektor, audio-visual',
                    'Buffet makanan premium tersedia',
                    'Tim event profesional',
                ],
                'itinerary' => [
                    [
                        'time' => '2 Minggu Sebelumnya',
                        'title' => 'Konsultasi & Planning',
                        'description' => 'Diskusi kebutuhan acara (meeting/wedding/gathering), tema dekorasi, menu catering premium, rundown, dan setup (U-Shape/Classroom/Bundar)',
                    ],
                    [
                        'time' => 'Hari H - Persiapan',
                        'title' => 'Setup & Dekorasi',
                        'description' => 'Tim event menyiapkan ruangan ber-AC, dekorasi sesuai tema, instalasi bunga, sound system, proyektor, dan kursi premium',
                    ],
                    [
                        'time' => 'Hari H - Pelaksanaan',
                        'title' => 'Acara Berlangsung',
                        'description' => 'Acara berjalan lancar dengan pemandangan vegetasi hijau melalui dinding kaca, didukung penuh oleh tim event dan layanan buffet premium',
                    ],
                    [
                        'time' => 'Setelah Acara',
                        'title' => 'Cleanup & Documentation',
                        'description' => 'Tim membersihkan area dan menyerahkan dokumentasi foto acara',
                    ],
                ],
                'included' => [
                    'Ruang ber-AC kapasitas 100 orang dengan dinding kaca',
                    'Meja kayu berkualitas, kursi premium (kayu kontemporer)',
                    'Setup fleksibel (U-Shape/Classroom/Bundar)',
                    'Sound system, proyektor besar, audio-visual',
                    'WiFi gratis kecepatan tinggi',
                    'Dekorasi basic (bisa upgrade)',
                    'Buffet makanan premium (optional)',
                    'Tim event support profesional',
                    'Area parkir luas',
                    'Dokumentasi foto acara',
                ],
                'excluded' => [
                    'Paket catering premium (bisa ditambahkan)',
                    'Dekorasi khusus ekstra (klasik romantis/rustic)',
                    'Entertainment atau MC',
                    'Dokumentasi video profesional',
                ],
                'gallery' => [
                    asset('images/Private Images/Paket-Meeting-1536x701.webp'),
                    asset('images/Private Images/Dekorasi-Wedding-dan-Lamaran-2048x1137.webp'),
                    asset('images/Private Images/Paket-Wedding-dan-Engagement-1536x701.webp'),
                    asset('images/Private Images/Paket-Gathering-2048x934.webp'),
                    asset('images/Private Images/BCA-Gathering-2048x1137.webp'),
                    asset('images/placeholders/asset 3.png'),
                ],
                'info_images' => [],
            ],
            'Paket Rekreasi Keluarga' => [
                'tagline' => 'Paket Lengkap untuk Liburan Keluarga',
                'duration' => '1 Hari',
                'hero_image' => asset('images/placeholders/asset 4.png'),
                'description_short' => 'Nikmati pengalaman liburan keluarga yang lengkap dengan kombinasi kuliner, edukasi satwa, dan rekreasi fishing di satu lokasi yang asri dan ramah keluarga.',
                'features' => [
                    'Makan di The Waterfall Resto',
                    'Akses Mini Zoo & Kids Area',
                    'Fishing experience (1 jam)',
                    'Foto bersama satwa',
                    'Area bermain anak aman',
                    'Parkir gratis',
                ],
                'itinerary' => [
                    [
                        'time' => '09:00 - 10:00',
                        'title' => 'Check-in & Welcome',
                        'description' => 'Registrasi dan welcome drink, penjelasan fasilitas dan area yang dapat dinikmati',
                    ],
                    [
                        'time' => '10:00 - 12:00',
                        'title' => 'Mini Zoo & Kids Area',
                        'description' => 'Anak-anak bermain di area bermain dan berinteraksi dengan satwa di Mini Zoo',
                    ],
                    [
                        'time' => '12:00 - 14:00',
                        'title' => 'Lunch Time',
                        'description' => 'Nikmati makan siang di The Waterfall Resto dengan menu pilihan',
                    ],
                    [
                        'time' => '14:00 - 15:00',
                        'title' => 'Fishing Experience',
                        'description' => 'Pengalaman memancing keluarga dengan pendampingan staff (1 jam)',
                    ],
                    [
                        'time' => '15:00 - 16:00',
                        'title' => 'Free Time & Photo',
                        'description' => 'Waktu bebas untuk eksplorasi, foto keluarga, dan berbelanja souvenir',
                    ],
                ],
                'included' => [
                    'Paket makan untuk keluarga (4 orang)',
                    'Akses Mini Zoo & Kids Area seharian',
                    'Fishing experience 1 jam dengan alat',
                    'Welcome drink',
                    'Foto bersama satwa',
                    'WiFi gratis',
                    'Parkir gratis',
                ],
                'excluded' => [
                    'Tambahan makanan atau minuman diluar paket',
                    'Transport menuju lokasi',
                    'Souvenir atau merchandise',
                ],
                'gallery' => [
                    asset('images/placeholders/hewan.jpg'),
                    asset('images/placeholders/asset 4.png'),
                    asset('images/placeholders/asset 3 (3).jpg'),
                    asset('images/placeholders/asset 2.png'),
                    asset('images/placeholders/asset 6.png'),
                    asset('images/placeholders/asset 5.png'),
                ],
                'info_images' => [],
            ],
        ];
        
        // Return static data for the package, or default values if not found
        return $staticData[$packageName] ?? [
            'tagline' => 'Pengalaman Wisata yang Tak Terlupakan',
            'duration' => 'Fleksibel',
            'hero_image' => asset('images/placeholders/asset 1.jpg'),
            'description_short' => '',
            'features' => [],
            'itinerary' => [],
            'included' => [],
            'excluded' => [],
            'gallery' => [
                asset('images/placeholders/asset 1.jpg'),
                asset('images/placeholders/asset 2.png'),
                asset('images/placeholders/asset 3.png'),
                asset('images/placeholders/asset 4.png'),
            ],
            'info_images' => [],
        ];
    }
    
    /**
     * Format package data for view by merging database and static data
     *
     * @param PaketWisata $package Package model from database
     * @param array $staticData Static package data
     * @return array Formatted package data
     */
    private function formatPackageForView(PaketWisata $package, array $staticData): array
    {
        // Generate slug from package name
        $slug = Str::slug($package->nama_paket);
        
        // Format price
        if ($package->harga > 0) {
            $priceFormatted = 'Rp ' . number_format($package->harga, 0, ',', '.');
            $pricePerPerson = '/ orang';
        } else {
            $priceFormatted = 'Hubungi Kami';
            $pricePerPerson = '';
        }
        
        return [
            'id' => $package->id,
            'name' => $package->nama_paket,
            'slug' => $slug,
            'tagline' => $staticData['tagline'],
            'description' => $package->deskripsi,
            'hero_image' => $staticData['hero_image'] ?? asset($package->foto),
            'price' => $priceFormatted,
            'pricePerPerson' => $pricePerPerson,
            'priceRaw' => $package->harga,
            'duration' => $staticData['duration'],
            'features' => $staticData['features'],
            'itinerary' => $staticData['itinerary'],
            'included' => $staticData['included'],
            'excluded' => $staticData['excluded'],
            'gallery' => $staticData['gallery'],
        ];
    }
}
