<?php

namespace App\Http\Controllers;

use App\Models\PaketWisata;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

/**
 * SitemapController
 * 
 * Generates XML sitemap for SEO optimization.
 * 
 * Requirements:
 * - 7.8: Generate sitemap.xml with all public pages
 */
class SitemapController extends Controller
{
    /**
     * Generate and return sitemap XML
     *
     * @return Response
     */
    public function index(): Response
    {
        // Define static pages
        $staticPages = [
            [
                'url' => route('landing'),
                'lastmod' => now()->toW3cString(),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'url' => route('education'),
                'lastmod' => now()->toW3cString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
            [
                'url' => route('contact'),
                'lastmod' => now()->toW3cString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ],
        ];
        
        // Define destination pages
        $destinationSlugs = ['the-waterfall', 'monster-fish', 'vertical-garden'];
        $destinations = [];
        
        foreach ($destinationSlugs as $slug) {
            $destinations[] = [
                'url' => route('destination.show', ['slug' => $slug]),
                'lastmod' => now()->toW3cString(),
                'changefreq' => 'weekly',
                'priority' => '0.9',
            ];
        }
        
        // Get package pages from database
        $packages = [];
        try {
            $paketWisata = PaketWisata::where('is_active', true)->get();
            
            foreach ($paketWisata as $paket) {
                $slug = Str::slug($paket->nama_paket);
                $packages[] = [
                    'url' => route('package.show', ['slug' => $slug]),
                    'lastmod' => $paket->updated_at ? $paket->updated_at->toW3cString() : now()->toW3cString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.9',
                ];
            }
        } catch (\Exception $e) {
            \Log::error('SitemapController: Failed to fetch packages', [
                'error' => $e->getMessage(),
            ]);
        }
        
        // Merge all URLs
        $urls = array_merge($staticPages, $destinations, $packages);
        
        // Generate XML
        $xml = $this->generateXML($urls);
        
        // Return XML response
        return response($xml, 200)
            ->header('Content-Type', 'application/xml')
            ->header('Cache-Control', 'public, max-age=3600'); // Cache for 1 hour
    }
    
    /**
     * Generate sitemap XML from URLs array
     *
     * @param array $urls Array of URL data
     * @return string XML string
     */
    private function generateXML(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        foreach ($urls as $urlData) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($urlData['url']) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . $urlData['lastmod'] . '</lastmod>' . "\n";
            $xml .= '    <changefreq>' . $urlData['changefreq'] . '</changefreq>' . "\n";
            $xml .= '    <priority>' . $urlData['priority'] . '</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }
}
