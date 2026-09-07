@props([
    'backgroundImage' => asset('images/education/hero-background.webp'),
    'backgroundImageFallback' => asset('images/education/hero-background.jpg'),
    'title' => 'Wisata Edukasi Ecotainment Godongijo',
    'description' => 'Ecotainment adalah kegiatan karyawisata yang memiliki nilai edukasi di dalamnya.',
    'locationBadge' => 'Depok, Jawa Barat - buka setiap hari',
    'buttons' => [],
    'statistics' => [],
    'alt' => 'Aktivitas edukasi Ecotainment di Godong Ijo',
])

@php
    $badgeText = is_array($locationBadge) ? ($locationBadge['text'] ?? '') : $locationBadge;
    $badgeIcon = is_array($locationBadge) ? ($locationBadge['icon'] ?? 'map-pin') : 'map-pin';
@endphp

<section class="hero-ecotainment" role="banner">
    <div class="hero-split-layout">
        <div class="hero-text-column">
            @if($badgeText)
                <div class="hero-location-badge">
                    <i class="ti ti-{{ $badgeIcon }}" aria-hidden="true"></i>
                    <span>{{ $badgeText }}</span>
                </div>
            @endif

            <h1 class="hero-title">{{ $title }}</h1>
            <p class="hero-description">{{ $description }}</p>

            @if(count($buttons) > 0)
                <div class="hero-buttons" aria-label="Aksi wisata edukasi">
                    @foreach($buttons as $button)
                        @php
                            $variant = $button['variant'] ?? $button['type'] ?? 'outline';
                            $icon = $button['icon'] ?? null;
                        @endphp
                        <a
                            href="{{ $button['url'] ?? '#' }}"
                            class="hero-btn hero-btn-{{ $variant }}"
                            @if($button['external'] ?? false) target="_blank" rel="noopener noreferrer" @endif
                        >
                            @if($icon)
                                <i class="ti ti-{{ $icon }}" aria-hidden="true"></i>
                            @endif
                            <span>{{ $button['label'] ?? 'Pelajari' }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <figure class="hero-image-column">
            <picture>
                <source srcset="{{ $backgroundImage }}" type="image/webp">
                <img
                    src="{{ $backgroundImageFallback }}"
                    alt="{{ $alt }}"
                    loading="eager"
                    class="hero-image"
                    onerror="this.src='{{ asset('images/education/placeholder-education.jpg') }}'"
                >
            </picture>
        </figure>
    </div>

    @if(count($statistics) > 0)
        <div class="hero-statistics-strip">
            <div class="container">
                <div class="statistics-grid">
                    @foreach($statistics as $stat)
                        <div class="education-stat-item">
                            <span class="education-stat-value">{{ $stat['value'] }}</span>
                            <span class="education-stat-label">{{ $stat['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</section>
