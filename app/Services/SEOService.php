<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * SEOService
 * 
 * Generates SEO meta tags and structured data for all pages.
 * 
 * Requirements:
 * - 7.1: Generate unique page titles in format "[Page Name] | Godong Ijo"
 * - 7.2: Generate meta descriptions (150-160 characters)
 * - 7.3: Generate Open Graph meta tags for social media sharing
 * - 7.4: Generate Twitter Card meta tags
 * - 7.5: Generate canonical URLs
 * - 7.6: Generate structured data (JSON-LD schema)
 * - 7.7: Include Indonesian language keywords
 * - 7.8: Generate sitemap.xml entries
 */
class SEOService
{
    /**
     * Base URL for the site
     *
     * @var string
     */
    private string $baseUrl;
    
    /**
     * Default image for social media sharing
     *
     * @var string
     */
    private string $defaultImage;
    
    /**
     * Site name
     *
     * @var string
     */
    private string $siteName = 'Godong Ijo';
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->baseUrl = config('app.url', 'http://localhost');
        $this->defaultImage = $this->baseUrl . '/images/godong-ijo-og.jpg';
    }
    
    /**
     * Generate complete metadata for a page
     *
     * @param string $pageType Type of page (home, destination, package, education, about, contact)
     * @param array $data Page-specific data
     * @return array Complete SEO metadata
     */
    public function generateMetadata(string $pageType, array $data): array
    {
        $title = $this->getTitle($pageType, $data);
        $description = $this->getDescription($pageType, $data);
        $canonicalUrl = $this->getCanonicalUrl($data['route'] ?? 'home', $data['slug'] ?? null);
        $image = $data['image'] ?? $this->defaultImage;
        
        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $this->getKeywords($pageType, $data),
            'canonical' => $canonicalUrl,
            'og' => [
                'title' => $title,
                'description' => $description,
                'image' => $image,
                'url' => $canonicalUrl,
                'type' => $this->getOGType($pageType),
            ],
            'twitter' => [
                'card' => 'summary_large_image',
                'title' => $title,
                'description' => $description,
                'image' => $image,
            ],
            'structuredData' => $this->getStructuredDataForPage($pageType, $data),
        ];
    }
    
    /**
     * Get page title in format "[Page Name] | Godong Ijo"
     *
     * @param string $pageType Type of page
     * @param array $data Page-specific data
     * @return string Page title
     */
    public function getTitle(string $pageType, array $data): string
    {
        $pageName = match ($pageType) {
            'home' => 'Wisata Alam & Kuliner',
            'destination' => $data['name'] ?? 'Destinasi',
            'package' => $data['name'] ?? 'Paket Wisata',
            'education' => 'Wisata Edukasi - Ecotainment Godongijo',
            'about' => 'Tentang Kami',
            'contact' => 'Kontak',
            default => 'Halaman',
        };
        
        // Home page gets special treatment - no pipe separator
        if ($pageType === 'home') {
            return "{$pageName} | {$this->siteName}";
        }
        
        return "{$pageName} | {$this->siteName}";
    }
    
    /**
     * Get meta description (150-160 characters)
     *
     * @param string $pageType Type of page
     * @param array $data Page-specific data
     * @return string Meta description
     */
    public function getDescription(string $pageType, array $data): string
    {
        $description = match ($pageType) {
            'home' => 'Nikmati pengalaman wisata alam dan kuliner terbaik di Godong Ijo. Destinasi keluarga dengan air terjun, kolam memancing monster fish, dan taman vertikal hijau.',
            'destination' => $this->truncateDescription(
                $data['description'] ?? 'Kunjungi destinasi wisata alam yang indah dan menarik di Godong Ijo. Pengalaman tak terlupakan untuk keluarga.',
                150,
                160
            ),
            'package' => $this->truncateDescription(
                $data['description'] ?? 'Paket wisata lengkap dengan berbagai fasilitas dan aktivitas menarik. Cocok untuk keluarga, outing, dan acara khusus.',
                150,
                160
            ),
            'education' => 'Program wisata edukasi Ecotainment Godongijo untuk sekolah, pelajar, dan keluarga dengan aktivitas lingkungan, sains, dan seni di Depok.',
            'about' => 'Godong Ijo adalah destinasi wisata alam dan kuliner yang menawarkan pengalaman unik dengan konsep eco-luxury. Ketahui lebih lanjut tentang kami.',
            'contact' => 'Hubungi Godong Ijo untuk informasi, reservasi, dan pertanyaan. Kami siap membantu merencanakan kunjungan Anda ke destinasi wisata alam kami.',
            default => 'Kunjungi Godong Ijo untuk pengalaman wisata alam dan kuliner yang tak terlupakan.',
        };
        
        return $description;
    }
    
    /**
     * Get structured data based on schema type
     *
     * @param string $schemaType Schema type (TouristAttraction, Product, Organization, BreadcrumbList)
     * @param array $data Schema-specific data
     * @return array JSON-LD structured data
     */
    public function getStructuredData(string $schemaType, array $data): array
    {
        return match ($schemaType) {
            'TouristAttraction' => $this->generateTouristAttractionSchema($data),
            'Product' => $this->generateProductSchema($data),
            'Organization' => $this->generateOrganizationSchema($data),
            'BreadcrumbList' => $this->generateBreadcrumbListSchema($data),
            default => [],
        };
    }
    
    /**
     * Get canonical URL for a route
     *
     * @param string $route Route name
     * @param string|null $slug Optional slug parameter
     * @return string Canonical URL
     */
    public function getCanonicalUrl(string $route, ?string $slug = null): string
    {
        try {
            if ($slug === null) {
                return route($route, [], true);
            }

            $routeParameters = match ($route) {
                'packages.category' => ['category' => $slug],
                default => ['slug' => $slug],
            };

            return route($route, $routeParameters, true);
        } catch (\Exception $e) {
            // Fallback to base URL if route generation fails
            return $this->baseUrl;
        }
    }
    
    /**
     * Get keywords for a page
     *
     * @param string $pageType Type of page
     * @param array $data Page-specific data
     * @return array Keywords array
     */
    private function getKeywords(string $pageType, array $data): array
    {
        $baseKeywords = ['godong ijo', 'wisata alam', 'wisata keluarga', 'kuliner'];
        
        $pageSpecificKeywords = match ($pageType) {
            'home' => ['air terjun', 'kolam memancing', 'taman vertikal', 'restoran'],
            'destination' => $this->getDestinationKeywords($data),
            'package' => ['paket wisata', 'rekreasi keluarga', 'outing', 'acara'],
            'education' => ['wisata edukasi', 'ecotainment godongijo', 'fieldtrip sekolah', 'edukasi lingkungan', 'pembelajaran'],
            'about' => ['tentang kami', 'profil', 'visi misi'],
            'contact' => ['kontak', 'hubungi kami', 'lokasi', 'reservasi'],
            default => [],
        };
        
        return array_merge($baseKeywords, $pageSpecificKeywords);
    }
    
    /**
     * Get destination-specific keywords
     *
     * @param array $data Destination data
     * @return array Keywords
     */
    private function getDestinationKeywords(array $data): array
    {
        $slug = $data['slug'] ?? '';
        
        return match ($slug) {
            'the-waterfall' => ['air terjun', 'waterfall', 'kolam renang', 'restoran'],
            'monster-fish' => ['kolam memancing', 'monster fish', 'fishing', 'ikan raksasa'],
            'vertical-garden' => ['taman vertikal', 'vertical garden', 'taman hijau'],
            default => ['destinasi', 'tempat wisata'],
        };
    }
    
    /**
     * Get Open Graph type for page
     *
     * @param string $pageType Type of page
     * @return string OG type
     */
    private function getOGType(string $pageType): string
    {
        return match ($pageType) {
            'home' => 'website',
            'destination' => 'place',
            'package' => 'product',
            default => 'website',
        };
    }
    
    /**
     * Truncate description to fit within character limits
     *
     * @param string $text Text to truncate
     * @param int $minLength Minimum length
     * @param int $maxLength Maximum length
     * @return string Truncated text
     */
    private function truncateDescription(string $text, int $minLength, int $maxLength): string
    {
        // If text is already within range, return as is
        if (strlen($text) <= $maxLength) {
            return $text;
        }
        
        // Truncate at word boundary near maxLength
        $truncated = Str::limit($text, $maxLength, '');
        
        // Find last space to avoid cutting words
        $lastSpace = strrpos($truncated, ' ');
        if ($lastSpace !== false && $lastSpace >= $minLength) {
            $truncated = substr($truncated, 0, $lastSpace);
        }
        
        return rtrim($truncated, '.,;:') . '...';
    }
    
    /**
     * Get all structured data for a page
     *
     * @param string $pageType Type of page
     * @param array $data Page data
     * @return array Array of JSON-LD schemas
     */
    private function getStructuredDataForPage(string $pageType, array $data): array
    {
        $schemas = [];
        
        // Add page-specific schema
        switch ($pageType) {
            case 'destination':
                $schemas[] = $this->getStructuredData('TouristAttraction', $data);
                break;
            case 'package':
                $schemas[] = $this->getStructuredData('Product', $data);
                break;
            case 'about':
                $schemas[] = $this->getStructuredData('Organization', $data);
                break;
        }
        
        // Add breadcrumb schema for sub-pages
        if ($pageType !== 'home' && isset($data['breadcrumbs'])) {
            $schemas[] = $this->getStructuredData('BreadcrumbList', $data['breadcrumbs']);
        }
        
        return $schemas;
    }
    
    /**
     * Generate TouristAttraction schema
     *
     * @param array $data Destination data
     * @return array JSON-LD schema
     */
    private function generateTouristAttractionSchema(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'TouristAttraction',
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? '',
            'image' => $data['image'] ?? $this->defaultImage,
            'url' => $data['url'] ?? $this->baseUrl,
        ];
        
        // Add address if available
        if (isset($data['address'])) {
            $schema['address'] = [
                '@type' => 'PostalAddress',
                'addressLocality' => $data['address']['locality'] ?? 'Semarang',
                'addressRegion' => $data['address']['region'] ?? 'Jawa Tengah',
                'addressCountry' => 'ID',
            ];
        }
        
        // Add opening hours if available
        if (isset($data['openingHours'])) {
            $schema['openingHours'] = $data['openingHours'];
        }
        
        return $schema;
    }
    
    /**
     * Generate Product schema for packages
     *
     * @param array $data Package data
     * @return array JSON-LD schema
     */
    private function generateProductSchema(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? '',
            'image' => $data['image'] ?? $this->defaultImage,
            'url' => $data['url'] ?? $this->baseUrl,
        ];
        
        // Add offers if price available
        if (isset($data['price'])) {
            $schema['offers'] = [
                '@type' => 'Offer',
                'price' => $data['price'],
                'priceCurrency' => 'IDR',
                'availability' => 'https://schema.org/InStock',
                'url' => $data['url'] ?? $this->baseUrl,
            ];
        }
        
        // Add brand
        $schema['brand'] = [
            '@type' => 'Brand',
            'name' => $this->siteName,
        ];
        
        return $schema;
    }
    
    /**
     * Generate Organization schema
     *
     * @param array $data Organization data
     * @return array JSON-LD schema
     */
    private function generateOrganizationSchema(array $data): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $this->siteName,
            'url' => $this->baseUrl,
            'logo' => $this->baseUrl . '/images/logo.png',
            'description' => $data['description'] ?? 'Destinasi wisata alam dan kuliner dengan konsep eco-luxury',
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Semarang',
                'addressRegion' => 'Jawa Tengah',
                'addressCountry' => 'ID',
            ],
            'sameAs' => $data['socialMedia'] ?? [],
        ];
    }
    
    /**
     * Generate BreadcrumbList schema
     *
     * @param array $breadcrumbs Breadcrumb items
     * @return array JSON-LD schema
     */
    private function generateBreadcrumbListSchema(array $breadcrumbs): array
    {
        $items = [];
        $position = 1;
        
        foreach ($breadcrumbs as $breadcrumb) {
            // Only add items with URLs (not the current page)
            if (!empty($breadcrumb['url'])) {
                $items[] = [
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $breadcrumb['label'],
                    'item' => $breadcrumb['url'],
                ];
            }
        }
        
        // Add the current page as the last item
        $currentPage = end($breadcrumbs);
        if ($currentPage) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $currentPage['label'],
            ];
        }
        
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }
    
    /**
     * Generate SEO metadata for package category pages
     * 
     * Requirements:
     * - 7.1: Generate unique page titles in format "{Category Name} - Paket Wisata | Godong Ijo"
     * - 7.2: Generate meta descriptions combining category name with "Pilih Paket Sesuai Kebutuhan"
     * - 7.3: Generate Open Graph metadata (og:title, og:description, og:image, og:url)
     * - 7.4: Generate Twitter Card metadata
     * - 7.5: Generate canonical URL using route('packages.category', ['category' => $categorySlug])
     * - 7.6: Select OG image: first package photo if available, else default category image
     * - 7.7: Generate Product schema for each package in collection
     *
     * @param string $categoryName Display name of the category
     * @param string $categorySlug URL slug for the category
     * @param array $packages Package collection for image selection and structured data
     * @return array Complete SEO metadata
     */
    public function generateCategoryMetadata(
        string $categoryName,
        string $categorySlug,
        array $packages
    ): array {
        // Generate title: "{Category Name} - Paket Wisata | Godong Ijo"
        $title = "{$categoryName} - Paket Wisata | {$this->siteName}";
        
        // Generate description combining category name with "Pilih Paket Sesuai Kebutuhan"
        $description = "Pilih Paket Sesuai Kebutuhan - {$categoryName}. Dari kuliner keluarga hingga event perusahaan, kami punya paket untuk Anda.";
        
        // Generate canonical URL using route helper
        $canonicalUrl = route('packages.category', ['category' => $categorySlug]);
        
        // Select OG image: first package photo if available, else default category image
        $ogImage = $this->defaultImage;
        if (!empty($packages) && is_array($packages)) {
            foreach ($packages as $package) {
                if (isset($package['foto']) && !empty($package['foto'])) {
                    $ogImage = asset($package['foto']);
                    break;
                } elseif (is_object($package) && isset($package->foto) && !empty($package->foto)) {
                    $ogImage = asset($package->foto);
                    break;
                }
            }
        }
        
        // Generate Open Graph metadata
        $openGraph = [
            'title' => $title,
            'description' => $description,
            'image' => $ogImage,
            'url' => $canonicalUrl,
            'type' => 'website',
        ];
        
        // Generate Twitter Card metadata
        $twitterCard = [
            'card' => 'summary_large_image',
            'title' => $title,
            'description' => $description,
            'image' => $ogImage,
        ];
        
        // Generate Product schema for each package in collection
        $productSchemas = [];
        if (!empty($packages)) {
            $position = 1;
            foreach ($packages as $package) {
                $packageData = is_object($package) ? (array) $package : $package;
                
                $productSchema = [
                    '@type' => 'Product',
                    'position' => $position++,
                    'name' => $packageData['nama_paket'] ?? '',
                    'description' => $packageData['deskripsi'] ?? '',
                ];
                
                // Add image if available
                if (!empty($packageData['foto'])) {
                    $productSchema['image'] = asset($packageData['foto']);
                }
                
                // Add offers if price available
                if (isset($packageData['harga']) && $packageData['harga'] > 0) {
                    $productSchema['offers'] = [
                        '@type' => 'Offer',
                        'price' => (string) $packageData['harga'],
                        'priceCurrency' => 'IDR',
                        'availability' => 'https://schema.org/InStock',
                        'url' => $canonicalUrl,
                    ];
                }
                
                // Add brand
                $productSchema['brand'] = [
                    '@type' => 'Brand',
                    'name' => $this->siteName,
                ];
                
                $productSchemas[] = $productSchema;
            }
        }
        
        // Create ItemList structured data wrapping all products
        $structuredData = [];
        if (!empty($productSchemas)) {
            $structuredData[] = [
                '@context' => 'https://schema.org',
                '@type' => 'ItemList',
                'name' => $categoryName,
                'description' => $description,
                'numberOfItems' => count($productSchemas),
                'itemListElement' => $productSchemas,
            ];
        }
        
        // Return complete metadata array
        return [
            'title' => $title,
            'description' => $description,
            'keywords' => ['godong ijo', 'paket wisata', 'wisata keluarga', 'kuliner', strtolower($categoryName)],
            'canonical' => $canonicalUrl,
            'og' => $openGraph,
            'twitter' => $twitterCard,
            'structuredData' => $structuredData,
        ];
    }
}
