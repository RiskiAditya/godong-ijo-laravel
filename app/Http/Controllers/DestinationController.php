<?php

namespace App\Http\Controllers;

use App\Services\NavigationService;
use App\Services\BreadcrumbService;
use App\Services\SEOService;
use Illuminate\View\View;

class DestinationController extends Controller
{
    /**
     * NavigationService instance
     *
     * @var NavigationService
     */
    private NavigationService $navigationService;
    
    /**
     * BreadcrumbService instance
     *
     * @var BreadcrumbService
     */
    private BreadcrumbService $breadcrumbService;
    
    /**
     * SEOService instance
     *
     * @var SEOService
     */
    private SEOService $seoService;
    
    /**
     * Constructor
     *
     * @param NavigationService $navigationService
     * @param BreadcrumbService $breadcrumbService
     * @param SEOService $seoService
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
     * Display a destination page
     *
     * @param string $slug Destination slug (the-waterfall, monster-fish)
     * @return View
     */
    public function show(string $slug): View
    {
        // Get destination data or abort with 404 if invalid slug
        $destination = $this->getDestinationData($slug);
        
        if ($destination === null) {
            abort(404);
        }
        
        // Get gallery images
        $gallery = $this->getDestinationGallery($slug);
        
        // Get facility information
        $facilities = $this->getDestinationFacilities($slug);
        
        // Generate navigation
        $navigation = $this->navigationService->getMainNavigation();
        $cta = $this->navigationService->getCTA();
        
        // Generate breadcrumbs
        $breadcrumbs = $this->breadcrumbService->generate('destination.show', $slug, [
            'name' => $destination['name'],
        ]);
        
        // Generate SEO metadata
        $seoData = $this->seoService->generateMetadata('destination', [
            'name' => $destination['name'],
            'description' => $destination['description'],
            'image' => $destination['hero_image'],
            'route' => 'destination.show',
            'slug' => $slug,
            'url' => route('destination.show', ['slug' => $slug], true),
            'breadcrumbs' => $breadcrumbs,
            'openingHours' => $facilities['hours'] ?? null,
        ]);
        
        // Prepare page data
        $pageData = [
            'destination' => $destination,
            'gallery' => $gallery,
            'facilities' => $facilities,
            'navigation' => $navigation,
            'cta' => $cta,
            'breadcrumbs' => $breadcrumbs,
            'seoData' => $seoData,
            'currentRoute' => 'destination.show',
        ];
        
        return view('pages.destination', $pageData);
    }
    
    /**
     * Get destination data by slug
     *
     * @param string $slug Destination slug
     * @return array|null Destination data or null if not found
     */
    private function getDestinationData(string $slug): ?array
    {
        $destinations = [
            'the-waterfall' => [
                'slug' => 'the-waterfall',
                'name' => 'The Waterfall Resto',
                'tagline' => 'Dine in Nature Experience',
                'description' => 'Destinasi kuliner paling ekologis di Indonesia dengan konsep Dine in Nature yang asri. Nikmati hidangan Eropa dan Nusantara berkualitas premium di tengah suasana air terjun mini yang menenangkan. The Waterfall Resto menawarkan pengalaman bersantap unik yang menggabungkan cita rasa kuliner tinggi dengan keindahan alam yang asri.',
                'hero_image' => asset('images/placeholders/Tentang-The-Waterfall-Resto-by-Godongijo-1-1536x1121.webp'),
                'cta_text' => 'Pesan Sekarang',
                'cta_package' => null, // No specific package pre-selection
                'content_sections' => $this->getWaterfallContentSections(),
            ],
            'monster-fish' => [
                'slug' => 'monster-fish',
                'name' => 'Monster Fish Fishing Lake',
                'tagline' => 'Sport Fishing Adventure',
                'description' => 'Destinasi pemancingan sport eksklusif dengan tantangan adrenalin yang menantang. Uji kemampuan Anda melawan ikan-ikan berukuran raksasa di kolam pemancingan yang dirancang khusus. Monster Fish Fishing Lake adalah surga bagi pecinta mancing, menawarkan pengalaman memancing ikan raksasa dengan fasilitas lengkap dan pemandangan yang menakjubkan.',
                'hero_image' => asset('images/placeholders/Redtail-Catfish-Ikan-Predator-Amerika-Selatan-1536x853.webp'),
                'cta_text' => 'Pesan Sekarang',
                'cta_package' => null,
            ],
        ];
        
        return $destinations[$slug] ?? null;
    }
    
    /**
     * Get gallery images for a destination (minimum 4 images)
     *
     * @param string $slug Destination slug
     * @return array Gallery images
     */
    private function getDestinationGallery(string $slug): array
    {
        $galleries = [
            'the-waterfall' => [
                [
                    'url' => asset('images/The Waterfall Resto Images/Menu-Western-The-Waterfall-Resto-1536x1150.webp'),
                    'alt' => 'Menu Western The Waterfall Resto',
                    'caption' => 'Hidangan premium Eropa dan Nusantara',
                ],
                [
                    'url' => asset('images/The Waterfall Resto Images/Seni-Kuliner-dan-Hospitality-1536x1151.webp'),
                    'alt' => 'Seni Kuliner dan Hospitality',
                    'caption' => 'Chef berpengalaman 20+ tahun',
                ],
                [
                    'url' => asset('images/The Waterfall Resto Images/Fasilitas-Umum-1536x1151.webp'),
                    'alt' => 'Fasilitas Umum The Waterfall Resto',
                    'caption' => 'Fasilitas lengkap untuk kenyamanan Anda',
                ],
                [
                    'url' => asset('images/placeholders/Tentang-The-Waterfall-Resto-by-Godongijo-1-1536x1121.webp'),
                    'alt' => 'The Waterfall Resto dengan konsep Dine in Nature',
                    'caption' => 'Suasana restoran dengan air terjun mini',
                ],
                [
                    'url' => asset('images/placeholders/asset 1.jpg'),
                    'alt' => 'Area makan outdoor The Waterfall Resto',
                    'caption' => 'Area makan outdoor yang asri',
                ],
                [
                    'url' => asset('images/placeholders/asset 5.png'),
                    'alt' => 'Interior The Waterfall Resto',
                    'caption' => 'Interior restoran yang nyaman',
                ],
            ],
            'monster-fish' => [
                [
                    'url' => asset('images/placeholders/Redtail-Catfish-Ikan-Predator-Amerika-Selatan-1536x853.webp'),
                    'alt' => 'Redtail Catfish di Monster Fish Lake',
                    'caption' => 'Ikan raksasa Redtail Catfish',
                ],
                [
                    'url' => asset('images/placeholders/asset 2.png'),
                    'alt' => 'Kolam pemancingan Monster Fish',
                    'caption' => 'Kolam pemancingan eksklusif',
                ],
                [
                    'url' => asset('images/placeholders/asset 4.png'),
                    'alt' => 'Aktivitas memancing di Monster Fish Lake',
                    'caption' => 'Pengalaman sport fishing yang menantang',
                ],
                [
                    'url' => asset('images/placeholders/Redtail-Catfish-Ikan-Predator-Amerika-Selatan-1536x853.webp'),
                    'alt' => 'Fasilitas Monster Fish Fishing Lake',
                    'caption' => 'Fasilitas lengkap untuk pengunjung',
                ],
            ],
        ];
        
        return $galleries[$slug] ?? [];
    }
    
    /**
     * Get facility information for a destination
     *
     * @param string $slug Destination slug
     * @return array Facility information (hours, admission, amenities, contact)
     */
    private function getDestinationFacilities(string $slug): array
    {
        $facilities = [
            'the-waterfall' => [
                'hours' => 'Weekday: 11.00 - 21.00 WIB | Weekend & Libur: 10.00 - 21.00 WIB',
                'admission' => 'GRATIS tiket masuk! Pembayaran sesuai pemesanan makanan/minuman.',
                'amenities' => [
                    'Area 2,9 hektar dengan air terjun',
                    'WiFi gratis berkecepatan tinggi',
                    'Area parkir luas (depan & belakang)',
                    'Buggy Cart untuk antar tamu',
                    'Musholla bersih',
                    'Area bermain anak',
                    'Mini Zoo menarik',
                    'Toilet ramah difabel',
                    'Private room untuk acara',
                ],
                'contact' => config('app.whatsapp.display'),
                'info_images' => [],
            ],
            'monster-fish' => [
                'hours' => 'Senin - Minggu: 09.00 - 21.00 WIB',
                'admission' => 'Tarif per jam mulai dari Rp 75.000. Tersedia paket harian, kiloan, dan lomba.',
                'amenities' => [
                    'Kolam pemancingan eksklusif',
                    'Berbagai spesies ikan raksasa',
                    'Peralatan mancing (tersedia sewa)',
                    'Lomba Mancing Galatama (rutin setiap akhir bulan)',
                    'Spot foto dengan hasil tangkapan',
                    'Area istirahat nyaman',
                    'Toilet dan fasilitas umum',
                    'Warung makan & minuman',
                ],
                'contact' => config('app.whatsapp.display'),
                'info_images' => [
                    [
                        'url' => asset('images/fish paket images/Daftar-Harga-Pemancingan-4-April-2026.jpg'),
                        'title' => 'Daftar Harga Pemancingan',
                        'description' => 'Tarif lengkap untuk berbagai paket pemancingan',
                    ],
                    [
                        'url' => asset('images/fish paket images/Jenis-Paket-Mancing-4-April-2026.jpg'),
                        'title' => 'Jenis Paket Mancing',
                        'description' => 'Pilihan paket: Harian, Kiloan, dan Lomba',
                    ],
                    [
                        'url' => asset('images/fish paket images/Syarat-dan-Ketentuan-Lomba-Manicng-Tarikan.webp'),
                        'title' => 'Syarat & Ketentuan Lomba',
                        'description' => 'Aturan dan regulasi lomba mancing',
                    ],
                ],
            ],
        ];
        
        return $facilities[$slug] ?? [];
    }
    
    /**
     * Get custom content sections for The Waterfall Resto
     *
     * @return array Content sections with title, description, image, and layout
     */
    private function getWaterfallContentSections(): array
    {
        return [
            [
                'title' => 'Tentang The Waterfall Resto',
                'description' => 'Selamat datang di The Waterfall Resto by Godongijo, destinasi kuliner yang bukan sekadar tempat makan, melainkan perwujudan visi kami sebagai destinasi kuliner ekologis di Indonesia. Kami unggul dalam gastronomi premium dan pelayanan prima, didukung oleh lingkungan alam hijau yang tak tertandingi. Dibangun di atas area seluas 2,9 hektar dengan bangunan resto, kami merancang setiap sudut The Waterfall Resto untuk menghadirkan kenyamanan maksimal bagi setiap pengunjung. Dengan konsep Elegan Kontemporer yang terintegrasi penuh dengan Alam, The Waterfall Resto memadukan kemewahan formal dengan penataan lansekap alami yang menawan. Kehadiran air terjun dan vegetasi subur menciptakan setting ideal yang dramatis dan menenangkan, sempurna untuk acara korporat besar maupun perayaan intim Anda.',
                'image' => asset('images/The Waterfall Resto Images/Tentang-TWF-2048x769.webp'),
                'layout' => 'image-right',
                'highlights' => [
                    'Area seluas 2,9 hektar',
                    'Konsep Elegan Kontemporer dengan Alam',
                    'Lingkungan alam hijau yang tak tertandingi',
                ],
            ],
            [
                'title' => 'Seni Kuliner dan Hospitality',
                'description' => 'Di The Waterfall Resto, Seni Kuliner dan Hospitality adalah komitmen utama kami, didukung oleh tim profesional yang telah teruji. Kepemimpinan Gastronomi Dapur kami dipimpin oleh Head Chef berpengalaman yang telah mengabdikan diri di industri F&B selama lebih dari 20 tahun, didampingi oleh Sous Chef yang juga memiliki pengalaman solid lebih dari 15 tahun. Keunggulan Pelayanan Layanan dan hospitality Anda dipastikan prima, dikelola langsung oleh Front Of The House Manager dengan pengalaman di dunia hospitality terkemuka selama lebih dari 15 tahun. Bersama tim dapur dan tim hospitality yang berdedikasi, kami berkomitmen menyajikan menu a la carte, buffet, dan tasting menu yang berevolusi. Kami menjamin inovasi dan konsistensi tinggi dalam setiap presentasi hidangan, memberikan pengalaman bersantap yang tak terlupakan bagi Anda.',
                'image' => asset('images/The Waterfall Resto Images/Seni-Kuliner-dan-Hospitality-1536x1151.webp'),
                'layout' => 'image-left',
                'highlights' => [
                    'Head Chef 20+ tahun pengalaman',
                    'Sous Chef 15+ tahun pengalaman',
                    'Front Of The House Manager 15+ tahun',
                ],
            ],
            [
                'title' => 'Menu Berkualitas Premium',
                'description' => 'Di The Waterfall Resto, setiap hidangan adalah perwujudan komitmen kami pada kualitas tanpa kompromi. Keunggulan kuliner kami mencakup perpaduan istimewa antara Menu Eropa dan Hidangan Nusantara otentik. Kami menjamin integritas dan cita rasa tinggi dengan Bahan Baku Premium - Seluruh hidangan didukung oleh bahan baku segar dan berkualitas premium, bersumber secara strategis dari pemasok terpilih. Presisi Pengolahan - Proses memasak didukung oleh metode kuliner profesional dan penggunaan peralatan dapur yang canggih dan modern. Ini memastikan presisi rasa, konsistensi kualitas, dan kecepatan penyajian yang selalu memenuhi standar tertinggi.',
                'image' => asset('images/The Waterfall Resto Images/Menu-Western-The-Waterfall-Resto-1536x1150.webp'),
                'layout' => 'image-right',
                'highlights' => [
                    'Perpaduan Menu Eropa dan Nusantara',
                    'Bahan baku segar dan premium',
                    'Metode kuliner profesional',
                ],
                'menu_images' => [
                    [
                        'url' => asset('images/The Waterfall Resto Images/Menu-Western-The-Waterfall-Resto-1536x1150.webp'),
                        'alt' => 'Menu Western The Waterfall Resto',
                        'title' => 'Menu Western',
                    ],
                    [
                        'url' => asset('images/The Waterfall Resto Images/Menu-Nusantara.webp'),
                        'alt' => 'Menu Nusantara The Waterfall Resto',
                        'title' => 'Menu Nusantara',
                    ],
                ],
            ],
            [
                'title' => 'Fasilitas Umum',
                'description' => 'Kami mengutamakan kenyamanan setiap pengunjung. Oleh karena itu, The Waterfall Resto menyediakan fasilitas umum yang lengkap dan memadai. Akses dan Parkir Luas - Kami memiliki area parkir yang sangat luas untuk kendaraan roda dua dan empat (tersedia di area depan dan belakang). Aksesibilitas Tinggi - Untuk kemudahan Anda, terutama dari area parkir belakang menuju restoran, Buggy Cart kami selalu siap mengantar para tamu. Fasilitas Inklusif (Ramah Difabel) - Restoran kami didesain ramah untuk penyandang difabel, baik dari segi akses jalan hingga fasilitas toilet, memastikan semua tamu dapat menikmati pengalaman bersantap dengan mudah dan nyaman. Fasilitas Pendukung - Tersedia Musholla untuk beribadah, toilet yang bersih, Wi-Fi gratis berkecepatan tinggi, serta Area Bermain Anak yang dilengkapi dengan Mini Zoo yang menarik.',
                'image' => asset('images/The Waterfall Resto Images/Fasilitas-Umum-1536x1151.webp'),
                'layout' => 'image-left',
                'highlights' => [
                    'Area parkir luas & Buggy Cart',
                    'Fasilitas ramah difabel',
                    'Musholla, WiFi, Area Bermain Anak & Mini Zoo',
                ],
            ],
        ];
    }
}
