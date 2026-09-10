<?php

namespace App\Services;

use Illuminate\Support\Facades\Route;

class BreadcrumbService
{
    /**
     * Generate breadcrumb items for a given route
     *
     * @param string $route Current route name
     * @param string|null $slug Optional slug parameter for dynamic routes
     * @param array $data Optional additional data (e.g., page name)
     * @return array Breadcrumb items array
     */
    public function generate(string $route, ?string $slug = null, array $data = []): array
    {
        $breadcrumbs = [
            [
                'label' => 'Home',
                'url' => route('landing'),
                'current' => false,
            ],
        ];
        
        // Generate breadcrumbs based on route
        switch ($route) {
            case 'destination.show':
                $breadcrumbs[] = [
                    'label' => 'Destinasi',
                    'url' => null, // No parent listing page
                    'current' => false,
                ];
                $breadcrumbs[] = [
                    'label' => $data['name'] ?? $this->getDestinationNameFromSlug($slug),
                    'url' => null,
                    'current' => true,
                ];
                break;
                
            case 'package.show':
                $breadcrumbs[] = [
                    'label' => 'Paket Wisata',
                    'url' => null, // No parent listing page
                    'current' => false,
                ];
                $breadcrumbs[] = [
                    'label' => $data['name'] ?? $this->getPackageNameFromSlug($slug),
                    'url' => null,
                    'current' => true,
                ];
                break;
                
            case 'education':
                $breadcrumbs[] = [
                    'label' => 'Wisata Edukasi',
                    'url' => null,
                    'current' => true,
                ];
                break;
                
            case 'about':
                $breadcrumbs[] = [
                    'label' => 'Tentang Kami',
                    'url' => null,
                    'current' => true,
                ];
                break;
                
            case 'contact':
                $breadcrumbs[] = [
                    'label' => 'Kontak',
                    'url' => null,
                    'current' => true,
                ];
                break;
                
            default:
                // For any other routes, just show Home
                break;
        }
        
        return $breadcrumbs;
    }
    
    /**
     * Generate BreadcrumbList structured data (JSON-LD)
     *
     * @param array $breadcrumbs Breadcrumb items array
     * @return array JSON-LD structured data
     */
    public function getStructuredData(array $breadcrumbs): array
    {
        $items = [];
        $position = 1;
        
        foreach ($breadcrumbs as $breadcrumb) {
            // Add items with URLs (clickable breadcrumbs)
            if (!empty($breadcrumb['url'])) {
                $items[] = [
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $breadcrumb['label'],
                    'item' => $breadcrumb['url'],
                ];
            }
        }
        
        // Add the current page as the last item (no URL)
        $currentPage = collect($breadcrumbs)->firstWhere('current', true);
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
     * Generate breadcrumbs for package category pages
     * 
     * Requirements: 8.1, 8.2, 8.3, 8.4, 8.5, 8.6
     *
     * @param string $categoryName Display name for the category
     * @return array Breadcrumb items array
     */
    public function generateForCategory(string $categoryName): array
    {
        return [
            [
                'label' => 'Home',
                'url' => route('landing'),
                'current' => false,
            ],
            [
                'label' => 'Paket Wisata',
                'url' => null,
                'current' => false,
            ],
            [
                'label' => $categoryName,
                'url' => null,
                'current' => true,
            ],
        ];
    }
    
    /**
     * Get human-readable destination name from slug
     *
     * @param string|null $slug Destination slug
     * @return string Destination name
     */
    private function getDestinationNameFromSlug(?string $slug): string
    {
        return match ($slug) {
            'the-waterfall' => 'The Waterfall',
            'monster-fish' => 'Monster Fish',
            'vertical-garden' => 'Vertical Garden',
            default => 'Destinasi',
        };
    }
    
    /**
     * Get human-readable package name from slug
     *
     * @param string|null $slug Package slug
     * @return string Package name
     */
    private function getPackageNameFromSlug(?string $slug): string
    {
        // Convert slug to title case
        if ($slug) {
            return ucwords(str_replace('-', ' ', $slug));
        }
        
        return 'Paket Wisata';
    }
}
