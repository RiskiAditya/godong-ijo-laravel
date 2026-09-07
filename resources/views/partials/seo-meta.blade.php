{{--
    SEO Meta Tags Partial
    
    Renders all SEO meta tags for a page including:
    - Page title
    - Meta description and keywords
    - Open Graph tags for social media
    - Twitter Card tags
    - Canonical URL
    - JSON-LD structured data
    
    Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 7.6
    
    Usage:
    @include('partials.seo-meta', ['seoData' => $seoData])
    or
    @include('partials.seo-meta', ['seo' => $seoData])
    
    Expected $seoData structure:
    [
        'title' => string,
        'description' => string,
        'keywords' => array,
        'canonical' => string,
        'og' => [
            'title' => string,
            'description' => string,
            'image' => string,
            'url' => string,
            'type' => string,
        ],
        'twitter' => [
            'card' => string,
            'title' => string,
            'description' => string,
            'image' => string,
        ],
        'structuredData' => array,
    ]
--}}

@php
    // Support both 'seoData' and 'seo' parameter names for flexibility
    $seoData = $seoData ?? $seo ?? [];
@endphp

{{-- Page Title --}}
<title>{{ $seoData['title'] ?? 'Godong Ijo' }}</title>

{{-- Meta Description --}}
@if(isset($seoData['description']))
<meta name="description" content="{{ $seoData['description'] }}">
@endif

{{-- Meta Keywords --}}
@if(isset($seoData['keywords']) && is_array($seoData['keywords']))
<meta name="keywords" content="{{ implode(', ', $seoData['keywords']) }}">
@endif

{{-- Canonical URL --}}
@if(isset($seoData['canonical']))
<link rel="canonical" href="{{ $seoData['canonical'] }}">
@endif

{{-- Open Graph Tags --}}
@if(isset($seoData['og']))
<meta property="og:title" content="{{ $seoData['og']['title'] ?? $seoData['title'] ?? '' }}">
<meta property="og:description" content="{{ $seoData['og']['description'] ?? $seoData['description'] ?? '' }}">
@if(isset($seoData['og']['image']))
<meta property="og:image" content="{{ $seoData['og']['image'] }}">
@endif
@if(isset($seoData['og']['url']))
<meta property="og:url" content="{{ $seoData['og']['url'] }}">
@endif
<meta property="og:type" content="{{ $seoData['og']['type'] ?? 'website' }}">
<meta property="og:site_name" content="Godong Ijo">
<meta property="og:locale" content="id_ID">
@endif

{{-- Twitter Card Tags --}}
@if(isset($seoData['twitter']))
<meta name="twitter:card" content="{{ $seoData['twitter']['card'] ?? 'summary_large_image' }}">
<meta name="twitter:title" content="{{ $seoData['twitter']['title'] ?? $seoData['title'] ?? '' }}">
<meta name="twitter:description" content="{{ $seoData['twitter']['description'] ?? $seoData['description'] ?? '' }}">
@if(isset($seoData['twitter']['image']))
<meta name="twitter:image" content="{{ $seoData['twitter']['image'] }}">
@endif
@endif

{{-- JSON-LD Structured Data --}}
@if(isset($seoData['structuredData']) && is_array($seoData['structuredData']) && count($seoData['structuredData']) > 0)
@foreach($seoData['structuredData'] as $schema)
@if(!empty($schema))
<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endif
@endforeach
@endif

{{-- Additional Meta Tags --}}
<meta name="robots" content="index, follow">
<meta name="language" content="Indonesian">
<meta http-equiv="content-language" content="id">
