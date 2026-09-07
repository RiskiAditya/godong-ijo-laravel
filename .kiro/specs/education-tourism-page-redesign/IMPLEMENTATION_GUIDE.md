# Education Tourism Page Redesign - Implementation Guide

## Executive Summary

**Project Status:** 23/96 tasks completed (24%)  
**Current State:** Foundation complete, frontend components ready to build  
**Estimated Remaining Work:** 3-4 days for a single developer

This guide provides complete implementation instructions for the remaining 73 tasks of the Education Tourism Page Redesign project.

---

## ✅ COMPLETED WORK (Tasks 1-3, 13.3, 19.1-19.2, 21.1-21.5)

### 1. Foundation & Configuration

#### 1.1 CSS Color Tokens ✅
**File:** `resources/css/app.css`

New color palette variables added:
```css
:root {
  --forest: #1E4636;
  --forest-d: #0F2A1F;
  --gold: #C98A3E;
  --cream: #FAF6ED;
  --line: #EAE4D6;
  --ink: #22301E;
}

/* Card styling updated */
.card {
  border: 1px solid var(--line);
  border-radius: 12px;
  box-shadow: none; /* Removed */
}
```

#### 2. Database & Models ✅

**Migration:** `database/migrations/2026_08_08_115100_create_school_partners_table.php`
```php
Schema::create('school_partners', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->enum('level', ['TK', 'SD', 'SMP', 'SMA']);
    $table->string('logo_path')->nullable();
    $table->boolean('is_active')->default(true);
    $table->integer('order')->default(0);
    $table->timestamps();
    
    $table->index(['is_active', 'order']);
});
```

**Model:** `app/Models/SchoolPartner.php`
```php
class SchoolPartner extends Model
{
    protected $fillable = ['name', 'level', 'logo_path', 'is_active', 'order'];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    public function scopeActive($query) {
        return $query->where('is_active', true);
    }
    
    public function scopeOrdered($query) {
        return $query->orderBy('order');
    }
    
    public function getMonogramAttribute() {
        $words = explode(' ', $this->name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($this->name, 0, 2));
    }
}
```

**Seeder:** `database/seeders/SchoolPartnerSeeder.php` - 21 sample schools created

#### 3. Controller Data Structure ✅

**File:** `app/Http/Controllers/StaticPageController.php`

The `getEducationData()` method now includes:
- Enhanced hero with location badge, dual buttons, statistics strip
- Platform Fieldtrip with Tabler icons (ti-device-laptop, ti-bus, ti-trees)
- Art programs split into `featured` (3) and `others` (11) arrays
- School partners query (first 7 active)
- Testimonial with dark card design metadata
- Footer CTA bar data

The `schoolPartners()` method created for full list page with pagination.

#### 4. Routes ✅

**File:** `routes/web.php`
```php
Route::get('/wisata-edukasi/sekolah-mitra', [StaticPageController::class, 'schoolPartners'])
    ->name('education.school-partners');
```

#### 5. Configuration ✅

**File:** `config/app.php`
```php
'whatsapp_number' => env('WHATSAPP_NUMBER', '6281234567890'),
```

#### 6. Placeholder Images ✅

Created in `public/images/education/`:
- `hero-background.{webp,jpg}`
- `category-{environmental,science,art}.{webp,jpg}`
- `learning-animals.{webp,jpg}`, `urban-farming.{webp,jpg}`, `renewable-energy.{webp,jpg}`
- `testimonial-photo.{webp,jpg}`
- `placeholder-education.jpg`, `placeholder-blur.{webp,jpg}`

#### 7. Completed Components ✅

**File:** `resources/views/components/education/icon-badge.blade.php`
```blade
@props([
    'iconName' => '',
    'backgroundColor' => '#FAF6ED',
    'borderRadius' => '8px',
    'size' => '48px',
    'iconSize' => '24px',
    'iconColor' => '#1E4636'
])

<div 
    class="icon-badge" 
    style="
        background-color: {{ $backgroundColor }};
        border-radius: {{ $borderRadius }};
        width: {{ $size }};
        height: {{ $size }};
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    "
    role="img"
    aria-label="{{ $iconName }}"
>
    <i 
        class="ti {{ $iconName }}" 
        style="
            font-size: {{ $iconSize }};
            color: {{ $iconColor }};
            line-height: 1;
        "
        aria-hidden="true"
    ></i>
</div>
```

**File:** `resources/views/components/education/card-grid.blade.php`
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

<style>
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
    display: none;
  }
}
</style>
```

---

## 🔧 REMAINING WORK - IMPLEMENTATION GUIDE

### Phase 1: Core Blade Components (Tasks 4-13)

#### Task 4.1: Hero Ecotainment Component

**File:** `resources/views/components/education/hero-ecotainment.blade.php`

```blade
@props([
    'hero' => []
])

<section class="hero-ecotainment" role="banner">
    <div class="hero-split-layout">
        {{-- Left Column: Text Content --}}
        <div class="hero-text-column">
            {{-- Location Badge --}}
            @if(isset($hero['locationBadge']))
            <div class="hero-location-badge">
                <i class="ti ti-{{ $hero['locationBadge']['icon'] }}"></i>
                <span>{{ $hero['locationBadge']['text'] }}</span>
            </div>
            @endif
            
            {{-- Title --}}
            <h1 class="hero-title">{{ $hero['title'] }}</h1>
            
            {{-- Description --}}
            <p class="hero-description">{{ $hero['description'] }}</p>
            
            {{-- Buttons --}}
            @if(isset($hero['buttons']))
            <div class="hero-buttons">
                @foreach($hero['buttons'] as $button)
                    <a 
                        href="{{ $button['url'] }}" 
                        class="hero-btn hero-btn-{{ $button['variant'] }}"
                        @if($button['external'] ?? false) target="_blank" rel="noopener noreferrer" @endif
                    >
                        @if($button['icon'])
                        <i class="ti ti-{{ $button['icon'] }}"></i>
                        @endif
                        <span>{{ $button['label'] }}</span>
                    </a>
                @endforeach
            </div>
            @endif
        </div>
        
        {{-- Right Column: Image --}}
        <div class="hero-image-column">
            <picture>
                <source srcset="{{ $hero['image'] }}" type="image/webp">
                <img 
                    src="{{ $hero['imageFallback'] }}" 
                    alt="{{ $hero['alt'] }}"
                    loading="eager"
                    class="hero-image"
                    @error="this.src='{{ asset('images/education/placeholder-education.jpg') }}'"
                >
            </picture>
        </div>
    </div>
    
    {{-- Statistics Strip --}}
    @if(isset($hero['statistics']))
    <div class="hero-statistics-strip">
        <div class="container">
            <div class="statistics-grid">
                @foreach($hero['statistics'] as $stat)
                <div class="stat-item">
                    <div class="stat-value">{{ $stat['value'] }}</div>
                    <div class="stat-label">{{ $stat['label'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</section>
```

#### Task 4.3: Hero CSS Styling

**Add to:** `resources/css/app.css`

```css
/* Hero Ecotainment Component */
.hero-ecotainment {
  width: 100%;
  min-height: 50vh;
  background-color: white;
}

.hero-split-layout {
  display: grid;
  grid-template-columns: 55% 45%;
  gap: 48px;
  min-height: 50vh;
}

.hero-text-column {
  background-color: var(--forest-d);
  padding: 60px 80px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.hero-location-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  background-color: rgba(255, 255, 255, 0.1);
  border-radius: 999px;
  color: white;
  font-size: 13px;
  margin-bottom: 24px;
  width: fit-content;
}

.hero-title {
  font-family: 'Fraunces', serif;
  font-size: clamp(32px, 4vw, 36px);
  font-weight: 700;
  color: white;
  line-height: 1.2;
  margin-bottom: 16px;
}

.hero-description {
  color: rgba(255, 255, 255, 0.75);
  font-size: 16px;
  line-height: 1.7;
  max-width: 500px;
  margin-bottom: 32px;
}

.hero-buttons {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
}

.hero-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
}

.hero-btn-outline {
  background-color: transparent;
  border: 2px solid rgba(255, 255, 255, 0.3);
  color: white;
}

.hero-btn-outline:hover {
  background-color: rgba(255, 255, 255, 0.1);
  border-color: white;
}

.hero-btn-solid-gold {
  background-color: var(--gold);
  color: var(--forest-d);
  border: none;
}

.hero-btn-solid-gold:hover {
  background-color: #B57A2F;
  transform: translateY(-2px);
}

.hero-image-column {
  position: relative;
  overflow: hidden;
  border-radius: 0 12px 12px 0;
}

.hero-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
}

.hero-statistics-strip {
  border-top: 1px solid var(--line);
  padding: 32px 0;
  background-color: white;
}

.statistics-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 48px;
}

.stat-item {
  text-align: center;
}

.stat-value {
  font-size: 32px;
  font-weight: 700;
  color: var(--forest);
  margin-bottom: 8px;
}

.stat-label {
  font-size: 14px;
  color: var(--ink);
  opacity: 0.7;
}

/* Responsive */
@media (max-width: 767px) {
  .hero-split-layout {
    grid-template-columns: 1fr;
    gap: 0;
    min-height: auto;
  }
  
  .hero-text-column {
    padding: 40px 24px;
  }
  
  .hero-image-column {
    max-height: 240px;
    border-radius: 0;
  }
  
  .statistics-grid {
    grid-template-columns: 1fr;
    gap: 24px;
  }
}

@media (min-width: 768px) and (max-width: 1024px) {
  .hero-split-layout {
    grid-template-columns: 1fr;
  }
  
  .hero-text-column {
    padding: 50px 40px;
  }
  
  .hero-image-column {
    max-height: 300px;
  }
}
```

#### Task 8.1: Program Card Component

**File:** `resources/views/components/education/program-card.blade.php`

```blade
@props([
    'card' => [],
    'sectionId' => 'default'
])

<article class="program-card" data-section="{{ $sectionId }}">
    {{-- Card Media --}}
    @if(isset($card['image']))
        <figure class="card-media card-image-wrapper">
            @if(isset($card['icon']))
                <div class="card-icon-badge-overlay">
                    <x-education.icon-badge 
                        :iconName="$card['icon']" 
                        backgroundColor="#FAF6ED" 
                        borderRadius="8px"
                    />
                </div>
            @endif
            <picture>
                <source srcset="{{ $card['image'] }}" type="image/webp">
                <img 
                    src="{{ $card['imageFallback'] ?? $card['image'] }}" 
                    alt="{{ $card['imageAlt'] ?? $card['title'] }}" 
                    loading="lazy"
                    class="card-image"
                    @error="this.src='{{ asset('images/education/placeholder-education.jpg') }}'"
                >
            </picture>
        </figure>
    @elseif(isset($card['icon']))
        <div class="card-media card-icon-wrapper">
            <span class="card-icon-emoji">{{ $card['icon'] }}</span>
        </div>
    @endif
    
    {{-- Card Content --}}
    <div class="card-content">
        <h3 class="card-title">{{ $card['title'] }}</h3>
        @if(isset($card['description']))
        <p class="card-description">{{ $card['description'] }}</p>
        @endif
    </div>
    
    {{-- Card Action --}}
    @if(isset($card['whatsappMessage']))
    <div class="card-action">
        <x-education.whatsapp-button 
            :message="$card['whatsappMessage']"
            label="Hubungi via WhatsApp"
            variant="primary"
        />
    </div>
    @endif
</article>
```

#### Task 8.2: Program Card CSS

**Add to:** `resources/css/app.css`

```css
/* Program Card Component */
.program-card {
  background-color: white;
  border: 1px solid var(--line);
  border-radius: 12px;
  overflow: hidden;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
  height: 100%;
}

.program-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(30, 70, 54, 0.12);
}

.card-media {
  width: 100%;
  position: relative;
}

.card-image-wrapper {
  aspect-ratio: 16 / 9;
  overflow: hidden;
  position: relative;
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

.card-icon-badge-overlay {
  position: absolute;
  top: 12px;
  left: 12px;
  z-index: 2;
}

.card-icon-wrapper {
  padding: 40px 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, var(--cream) 0%, var(--line) 100%);
}

.card-icon-emoji {
  font-size: 4rem;
  line-height: 1;
}

.card-content {
  flex: 1;
  padding: 24px;
}

.card-title {
  font-family: 'Fraunces', serif;
  font-size: 1.25rem;
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

#### Task 13.1: WhatsApp Button Component

**File:** `resources/views/components/education/whatsapp-button.blade.php`

```blade
@props([
    'message' => 'Halo, saya tertarik dengan informasi program di Godongijo',
    'phoneNumber' => config('app.whatsapp_number', '6281234567890'),
    'label' => 'Hubungi via WhatsApp',
    'variant' => 'primary'
])

@php
    $encodedMessage = urlencode($message);
    $isMobile = preg_match('/Mobile|Android|iPhone/i', $_SERVER['HTTP_USER_AGENT'] ?? '');
    $whatsappUrl = $isMobile 
        ? "whatsapp://send?phone={$phoneNumber}&text={$encodedMessage}"
        : "https://wa.me/{$phoneNumber}?text={$encodedMessage}";
@endphp

<a 
    href="{{ $whatsappUrl }}" 
    target="_blank"
    rel="noopener noreferrer"
    class="whatsapp-btn whatsapp-btn-{{ $variant }}"
>
    <svg class="whatsapp-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" fill="currentColor"/>
    </svg>
    <span>{{ $label }}</span>
</a>
```

#### Task 13.2: WhatsApp Button CSS

**Add to:** `resources/css/app.css`

```css
/* WhatsApp Button Component */
.whatsapp-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 24px;
  border-radius: 50px;
  font-size: 0.9375rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.25s ease;
  width: 100%;
}

.whatsapp-btn-primary {
  background-color: var(--gold);
  color: white;
}

.whatsapp-btn-primary:hover {
  background-color: #B57A2F;
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(201, 138, 62, 0.3);
}

.whatsapp-btn-secondary {
  background-color: transparent;
  border: 2px solid var(--gold);
  color: var(--gold);
}

.whatsapp-btn-secondary:hover {
  background-color: var(--gold);
  color: white;
}

.whatsapp-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}
```

**Continue to next message for more components...**



### More Components Continued...

#### Task 7.2: Card Carousel Component

**File:** `resources/views/components/education/card-carousel.blade.php`

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
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
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
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
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
                :aria-label="`Go to slide {{ $index + 1 }}`"
                :aria-current="currentIndex === {{ $index }} ? 'true' : 'false'"
            ></button>
        @endforeach
    </div>
</div>
```



#### Task 7.4: Carousel CSS Styling

**Add to:** `resources/css/app.css`

```css
/* Card Carousel Component */
.card-carousel {
  display: none;
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
  background-color: var(--cream);
  color: var(--forest);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 10;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
}

.carousel-nav:hover:not(:disabled) {
  background-color: var(--forest);
  color: white;
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
  background-color: var(--line);
  opacity: 0.5;
  transition: all 0.2s ease;
  cursor: pointer;
  border: none;
  padding: 0;
}

.pagination-dot.active {
  opacity: 1;
  width: 24px;
  border-radius: 4px;
  background-color: var(--forest);
}

@media (max-width: 767px) {
  .card-carousel {
    display: block;
  }
}
```

