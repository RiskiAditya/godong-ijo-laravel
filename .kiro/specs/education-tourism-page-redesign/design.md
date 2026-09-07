# Design Document: Education Tourism Page Redesign (Wisata Edukasi - Ecotainment Godongijo)

## Overview

This design document outlines the technical approach for redesigning the Wisata Edukasi (Educational Tourism) page for Godong Ijo. The page will replace the current empty placeholder with a comprehensive, visually engaging presentation of Ecotainment Godongijo educational tourism programs targeting schools and educational institutions.

### Goals

- Create an immersive educational tourism landing page that showcases Godong Ijo's Ecotainment programs
- Present three distinct fieldtrip platforms (Virtual, Goes To School Field Trip, Fieldtrip at Godongijo) with clear differentiation
- Display three program categories (Edukasi Lingkungan, Science, Art) with attractive visuals
- Provide detailed program information with appropriate layout patterns for readability
- Enable direct WhatsApp inquiry from every program/platform card with pre-filled context
- Ensure mobile-responsive design with smooth carousel navigation
- Optimize for SEO, accessibility, and performance
- Maintain visual consistency with The Waterfall brand identity

### Technology Stack

This page will be built using the existing Laravel application stack:

- **Laravel 10.x**: PHP framework providing MVC structure, routing, and templating
- **Blade Templating Engine**: Laravel's templating engine for reusable components and layouts
- **Vite**: Modern asset compilation for CSS and JavaScript bundling
- **Alpine.js**: Lightweight JavaScript framework for interactive components (already integrated)
- **Tailwind CSS / Custom CSS**: Styling framework using existing design tokens (forest, mint, gold, clay, sand, ink colors)
- **WebP images with fallbacks**: Optimized visual assets with graceful degradation
- **PHP 8.1+**: Server-side language runtime

The page will integrate with existing services:
- **NavigationService**: For consistent main navigation
- **BreadcrumbService**: For hierarchical navigation (Home > Wisata Edukasi)
- **SEOService**: For metadata generation and structured data

## Architecture

### System Architecture

The education tourism page follows Laravel's MVC architecture pattern:

```
┌─────────────────────────────────────────┐
│         Browser (Client)                │
│  ┌───────────────────────────────────┐  │
│  │   Rendered HTML (from Blade)      │  │
│  │   - Semantic HTML5 markup         │  │
│  │   - SEO metadata & structured data│  │
│  │   - ARIA labels & accessibility   │  │
│  └───────────────────────────────────┘  │
│  ┌───────────────────────────────────┐  │
│  │   Compiled CSS (Vite)             │  │
│  │   - Brand color tokens            │  │
│  │   - Responsive layouts            │  │
│  │   - Card components styling       │  │
│  │   - Carousel animations           │  │
│  └───────────────────────────────────┘  │
│  ┌───────────────────────────────────┐  │
│  │   Alpine.js Components            │  │
│  │   - Mobile carousel navigation    │  │
│  │   - Image lazy loading            │  │
│  │   - Accordion interactions        │  │
│  │   - WhatsApp click handling       │  │
│  └───────────────────────────────────┘  │
└─────────────────────────────────────────┘
         │ HTTP Request
         ▼
┌─────────────────────────────────────────┐
│      Laravel Application (Server)       │
│  ┌───────────────────────────────────┐  │
│  │   Routes (web.php)                │  │
│  │   - GET /wisata-edukasi           │  │
│  └───────────────────────────────────┘  │
│  ┌───────────────────────────────────┐  │
│  │   Controller                      │  │
│  │   - StaticPageController          │  │
│  │   - education() method (updated)  │  │
│  │   - Prepare comprehensive data    │  │
│  └───────────────────────────────────┘  │
│  ┌───────────────────────────────────┐  │
│  │   Services                        │  │
│  │   - NavigationService             │  │
│  │   - BreadcrumbService             │  │
│  │   - SEOService                    │  │
│  └───────────────────────────────────┘  │
│  ┌───────────────────────────────────┐  │
│  │   View (Blade Templates)          │  │
│  │   - layouts/app.blade.php         │  │
│  │   - pages/education.blade.php     │  │
│  │   - components/education/*        │  │
│  └───────────────────────────────────┘  │
└─────────────────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────────┐
│   Static Assets (public/)               │
│   - /css/app.css (compiled)             │
│   - /js/app.js (compiled)               │
│   - /images/education/ (WebP/JPG)       │
│   - /build/ (Vite manifest)             │
└─────────────────────────────────────────┘
```

### Page Structure

The education tourism page is organized using Blade components:

1. **Main Layout** (`resources/views/layouts/app.blade.php`) - existing
   - HTML structure, meta tags, asset includes
   - Navigation integration
   - Footer integration
   
2. **Education Page View** (`resources/views/pages/education.blade.php`) - to be updated
   - Breadcrumb navigation
   - Hero section with background image
   - Platform Fieldtrip section
   - Program Category section
   - Program Detail sections (Environmental, Art, Science)
   - Testimonial section

3. **Reusable Blade Components** (new)
   - `components/education/hero-ecotainment.blade.php`
   - `components/education/card-grid.blade.php`
   - `components/education/card-carousel.blade.php`
   - `components/education/program-card.blade.php`
   - `components/education/program-detail-alternating.blade.php`
   - `components/education/testimonial-section.blade.php`
   - `components/education/whatsapp-button.blade.php`

4. **Controller** (`app/Http/Controllers/StaticPageController.php`) - to be updated
   - education() method enhancement with comprehensive data structure

5. **Routes** (`routes/web.php`) - existing route maintained
   - `GET /wisata-edukasi` → StaticPageController@education

### Laravel File Structure

```
app/
├── Http/
│   └── Controllers/
│       └── StaticPageController.php (update education method)
│
resources/
├── views/
│   ├── layouts/
│   │   └── app.blade.php (existing)
│   ├── pages/
│   │   └── education.blade.php (redesign)
│   └── components/
│       ├── navigation.blade.php (existing)
│       ├── footer.blade.php (existing)
│       ├── whatsapp-float.blade.php (existing)
│       └── education/ (new directory)
│           ├── hero-ecotainment.blade.php
│           ├── card-grid.blade.php
│           ├── card-carousel.blade.php
│           ├── program-card.blade.php
│           ├── program-detail-alternating.blade.php
│           ├── testimonial-section.blade.php
│           └── whatsapp-button.blade.php
├── css/
│   ├── app.css (add education page styles)
│   └── components/
│       └── education.css (new, optional separate file)
└── js/
    ├── app.js (existing)
    └── modules/
        └── education-carousel.js (new, optional)
│
public/
├── images/
│   └── education/ (new directory)
│       ├── hero-background.webp
│       ├── hero-background.jpg (fallback)
│       ├── category-environmental.webp
│       ├── category-science.webp
│       ├── category-art.webp
│       ├── learning-animals.webp
│       ├── urban-farming.webp
│       ├── renewable-energy.webp
│       ├── testimonial-photo.webp
│       └── placeholder-education.jpg (fallback state)
│
routes/
└── web.php (existing route maintained)
```

### Responsive Breakpoints

Following existing project patterns:

- **Mobile**: < 768px (single column, carousel for cards)
- **Tablet**: 768px - 1024px (2-column grid where appropriate)
- **Desktop**: > 1024px (multi-column grid, alternating layouts)


## Components and Interfaces

### 1. Hero Section with Background Image

**Component**: `components/education/hero-ecotainment.blade.php`

**Purpose**: Create immediate visual impact with Ecotainment branding and compelling hero background

```blade
@props([
    'backgroundImage' => asset('images/education/hero-background.webp'),
    'backgroundImageFallback' => asset('images/education/hero-background.jpg'),
    'title' => 'Ecotainment Godongijo',
    'description' => 'Ecotainment adalah suatu kegiatan karyawisata yang memiliki nilai edukasi...'
])

<section class="hero-ecotainment" role="banner">
    {{-- Background Image with Overlay --}}
    <picture class="hero-background">
        <source srcset="{{ $backgroundImage }}" type="image/webp">
        <img 
            src="{{ $backgroundImageFallback }}" 
            alt="Ecotainment educational activities at Godong Ijo"
            class="hero-bg-image"
            loading="eager"
            @error="this.src='{{ asset('images/education/placeholder-education.jpg') }}'"
        >
    </picture>
    
    {{-- Semi-transparent Overlay --}}
    <div class="hero-overlay"></div>
    
    {{-- Hero Content --}}
    <div class="hero-content container">
        <h1 class="hero-title">{{ $title }}</h1>
        <p class="hero-description">{{ $description }}</p>
    </div>
</section>
```

**Styling Requirements** (in `resources/css/app.css`):

```css
.hero-ecotainment {
  position: relative;
  width: 100%;
  min-height: 50vh;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.hero-background {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 0;
}

.hero-bg-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    rgba(14, 42, 32, 0.6),
    rgba(14, 42, 32, 0.4)
  );
  z-index: 1;
}

.hero-content {
  position: relative;
  z-index: 2;
  text-align: center;
  color: var(--sand);
  padding: 40px 24px;
}

.hero-title {
  font-family: 'Fraunces', serif;
  font-size: clamp(2rem, 5vw, 3.5rem);
  font-weight: 700;
  margin-bottom: 16px;
  text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.hero-description {
  font-size: clamp(1rem, 2vw, 1.125rem);
  line-height: 1.7;
  max-width: 800px;
  margin: 0 auto;
  text-shadow: 0 1px 5px rgba(0, 0, 0, 0.2);
}

@media (max-width: 767px) {
  .hero-ecotainment {
    min-height: 40vh;
  }
}
```

**Behavior**:
- Desktop: Full-width hero with centered text overlay
- Mobile: Maintains aspect ratio, text remains readable
- Image loading: Eager loading for hero (above fold), fallback on error

---

### 2. Card Grid Component (Desktop Layout)

**Component**: `components/education/card-grid.blade.php`

**Purpose**: Display cards horizontally in grid format for desktop view

```blade
@props([
    'cards' => [],
    'columns' => 3,
    'sectionId' => 'card-grid'
])

<div class="card-grid" id="{{ $sectionId }}" data-columns="{{ $columns }}">
    @foreach($cards as $card)
        <x-education.program-card 
            :card="$card"
            :sectionId="$sectionId"
        />
    @endforeach
</div>
```

**Styling**:

```css
.card-grid {
  display: grid;
  gap: 24px;
  width: 100%;
}

.card-grid[data-columns="3"] {
  grid-template-columns: repeat(3, 1fr);
}

.card-grid[data-columns="2"] {
  grid-template-columns: repeat(2, 1fr);
}

@media (max-width: 1024px) and (min-width: 768px) {
  .card-grid[data-columns="3"] {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 767px) {
  .card-grid {
    display: none; /* Hidden on mobile, replaced by carousel */
  }
}
```

---

### 3. Card Carousel Component (Mobile Layout)

**Component**: `components/education/card-carousel.blade.php`

**Purpose**: Display cards in swipeable carousel format for mobile view

```blade
@props([
    'cards' => [],
    'carouselId' => 'carousel'
])

<div 
    class="card-carousel" 
    id="{{ $carouselId }}"
    x-data="educationCarousel({{ count($cards) }})"
>
    {{-- Carousel Track --}}
    <div class="carousel-track-container">
        <div 
            class="carousel-track"
            :style="`transform: translateX(-${currentIndex * 100}%)`"
            @touchstart="handleTouchStart($event)"
            @touchmove="handleTouchMove($event)"
            @touchend="handleTouchEnd($event)"
        >
            @foreach($cards as $index => $card)
                <div class="carousel-slide">
                    <x-education.program-card 
                        :card="$card"
                        :sectionId="$carouselId"
                    />
                </div>
            @endforeach
        </div>
    </div>
    
    {{-- Navigation Buttons --}}
    <button 
        class="carousel-nav carousel-prev"
        @click="prevSlide()"
        :disabled="currentIndex === 0"
        :aria-label="`Previous slide, currently on slide ${currentIndex + 1} of ${totalSlides}`"
        x-show="currentIndex > 0"
    >
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
    
    <button 
        class="carousel-nav carousel-next"
        @click="nextSlide()"
        :disabled="currentIndex === totalSlides - 1"
        :aria-label="`Next slide, currently on slide ${currentIndex + 1} of ${totalSlides}`"
        x-show="currentIndex < totalSlides - 1"
    >
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
    
    {{-- Pagination Dots --}}
    <div class="carousel-pagination">
        @foreach($cards as $index => $card)
            <button 
                class="pagination-dot"
                :class="{ 'active': currentIndex === {{ $index }} }"
                @click="goToSlide({{ $index }})"
                :aria-label="`Go to slide ${{{ $index + 1 }}}`"
                :aria-current="currentIndex === {{ $index }} ? 'true' : 'false'"
            ></button>
        @endforeach
    </div>
</div>
```

**Alpine.js Component** (in `resources/js/app.js` or separate module):

```javascript
document.addEventListener('alpine:init', () => {
    Alpine.data('educationCarousel', (totalSlides) => ({
        currentIndex: 0,
        totalSlides: totalSlides,
        touchStartX: 0,
        touchEndX: 0,
        
        nextSlide() {
            if (this.currentIndex < this.totalSlides - 1) {
                this.currentIndex++;
            }
        },
        
        prevSlide() {
            if (this.currentIndex > 0) {
                this.currentIndex--;
            }
        },
        
        goToSlide(index) {
            this.currentIndex = index;
        },
        
        handleTouchStart(event) {
            this.touchStartX = event.touches[0].clientX;
        },
        
        handleTouchMove(event) {
            this.touchEndX = event.touches[0].clientX;
        },
        
        handleTouchEnd() {
            const swipeThreshold = 50;
            const diff = this.touchStartX - this.touchEndX;
            
            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    this.nextSlide();
                } else {
                    this.prevSlide();
                }
            }
            
            this.touchStartX = 0;
            this.touchEndX = 0;
        }
    }));
});
```

**Styling**:

```css
.card-carousel {
  display: none; /* Hidden on desktop */
  position: relative;
  width: 100%;
  overflow: hidden;
  padding: 0 48px 40px;
}

.carousel-track-container {
  overflow: hidden;
  width: 100%;
}

.carousel-track {
  display: flex;
  transition: transform 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.carousel-slide {
  flex: 0 0 100%;
  min-width: 100%;
  padding: 0 8px;
}

.carousel-nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background-color: var(--sand);
  color: var(--forest);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 10;
  cursor: pointer;
  transition: all 0.2s ease;
}

.carousel-nav:hover:not(:disabled) {
  background-color: var(--forest);
  color: var(--sand);
}

.carousel-nav:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

.carousel-prev {
  left: 8px;
}

.carousel-next {
  right: 8px;
}

.carousel-pagination {
  display: flex;
  justify-content: center;
  gap: 8px;
  margin-top: 16px;
}

.pagination-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: var(--mint);
  opacity: 0.3;
  transition: all 0.2s ease;
  cursor: pointer;
}

.pagination-dot.active {
  opacity: 1;
  width: 24px;
  border-radius: 4px;
}

@media (max-width: 767px) {
  .card-carousel {
    display: block;
  }
}
```

---

### 4. Program Card Component

**Component**: `components/education/program-card.blade.php`

**Purpose**: Reusable card component for displaying programs, platforms, and categories

```blade
@props([
    'card' => [],
    'sectionId' => 'default'
])

<article class="program-card" data-section="{{ $sectionId }}">
    {{-- Card Image or Icon --}}
    @if(isset($card['image']))
        <figure class="card-media card-image-wrapper">
            @if(isset($card['icon']))
                <span class="card-icon-badge">{{ $card['icon'] }}</span>
            @endif
            <picture>
                <source srcset="{{ $card['image'] }}" type="image/webp">
                <img 
                    data-src="{{ $card['imageFallback'] ?? $card['image'] }}" 
                    src="{{ asset('images/education/placeholder-education.jpg') }}"
                    alt="{{ $card['imageAlt'] ?? $card['title'] }}" 
                    loading="lazy"
                    class="lazy-image card-image"
                    @error="this.src='{{ asset('images/education/placeholder-education.jpg') }}'"
                >
            </picture>
        </figure>
    @elseif(isset($card['icon']))
        <div class="card-media card-icon-wrapper">
            <span class="card-icon">{{ $card['icon'] }}</span>
        </div>
    @endif
    
    {{-- Card Content --}}
    <div class="card-content">
        <h3 class="card-title">{{ $card['title'] }}</h3>
        <p class="card-description">{{ $card['description'] }}</p>
    </div>
    
    {{-- Card Action --}}
    <div class="card-action">
        <x-education.whatsapp-button 
            :message="$card['whatsappMessage'] ?? 'Halo, saya tertarik dengan informasi program ' . $card['title'] . ' di Godongijo'"
            label="Hubungi via WhatsApp"
        />
    </div>
</article>
```

**Styling**:

```css
.program-card {
  background-color: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(14, 42, 32, 0.08);
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
  height: 100%;
}

.program-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(14, 42, 32, 0.12);
}

.card-media {
  width: 100%;
  position: relative;
}

.card-image-wrapper {
  aspect-ratio: 16 / 9;
  overflow: hidden;
}

.card-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.program-card:hover .card-image {
  transform: scale(1.05);
}

.card-icon-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  font-size: 2rem;
  background-color: rgba(255, 255, 255, 0.9);
  padding: 8px 12px;
  border-radius: 8px;
  z-index: 2;
}

.card-icon-wrapper {
  padding: 40px 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, var(--mint) 0%, var(--sand) 100%);
}

.card-icon {
  font-size: 4rem;
  line-height: 1;
}

.card-content {
  flex: 1;
  padding: 24px;
}

.card-title {
  font-family: 'Fraunces', serif;
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--forest);
  margin-bottom: 12px;
  line-height: 1.3;
}

.card-description {
  font-size: 0.9375rem;
  line-height: 1.6;
  color: var(--ink);
  opacity: 0.85;
}

.card-action {
  padding: 0 24px 24px;
  margin-top: auto;
}
```

---

### 5. WhatsApp Button Component

**Component**: `components/education/whatsapp-button.blade.php`

**Purpose**: Standardized WhatsApp contact button with pre-filled messages

```blade
@props([
    'message' => 'Halo, saya tertarik dengan informasi program di Godongijo',
    'phoneNumber' => config('app.whatsapp_number', '6281234567890'),
    'label' => 'Hubungi via WhatsApp',
    'variant' => 'primary'
])

@php
    $encodedMessage = urlencode($message);
    $whatsappUrl = "https://wa.me/{$phoneNumber}?text={$encodedMessage}";
@endphp

<a 
    href="{{ $whatsappUrl }}" 
    target="_blank"
    rel="noopener noreferrer"
    class="whatsapp-btn whatsapp-btn-{{ $variant }}"
    @click="handleWhatsAppClick($event)"
>
    <svg class="whatsapp-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M17 9.5C17 13.6421 13.6421 17 9.5 17C8.23 17 7.03 16.69 6 16.14L3 17L3.86 14.14C3.28 13.09 3 11.84 3 10.5C3 6.35786 6.35786 3 10.5 3C14.6421 3 17 6.35786 17 9.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M13.5 11.5C13.5 11.5 13 11 12.5 11C12 11 11.5 11.5 11.5 11.5C11.5 11.5 10.5 11 9.5 10C8.5 9 8 8 8 8C8 8 8.5 7.5 8.5 7C8.5 6.5 8 6 8 6C8 6 7.5 5 7 5.5C6.5 6 6.5 6.5 6.5 7C6.5 8 7.5 10 9 11.5C10.5 13 12.5 13.5 13 13.5C13.5 13.5 14 13.5 14.5 13C15 12.5 14.5 12 14.5 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <span>{{ $label }}</span>
</a>
```

**Styling**:

```css
.whatsapp-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  border-radius: 50px;
  font-size: 0.9375rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.25s ease;
  width: 100%;
  justify-content: center;
}

.whatsapp-btn-primary {
  background-color: #25D366; /* WhatsApp green */
  color: white;
}

.whatsapp-btn-primary:hover {
  background-color: #1EBE57;
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(37, 211, 102, 0.3);
}

.whatsapp-btn-secondary {
  background-color: transparent;
  border: 2px solid #25D366;
  color: #25D366;
}

.whatsapp-btn-secondary:hover {
  background-color: #25D366;
  color: white;
}

.whatsapp-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}
```

**JavaScript Behavior** (optional analytics tracking):

```javascript
// In resources/js/app.js
window.handleWhatsAppClick = function(event) {
    // Optional: Track WhatsApp clicks for analytics
    if (typeof gtag !== 'undefined') {
        const message = new URL(event.currentTarget.href).searchParams.get('text');
        gtag('event', 'whatsapp_click', {
            'event_category': 'engagement',
            'event_label': message,
        });
    }
};
```

---

### 6. Program Detail Alternating Layout Component

**Component**: `components/education/program-detail-alternating.blade.php`

**Purpose**: Display detailed program information in alternating left-right image-text layout (Environmental Education section)

```blade
@props([
    'programs' => [],
    'sectionTitle' => 'Program Details'
])

<section class="program-detail-section">
    <div class="container">
        <h2 class="section-title">{{ $sectionTitle }}</h2>
        
        @foreach($programs as $index => $program)
            @php
                $isImageLeft = $index % 2 === 0;
            @endphp
            
            <article class="program-detail-item {{ $isImageLeft ? 'image-left' : 'image-right' }}">
                <figure class="program-detail-image">
                    <picture>
                        <source srcset="{{ $program['image'] }}" type="image/webp">
                        <img 
                            data-src="{{ $program['imageFallback'] ?? $program['image'] }}" 
                            src="{{ asset('images/education/placeholder-education.jpg') }}"
                            alt="{{ $program['imageAlt'] ?? $program['title'] }}" 
                            loading="lazy"
                            class="lazy-image"
                            @error="this.src='{{ asset('images/education/placeholder-education.jpg') }}'"
                        >
                    </picture>
                </figure>
                
                <div class="program-detail-content">
                    <h3 class="program-detail-title">{{ $program['title'] }}</h3>
                    <p class="program-detail-description">{{ $program['description'] }}</p>
                </div>
            </article>
        @endforeach
    </div>
</section>
```

**Styling**:

```css
.program-detail-section {
  padding: 80px 0;
}

.section-title {
  font-family: 'Fraunces', serif;
  font-size: clamp(2rem, 4vw, 2.5rem);
  font-weight: 700;
  color: var(--forest);
  text-align: center;
  margin-bottom: 48px;
}

.program-detail-item {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 48px;
  align-items: center;
  margin-bottom: 64px;
}

.program-detail-item:last-child {
  margin-bottom: 0;
}

.program-detail-item.image-left {
  grid-template-areas: "image content";
}

.program-detail-item.image-right {
  grid-template-areas: "content image";
}

.program-detail-item.image-left .program-detail-image {
  grid-area: image;
}

.program-detail-item.image-right .program-detail-image {
  grid-area: image;
}

.program-detail-item .program-detail-content {
  grid-area: content;
}

.program-detail-image {
  aspect-ratio: 4 / 3;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 8px 24px rgba(14, 42, 32, 0.12);
}

.program-detail-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.program-detail-title {
  font-family: 'Fraunces', serif;
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--forest);
  margin-bottom: 16px;
}

.program-detail-description {
  font-size: 1rem;
  line-height: 1.7;
  color: var(--ink);
  opacity: 0.85;
}

/* Mobile: Stack vertically */
@media (max-width: 767px) {
  .program-detail-item {
    grid-template-columns: 1fr;
    grid-template-areas: 
      "image"
      "content" !important;
    gap: 24px;
    margin-bottom: 48px;
  }
}
```

---

### 7. Testimonial Section Component

**Component**: `components/education/testimonial-section.blade.php`

**Purpose**: Display testimonial with horizontal photo-quote layout

```blade
@props([
    'photo' => asset('images/education/testimonial-photo.webp'),
    'photoFallback' => asset('images/education/testimonial-photo.jpg'),
    'photoAlt' => 'Educational tourism activities at Godong Ijo',
    'quote' => 'Ada banyak wahana yang ditawarkan...',
    'source' => '— kapanlagi.com'
])

<section class="testimonial-section">
    <div class="container">
        <div class="testimonial-content">
            {{-- Testimonial Photo --}}
            <figure class="testimonial-photo">
                <picture>
                    <source srcset="{{ $photo }}" type="image/webp">
                    <img 
                        data-src="{{ $photoFallback }}" 
                        src="{{ asset('images/education/placeholder-education.jpg') }}"
                        alt="{{ $photoAlt }}" 
                        loading="lazy"
                        class="lazy-image"
                        @error="this.src='{{ asset('images/education/placeholder-education.jpg') }}'"
                    >
                </picture>
            </figure>
            
            {{-- Quote Box --}}
            <div class="testimonial-quote-box">
                <svg class="quote-icon" width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 28C10 24.686 12.686 22 16 22C19.314 22 22 24.686 22 28C22 31.314 19.314 34 16 34C14.686 34 13.447 33.515 12.464 32.707C12.809 30.619 13 28.352 13 26C13 24.343 13.343 22.343 10 22.343V18C16.343 18 19 22.343 19 26C19 28.352 18.809 30.619 18.464 32.707C17.481 33.515 16.242 34 14.928 34H16M26 28C26 24.686 28.686 22 32 22C35.314 22 38 24.686 38 28C38 31.314 35.314 34 32 34C30.686 34 29.447 33.515 28.464 32.707C28.809 30.619 29 28.352 29 26C29 24.343 29.343 22.343 26 22.343V18C32.343 18 35 22.343 35 26C35 28.352 34.809 30.619 34.464 32.707C33.481 33.515 32.242 34 30.928 34H32" fill="var(--mint)" opacity="0.2"/>
                </svg>
                
                <blockquote class="testimonial-quote">
                    <p class="quote-text">{{ $quote }}</p>
                    <cite class="quote-source">{{ $source }}</cite>
                </blockquote>
            </div>
        </div>
    </div>
</section>
```

**Styling**:

```css
.testimonial-section {
  padding: 80px 0;
  background-color: var(--sand);
}

.testimonial-content {
  display: grid;
  grid-template-columns: 45% 55%;
  gap: 48px;
  align-items: center;
}

.testimonial-photo {
  aspect-ratio: 4 / 3;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 8px 24px rgba(14, 42, 32, 0.12);
}

.testimonial-photo img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.testimonial-quote-box {
  background-color: white;
  padding: 40px;
  border-radius: 16px;
  box-shadow: 0 4px 16px rgba(14, 42, 32, 0.08);
  position: relative;
}

.quote-icon {
  position: absolute;
  top: 20px;
  left: 20px;
  opacity: 0.3;
}

.testimonial-quote {
  position: relative;
  z-index: 1;
}

.quote-text {
  font-size: 1.125rem;
  line-height: 1.7;
  color: var(--ink);
  margin-bottom: 16px;
  font-style: italic;
}

.quote-source {
  display: block;
  font-size: 1rem;
  font-style: normal;
  font-weight: 600;
  color: var(--forest);
}

/* Mobile: Stack vertically */
@media (max-width: 767px) {
  .testimonial-content {
    grid-template-columns: 1fr;
    gap: 32px;
  }
  
  .testimonial-quote-box {
    padding: 32px 24px;
  }
  
  .quote-text {
    font-size: 1rem;
  }
}
```


## Data Models

### Controller Data Structure

The `StaticPageController::education()` method will be updated to provide comprehensive structured data for the education tourism page.

#### Updated Controller Method Signature

```php
/**
 * Display Wisata Edukasi page with comprehensive Ecotainment program data
 *
 * @return View
 */
public function education(): View
{
    // Get comprehensive page data
    $pageData = $this->getEducationData();
    
    // Generate breadcrumbs
    $breadcrumbs = $this->breadcrumbService->generate('education', null, [
        'name' => 'Wisata Edukasi',
    ]);
    
    // Generate SEO metadata
    $seoData = $this->seoService->generateMetadata('education', [
        'route' => 'education',
        'breadcrumbs' => $breadcrumbs,
        'title' => 'Wisata Edukasi - Ecotainment Godongijo | The Waterfall',
        'description' => 'Program wisata edukasi Ecotainment Godongijo untuk sekolah dan institusi pendidikan. Platform Virtual, Goes To School Field Trip, dan Fieldtrip at Godongijo dengan program Lingkungan, Science, dan Art.',
    ]);
    
    // Get navigation data
    $navigation = $this->navigationService->getMainNavigation();
    $cta = $this->navigationService->getCTA();
    $currentRoute = Route::currentRouteName();
    
    return view('pages.education', [
        'pageData' => $pageData,
        'breadcrumbs' => $breadcrumbs,
        'seoData' => $seoData,
        'navigation' => $navigation,
        'cta' => $cta,
        'currentRoute' => $currentRoute,
    ]);
}
```

#### Complete Page Data Structure

```php
private function getEducationData(): array
{
    return [
        // Hero Section
        'hero' => [
            'backgroundImage' => asset('images/education/hero-background.webp'),
            'backgroundImageFallback' => asset('images/education/hero-background.jpg'),
            'title' => 'Ecotainment Godongijo',
            'description' => 'Ecotainment adalah suatu kegiatan karyawisata yang memiliki nilai edukasi. Pembelajaran dalam membantu meningkatkan kecerdasan. Meningkatkan kreativitas wawasan ilmu pengetahuan terhadap lingkungan sekitar.',
        ],
        
        // Platform Fieldtrip Section (3 cards)
        'platformFieldtrip' => [
            'sectionTitle' => 'Platform Fieldtrip',
            'cards' => [
                [
                    'id' => 'virtual-fieldtrip',
                    'icon' => '💻',
                    'title' => 'Virtual Fieldtrip',
                    'description' => 'Bimbingan edukasi dari instruktur (Guide From Instruktur) setelah bahan praktek dikirimkan oleh kami ke siswa/i dan akan dipandu oleh kami lewat zoom meeting',
                    'whatsappMessage' => 'Halo, saya tertarik dengan informasi program Virtual Fieldtrip di Godongijo',
                ],
                [
                    'id' => 'goes-to-school',
                    'icon' => '🚌',
                    'title' => 'Goes To School Field Trip',
                    'description' => 'Bimbingan edukasi oleh instruktur Godongijo secara tatap muka di sekolah',
                    'whatsappMessage' => 'Halo, saya tertarik dengan informasi program Goes To School Field Trip di Godongijo',
                ],
                [
                    'id' => 'at-godongijo',
                    'icon' => '🌳',
                    'title' => 'Fieldtrip at Godongijo',
                    'description' => 'Bimbingan edukasi oleh instruktur Godongijo secara on site / secara tatap muka di Godongijo',
                    'whatsappMessage' => 'Halo, saya tertarik dengan informasi program Fieldtrip at Godongijo',
                ],
            ],
        ],
        
        // Program Category Section (3 cards with images)
        'programCategory' => [
            'sectionTitle' => 'Kategori Program Edukasi',
            'cards' => [
                [
                    'id' => 'edukasi-lingkungan',
                    'image' => asset('images/education/category-environmental.webp'),
                    'imageFallback' => asset('images/education/category-environmental.jpg'),
                    'imageAlt' => 'Program Edukasi Lingkungan di Godong Ijo',
                    'icon' => '🌱', // Optional badge
                    'title' => 'Edukasi Lingkungan',
                    'description' => 'Edukasi lingkungan mengajak siswa untuk lebih peduli pada alam dengan cara mengenal hewan, belajar bercocok tanam dan mengolah sampah menjadi barang yang bermanfaat.',
                    'whatsappMessage' => 'Halo, saya tertarik dengan informasi program Edukasi Lingkungan di Godongijo',
                ],
                [
                    'id' => 'science',
                    'image' => asset('images/education/category-science.webp'),
                    'imageFallback' => asset('images/education/category-science.jpg'),
                    'imageAlt' => 'Program Science di Godong Ijo',
                    'icon' => '🔬',
                    'title' => 'Science',
                    'description' => 'Siswa akan mendapatkan edukasi sains praktis yang mencakup pembuatan bio-diesel, detektor banjir, kompas, hingga dasar-dasar listrik dan robotik.',
                    'whatsappMessage' => 'Halo, saya tertarik dengan informasi program Science di Godongijo',
                ],
                [
                    'id' => 'art',
                    'image' => asset('images/education/category-art.webp'),
                    'imageFallback' => asset('images/education/category-art.jpg'),
                    'imageAlt' => 'Program Art di Godong Ijo',
                    'icon' => '🎨',
                    'title' => 'Art',
                    'description' => 'Program ini memberikan edukasi seni yang mencakup seni lukis, rupa, memasak, hingga tari tradisional.',
                    'whatsappMessage' => 'Halo, saya tertarik dengan informasi program Art di Godongijo',
                ],
            ],
        ],
        
        // Environmental Education Programs (alternating layout)
        'environmentalPrograms' => [
            'sectionTitle' => 'Program Edukasi Lingkungan',
            'programs' => [
                [
                    'id' => 'learning-animals',
                    'image' => asset('images/education/learning-animals.webp'),
                    'imageFallback' => asset('images/education/learning-animals.jpg'),
                    'imageAlt' => 'Learning About Animals program at Godong Ijo',
                    'title' => 'Learning About Animals',
                    'description' => 'Program Edukasi Lingkungan (Interaction & Knowledge) merupakan inisiatif pembelajaran komprehensif yang dirancang untuk memperdalam pemahaman peserta didik terhadap dinamika alam. Melalui program ini, siswa diajak untuk secara interaktif mengkaji struktur ekosistem, karakteristik berbagai habitat, serta aspek biologi dan perilaku satwa, guna membangun kesadaran dan kepedulian ekologis yang berkelanjutan.',
                ],
                [
                    'id' => 'urban-farming',
                    'image' => asset('images/education/urban-farming.webp'),
                    'imageFallback' => asset('images/education/urban-farming.jpg'),
                    'imageAlt' => 'Urban Farming program at Godong Ijo',
                    'title' => 'Urban Farming',
                    'description' => 'Program Edukasi Urban Farming dirancang untuk membekali siswa/i dengan keterampilan praktik pertanian modern dalam mengoptimalkan pemanfaatan lahan terbatas. Melalui pendekatan aplikatif, peserta didik akan menguasai teknik budidaya inovatif berkelanjutan. Sistem vertical garden, penanaman berbasis hidroponik serta metode perbanyakan tanaman secara vegetatif (mencangkok) untuk mendukung kemandirian lingkungan.',
                ],
                [
                    'id' => 'renewable-energy',
                    'image' => asset('images/education/renewable-energy.webp'),
                    'imageFallback' => asset('images/education/renewable-energy.jpg'),
                    'imageAlt' => 'Renewable Energy program at Godong Ijo',
                    'title' => 'Renewable Energy',
                    'description' => 'Program Edukasi Renewable Energy berfokus pada eksplorasi energi ramah lingkungan melalui pendekatan Ilmu Terapan (Applied Science). Kami membekali peserta dengan keterampilan praktis untuk mengembangkan dan mengimplementasikan inovasi konversi energi bersih secara nyata di masyarakat.',
                ],
            ],
        ],
        
        // Art Programs (card layout)
        'artPrograms' => [
            'sectionTitle' => 'Program Seni',
            'cards' => [
                [
                    'id' => 'art-painting',
                    'title' => 'Art Painting',
                    'description' => 'Program edukasi yang mengajarkan siswa/i melukis pada sebuah media menggunakan alat-alat lukis',
                    'whatsappMessage' => 'Halo, saya tertarik dengan informasi program Art Painting di Godongijo',
                ],
                [
                    'id' => 'traditional-dance',
                    'title' => 'Traditional Dance',
                    'description' => 'Program edukasi yang mengajarkan siswa/i tarian-tarian tradisional Indonesia',
                    'whatsappMessage' => 'Halo, saya tertarik dengan informasi program Traditional Dance di Godongijo',
                ],
                [
                    'id' => 'fine-art',
                    'title' => 'Fine Art',
                    'description' => 'Program edukasi yang mengajarkan siswa/i berkreasi membuat karya seni rupa',
                    'whatsappMessage' => 'Halo, saya tertarik dengan informasi program Fine Art di Godongijo',
                ],
            ],
        ],
        
        // Science Programs (card layout)
        'sciencePrograms' => [
            'sectionTitle' => 'Program Sains',
            'cards' => [
                [
                    'id' => 'aero-glider',
                    'title' => 'Aero Glider (Prototype Pembangkit Listrik Tenaga Angin)',
                    'description' => 'Siswa/i akan belajar bagaimana menghasilkan listrik dari rangkain pembangkit tenaga angin, serta dengan sumber listrik lainnya.',
                    'whatsappMessage' => 'Halo, saya tertarik dengan informasi program Aero Glider di Godongijo',
                ],
                [
                    'id' => 'profesor-cilik',
                    'title' => 'Profesor Cilik',
                    'description' => 'Sains teknologi sederhana yang ramah lingkungan seperti membuat detektor banjir, membuat kompas, pembersih air sederhana, dan membuat shampoo / conditioner',
                    'whatsappMessage' => 'Halo, saya tertarik dengan informasi program Profesor Cilik di Godongijo',
                ],
            ],
        ],
        
        // Testimonial Section
        'testimonial' => [
            'photo' => asset('images/education/testimonial-photo.webp'),
            'photoFallback' => asset('images/education/testimonial-photo.jpg'),
            'photoAlt' => 'Educational tourism activities and facilities at Godong Ijo',
            'quote' => 'Ada banyak wahana yang ditawarkan seperti lokasi khusus aneka tanaman dan reptil. Tempat outbond dan wahana edukasi lain khusus anak-anak. Tak heran banyak rombongan sekolah yang berkunjung ke sini untuk refreshing.',
            'source' => '— kapanlagi.com',
        ],
        
        // WhatsApp Configuration
        'whatsapp' => [
            'phoneNumber' => config('app.whatsapp_number', '6281234567890'),
        ],
    ];
}
```

### Array Structure Definitions

#### Card Data Structure

Used for Platform Fieldtrip, Program Category, Art Programs, and Science Programs:

```php
[
    'id' => string,                    // Unique identifier (kebab-case)
    'icon' => string|null,             // Emoji icon (optional)
    'image' => string|null,            // WebP image URL (optional)
    'imageFallback' => string|null,    // JPG fallback URL (optional)
    'imageAlt' => string|null,         // Image alt text (optional)
    'title' => string,                 // Card title
    'description' => string,           // Card description text
    'whatsappMessage' => string,       // Pre-filled WhatsApp message
]
```

**Usage Variations**:
- **Icon-only cards** (Platform Fieldtrip): Use `icon`, omit `image`
- **Image cards** (Program Category): Use `image`, `imageFallback`, `imageAlt`, optionally include `icon` as badge
- **Text-only cards** (Art/Science): Omit both `icon` and `image`

#### Program Detail Structure

Used for Environmental Education alternating layout:

```php
[
    'id' => string,                    // Unique identifier (kebab-case)
    'image' => string,                 // WebP image URL
    'imageFallback' => string,         // JPG fallback URL
    'imageAlt' => string,              // Image alt text
    'title' => string,                 // Program title
    'description' => string,           // Long-form description (paragraph)
]
```

#### Hero Section Structure

```php
[
    'backgroundImage' => string,           // WebP background image URL
    'backgroundImageFallback' => string,   // JPG fallback URL
    'title' => string,                     // Hero title text
    'description' => string,               // Hero description paragraph
]
```

#### Testimonial Structure

```php
[
    'photo' => string,           // WebP testimonial photo URL
    'photoFallback' => string,   // JPG fallback URL
    'photoAlt' => string,        // Photo alt text
    'quote' => string,           // Testimonial quote text
    'source' => string,          // Quote attribution/source
]
```

### Configuration Values

#### WhatsApp Configuration

Add to `config/app.php`:

```php
return [
    // ... existing config
    
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Business Number
    |--------------------------------------------------------------------------
    |
    | The official WhatsApp business number for customer inquiries.
    | Format: Country code + phone number (no spaces, no +)
    | Example: '6281234567890' for +62 812-3456-7890
    |
    */
    'whatsapp_number' => env('WHATSAPP_NUMBER', '6281234567890'),
];
```

Add to `.env`:

```env
WHATSAPP_NUMBER=6281234567890
```

### SEO Metadata Structure

The `SEOService::generateMetadata()` method should generate structured data for educational tourism:

```php
[
    'title' => 'Wisata Edukasi - Ecotainment Godongijo | The Waterfall',
    'description' => 'Program wisata edukasi Ecotainment Godongijo untuk sekolah dan institusi pendidikan. Platform Virtual, Goes To School Field Trip, dan Fieldtrip at Godongijo dengan program Lingkungan, Science, dan Art.',
    'keywords' => [
        'wisata edukasi',
        'ecotainment',
        'fieldtrip',
        'edukasi lingkungan',
        'program sains',
        'program seni',
        'godongijo',
        'the waterfall',
    ],
    'og' => [
        'type' => 'website',
        'url' => route('education'),
        'title' => 'Wisata Edukasi - Ecotainment Godongijo',
        'description' => 'Program wisata edukasi dengan platform Virtual Fieldtrip, Goes To School Field Trip, dan Fieldtrip at Godongijo. Kategori program: Edukasi Lingkungan, Science, Art.',
        'image' => asset('images/education/og-image.jpg'),
    ],
    'structuredData' => [
        '@context' => 'https://schema.org',
        '@type' => 'EducationalOrganization',
        'name' => 'Ecotainment Godongijo - The Waterfall',
        'description' => 'Program wisata edukasi untuk sekolah dan institusi pendidikan',
        'url' => route('education'),
        'image' => asset('images/education/hero-background.jpg'),
        'telephone' => '+62-21-7471-0678',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => 'Jalan Cinangka Raya Km 10 No. 60',
            'addressLocality' => 'Depok',
            'addressRegion' => 'Jawa Barat',
            'postalCode' => '16517',
            'addressCountry' => 'ID',
        ],
    ],
];
```

### Breadcrumb Structure

The `BreadcrumbService::generate()` method provides hierarchical navigation:

```php
[
    [
        'label' => 'Home',
        'url' => route('landing'),
        'current' => false,
    ],
    [
        'label' => 'Wisata Edukasi',
        'url' => route('education'),
        'current' => true,
    ],
]
```


## Correctness Properties

### Assessment: Property-Based Testing Not Applicable

After analyzing all acceptance criteria in the requirements document, property-based testing is **not applicable** to this education tourism page redesign feature.

**Why PBT Doesn't Apply:**

This is a **UI rendering and visual presentation project** with the following characteristics:

1. **No computational logic**: The page presents structured content without algorithms, parsers, serializers, or data transformations
2. **Visual requirements only**: All 22 acceptance criteria specify presentation behaviors:
   - "SHALL display" (hero section, cards, images, text)
   - "SHALL arrange" (layout patterns, alternating, grid, carousel)
   - "SHALL use" (colors, styling, responsive breakpoints)
   - "SHALL open" (WhatsApp links with pre-filled messages)
3. **No universal properties**: There are no behaviors that can be expressed as "for all inputs X, property P(X) holds"
4. **Static content structure**: The page displays predetermined content from the controller without processing varying user inputs
5. **Infrastructure-like characteristics**: Similar to IaC, this feature configures and presents a UI structure rather than computing outputs from inputs

**Requirements Analysis:**

- **Requirements 1-19**: Visual display, layout, styling, responsive behavior, SEO metadata
- **Requirements 20-22**: WhatsApp integration, image loading, fallback states
- All testable aspects involve:
  - Visual rendering (does the hero section display correctly?)
  - Layout behavior (does carousel work on mobile?)
  - Navigation (does WhatsApp button open with correct message?)
  - Responsive design (do breakpoints work?)
  - Accessibility (are ARIA labels present?)

None of these involve data transformation, algorithmic behavior, or input-varying logic suitable for property-based testing.

**Testing Strategy**: See the Testing Strategy section below for comprehensive coverage using:
- Laravel Feature Tests (HTTP responses, view data, routing)
- Laravel Dusk Browser Tests (actual rendering, interactions, responsive behavior)
- Visual Regression Testing (UI appearance consistency)
- Accessibility Audits (WCAG compliance, ARIA labels)
- Performance Monitoring (load times, image optimization)


## Error Handling

### Image Loading Errors

**Strategy**: Graceful degradation with fallbacks and error states

#### 1. WebP Format Fallback (Browser Compatibility)

All images use the `<picture>` element with WebP source and JPEG/PNG fallback:

```blade
<picture>
    <source srcset="{{ $image['src'] }}" type="image/webp">
    <img 
        src="{{ $image['fallback'] }}" 
        alt="{{ $image['alt'] }}"
        loading="lazy"
    >
</picture>
```

**Behavior**:
- Modern browsers: Load WebP (smaller file size, better performance)
- Older browsers: Automatically fall back to JPEG/PNG
- No JavaScript required for format fallback

#### 2. Missing Image Fallback (Loading Errors)

All images include error handling with Alpine.js `@error` directive:

```blade
<img 
    src="{{ $image['src'] }}" 
    alt="{{ $image['alt'] }}"
    @error="this.src='{{ asset('images/education/placeholder-education.jpg') }}'"
>
```

**Behavior**:
- If image fails to load (404, network error, CORS issue)
- Browser triggers error event
- Alpine.js replaces src with fallback placeholder
- Maintains layout integrity (no broken image icons)

**Placeholder Image Requirements**:
- Aspect ratio matches expected image (16:9 or 4:3)
- Subtle branded design (logo watermark, neutral background)
- File size < 50KB for fast loading
- Located at `public/images/education/placeholder-education.jpg`

#### 3. Lazy Loading States

Images below the fold use lazy loading with skeleton/placeholder:

```blade
<img 
    data-src="{{ $image['src'] }}" 
    src="{{ asset('images/education/placeholder-blur.jpg') }}"
    loading="lazy"
    class="lazy-image"
>
```

**CSS for Loading State**:

```css
.lazy-image {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}

.lazy-image[src*="placeholder-blur.jpg"] {
    filter: blur(10px);
    transform: scale(1.1);
}

@keyframes shimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
```

**Behavior**:
- Initial load: Shows blurred low-quality preview or skeleton
- Browser intersects viewport: Swaps to full-quality image
- Smooth transition with CSS

#### 4. Hero Background Image Error

Hero section has special handling due to critical importance:

```blade
{{-- In hero component --}}
<picture class="hero-background">
    <source srcset="{{ $backgroundImage }}" type="image/webp">
    <img 
        src="{{ $backgroundImageFallback }}" 
        alt="Educational activities"
        loading="eager"
        @error="handleHeroImageError($el)"
    >
</picture>
```

**JavaScript Handler**:

```javascript
// In resources/js/app.js
window.handleHeroImageError = function(element) {
    // Try final fallback
    element.src = '/images/education/hero-fallback-solid.jpg';
    
    // If even fallback fails, apply gradient background
    element.addEventListener('error', function() {
        element.parentElement.parentElement.classList.add('hero-gradient-fallback');
    }, { once: true });
};
```

**CSS Fallback Style**:

```css
.hero-gradient-fallback {
    background: linear-gradient(135deg, var(--forest) 0%, var(--mint) 100%);
}

.hero-gradient-fallback .hero-bg-image {
    display: none;
}
```

---

### WhatsApp Integration Errors

#### 1. Desktop vs Mobile Detection

```php
{{-- In WhatsApp button component --}}
@php
    $isMobile = preg_match('/iPhone|Android/i', request()->userAgent());
    $whatsappUrl = $isMobile 
        ? "https://wa.me/{$phoneNumber}?text={$encodedMessage}"
        : "https://web.whatsapp.com/send?phone={$phoneNumber}&text={$encodedMessage}";
@endphp
```

**Behavior**:
- Mobile devices: Open native WhatsApp app
- Desktop browsers: Open WhatsApp Web in new tab
- Fallback: If native app not installed, web version still accessible

#### 2. Message Encoding Errors

```php
@php
    // Properly encode message to handle special characters
    $encodedMessage = rawurlencode($message);
    
    // Validate message length (WhatsApp limit ~2000 characters)
    if (strlen($message) > 2000) {
        $message = substr($message, 0, 1997) . '...';
        $encodedMessage = rawurlencode($message);
    }
@endphp
```

**Error Prevention**:
- Use `rawurlencode()` for proper URL encoding
- Handle special characters (emoji, quotes, line breaks)
- Truncate messages exceeding WhatsApp limits
- Validate phone number format

#### 3. Missing Configuration

```php
// In WhatsApp button component
@php
    $phoneNumber = config('app.whatsapp_number');
    
    if (empty($phoneNumber)) {
        Log::warning('WhatsApp number not configured in app.whatsapp_number');
        $phoneNumber = '6281234567890'; // Fallback number
    }
    
    // Validate format (numbers only, no +)
    $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
@endphp
```

---

### Browser Compatibility Issues

#### 1. CSS Grid Fallback

Modern layout uses CSS Grid with Flexbox fallback:

```css
.card-grid {
    /* Flexbox fallback for older browsers */
    display: flex;
    flex-wrap: wrap;
    gap: 24px;
    
    /* CSS Grid for modern browsers (overrides flexbox) */
    display: grid;
    grid-template-columns: repeat(3, 1fr);
}

/* Flexbox-specific adjustments */
.card-grid > * {
    flex: 1 1 calc(33.333% - 24px);
    min-width: 280px;
}

/* Grid-specific adjustments (overrides flexbox) */
@supports (display: grid) {
    .card-grid > * {
        flex: none;
        min-width: auto;
    }
}
```

#### 2. CSS Custom Properties Fallback

```css
.hero-title {
    color: #F6F2E9; /* Static fallback */
    color: var(--sand); /* Modern browsers */
}

.program-card {
    background-color: white; /* Static fallback */
    background-color: var(--sand); /* Modern browsers */
}
```

#### 3. Alpine.js Unavailable

Page remains functional with progressive enhancement:

```blade
{{-- Desktop grid always visible (no Alpine required) --}}
<div class="card-grid desktop-only">
    @foreach($cards as $card)
        <x-education.program-card :card="$card" />
    @endforeach
</div>

{{-- Mobile carousel degrades to vertical list if Alpine fails --}}
<div class="card-carousel mobile-only" x-data="educationCarousel({{ count($cards) }})">
    {{-- Carousel with Alpine --}}
</div>

<noscript>
    {{-- Fallback: Show all cards stacked vertically --}}
    <div class="card-list mobile-only">
        @foreach($cards as $card)
            <x-education.program-card :card="$card" />
        @endforeach
    </div>
</noscript>
```

**CSS for Graceful Degradation**:

```css
/* If JavaScript disabled, show static list */
.card-carousel {
    display: block;
}

.carousel-track {
    display: flex;
    flex-direction: column;
    gap: 24px;
    transform: none !important;
}

.carousel-slide {
    flex: none;
    width: 100%;
}

.carousel-nav,
.carousel-pagination {
    display: none;
}

/* Re-enable carousel when Alpine.js loads */
[x-cloak] {
    display: none !important;
}
```

---

### Performance Degradation

#### 1. Slow Network Handling

**Critical CSS Inlining** (in layout):

```blade
<head>
    {{-- Inline critical above-fold CSS --}}
    <style>
        /* Minimal critical styles for hero and navigation */
        .navbar { /* ... */ }
        .hero-ecotainment { /* ... */ }
        .hero-overlay { /* ... */ }
        .hero-content { /* ... */ }
    </style>
    
    {{-- Load full stylesheet asynchronously --}}
    <link rel="preload" href="{{ asset('css/app.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('css/app.css') }}"></noscript>
</head>
```

**Image Optimization**:

```php
// Generate responsive image sizes
$image['srcset'] = implode(', ', [
    asset('images/education/hero-bg-400w.webp') . ' 400w',
    asset('images/education/hero-bg-800w.webp') . ' 800w',
    asset('images/education/hero-bg-1200w.webp') . ' 1200w',
    asset('images/education/hero-bg-1920w.webp') . ' 1920w',
]);

$image['sizes'] = '(max-width: 767px) 100vw, (max-width: 1024px) 100vw, 1920px';
```

#### 2. Asset Loading Errors

**Vite Manifest Fallback**:

```blade
{{-- In layout head --}}
@vite(['resources/css/app.css', 'resources/js/app.js'])

{{-- Fallback if Vite assets fail --}}
@if(app()->environment('production'))
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" onerror="this.onerror=null;this.href='/css/app-fallback.css'">
    <script src="{{ asset('js/app.js') }}" onerror="this.onerror=null;this.src='/js/app-fallback.js'"></script>
@endif
```

#### 3. Third-Party Service Failures

**Font Loading Fallback**:

```css
@font-face {
    font-family: 'Fraunces';
    font-display: swap; /* Show fallback immediately, swap when loaded */
    src: url('https://fonts.bunny.net/...');
}

body {
    font-family: 'Sora', system-ui, -apple-system, 'Segoe UI', sans-serif;
}

h1, h2, h3 {
    font-family: 'Fraunces', Georgia, serif;
}
```

---

### Data Validation Errors

#### 1. Missing Required Data

```php
// In controller
private function getEducationData(): array
{
    try {
        $data = [
            'hero' => $this->getHeroData(),
            'platformFieldtrip' => $this->getPlatformFieldtripData(),
            // ... other sections
        ];
        
        // Validate critical data exists
        if (empty($data['hero']['title'])) {
            Log::error('Missing hero title in education page data');
            $data['hero']['title'] = 'Ecotainment Godongijo'; // Fallback
        }
        
        return $data;
        
    } catch (\Exception $e) {
        Log::error('Error generating education page data: ' . $e->getMessage());
        
        // Return minimal safe fallback data
        return $this->getFallbackEducationData();
    }
}

private function getFallbackEducationData(): array
{
    return [
        'hero' => [
            'title' => 'Wisata Edukasi',
            'description' => 'Program wisata edukasi untuk sekolah dan institusi pendidikan.',
            'backgroundImage' => asset('images/placeholders/default-hero.jpg'),
            'backgroundImageFallback' => asset('images/placeholders/default-hero.jpg'),
        ],
        'platformFieldtrip' => ['cards' => []],
        'programCategory' => ['cards' => []],
        // ... minimal structure
    ];
}
```

#### 2. Invalid Asset Paths

```php
// Helper function in controller
private function validateAssetPath(string $path, string $fallback): string
{
    $fullPath = public_path($path);
    
    if (!file_exists($fullPath)) {
        Log::warning("Asset not found: {$path}");
        return asset($fallback);
    }
    
    return asset($path);
}

// Usage
'image' => $this->validateAssetPath(
    'images/education/category-environmental.webp',
    'images/education/placeholder-education.jpg'
),
```

---

### Laravel Error Handling

#### 1. View Rendering Errors

```php
// In controller
public function education(): View
{
    try {
        $pageData = $this->getEducationData();
        $breadcrumbs = $this->breadcrumbService->generate('education', null, ['name' => 'Wisata Edukasi']);
        $seoData = $this->seoService->generateMetadata('education', [/* ... */]);
        
        return view('pages.education', [
            'pageData' => $pageData,
            'breadcrumbs' => $breadcrumbs,
            'seoData' => $seoData,
            // ... other data
        ]);
        
    } catch (\Exception $e) {
        Log::error('Education page rendering error: ' . $e->getMessage(), [
            'exception' => $e,
            'trace' => $e->getTraceAsString(),
        ]);
        
        // Show user-friendly error page
        return view('errors.education-unavailable')->with([
            'message' => 'Halaman wisata edukasi sedang dalam perbaikan. Silakan coba lagi nanti.',
        ]);
    }
}
```

#### 2. Service Unavailability

```php
// In controller constructor
public function __construct(
    NavigationService $navigationService,
    BreadcrumbService $breadcrumbService,
    SEOService $seoService
) {
    $this->navigationService = $navigationService;
    $this->breadcrumbService = $breadcrumbService;
    $this->seoService = $seoService;
}

// With error handling in method
public function education(): View
{
    try {
        $navigation = $this->navigationService->getMainNavigation();
    } catch (\Exception $e) {
        Log::error('Navigation service error: ' . $e->getMessage());
        $navigation = []; // Fallback to empty navigation
    }
    
    // ... similar for other services
}
```


## Testing Strategy

### Applicability of Property-Based Testing

This education tourism page redesign is primarily a **UI rendering and layout project** with the following characteristics:
- Static content presentation and visual design
- User interface interactions (carousel, WhatsApp buttons)
- Responsive layout behavior
- No complex business logic or data transformations

**Assessment**: Property-based testing is **NOT appropriate** for this feature.

**Rationale**:
- All 22 requirements specify visual presentation, layout patterns, and UI behavior
- No universal properties to test across input spaces
- No parsers, serializers, algorithms, or data transformations
- Testing needs are best served by functional tests, browser tests, visual regression, and accessibility audits

### Recommended Testing Approach

#### 1. Laravel Feature Tests (PHPUnit)

**Purpose**: Test HTTP routes, controller responses, view rendering, and data structure

**Test File**: `tests/Feature/EducationPageTest.php`


```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class EducationPageTest extends TestCase
{
    /**
     * Test education page loads successfully
     * Validates: Requirements 1.5, 12.1, 13.1, 17.1
     */
    public function test_education_page_loads_successfully(): void
    {
        $response = $this->get('/wisata-edukasi');
        
        $response->assertStatus(200);
        $response->assertViewIs('pages.education');
    }
    
    /**
     * Test hero section content is present
     * Validates: Requirements 1.1, 1.2, 1.3
     */
    public function test_hero_section_displays_required_content(): void
    {
        $response = $this->get('/wisata-edukasi');
        
        $response->assertSee('Ecotainment Godongijo');
        $response->assertSee('Ecotainment adalah suatu kegiatan karyawisata');
    }

    
    /**
     * Test platform fieldtrip cards are provided
     * Validates: Requirements 2.1, 2.2, 2.3, 2.4
     */
    public function test_platform_fieldtrip_data_structure(): void
    {
        $response = $this->get('/wisata-edukasi');
        
        $response->assertViewHas('pageData');
        $pageData = $response->viewData('pageData');
        
        $this->assertArrayHasKey('platformFieldtrip', $pageData);
        $this->assertArrayHasKey('cards', $pageData['platformFieldtrip']);
        $this->assertCount(3, $pageData['platformFieldtrip']['cards']);
        
        // Verify card structure
        foreach ($pageData['platformFieldtrip']['cards'] as $card) {
            $this->assertArrayHasKey('icon', $card);
            $this->assertArrayHasKey('title', $card);
            $this->assertArrayHasKey('description', $card);
            $this->assertArrayHasKey('whatsappMessage', $card);
        }
    }

    
    /**
     * Test program category cards with images
     * Validates: Requirements 4.1, 4.2, 4.3, 4.4, 4.7
     */
    public function test_program_category_data_includes_images(): void
    {
        $response = $this->get('/wisata-edukasi');
        $pageData = $response->viewData('pageData');
        
        $this->assertArrayHasKey('programCategory', $pageData);
        $this->assertCount(3, $pageData['programCategory']['cards']);
        
        foreach ($pageData['programCategory']['cards'] as $card) {
            $this->assertArrayHasKey('image', $card);
            $this->assertArrayHasKey('imageFallback', $card);
            $this->assertArrayHasKey('imageAlt', $card);
            $this->assertArrayHasKey('title', $card);
            $this->assertArrayHasKey('description', $card);
        }
    }
    
    /**
     * Test environmental programs alternating layout data
     * Validates: Requirements 6.1, 6.2, 6.3, 6.4, 6.5
     */
    public function test_environmental_programs_data_structure(): void
    {
        $response = $this->get('/wisata-edukasi');
        $pageData = $response->viewData('pageData');
        
        $this->assertArrayHasKey('environmentalPrograms', $pageData);
        $this->assertCount(3, $pageData['environmentalPrograms']['programs']);
        
        $expectedTitles = ['Learning About Animals', 'Urban Farming', 'Renewable Energy'];
        $actualTitles = array_column($pageData['environmentalPrograms']['programs'], 'title');
        
        $this->assertEquals($expectedTitles, $actualTitles);
    }

    
    /**
     * Test art and science programs
     * Validates: Requirements 7.1-7.4, 9.1-9.3
     */
    public function test_art_and_science_programs_data(): void
    {
        $response = $this->get('/wisata-edukasi');
        $pageData = $response->viewData('pageData');
        
        $this->assertArrayHasKey('artPrograms', $pageData);
        $this->assertCount(3, $pageData['artPrograms']['cards']);
        
        $this->assertArrayHasKey('sciencePrograms', $pageData);
        $this->assertCount(2, $pageData['sciencePrograms']['cards']);
    }
    
    /**
     * Test testimonial section data
     * Validates: Requirements 11.1-11.10
     */
    public function test_testimonial_data_structure(): void
    {
        $response = $this->get('/wisata-edukasi');
        $pageData = $response->viewData('pageData');
        
        $this->assertArrayHasKey('testimonial', $pageData);
        $this->assertArrayHasKey('photo', $pageData['testimonial']);
        $this->assertArrayHasKey('quote', $pageData['testimonial']);
        $this->assertArrayHasKey('source', $pageData['testimonial']);
        
        $this->assertStringContainsString('kapanlagi.com', $pageData['testimonial']['source']);
    }

    
    /**
     * Test WhatsApp pre-filled messages are unique per card
     * Validates: Requirement 20.1-20.4
     */
    public function test_whatsapp_messages_are_contextual(): void
    {
        $response = $this->get('/wisata-edukasi');
        $pageData = $response->viewData('pageData');
        
        $allMessages = [];
        
        // Collect all WhatsApp messages
        foreach ($pageData['platformFieldtrip']['cards'] as $card) {
            $allMessages[] = $card['whatsappMessage'];
        }
        
        foreach ($pageData['programCategory']['cards'] as $card) {
            $allMessages[] = $card['whatsappMessage'];
        }
        
        // Verify all messages are unique
        $this->assertEquals(count($allMessages), count(array_unique($allMessages)));
        
        // Verify messages contain program context
        $this->assertStringContainsString('Virtual Fieldtrip', $allMessages[0]);
        $this->assertStringContainsString('Edukasi Lingkungan', $allMessages[3]);
    }
    
    /**
     * Test SEO metadata is present
     * Validates: Requirements 14.1-14.6
     */
    public function test_seo_metadata_is_generated(): void
    {
        $response = $this->get('/wisata-edukasi');
        
        $response->assertViewHas('seoData');
        $seoData = $response->viewData('seoData');
        
        $this->assertStringContainsString('Wisata Edukasi', $seoData['title']);
        $this->assertStringContainsString('Ecotainment', $seoData['description']);
        $this->assertArrayHasKey('og', $seoData);
        $this->assertArrayHasKey('structuredData', $seoData);
    }
    
    /**
     * Test breadcrumb navigation structure
     * Validates: Requirements 12.1-12.5
     */
    public function test_breadcrumb_navigation_is_correct(): void
    {
        $response = $this->get('/wisata-edukasi');
        
        $response->assertViewHas('breadcrumbs');
        $breadcrumbs = $response->viewData('breadcrumbs');
        
        $this->assertCount(2, $breadcrumbs);
        $this->assertEquals('Home', $breadcrumbs[0]['label']);
        $this->assertEquals('Wisata Edukasi', $breadcrumbs[1]['label']);
        $this->assertTrue($breadcrumbs[1]['current']);
    }
}
```

#### 2. Laravel Dusk Browser Tests

**Purpose**: Test actual rendering, user interactions, responsive behavior, and carousel functionality

**Test File**: `tests/Browser/EducationPageBrowserTest.php`

```php
<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class EducationPageBrowserTest extends DuskTestCase
{
    /**
     * Test hero section is visually prominent
     * Validates: Requirements 1.4, 1.5, 1.6
     */
    public function test_hero_section_displays_with_background_image(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/wisata-edukasi')
                    ->assertSee('Ecotainment Godongijo')
                    ->assertPresent('.hero-ecotainment')
                    ->assertPresent('.hero-background img')
                    ->assertPresent('.hero-overlay');
        });
    }
    
    /**
     * Test mobile carousel navigation
     * Validates: Requirements 19.1-19.12
     */
    public function test_mobile_carousel_navigation_works(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(375, 667) // iPhone size
                    ->visit('/wisata-edukasi')
                    ->assertPresent('.card-carousel')
                    ->assertPresent('.carousel-nav.carousel-next')
                    ->click('.carousel-nav.carousel-next')
                    ->pause(500) // Wait for animation
                    ->assertPresent('.carousel-nav.carousel-prev')
                    ->click('.carousel-nav.carousel-prev')
                    ->pause(500);
        });
    }
    
    /**
     * Test WhatsApp button opens correctly
     * Validates: Requirements 3.1-3.4, 20.1-20.4, 21.1-21.4
     */
    public function test_whatsapp_button_has_correct_link(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/wisata-edukasi')
                    ->assertPresent('.whatsapp-btn')
                    ->assertAttribute('.whatsapp-btn', 'href', function ($href) {
                        return str_contains($href, 'wa.me') || str_contains($href, 'whatsapp.com');
                    })
                    ->assertAttribute('.whatsapp-btn', 'target', '_blank');
        });
    }
    
    /**
     * Test desktop grid layout displays cards horizontally
     * Validates: Requirements 2.8, 4.9, 7.6, 9.5
     */
    public function test_desktop_displays_card_grid(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1440, 900) // Desktop size
                    ->visit('/wisata-edukasi')
                    ->assertPresent('.card-grid')
                    ->assertMissing('.card-carousel'); // Carousel hidden on desktop
        });
    }
    
    /**
     * Test responsive breakpoints
     * Validates: Requirements 13.1-13.5
     */
    public function test_responsive_layout_changes(): void
    {
        $this->browse(function (Browser $browser) {
            // Test mobile (< 768px)
            $browser->resize(375, 667)
                    ->visit('/wisata-edukasi')
                    ->assertPresent('.card-carousel')
                    ->assertMissing('.card-grid');
            
            // Test tablet (768px - 1024px)
            $browser->resize(768, 1024)
                    ->visit('/wisata-edukasi')
                    ->assertPresent('.card-grid');
            
            // Test desktop (> 1024px)
            $browser->resize(1440, 900)
                    ->visit('/wisata-edukasi')
                    ->assertPresent('.card-grid');
        });
    }
    
    /**
     * Test alternating layout for environmental programs
     * Validates: Requirements 6.5, 6.6
     */
    public function test_environmental_programs_alternating_layout(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1440, 900) // Desktop
                    ->visit('/wisata-edukasi')
                    ->assertPresent('.program-detail-item.image-left')
                    ->assertPresent('.program-detail-item.image-right');
        });
    }
    
    /**
     * Test testimonial section layout
     * Validates: Requirements 11.1-11.10
     */
    public function test_testimonial_section_horizontal_layout(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1440, 900)
                    ->visit('/wisata-edukasi')
                    ->assertPresent('.testimonial-section')
                    ->assertPresent('.testimonial-photo')
                    ->assertPresent('.testimonial-quote-box')
                    ->assertSee('kapanlagi.com');
        });
    }
    
    /**
     * Test image fallback on error
     * Validates: Requirement 22.1-22.3
     */
    public function test_image_fallback_on_error(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/wisata-edukasi')
                    ->script([
                        // Simulate image load error
                        "document.querySelector('.program-card img').dispatchEvent(new Event('error'))"
                    ])
                    ->pause(100)
                    ->assertAttribute('.program-card img', 'src', function ($src) {
                        return str_contains($src, 'placeholder-education.jpg');
                    });
        });
    }
}
```

#### 3. Component Unit Tests

**Purpose**: Test individual Blade components in isolation

**Test File**: `tests/Unit/Components/EducationComponentsTest.php`

```php
<?php

namespace Tests\Unit\Components;

use Tests\TestCase;
use Illuminate\Support\Facades\Blade;

class EducationComponentsTest extends TestCase
{
    /**
     * Test program card component renders correctly
     * Validates: Requirements 18.1-18.8
     */
    public function test_program_card_component_structure(): void
    {
        $cardData = [
            'icon' => '🌱',
            'title' => 'Test Program',
            'description' => 'Test description',
            'whatsappMessage' => 'Test message',
        ];
        
        $html = Blade::render(
            '<x-education.program-card :card="$card" />',
            ['card' => $cardData]
        );
        
        $this->assertStringContainsString('Test Program', $html);
        $this->assertStringContainsString('Test description', $html);
        $this->assertStringContainsString('🌱', $html);
        $this->assertStringContainsString('whatsapp-btn', $html);
    }
    
    /**
     * Test WhatsApp button component generates correct URL
     * Validates: Requirements 20.1-20.4, 21.1-21.4
     */
    public function test_whatsapp_button_url_encoding(): void
    {
        $message = 'Halo, saya tertarik dengan program Urban Farming';
        $phoneNumber = '6281234567890';
        
        $html = Blade::render(
            '<x-education.whatsapp-button :message="$message" :phoneNumber="$phoneNumber" />',
            ['message' => $message, 'phoneNumber' => $phoneNumber]
        );
        
        $this->assertStringContainsString('wa.me/6281234567890', $html);
        $this->assertStringContainsString(urlencode($message), $html);
        $this->assertStringContainsString('target="_blank"', $html);
    }
    
    /**
     * Test hero component with background image
     * Validates: Requirements 1.1-1.7
     */
    public function test_hero_component_structure(): void
    {
        $heroData = [
            'backgroundImage' => 'images/hero.webp',
            'backgroundImageFallback' => 'images/hero.jpg',
            'title' => 'Ecotainment Godongijo',
            'description' => 'Educational tourism description',
        ];
        
        $html = Blade::render(
            '<x-education.hero-ecotainment :backgroundImage="$data[\'backgroundImage\']" :backgroundImageFallback="$data[\'backgroundImageFallback\']" :title="$data[\'title\']" :description="$data[\'description\']" />',
            ['data' => $heroData]
        );
        
        $this->assertStringContainsString('<picture', $html);
        $this->assertStringContainsString('hero.webp', $html);
        $this->assertStringContainsString('hero.jpg', $html);
        $this->assertStringContainsString('Ecotainment Godongijo', $html);
    }
}
```



#### 4. Accessibility Testing

**Purpose**: Verify WCAG compliance and assistive technology support

**Tools**: 
- Lighthouse CI (automated)
- axe DevTools (manual)
- NVDA/JAWS screen readers (manual)

**Test Checklist**:

| Requirement | Test | Validation |
|-------------|------|------------|
| 14.3 | Heading hierarchy (H1 → H2 → H3) | `<h1>Ecotainment</h1>`, `<h2>Platform Fieldtrip</h2>`, `<h3>Virtual Fieldtrip</h3>` |
| 14.4 | Image alt text | All images have descriptive alt attributes |
| 19.7, 19.8, 19.9 | Carousel navigation accessible | Buttons have aria-label, keyboard navigable, focus visible |
| 12.5 | Breadcrumb current page | aria-current="page" on "Wisata Edukasi" |
| 11.4 | Quote semantic markup | `<blockquote>` and `<cite>` elements used |
| General | Color contrast | Text meets WCAG AA (4.5:1) or AAA (7:1) ratios |
| General | Keyboard navigation | All interactive elements reachable via Tab |
| General | Focus indicators | Visible focus styles on all interactive elements |

**Lighthouse Audit Targets**:
- **Performance**: ≥ 90
- **Accessibility**: = 100
- **Best Practices**: ≥ 90
- **SEO**: = 100

#### 5. Visual Regression Testing

**Purpose**: Detect unintended visual changes across updates

**Tool**: Percy.io or BackstopJS

**Test Scenarios**:
- Desktop homepage (1920x1080)
- Tablet landscape (1024x768)
- Mobile portrait (375x667)
- Mobile landscape (667x375)
- Hero section variations
- Card layouts (grid vs carousel)
- Hover states on cards and buttons
- WhatsApp button styles

**Baseline Screenshots**:
```bash
# Generate initial baseline
npm run percy:snapshot

# Compare against baseline on new builds
npm run percy:test
```

#### 6. Performance Testing

**Purpose**: Ensure fast loading and smooth interactions

**Metrics to Monitor**:

| Metric | Target | Validation Method |
|--------|--------|-------------------|
| First Contentful Paint (FCP) | < 1.5s | Lighthouse |
| Largest Contentful Paint (LCP) | < 2.5s | Lighthouse, WebPageTest |
| Time to Interactive (TTI) | < 3.5s | Lighthouse |
| Cumulative Layout Shift (CLS) | < 0.1 | Lighthouse, Chrome DevTools |
| Total Blocking Time (TBT) | < 300ms | Lighthouse |
| Page Weight | < 2MB | Network tab |
| Image Optimization | WebP < 100KB each | Image analysis |
| Carousel Animation | 60fps | Chrome DevTools Performance |

**Performance Test Script** (`tests/Performance/EducationPagePerformanceTest.php`):

```php
<?php

namespace Tests\Performance;

use Tests\TestCase;

class EducationPagePerformanceTest extends TestCase
{
    /**
     * Test page loads within performance budget
     * Validates: Requirement 17.1
     */
    public function test_page_loads_within_3_seconds(): void
    {
        $startTime = microtime(true);
        
        $response = $this->get('/wisata-edukasi');
        
        $loadTime = microtime(true) - $startTime;
        
        $response->assertStatus(200);
        $this->assertLessThan(3.0, $loadTime, 'Page should load within 3 seconds');
    }
    
    /**
     * Test asset optimization
     * Validates: Requirements 17.2, 17.3, 22.1-22.3
     */
    public function test_images_are_optimized(): void
    {
        $images = [
            'images/education/hero-background.webp',
            'images/education/category-environmental.webp',
            'images/education/learning-animals.webp',
        ];
        
        foreach ($images as $image) {
            $path = public_path($image);
            
            if (file_exists($path)) {
                $sizeKB = filesize($path) / 1024;
                $this->assertLessThan(200, $sizeKB, "{$image} should be < 200KB");
            }
        }
    }
}
```

#### 7. SEO Testing

**Purpose**: Verify search engine optimization effectiveness

**Test Checklist**:

| Requirement | Element | Expected Value |
|-------------|---------|----------------|
| 14.1 | `<title>` | "Wisata Edukasi - Ecotainment Godongijo \| The Waterfall" |
| 14.2 | `<meta name="description">` | 155-160 characters, includes "Ecotainment", "wisata edukasi", "program" |
| 14.3 | Heading structure | Single H1, logical H2/H3 hierarchy |
| 14.4 | Image alt text | Descriptive, unique per image |
| 14.5 | Open Graph tags | og:title, og:description, og:image, og:url present |
| 14.6 | Structured data | JSON-LD with @type: EducationalOrganization |
| General | Canonical URL | `<link rel="canonical" href="https://...">` |
| General | Mobile-friendly | viewport meta tag, responsive design |
| General | HTTPS | All assets loaded over HTTPS |
| General | robots.txt | Allows indexing of /wisata-edukasi |

**SEO Test** (`tests/Feature/EducationSEOTest.php`):

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class EducationSEOTest extends TestCase
{
    /**
     * Test SEO meta tags are present and correct
     * Validates: Requirements 14.1-14.6
     */
    public function test_seo_meta_tags_are_correct(): void
    {
        $response = $this->get('/wisata-edukasi');
        
        $response->assertSee('<title>Wisata Edukasi - Ecotainment Godongijo', false);
        $response->assertSee('<meta name="description"', false);
        $response->assertSee('<meta property="og:title"', false);
        $response->assertSee('<meta property="og:description"', false);
        $response->assertSee('<meta property="og:image"', false);
        $response->assertSee('application/ld+json', false);
    }
    
    /**
     * Test structured data is valid JSON-LD
     * Validates: Requirement 14.6
     */
    public function test_structured_data_is_valid_jsonld(): void
    {
        $response = $this->get('/wisata-edukasi');
        $content = $response->getContent();
        
        preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $content, $matches);
        
        $this->assertNotEmpty($matches, 'JSON-LD structured data should be present');
        
        $jsonData = json_decode($matches[1], true);
        
        $this->assertNotNull($jsonData, 'JSON-LD should be valid JSON');
        $this->assertEquals('https://schema.org', $jsonData['@context']);
        $this->assertEquals('EducationalOrganization', $jsonData['@type']);
    }
}
```

### Test Execution Strategy

#### Local Development

```bash
# Run PHPUnit tests
php artisan test --filter=Education

# Run Dusk browser tests
php artisan dusk --filter=Education

# Run all tests with coverage
php artisan test --coverage
```

#### CI/CD Pipeline

```yaml
# .github/workflows/test.yml
name: Education Page Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
          
      - name: Install Dependencies
        run: composer install
        
      - name: Run PHPUnit Tests
        run: php artisan test --filter=Education
        
      - name: Run Dusk Browser Tests
        run: php artisan dusk --filter=Education
        
      - name: Run Lighthouse CI
        run: npm run lighthouse
        
      - name: Percy Visual Regression
        run: npx percy snapshot
```

### Manual Testing Checklist

Before deploying, manually verify:

- [ ] **Desktop (1920x1080)**
  - [ ] Hero section displays with background image
  - [ ] Platform Fieldtrip cards in horizontal grid (3 columns)
  - [ ] Program Category cards in horizontal grid (3 columns)
  - [ ] Environmental programs in alternating left-right layout
  - [ ] Art programs in horizontal grid (3 columns)
  - [ ] Science programs in horizontal grid (2 columns)
  - [ ] Testimonial section with photo left, quote right
  - [ ] All WhatsApp buttons functional
  - [ ] No horizontal scrolling

- [ ] **Tablet (768x1024)**
  - [ ] Hero section responsive
  - [ ] Cards adapt to 2-column grid where appropriate
  - [ ] Touch targets minimum 44x44px
  - [ ] Images load correctly

- [ ] **Mobile (375x667)**
  - [ ] Hero section stacks vertically
  - [ ] Platform Fieldtrip carousel works (swipe, prev/next buttons)
  - [ ] Program Category carousel works
  - [ ] Environmental programs stack vertically (image above text)
  - [ ] Art/Science carousels work
  - [ ] Testimonial stacks vertically (photo above quote)
  - [ ] Carousel pagination dots functional
  - [ ] No horizontal scrolling
  - [ ] Text readable without zooming

- [ ] **Cross-Browser**
  - [ ] Chrome/Edge (latest)
  - [ ] Firefox (latest)
  - [ ] Safari (latest)
  - [ ] Mobile Safari (iOS)
  - [ ] Chrome Mobile (Android)

- [ ] **Accessibility**
  - [ ] Screen reader announces content correctly
  - [ ] Keyboard navigation works (Tab, Enter, Arrow keys)
  - [ ] Focus indicators visible
  - [ ] Color contrast sufficient
  - [ ] Images have alt text

- [ ] **Performance**
  - [ ] Page loads < 3 seconds on 3G
  - [ ] Images lazy load below fold
  - [ ] No layout shift (CLS < 0.1)
  - [ ] Carousel animations smooth (60fps)

- [ ] **WhatsApp Integration**
  - [ ] Buttons open WhatsApp on mobile
  - [ ] Buttons open WhatsApp Web on desktop
  - [ ] Messages pre-filled correctly
  - [ ] Each card has unique message

- [ ] **Error Handling**
  - [ ] Broken images show fallback placeholder
  - [ ] Page renders if JavaScript disabled
  - [ ] Carousel degrades to vertical list without JS

---

## Summary

This design document provides a comprehensive technical specification for the Education Tourism Page Redesign. The implementation follows Laravel best practices with:

- **Reusable Blade components** for maintainability
- **Responsive design** with mobile-first carousel patterns
- **Accessibility-first approach** with semantic HTML and ARIA labels
- **Performance optimization** through image lazy loading and asset optimization
- **Error handling** with graceful degradation and fallback states
- **Comprehensive testing strategy** covering functional, browser, accessibility, performance, and SEO aspects

The design prioritizes user experience across all devices while maintaining The Waterfall brand identity and enabling direct customer engagement through contextual WhatsApp integration.