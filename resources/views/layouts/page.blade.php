<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- SEO Meta Tags --}}
    @if(isset($seoData))
        @include('partials.seo-meta', ['seo' => $seoData])
    @else
        <title>{{ $title ?? 'Godong Ijo' }}</title>
        <meta name="description" content="{{ $description ?? 'Wisata alam dan rekreasi keluarga di Godong Ijo' }}">
    @endif
    
    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Additional Head Content --}}
    @stack('head')
</head>
<body>
    {{-- Skip Navigation Link for Accessibility --}}
    <a href="#main-content" class="skip-nav">Skip to content</a>
    
    {{-- Navigation Component --}}
    <x-navigation 
        :items="$navigation ?? []" 
        :cta="$cta ?? []" 
        :currentRoute="$currentRoute ?? ''"
    />
    
    {{-- Breadcrumb Navigation (conditional) --}}
    @if(isset($breadcrumbs) && count($breadcrumbs) > 1)
        <x-breadcrumb 
            :items="$breadcrumbs" 
            :structuredData="$breadcrumbStructuredData ?? null"
        />
    @endif
    
    {{-- Main Content --}}
    <main id="main-content">
        @yield('content')
    </main>
    
    {{-- Footer Component --}}
    <x-footer />
    
    {{-- Floating WhatsApp Button --}}
    <x-whatsapp-float />
    
    {{-- Additional Scripts --}}
    @stack('scripts')
</body>
</html>
