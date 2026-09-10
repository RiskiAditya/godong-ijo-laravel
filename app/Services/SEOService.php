<?php

namespace App\Services;

class SEOService
{
    private string $siteName = 'Godong Ijo';

    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('app.url', url('/')), '/');
    }

    public function generateMetadata(string $pageType, array $data): array
    {
        $title = $data['title'] ?? $this->getTitle($pageType, $data);
        $description = $data['description'] ?? $this->getDescription($pageType, $data);
        $canonical = $this->getCanonicalUrl($data['route'] ?? 'landing', $data['slug'] ?? null);
        $image = $data['image'] ?? asset('images/placeholders/asset 3.png');

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $this->getKeywords($pageType),
            'canonical' => $canonical,
            'og' => [
                'title' => $title,
                'description' => $description,
                'image' => $image,
                'url' => $canonical,
                'type' => $pageType === 'package' ? 'product' : 'website',
            ],
            'twitter' => [
                'card' => 'summary_large_image',
                'title' => $title,
                'description' => $description,
                'image' => $image,
            ],
            'structuredData' => [],
        ];
    }

    public function generateCategoryMetadata(string $name, string $slug, array $packages = []): array
    {
        return $this->generateMetadata('package', [
            'title' => $name.' | '.$this->siteName,
            'description' => 'Jelajahi pilihan paket '.$name.' di '.$this->siteName.'.',
            'route' => 'packages.category',
            'slug' => $slug,
        ]);
    }

    public function getCanonicalUrl(string $route, ?string $slug = null): string
    {
        try {
            if ($slug === null) {
                return route($route, [], true);
            }

            $parameter = $route === 'packages.category' ? ['category' => $slug] : ['slug' => $slug];

            return route($route, $parameter, true);
        } catch (\Throwable) {
            return $this->baseUrl;
        }
    }

    private function getTitle(string $pageType, array $data): string
    {
        $name = $data['name'] ?? match ($pageType) {
            'home' => 'Wisata Alam & Kuliner',
            'education', 'school-partners' => 'Wisata Edukasi',
            'contact' => 'Kontak',
            default => 'Paket Wisata',
        };

        return $name.' | '.$this->siteName;
    }

    private function getDescription(string $pageType, array $data): string
    {
        return $data['description'] ?? match ($pageType) {
            'education', 'school-partners' => 'Program wisata edukasi dan pengalaman belajar menyenangkan di Godong Ijo.',
            'contact' => 'Hubungi Godong Ijo untuk informasi, reservasi, dan pertanyaan kunjungan.',
            default => 'Nikmati pengalaman wisata alam dan kuliner terbaik di Godong Ijo.',
        };
    }

    private function getKeywords(string $pageType): array
    {
        return array_merge(
            ['godong ijo', 'wisata alam', 'wisata keluarga'],
            match ($pageType) {
                'education', 'school-partners' => ['wisata edukasi', 'ecotainment'],
                'contact' => ['kontak', 'reservasi'],
                default => ['kuliner', 'paket wisata'],
            },
        );
    }
}
