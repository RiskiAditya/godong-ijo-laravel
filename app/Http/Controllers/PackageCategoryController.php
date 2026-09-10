<?php

namespace App\Http\Controllers;

use App\Services\SEOService;
use App\Services\BreadcrumbService;
use App\Services\NavigationService;
use App\Support\PackageTypeCatalog;
use App\Support\PrivateRoomPackageCatalog;

/**
 * PackageCategoryController
 * 
 * Handles HTTP requests for package category pages and orchestrates data retrieval.
 * 
 * Requirements:
 * - 1.1: Map route name 'packages.category' to URL pattern '/paket/{category}'
 * - 1.2: Display The Waterfall Resto category page when visiting '/paket/the-waterfall-resto'
 * - 1.3: Display Private Room category page when visiting '/paket/private-room'
 * - 1.4: Display Fishing Lake category page when visiting '/paket/fishing-lake'
 * - 2.2: Map URL slug to jenis_paket ENUM values
 * - 2.3: Map 'the-waterfall-resto' to 'The Waterfall Resto'
 * - 2.4: Map 'private-room' to 'Private Room'
 * - 3.3: Display specific category name as secondary heading
 */
class PackageCategoryController extends Controller
{
    /**
     * SEO service instance
     */
    protected SEOService $seoService;
    
    /**
     * Breadcrumb service instance
     */
    protected BreadcrumbService $breadcrumbService;
    
    /**
     * Navigation service instance
     */
    protected NavigationService $navigationService;
    
    /**
     * Slug-to-enum mapping for category slugs to jenis_paket ENUM values
     * 
     * @var array<string, string>
     */
    private array $categoryMap = [
        'the-waterfall-resto' => 'The Waterfall Resto',
        'private-room' => 'Private Room',
        'fishing-lake' => 'Fishing Lake',
    ];

    private function canonicalizeCategorySlug(string $slug): string
    {
        return match ($slug) {
            'the-waterfall-resto' => PackageTypeCatalog::normalize('the_waterfall_resto'),
            'private-room' => PackageTypeCatalog::normalize('private_room'),
            'fishing-lake' => PackageTypeCatalog::normalize('fishing_lake'),
            default => PackageTypeCatalog::normalize($slug),
        };
    }
    
    /**
     * Category display metadata (name, description, default_image)
     * 
     * @var array<string, array{name: string, description: string, default_image: string}>
     */
    private array $categoryMetadata = [
        'the-waterfall-resto' => [
            'name' => 'Paket The Waterfall Resto',
            'description' => 'Pengalaman kuliner ekologis dengan konsep Dine in Nature',
            'default_image' => 'images/The Waterfall Resto Images/Tentang-TWF-2048x769.webp',
        ],
        'private-room' => [
            'name' => 'Paket Private Room',
            'description' => 'Ruang privat untuk acara spesial dan gathering',
            'default_image' => 'images/Private Images/BCA-Gathering-2048x1137.webp',
        ],
        'fishing-lake' => [
            'name' => 'Paket Fishing Lake',
            'description' => 'Sport fishing dengan ikan monster di kolam eksklusif',
            'default_image' => 'images/placeholders/Redtail-Catfish-Ikan-Predator-Amerika-Selatan-1536x853.webp',
        ],
    ];
    
    /**
     * Constructor - inject dependencies
     * 
     * @param SEOService $seoService
     * @param BreadcrumbService $breadcrumbService
     * @param NavigationService $navigationService
     */
    public function __construct(
        SEOService $seoService,
        BreadcrumbService $breadcrumbService,
        NavigationService $navigationService
    ) {
        $this->seoService = $seoService;
        $this->breadcrumbService = $breadcrumbService;
        $this->navigationService = $navigationService;
    }
    
    /**
     * Display category page
     * 
     * Requirements: 1.2, 1.3, 1.4, 2.1, 2.5, 2.6, 10.3
     * 
     * @param string $category URL slug (the-waterfall-resto, private-room, fishing-lake)
     * @return \Illuminate\View\View
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException if category invalid
     */
    public function show(string $category)
    {
        // Map URL slug to jenis_paket ENUM value
        try {
            $jenisPaket = $this->mapSlugToJenisPaket($category);
            $jenisPaket = PackageTypeCatalog::normalize($jenisPaket);
        } catch (\InvalidArgumentException $e) {
            abort(404, $e->getMessage());
        }
        
        // Query PaketWisata filtered by jenis_paket and is_active
        // Wrap in 5-minute cache with sanitized key
        $cacheKey = 'packages.category.' . md5($jenisPaket);
        $packages = \Cache::remember(
            $cacheKey,
            now()->addMinutes(5),
            function () use ($jenisPaket) {
                return \App\Models\PaketWisata::where('jenis_paket', $jenisPaket)
                    ->where('is_active', true)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        );
        
        // Get category metadata
        $categoryMeta = $this->getCategoryMetadata($category);
        
        // Generate SEO metadata
        $seoData = $this->seoService->generateCategoryMetadata(
            $categoryMeta['name'],
            $category,
            $packages->toArray()
        );
        
        // Generate breadcrumbs
        $breadcrumbs = $this->breadcrumbService->generateForCategory($categoryMeta['name']);
        
        // Get navigation items with active state
        $navigation = $this->navigationService->getMainNavigation();
        $cta = $this->navigationService->getCTA();
        
        // Return view with all data
        return view('categories.show', [
            'category' => $category,
            'categoryMeta' => $categoryMeta,
            'packages' => $packages,
            'seoData' => $seoData,
            'breadcrumbs' => $breadcrumbs,
            'navigation' => $navigation,
            'cta' => $cta,
            'privateRoomCards' => $category === 'private-room'
                ? PrivateRoomPackageCatalog::cards()
                : [],
        ]);
    }
    
    /**
     * Map URL slug to jenis_paket ENUM value
     * 
     * Validates: Requirements 2.2, 2.3, 2.4
     * 
     * @param string $slug URL slug (the-waterfall-resto, private-room, fishing-lake)
     * @return string jenis_paket ENUM value
     * @throws \InvalidArgumentException if slug not found in categoryMap
     */
    private function mapSlugToJenisPaket(string $slug): string
    {
        if (!isset($this->categoryMap[$slug])) {
            throw new \InvalidArgumentException("Invalid category slug: {$slug}");
        }
        
        return $this->categoryMap[$slug];
    }
    
    /**
     * Get category display metadata
     * 
     * Returns metadata array containing name, description, and default_image
     * for the given category slug. Returns default values if slug not found.
     * 
     * Requirements:
     * - 3.3: Display specific category name as secondary heading
     * - 7.6: Use default category image when no package photos exist
     * 
     * @param string $slug URL slug (the-waterfall-resto, private-room, fishing-lake)
     * @return array{name: string, description: string, default_image: string} Category metadata
     */
    private function getCategoryMetadata(string $slug): array
    {
        // Check if slug exists in $categoryMetadata
        if (isset($this->categoryMetadata[$slug])) {
            return $this->categoryMetadata[$slug];
        }
        
        // Return default values if slug not found
        return [
            'name' => 'Paket Wisata',
            'description' => 'Paket wisata pilihan untuk pengalaman terbaik Anda',
            'default_image' => 'images/placeholders/default-package.webp',
        ];
    }
}
