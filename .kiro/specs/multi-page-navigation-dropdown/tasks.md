# Implementation Plan: Multi-Page Navigation Dropdown System

## Overview

This implementation plan converts the multi-page navigation dropdown design into actionable coding tasks. The system transforms the existing single-page website into a multi-page structure with dropdown menus for destinations and tour packages, breadcrumb navigation, SEO optimization, and booking integration across all pages.

## Tasks

- [x] 1. Create infrastructure and service layer
  - [x] 1.1 Create navigation configuration file
    - Create `config/navigation.php` with main menu structure and CTA configuration
    - Define navigation items: Beranda, Destinasi (with 3 children), Paket Wisata (dynamic), Wisata Edukasi, Tentang Kami, Kontak
    - _Requirements: 1.1, 2.1, 11.2, 11.5, 18.1_
  
  - [x] 1.2 Create NavigationService class
    - Create `app/Services/NavigationService.php` with methods: getMainNavigation(), getCTA(), isActive(), getActiveParent(), loadDynamicPackages()
    - Implement logic to load navigation from config and merge dynamic packages from database
    - Implement active state detection based on current route and slug
    - _Requirements: 1.7, 8.1, 8.2, 8.3, 11.3, 18.4_
  
  - [x] 1.3 Create BreadcrumbService class
    - Create `app/Services/BreadcrumbService.php` with methods: generate(), getStructuredData()
    - Implement breadcrumb generation for all page types (destinations, packages, static pages)
    - Generate BreadcrumbList JSON-LD structured data
    - _Requirements: 3.7, 4.8, 5.5, 16.1, 16.2, 16.3, 16.5_
  
  - [x] 1.4 Create SEOService class
    - Create `app/Services/SEOService.php` with methods: generateMetadata(), getTitle(), getDescription(), getStructuredData(), getCanonicalUrl()
    - Implement page title format: "[Page Name] | Godong Ijo"
    - Implement meta description generation (150-160 characters)
    - Generate structured data for TouristAttraction, Product, Organization, BreadcrumbList schemas
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 7.6, 7.7, 7.8_

- [x] 2. Update navigation component with dropdown support
  - [x] 2.1 Update navigation Blade component
    - Update `resources/views/components/navigation.blade.php` to support nested menu structures (2 levels)
    - Add desktop dropdown rendering with hover behavior
    - Add mobile accordion rendering with click/tap behavior
    - Add active state classes for current page and parent items
    - Add ARIA attributes: role, aria-label, aria-haspopup, aria-expanded, aria-current
    - Integrate Alpine.js x-data directive for dropdown controller
    - _Requirements: 1.1, 1.2, 1.3, 1.5, 1.6, 1.7, 2.1, 2.2, 2.3, 2.4, 2.5, 11.1, 11.3, 11.4, 15.1, 15.2, 15.4_
  
  - [x] 2.2 Create Alpine.js navigation dropdown controller
    - Create `resources/js/modules/navigation-dropdown.js` with navigationDropdown() function
    - Implement desktop dropdown: openDropdown(), closeDropdown(), closeWithDelay(300ms)
    - Implement mobile accordion: toggleMobile(), toggleAccordion()
    - Implement keyboard navigation: handleKeydown() for Tab, Enter, Arrow keys, Escape
    - Implement focus trapping: trapFocus()
    - Implement body scroll prevention when mobile menu is open
    - _Requirements: 1.2, 1.3, 1.5, 1.6, 2.3, 2.4, 2.5, 2.7, 2.8, 1.9, 15.5, 15.6_
  
  - [x] 2.3 Add navigation dropdown styles
    - Add CSS for desktop dropdown menus with hover transitions
    - Add CSS for mobile accordion with expand/collapse animations
    - Add CSS for active state indicators
    - Add CSS for focus indicators (WCAG AA contrast ratio)
    - Ensure responsive behavior: mobile menu < 768px, desktop menu >= 768px
    - _Requirements: 1.1, 1.2, 1.3, 1.5, 1.6, 1.7, 2.1, 2.2, 2.3, 8.6, 13.4_

- [x] 3. Create breadcrumb component
  - [x] 3.1 Create breadcrumb Blade component
    - Create `resources/views/components/breadcrumb.blade.php` accepting items array
    - Render breadcrumb items with "/" separators
    - Make parent levels clickable links, current page plain text
    - Add ARIA attributes: aria-label="Breadcrumb", aria-current="page"
    - Add BreadcrumbList structured data markup
    - _Requirements: 16.1, 16.2, 16.3, 16.4, 16.5, 16.7, 16.8_
  
  - [x] 3.2 Add breadcrumb styles
    - Add CSS for breadcrumb navigation below navbar
    - Ensure responsive wrapping on narrow viewports
    - Style current page item distinctly from parent links
    - _Requirements: 16.1, 16.6_

- [x] 4. Create page layout template
  - [x] 4.1 Create master page layout
    - Create `resources/views/layouts/page.blade.php` extending app.blade.php structure
    - Include skip navigation link
    - Include navigation component with service data
    - Include breadcrumb component (conditional)
    - Include main content section with @yield('content')
    - Include footer component
    - Include SEO meta tags partial
    - _Requirements: 3.8, 4.8, 5.5, 8.1, 15.8_
  
  - [x] 4.2 Create SEO meta tags partial
    - Create `resources/views/partials/seo-meta.blade.php` accepting seoData array
    - Render page title, meta description, meta keywords
    - Render Open Graph tags (og:title, og:description, og:image, og:url, og:type)
    - Render Twitter Card tags (twitter:card, twitter:title, twitter:description, twitter:image)
    - Render canonical URL link tag
    - Render JSON-LD structured data script tag
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 7.6_

- [ ] 5. Checkpoint - Verify infrastructure and components
  - Ensure all services, components, and layouts are created correctly
  - Test navigation config loads without errors
  - Ask user if questions arise

- [ ] 6. Implement destination pages
  - [x] 6.1 Create DestinationController
    - Create `app/Http/Controllers/DestinationController.php` with show() method
    - Implement getDestinationData() for slug-based destination lookup
    - Implement getDestinationGallery() returning minimum 4 images
    - Implement getDestinationFacilities() with hours, admission, amenities, contact
    - Integrate NavigationService, BreadcrumbService, SEOService
    - Handle invalid slugs with 404 page
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8, 14.1, 14.2, 14.3, 14.7_
  
  - [x] 6.2 Create destination routes
    - Add route in `routes/web.php`: GET /destinasi/{slug} → DestinationController@show
    - Use route constraint: where('slug', 'the-waterfall|monster-fish|vertical-garden')
    - Name route: 'destination.show'
    - _Requirements: 3.1_
  
  - [x] 6.3 Create destination page template
    - Create `resources/views/pages/destination.blade.php` extending layouts/page.blade.php
    - Add hero section with destination name, tagline, featured image
    - Add content section with description
    - Add image gallery section (minimum 4 photos)
    - Add facilities section with operating hours, admission, amenities
    - Add booking CTA button linking to booking modal
    - Use responsive image tags with srcset, sizes, lazy loading
    - _Requirements: 3.2, 3.3, 3.4, 3.5, 3.6, 10.1, 10.2, 10.3, 10.4, 10.7_
  
  - [ ] 6.4 Extract and populate The Waterfall content
    - Extract content from https://thewaterfallresto.com/
    - Populate destination data: name, tagline, description, hero image, gallery (4+ images)
    - Populate facilities: operating hours, admission fees, amenities list, contact info
    - Adapt content tone to match eco-luxury brand positioning
    - _Requirements: 6.1, 6.2, 6.3, 6.6_
  
  - [ ] 6.5 Extract and populate Monster Fish content
    - Extract content from https://monsterfishfishinglake.com/
    - Populate destination data: name, tagline, description, hero image, gallery (4+ images)
    - Populate facilities: operating hours, admission fees, amenities list, contact info
    - Emphasize sport fishing experience and unique species
    - _Requirements: 6.1, 6.2, 6.3, 6.6_
  
  - [ ] 6.6 Create Vertical Garden placeholder content
    - Create placeholder content with TODO markers (URL not provided in requirements)
    - Populate basic destination structure with placeholder text and images
    - Ensure page structure matches other destinations
    - _Requirements: 6.1, 6.2, 6.5_

- [ ] 7. Implement package pages
  - [x] 7.1 Create PackageController
    - Create `app/Http/Controllers/PackageController.php` with show() method
    - Implement getPackageBySlug() using database lookup with slug generation
    - Implement getPackageStaticData() returning duration, features, itinerary, included/excluded lists, gallery
    - Implement formatPackageForView() merging database and static data
    - Integrate NavigationService, BreadcrumbService, SEOService
    - Handle missing packages gracefully with "content not found" message
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.9, 14.1, 14.3, 14.4, 14.5, 14.7_
  
  - [x] 7.2 Create package routes
    - Add route in `routes/web.php`: GET /paket/{slug} → PackageController@show
    - Name route: 'package.show'
    - _Requirements: 4.1_
  
  - [x] 7.3 Create package page template
    - Create `resources/views/pages/package.blade.php` extending layouts/page.blade.php
    - Add hero section with package name, tagline, featured image
    - Add package highlights section (bullet points or cards)
    - Add pricing section with base price, group size, duration
    - Add itinerary section with activity details
    - Add inclusion/exclusion lists
    - Add booking CTA button with package ID for pre-selection
    - Use responsive image tags with srcset, sizes, lazy loading
    - _Requirements: 4.2, 4.3, 4.4, 4.5, 4.6, 4.7, 4.8, 10.1, 10.2, 10.3, 10.4, 10.7_
  
  - [ ] 7.4 Create static package data configuration
    - Create configuration mapping package names to static data (duration, features, itinerary, included, excluded, gallery images, badges)
    - Map all 4 packages: Rekreasi Keluarga, Sport Fishing, Private Event, Kuliner Keluarga
    - Store in controller method or separate config file
    - _Requirements: 4.3, 4.4, 4.5, 4.6_
  
  - [ ] 7.5 Implement package slug generation
    - Add slug generation method converting package name to kebab-case
    - Ensure slugs match navigation URLs: rekreasi-keluarga, sport-fishing, private-event, kuliner-keluarga
    - _Requirements: 4.1_

- [ ] 8. Implement static pages
  - [x] 8.1 Create StaticPageController
    - Create `app/Http/Controllers/StaticPageController.php` with methods: education(), about(), contact()
    - Implement page data methods for each static page
    - Integrate NavigationService, BreadcrumbService, SEOService
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 14.1, 14.3, 14.7_
  
  - [x] 8.2 Create static page routes
    - Add routes in `routes/web.php`: GET /wisata-edukasi, GET /tentang-kami, GET /kontak
    - Name routes: 'education', 'about', 'contact'
    - _Requirements: 5.1_
  
  - [x] 8.3 Create Wisata Edukasi page template
    - Create `resources/views/pages/education.blade.php` extending layouts/page.blade.php
    - Add hero section, program description, target audience, educational benefits
    - Add booking CTA button
    - _Requirements: 5.2_
  
  - [x] 8.4 Create Tentang Kami page template
    - Create `resources/views/pages/about.blade.php` extending layouts/page.blade.php
    - Add hero section, company history, vision and mission, team information, awards/certifications
    - _Requirements: 5.3_
  
  - [x] 8.5 Create Kontak page template
    - Create `resources/views/pages/contact.blade.php` extending layouts/page.blade.php
    - Add hero section, contact form, location map embed, operating hours, phone, email, social media links
    - _Requirements: 5.4_
  
  - [ ] 8.6 Populate static page content
    - Write or adapt content for all 3 static pages
    - Ensure consistent typography and spacing standards
    - _Requirements: 5.2, 5.3, 5.4, 5.6_

- [ ] 9. Checkpoint - Verify all pages render correctly
  - Test each destination page loads with correct content and structure
  - Test each package page loads with database data merged with static config
  - Test each static page loads with appropriate content
  - Verify breadcrumbs display on all sub-pages
  - Verify SEO metadata present on all pages
  - Ask user if questions arise

- [ ] 10. Implement smooth scrolling and anchor navigation
  - [ ] 10.1 Add smooth scroll JavaScript
    - Add smooth scroll behavior to `resources/js/app.js` for anchor link clicks
    - Implement 500ms scroll duration with ease-in-out easing
    - Account for fixed navbar offset (80px) in final scroll position
    - Support cross-page navigation with hash fragments
    - Disable smooth scroll for users with prefers-reduced-motion preference
    - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5, 9.6, 9.7_
  
  - [ ] 10.2 Add back-to-top button
    - Add floating back-to-top button appearing after user scrolls down
    - Implement smooth scroll to top on button click
    - _Requirements: 9.5_

- [ ] 11. Implement booking integration from navigation
  - [ ] 11.1 Update CTA button booking context
    - Update navigation CTA button to open booking modal with correct context
    - On package pages: pre-select current package in booking modal
    - On destination pages: open booking modal with no pre-selection
    - Ensure booking modal prevents body scrolling when open
    - Support closing modal with ESC key or outside click
    - _Requirements: 12.1, 12.2, 12.3, 12.4, 12.5, 12.6, 12.7_
  
  - [ ] 11.2 Update booking modal z-index
    - Ensure booking modal displays above fixed navbar
    - _Requirements: 12.5_
  
  - [ ] 11.3 Test booking state persistence
    - Verify booking state persists when navigating between pages
    - _Requirements: 12.8_

- [ ] 12. Implement responsive images and performance optimization
  - [ ] 12.1 Add responsive image handling
    - Update all image tags to include srcset and sizes attributes
    - Define breakpoints: small (<768px), medium (768-1024px), large (>1024px)
    - Implement WebP format with JPEG fallback using picture element
    - Add loading="lazy" to below-the-fold images
    - Add loading="eager" or preload to hero section images
    - Ensure all images have descriptive alt text
    - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.6, 10.7_
  
  - [ ] 12.2 Add image fallback handling
    - Implement onerror handler to display fallback placeholder when images fail to load
    - _Requirements: 10.5_
  
  - [ ] 12.3 Optimize asset loading
    - Configure Vite to minify and bundle CSS/JS
    - Add cache headers for static assets
    - Implement CSS-only animations where possible
    - _Requirements: 17.5, 17.6, 17.7_

- [ ] 13. Implement sitemap generation
  - [ ] 13.1 Create sitemap route and controller
    - Add route: GET /sitemap.xml → SitemapController@index
    - Create `app/Http/Controllers/SitemapController.php`
    - Generate XML sitemap including all public pages (home, 3 destinations, 4 packages, 3 static pages)
    - Include URL, last modified date, change frequency, priority for each page
    - _Requirements: 7.8_

- [ ] 14. Implement 404 error page
  - [ ] 14.1 Create custom 404 error page
    - Create `resources/views/errors/404.blade.php` extending layouts/page.blade.php
    - Add error message with navigation links back to home
    - Include navigation component for easy recovery
    - _Requirements: 14.2_

- [ ] 15. Update homepage to use new navigation
  - [ ] 15.1 Update LandingPageController to use NavigationService
    - Update `app/Http/Controllers/LandingPageController.php` to inject NavigationService
    - Pass navigation data with dynamic packages to view
    - Integrate SEOService for homepage metadata
    - _Requirements: 1.1, 18.4_
  
  - [ ] 15.2 Update homepage route name
    - Ensure homepage route is named 'home' or update references in navigation config
    - _Requirements: 1.1_

- [ ] 16. Final checkpoint - End-to-end testing
  - Test desktop navigation with dropdown menus (hover behavior)
  - Test mobile navigation with accordion (click/tap behavior)
  - Test keyboard navigation (Tab, Enter, Arrow keys, Escape)
  - Test active state indication on all pages
  - Test breadcrumb navigation and links
  - Test smooth scrolling for anchor links
  - Test booking modal opens with correct context from all pages
  - Test responsive behavior at various viewport widths (320px to 2560px)
  - Test all routes return 200 status for valid URLs and 404 for invalid
  - Verify performance targets (desktop Lighthouse >= 85, mobile >= 75)
  - Verify accessibility (WCAG AA contrast ratios, ARIA attributes, keyboard navigation)
  - Ask user if questions arise and if ready to deploy

## Notes

- All tasks build incrementally from infrastructure (services, components) to pages (destinations, packages, static) to enhancements (smooth scroll, responsive images, SEO)
- Navigation configuration is centralized in `config/navigation.php` for easy maintenance
- Services (NavigationService, BreadcrumbService, SEOService) provide reusable logic across all pages
- Blade components (navigation, breadcrumb) ensure UI consistency
- Package data dynamically loads from database and merges with static configuration
- Content extraction from existing Godong Ijo websites (The Waterfall, Monster Fish) preserves SEO value and brand consistency
- Responsive images and lazy loading optimize performance across devices
- ARIA attributes and keyboard navigation ensure accessibility compliance
- Smooth scrolling and booking context awareness enhance user experience
- Checkpoints ensure incremental validation before proceeding to next phase

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1.1", "1.2", "1.3", "1.4"] },
    { "id": 1, "tasks": ["2.1", "3.1", "4.1", "4.2"] },
    { "id": 2, "tasks": ["2.2", "2.3", "3.2"] },
    { "id": 3, "tasks": ["6.1", "7.1", "8.1"] },
    { "id": 4, "tasks": ["6.2", "7.2", "8.2"] },
    { "id": 5, "tasks": ["6.3", "7.3", "8.3", "8.4", "8.5"] },
    { "id": 6, "tasks": ["6.4", "6.5", "6.6", "7.4", "7.5", "8.6"] },
    { "id": 7, "tasks": ["10.1", "10.2", "11.1", "11.2", "12.1", "12.2"] },
    { "id": 8, "tasks": ["11.3", "12.3", "13.1", "14.1"] },
    { "id": 9, "tasks": ["15.1", "15.2"] }
  ]
}
```
