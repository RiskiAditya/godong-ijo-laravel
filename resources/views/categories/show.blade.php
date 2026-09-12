@extends('layouts.app')

@section('title', $seoData['title'] ?? 'Paket Wisata')
@section('meta_description', $seoData['description'] ?? '')

@push('meta')
    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ $seoData['canonical'] ?? url()->current() }}">
    
    {{-- Open Graph Meta Tags --}}
    <meta property="og:title" content="{{ $seoData['og']['title'] ?? $seoData['title'] }}">
    <meta property="og:description" content="{{ $seoData['og']['description'] ?? $seoData['description'] }}">
    <meta property="og:image" content="{{ $seoData['og']['image'] ?? asset('images/godong-ijo-og.jpg') }}">
    <meta property="og:url" content="{{ $seoData['og']['url'] ?? url()->current() }}">
    <meta property="og:type" content="{{ $seoData['og']['type'] ?? 'website' }}">
    
    {{-- Twitter Card Meta Tags --}}
    <meta name="twitter:card" content="{{ $seoData['twitter']['card'] ?? 'summary_large_image' }}">
    <meta name="twitter:title" content="{{ $seoData['twitter']['title'] ?? $seoData['title'] }}">
    <meta name="twitter:description" content="{{ $seoData['twitter']['description'] ?? $seoData['description'] }}">
    <meta name="twitter:image" content="{{ $seoData['twitter']['image'] ?? asset('images/godong-ijo-og.jpg') }}">
    
    {{-- Structured Data (JSON-LD) --}}
    @if(!empty($seoData['structuredData']))
        @foreach($seoData['structuredData'] as $schema)
            <script type="application/ld+json">
                {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
            </script>
        @endforeach
    @endif
    
    {{-- Breadcrumb Structured Data --}}
    @if(!empty($breadcrumbs))
        <script type="application/ld+json">
            {!! json_encode(app('App\Services\BreadcrumbService')->getStructuredData($breadcrumbs), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
        </script>
    @endif
@endpush

@section('content')
<div class="category-page">
    {{-- Breadcrumbs --}}
    @if(!empty($breadcrumbs))
        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <ol class="breadcrumb-list">
                @foreach($breadcrumbs as $index => $breadcrumb)
                    <li class="breadcrumb-item {{ $breadcrumb['current'] ? 'active' : '' }}">
                        @if($breadcrumb['url'])
                            <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a>
                        @else
                            <span>{{ $breadcrumb['label'] }}</span>
                        @endif
                        @if(!$loop->last)
                            <span class="breadcrumb-separator" aria-hidden="true">&gt;</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
    @endif
    
    {{-- Page Header --}}
    <section class="category-header">
        <h1 class="category-main-heading">Pilih Paket Sesuai Kebutuhan</h1>
        <p class="category-subtitle">
            Dari kuliner keluarga hingga event perusahaan, kami punya paket untuk Anda
        </p>
        <h2 class="category-name">{{ $categoryMeta['name'] }}</h2>
    </section>
    
    {{-- Package Grid or Empty State --}}
    @if($packages->isEmpty())
        <x-empty-state 
            message="Belum ada paket tersedia untuk kategori ini"
            submessage="Silakan hubungi kami untuk informasi paket custom"
            ctaText="Hubungi Kami"
            ctaLink="#contact"
        />
    @else
        <section class="packages-section">
            <div class="packages-grid horizontal-scroll">
                @if($category === 'private-room')
                    @foreach($privateRoomCards as $privateCard)
                        <article class="package-card private-room-category-card">
                            <div class="package-image">
                                <img
                                    src="{{ asset($privateCard['image']) }}"
                                    alt="{{ $privateCard['name'] }}"
                                    loading="lazy"
                                >
                                <span class="package-badge-category">PRIVATE ROOM</span>
                            </div>
                            <div class="package-info">
                                <h3 class="package-name">{{ $privateCard['name'] }}</h3>
                                <p class="package-description">{{ $privateCard['description'] }}</p>
                                <ul class="private-room-card-options">
                                    @foreach($privateCard['options'] as $privateOption)
                                        <li>{{ $privateOption['label'] }}</li>
                                    @endforeach
                                </ul>
                                @php
                                    $privateRoomTarget = $packages->firstWhere('nama_paket', $privateCard['name'])
                                        ?? $packages->first();
                                @endphp
                                @if($privateRoomTarget)
                                    @php
                                        $privateBookingConfig = [
                                            'price_type' => 'package',
                                            'minimum_pax' => 1,
                                            'event_type' => $privateCard['key'],
                                            'duration' => 'package',
                                            'private_room_options' => $privateCard['options'],
                                        ];
                                    @endphp
                                    <button
                                        type="button"
                                        class="package-cta btn-pesan"
                                        data-booking-type="private-room"
                                        data-package-id="{{ $privateRoomTarget->id }}"
                                        data-package-name="{{ $privateCard['name'] }}"
                                        data-package-price="0"
                                        data-package-image="{{ asset($privateCard['image']) }}"
                                        data-package-config="{{ base64_encode(json_encode($privateBookingConfig)) }}">
                                        BOOK NOW
                                    </button>
                                @endif
                            </div>
                        </article>
                    @endforeach
                @else
                @foreach($packages as $package)
                    @php
                        $bookingConfig = $package->booking_config ?? [];
                        $packageImage = match ($package->jenis_paket ?? '') {
                            'Private Room' => asset('images/Private Images/BCA-Gathering-2048x1137.webp'),
                            'Fishing Lake' => asset('images/placeholders/Redtail-Catfish-Ikan-Predator-Amerika-Selatan-1536x853.webp'),
                            default => asset('images/placeholders/Tentang-The-Waterfall-Resto-by-Godongijo-1-1536x1121.webp'),
                        };

                        $resolvedImage = $package->foto && file_exists(public_path($package->foto))
                            ? asset($package->foto)
                            : $packageImage;
                    @endphp
                    {{-- Package Card --}}
                    <article class="package-card">
                        {{-- Package Image --}}
                        <div class="package-image">
                            @if($package->foto && file_exists(public_path($package->foto)))
                                <img 
                                    src="{{ asset($package->foto) }}" 
                                    alt="{{ $package->nama_paket }}"
                                    loading="lazy"
                                    onerror="this.onerror=null;this.src='{{ $packageImage }}'"
                                >
                            @else
                                <img 
                                    src="{{ $resolvedImage }}" 
                                    alt="{{ $package->nama_paket }}"
                                    loading="lazy"
                                    onerror="this.onerror=null;this.src='{{ $packageImage }}'"
                                >
                            @endif
                            <span class="package-badge-category">{{ strtoupper($package->jenis_paket ?? 'ADVENTURE') }}</span>
                        </div>
                        
                        {{-- Package Info --}}
                        <div class="package-info">
                            <h3 class="package-name">{{ $package->nama_paket }}</h3>
                            
                            {{-- Rating --}}
                            <div class="package-rating">
                                <span class="rating-score">
                                    <i class="ti ti-star-filled rating-star" aria-hidden="true"></i>
                                    <span class="rating-value">5.0</span>
                                </span>
                                <span class="rating-divider" aria-hidden="true"></span>
                                <span class="rating-reviews">See Reviews</span>
                            </div>
                            
                            {{-- Original & Discount Price --}}
                            @if($package->harga > 0)
                                <div class="package-pricing">
                                    @if(isset($package->diskon_persen) && $package->diskon_persen > 0)
                                        <div class="price-row">
                                            <span class="price-original">Rp {{ number_format($package->harga, 0, ',', '.') }}</span>
                                            <span class="discount-badge">{{ $package->diskon_persen }}% OFF</span>
                                        </div>
                                    @endif
                                    <div class="price-current-row">
                                        <span class="price-caption">Mulai dari</span>
                                        <span class="price-label">Rp</span>
                                        <span class="price-amount">{{ number_format($package->harga * (100 - ($package->diskon_persen ?? 0)) / 100, 0, ',', '.') }}</span>
                                        <span class="price-unit">{{ ($bookingConfig['price_type'] ?? 'per_person') === 'package' ? '/PACKAGE' : '/PERSON' }}</span>
                                    </div>
                                </div>
                            @else
                                <p class="package-price-contact">Hubungi Kami</p>
                            @endif
                            
                            {{-- Description --}}
                            @if($package->deskripsi)
                                @php
                                    $descriptionParts = preg_split('/\s*Fasilitas:\s*/', $package->deskripsi, 2);
                                    $packageDescription = trim($descriptionParts[0]);
                                    $packageFacilities = isset($descriptionParts[1])
                                        ? preg_split('/\s*•\s*/', trim($descriptionParts[1]), -1, PREG_SPLIT_NO_EMPTY)
                                        : [];
                                @endphp
                                @if($packageDescription)
                                    <p class="package-description">{{ $packageDescription }}</p>
                                @endif
                                @if(count($packageFacilities) > 0)
                                    <div class="package-description-facilities">
                                        <span class="package-description-facilities-label">Fasilitas</span>
                                        <ul>
                                            @foreach($packageFacilities as $facility)
                                                <li>{{ trim($facility) }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            @endif
                            
                            {{-- What's Included --}}
                            @if(isset($package->fasilitas) && is_array($package->fasilitas) && count($package->fasilitas) > 0)
                                <div class="package-facilities">
                                    <h4 class="facilities-title">WHAT'S INCLUDED</h4>
                                    <ul class="facilities-list">
                                        @foreach(array_slice($package->fasilitas, 0, 5) as $fasilitas)
                                            @if(is_string($fasilitas) && strlen(trim($fasilitas)) > 0)
                                                <li>{{ Str::limit($fasilitas, 100) }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            @if($package->jenis_paket === 'Fishing Lake')
                                {{-- Fishing Lake: Use Fishing Modal --}}
                                <button 
                                    type="button"
                                    class="package-cta btn-pesan"
                                    data-booking-type="fishing"
                                    data-destination="{{ $package->nama_paket }}">
                                    BOOK NOW
                                </button>
                            @elseif($package->jenis_paket === 'Private Room' && !empty($bookingConfig))
                                <button
                                    type="button"
                                    class="package-cta btn-pesan"
                                    data-booking-type="private-room"
                                    data-package-id="{{ $package->id }}"
                                    data-package-name="{{ $package->nama_paket }}"
                                    data-package-price="{{ $package->harga }}"
                                    data-package-image="{{ $resolvedImage }}"
                                    data-package-config="{{ base64_encode(json_encode($bookingConfig)) }}">
                                    BOOK NOW
                                </button>
                            @else
                                {{-- Other packages: Use Generic Modal --}}
                                <button 
                                    type="button"
                                    class="package-cta btn-pesan"
                                    data-booking-type="generic"
                                    data-package-id="{{ $package->id }}"
                                    data-package-name="{{ addslashes($package->nama_paket) }}"
                                    data-package-price="{{ $package->harga }}">
                                    BOOK NOW
                                </button>
                            @endif
                        </div>
                    </article>
                @endforeach
                @endif
            </div>
        </section>
    @endif
</div>

{{-- Booking Modal Components --}}
<x-booking-modal-simple />
@include('components.private-room-booking-modal')
@include('components.fishing-booking-modal')
@endsection

