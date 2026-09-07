@extends('layouts.app')

@section('content')
{{-- Breadcrumb Navigation --}}
@if(isset($breadcrumbs) && count($breadcrumbs) > 0)
    <nav class="breadcrumb-nav" aria-label="Breadcrumb">
        <div class="breadcrumb-container">
            <ol class="breadcrumb-list">
                @foreach($breadcrumbs as $index => $breadcrumb)
                    <li class="breadcrumb-item">
                        @if($breadcrumb['current'])
                            <span class="breadcrumb-current" aria-current="page">{{ $breadcrumb['label'] }}</span>
                        @else
                            <a href="{{ $breadcrumb['url'] }}" class="breadcrumb-link">{{ $breadcrumb['label'] }}</a>
                        @endif
                        
                        @if(!$loop->last)
                            <span class="breadcrumb-separator" aria-hidden="true">/</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>
    </nav>
@endif

{{-- Hero Section --}}
<section class="destination-hero">
    <div class="destination-hero-content">
        <div class="container">
            <div class="destination-hero-text">
                <h1 class="destination-title">{{ $destination['name'] }}</h1>
                <p class="destination-tagline">{{ $destination['tagline'] }}</p>
            </div>
        </div>
    </div>
    <div class="destination-hero-image">
        <picture>
            <source
                type="image/webp"
                srcset="{{ $destination['hero_image'] }}"
                sizes="(max-width: 768px) 100vw, (max-width: 1024px) 100vw, 1400px"
            >
            <img
                src="{{ $destination['hero_image'] }}"
                alt="{{ $destination['name'] }} - {{ $destination['tagline'] }}"
                class="hero-img"
                loading="eager"
                width="1400"
                height="600"
            >
        </picture>
        <div class="hero-overlay"></div>
    </div>
</section>

{{-- Content Section --}}
<section class="destination-content section-padding">
    <div class="container">
        <div class="content-wrapper">
            <div class="content-text">
                <h2 class="section-title">Tentang {{ $destination['name'] }}</h2>
                <p class="section-description">{{ $destination['description'] }}</p>
            </div>
        </div>
    </div>
</section>

{{-- Image Gallery Section --}}
<section class="destination-gallery section-padding">
    <div class="container">
        <h2 class="section-title text-center">Galeri Foto</h2>
        <p class="section-subtitle text-center">Lihat keindahan dan fasilitas kami</p>
        
        <div class="gallery-grid">
            @foreach($gallery as $image)
                <div class="gallery-item">
                    <picture>
                        <source
                            type="image/webp"
                            srcset="{{ $image['url'] }}"
                            sizes="(max-width: 768px) 100vw, (max-width: 1024px) 50vw, 33vw"
                        >
                        <img
                            src="{{ $image['url'] }}"
                            alt="{{ $image['alt'] }}"
                            class="gallery-img"
                            loading="lazy"
                            width="600"
                            height="400"
                        >
                    </picture>
                    @if(isset($image['caption']))
                        <div class="gallery-caption">
                            <p>{{ $image['caption'] }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- The Waterfall Custom Content Sections --}}
@if($destination['slug'] === 'the-waterfall' && isset($destination['content_sections']))
    @foreach($destination['content_sections'] as $index => $section)
        <section class="waterfall-content-section {{ $index % 2 === 0 ? 'bg-light' : '' }}">
            <div class="container">
                <div class="content-section-wrapper {{ $section['layout'] }}">
                    <div class="content-section-text">
                        <h2 class="content-section-title">{{ $section['title'] }}</h2>
                        <p class="content-section-description">{{ $section['description'] }}</p>
                    </div>
                    
                    <div class="content-section-image">
                        @if(isset($section['menu_images']) && count($section['menu_images']) > 0)
                            {{-- Menu section with auto-sliding carousel --}}
                            <div class="menu-carousel-wrapper">
                                <div class="menu-carousel-track">
                                    @foreach($section['menu_images'] as $menuImg)
                                        <div class="menu-carousel-slide">
                                            <img
                                                src="{{ $menuImg['url'] }}"
                                                alt="{{ $menuImg['alt'] }}"
                                                loading="lazy"
                                                class="menu-carousel-img"
                                            >
                                        </div>
                                    @endforeach
                                </div>
                                <div class="menu-carousel-dots">
                                    @foreach($section['menu_images'] as $index => $menuImg)
                                        <button class="menu-carousel-dot {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}"></button>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            {{-- Regular single image --}}
                            <img
                                src="{{ $section['image'] }}"
                                alt="{{ $section['title'] }}"
                                loading="lazy"
                                class="section-img"
                            >
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endforeach
@endif

{{-- Facilities Section / Package Info Section --}}
@if($destination['slug'] !== 'the-waterfall')
    @if(isset($facilities['info_images']) && count($facilities['info_images']) > 0)
{{-- Monster Fish: Show Scrolling Info Images --}}
<section class="destination-info-carousel section-padding bg-light">
    <div class="container">
        <h2 class="section-title text-center">Informasi Paket & Harga</h2>
        <p class="section-subtitle text-center">Lihat detail lengkap paket pemancingan kami</p>
    </div>
    
    <div class="info-carousel-wrapper">
        <div class="info-carousel-track">
            @foreach($facilities['info_images'] as $info)
                <div class="info-carousel-item">
                    <img src="{{ $info['url'] }}" alt="{{ $info['title'] }}" loading="lazy">
                    <div class="info-carousel-caption">
                        <h3>{{ $info['title'] }}</h3>
                        <p>{{ $info['description'] }}</p>
                    </div>
                </div>
            @endforeach
            {{-- Duplicate for seamless loop --}}
            @foreach($facilities['info_images'] as $info)
                <div class="info-carousel-item">
                    <img src="{{ $info['url'] }}" alt="{{ $info['title'] }}" loading="lazy">
                    <div class="info-carousel-caption">
                        <h3>{{ $info['title'] }}</h3>
                        <p>{{ $info['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    
    {{-- Contact Info for Monster Fish --}}
    <div class="container">
        <div class="contact-cta-section">
            <div class="contact-cta-card">
                <div class="contact-cta-content">
                    <h3>Butuh Informasi Lebih Lanjut?</h3>
                    <p>Hubungi kami untuk detail harga, reservasi, atau pertanyaan seputar pemancingan</p>
                </div>
                <div class="contact-cta-actions">
                    <a href="https://wa.me/{{ config('app.whatsapp.number') }}" class="btn btn-success btn-lg" target="_blank" rel="noopener">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24" style="margin-right: 8px;">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        Chat WhatsApp
                    </a>
                    <a href="tel:+{{ config('app.whatsapp.number') }}" class="btn btn-outline-green btn-lg">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 8px;">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                        Telepon Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@else
{{-- Other Destinations: Show Normal Facilities Section --}}
<section class="destination-facilities section-padding bg-light">
    <div class="container">
        <h2 class="section-title text-center">Fasilitas & Informasi</h2>
        <p class="section-subtitle text-center">Semua yang perlu Anda ketahui sebelum berkunjung</p>
        
        <div class="facilities-grid">
            {{-- Operating Hours --}}
            <div class="facility-card">
                <div class="facility-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <h3 class="facility-title">Jam Operasional</h3>
                <p class="facility-text">{{ $facilities['hours'] }}</p>
            </div>
            
            {{-- Admission --}}
            <div class="facility-card">
                <div class="facility-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                        <line x1="1" y1="10" x2="23" y2="10"></line>
                    </svg>
                </div>
                <h3 class="facility-title">Biaya Masuk</h3>
                <p class="facility-text">{{ $facilities['admission'] }}</p>
            </div>
            
            {{-- Contact --}}
            <div class="facility-card">
                <div class="facility-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                </div>
                <h3 class="facility-title">Kontak</h3>
                <p class="facility-text">{{ $facilities['contact'] }}</p>
            </div>
        </div>
        
        {{-- Amenities List --}}
        @if(isset($facilities['amenities']) && count($facilities['amenities']) > 0)
            <div class="amenities-section">
                <h3 class="amenities-title">Fasilitas Tersedia</h3>
                <ul class="amenities-list">
                    @foreach($facilities['amenities'] as $amenity)
                        <li class="amenity-item">
                            <svg class="amenity-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span>{{ $amenity }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</section>
@endif
@endif

{{-- Booking CTA Section --}}
<section class="destination-cta section-padding">
    <div class="container">
        <div class="cta-card">
            <div class="cta-content">
                <h2 class="cta-title">Siap Berkunjung?</h2>
                <p class="cta-description">Pesan sekarang dan nikmati pengalaman tak terlupakan di {{ $destination['name'] }}</p>
            </div>
            <div class="cta-actions">
                <button 
                    class="btn btn-primary btn-lg"
                    onclick="openBookingModal(null, null, null)"
                    type="button"
                >
                    {{ $destination['cta_text'] }}
                </button>
            </div>
        </div>
    </div>
</section>

@endsection

{{-- Simple Booking Modal (Pure JavaScript) --}}
<x-booking-modal-simple />
