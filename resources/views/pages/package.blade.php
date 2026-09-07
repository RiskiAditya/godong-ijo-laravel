@extends('layouts.page')

@section('content')
<div class="package-page">
    {{-- Hero Section --}}
    <section class="package-hero">
        <div class="package-hero-content">
            <div class="container">
                <div class="package-hero-text">
                    @if(isset($package['badge']))
                    <span class="package-badge">{{ $package['badge'] }}</span>
                    @endif
                    
                    <h1 class="package-hero-title">{{ $package['name'] }}</h1>
                    
                    @if(isset($package['tagline']))
                    <p class="package-hero-tagline">{{ $package['tagline'] }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="package-hero-image">
            <picture>
                <source
                    type="image/webp"
                    srcset="{{ $package['hero_image'] ?? asset('images/placeholders/package-hero-default.jpg') }}"
                    sizes="(max-width: 768px) 100vw, (max-width: 1024px) 100vw, 1400px"
                >
                <img
                    src="{{ $package['hero_image'] ?? asset('images/placeholders/package-hero-default.jpg') }}"
                    alt="{{ $package['name'] }} - {{ $package['tagline'] ?? 'The Waterfall Resto' }}"
                    class="hero-img"
                    loading="eager"
                    width="1400"
                    height="600"
                >
            </picture>
            <div class="hero-overlay"></div>
        </div>
    </section>

    {{-- Package Content --}}
    <section class="package-content-section">
        <div class="container">
            <div class="package-layout">
                {{-- Main Content --}}
                <div class="package-main">
                    {{-- Description --}}
                    @if(isset($package['description']))
                    <div class="content-block scroll-reveal">
                        <h2 class="content-title">Tentang Paket</h2>
                        <p class="content-text">{{ $package['description'] }}</p>
                        @if(isset($package['description_short']) && $package['description_short'])
                        <p class="content-text" style="margin-top: 16px;">{{ $package['description_short'] }}</p>
                        @endif
                    </div>
                    @endif

                    {{-- Gallery --}}
                    @if(isset($package['gallery']) && count($package['gallery']) > 0)
                    <div class="content-block scroll-reveal scroll-reveal-delay-1">
                        <h2 class="content-title">Galeri</h2>
                        <div class="package-gallery">
                            @foreach($package['gallery'] as $index => $imageUrl)
                            <div class="gallery-item">
                                <img 
                                    src="{{ $imageUrl }}" 
                                    alt="{{ $package['name'] }} - Foto {{ $index + 1 }}"
                                    loading="{{ $index < 2 ? 'eager' : 'lazy' }}"
                                >
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Highlights/Features --}}
                    @if(isset($package['features']) && count($package['features']) > 0)
                    <div class="content-block scroll-reveal scroll-reveal-delay-2">
                        <h2 class="content-title">Highlights Paket</h2>
                        <div class="features-grid">
                            @foreach($package['features'] as $feature)
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                                <p class="feature-text">{{ $feature }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Fish Species (for Paket Sport Fishing only) --}}
                    @if(isset($package['fish_species']) && count($package['fish_species']) > 0)
                    <div class="content-block scroll-reveal scroll-reveal-delay-3">
                        <h2 class="content-title">Koleksi Ikan Monster</h2>
                        <p class="content-text" style="margin-bottom: 24px;">Danau kami adalah rumah bagi koleksi ikan predator air tawar raksasa yang menantang. Berikut profil ikan-ikan monster yang siap Anda taklukkan:</p>
                        <div class="fish-species-list">
                            @foreach($package['fish_species'] as $fish)
                            <div class="fish-species-card">
                                <div class="fish-header">
                                    <h3 class="fish-name">{{ $fish['name'] }}</h3>
                                    <span class="fish-weight">{{ $fish['weight'] }}</span>
                                </div>
                                <p class="fish-alias">{{ $fish['alias'] }}</p>
                                <p class="fish-description">{{ $fish['description'] }}</p>
                                <div class="fish-origin">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                    <span>{{ $fish['origin'] }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Itinerary --}}
                    @if(isset($package['itinerary']) && count($package['itinerary']) > 0)
                    <div class="content-block scroll-reveal scroll-reveal-delay-3">
                        <h2 class="content-title">Itinerary</h2>
                        <div class="itinerary-list">
                            @foreach($package['itinerary'] as $index => $activity)
                            <div class="itinerary-item">
                                <div class="itinerary-number">{{ $index + 1 }}</div>
                                <div class="itinerary-content">
                                    <h3 class="itinerary-title">{{ $activity['title'] ?? "Aktivitas " . ($index + 1) }}</h3>
                                    @if(isset($activity['description']))
                                    <p class="itinerary-description">{{ $activity['description'] }}</p>
                                    @endif
                                    @if(isset($activity['duration']))
                                    <span class="itinerary-duration">{{ $activity['duration'] }}</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Included/Excluded --}}
                    <div class="content-block scroll-reveal scroll-reveal-delay-4">
                        <div class="inclusion-grid">
                            {{-- Included --}}
                            @if(isset($package['included']) && count($package['included']) > 0)
                            <div class="inclusion-card included">
                                <h3 class="inclusion-title">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    Sudah Termasuk
                                </h3>
                                <ul class="inclusion-list">
                                    @foreach($package['included'] as $item)
                                    <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            {{-- Excluded --}}
                            @if(isset($package['excluded']) && count($package['excluded']) > 0)
                            <div class="inclusion-card excluded">
                                <h3 class="inclusion-title">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                    Tidak Termasuk
                                </h3>
                                <ul class="inclusion-list">
                                    @foreach($package['excluded'] as $item)
                                    <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Package Info (Pricing, Rules, etc.) --}}
                    @if(isset($package['info_images']) && count($package['info_images']) > 0)
                    <div class="content-block scroll-reveal scroll-reveal-delay-5">
                        <h2 class="content-title">Informasi Paket & Harga</h2>
                        <div class="info-images-grid">
                            @foreach($package['info_images'] as $info)
                            <div class="info-image-card">
                                <img src="{{ $info['url'] }}" alt="{{ $info['title'] }}" loading="lazy">
                                <div class="info-image-caption">
                                    <h4>{{ $info['title'] }}</h4>
                                    <p>{{ $info['description'] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <aside class="package-sidebar">
                    {{-- Booking Card --}}
                    <div class="booking-card scroll-reveal sticky-sidebar">
                        <div class="booking-card-header">
                            <span class="booking-price-label">Harga Mulai Dari</span>
                            <div class="booking-price">Rp {{ number_format((float)$package['priceRaw'], 0, ',', '.') }}</div>
                            @if(isset($package['pricePerPerson']))
                            <span class="booking-price-unit">{{ $package['pricePerPerson'] }}</span>
                            @endif
                        </div>

                        <div class="booking-card-body">
                            @if(isset($package['group_size']))
                            <div class="booking-info-item">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <span>{{ $package['group_size'] }}</span>
                            </div>
                            @endif

                            @if(isset($package['duration']))
                            <div class="booking-info-item">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <span>{{ $package['duration'] }}</span>
                            </div>
                            @endif
                        </div>

                        <button 
                            class="btn btn-primary btn-lg w-full booking-cta-btn" 
                            onclick="openBookingModal({{ $package['id'] }})"
                        >
                            Pesan Sekarang
                        </button>

                        <p class="booking-note">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            Harga dapat berubah tergantung tanggal dan jumlah peserta
                        </p>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</div>
@endsection

@push('head')
<style>
/* Package Page Specific Styles */
.package-hero {
    position: relative;
    height: 500px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 72px;
    overflow: hidden;
    background-color: var(--forest);
}

.package-hero-image {
    position: absolute;
    inset: 0;
    z-index: 1;
}

.package-hero-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(14, 42, 32, 0.6) 0%, rgba(14, 42, 32, 0.4) 100%);
    z-index: 2;
}

.package-hero-content {
    position: relative;
    z-index: 3;
    text-align: center;
    color: white;
    padding: 0 24px;
}

.package-hero-text {
    max-width: 800px;
    margin: 0 auto;
}

.package-badge {
    display: inline-block;
    padding: 6px 16px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50px;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 16px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.package-hero-title {
    font-family: 'Fraunces', Georgia, serif;
    font-size: clamp(2rem, 4vw, 3.5rem);
    font-weight: 700;
    margin-bottom: 16px;
    line-height: 1.2;
    color: #FFFFFF;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.package-hero-tagline {
    font-size: clamp(1rem, 2vw, 1.125rem);
    color: var(--mint);
    font-weight: 500;
    text-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
}

@media (max-width: 768px) {
    .package-hero {
        height: 400px;
    }
}

.package-content-section {
    padding: 80px 0;
}

.package-layout {
    display: grid;
    grid-template-columns: 1fr;
    gap: 48px;
}

@media (min-width: 1024px) {
    .package-layout {
        grid-template-columns: 1fr 380px;
    }
}

.package-main {
    display: flex;
    flex-direction: column;
    gap: 48px;
}

.content-block {
    background: #FFFFFF;
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.content-title {
    font-family: 'Fraunces', Georgia, serif;
    font-size: 28px;
    font-weight: 700;
    color: var(--forest);
    margin-bottom: 20px;
}

.content-text {
    font-size: 16px;
    line-height: 1.7;
    color: #4a4a4a;
}

.package-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 16px;
}

.gallery-item {
    aspect-ratio: 4/3;
    border-radius: 12px;
    overflow: hidden;
}

.gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-item:hover img {
    transform: scale(1.05);
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
}

.feature-card {
    display: flex;
    gap: 12px;
    padding: 16px;
    background: rgba(191, 230, 201, 0.1);
    border-radius: 12px;
}

.feature-icon {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    background: var(--mint);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--forest);
}

.feature-text {
    font-size: 15px;
    color: #4a4a4a;
    margin: 0;
    display: flex;
    align-items: center;
}

.itinerary-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.itinerary-item {
    display: flex;
    gap: 20px;
}

.itinerary-number {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    background: var(--mint);
    color: var(--forest);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 18px;
}

.itinerary-content {
    flex: 1;
}

.itinerary-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--forest);
    margin-bottom: 8px;
}

.itinerary-description {
    font-size: 15px;
    color: #4a4a4a;
    line-height: 1.6;
    margin-bottom: 8px;
}

.itinerary-duration {
    font-size: 13px;
    color: #6B6B6B;
    font-weight: 500;
}

.inclusion-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
}

@media (min-width: 768px) {
    .inclusion-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

.inclusion-card {
    padding: 24px;
    border-radius: 12px;
    border: 2px solid;
}

.inclusion-card.included {
    border-color: var(--mint);
    background: rgba(191, 230, 201, 0.05);
}

.inclusion-card.excluded {
    border-color: #FEE;
    background: rgba(254, 238, 238, 0.5);
}

.inclusion-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 16px;
}

.inclusion-card.included .inclusion-title {
    color: var(--forest);
}

.inclusion-card.excluded .inclusion-title {
    color: #C1622C;
}

.inclusion-list {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.inclusion-list li {
    font-size: 15px;
    color: #4a4a4a;
    padding-left: 24px;
    position: relative;
}

.inclusion-list li::before {
    content: '•';
    position: absolute;
    left: 8px;
    font-size: 18px;
}

.package-sidebar {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.booking-card,
.contact-card {
    background: #FFFFFF;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

@media (min-width: 1024px) {
    .sticky-sidebar {
        position: sticky;
        top: 96px;
    }
}

.booking-card-header {
    text-align: center;
    padding-bottom: 20px;
    border-bottom: 1px solid #E5E5E5;
    margin-bottom: 20px;
}

.booking-price-label {
    font-size: 13px;
    color: #6B6B6B;
    display: block;
    margin-bottom: 8px;
}

.booking-price {
    font-family: 'Fraunces', Georgia, serif;
    font-size: 32px;
    font-weight: 700;
    color: var(--forest);
}

.booking-price-unit {
    font-size: 14px;
    color: #6B6B6B;
    display: block;
    margin-top: 4px;
}

.booking-card-body {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 20px;
}

.booking-info-item {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 15px;
    color: #4a4a4a;
}

.booking-info-item svg {
    color: var(--mint);
}

.booking-note {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    font-size: 13px;
    color: #6B6B6B;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid #E5E5E5;
}

.booking-note svg {
    flex-shrink: 0;
    margin-top: 2px;
}

.contact-card-title {
    font-size: 20px;
    font-weight: 700;
    color: var(--forest);
    margin-bottom: 8px;
}

.contact-card-text {
    font-size: 14px;
    color: #6B6B6B;
    margin-bottom: 16px;
}

.w-full {
    width: 100%;
}

/* Fish Species Cards */
.fish-species-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.fish-species-card {
    padding: 24px;
    background: linear-gradient(135deg, rgba(191, 230, 201, 0.1) 0%, rgba(191, 230, 201, 0.05) 100%);
    border-left: 4px solid var(--mint);
    border-radius: 12px;
    transition: all 0.3s ease;
}

.fish-species-card:hover {
    background: linear-gradient(135deg, rgba(191, 230, 201, 0.15) 0%, rgba(191, 230, 201, 0.08) 100%);
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.fish-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.fish-name {
    font-size: 20px;
    font-weight: 700;
    color: var(--forest);
    margin: 0;
}

.fish-weight {
    display: inline-block;
    padding: 4px 12px;
    background: var(--forest);
    color: #FFFFFF;
    border-radius: 50px;
    font-size: 13px;
    font-weight: 600;
}

.fish-alias {
    font-size: 14px;
    color: #6B6B6B;
    font-style: italic;
    margin: 0 0 12px 0;
}

.fish-description {
    font-size: 15px;
    color: #4a4a4a;
    line-height: 1.6;
    margin: 0 0 12px 0;
}

.fish-origin {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #6B6B6B;
}

.fish-origin svg {
    color: var(--mint);
    flex-shrink: 0;
}

/* Info Images Grid */
.info-images-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
}

@media (min-width: 768px) {
    .info-images-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .info-images-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

.info-image-card {
    background: #FFFFFF;
    border-radius: 12px;
    overflow: hidden;
    border: 2px solid #E5E5E5;
    transition: all 0.3s ease;
}

.info-image-card:hover {
    border-color: var(--mint);
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.info-image-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.info-image-caption {
    padding: 16px;
}

.info-image-caption h4 {
    font-size: 16px;
    font-weight: 700;
    color: var(--forest);
    margin: 0 0 8px 0;
}

.info-image-caption p {
    font-size: 14px;
    color: #6B6B6B;
    margin: 0;
}

</style>
@endpush
