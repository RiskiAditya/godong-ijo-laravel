# Implementation Plan: Culinary Family Package Selection

## Overview

This implementation creates three dedicated package category pages (The Waterfall Resto, Private Room, Fishing Lake) accessible from the navbar "Paket Wisata" dropdown. Each category page displays packages filtered by `jenis_paket` in a horizontal scrolling card layout, reusing the landing page card design with full SEO, breadcrumb, and navigation integration.

## Tasks

- [x] 1. Create database migration for performance index
  - Create migration file to add composite index on `jenis_paket` and `is_active` columns
  - Index name: `idx_jenis_paket_active`
  - Run migration to apply changes
  - _Requirements: 10.1_

- [ ] 2. Create PackageCategoryController with slug mapping and metadata
  - [x] 2.1 Create controller file at `app/Http/Controllers/PackageCategoryController.php`
    - Create controller extending base Controller class
    - Inject SEOService, BreadcrumbService, and NavigationService dependencies
    - Define `$categoryMap` property with slug-to-enum mapping
    - Define `$categoryMetadata` property with name, description, and default_image for each category
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 2.2, 2.3, 2.4, 3.3_
  
  - [ ] 2.2 Implement show() method for category page display
    - Accept `$category` parameter (URL slug)
    - Call `mapSlugToJenisPaket()` to convert slug to ENUM value
    - Query `PaketWisata` filtered by `jenis_paket` and `is_active = true`
    - Order results by `created_at DESC`
    - Wrap query in 5-minute cache with key `"packages.category.{$jenisPaket}"`
    - Get category metadata via `getCategoryMetadata()`
    - Generate SEO metadata via `SEOService::generateCategoryMetadata()`
    - Generate breadcrumbs via `BreadcrumbService::generateForCategory()`
    - Get navigation items with active state
    - Return view with all data
    - _Requirements: 1.2, 1.3, 1.4, 2.1, 2.5, 2.6, 10.3_
  
  - [ ] 2.3 Implement mapSlugToJenisPaket() private method
    - Check if slug exists in `$categoryMap`
    - Return corresponding ENUM value
    - Throw `InvalidArgumentException` if slug not found
    - _Requirements: 2.2, 2.3, 2.4_
  
  - [ ] 2.4 Implement getCategoryMetadata() private method
    - Check if slug exists in `$categoryMetadata`
    - Return metadata array with name, description, default_image
    - Return default values if slug not found
    - _Requirements: 3.3, 7.6_

- [ ] 3. Extend SEOService with category metadata generation
  - [ ] 3.1 Add generateCategoryMetadata() method to SEOService
    - Accept `$categoryName`, `$categorySlug`, and `$packages` parameters
    - Generate title in format `"{$categoryName} - Paket Wisata | Godong Ijo"`
    - Generate description combining category name with "Pilih Paket Sesuai Kebutuhan"
    - Generate canonical URL using `route('packages.category', ['category' => $categorySlug])`
    - Select OG image: first package photo if available, else default category image
    - Generate Open Graph metadata (og:title, og:description, og:image, og:url)
    - Generate Twitter Card metadata
    - Generate Product schema for each package in collection
    - Return complete metadata array
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 7.6, 7.7_

- [ ] 4. Extend BreadcrumbService with category page support
  - [ ] 4.1 Add generateForCategory() method to BreadcrumbService
    - Accept `$categoryName` parameter
    - Build breadcrumb array: Home (clickable) > Paket Wisata (non-clickable) > {Category Name} (current)
    - Set `url` to `route('landing')` for Home breadcrumb
    - Set `url` to `null` for Paket Wisata breadcrumb
    - Mark category name breadcrumb as current with `current => true`
    - Return breadcrumb items array
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5, 8.6_

- [ ] 5. Extend NavigationService with category active state detection
  - [ ] 5.1 Add isCategoryActive() method to NavigationService
    - Accept `$categorySlug` parameter
    - Get current route name via `Route::currentRouteName()`
    - Check if route is `packages.category`
    - Get current route parameter `category` via `request()->route('category')`
    - Compare current category parameter with provided slug
    - Return boolean indicating active state
    - _Requirements: 6.1, 6.2, 6.3, 6.4_

- [x] 6. Create category page route with validation
  - Add route to `routes/web.php`: `Route::get('/paket/{category}', [PackageCategoryController::class, 'show'])->name('packages.category')`
  - Apply route constraint with `->where('category', 'the-waterfall-resto|private-room|fishing-lake')`
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 1.6_

- [ ] 7. Create category page Blade template
  - [ ] 7.1 Create view file at `resources/views/categories/show.blade.php`
    - Extend `layouts.app` layout
    - Define `@section('content')` block
    - Include breadcrumbs component: `<x-breadcrumbs :items="$breadcrumbs" />`
    - Create category header section with heading "Pilih Paket Sesuai Kebutuhan"
    - Display subtitle "Dari kuliner keluarga hingga event perusahaan, kami punya paket untuk Anda"
    - Display category name from `$categoryMeta['name']`
    - Style header with classes matching landing page section headers
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 8.1, 8.3_
  
  - [ ] 7.2 Add package grid section with horizontal scrolling
    - Check if `$packages->isEmpty()`
    - If empty, include empty state component with message "Belum ada paket tersedia untuk kategori ini"
    - If not empty, create `<section class="packages-section">` container
    - Create `<div class="packages-grid horizontal-scroll">` for card container
    - Loop through `$packages` with `@foreach($packages as $package)`
    - For each package, include package card component (inline HTML, not separate component)
    - _Requirements: 2.7, 4.1, 4.8, 5.1, 5.2, 5.3, 5.4, 5.5, 11.1, 11.5, 11.6_
  
  - [ ] 7.3 Create inline package card HTML matching landing page design
    - Create `<article class="package-card">` element
    - Add `<div class="package-image">` container
    - Check if `$package->foto` exists with `@if($package->foto)`
    - If exists, display `<img src="{{ asset($package->foto) }}" alt="{{ $package->nama_paket }}" loading="lazy">`
    - If not exists, display placeholder div with SVG icon and "Tidak ada foto" text
    - Add badge overlay `<span class="package-badge">Fleksibel</span>`
    - Create `<div class="package-info">` container
    - Display package name as `<h3 class="package-name">{{ $package->nama_paket }}</h3>`
    - Display price: if `$package->harga > 0` show formatted price, else show "Hubungi Kami"
    - Add CTA link `<a href="#contact" class="package-cta">Hubungi</a>`
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7, 4.8_

- [ ] 8. Create empty state Blade component
  - [ ] 8.1 Create component file at `resources/views/components/empty-state.blade.php`
    - Accept props: `$message`, `$submessage`, `$ctaText`, `$ctaLink`
    - Create `<div class="empty-state">` container
    - Add SVG icon (image placeholder icon)
    - Display `<h3>{{ $message }}</h3>`
    - Display `<p>{{ $submessage }}</p>`
    - Display `<a href="{{ $ctaLink }}" class="btn btn-primary">{{ $ctaText }}</a>`
    - _Requirements: 11.1, 11.2, 11.3, 11.4, 11.6_

- [ ] 9. Create CSS for horizontal scrolling layout
  - [ ] 9.1 Add horizontal scroll styles to `resources/css/app.css`
    - Style `.packages-grid.horizontal-scroll` with `display: flex`, `gap: 24px`, `overflow-x: auto`
    - Add `scroll-behavior: smooth` for smooth scrolling
    - Style scrollbar with thin appearance using `scrollbar-width: thin` and webkit scrollbar pseudo-elements
    - Set scrollbar colors: track transparent, thumb `rgba(0, 0, 0, 0.2)`
    - Add hover state for thumb: `rgba(0, 0, 0, 0.3)`
    - _Requirements: 5.1, 5.3, 5.4, 5.5_
  
  - [ ] 9.2 Add responsive grid column styles
    - For desktop (min-width: 1024px): Set `.package-card` with `flex: 0 0 calc((100% - 72px) / 4)` (4 cards)
    - For tablet (768px - 1023px): Set `.package-card` with `flex: 0 0 calc((100% - 48px) / 3)` (3 cards)
    - For mobile (max-width: 767px): Set `.package-card` with `flex: 0 0 calc((100% - 24px) / 2)` (2 cards)
    - _Requirements: 5.6, 5.7, 5.8, 9.1, 9.2, 9.3_
  
  - [ ] 9.3 Add package card component styles
    - Style `.package-card` with border, border-radius, overflow hidden, background white
    - Style `.package-image` as aspect-ratio container with relative positioning
    - Style `.package-image img` with object-fit cover and full dimensions
    - Style `.no-image-placeholder` with centered flexbox, gray background, padding
    - Style `.package-badge` with absolute positioning on top-right of image
    - Style `.package-info` with padding and flex layout
    - Style `.package-name` with font-size, font-weight, margin
    - Style `.package-price` with color, font-size
    - Style `.package-cta` button with primary colors, padding, border-radius, hover states
    - Ensure minimum touch target size of 44x44px for buttons
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7, 4.8, 9.4_
  
  - [ ] 9.4 Add empty state component styles
    - Style `.empty-state` with centered text-align, padding, max-width
    - Style `.empty-icon` SVG with width, height, margin, gray color
    - Style `h3` with font-size, font-weight, margin
    - Style `p` with color, font-size, margin
    - Style `.btn-primary` with colors, padding, border-radius, hover effects
    - _Requirements: 11.1, 11.2, 11.3, 11.4_
  
  - [ ] 9.5 Add category page header styles
    - Style `.category-header` with text-align center, padding, margin
    - Style heading with font-size matching landing page sections
    - Style `.category-subtitle` with muted color, font-size, margin
    - Style `.category-name` with larger font-size, bold weight, accent color
    - Add responsive typography for mobile viewports
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 9.1_

- [ ] 10. Update navigation component to highlight active category
  - [ ] 10.1 Modify `resources/views/components/navigation.blade.php` dropdown items
    - Locate "Paket Wisata" dropdown section
    - For each category link (The Waterfall Resto, Private Room, Fishing Lake)
    - Change href to category route: `href="{{ route('packages.category', ['category' => 'the-waterfall-resto']) }}"`
    - Add active state check: `@if(app(App\Services\NavigationService::class)->isCategoryActive('the-waterfall-resto')) class="active" @endif`
    - Repeat for all three categories with their respective slugs
    - _Requirements: 6.1, 6.2, 6.3, 6.4_

- [ ] 11. Add cache invalidation to PaketWisata model
  - [ ] 11.1 Add model event listeners to `app/Models/PaketWisata.php`
    - Add `protected static function booted()` method
    - Add `static::saved()` event listener
    - Inside saved listener, call `Cache::forget("packages.category.{$package->jenis_paket}")`
    - Add `static::deleted()` event listener
    - Inside deleted listener, call `Cache::forget("packages.category.{$package->jenis_paket}")`
    - _Requirements: 10.3_

- [ ] 12. Checkpoint - Test category pages functionality
  - Visit each category URL: /paket/the-waterfall-resto, /paket/private-room, /paket/fishing-lake
  - Verify packages filtered correctly by category
  - Test invalid category URL returns 404
  - Verify empty state displays when no packages exist
  - Test horizontal scroll with mouse and touch
  - Verify responsive layout at different viewport sizes
  - Check navigation dropdown highlights active category
  - Verify SEO metadata in page source
  - Check breadcrumb navigation display and links
  - Ensure all tests pass, ask the user if questions arise

- [ ] 13. Create unit tests for PackageCategoryController
  - [ ] 13.1 Create test file at `tests/Unit/Controllers/PackageCategoryControllerTest.php`
    - Create test class extending `TestCase`
    - Import necessary classes and traits
    - _Requirements: Design Testing Strategy_
  
  - [ ]* 13.2 Write test for slug mapping correctness
    - Test method: `test_mapSlugToJenisPaket_returns_correct_enum_value()`
    - Create controller instance via reflection to access private method
    - Test 'the-waterfall-resto' returns 'The Waterfall Resto'
    - Test 'private-room' returns 'Private Room'
    - Test 'fishing-lake' returns 'Fishing Lake'
    - _Requirements: Design Testing Strategy_
  
  - [ ]* 13.3 Write test for invalid slug handling
    - Test method: `test_mapSlugToJenisPaket_throws_exception_for_invalid_slug()`
    - Expect `InvalidArgumentException` when calling with invalid slug
    - _Requirements: Design Testing Strategy_
  
  - [ ]* 13.4 Write test for category metadata retrieval
    - Test method: `test_getCategoryMetadata_returns_correct_data()`
    - Create controller instance via reflection
    - Test each category slug returns array with name, description, default_image keys
    - _Requirements: Design Testing Strategy_

- [ ] 14. Create feature tests for category page functionality
  - [ ] 14.1 Create test file at `tests/Feature/PackageCategoryPageTest.php`
    - Create test class extending `TestCase`
    - Use `RefreshDatabase` trait for test isolation
    - _Requirements: Design Testing Strategy_
  
  - [ ]* 14.2 Write test for filtered package display
    - Test method: `test_category_page_displays_filtered_packages()`
    - Create packages with factory for different categories
    - Visit '/paket/the-waterfall-resto'
    - Assert status 200
    - Assert only The Waterfall Resto packages visible
    - Assert other category packages not visible
    - _Requirements: Design Testing Strategy, 2.1, 2.2, 2.5_
  
  - [ ]* 14.3 Write test for invalid category 404 error
    - Test method: `test_category_page_returns_404_for_invalid_slug()`
    - Visit '/paket/invalid-category'
    - Assert status 404
    - _Requirements: Design Testing Strategy, 1.5, 1.6_
  
  - [ ]* 14.4 Write test for empty state display
    - Test method: `test_category_page_displays_empty_state_when_no_packages()`
    - Visit '/paket/fishing-lake' with no packages in database
    - Assert status 200
    - Assert see text 'Belum ada paket tersedia'
    - Assert see text 'Hubungi Kami'
    - _Requirements: Design Testing Strategy, 2.7, 11.1, 11.2, 11.3, 11.4_
  
  - [ ]* 14.5 Write test for breadcrumb display
    - Test method: `test_category_page_includes_breadcrumbs()`
    - Visit '/paket/private-room'
    - Assert see text 'Home'
    - Assert see text 'Paket Wisata'
    - Assert see text 'Paket Private Room'
    - _Requirements: Design Testing Strategy, 8.1, 8.3_
  
  - [ ]* 14.6 Write test for SEO metadata
    - Test method: `test_category_page_includes_seo_metadata()`
    - Visit '/paket/the-waterfall-resto'
    - Assert see '<title>Paket The Waterfall Resto - Paket Wisata | Godong Ijo</title>' in HTML
    - Assert see '<meta property="og:title"' in HTML
    - Assert see '<meta name="description"' in HTML
    - _Requirements: Design Testing Strategy, 7.1, 7.2, 7.3_
  
  - [ ]* 14.7 Write test for inactive packages exclusion
    - Test method: `test_inactive_packages_not_displayed()`
    - Create package with `is_active => false`
    - Visit category page
    - Assert don't see inactive package name
    - _Requirements: Design Testing Strategy, 2.5_
  
  - [ ]* 14.8 Write test for package ordering
    - Test method: `test_packages_ordered_by_created_at_desc()`
    - Create older and newer packages
    - Visit category page
    - Parse HTML content
    - Assert newer package appears before older package
    - _Requirements: Design Testing Strategy, 2.6_

- [ ] 15. Final checkpoint - Complete system verification
  - Run full test suite: `php artisan test`
  - Verify all tests pass
  - Test all three category pages in browser
  - Verify responsive design on mobile, tablet, desktop
  - Check accessibility with keyboard navigation
  - Verify image lazy loading works
  - Test cache functionality with repeated page loads
  - Ensure all tests pass, ask the user if questions arise

## Notes

- Tasks marked with `*` are optional test tasks and can be skipped for faster MVP delivery
- Each task references specific requirements for traceability
- Implementation uses existing Laravel patterns and service architecture
- Package card design reuses landing page styles for consistency
- Horizontal scrolling CSS uses flexbox with smooth scroll behavior
- Database query caching improves performance with 5-minute TTL
- Route constraint validation prevents invalid category access
- Empty state handling ensures good UX when no packages exist
- Navigation integration provides clear active state indication
- SEO and breadcrumb services extend cleanly with new methods

## Task Dependency Graph

```json
{
  "waves": [
    {
      "id": 0,
      "tasks": ["1", "2.1", "6"]
    },
    {
      "id": 1,
      "tasks": ["2.3", "2.4", "3.1", "4.1", "5.1"]
    },
    {
      "id": 2,
      "tasks": ["2.2", "7.1", "8.1", "11.1", "13.1"]
    },
    {
      "id": 3,
      "tasks": ["7.2", "7.3", "9.1", "9.4", "9.5", "14.1"]
    },
    {
      "id": 4,
      "tasks": ["9.2", "9.3", "10.1", "13.2", "13.3", "13.4", "14.2", "14.3", "14.4"]
    },
    {
      "id": 5,
      "tasks": ["14.5", "14.6", "14.7", "14.8"]
    }
  ]
}
```
