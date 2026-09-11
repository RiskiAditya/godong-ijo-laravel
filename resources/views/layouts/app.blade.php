<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta Tags --}}
    <title>{{ $seoData['title'] ?? 'Godong Ijo — Wisata Eco-Luxury | Dimana Alam Bertemu Keajaiban' }}</title>
    <meta name="description" content="{{ $seoData['description'] ?? 'Rasakan kemewahan berkelanjutan dengan Godong Ijo. Jelajahi hutan lebat, air terjun berkabut, dan petualangan ramah lingkungan.' }}">
    @isset($seoData['keywords'])
        <meta name="keywords" content="{{ implode(', ', $seoData['keywords']) }}">
    @endisset

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="{{ $seoData['og']['type'] ?? 'website' }}">
    <meta property="og:url" content="{{ $seoData['og']['url'] ?? url()->current() }}">
    <meta property="og:title" content="{{ $seoData['og']['title'] ?? 'Godong Ijo — Dimana Alam Bertemu Keajaiban' }}">
    <meta property="og:description" content="{{ $seoData['og']['description'] ?? 'Wisata eco-luxury pemenang penghargaan dengan air terjun menakjubkan dan petualangan berkelanjutan.' }}">
    <meta property="og:image" content="{{ $seoData['og']['image'] ?? asset('images/og-image.svg') }}">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $seoData['og']['url'] ?? url()->current() }}">
    <meta name="twitter:title" content="{{ $seoData['og']['title'] ?? 'Godong Ijo — Dimana Alam Bertemu Keajaiban' }}">
    <meta name="twitter:description" content="{{ $seoData['og']['description'] ?? 'Wisata eco-luxury pemenang penghargaan dengan air terjun menakjubkan dan petualangan berkelanjutan.' }}">
    <meta name="twitter:image" content="{{ $seoData['og']['image'] ?? asset('images/og-image.svg') }}">

    {{-- Structured Data --}}
    @isset($seoData['structuredData'])
        <script type="application/ld+json">
        {!! json_encode($seoData['structuredData'], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
        </script>
    @endisset

    {{-- Fonts: Fraunces (serif for headings) + Sora (sans-serif for body) --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=fraunces:400,600,700,800|sora:400,500,600,700" rel="stylesheet" />

    @if(Route::is('landing'))
        <link rel="preload" as="image" href="{{ asset('images/placeholders/asset 3.webp') }}" />
    @endif

    {{-- Tabler Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    {{-- Vite Assets (includes Alpine.js bundled in app.js) --}}
    @vite(['resources/css/app.css', 'resources/js/modules/trust-badges.js', 'resources/js/app.js'])
</head>
<body class="antialiased">
    {{-- Skip Navigation (Accessibility) --}}
    <a class="skip-nav" href="#main-content">Skip to content</a>

    {{-- Navigation --}}
    @isset($navigation)
        <x-navigation :items="$navigation" :cta="$cta ?? []" />
    @endisset

    {{-- Main Content --}}
    <main id="main-content">
        @yield('content')
    </main>

    {{-- Footer --}}
    <x-footer />

    {{-- Floating WhatsApp Button --}}
    <x-whatsapp-float />

    {{-- FAQ chatbot for public visitors --}}
    <x-ai-chatbot audience="public" />

    @if(Route::is('landing', 'packages.category', 'destination.show', 'booking.confirmation'))
        <script type="text/javascript" defer
                src="https://app.{{ config('midtrans.is_production') ? '' : 'sandbox.' }}midtrans.com/snap/snap.js" 
                data-client-key="{{ config('midtrans.client_key') }}"></script>
    @endif

    @stack('scripts')
</body>
</html>
