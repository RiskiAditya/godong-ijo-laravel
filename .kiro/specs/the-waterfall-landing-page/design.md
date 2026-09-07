# Design Document: The Waterfall Landing Page

## Overview

This design document outlines the technical approach for creating an eco-luxury tourism landing page for The Waterfall at godongijo.com/the-waterfall/. The landing page is a single-page web application designed to showcase sustainable luxury tourism experiences inspired by Costa Rica's "Pura Vida" philosophy.

### Goals

- Create an immersive first impression that communicates eco-luxury tourism values
- Showcase visual content through an engaging image collage
- Provide clear calls-to-action for visitor engagement
- Ensure accessibility and performance across all devices
- Optimize for search engine visibility and social media sharing

### Technology Stack

This landing page will be built using Laravel framework for maintainability, scalability, and integration capabilities:

- **Laravel 10.x**: PHP framework providing MVC structure, routing, and templating
- **Blade Templating Engine**: Laravel's templating engine for reusable components and layouts
- **Laravel Mix / Vite**: Modern asset compilation for CSS and JavaScript bundling
- **Tailwind CSS** (or custom CSS3): Modern styling with CSS Grid, Flexbox, gradients, and animations
- **Alpine.js** (optional) or Vanilla JavaScript: Lightweight interactions for navigation, lazy loading, and responsive behavior
- **WebP images with fallbacks**: Optimized visual assets served from Laravel's public directory
- **PHP 8.1+**: Server-side language runtime

This stack ensures maintainability through Laravel's MVC architecture, easy integration with future backend features (contact forms, booking systems), and professional development practices.

## Architecture

### System Architecture

The landing page follows Laravel's MVC architecture:

```
┌─────────────────────────────────────────┐
│         Browser (Client)                │
│  ┌───────────────────────────────────┐  │
│  │   Rendered HTML (from Blade)      │  │
│  │   - Semantic markup               │  │
│  │   - SEO metadata                  │  │
│  │   - Accessibility attributes      │  │
│  └───────────────────────────────────┘  │
│  ┌───────────────────────────────────┐  │
│  │   Compiled CSS (Mix/Vite)         │  │
│  │   - Responsive layouts            │  │
│  │   - Eco color palette             │  │
│  │   - Animations & transitions      │  │
│  └───────────────────────────────────┘  │
│  ┌───────────────────────────────────┐  │
│  │   Compiled JavaScript             │  │
│  │   - Lazy loading                  │  │
│  │   - Mobile navigation             │  │
│  │   - Interaction handlers          │  │
│  └───────────────────────────────────┘  │
└─────────────────────────────────────────┘
         │ HTTP Request
         ▼
┌─────────────────────────────────────────┐
│      Laravel Application (Server)       │
│  ┌───────────────────────────────────┐  │
│  │   Routes (web.php)                │  │
│  │   - GET /the-waterfall            │  │
│  └───────────────────────────────────┘  │
│  ┌───────────────────────────────────┐  │
│  │   Controller                      │  │
│  │   - LandingPageController         │  │
│  │   - Prepare view data             │  │
│  └───────────────────────────────────┘  │
│  ┌───────────────────────────────────┐  │
│  │   View (Blade Templates)          │  │
│  │   - layouts/app.blade.php         │  │
│  │   - landing/index.blade.php       │  │
│  │   - components/hero.blade.php     │  │
│  │   - components/navigation.blade   │  │
│  │   - components/image-collage      │  │
│  └───────────────────────────────────┘  │
└─────────────────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────────┐
│   Static Assets (public/)               │
│   - /css/app.css (compiled)             │
│   - /js/app.js (compiled)               │
│   - /images/ (WebP/JPG)                 │
│   - /build/ (Vite manifest)             │
└─────────────────────────────────────────┘
```

### Page Structure

The landing page is organized using Laravel's Blade component system:

1. **Main Layout** (`resources/views/layouts/app.blade.php`)
   - HTML structure, meta tags, asset includes
2. **Navigation Component** (`resources/views/components/navigation.blade.php`)
   - Fixed/sticky navigation bar
3. **Landing Page View** (`resources/views/landing/index.blade.php`)
   - Hero Section (Blade component or section)
   - Image Collage Section (Blade component)
   - Footer (optional)
4. **Controller** (`app/Http/Controllers/LandingPageController.php`)
   - Handles route logic and passes data to views
5. **Routes** (`routes/web.php`)
   - Define URL endpoints

### Laravel File Structure

```
app/
├── Http/
│   └── Controllers/
│       └── LandingPageController.php
│
resources/
├── views/
│   ├── layouts/
│   │   └── app.blade.php
│   ├── landing/
│   │   └── index.blade.php
│   └── components/
│       ├── navigation.blade.php
│       ├── hero-section.blade.php
│       ├── image-collage.blade.php
│       └── cta-button.blade.php
├── css/
│   └── app.css
└── js/
    └── app.js
│
public/
├── css/
│   └── app.css (compiled)
├── js/
│   └── app.js (compiled)
├── images/
│   ├── hero/
│   ├── collage/
│   │   ├── forest.webp
│   │   ├── waterfall.webp
│   │   └── ...
│   └── placeholders/
└── build/ (Vite manifest)
│
routes/
└── web.php
```

### Responsive Breakpoints

- **Mobile**: < 768px (single column layout)
- **Tablet**: 768px - 1024px (2-column grid)
- **Desktop**: > 1024px (multi-column grid with optimal spacing)

## Components and Interfaces

### Laravel MVC Structure

#### Routes (`routes/web.php`)

```php
<?php

use App\Http\Controllers\LandingPageController;
use Illuminate\Support\Facades\Route;

Route::get('/the-waterfall', [LandingPageController::class, 'index'])
    ->name('landing.waterfall');
```

#### Controller (`app/Http/Controllers/LandingPageController.php`)

```php
<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function index(): View
    {
        $pageData = [
            'title' => 'The Waterfall - Eco-Luxury Tourism',
            'description' => 'Experience Pura Vida eco-luxury tourism at The Waterfall. Where nature meets wonder in sustainable luxury.',
            'images' => $this->getCollageImages(),
            'navigation' => [
                ['label' => 'Home', 'href' => '#hero'],
                ['label' => 'Experience', 'href' => '#experience'],
                ['label' => 'Gallery', 'href' => '#gallery'],
                ['label' => 'Contact', 'href' => '#contact'],
            ],
        ];
        
        return view('landing.index', $pageData);
    }
    
    private function getCollageImages(): array
    {
        return [
            [
                'src' => asset('images/collage/forest.webp'),
                'fallback' => asset('images/collage/forest.jpg'),
                'alt' => 'Lush green forest canopy',
                'category' => 'forest',
            ],
            [
                'src' => asset('images/collage/waterfall.webp'),
                'fallback' => asset('images/collage/waterfall.jpg'),
                'alt' => 'Misty waterfall cascading through rocks',
                'category' => 'waterfall',
            ],
            // Additional images...
        ];
    }
}
```

### 1. Navigation Blade Component

**File**: `resources/views/components/navigation.blade.php`

**Purpose**: Provide site navigation with rounded UI elements

```blade
<nav class="navbar">
  <div class="nav-container">
    <div class="nav-brand">
      <a href="{{ route('landing.waterfall') }}">The Waterfall</a>
    </div>
    <ul class="nav-menu">
      @foreach($items as $item)
        <li>
          <a href="{{ $item['href'] }}" class="nav-link">
            {{ $item['label'] }}
          </a>
        </li>
      @endforeach
    </ul>
    <button class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false">
      ☰
    </button>
  </div>
</nav>
```

**Component Usage**:
```blade
<x-navigation :items="$navigation" />
```

**Styling Requirements**:
- Fixed/sticky positioning
- Rounded corners on menu items (border-radius: 8px+)
- Smooth hover transitions
- Semi-transparent background with backdrop blur
- Mobile: Hamburger menu with slide-in animation

**Behavior**:
- Desktop: Horizontal menu with hover effects
- Mobile: Toggle button reveals vertical menu
- Smooth scroll to anchor sections when clicked (using Alpine.js or vanilla JS)
- Active section highlighting

### 2. Hero Section Blade Component

**File**: `resources/views/components/hero-section.blade.php`

**Purpose**: Create immediate visual impact with brand message and CTAs

```blade
@props(['title' => 'Pura Vida - Eco-Luxury Tourism', 'tagline' => 'Where Nature Meets Wonder'])

<section class="hero-section">
  <div class="hero-content">
    <h1 class="hero-heading">🌿 {{ $title }}</h1>
    <p class="hero-tagline gradient-text">{{ $tagline }}</p>
    <div class="hero-cta">
      <x-cta-button variant="primary">Get Inspired</x-cta-button>
      <x-cta-button variant="secondary">Start Planning</x-cta-button>
    </div>
  </div>
  <div class="hero-background">
    <!-- Background imagery or video -->
  </div>
</section>
```

**Styling Requirements**:
- Full viewport height (100vh)
- Centered content with flexbox
- Gradient text effect on tagline using `background-clip: text`
- Rounded CTA buttons with hover animations (scale, shadow)
- Semi-transparent overlay for text readability

**Key CSS Classes**:
- `.gradient-text`: Background gradient clipped to text
- `.btn-primary`: Strong eco-green color, prominent
- `.btn-secondary`: Outlined style, earth tone

### 3. Image Collage Blade Component

**File**: `resources/views/components/image-collage.blade.php`

**Purpose**: Showcase diverse aspects of eco-luxury tourism through visual storytelling

```blade
@props(['images' => []])

<section class="image-collage">
  <div class="collage-grid">
    @foreach($images as $image)
      <figure class="collage-item" data-category="{{ $image['category'] }}">
        <picture>
          <source srcset="{{ $image['src'] }}" type="image/webp">
          <img 
            data-src="{{ $image['fallback'] }}" 
            src="{{ asset('images/placeholders/blur.jpg') }}"
            alt="{{ $image['alt'] }}" 
            loading="lazy"
            class="lazy-image"
          >
        </picture>
      </figure>
    @endforeach
  </div>
</section>
```

**Component Usage**:
```blade
<x-image-collage :images="$images" />
```

**Layout Strategy**:
- CSS Grid with dynamic column count based on viewport
- Desktop: 4-column asymmetric grid
- Tablet: 2-column grid
- Mobile: 1-column stacked layout
- Varied image sizes for visual interest (use grid-row-span and grid-column-span)

**Image Categories** (7 required):
1. Lush green forests
2. Misty waterfalls
3. Aerial views
4. Adventure activities
5. Wildlife
6. Relaxation experiences
7. Sustainable luxury

**Lazy Loading Implementation**:
- Use `loading="lazy"` attribute for native lazy loading
- JavaScript IntersectionObserver as fallback for older browsers
- Placeholder images or blur-up effect while loading

### 4. CTA Button Blade Component

**File**: `resources/views/components/cta-button.blade.php`

**Purpose**: Drive user action with prominent, accessible buttons

```blade
@props([
    'variant' => 'primary',
    'href' => '#',
    'type' => 'button'
])

@if($type === 'link')
  <a href="{{ $href }}" {{ $attributes->merge(['class' => "btn btn-{$variant}"]) }}>
    {{ $slot }}
  </a>
@else
  <button {{ $attributes->merge(['class' => "btn btn-{$variant}", 'type' => 'button']) }}>
    {{ $slot }}
  </button>
@endif
```

**Variants**:
- Primary: Solid background, high contrast
- Secondary: Outlined or ghost style

**Styling** (in `resources/css/app.css`):
```css
.btn {
  padding: 14px 32px;
  border-radius: 24px;
  font-weight: 600;
  transition: all 0.3s ease;
  cursor: pointer;
}

.btn-primary {
  background: var(--eco-green);
  color: white;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(0,0,0,0.2);
}
```

**Accessibility**:
- Keyboard focusable (proper tab order)
- Visible focus indicator
- Sufficient color contrast
- Descriptive button text (no "click here")

### 5. Main Layout

**File**: `resources/views/layouts/app.blade.php`

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? 'The Waterfall - Eco-Luxury Tourism' }}</title>
    <meta name="description" content="{{ $description ?? 'Experience Pura Vida eco-luxury tourism' }}">
    
    <!-- Open Graph / Social Media -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title ?? 'The Waterfall - Eco-Luxury Tourism' }}">
    <meta property="og:description" content="{{ $description ?? 'Experience Pura Vida eco-luxury tourism' }}">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <x-navigation :items="$navigation ?? []" />
    
    <main>
        @yield('content')
    </main>
    
    @stack('scripts')
</body>
</html>
```

### 6. Landing Page View

**File**: `resources/views/landing/index.blade.php`

```blade
@extends('layouts.app')

@section('content')
    <x-hero-section 
        title="Pura Vida - Eco-Luxury Tourism"
        tagline="Where Nature Meets Wonder"
    />
    
    <x-image-collage :images="$images" />
@endsection

@push('scripts')
    <script>
        // Lazy loading implementation
        // Mobile navigation toggle
        // Smooth scroll behavior
    </script>
@endpush
```

## Data Models

### Laravel Data Structures

#### Image Asset Array Structure

The controller passes image data to views as PHP arrays:

```php
// In LandingPageController.php
private function getCollageImages(): array
{
    return [
        [
            'src' => asset('images/collage/forest.webp'),
            'fallback' => asset('images/collage/forest.jpg'),
            'alt' => 'Lush green forest canopy',
            'category' => 'forest',
            'width' => 1200,
            'height' => 800,
            'gridSpan' => [
                'columns' => 1,
                'rows' => 1,
            ],
        ],
        [
            'src' => asset('images/collage/waterfall.webp'),
            'fallback' => asset('images/collage/waterfall.jpg'),
            'alt' => 'Misty waterfall cascading through rocks',
            'category' => 'waterfall',
            'width' => 1200,
            'height' => 1600,
            'gridSpan' => [
                'columns' => 1,
                'rows' => 2,
            ],
        ],
        // Additional images for all 7 categories...
    ];
}
```

**Array Keys**:
- `src` (string): WebP image source path (via asset() helper)
- `fallback` (string): JPG fallback for older browsers
- `alt` (string): Descriptive alt text for accessibility
- `category` (string): Image category ('forest', 'waterfall', 'aerial', 'adventure', 'wildlife', 'relaxation', 'luxury')
- `width` (int): Original width in pixels
- `height` (int): Original height in pixels
- `gridSpan` (array): Grid layout configuration
  - `columns` (int): Grid column span (1-2)
  - `rows` (int): Grid row span (1-2)

#### Navigation Item Array Structure

```php
// In LandingPageController.php
private function getNavigationItems(): array
{
    return [
        ['label' => 'Home', 'href' => '#hero'],
        ['label' => 'Experience', 'href' => '#experience'],
        ['label' => 'Gallery', 'href' => '#gallery'],
        ['label' => 'Contact', 'href' => '#contact'],
    ];
}
```

### CSS Configuration (in resources/css/app.css)

#### Color Palette Variables

```css
:root {
  /* Primary Colors */
  --eco-green: #2D7A3E;
  --eco-green-light: #5FB878;
  --eco-green-dark: #1A4D2E;
  
  /* Earth Tones */
  --earth-sand: #D4A574;
  --earth-clay: #B8856D;
  --earth-stone: #8B7E74;
  
  /* Neutrals */
  --white: #FFFFFF;
  --cream: #F5F1E8;
  --charcoal: #2C2C2C;
  
  /* Gradients */
  --gradient-primary: linear-gradient(135deg, #2D7A3E 0%, #5FB878 100%);
  --gradient-tagline: linear-gradient(90deg, #5FB878 0%, #D4A574 100%);
}
```

### SEO Metadata Configuration

The controller or a dedicated service can provide SEO metadata:

```php
// In LandingPageController.php or a dedicated SEOService
private function getSEOMetadata(): array
{
    return [
        'title' => 'The Waterfall - Eco-Luxury Tourism | Pura Vida Experience',
        'description' => 'Experience sustainable luxury at The Waterfall. Discover lush forests, misty waterfalls, and eco-conscious adventure in a breathtaking natural paradise.',
        'keywords' => ['eco-luxury tourism', 'sustainable travel', 'Costa Rica', 'Pura Vida', 'nature retreat', 'eco resort'],
        'og' => [
            'title' => 'The Waterfall - Where Nature Meets Wonder',
            'description' => 'Immerse yourself in eco-luxury tourism with stunning waterfalls, lush forests, and sustainable adventures.',
            'image' => asset('images/og-image.jpg'),
            'url' => route('landing.waterfall'),
            'type' => 'website',
        ],
        'structuredData' => [
            '@context' => 'https://schema.org',
            '@type' => 'TouristAttraction',
            'name' => 'The Waterfall',
            'description' => 'Eco-luxury tourism destination offering sustainable travel experiences',
            'image' => [
                asset('images/collage/waterfall.jpg'),
                asset('images/collage/forest.jpg'),
            ],
            'address' => [
                '@type' => 'PostalAddress',
                'addressCountry' => 'CR',
            ],
        ],
    ];
}
```

**Usage in Layout**:
```blade
<!-- In layouts/app.blade.php -->
<title>{{ $seoData['title'] ?? config('app.name') }}</title>
<meta name="description" content="{{ $seoData['description'] ?? '' }}">
<meta property="og:title" content="{{ $seoData['og']['title'] ?? '' }}">
<meta property="og:description" content="{{ $seoData['og']['description'] ?? '' }}">

@if(isset($seoData['structuredData']))
<script type="application/ld+json">
    {!! json_encode($seoData['structuredData']) !!}
</script>
@endif
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Assessment: Property-Based Testing Not Applicable

After analyzing all acceptance criteria in the requirements document, property-based testing is **not applicable** to this landing page feature.

**Why PBT Doesn't Apply:**

This is a **UI rendering and visual presentation project** with the following characteristics:

- **No computational logic**: The landing page presents static content without algorithms, parsers, serializers, or data transformations
- **Visual requirements only**: All acceptance criteria specify presentation ("SHALL display", "SHALL use colors", "SHALL arrange") rather than input-varying behavior
- **No universal properties**: There are no behaviors that can be expressed as "for all inputs X, property P(X) holds"
- **Static content structure**: The page does not process varying inputs that would benefit from randomized testing

**Requirements Analysis:**

- Requirements 1-10 all focus on visual presentation, layout, styling, and static content display
- No acceptance criteria involve data transformation, parsing, serialization, or algorithmic behavior
- All testable aspects are better verified through visual regression, responsive design, accessibility, and performance testing

**Testing Strategy**: See the Testing Strategy section below for comprehensive coverage using visual regression testing, example-based functional tests, accessibility audits, and performance monitoring.

## Error Handling

### Image Loading Errors

**Strategy**: Graceful degradation with fallbacks in Blade templates

1. **WebP Fallback in Blade**:
   ```blade
   <picture>
     <source srcset="{{ $image['src'] }}" type="image/webp">
     <img src="{{ $image['fallback'] }}" alt="{{ $image['alt'] }}">
   </picture>
   ```

2. **Error Event Handling (JavaScript)**:
   ```javascript
   // In resources/js/app.js
   document.querySelectorAll('.lazy-image').forEach(img => {
     img.addEventListener('error', function() {
       this.src = '/images/placeholders/error.jpg';
       this.classList.add('image-error');
     });
   });
   ```

3. **Loading States**:
   - Display skeleton/placeholder while loading
   - Show error icon if image fails to load
   - Log errors to Laravel log for debugging
   ```php
   // Optional: Log missing images
   if (!file_exists(public_path('images/collage/forest.jpg'))) {
       Log::warning('Missing image: forest.jpg');
   }
   ```

### Browser Compatibility

**Fallbacks for older browsers**:

1. **CSS Grid**: Provide flexbox fallback
   ```css
   .collage-grid {
     display: flex; /* Fallback */
     flex-wrap: wrap;
     display: grid; /* Modern browsers override */
   }
   ```

2. **CSS Variables**: Provide static values as fallback
   ```css
   .hero-tagline {
     color: #5FB878; /* Fallback */
     color: var(--eco-green); /* Modern browsers */
   }
   ```

3. **IntersectionObserver**: Polyfill or load all images for older browsers
   ```javascript
   if ('IntersectionObserver' in window) {
     // Use lazy loading
   } else {
     // Load all images immediately
   }
   ```

### Performance Degradation

**Slow Network Handling**:
- Critical CSS inlined in `<head>` or prioritized by Vite
- Non-critical CSS loaded asynchronously
- Images use `loading="lazy"` to prioritize above-fold content
- Provide visual feedback during loading (skeleton screens)
- Laravel caching for asset versioning and browser caching

**Asset Optimization with Laravel Mix/Vite**:
```javascript
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
});
```

**JavaScript Disabled**:
- Page remains functional without JavaScript (progressive enhancement)
- Navigation links work via anchor tags
- Images still load (without lazy loading optimization)
- CTA buttons remain clickable (use `<a>` tags styled as buttons when appropriate)

## Testing Strategy

### Applicability of Property-Based Testing

This landing page is primarily a **UI rendering and layout project** with the following characteristics:
- Static content presentation
- Visual design and styling
- User interface interactions
- No complex business logic or data transformations
- No parsers, serializers, or algorithms

**Assessment**: Property-based testing is **NOT appropriate** for this feature.

**Rationale**:
- The majority of requirements specify visual presentation ("SHALL display", "SHALL use colors", "SHALL arrange images")
- There are no universal properties to test across input spaces
- The feature involves UI rendering, CSS styling, and layout behavior
- Testing needs are better served by visual regression tests, snapshot tests, and example-based functional tests

### Recommended Testing Approach

#### 1. Laravel Feature Tests (PHPUnit)

**Purpose**: Test HTTP routes, controller responses, and view rendering

**Test File**: `tests/Feature/LandingPageTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingPageTest extends TestCase
{
    public function test_landing_page_loads_successfully(): void
    {
        $response = $this->get('/the-waterfall');
        
        $response->assertStatus(200);
        $response->assertViewIs('landing.index');
    }
    
    public function test_landing_page_contains_required_content(): void
    {
        $response = $this->get('/the-waterfall');
        
        $response->assertSee('Pura Vida - Eco-Luxury Tourism');
        $response->assertSee('Where Nature Meets Wonder');
        $response->assertSee('Get Inspired');
        $response->assertSee('Start Planning');
    }
    
    public function test_landing_page_has_seo_metadata(): void
    {
        $response = $this->get('/the-waterfall');
        
        $response->assertSee('<title>', false);
        $response->assertSee('<meta name="description"', false);
        $response->assertSee('<meta property="og:title"', false);
        $response->assertSee('<meta property="og:image"', false);
    }
    
    public function test_controller_provides_image_data(): void
    {
        $response = $this->get('/the-waterfall');
        
        $response->assertViewHas('images');
        
        $images = $response->viewData('images');
        $this->assertIsArray($images);
        $this->assertCount(7, $images); // 7 categories
        
        // Verify image structure
        foreach ($images as $image) {
            $this->assertArrayHasKey('src', $image);
            $this->assertArrayHasKey('alt', $image);
            $this->assertArrayHasKey('category', $image);
        }
    }
    
    public function test_navigation_items_are_provided(): void
    {
        $response = $this->get('/the-waterfall');
        
        $response->assertViewHas('navigation');
        
        $navigation = $response->viewData('navigation');
        $this->assertIsArray($navigation);
        $this->assertNotEmpty($navigation);
    }
}
```

#### 2. Laravel Dusk Browser Tests

**Purpose**: Test actual browser behavior, JavaScript interactions, and responsive design

**Test File**: `tests/Browser/LandingPageTest.php`

```php
<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LandingPageTest extends DuskTestCase
{
    public function test_hero_section_is_visible(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/the-waterfall')
                    ->assertSee('🌿 Pura Vida - Eco-Luxury Tourism')
                    ->assertSee('Where Nature Meets Wonder')
                    ->assertPresent('.hero-section')
                    ->assertVisible('.hero-section');
        });
    }
    
    public function test_cta_buttons_are_clickable(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/the-waterfall')
                    ->assertPresent('.btn-primary')
                    ->assertPresent('.btn-secondary')
                    ->click('.btn-primary')
                    ->pause(500); // Verify no errors occur
        });
    }
    
    public function test_navigation_menu_works_on_mobile(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(375, 667) // iPhone size
                    ->visit('/the-waterfall')
                    ->assertPresent('.nav-toggle')
                    ->click('.nav-toggle')
                    ->pause(300)
                    ->assertVisible('.nav-menu');
        });
    }
    
    public function test_responsive_layout_on_tablet(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(768, 1024) // Tablet size
                    ->visit('/the-waterfall')
                    ->assertPresent('.collage-grid')
                    ->assertVisible('.collage-grid');
        });
    }
    
    public function test_responsive_layout_on_desktop(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080) // Desktop size
                    ->visit('/the-waterfall')
                    ->assertPresent('.collage-grid')
                    ->assertVisible('.hero-section');
        });
    }
    
    public function test_images_lazy_load(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/the-waterfall')
                    ->scrollIntoView('.image-collage')
                    ->pause(1000)
                    ->assertPresent('.collage-item img')
                    ->assertAttribute('.collage-item img', 'loading', 'lazy');
        });
    }
    
    public function test_navigation_smooth_scroll(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/the-waterfall')
                    ->click('a[href="#gallery"]')
                    ->pause(1000)
                    ->assertPathIs('/the-waterfall');
        });
    }
}
```

#### 3. Component/Unit Tests

**Purpose**: Test Blade components in isolation

**Test File**: `tests/Unit/BladeComponentsTest.php`

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Blade;

class BladeComponentsTest extends TestCase
{
    public function test_cta_button_renders_with_primary_variant(): void
    {
        $html = Blade::render('<x-cta-button variant="primary">Click Me</x-cta-button>');
        
        $this->assertStringContainsString('btn-primary', $html);
        $this->assertStringContainsString('Click Me', $html);
    }
    
    public function test_cta_button_renders_with_secondary_variant(): void
    {
        $html = Blade::render('<x-cta-button variant="secondary">Learn More</x-cta-button>');
        
        $this->assertStringContainsString('btn-secondary', $html);
        $this->assertStringContainsString('Learn More', $html);
    }
    
    public function test_navigation_component_renders_menu_items(): void
    {
        $items = [
            ['label' => 'Home', 'href' => '#home'],
            ['label' => 'About', 'href' => '#about'],
        ];
        
        $html = Blade::render('<x-navigation :items="$items" />', ['items' => $items]);
        
        $this->assertStringContainsString('Home', $html);
        $this->assertStringContainsString('About', $html);
        $this->assertStringContainsString('#home', $html);
        $this->assertStringContainsString('#about', $html);
    }
}
```

#### 4. Visual Regression Testing

**Tool**: Percy, Chromatic, or BackstopJS (integrated with Laravel Dusk)

**Test Cases**:
- Hero section appearance across viewports (mobile, tablet, desktop)
- Image collage layout variations
- Navigation bar states (default, hover, mobile menu open)
- CTA button states (default, hover, focus)
- Color palette consistency
- Typography and gradient effects

**Example BackstopJS Configuration**:
```javascript
// backstop.json
{
  "id": "the-waterfall-landing",
  "viewports": [
    {"label": "phone", "width": 375, "height": 667},
    {"label": "tablet", "width": 768, "height": 1024},
    {"label": "desktop", "width": 1920, "height": 1080}
  ],
  "scenarios": [
    {
      "label": "Hero Section",
      "url": "http://localhost:8000/the-waterfall",
      "selectors": [".hero-section"]
    },
    {
      "label": "Image Collage",
      "url": "http://localhost:8000/the-waterfall",
      "selectors": [".image-collage"]
    }
  ]
}
```

#### 5. Accessibility Testing

**Tools**: Laravel Dusk + axe-core integration

**Test File**: `tests/Browser/AccessibilityTest.php`

```php
<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AccessibilityTest extends DuskTestCase
{
    public function test_page_passes_accessibility_audit(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/the-waterfall')
                    // Inject axe-core
                    ->script("
                        var script = document.createElement('script');
                        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/axe-core/4.7.2/axe.min.js';
                        document.head.appendChild(script);
                    ")
                    ->pause(2000)
                    // Run axe accessibility audit
                    ->assertScript("
                        return axe.run().then(results => {
                            return results.violations.length === 0;
                        });
                    ", true);
        });
    }
    
    public function test_all_images_have_alt_text(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/the-waterfall')
                    ->assertScript("
                        const images = document.querySelectorAll('img');
                        return Array.from(images).every(img => img.alt && img.alt.length > 0);
                    ", true);
        });
    }
    
    public function test_buttons_are_keyboard_accessible(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/the-waterfall')
                    ->keys('.btn-primary', '{tab}')
                    ->assertFocused('.btn-primary');
        });
    }
}
```

#### 6. Performance Testing

**Tools**: Laravel Telescope, Lighthouse CI, Chrome DevTools

**Metrics to Monitor**:
- First Contentful Paint (FCP) < 1.8s
- Largest Contentful Paint (LCP) < 2.5s
- Time to Interactive (TTI) < 3.8s
- Total Blocking Time (TBT) < 200ms
- Cumulative Layout Shift (CLS) < 0.1

**Test File**: `tests/Browser/PerformanceTest.php`

```php
<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PerformanceTest extends DuskTestCase
{
    public function test_page_loads_within_3_seconds(): void
    {
        $this->browse(function (Browser $browser) {
            $startTime = microtime(true);
            
            $browser->visit('/the-waterfall')
                    ->waitFor('.hero-section', 3);
            
            $loadTime = microtime(true) - $startTime;
            
            $this->assertLessThan(3.0, $loadTime, 'Page took too long to load');
        });
    }
    
    public function test_images_use_optimized_formats(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/the-waterfall')
                    ->assertScript("
                        const sources = document.querySelectorAll('picture source');
                        return Array.from(sources).some(source => 
                            source.type === 'image/webp'
                        );
                    ", true);
        });
    }
}
```

#### 7. SEO Validation

**Test File**: `tests/Feature/SEOTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class SEOTest extends TestCase
{
    public function test_page_has_title_tag(): void
    {
        $response = $this->get('/the-waterfall');
        
        $response->assertSee('<title>', false);
        $response->assertSee('The Waterfall', false);
    }
    
    public function test_page_has_meta_description(): void
    {
        $response = $this->get('/the-waterfall');
        
        $response->assertSee('<meta name="description"', false);
    }
    
    public function test_page_has_open_graph_tags(): void
    {
        $response = $this->get('/the-waterfall');
        
        $response->assertSee('<meta property="og:title"', false);
        $response->assertSee('<meta property="og:description"', false);
        $response->assertSee('<meta property="og:image"', false);
    }
    
    public function test_page_has_structured_data(): void
    {
        $response = $this->get('/the-waterfall');
        
        $response->assertSee('<script type="application/ld+json">', false);
        $response->assertSee('"@type":"TouristAttraction"', false);
    }
    
    public function test_heading_hierarchy_is_correct(): void
    {
        $response = $this->get('/the-waterfall');
        
        $content = $response->getContent();
        
        // Should have exactly one h1
        $this->assertEquals(1, substr_count($content, '<h1'));
        $this->assertStringContainsString('Pura Vida', $content);
    }
}
```

### Running Tests

**PHPUnit Feature/Unit Tests**:
```bash
php artisan test
php artisan test --filter=LandingPageTest
```

**Laravel Dusk Browser Tests**:
```bash
php artisan dusk
php artisan dusk tests/Browser/LandingPageTest.php
```

**Performance Testing with Lighthouse CI**:
```bash
npm install -g @lhci/cli
lhci autorun --collect.url=http://localhost:8000/the-waterfall
```

### Test Coverage Goals

- **Feature Coverage**: All controller methods and routes tested
- **Component Coverage**: All Blade components render correctly
- **Visual Coverage**: All viewport sizes and component states
- **Functional Coverage**: All user interactions and navigation paths
- **Accessibility Coverage**: 100% of WCAG 2.1 AA criteria
- **Performance Coverage**: Core Web Vitals within recommended thresholds
- **Browser Coverage**: Last 2 versions of major browsers + Safari

### Testing Summary

This Laravel-based landing page requires a comprehensive testing approach focused on:

1. **Backend Testing** (PHPUnit)
   - Route and controller functionality
   - View data preparation
   - Blade component rendering

2. **Frontend Testing** (Laravel Dusk)
   - Browser behavior and interactions
   - Responsive design across devices
   - JavaScript functionality

3. **Visual Testing** (BackstopJS/Percy)
   - Visual regression across viewports
   - Component state variations

4. **Accessibility Testing** (axe-core integration)
   - WCAG compliance
   - Keyboard navigation
   - Screen reader compatibility

5. **Performance Testing** (Lighthouse CI)
   - Core Web Vitals monitoring
   - Asset optimization verification

6. **SEO Testing** (PHPUnit)
   - Metadata validation
   - Structured data compliance

Property-based testing is not applicable because there are no algorithms, parsers, or data transformations to test. The focus is on presentation, interaction, and optimization rather than computational correctness.

