# Design Document

## Overview

This feature implements dedicated package category pages for the Godong Ijo tourism website. The system will create three separate category pages—one for each package type (The Waterfall Resto, Private Room, Fishing Lake)—accessible through the navbar dropdown menu. Each category page displays packages filtered by `jenis_paket` in a horizontal scrolling card layout with full SEO support, breadcrumb navigation, and responsive design.

### Core Functionality

- **Route-based Category Filtering**: URL pattern `/paket/{category}` maps to database ENUM values via slug translation
- **Dynamic Package Display**: Fetches and displays packages filtered by category with card-based UI
- **Navigation Integration**: Active state highlighting in navbar dropdown for current category
- **SEO Optimization**: Category-specific metadata, Open Graph tags, and structured data
- **Responsive Layout**: Horizontal scrolling grid adapting to viewport sizes (1, 2, 3, or 4 columns)
- **Performance**: Database query caching (5-minute TTL) and lazy image loading

### Technical Approach

The implementation leverages Laravel's existing architecture:
- **Controller**: New `PackageCategoryController` handles category page logic
- **Services**: Extends `SEOService`, `BreadcrumbService`, and `NavigationService` with category support
- **Model**: Uses existing `PaketWisata` model with `jenis_paket` column
- **View**: New Blade template reusing package card component patterns from landing page
- **Routes**: New route definition with category parameter validation

## Architecture

### System Components

```
┌─────────────────────────────────────────────────────────────┐
│                     Browser (User)                          │
└────────────┬────────────────────────────────────────────────┘
             │
             │ HTTP GET /paket/{category}
             ▼
┌─────────────────────────────────────────────────────────────┐
│                     Laravel Router                          │
│  - Route: packages.category                                 │
│  - Pattern: /paket/{category}                               │
│  - Validation: the-waterfall-resto|private-room|fishing-lake│
└────────────┬────────────────────────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────────────────────────┐
│              PackageCategoryController                      │
│  - show(string $category)                                   │
│  - mapSlugToJenisPaket(string $slug): string               │
│  - getCategoryMetadata(string $category): array            │
└────────────┬──────────────────────┬─────────────────────────┘
             │                      │
             │                      │ Inject Services
             ▼                      ▼
┌──────────────────────┐  ┌──────────────────────────────────┐
│   PaketWisata Model  │  │   Service Layer                  │
│  - where(jenis_paket)│  │  - SEOService                    │
│  - where(is_active)  │  │  - BreadcrumbService             │
│  - orderBy()         │  │  - NavigationService             │
└──────────────────────┘  └──────────────────────────────────┘
             │
             │ Query Results (Collection)
             ▼
┌─────────────────────────────────────────────────────────────┐
│              View Layer (Blade Template)                    │
│  - categories/show.blade.php                                │
│  - Renders: Header, Package Cards, Empty State              │
└─────────────────────────────────────────────────────────────┘
```

### Data Flow

1. **Request Processing**:
   - User clicks category link in navbar dropdown (e.g., "Paket Kuliner Keluarga")
   - Router receives GET request to `/paket/the-waterfall-resto`
   - Route validation ensures category slug is valid
   - Request forwarded to `PackageCategoryController@show`

2. **Controller Logic**:
   - Map URL slug to database ENUM value (e.g., `the-waterfall-resto` → `The Waterfall Resto`)
   - Query `PaketWisata` model filtered by `jenis_paket` and `is_active`
   - Generate SEO metadata via `SEOService`
   - Generate breadcrumbs via `BreadcrumbService`
   - Pass data to view template

3. **View Rendering**:
   - Display category header with title and subtitle
   - Render package cards in horizontal scrolling grid
   - Show empty state if no packages found
   - Include navigation, breadcrumbs, and footer components

4. **Client-Side Interaction**:
   - Horizontal scroll via mouse wheel or touch gestures
   - Responsive grid layout based on viewport width
   - Lazy loading of package images below the fold
   - "Hubungi" button links scroll to contact section

### Integration Points

- **Navigation Component**: Highlights active category in dropdown menu
- **SEO Service**: Generates category-specific meta tags and structured data
- **Breadcrumb Service**: Builds category page breadcrumb trail
- **Package Model**: Provides filtered package data
- **Cache Layer**: Stores query results for 5 minutes to reduce database load

## Components and Interfaces

### Controller: PackageCategoryController

**Purpose**: Handles HTTP requests for category pages and orchestrates data retrieval

**Location**: `app/Http/Controllers/PackageCategoryController.php`

**Methods**:

```php
class PackageCategoryController extends Controller
{
    protected SEOService $seoService;
    protected BreadcrumbService $breadcrumbService;
    protected NavigationService $navigationService;

    public function __construct(
        SEOService $seoService,
        BreadcrumbService $breadcrumbService,
        NavigationService $navigationService
    );
    
    /**
     * Display category page
     * @param string $category URL slug (the-waterfall-resto, private-room, fishing-lake)
     * @return \Illuminate\View\View
     * @throws NotFoundHttpException if category invalid
     */
    public function show(string $category): View;
    
    /**
     * Map URL slug to jenis_paket ENUM value
     * @param string $slug URL slug
     * @return string ENUM value
     * @throws InvalidArgumentException if slug not recognized
     */
    private function mapSlugToJenisPaket(string $slug): string;
    
    /**
     * Get category display metadata
     * @param string $slug URL slug
     * @return array ['name' => string, 'description' => string, 'default_image' => string]
     */
    private function getCategoryMetadata(string $slug): array;
}
```

**Slug Mapping**:

```php
private array $categoryMap = [
    'the-waterfall-resto' => 'The Waterfall Resto',
    'private-room' => 'Private Room',
    'fishing-lake' => 'Fishing Lake',
];
```

**Category Metadata**:

```php
private array $categoryMetadata = [
    'the-waterfall-resto' => [
        'name' => 'Paket The Waterfall Resto',
        'description' => 'Pengalaman kuliner ekologis dengan konsep Dine in Nature',
        'default_image' => 'images/The Waterfall Resto Images/Tentang-TWF-2048x769.webp',
    ],
    'private-room' => [
        'name' => 'Paket Private Room',
        'description' => 'Ruang privat untuk acara spesial dan gathering',
        'default_image' => 'images/Private Images/BCA-Gathering-2048x1137.webp',
    ],
    'fishing-lake' => [
        'name' => 'Paket Fishing Lake',
        'description' => 'Sport fishing dengan ikan monster di kolam eksklusif',
        'default_image' => 'images/placeholders/Redtail-Catfish-Ikan-Predator-Amerika-Selatan-1536x853.webp',
    ],
];
```

### Service Extensions

#### SEOService Extension

**New Method**:

```php
/**
 * Generate SEO metadata for package category pages
 * @param string $categoryName Display name
 * @param string $categorySlug URL slug
 * @param array $packages Package collection for image selection
 * @return array Complete SEO metadata
 */
public function generateCategoryMetadata(
    string $categoryName,
    string $categorySlug,
    array $packages
): array;
```

**Implementation Details**:
- **Title Format**: `"{Category Name} - Paket Wisata | Godong Ijo"`
- **Description**: Combines category name with "Pilih Paket Sesuai Kebutuhan"
- **Canonical URL**: `route('packages.category', ['category' => $categorySlug])`
- **OG Image**: First package photo if available, else default category image
- **Structured Data**: Product schema for each package

#### BreadcrumbService Extension

**New Method**:

```php
/**
 * Generate breadcrumbs for category pages
 * @param string $categoryName Display name
 * @return array Breadcrumb items
 */
public function generateForCategory(string $categoryName): array;
```

**Breadcrumb Structure**:

```php
[
    ['label' => 'Home', 'url' => route('landing'), 'current' => false],
    ['label' => 'Paket Wisata', 'url' => null, 'current' => false],
    ['label' => $categoryName, 'url' => null, 'current' => true],
]
```

#### NavigationService Extension

**New Method**:

```php
/**
 * Check if category route is active
 * @param string $categorySlug URL slug
 * @return bool True if current category page
 */
public function isCategoryActive(string $categorySlug): bool;
```

**Implementation**: Compares current route name and category parameter with provided slug

### View Template

**Location**: `resources/views/categories/show.blade.php`

**Template Structure**:

```blade
@extends('layouts.app')

@section('content')
<div class="category-page">
    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="$breadcrumbs" />
    
    {{-- Page Header --}}
    <section class="category-header">
        <h1>Pilih Paket Sesuai Kebutuhan</h1>
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
                @foreach($packages as $package)
                    <x-package-card :package="$package" />
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
```

### Package Card Component

**Reuses existing pattern from landing page**:

```blade
<article class="package-card">
    {{-- Package Image --}}
    <div class="package-image">
        @if($package->foto)
            <img 
                src="{{ asset($package->foto) }}" 
                alt="{{ $package->nama_paket }}"
                loading="lazy"
            >
        @else
            <div class="package-placeholder">
                <svg><!-- Placeholder icon --></svg>
            </div>
        @endif
        <span class="package-badge">Fleksibel</span>
    </div>
    
    {{-- Package Info --}}
    <div class="package-info">
        <h3 class="package-name">{{ $package->nama_paket }}</h3>
        <p class="package-price">
            @if($package->harga > 0)
                Mulai Rp {{ number_format($package->harga, 0, ',', '.') }} /orang
            @else
                Hubungi Kami
            @endif
        </p>
        <a href="#contact" class="package-cta">Hubungi</a>
    </div>
</article>
```

### Route Definition

**Location**: `routes/web.php`

```php
Route::get('/paket/{category}', [PackageCategoryController::class, 'show'])
    ->name('packages.category')
    ->where('category', 'the-waterfall-resto|private-room|fishing-lake');
```

## Data Models

### PaketWisata Model

**Existing Model** (no changes needed):

```php
class PaketWisata extends Model
{
    protected $table = 'paket_wisata';
    
    protected $fillable = [
        'nama_paket',
        'jenis_paket',  // ENUM: 'The Waterfall Resto', 'Private Room', 'Fishing Lake', 'Ecotainment'
        'deskripsi',
        'foto',
        'harga',
        'kuota',
        'is_active',
    ];
    
    protected $casts = [
        'harga' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
```

### Database Query

**Controller Query with Caching**:

```php
$packages = Cache::remember(
    "packages.category.{$jenisPaket}",
    now()->addMinutes(5),
    function () use ($jenisPaket) {
        return PaketWisata::where('jenis_paket', $jenisPaket)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }
);
```


**Performance Optimization**:
- Index on `jenis_paket` column: `CREATE INDEX idx_jenis_paket ON paket_wisata(jenis_paket, is_active);`
- Composite index for filtering: Speeds up `WHERE jenis_paket = ? AND is_active = 1` queries
- Query caching reduces database load on repeated requests
- Cache invalidation on package create/update/delete events

### View Data Transfer Object

**Data passed to view**:

```php
return view('categories.show', [
    'category' => $categorySlug,              // 'the-waterfall-resto'
    'categoryMeta' => $categoryMetadata,       // Name, description, default image
    'packages' => $packages,                   // Collection of PaketWisata
    'seoData' => $seoMetadata,                 // From SEOService
    'breadcrumbs' => $breadcrumbs,             // From BreadcrumbService
    'navigation' => $navigationItems,          // From NavigationService
    'cta' => $ctaConfig,                       // CTA button configuration
]);
```

## Error Handling

### Invalid Category Slug

**Scenario**: User navigates to `/paket/invalid-category`

**Handling**:
- Route constraint validation fails
- Laravel returns 404 Not Found automatically
- No controller code reached

**Prevention**:
```php
// In routes/web.php
->where('category', 'the-waterfall-resto|private-room|fishing-lake');
```

### Empty Package Collection

**Scenario**: Valid category but no active packages exist

**Handling**:
- Controller query returns empty collection
- View template checks `$packages->isEmpty()`
- Empty state component displayed
- User sees message: "Belum ada paket tersedia untuk kategori ini"
- CTA button links to contact section

**Implementation**:
```blade
@if($packages->isEmpty())
    <div class="empty-state">
        <svg class="empty-icon"><!-- Icon --></svg>
        <h3>Belum ada paket tersedia untuk kategori ini</h3>
        <p>Silakan hubungi kami untuk informasi paket custom</p>
        <a href="#contact" class="btn btn-primary">Hubungi Kami</a>
    </div>
@else
    {{-- Display packages --}}
@endif
```

### Database Query Failure

**Scenario**: Database connection error or query execution fails

**Handling**:
- Laravel's query exception caught by global handler
- Error logged to application log
- User sees 500 Internal Server Error page
- Cache layer helps prevent repeated failures

**Logging**:
```php
try {
    $packages = PaketWisata::where('jenis_paket', $jenisPaket)
        ->where('is_active', true)
        ->orderBy('created_at', 'desc')
        ->get();
} catch (\Exception $e) {
    \Log::error('Failed to fetch packages for category', [
        'category' => $category,
        'jenis_paket' => $jenisPaket,
        'error' => $e->getMessage(),
    ]);
    
    // Return empty collection to show empty state instead of error page
    $packages = collect([]);
}
```

### Missing Package Images

**Scenario**: Package record has `foto` field as NULL

**Handling**:
- View template checks for image existence
- Placeholder with icon displayed if no image
- Consistent with landing page card behavior

**Implementation**:
```blade
@if($package->foto)
    <img src="{{ asset($package->foto) }}" alt="{{ $package->nama_paket }}" loading="lazy">
@else
    <div class="no-image-placeholder">
        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
            <circle cx="8.5" cy="8.5" r="1.5"/>
            <polyline points="21 15 16 10 5 21"/>
        </svg>
        <p>Tidak ada foto</p>
    </div>
@endif
```

### SEO Metadata Generation Errors

**Scenario**: SEOService fails to generate metadata

**Handling**:
- Fallback to default metadata values
- Error logged for debugging
- Page still renders with basic SEO tags

**Fallback Values**:
```php
$seoData = [
    'title' => "{$categoryMeta['name']} | Godong Ijo",
    'description' => "Jelajahi paket wisata {$categoryMeta['name']} di Godong Ijo",
    'keywords' => ['godong ijo', 'paket wisata', 'wisata keluarga'],
    'og' => [
        'title' => $categoryMeta['name'],
        'description' => $categoryMeta['description'],
        'image' => asset($categoryMeta['default_image']),
        'url' => route('packages.category', ['category' => $category]),
    ],
];
```

### Cache Invalidation

**Scenario**: Package data updated but cached version still served

**Handling**:
- Cache automatically expires after 5 minutes
- Manual cache clearing on package CRUD operations
- Event listeners clear category cache when package updated

**Cache Clearing**:
```php
// In PaketWisata model observers or event listeners
public function saved()
{
    Cache::forget("packages.category.{$this->jenis_paket}");
}

public function deleted()
{
    Cache::forget("packages.category.{$this->jenis_paket}");
}
```

## Testing Strategy

### Unit Tests

**Test Class**: `tests/Unit/Controllers/PackageCategoryControllerTest.php`

**Test Cases**:

1. **test_mapSlugToJenisPaket_returns_correct_enum_value()**
   - Input: `'the-waterfall-resto'`
   - Expected: `'The Waterfall Resto'`
   - Purpose: Verify slug-to-enum mapping correctness

2. **test_mapSlugToJenisPaket_throws_exception_for_invalid_slug()**
   - Input: `'invalid-category'`
   - Expected: `InvalidArgumentException`
   - Purpose: Ensure invalid slugs rejected

3. **test_getCategoryMetadata_returns_correct_data()**
   - Input: `'private-room'`
   - Expected: Array with name, description, default_image keys
   - Purpose: Verify metadata structure

### Integration Tests

**Test Class**: `tests/Feature/PackageCategoryPageTest.php`

**Test Cases**:

1. **test_category_page_displays_filtered_packages()**
   ```php
   // Arrange: Create packages for different categories
   PaketWisata::factory()->create(['jenis_paket' => 'The Waterfall Resto', 'is_active' => true]);
   PaketWisata::factory()->create(['jenis_paket' => 'Private Room', 'is_active' => true]);
   
   // Act: Visit category page
   $response = $this->get('/paket/the-waterfall-resto');
   
   // Assert: Only The Waterfall Resto packages visible
   $response->assertStatus(200);
   $response->assertSee('The Waterfall Resto');
   $response->assertDontSee('Private Room');
   ```

2. **test_category_page_returns_404_for_invalid_slug()**
   ```php
   $response = $this->get('/paket/invalid-category');
   $response->assertStatus(404);
   ```

3. **test_category_page_displays_empty_state_when_no_packages()**
   ```php
   // Arrange: No packages for category
   
   // Act
   $response = $this->get('/paket/fishing-lake');
   
   // Assert
   $response->assertStatus(200);
   $response->assertSee('Belum ada paket tersedia');
   $response->assertSee('Hubungi Kami');
   ```

4. **test_category_page_includes_breadcrumbs()**
   ```php
   $response = $this->get('/paket/private-room');
   
   $response->assertSee('Home');
   $response->assertSee('Paket Wisata');
   $response->assertSee('Paket Private Room');
   ```

5. **test_category_page_includes_seo_metadata()**
   ```php
   $response = $this->get('/paket/the-waterfall-resto');
   
   $response->assertSee('<title>Paket The Waterfall Resto - Paket Wisata | Godong Ijo</title>', false);
   $response->assertSee('<meta property="og:title"', false);
   $response->assertSee('<meta name="description"', false);
   ```

6. **test_inactive_packages_not_displayed()**
   ```php
   // Arrange
   PaketWisata::factory()->create([
       'jenis_paket' => 'The Waterfall Resto',
       'is_active' => false,
       'nama_paket' => 'Inactive Package'
   ]);
   
   // Act
   $response = $this->get('/paket/the-waterfall-resto');
   
   // Assert
   $response->assertDontSee('Inactive Package');
   ```

7. **test_packages_ordered_by_created_at_desc()**
   ```php
   // Arrange: Create packages with different timestamps
   $older = PaketWisata::factory()->create([
       'jenis_paket' => 'The Waterfall Resto',
       'created_at' => now()->subDays(2),
       'nama_paket' => 'Older Package'
   ]);
   $newer = PaketWisata::factory()->create([
       'jenis_paket' => 'The Waterfall Resto',
       'created_at' => now(),
       'nama_paket' => 'Newer Package'
   ]);
   
   // Act
   $response = $this->get('/paket/the-waterfall-resto');
   $content = $response->getContent();
   
   // Assert: Newer package appears before older
   $newerPos = strpos($content, 'Newer Package');
   $olderPos = strpos($content, 'Older Package');
   $this->assertTrue($newerPos < $olderPos);
   ```

### Browser Tests (Laravel Dusk)

**Test Class**: `tests/Browser/PackageCategoryTest.php`

**Test Cases**:

1. **test_horizontal_scroll_works_with_mouse_wheel()**
   - Action: Scroll mouse wheel over package grid
   - Expected: Package cards scroll horizontally
   - Purpose: Verify scroll interaction

2. **test_responsive_grid_columns()**
   - Action: Resize browser to different widths
   - Expected:
     - Desktop (≥1024px): 4 cards per row
     - Tablet (768-1023px): 3 cards per row
     - Mobile (<768px): 2 cards per row
   - Purpose: Verify responsive layout

3. **test_hubungi_button_scrolls_to_contact()**
   - Action: Click "Hubungi" button on package card
   - Expected: Page scrolls to #contact section
   - Purpose: Verify CTA functionality

4. **test_navigation_highlights_active_category()**
   - Action: Visit category page
   - Expected: Corresponding navbar dropdown item has "active" class
   - Purpose: Verify navigation integration

5. **test_images_lazy_load()**
   - Action: Check image attributes
   - Expected: Images have `loading="lazy"` attribute
   - Purpose: Verify performance optimization

### Performance Tests

**Test Cases**:

1. **test_category_page_loads_in_acceptable_time()**
   ```php
   $start = microtime(true);
   $response = $this->get('/paket/the-waterfall-resto');
   $duration = (microtime(true) - $start) * 1000; // Convert to milliseconds
   
   $this->assertLessThan(500, $duration, 'Page load time exceeds 500ms');
   ```

2. **test_database_query_uses_index()**
   ```php
   // Run EXPLAIN on query
   $explain = DB::select('EXPLAIN SELECT * FROM paket_wisata WHERE jenis_paket = ? AND is_active = 1', ['The Waterfall Resto']);
   
   // Assert index usage
   $this->assertStringContainsString('idx_jenis_paket', $explain[0]->key);
   ```

3. **test_cache_reduces_query_count()**
   ```php
   // First request: should hit database
   DB::enableQueryLog();
   $this->get('/paket/the-waterfall-resto');
   $queries1 = count(DB::getQueryLog());
   DB::disableQueryLog();
   
   // Second request: should use cache
   DB::enableQueryLog();
   $this->get('/paket/the-waterfall-resto');
   $queries2 = count(DB::getQueryLog());
   DB::disableQueryLog();
   
   // Cache should reduce queries
   $this->assertLessThan($queries1, $queries2);
   ```

### Accessibility Tests

**Manual Testing Checklist** (automated tools cannot fully validate):

1. **Keyboard Navigation**
   - [ ] Tab key navigates through all interactive elements
   - [ ] Arrow keys scroll horizontal package grid
   - [ ] Enter/Space activates buttons and links
   - [ ] Focus indicators visible on all elements

2. **Screen Reader Compatibility**
   - [ ] Page heading announced correctly
   - [ ] Package count announced (e.g., "3 packages found")
   - [ ] Card information read in logical order
   - [ ] Empty state message announced clearly

3. **Color Contrast**
   - [ ] Text-to-background ratio ≥ 4.5:1 (WCAG AA)
   - [ ] Interactive elements distinguishable
   - [ ] Badge overlay readable against images

4. **Semantic HTML**
   - [ ] Proper heading hierarchy (h1 → h2 → h3)
   - [ ] Section, article, nav elements used correctly
   - [ ] Alt text present on all images
   - [ ] ARIA labels on interactive elements

5. **Browser Zoom**
   - [ ] Layout usable at 200% zoom
   - [ ] Text not truncated
   - [ ] Horizontal scroll still functional
   - [ ] No horizontal page overflow

### Testing Tools

- **Unit/Integration Tests**: PHPUnit (included with Laravel)
- **Browser Tests**: Laravel Dusk
- **Performance**: Laravel Debugbar, Chrome DevTools
- **Accessibility**: WAVE, axe DevTools, NVDA screen reader
- **Responsiveness**: Chrome DevTools Device Toolbar

### Test Execution

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run specific test class
php artisan test tests/Feature/PackageCategoryPageTest.php

# Run with coverage
php artisan test --coverage

# Run browser tests
php artisan dusk

# Run specific browser test
php artisan dusk tests/Browser/PackageCategoryTest.php
```

## Implementation Notes

### CSS Horizontal Scroll Implementation

**Smooth scrolling without gaps** (as specified in requirements):

```css
.packages-grid {
    display: flex;
    gap: 24px;
    overflow-x: auto;
    scroll-behavior: smooth;
    
    /* Hide scrollbar but keep functionality */
    scrollbar-width: thin;
    scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
}

.packages-grid::-webkit-scrollbar {
    height: 8px;
}

.packages-grid::-webkit-scrollbar-track {
    background: transparent;
}

.packages-grid::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 4px;
}

.packages-grid::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.3);
}

/* Responsive grid */
@media (min-width: 1024px) {
    .package-card {
        flex: 0 0 calc((100% - 72px) / 4); /* 4 cards */
    }
}

@media (min-width: 768px) and (max-width: 1023px) {
    .package-card {
        flex: 0 0 calc((100% - 48px) / 3); /* 3 cards */
    }
}

@media (max-width: 767px) {
    .package-card {
        flex: 0 0 calc((100% - 24px) / 2); /* 2 cards */
    }
}
```

### Database Index Migration

```php
// Migration: database/migrations/YYYY_MM_DD_HHMMSS_add_index_to_paket_wisata_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paket_wisata', function (Blueprint $table) {
            // Composite index for filtering queries
            $table->index(['jenis_paket', 'is_active'], 'idx_jenis_paket_active');
        });
    }

    public function down(): void
    {
        Schema::table('paket_wisata', function (Blueprint $table) {
            $table->dropIndex('idx_jenis_paket_active');
        });
    }
};
```

### Cache Configuration

**Cache Tags** (requires Redis or Memcached):

```php
// In controller
Cache::tags(['packages', "category:{$jenisPaket}"])
    ->remember("packages.category.{$jenisPaket}", now()->addMinutes(5), function () use ($jenisPaket) {
        return PaketWisata::where('jenis_paket', $jenisPaket)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();
    });

// Cache invalidation on model events
class PaketWisata extends Model
{
    protected static function booted()
    {
        static::saved(function ($package) {
            Cache::tags(['packages', "category:{$package->jenis_paket}"])->flush();
        });
        
        static::deleted(function ($package) {
            Cache::tags(['packages', "category:{$package->jenis_paket}"])->flush();
        });
    }
}
```

### SEO Structured Data Example

```json
{
  "@context": "https://schema.org",
  "@type": "ItemList",
  "name": "Paket The Waterfall Resto",
  "description": "Pengalaman kuliner ekologis dengan konsep Dine in Nature",
  "numberOfItems": 3,
  "itemListElement": [
    {
      "@type": "Product",
      "position": 1,
      "name": "Paket Kuliner Keluarga",
      "description": "Nikmati pengalaman kuliner ekologis...",
      "image": "https://example.com/images/paket-kuliner.jpg",
      "offers": {
        "@type": "Offer",
        "price": "75000",
        "priceCurrency": "IDR",
        "availability": "https://schema.org/InStock",
        "url": "https://example.com/paket/the-waterfall-resto"
      }
    }
  ]
}
```

## Security Considerations

### Input Validation

- **Route Constraint**: Limits category parameter to whitelisted values
- **No User Input**: Category slug comes from URL, not form input
- **CSRF Protection**: Not applicable (GET request only)

### SQL Injection Prevention

- **Eloquent ORM**: Uses parameter binding automatically
- **Query Example**: `where('jenis_paket', $value)` is safe
- **No Raw Queries**: Avoid `DB::raw()` with user input

### XSS Prevention

- **Blade Escaping**: `{{ $variable }}` escapes HTML by default
- **Image URLs**: Use `asset()` helper for trusted paths
- **Package Names**: Escaped in view templates automatically

### Rate Limiting

```php
// In routes/web.php or middleware
Route::get('/paket/{category}', [PackageCategoryController::class, 'show'])
    ->middleware('throttle:60,1') // 60 requests per minute
    ->name('packages.category');
```

## Deployment Checklist

### Pre-Deployment

- [ ] Run test suite: `php artisan test`
- [ ] Run static analysis: `vendor/bin/phpstan analyse`
- [ ] Check code style: `vendor/bin/php-cs-fixer fix --dry-run`
- [ ] Build assets: `npm run production`
- [ ] Clear caches: `php artisan cache:clear`

### Database

- [ ] Run migrations: `php artisan migrate`
- [ ] Verify index created: `SHOW INDEX FROM paket_wisata;`
- [ ] Test query performance: `EXPLAIN SELECT ... WHERE jenis_paket = ?`

### Configuration

- [ ] Update `.env` with production cache driver (Redis recommended)
- [ ] Configure cache TTL if needed
- [ ] Set `APP_DEBUG=false` in production
- [ ] Configure SEO base URL: `APP_URL=https://production-domain.com`

### Post-Deployment

- [ ] Test all 3 category URLs in production
- [ ] Verify SEO metadata with Google Search Console
- [ ] Check navigation active states
- [ ] Test horizontal scroll on mobile devices
- [ ] Monitor error logs for first 24 hours
- [ ] Verify cache warming (first load may be slow)

### Monitoring

- [ ] Set up application monitoring (e.g., Laravel Horizon for queue jobs)
- [ ] Configure error tracking (e.g., Sentry, Bugsnag)
- [ ] Monitor page load times (e.g., New Relic, Scout APM)
- [ ] Track SEO performance (Google Analytics, Search Console)
