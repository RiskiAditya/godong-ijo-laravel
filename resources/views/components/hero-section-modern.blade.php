@props([
    'heroImages' => [],
    'heroDestinationCards' => [],
])

{{-- Modern Hero Section with Background Slideshow & Destination Cards --}}
<section class="hero-modern-section" id="hero">
    {{-- Static Background Image --}}
    <div class="hero-background-static">
        <img
            src="{{ asset('images/placeholders/asset 3.webp') }}"
            alt="Godong Ijo Background"
            loading="eager"
            width="1920"
            height="1080"
        >
        
        {{-- Dark Overlay for Text Contrast --}}
        <div class="hero-overlay"></div>
    </div>

    {{-- Main Content Container --}}
    <div class="hero-modern-container">
        {{-- Left Side: Content --}}
        <div class="hero-modern-content">
            {{-- Badge --}}
            <span class="hero-modern-badge">
                ✨ The Waterfall Resto n Monster Fish Fishing Lake
            </span>

            {{-- Main Heading --}}
            <h1 class="hero-modern-heading">
                GODONG IJO
            </h1>

            {{-- Description --}}
            <p class="hero-modern-description">
                Kuliner premium dengan konsep Dine in Nature dan tantangan seru Monster Fish Fishing. 
                Satu lokasi, pengalaman tak terlupakan.
            </p>

            {{-- CTA Buttons --}}
            <div class="hero-modern-cta">
                <a href="#destinations" class="btn-hero btn-hero-primary">Explore</a>
                <a href="#contact" class="btn-hero btn-hero-outline">Book Now</a>
            </div>

            {{-- Carousel indicators removed - static background --}}
        </div>

        {{-- Right Side: Destination Cards Carousel --}}
        <div class="hero-modern-cards-wrapper">
            <div class="hero-modern-cards" id="heroCardsCarousel">
                @foreach($heroDestinationCards as $index => $card)
                    <article class="hero-destination-card {{ $index === 0 ? 'active' : '' }}" data-card-index="{{ $index }}">
                        @if($card['layout'] === 'single')
                            <div class="card-image card-image-single">
                                <img src="{{ $card['images'][0] }}" alt="{{ $card['title'] }}" loading="lazy">
                            </div>
                        @elseif($card['layout'] === 'grid')
                            <div class="card-image card-image-grid">
                                @foreach(array_slice($card['images'], 0, 4) as $imgIndex => $image)
                                    <div class="grid-item grid-item-{{ $imgIndex + 1 }}">
                                        <img src="{{ $image }}" alt="{{ $card['title'] }}" loading="lazy">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        
                        <div class="card-content">
                            <h3 class="card-title">{{ $card['title'] }}</h3>
                            <p class="card-description">{{ $card['description'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Navigation Controls --}}
            <div class="hero-cards-navigation">
                <button type="button" class="card-nav-btn card-nav-prev" aria-label="Previous card">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>

                <div class="hero-cards-indicators">
                    @foreach($heroDestinationCards as $index => $card)
                        <button type="button" 
                            class="card-indicator-dot {{ $index === 0 ? 'active' : '' }}" 
                            data-card-to="{{ $index }}"
                            aria-label="Go to card {{ $index + 1 }}">
                        </button>
                    @endforeach
                </div>

                <button type="button" class="card-nav-btn card-nav-next" aria-label="Next card">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</section>
