<?php

namespace App\Services;

use App\Models\PaketWisata;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class NavigationService
{
    /**
     * Get the main navigation structure with dynamic packages loaded
     *
     * @return array Navigation items array
     */
    public function getMainNavigation(): array
    {
        // Load navigation configuration from config file
        $navigationConfig = config('navigation.main', []);
        
        // Process navigation items and load dynamic content
        $processedNavigation = array_map(function ($item) {
            // Check if this item has dynamic children (Paket Wisata)
            if (isset($item['children']) && $item['children'] === 'dynamic') {
                $item['children'] = $this->loadDynamicPackages();
            }

            return $this->normaliseNavigationItem($item);
        }, $navigationConfig);
        
        return $processedNavigation;
    }

    /**
     * Normalize menu payloads that may come from legacy landing page shapes.
     *
     * @param array $item
     * @return array
     */
    private function normaliseNavigationItem(array $item): array
    {
        // Some screens still provide the legacy anchor-style contract.
        // We need to honour both shapes during render.
        if (isset($item['href']) && !isset($item['url'])) {
            $item['url'] = $item['href'];
        }

        if (isset($item['children']) && is_array($item['children'])) {
            $item['children'] = array_map(function ($child) {
                if (isset($child['href']) && !isset($child['url'])) {
                    $child['url'] = $child['href'];
                }

                return $child;
            }, $item['children']);
        }

        if (!isset($item['children']) || !is_array($item['children'])) {
            $item['children'] = [];
        }

        return $item;
    }
    
    /**
     * Get the CTA button configuration
     *
     * @return array CTA configuration
     */
    public function getCTA(): array
    {
        return config('navigation.cta', [
            'label' => 'Pesan Sekarang',
            'action' => 'openBookingModal',
        ]);
    }
    
    /**
     * Check if a menu item is currently active
     *
     * @param string $route The route name to check
     * @param string|null $slug Optional slug parameter for dynamic routes
     * @return bool True if the route is active
     */
    public function isActive(string $route, ?string $slug = null): bool
    {
        // Get current route name
        $currentRoute = Route::currentRouteName();
        
        // Handle homepage special case
        if ($route === 'landing' && $currentRoute === 'landing') {
            return true;
        }
        
        // Handle packages.category routes (category pages)
        if ($route === 'packages.category' && $slug !== null) {
            return $this->isCategoryActive($slug);
        }
        
        // For dynamic routes (destinations, packages), check both route and slug
        if ($slug !== null) {
            // Get current route parameter if it exists
            $currentSlug = request()->route('slug');
            
            return $currentRoute === $route && $currentSlug === $slug;
        }
        
        // For static routes, just check route name match
        return $currentRoute === $route;
    }
    
    /**
     * Get the parent menu key for the current active child route
     *
     * @param string $route Current route name
     * @return string|null Parent menu label or null if no parent
     */
    public function getActiveParent(string $route): ?string
    {
        // Get current route name
        $currentRoute = Route::currentRouteName();
        
        // Map child routes to their parent menu items
        $parentMap = [
            'destination.show' => 'Destinasi',
            'package.show' => 'Paket Wisata',
        ];
        
        // Return parent if current route matches
        return $parentMap[$currentRoute] ?? null;
    }
    
    /**
     * Check if a category route is currently active
     *
     * Requirements: 6.1, 6.2, 6.3, 6.4
     *
     * @param string $categorySlug The category slug to check (e.g., 'the-waterfall-resto')
     * @return bool True if the current page is the specified category page
     */
    public function isCategoryActive(string $categorySlug): bool
    {
        // Get current route name
        $currentRoute = Route::currentRouteName();
        
        // Check if we're on the packages.category route
        if ($currentRoute !== 'packages.category') {
            return false;
        }
        
        // Get the current category parameter from the route
        $currentCategory = request()->route('category');
        
        // Compare current category with provided slug
        return $currentCategory === $categorySlug;
    }
    
    /**
     * Load dynamic packages from database for Paket Wisata dropdown
     *
     * @return array Array of package menu items
     */
    private function loadDynamicPackages(): array
    {
        try {
            // Fetch active packages from database
            $packages = PaketWisata::where('is_active', true)
                ->orderBy('nama_paket', 'asc')
                ->get();
            
            // Transform packages into navigation menu format
            return $packages->map(function ($package) {
                // Generate slug from package name
                $slug = Str::slug($package->nama_paket);
                
                return [
                    'label' => $package->nama_paket,
                    'route' => 'package.show',
                    'url' => "/paket/{$slug}",
                    'slug' => $slug,
                ];
            })->toArray();
            
        } catch (\Exception $e) {
            // Log error and return empty array as fallback
            \Log::error('NavigationService: Failed to load dynamic packages', [
                'error' => $e->getMessage(),
            ]);
            
            return [];
        }
    }
}
