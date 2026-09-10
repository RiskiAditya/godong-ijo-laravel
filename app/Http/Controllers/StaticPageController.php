<?php

namespace App\Http\Controllers;

use App\Services\NavigationService;
use App\Services\BreadcrumbService;
use App\Services\SEOService;
use Illuminate\View\View;
use Illuminate\Support\Facades\Route;

class StaticPageController extends Controller
{
    /**
     * Navigation service instance
     *
     * @var NavigationService
     */
    protected NavigationService $navigationService;
    
    /**
     * Breadcrumb service instance
     *
     * @var BreadcrumbService
     */
    protected BreadcrumbService $breadcrumbService;
    
    /**
     * SEO service instance
     *
     * @var SEOService
     */
    protected SEOService $seoService;
    
    /**
     * Constructor - inject required services
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
     * Display Wisata Edukasi page
     *
     * @return View
     */
    public function education(): View
    {
        // Get page data
        $pageData = $this->getEducationData();
        
        // Generate breadcrumbs
        $breadcrumbs = $this->breadcrumbService->generate('education', null, [
            'name' => 'Wisata Edukasi',
        ]);
        
        // Generate SEO metadata
        $seoData = $this->seoService->generateMetadata('education', [
            'route' => 'education',
            'breadcrumbs' => $breadcrumbs,
        ]);
        
        // Get navigation data
        $navigation = $this->navigationService->getMainNavigation();
        $cta = $this->navigationService->getCTA();
        $currentRoute = Route::currentRouteName();
        
        return view('pages.education', [
            'pageData' => $pageData,
            'breadcrumbs' => $breadcrumbs,
            'seoData' => $seoData,
            'navigation' => $navigation,
            'cta' => $cta,
            'currentRoute' => $currentRoute,
        ]);
    }
    
    /**
     * Display Kontak page
     *
     * @return View
     */
    public function contact(): View
    {
        // Get page data
        $pageData = $this->getContactData();
        
        // Generate breadcrumbs
        $breadcrumbs = $this->breadcrumbService->generate('contact', null, [
            'name' => 'Kontak',
        ]);
        
        // Generate SEO metadata
        $seoData = $this->seoService->generateMetadata('contact', [
            'route' => 'contact',
            'breadcrumbs' => $breadcrumbs,
        ]);
        
        // Get navigation data
        $navigation = $this->navigationService->getMainNavigation();
        $cta = $this->navigationService->getCTA();
        $currentRoute = Route::currentRouteName();
        
        return view('pages.contact', [
            'pageData' => $pageData,
            'breadcrumbs' => $breadcrumbs,
            'seoData' => $seoData,
            'navigation' => $navigation,
            'cta' => $cta,
            'currentRoute' => $currentRoute,
        ]);
    }
    
    /**
     * Get Wisata Edukasi page data
     *
     * @return array
     */
    private function getEducationData(): array
    {
        // Task 3.4: Query school partners safely so the redesign does not explode when the DB container is off.
        try {
            // Fetch all active partners so both marquee rows have logos to scroll.
            $schoolPartners = \App\Models\SchoolPartner::active()->ordered()->get();
            $totalSchoolPartners = $schoolPartners->count();
        } catch (\Throwable $e) {
            \Log::warning('StaticPageController: SchoolPartner lookup failed during education data build.', [
                'error' => $e->getMessage(),
            ]);
            $schoolPartners = collect([]);
            $totalSchoolPartners = 0;
        }

        // Anything beyond what the two marquee rows show gets surfaced via the "+N sekolah lainnya" link.
        $remainingSchoolsCount = max(0, $totalSchoolPartners - $schoolPartners->count());
        
        // Task 3.3: Count total programs dynamically
        $totalEducationPrograms = 3 + 2 + 14; // Environmental (3) + Science (2) + Art (14)
        
        return [
            // Task 3.1: Enhanced hero data with location badge, dual buttons, statistics strip
            'hero' => [
                'title' => 'Wisata Edukasi Ecotainment Godongijo',
                'subtitle' => 'Belajar Sambil Bermain di Alam',
                'description' => 'Ecotainment adalah suatu kegiatan karyawisata yang memiliki nilai edukasi dengan konsep wisata yang menghibur. Program wisata edukasi yang menggabungkan pembelajaran tentang alam, ekologi, dan konservasi dengan pengalaman rekreasi yang menyenangkan.',
                'image' => asset('images/wisata edukasi/haloo.webp'),
                'imageFallback' => asset('images/wisata edukasi/haloo.webp'),
                'alt' => 'Wisata Edukasi Ecotainment di Godong Ijo',
                'locationBadge' => [
                    'icon' => 'map-pin',
                    'text' => 'Depok, Jawa Barat - buka setiap hari',
                ],
                'buttons' => [
                    [
                        'label' => 'About us',
                        'url' => route('contact'),
                        'variant' => 'outline',
                        'icon' => null,
                    ],
                    [
                        'label' => 'Reservasi',
                        'url' => 'https://wa.me/' . config('app.whatsapp_number') . '?text=' . urlencode('Halo, saya tertarik untuk melakukan reservasi wisata edukasi di Godong Ijo'),
                        'variant' => 'solid-gold',
                        'icon' => 'brand-whatsapp',
                        'external' => true,
                    ],
                ],
                'statistics' => [
                    [
                        'value' => '20+',
                        'label' => 'tahun beroperasi',
                    ],
                    [
                        'value' => $totalEducationPrograms,
                        'label' => 'program edukasi',
                    ],
                    [
                        'value' => $totalSchoolPartners,
                        'label' => 'sekolah mitra',
                    ],
                ],
            ],
            // Task 3.2: Platform Fieldtrip with Tabler icons (ti-device-laptop, ti-bus, ti-trees)
            'platformFieldtrip' => [
                [
                    'title' => 'Virtual Fieldtrip',
                    'icon' => 'ti-device-laptop',
                    'iconStyle' => [
                        'borderRadius' => '8px',
                        'backgroundColor' => '#FAF6ED', // --cream color
                    ],
                    'description' => 'Program karyawisata virtual interaktif yang memungkinkan siswa belajar tentang alam dan konservasi dari sekolah dengan teknologi multimedia.',
                    'image' => asset('images/wisata edukasi/Virtual Fieldtrip.jpg'),
                    'imageFallback' => asset('images/wisata edukasi/Virtual Fieldtrip.jpg'),
                    'whatsappMessage' => 'Halo, saya tertarik dengan program Virtual Fieldtrip di Godong Ijo',
                ],
                [
                    'title' => 'Goes To School Field Trip',
                    'icon' => 'ti-bus',
                    'iconStyle' => [
                        'borderRadius' => '8px',
                        'backgroundColor' => '#FAF6ED', // --cream color
                    ],
                    'description' => 'Tim edukator kami datang ke sekolah Anda dengan materi pembelajaran interaktif dan hands-on activities tentang lingkungan.',
                    'image' => asset('images/wisata edukasi/Goes To Shool Field Trip.jpg'),
                    'imageFallback' => asset('images/wisata edukasi/Goes To Shool Field Trip.jpg'),
                    'whatsappMessage' => 'Halo, saya tertarik dengan program Goes To School Field Trip dari Godong Ijo',
                ],
                [
                    'title' => 'Fieldtrip at Godongijo',
                    'icon' => 'ti-trees',
                    'iconStyle' => [
                        'borderRadius' => '8px',
                        'backgroundColor' => '#FAF6ED', // --cream color
                    ],
                    'description' => 'Kunjungan langsung ke Godong Ijo untuk pengalaman belajar di alam terbuka dengan berbagai program edukasi yang menarik.',
                    'image' => asset('images/wisata edukasi/Fieldtrip at Godongijo.jpg'),
                    'imageFallback' => asset('images/wisata edukasi/Fieldtrip at Godongijo.jpg'),
                    'whatsappMessage' => 'Halo, saya tertarik dengan program Fieldtrip at Godongijo',
                ],
            ],

            'programCategories' => [
                [
                    'title' => 'Edukasi Lingkungan',
                    'icon' => 'ti-leaf',
                    'description' => 'Program belajar langsung tentang satwa, pertanian urban, dan kebiasaan ramah lingkungan melalui aktivitas lapangan.',
                    'image' => asset('images/wisata edukasi/Edukasi Lingkungan.jpg'),
                    'imageFallback' => asset('images/wisata edukasi/Edukasi Lingkungan.jpg'),
                    'whatsappMessage' => 'Halo, saya tertarik dengan kategori program Edukasi Lingkungan di Godong Ijo',
                ],
                [
                    'title' => 'Science',
                    'icon' => 'ti-flask-2',
                    'description' => 'Eksperimen dan praktik sains ringan yang membantu siswa memahami konsep fisika, biologi, dan teknologi sederhana.',
                    'image' => asset('images/wisata edukasi/science.jpg'),
                    'imageFallback' => asset('images/wisata edukasi/science.jpg'),
                    'whatsappMessage' => 'Halo, saya tertarik dengan kategori program Science di Godong Ijo',
                ],
                [
                    'title' => 'Art',
                    'icon' => 'ti-palette',
                    'description' => 'Aktivitas kreatif seperti batik, memasak, tari, dan kerajinan yang melatih ekspresi serta kolaborasi anak.',
                    'image' => asset('images/wisata edukasi/art.webp'),
                    'imageFallback' => asset('images/wisata edukasi/art.webp'),
                    'whatsappMessage' => 'Halo, saya tertarik dengan kategori program Art di Godong Ijo',
                ],
            ],
            
            // Environmental Education Programs (3 programs)
            'environmentalPrograms' => [
                [
                    'title' => 'Fast Learning Camp',
                    'description' => 'Program pembelajaran intensif yang menggabungkan aktivitas outdoor, team building, dan edukasi lingkungan dalam format camp yang menyenangkan.',
                    'image' => asset('images/wisata edukasi/Edukasi Lingkungan.jpg'),
                    'imageFallback' => asset('images/wisata edukasi/Edukasi Lingkungan.jpg'),
                    'whatsappMessage' => 'Halo, saya tertarik dengan program Fast Learning Camp di Godong Ijo',
                ],
                [
                    'title' => 'Learning About Animals',
                    'description' => 'Kenali berbagai jenis satwa di Mini Zoo kami dan pelajari tentang konservasi, habitat, perilaku hewan, serta pentingnya melindungi keanekaragaman hayati.',
                    'image' => asset('images/wisata edukasi/animals.jpg'),
                    'imageFallback' => asset('images/wisata edukasi/animals.jpg'),
                    'whatsappMessage' => 'Halo, saya tertarik dengan program Learning About Animals di Godong Ijo',
                ],
                [
                    'title' => 'Urban Farming',
                    'description' => 'Eksplorasi konsep pertanian urban dan keberlanjutan melalui praktik langsung di area hijau dengan sistem hidroponik, taman vertikal, dan composting.',
                    'image' => asset('images/wisata edukasi/urban farming.jpg'),
                    'imageFallback' => asset('images/wisata edukasi/urban farming.jpg'),
                    'whatsappMessage' => 'Halo, saya tertarik dengan program Urban Farming di Godong Ijo',
                ],
            ],
            
            // Science Programs (2 programs)
            'sciencePrograms' => [
                [
                    'title' => 'Aero Glider',
                    'icon' => 'ti-plane',
                    'image' => asset('images/wisata edukasi/Aero Glider (Prototype Pembangkit Listrik Tenaga Angin).jpg'),
                    'imageFallback' => asset('images/wisata edukasi/Aero Glider (Prototype Pembangkit Listrik Tenaga Angin).jpg'),
                    'description' => 'Pelajari prinsip aerodinamika dan fisika terbang melalui aktivitas membuat dan menerbangkan pesawat glider dengan berbagai desain.',
                    'whatsappMessage' => 'Halo, saya tertarik dengan program Aero Glider di Godong Ijo',
                ],
                [
                    'title' => 'Profesor Cilik',
                    'icon' => 'ti-microscope',
                    'image' => asset('images/wisata edukasi/profesor cilik.webp'),
                    'imageFallback' => asset('images/wisata edukasi/profesor cilik.webp'),
                    'description' => 'Program sains interaktif yang mengajak anak-anak melakukan eksperimen sederhana untuk memahami konsep dasar fisika, kimia, dan biologi.',
                    'whatsappMessage' => 'Halo, saya tertarik dengan program Profesor Cilik di Godong Ijo',
                ],
            ],
            
            // Task 3.3: Art programs split into featured (3) and others (11)
            'artPrograms' => [
                'featured' => [
                    [
                        'title' => 'Fine Art',
                        'icon' => 'ti-brush',
                        'description' => 'Fine art adalah program edukasi yang mengajarkan siswa/i berkreasi membuat karya seni rupa.',
                        'image' => asset('images/wisata edukasi/fine art.webp'),
                        'imageFallback' => asset('images/wisata edukasi/fine art.webp'),
                        'whatsappMessage' => 'Halo, saya tertarik dengan program Fine Art di Godong Ijo',
                    ],
                    [
                        'title' => 'Art Painting',
                        'icon' => 'ti-palette',
                        'description' => 'Art painting adalah program edukasi yang mengajarkan siswa/i melukis pada sebuah media menggunakan alat-alat lukis.',
                        'image' => asset('images/wisata edukasi/art painting.jpg'),
                        'imageFallback' => asset('images/wisata edukasi/art painting.jpg'),
                        'whatsappMessage' => 'Halo, saya tertarik dengan program Art Painting di Godong Ijo',
                    ],
                    [
                        'title' => 'Traditional Dance',
                        'icon' => 'ti-music',
                        'description' => 'Mengenal dan mempelajari tarian tradisional Indonesia seperti tari Saman, Jaipong, atau Piring dengan bimbingan instruktur berpengalaman.',
                        'image' => asset('images/wisata edukasi/tari tradisional.jpg'),
                        'imageFallback' => asset('images/wisata edukasi/tari tradisional.jpg'),
                        'whatsappMessage' => 'Halo, saya tertarik dengan program Traditional Dance di Godong Ijo',
                    ],
                ],
                'others' => [
                    ['title' => 'Art Painting'],
                    ['title' => 'Fine Art'],
                    ['title' => 'Kerajinan Tangan'],
                    ['title' => 'Origami'],
                    ['title' => 'Kolase'],
                    ['title' => 'Melukis Kain'],
                    ['title' => 'Dekorasi Cupcake'],
                    ['title' => 'Membuat Keramik'],
                    ['title' => 'Mozaik'],
                    ['title' => 'Paper Craft'],
                    ['title' => 'Clay Sculpture'],
                ],
            ],
            
            // Task 3.5: Testimonial with dark card design metadata
            'testimonial' => [
                'photo' => asset('images/wisata edukasi/kapan lagi.jpg'),
                'photoFallback' => asset('images/wisata edukasi/kapan lagi.jpg'),
                'photoAlt' => 'Aktivitas wisata edukasi di Godong Ijo',
                'quote' => 'Ada banyak wahana yang ditawarkan seperti lokasi khusus aneka tanaman dan reptil. Tempat outbond dan wahana edukasi lain khusus anak-anak. Tak heran banyak rombongan sekolah yang berkunjung ke sini untuk refreshing.',
                'source' => '— kapanlagi.com',
                'design' => [
                    'backgroundColor' => 'var(--forest)',
                    'quoteIconColor' => 'var(--gold)',
                    'quoteTextStyle' => 'italic',
                    'quoteTextFont' => 'serif',
                    'sourceColor' => 'var(--gold)',
                    'borderRadius' => '14px',
                ],
            ],
            
            // Task 3.4: School partners data
            'schoolPartners' => $schoolPartners,
            'remainingSchoolsCount' => $remainingSchoolsCount,
            
            // Task 3.6: Footer CTA bar data
            'footerCTA' => [
                'text' => 'Siap merencanakan kunjungan sekolah?',
                'buttonLabel' => 'Hubungi via WhatsApp',
                'whatsappMessage' => 'Halo, saya ingin merencanakan kunjungan sekolah ke Godong Ijo untuk program wisata edukasi',
                'design' => [
                    'backgroundColor' => 'var(--forest-d)',
                    'buttonBackgroundColor' => 'var(--gold)',
                ],
            ],
            
            'target_audience' => [
                'title' => 'Target Peserta',
                'groups' => [
                    [
                        'name' => 'Sekolah & Universitas',
                        'icon' => '🎓',
                        'description' => 'Program field trip untuk siswa SD, SMP, SMA, dan mahasiswa dengan kurikulum disesuaikan tingkat pendidikan.',
                    ],
                    [
                        'name' => 'Keluarga',
                        'icon' => '👨‍👩‍👧‍👦',
                        'description' => 'Program edukasi keluarga yang menyenangkan untuk orang tua dan anak belajar bersama tentang alam.',
                    ],
                    [
                        'name' => 'Komunitas',
                        'icon' => '👥',
                        'description' => 'Program untuk komunitas lingkungan, pegiat alam, dan kelompok pecinta lingkungan hidup.',
                    ],
                ],
            ],
            'benefits' => [
                'title' => 'Manfaat Program',
                'items' => [
                    'Pengalaman belajar langsung di alam (learning by doing)',
                    'Meningkatkan kesadaran lingkungan sejak dini',
                    'Mengembangkan karakter peduli terhadap kelestarian alam',
                    'Memahami konsep ekologi dan konservasi secara praktis',
                    'Mendapatkan sertifikat partisipasi program edukasi',
                    'Dokumentasi kegiatan untuk portfolio sekolah',
                ],
            ],
            'facilities' => [
                'title' => 'Fasilitas Tersedia',
                'items' => [
                    'Pemandu edukasi berpengalaman',
                    'Materi pembelajaran terstruktur',
                    'Area ber-AC untuk presentasi',
                    'Mini Zoo edukatif',
                    'Area outdoor untuk aktivitas lapangan',
                    'Paket makan siang di The Waterfall Resto',
                    'Parkir bus dan kendaraan besar',
                    'Musholla dan toilet bersih',
                ],
            ],
            'booking_info' => [
                'title' => 'Informasi & Reservasi',
                'description' => 'Program wisata edukasi tersedia untuk grup minimal 20 orang. Untuk informasi lebih lanjut dan reservasi, silakan hubungi kami melalui tombol di bawah ini.',
                'cta_text' => 'Hubungi Kami',
            ],
        ];
    }
    
    /**
     * Display School Partners full list page
     *
     * @return View
     */
    public function schoolPartners(): View
    {
        // Query all active school partners ordered by display order, paginated (20 per page).
        // The DB container may be unavailable in a bare local/test runtime, so keep the page alive with an empty paginator.
        try {
            $schoolPartners = \App\Models\SchoolPartner::active()
                ->ordered()
                ->paginate(20);
        } catch (\Throwable $e) {
            \Log::warning('StaticPageController: SchoolPartner lookup failed during partner list page render.', [
                'error' => $e->getMessage(),
            ]);

            $schoolPartners = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                20,
                1
            );
        }
        
        // Generate breadcrumbs: Home > Wisata Edukasi > Sekolah Mitra
        $breadcrumbs = $this->breadcrumbService->generate('education.school-partners', null, [
            'name' => 'Sekolah Mitra',
            'parent' => [
                'name' => 'Wisata Edukasi',
                'route' => 'education',
            ],
        ]);
        
        // Generate SEO metadata
        $seoData = $this->seoService->generateMetadata('school-partners', [
            'title' => 'Sekolah Mitra Kami - Wisata Edukasi Godong Ijo | The Waterfall',
            'description' => 'Daftar sekolah mitra yang telah mempercayai Godong Ijo untuk program wisata edukasi. Bergabunglah dengan ratusan sekolah yang telah merasakan pengalaman belajar yang menyenangkan.',
            'route' => 'education.school-partners',
            'breadcrumbs' => $breadcrumbs,
        ]);
        
        // Get navigation data
        $navigation = $this->navigationService->getMainNavigation();
        $cta = $this->navigationService->getCTA();
        $currentRoute = Route::currentRouteName();
        
        return view('pages.school-partners', [
            'schoolPartners' => $schoolPartners,
            'breadcrumbs' => $breadcrumbs,
            'seoData' => $seoData,
            'navigation' => $navigation,
            'cta' => $cta,
            'currentRoute' => $currentRoute,
        ]);
    }
    
    /**
     * Get Kontak page data
     *
     * @return array
     */
    private function getContactData(): array
    {
        return [
            'hero' => [
                'title' => 'Hubungi Kami',
                'subtitle' => 'Kami Siap Membantu Anda',
                'description' => 'Ada pertanyaan atau ingin melakukan reservasi? Tim kami siap membantu Anda merencanakan kunjungan yang tak terlupakan ke Godong Ijo.',
                'image' => asset('images/placeholders/asset 3.png'),
                'alt' => 'Hubungi Godong Ijo',
            ],
            'contact_info' => [
                [
                    'icon' => '📍',
                    'title' => 'Alamat',
                    'content' => 'Jalan Cinangka Raya Km 10 No. 60, Serua, Bojongsari, Depok, Jawa Barat 16517',
                    'link' => 'https://maps.google.com/?q=Godong+Ijo+Depok',
                    'link_text' => 'Buka di Google Maps',
                ],
                [
                    'icon' => '📞',
                    'title' => 'Telepon',
                    'content' => '+62 21 7471 0678',
                    'link' => 'tel:+622174710678',
                    'link_text' => 'Hubungi Sekarang',
                ],
                [
                    'icon' => '📧',
                    'title' => 'Email',
                    'content' => 'info@godongijo.com',
                    'link' => 'mailto:info@godongijo.com',
                    'link_text' => 'Kirim Email',
                ],
                [
                    'icon' => '⏰',
                    'title' => 'Jam Operasional',
                    'content' => 'Senin - Minggu: 09:00 - 21:00 WIB',
                    'link' => null,
                    'link_text' => null,
                ],
            ],
            'social_media' => [
                'title' => 'Ikuti Kami',
                'platforms' => [
                    [
                        'name' => 'Instagram',
                        'icon' => 'instagram',
                        'handle' => '@godongijo',
                        'link' => 'https://www.instagram.com/godongijo',
                    ],
                    [
                        'name' => 'Facebook',
                        'icon' => 'facebook',
                        'handle' => 'Godong Ijo',
                        'link' => 'https://www.facebook.com/godongijo',
                    ],
                    [
                        'name' => 'WhatsApp',
                        'icon' => 'whatsapp',
                        'handle' => '+62 812-3456-7890',
                        'link' => 'https://wa.me/6281234567890',
                    ],
                ],
            ],
            'map' => [
                'title' => 'Lokasi Kami',
                'embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.0!2d106.7!3d-6.4!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwMjQnMDAuMCJTIDEwNsKwNDInMDAuMCJF!5e0!3m2!1sen!2sid!4v1234567890',
                'directions_link' => 'https://maps.google.com/?q=Godong+Ijo+Depok',
            ],
            'form' => [
                'title' => 'Kirim Pesan',
                'description' => 'Isi formulir di bawah ini dan kami akan menghubungi Anda sesegera mungkin.',
                'fields' => [
                    [
                        'name' => 'name',
                        'label' => 'Nama Lengkap',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'Masukkan nama lengkap Anda',
                    ],
                    [
                        'name' => 'email',
                        'label' => 'Email',
                        'type' => 'email',
                        'required' => true,
                        'placeholder' => 'contoh@email.com',
                    ],
                    [
                        'name' => 'phone',
                        'label' => 'Nomor Telepon',
                        'type' => 'tel',
                        'required' => true,
                        'placeholder' => '08xx xxxx xxxx',
                    ],
                    [
                        'name' => 'subject',
                        'label' => 'Subjek',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'Perihal pesan Anda',
                    ],
                    [
                        'name' => 'message',
                        'label' => 'Pesan',
                        'type' => 'textarea',
                        'required' => true,
                        'placeholder' => 'Tulis pesan Anda di sini...',
                        'rows' => 5,
                    ],
                ],
                'submit_text' => 'Kirim Pesan',
            ],
            'quick_links' => [
                'title' => 'Pertanyaan Umum?',
                'items' => [
                    [
                        'question' => 'Berapa harga tiket masuk?',
                        'answer' => 'Untuk informasi harga dan paket, silakan lihat halaman Paket Wisata atau hubungi kami langsung.',
                    ],
                    [
                        'question' => 'Apakah perlu reservasi?',
                        'answer' => 'Reservasi sangat direkomendasikan terutama untuk akhir pekan, hari libur, dan grup besar untuk memastikan ketersediaan.',
                    ],
                    [
                        'question' => 'Apakah tersedia parkir?',
                        'answer' => 'Ya, kami menyediakan area parkir luas untuk mobil, motor, dan bus secara gratis.',
                    ],
                ],
            ],
        ];
    }
}
