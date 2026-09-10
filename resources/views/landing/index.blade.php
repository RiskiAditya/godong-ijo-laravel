@extends('layouts.app')

@section('content')
    {{-- ======================================
         HERO SECTION (Modern Travel Style)
         ====================================== --}}
    <x-hero-section-modern 
        :heroImages="$heroImages" 
        :heroDestinationCards="$heroDestinationCards" 
    />

    {{-- OLD HERO SECTION (Bento Grid) - Backup
    <x-hero-section :heroImages="$heroImages" />
    --}}

    {{-- ======================================
         FEATURED DESTINATIONS SECTION
         ====================================== --}}
    <section class="destinations-section" id="destinations">
        <div class="container">
            <div class="section-header scroll-reveal">
                <h2 class="section-title">Lengkap untuk <span class="text-green-dark">Semua Kebutuhan</span></h2>
                <p class="section-subtitle">Kuliner, rekreasi, dan event dalam satu destinasi ekologis</p>
            </div>

            <div class="destinations-grid">
                @foreach($destinations as $destination)
                    <div class="destination-card scroll-reveal scroll-reveal-delay-{{ $loop->iteration }}">
                        <div class="destination-card-image">
                            <img
                                src="{{ $destination['image'] }}"
                                alt="{{ $destination['alt'] }}"
                                loading="lazy"
                                width="400"
                                height="300"
                            >
                        </div>
                        <div class="destination-card-overlay">
                            <span class="destination-tag">{{ $destination['tag'] }}</span>
                        </div>
                        <div class="destination-card-content">
                            <h3 class="destination-name">{{ $destination['name'] }}</h3>
                            <p class="destination-location">{{ $destination['location'] }}</p>
                            <p class="destination-description">{{ $destination['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>

    {{-- ======================================
         EXPERIENCE / STATS SECTION
         ====================================== --}}
    <section class="experience-section" id="experiences">
        <div class="container">
            <div class="experience-layout">
                <div class="experience-content scroll-reveal">
                    <h2 class="section-title text-left">Satu Destinasi untuk <span class="text-green-dark">Semua</span></h2>
                    <p class="experience-text">
                        Godong Ijo adalah destinasi kuliner dan rekreasi paling ekologis di Indonesia. 
                        Dengan konsep Dine in Nature, kami menghadirkan pengalaman bersantap premium di 
                        tengah suasana air terjun mini yang asri. Lokasi strategis hanya 15 menit dari 
                        pintu tol dengan fasilitas lengkap untuk keluarga, acara perusahaan, hingga sport fishing.
                    </p>

                    <div class="stats-grid">
                        @foreach($statistics as $stat)
                            <div class="stat-item">
                                <span class="stat-number" data-target="{{ $stat['number'] }}">0</span><span class="stat-suffix">{{ $stat['suffix'] }}</span>
                                <span class="stat-label">{{ $stat['label'] }}</span>
                            </div>
                        @endforeach
                    </div>

                    <a href="#contact" class="btn btn-primary">Hubungi Kami</a>
                </div>

                <div class="experience-images scroll-reveal scroll-reveal-delay-2">
                    <div class="experience-img-main">
                        <img
                            src="{{ asset('images/placeholders/asset 5.webp') }}"
                            alt="Air terjun megah di tengah hutan tropis yang lebat"
                            loading="lazy"
                            width="600"
                            height="400"
                        >
                    </div>
                    <div class="experience-img-secondary">
                        <img
                            src="{{ asset('images/placeholders/asset 6.webp') }}"
                            alt="Aktivitas petualangan eco-tourism yang menarik"
                            loading="lazy"
                            width="200"
                            height="200"
                        >
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ======================================
         TOUR PACKAGES SECTION - INFINITE CAROUSEL
         ====================================== --}}
    <section class="packages-section" id="packages">
        <div class="container-full">
            <div class="section-header scroll-reveal">
                <h2 class="section-title">Pilih <span class="text-green-dark">Paket</span> Sesuai Kebutuhan</h2>
                <p class="section-subtitle">Dari kuliner keluarga hingga event perusahaan, kami punya paket untuk Anda</p>
            </div>

            {{-- Infinite Auto-Scrolling Carousel --}}
            <div class="packages-carousel-container">
                {{-- Navigation Buttons (outside wrapper to avoid overflow clipping) --}}
                <button class="carousel-nav carousel-nav-prev" aria-label="Previous">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>
                <button class="carousel-nav carousel-nav-next" aria-label="Next">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
                
                <div class="packages-carousel-wrapper">
                    <div class="packages-carousel">
                    {{-- Original packages --}}
                    @foreach($packages as $package)
                        <div class="package-card {{ $package['popular'] ? 'package-card-popular' : '' }}">
                            {{-- Package Image (single or collage based on count) --}}
                            @if(!empty($package['images']))
                                @if(count($package['images']) === 1)
                                    {{-- Single Image Display --}}
                                    <div class="package-image-single">
                                        @if($package['badge'])
                                        <span class="package-badge-tag">{{ $package['badge'] }}</span>
                                        @endif
                                        <img src="{{ $package['images'][0] }}" alt="{{ $package['name'] }}" loading="lazy">
                                    </div>
                                @else
                                    {{-- Collage Display (for multiple images) --}}
                                    <div class="package-image-collage package-image-collage-count-{{ min(count($package['images']), 4) }}">
                                        @if($package['badge'])
                                        <span class="package-badge-tag">{{ $package['badge'] }}</span>
                                        @endif
                                        
                                        @foreach(array_slice($package['images'], 0, 4) as $imgIndex => $imgUrl)
                                        <div class="collage-item collage-item-{{ $imgIndex + 1 }}">
                                            <img src="{{ $imgUrl }}" alt="{{ $package['name'] }}" loading="lazy">
                                        </div>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                {{-- No image placeholder --}}
                                <div class="package-no-image">
                                    @if($package['badge'])
                                    <span class="package-badge-tag">{{ $package['badge'] }}</span>
                                    @endif
                                    <div class="no-image-placeholder">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                            <circle cx="8.5" cy="8.5" r="1.5"/>
                                            <polyline points="21 15 16 10 5 21"/>
                                        </svg>
                                        <p>Tidak ada foto</p>
                                    </div>
                                </div>
                            @endif

                            {{-- Package Content --}}
                            <div class="package-content-simple">
                                <h3 class="package-title">{{ $package['name'] }}</h3>
                                <p class="package-subtitle">{{ $package['duration'] }}</p>
                                <p class="package-desc">{{ Str::limit($package['description'], 100) }}</p>
                                
                                <div class="package-action">
                                    <div class="package-price-tag">
                                        @if($package['name'] !== 'Paket Sport Fishing')
                                        <span class="price-label">{{ $package['price'] }}</span>
                                        @endif
                                        @if($package['pricePerPerson'])
                                        <span class="price-unit">{{ $package['pricePerPerson'] }}</span>
                                        @endif
                                    </div>
                                    
                                    @if(isset($package['id']))
                                        @if(isset($package['jenis_paket']) && $package['jenis_paket'] === 'Fishing Lake')
                                            {{-- Fishing Lake package - open fishing modal --}}
                                            <button
                                                type="button"
                                                class="btn-pesan"
                                                data-destination="{{ $package['name'] }}"
                                                data-booking-type="fishing"
                                            >
                                                Pesan
                                            </button>
                                        @elseif(isset($package['jenis_paket']) && $package['jenis_paket'] === 'Private Room')
                                            <button
                                                type="button"
                                                class="btn-pesan"
                                                data-booking-type="private-room"
                                                data-package-id="{{ $package['id'] }}"
                                                data-package-name="{{ $package['name'] }}"
                                                data-package-price="0"
                                                data-package-image="{{ $package['image'] }}"
                                                data-package-config="{{ base64_encode(json_encode(['price_type' => 'package', 'minimum_pax' => 1, 'event_type' => 'private_room', 'duration' => 'package', 'private_room_options' => $package['privateRoomOptions']])) }}">
                                                Pesan
                                            </button>
                                        @else
                                            {{-- Other packages - open generic modal --}}
                                            <button 
                                                type="button"
                                                class="btn-pesan"
                                                data-booking-type="generic"
                                                data-package-id="{{ $package['id'] }}"
                                                data-package-name="{{ addslashes($package['name']) }}"
                                                data-package-price="{{ $package['priceDiscount'] > 0 ? $package['priceDiscount'] : ($package['priceOriginal'] > 0 ? $package['priceOriginal'] : 0) }}">
                                                Pesan
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Duplicate packages for seamless infinite loop --}}
                    @foreach($packages as $package)
                        @if($package['jenis_paket'] === 'Private Room')
                            @continue
                        @endif
                        <div class="package-card {{ $package['popular'] ? 'package-card-popular' : '' }}">
                            {{-- Package Image (single or collage based on count) --}}
                            @if(!empty($package['images']))
                                @if(count($package['images']) === 1)
                                    {{-- Single Image Display --}}
                                    <div class="package-image-single">
                                        @if($package['badge'])
                                        <span class="package-badge-tag">{{ $package['badge'] }}</span>
                                        @endif
                                        <img src="{{ $package['images'][0] }}" alt="{{ $package['name'] }}" loading="lazy">
                                    </div>
                                @else
                                    {{-- Collage Display (for multiple images) --}}
                                    <div class="package-image-collage package-image-collage-count-{{ min(count($package['images']), 4) }}">
                                        @if($package['badge'])
                                        <span class="package-badge-tag">{{ $package['badge'] }}</span>
                                        @endif
                                        
                                        @foreach(array_slice($package['images'], 0, 4) as $imgIndex => $imgUrl)
                                        <div class="collage-item collage-item-{{ $imgIndex + 1 }}">
                                            <img src="{{ $imgUrl }}" alt="{{ $package['name'] }}" loading="lazy">
                                        </div>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                {{-- No image placeholder --}}
                                <div class="package-no-image">
                                    @if($package['badge'])
                                    <span class="package-badge-tag">{{ $package['badge'] }}</span>
                                    @endif
                                    <div class="no-image-placeholder">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                            <circle cx="8.5" cy="8.5" r="1.5"/>
                                            <polyline points="21 15 16 10 5 21"/>
                                        </svg>
                                        <p>Tidak ada foto</p>
                                    </div>
                                </div>
                            @endif

                            {{-- Package Content --}}
                            <div class="package-content-simple">
                                <h3 class="package-title">{{ $package['name'] }}</h3>
                                <p class="package-subtitle">{{ $package['duration'] }}</p>
                                <p class="package-desc">{{ Str::limit($package['description'], 100) }}</p>
                                
                                <div class="package-action">
                                    <div class="package-price-tag">
                                        @if($package['name'] !== 'Paket Sport Fishing')
                                        <span class="price-label">{{ $package['price'] }}</span>
                                        @endif
                                        @if($package['pricePerPerson'])
                                        <span class="price-unit">{{ $package['pricePerPerson'] }}</span>
                                        @endif
                                    </div>
                                    
                                    @if(isset($package['id']))
                                        @if(isset($package['jenis_paket']) && $package['jenis_paket'] === 'Fishing Lake')
                                            {{-- Fishing Lake package - open fishing modal --}}
                                            <button
                                                type="button"
                                                class="btn-pesan"
                                                data-destination="{{ $package['name'] }}"
                                                data-booking-type="fishing"
                                            >
                                                Pesan
                                            </button>
                                        @elseif(isset($package['jenis_paket']) && $package['jenis_paket'] === 'Private Room')
                                            <button
                                                type="button"
                                                class="btn-pesan"
                                                data-booking-type="private-room"
                                                data-package-id="{{ $package['id'] }}"
                                                data-package-name="{{ $package['name'] }}"
                                                data-package-price="0"
                                                data-package-image="{{ $package['image'] }}"
                                                data-package-config="{{ base64_encode(json_encode(['price_type' => 'package', 'minimum_pax' => 1, 'event_type' => 'private_room', 'duration' => 'package', 'private_room_options' => $package['privateRoomOptions']])) }}">
                                                Pesan
                                            </button>
                                        @else
                                            {{-- Other packages - open generic modal --}}
                                            <button 
                                                type="button"
                                                class="btn-pesan"
                                                data-booking-type="generic"
                                                data-package-id="{{ $package['id'] }}"
                                                data-package-name="{{ addslashes($package['name']) }}"
                                                data-package-price="{{ $package['priceDiscount'] > 0 ? $package['priceDiscount'] : ($package['priceOriginal'] > 0 ? $package['priceOriginal'] : 0) }}">
                                                Pesan
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                </div>
            </div>

            <div class="section-cta scroll-reveal" style="margin-top: 48px;">
                <p class="packages-custom-text">Butuh paket custom atau informasi lebih lanjut?</p>
                <a href="#contact" class="btn btn-outline-green">Hubungi Kami</a>
            </div>
        </div>
    </section>

    {{-- ======================================
         CTA BANNER SECTION
         ====================================== --}}
    <section class="cta-banner" id="itineraries">
        <div class="cta-banner-bg"></div>
        <div class="container cta-banner-content scroll-reveal">
            <h2 class="cta-banner-heading">
                Siap Berkunjung ke
                <span class="text-green-bright">Godong Ijo?</span>
            </h2>
            <p class="cta-banner-text">
                Bergabunglah dengan ribuan pelanggan puas yang telah menikmati pengalaman kuliner dan 
                rekreasi di destinasi paling ekologis di Indonesia.
            </p>
            <div class="cta-banner-buttons">
                <a href="#contact" class="btn btn-primary btn-lg">Hubungi Kami</a>
                <a href="#packages" class="btn btn-glass">Lihat Paket</a>
            </div>
        </div>
    </section>

    {{-- ======================================
         LOCATION & HOURS SECTION
         ====================================== --}}
    <section class="location-section" id="contact">
        <div class="container">
            <div class="section-header scroll-reveal">
                <h2 class="section-title">Lokasi & <span class="text-green-dark">Jam Buka</span></h2>
                <p class="section-subtitle">Kami siap menyambut Anda setiap hari</p>
            </div>

            <div class="location-grid">
                {{-- Map Section --}}
                <div class="location-map scroll-reveal">
                    <div class="map-container">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.0!2d106.73!3d-6.398!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69e99999999999%3A0x9999999999999999!2sJl.%20Cinangka%20Raya%20No.km%2010%2C%20Serua%2C%20Kec.%20Bojongsari%2C%20Kota%20Depok%2C%20Jawa%20Barat%2016517!5e0!3m2!1sen!2sid!4v1620000000001!5m2!1sen!2sid"
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Lokasi Godong Ijo - Jl. Cinangka Raya No.km 10, Serua, Bojongsari, Depok">
                        </iframe>
                    </div>
                </div>

                {{-- Info Section --}}
                <div class="location-info scroll-reveal">
                    {{-- Address --}}
                    <div class="info-card">
                        <div class="info-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <div class="info-content">
                            <h3 class="info-title">Alamat</h3>
                            <p class="info-text">Jalan Cinangka Raya Km 10 No. 60<br>Kelurahan Serua, Kecamatan Bojongsari<br>Kota Depok, Jawa Barat 16517</p>
                            <a href="https://www.google.com/maps/search/Jalan+Cinangka+Raya+Km+10+No.+60+Serua+Bojongsari+Depok" target="_blank" class="info-link">Buka di Google Maps →</a>
                        </div>
                    </div>

                    {{-- Opening Hours --}}
                    <div class="info-card">
                        <div class="info-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <div class="info-content">
                            <h3 class="info-title">Jam Buka</h3>
                            <div class="hours-list">
                                <div class="hours-item">
                                    <span class="hours-label">The Waterfall Resto</span>
                                    <span class="hours-time">10.00 - 21.00</span>
                                </div>
                                <div class="hours-item">
                                    <span class="hours-label">Fishing Lake</span>
                                    <span class="hours-time">09.00 - 21.00</span>
                                </div>
                                <div class="hours-note">
                                    Buka setiap hari
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Contact --}}
                    <div class="info-card">
                        <div class="info-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                        </div>
                        <div class="info-content">
                            <h3 class="info-title">Hubungi Kami</h3>
                            <div class="contact-list">
                                <a href="tel:+622174710678" class="contact-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                    </svg>
                                    +62 21 7471 0678
                                </a>
                                <a href="https://wa.me/{{ config('app.whatsapp.number') }}" target="_blank" class="contact-item contact-whatsapp">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                    </svg>
                                    WhatsApp: {{ config('app.whatsapp.display') }}
                                </a>
                                <a href="mailto:info@godongijo.com" class="contact-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                        <polyline points="22,6 12,13 2,6"/>
                                    </svg>
                                    info@godongijo.com
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

{{-- Simple Booking Modal (Pure JavaScript) - for generic packages --}}
<x-booking-modal-simple />

{{-- Fishing Booking Modal (Alpine.js) - for Monster Fish / Fishing Lake --}}
@include('components.fishing-booking-modal')
@include('components.private-room-booking-modal')
