# Implementation Plan: Education Tourism Page Redesign (Wisata Edukasi - Ecotainment Godongijo)

## Overview

This implementation plan covers the complete redesign of the Wisata Edukasi (Educational Tourism) page for Godong Ijo with comprehensive visual adjustments. The redesign includes a new color palette, enhanced hero section, platform fieldtrip icons update, program education with progressive disclosure for the Art category, testimonial redesign, new "Dipercaya oleh" (school partners) section, and footer CTA bar.

The implementation uses Laravel/Blade templates, CSS with custom properties, Alpine.js for interactivity, and follows mobile-first responsive design patterns.

## Tasks

- [ ] 1. Set up project structure and color tokens
  - Create directory `resources/views/components/education/` for new Blade components
  - Create directory `public/images/education/` for visual assets
  - Update CSS custom properties in `resources/css/app.css` with new color palette
  - Add Tabler Icons CDN or install package for icon components
  - _Requirements: 12.1, 12.2, 12.3_

  - [x] 1.1 Update CSS color tokens with new palette
    - Replace existing color tokens with new values: `--forest: #1E4636`, `--forest-d: #0F2A1F`, `--gold: #C98A3E`, `--cream: #FAF6ED`, `--line: #EAE4D6`, `--ink: #22301E`
    - Update card styling to use 1px border with `--line` color and remove box-shadow
    - Set H1 hero to use serif font (`font-voice` / Fraunces), rest sans-serif
    - Update card border-radius to 12px
    - _Requirements: 12.1, 12.3, 12.6_

- [x] 2. Create database migration and seed for school partners
  - [x] 2.1 Create migration for `school_partners` table
    - Create migration file with columns: `id`, `name`, `level` (TK/SD/SMA), `logo_path` (nullable), `is_active`, `order`, `timestamps`
    - Add index on `is_active` and `order` for efficient querying
    - _Requirements: Custom (new section requirement)_

  - [x] 2.2 Create seeder for sample school partners data
    - Create `SchoolPartnerSeeder.php` with at least 20 sample school entries
    - Include mix of TK, SD, SMP, SMA levels
    - Set `logo_path` to null for initial state (will use monogram fallback)
    - Set `order` field for display sequence
    - _Requirements: Custom (new section requirement)_

  - [x] 2.3 Create `SchoolPartner` Eloquent model
    - Define fillable fields
    - Add scope for active partners: `scopeActive($query)`
    - Add scope for ordered display: `scopeOrdered($query)`
    - Add accessor for generating monogram from name
    - _Requirements: Custom (new section requirement)_


- [x] 3. Update StaticPageController education method with new data structure
  - [x] 3.1 Update `getEducationData()` method with enhanced hero data
    - Add location badge data: "Depok, Jawa Barat — buka setiap hari"
    - Add two buttons data: "About us" (outline) and "Reservasi" (WhatsApp link)
    - Add statistics strip data: years_operating (20+), total_programs (dynamic count), partner_schools (count from SchoolPartner)
    - _Requirements: 19.1, Custom (hero enhancements)_

  - [x] 3.2 Update Platform Fieldtrip data with Tabler icon mappings
    - Replace emoji icons with Tabler icon identifiers: `ti-device-laptop`, `ti-bus`, `ti-trees`
    - Keep existing title and description data
    - Add squared badge style configuration (rounded 8px, cream background)
    - _Requirements: 19.2, Custom (platform icons)_

  - [x] 3.3 Split Art programs data into `featured` and `others` arrays
    - Create `featured` array with 3 programs: Learning Batik, Junior MasterChef, Traditional Dance
    - Create `others` array with remaining 11 programs (title only, no long whatsappMessage)
    - Add metadata flag `featured: true` for data structure clarity
    - Count total programs dynamically for statistics strip
    - _Requirements: 19.5, Custom (program curation)_

  - [x] 3.4 Add school partners data query
    - Query `SchoolPartner::active()->ordered()->limit(7)->get()` for first 7 schools
    - Count remaining schools: `SchoolPartner::active()->count() - 7`
    - Pass data to view as `schoolPartners` and `remainingSchoolsCount`
    - _Requirements: Custom (school partners section)_

  - [x] 3.5 Update testimonial data structure for dark card design
    - Keep existing photo and quote data
    - Add design metadata: background color `--forest`, quote icon color `--gold`, source color `--gold`
    - Update quote text to use italic serif font
    - _Requirements: 19.7, Custom (testimonial redesign)_

  - [x] 3.6 Add footer CTA bar data
    - Add data: text "Siap merencanakan kunjungan sekolah?", button "Hubungi via WhatsApp", WhatsApp message
    - Configure background color `--forest-d`, button color `--gold`
    - _Requirements: Custom (footer CTA bar)_


- [ ] 4. Create enhanced hero section component with split layout
  - [ ] 4.1 Create `components/education/hero-ecotainment.blade.php` with 2-column split layout
    - Implement CSS Grid 2-column layout: ~55% left (text), ~45% right (image), gap 48px
    - **Left column** (background `--forest-d`, large padding):
      - Location badge: pill, white 10% opacity background, rounded-full, map-pin icon, white text 13px — "Depok, Jawa Barat — buka setiap hari"
      - H1 title: "Wisata Edukasi Ecotainment Godongijo", serif font (Fraunces), white color, large size (32-36px)
      - Description paragraph: white 75% opacity, max-width constrained
      - Two buttons side-by-side: "About us" (outline, transparent border, white text) and "Reservasi" (solid gold `--gold`, WhatsApp icon, dark text)
    - **Right column** (photo):
      - Educational activity photo (children learning/fieldtrip)
      - Fill full column height, `object-fit: cover`
      - Use `<picture>` with WebP source + JPEG fallback
      - `loading="eager"` (above fold)
      - Rounded corners only on outer edges (top-right, bottom-right)
      - Fallback placeholder + descriptive alt text on error
    - Minimum section height: 50vh
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, Custom (split layout hero)_

  - [ ] 4.2 Add statistics strip below hero (full-width, outside grid)
    - Create 3-column layout with thin `--line` border-top
    - Display: "20+ tahun beroperasi", "Total {count} program" (dynamic), "{count} sekolah mitra" (dynamic)
    - Style with subtle separator lines between columns
    - Responsive: stack vertically on mobile
    - _Requirements: Custom (statistics strip)_

  - [ ] 4.3 Add hero section CSS styling for split layout with new color palette
    - Desktop (≥1024px): CSS Grid 2 columns (~55% / 45%), gap 48px, min-height 50vh
    - Left column: `--forest-d` background, large padding (60-80px)
    - Location badge: white 10% transparent background, rounded-full, inline-flex with icon
    - H1: serif font (Fraunces), white color, 32-36px font size
    - Description: white 75% opacity, max-width 500px
    - Buttons: inline-flex gap, "About us" outline white border, "Reservasi" solid `--gold` background
    - Button hover effects: smooth transitions
    - Right column photo: full height, `object-fit: cover`, rounded corners right side (12px)
    - Statistics strip: full-width below hero, `--line` border-top, 3-column grid, padding
    - **Mobile (<768px)**: Stack vertically (text column first with reduced padding, photo below with max-height 240-260px), statistics strip 1 column
    - **Tablet (768-1024px)**: Adjust proportions or stack as appropriate
    - NO box-shadow on hero section
    - Ensure white text contrast ratio ≥4.5:1 on `--forest-d` background
    - _Requirements: 1.6, 1.7, 12.1, 12.2, 12.4, 15.6, Custom (split layout styling)_


- [ ] 5. Update Platform Fieldtrip section with Tabler icons
  - [x] 5.1 Create `components/education/icon-badge.blade.php` component
    - Accept props: `iconName` (Tabler icon class), `backgroundColor`, `borderRadius`
    - Render Tabler icon with squared badge (rounded 8px, cream background)
    - Support responsive sizing
    - _Requirements: 2.2, Custom (platform icons)_

  - [ ] 5.2 Update Platform Fieldtrip cards to use icon badges
    - Replace emoji icons with Tabler icon badges: `ti-device-laptop`, `ti-bus`, `ti-trees`
    - Maintain existing card structure (icon, title, description, WhatsApp button)
    - Update card styling to use new color palette and 1px border
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, Custom_

- [ ] 6. Implement program education with progressive disclosure
  - [ ] 6.1 Create `components/education/program-group.blade.php` reusable component
    - Accept props: `title`, `featured[]` (array of 3 featured programs), `others[]` (array of remaining programs)
    - Render featured programs as full cards with photo/icon header
    - Render others inside native `<details><summary>` element
    - Summary label: "Lihat {n} program lainnya" with chevron icon
    - Display others as small chips/pills that wrap automatically
    - _Requirements: Custom (program curation with progressive disclosure)_

  - [ ] 6.2 Update Edukasi Lingkungan section (3 programs - no disclosure needed)
    - Display all 3 programs as normal cards: Fast Learning Camp, Learning About Animals, Urban Farming
    - Use standard card layout with image, title, description, WhatsApp button
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7_

  - [ ] 6.3 Update Science section (2 programs - no disclosure needed)
    - Display both programs as normal cards: Aero Glider, Professor Cilik
    - Use standard card layout
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_

  - [ ] 6.4 Update Art section (14 programs - WITH progressive disclosure)
    - Display 3 featured programs as full cards: Learning Batik, Junior MasterChef, Traditional Dance
    - Display remaining 11 programs inside `<details>` as chips/pills
    - Use `program-group` component for consistent pattern
    - Style chips: small text, border, rounded, hover effects
    - _Requirements: 5.1, 5.5, 5.6, 5.7, Custom_

  - [ ] 6.5 Add CSS styling for program group and disclosure UI
    - Style `<details>` and `<summary>` elements with chevron rotation animation
    - Style program chips: border, padding, hover effects, wrap layout
    - Ensure accessibility: focus states, keyboard navigation
    - _Requirements: 15.2, 15.3, Custom_


- [ ] 7. Create card grid and carousel components
  - [x] 7.1 Create `components/education/card-grid.blade.php`
    - Accept props: `cards[]`, `columns` (default 3), `sectionId`
    - Display cards in CSS Grid layout for desktop
    - Support 3-column and 2-column layouts
    - Hide on mobile (< 768px)
    - _Requirements: 7.1, 7.2, 7.3_

  - [ ] 7.2 Create `components/education/card-carousel.blade.php`
    - Accept props: `cards[]`, `carouselId`
    - Implement swipeable carousel using Alpine.js
    - Add navigation arrow buttons (previous/next)
    - Add pagination dots with active state
    - Support touch swipe gestures
    - Show only on mobile (< 768px)
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 6.6, 6.7, 6.8_

  - [ ] 7.3 Add Alpine.js carousel component logic
    - Implement state management: `currentIndex`, `totalSlides`
    - Implement navigation: `nextSlide()`, `prevSlide()`, `goToSlide(index)`
    - Implement touch handlers: `handleTouchStart()`, `handleTouchMove()`, `handleTouchEnd()`
    - Add swipe threshold detection (50px)
    - Disable buttons at boundaries
    - _Requirements: 18.1, 18.2, 18.3, 18.4, 18.5, 18.6, 18.7, 18.8_

  - [ ] 7.4 Add carousel and grid CSS styling with new color palette
    - Style carousel track with smooth transitions
    - Style navigation buttons: circular, cream background, forest color
    - Style pagination dots with active state
    - Style grid with 3-column layout and responsive breakpoints
    - Use new card border styling: 1px solid `--line`, no box-shadow
    - _Requirements: 7.4, 7.5, 12.4, 12.5, Custom_


- [ ] 8. Create program card component with updated styling
  - [ ] 8.1 Create `components/education/program-card.blade.php`
    - Accept props: `card[]` (with id, icon, image, title, description, whatsappMessage), `sectionId`
    - Render card media: image OR icon OR icon badge on image
    - Render card content: title, description
    - Render card action: WhatsApp button
    - Use new border styling: 1px solid `--line`, border-radius 12px
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 3.1, 3.2, 3.3, 3.4, Custom_

  - [ ] 8.2 Add card CSS styling with hover effects
    - Style card with new colors: cream border, no box-shadow
    - Add hover lift animation (translateY -4px)
    - Add image zoom effect on hover (scale 1.05)
    - Style icon wrapper with gradient background
    - Style icon badge with positioned overlay on image
    - _Requirements: 17.1, 17.2, 17.3, 12.4, Custom_

- [ ] 9. Create program detail alternating layout component
  - [ ] 9.1 Create `components/education/program-detail-alternating.blade.php`
    - Accept props: `programs[]`, `sectionTitle`
    - Iterate through programs with alternating image-left and image-right layout
    - Display image with 4:3 aspect ratio
    - Display title and description
    - Use new border styling for images
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7, 4.8, Custom_

  - [ ] 9.2 Add alternating layout CSS styling
    - Use CSS Grid for desktop layout (2 columns)
    - Alternate grid areas: "image content" and "content image"
    - Stack vertically on mobile
    - Style images with rounded corners and new border
    - _Requirements: 4.8, 8.5, Custom_


- [ ] 10. Create redesigned testimonial section component
  - [ ] 10.1 Create `components/education/testimonial-section.blade.php` with dark card design
    - Accept props: `photo`, `photoFallback`, `photoAlt`, `quote`, `source`
    - Change layout from horizontal to single dark card design
    - Set background to `--forest`, border-radius 14px
    - Add gold quote icon on top of card
    - Display quote text in italic using serif font
    - Display source in gold color below quote
    - _Requirements: 11.1, 11.2, 11.3, 11.4, Custom (testimonial redesign)_

  - [ ] 10.2 Add testimonial CSS styling with new dark card design
    - Style dark card: forest background, 14px radius, padding
    - Position gold quote icon at top
    - Style quote text: italic, serif font, white color
    - Style source: gold color, normal weight
    - Remove horizontal photo-quote layout
    - Ensure responsive on mobile
    - _Requirements: 11.5, 11.6, 12.2, Custom_

- [ ] 11. Create school partners section ("Dipercaya oleh")
  - [ ] 11.1 Create `components/education/school-partner-card.blade.php`
    - Accept props: `partner` (with name, level, logo_path)
    - Display monogram avatar fallback (2-letter initials, forest background, white text, rounded 10px)
    - If `logo_path` exists, render `<img>` replacing monogram
    - Display school name below avatar (max 2 lines, font 10-11px)
    - Display level tag in gold below name (optional, e.g., "TK, SD, SMA")
    - _Requirements: Custom (school partners section)_

  - [ ] 11.2 Create school partners section in education page
    - Display section title "Dipercaya oleh" or "Mitra Sekolah Kami"
    - Display 4-column grid on desktop (2 columns on mobile)
    - Render first 7 school partner cards
    - Last card: dashed border, "+{remaining} sekolah lainnya" text, link to `/wisata-edukasi/sekolah-mitra`
    - Add hover effects on cards
    - _Requirements: Custom (school partners section)_

  - [ ] 11.3 Add school partners CSS styling
    - Style 4-column grid (responsive to 2-column on mobile)
    - Style partner cards: padding, hover lift effect
    - Style monogram avatar: forest background, white text, rounded 10px
    - Style school name: max 2 lines, truncate with ellipsis
    - Style level tag: gold color, small font
    - Style "+N sekolah lainnya" card: dashed border, center text
    - _Requirements: Custom (school partners section)_


- [ ] 12. Create footer CTA bar component
  - [ ] 12.1 Create `components/education/footer-cta-bar.blade.php`
    - Accept props: `text`, `buttonLabel`, `whatsappMessage`
    - Display thin full-width bar at bottom of section
    - Set background color `--forest-d`
    - Display text on left: "Siap merencanakan kunjungan sekolah?"
    - Display solid gold WhatsApp button on right
    - Include WhatsApp icon on button
    - _Requirements: Custom (footer CTA bar)_

  - [ ] 12.2 Add footer CTA bar CSS styling
    - Style bar: full-width, `--forest-d` background, padding
    - Layout: flexbox with space-between alignment
    - Style text: white color, left-aligned
    - Style button: solid gold background, rounded, hover effects
    - Responsive: stack vertically on mobile
    - Place at end of content before `<x-footer>`
    - _Requirements: Custom (footer CTA bar)_

- [ ] 13. Create WhatsApp button component with updated styling
  - [x] 13.1 Create `components/education/whatsapp-button.blade.php`
    - Accept props: `message`, `phoneNumber`, `label`, `variant` (primary/secondary)
    - Generate WhatsApp URL with encoded message
    - Detect mobile vs desktop for native app vs web
    - Add WhatsApp icon SVG
    - Support gold color for primary variant
    - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5, 9.6, 9.7, Custom_

  - [ ] 13.2 Add WhatsApp button CSS styling with gold accent
    - Style primary variant: gold background (#C98A3E), white text
    - Style secondary variant: transparent with gold border
    - Add hover effects: darker background, lift animation
    - Style icon with proper sizing
    - Ensure full-width on mobile
    - _Requirements: 17.4, 17.5, 17.6, 12.5, Custom_

  - [x] 13.3 Add WhatsApp configuration to `config/app.php`
    - Add `whatsapp_number` configuration key
    - Read from `WHATSAPP_NUMBER` environment variable
    - Provide fallback number
    - _Requirements: 9.7_


- [ ] 14. Update main education page view
  - [ ] 14.1 Update `resources/views/pages/education.blade.php` with new sections
    - Use updated layout with `layouts/app.blade.php`
    - Render breadcrumb navigation
    - Render enhanced hero section with location badge, buttons, and statistics strip
    - Render Platform Fieldtrip section with Tabler icon badges
    - Render Program Category section
    - Render Edukasi Lingkungan section (3 programs, no disclosure)
    - Render Science section (2 programs, no disclosure)
    - Render Art section (3 featured + 11 others with disclosure)
    - Render redesigned testimonial section (dark card)
    - Render new school partners section ("Dipercaya oleh")
    - Render footer CTA bar before footer
    - _Requirements: 20.1, 20.2, 20.3, 20.4, 20.5, 20.6, 20.7, Custom_

  - [ ] 14.2 Add responsive breakpoint handling
    - Show card-grid on desktop (> 1024px)
    - Show card-carousel on mobile (< 768px)
    - Apply tablet layout (768px-1024px) with 2-column grid
    - Stack alternating layouts vertically on mobile
    - Adjust hero text size for mobile
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_

  - [ ] 14.3 Integrate navigation, breadcrumb, and SEO services
    - Pass navigation data from controller
    - Render main navigation with current route highlighting
    - Render breadcrumb trail
    - Include SEO metadata in head section
    - _Requirements: 13.1, 13.2, 13.3, 13.4, 13.5_


- [ ] 15. Implement image optimization and lazy loading
  - [ ] 15.1 Set up WebP format with JPEG/PNG fallbacks
    - Use `<picture>` element for all images
    - Provide WebP source with type attribute
    - Provide JPEG/PNG fallback in img element
    - Test fallback in browsers without WebP support
    - _Requirements: 10.1_

  - [ ] 15.2 Add lazy loading for below-fold images
    - Set `loading="lazy"` attribute on images below fold
    - Set `loading="eager"` for hero background (above fold)
    - Add placeholder blur effect during loading
    - Prevent layout shift with aspect ratio preservation
    - _Requirements: 10.2, 10.3, 10.6_

  - [ ] 15.3 Add error handling with fallback placeholder images
    - Add Alpine.js `@error` directive to all images
    - Set fallback to placeholder image on error
    - Create placeholder images: `placeholder-education.jpg`, `placeholder-blur.jpg`
    - Add alt text to all images for accessibility
    - _Requirements: 10.4, 10.5, 21.1, 21.2_

- [ ] 16. Add SEO optimization
  - [ ] 16.1 Update SEOService metadata generation for education page
    - Set title: "Wisata Edukasi - Ecotainment Godongijo | The Waterfall"
    - Set meta description with educational tourism keywords
    - Add Open Graph tags for social media sharing
    - Add structured data for EducationalOrganization
    - _Requirements: 14.1, 14.2, 14.3, 14.4, 14.6_

  - [ ] 16.2 Ensure proper heading hierarchy
    - Use H1 for hero title only
    - Use H2 for section titles
    - Use H3 for card titles
    - Validate semantic structure
    - _Requirements: 14.5_


- [ ] 17. Implement accessibility features
  - [ ] 17.1 Add ARIA labels and semantic HTML
    - Use semantic HTML5 elements: `<section>`, `<article>`, `<figure>`
    - Add aria-label to carousel navigation buttons
    - Add aria-current to pagination dots
    - Ensure keyboard accessibility for all interactive elements
    - Add visible focus indicators
    - _Requirements: 15.1, 15.2, 15.3, 15.4, 15.5, 15.7_

  - [ ] 17.2 Validate color contrast ratios
    - Test text on backgrounds: ensure 4.5:1 minimum for normal text
    - Test forest text on cream background
    - Test white text on forest-d background
    - Test gold accent color readability
    - _Requirements: 15.6_

- [ ] 18. Add CSS progressive enhancement and browser fallbacks
  - [ ] 18.1 Add CSS Grid fallback with Flexbox
    - Provide Flexbox layout as fallback for older browsers
    - Use `@supports (display: grid)` for Grid-specific adjustments
    - Test in browsers without Grid support
    - _Requirements: 22.1_

  - [ ] 18.2 Add CSS custom properties fallback
    - Provide static color values before custom properties
    - Example: `color: #1E4636; color: var(--forest);`
    - Ensure older browsers get readable colors
    - _Requirements: 22.2_

  - [ ] 18.3 Add noscript fallback for Alpine.js
    - Provide static vertical list when JavaScript disabled
    - Hide carousel navigation when Alpine unavailable
    - Show all cards without carousel wrapper
    - Test with JavaScript disabled
    - _Requirements: 21.3, 22.3_

  - [ ] 18.4 Add font-display swap for web fonts
    - Set `font-display: swap` for Fraunces and Sora fonts
    - Prevent invisible text during font loading
    - _Requirements: 22.4_


- [ ] 19. Create route for school partners full list page
  - [x] 19.1 Add route `/wisata-edukasi/sekolah-mitra`
    - Create route in `routes/web.php`
    - Point to `StaticPageController@schoolPartners` method
    - Use SEO-friendly URL structure
    - _Requirements: Custom (school partners full list)_

  - [x] 19.2 Create `schoolPartners()` method in StaticPageController
    - Query all active school partners ordered by display order
    - Paginate results (20 per page)
    - Pass data to view with navigation and SEO metadata
    - _Requirements: Custom (school partners full list)_

  - [ ] 19.3 Create `resources/views/pages/school-partners.blade.php` view
    - Display page title "Sekolah Mitra Kami"
    - Display grid of all school partner cards (4 columns desktop, 2 mobile)
    - Use same partner card component
    - Add pagination controls
    - Include breadcrumb: Home > Wisata Edukasi > Sekolah Mitra
    - _Requirements: Custom (school partners full list)_

- [ ] 20. Checkpoint - Test visual adjustments and component integration
  - Ensure all tests pass, ask the user if questions arise.


- [x] 21. Add visual assets
  - [x] 21.1 Prepare hero background images
    - Create or source hero background image (educational activities scene)
    - Export as WebP format (optimized, < 300KB)
    - Export as JPEG fallback (optimized, < 400KB)
    - Place in `public/images/education/hero-background.{webp,jpg}`
    - _Requirements: 1.1, 10.1, 16.1_

  - [x] 21.2 Prepare program category images
    - Create or source 3 category images (16:9 aspect ratio)
    - Export as WebP and JPEG
    - Place in `public/images/education/category-{environmental,science,art}.{webp,jpg}`
    - _Requirements: 3.2, 10.1_

  - [x] 21.3 Prepare environmental program images
    - Create or source 3 program detail images (4:3 aspect ratio)
    - Export as WebP and JPEG
    - Place in `public/images/education/{learning-animals,urban-farming,renewable-energy}.{webp,jpg}`
    - _Requirements: 4.5, 10.1_

  - [x] 21.4 Prepare testimonial photo
    - Create or source testimonial photo (4:3 aspect ratio)
    - Export as WebP and JPEG
    - Place in `public/images/education/testimonial-photo.{webp,jpg}`
    - _Requirements: 11.2, 10.1_

  - [x] 21.5 Create placeholder fallback images
    - Create generic placeholder with Godong Ijo branding
    - Create blurred placeholder for lazy loading
    - Place in `public/images/education/placeholder-{education,blur}.jpg`
    - _Requirements: 10.4, 21.1_


- [ ] 22. Performance optimization
  - [ ] 22.1 Add critical CSS inlining
    - Inline critical above-fold CSS in `<head>` (hero, navigation)
    - Load full stylesheet asynchronously with `rel="preload"`
    - Prevent render-blocking CSS
    - _Requirements: 16.2_

  - [ ] 22.2 Optimize image delivery
    - Serve WebP images to supporting browsers
    - Add responsive image srcset for different screen sizes
    - Compress images with appropriate quality settings
    - Test page load time < 3 seconds
    - _Requirements: 10.1, 16.1, 16.4_

  - [ ] 22.3 Add preconnect for external resources
    - Add `<link rel="preconnect">` for Tabler Icons CDN (if used)
    - Add preconnect for WhatsApp domain
    - Reduce DNS lookup time
    - _Requirements: 16.2_

  - [ ] 22.4 Minimize layout shift
    - Set explicit width/height or aspect-ratio on all images
    - Reserve space for lazy-loaded content
    - Test with Lighthouse CLS metric
    - _Requirements: 10.6, 16.5_

- [ ] 23. Final integration and testing
  - [ ] 23.1 Test responsive breakpoints
    - Test mobile layout (< 768px): carousel, vertical stacks
    - Test tablet layout (768px-1024px): 2-column grid
    - Test desktop layout (> 1024px): 3-column grid, alternating layouts
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_

  - [ ] 23.2 Test all WhatsApp integrations
    - Test WhatsApp buttons open with correct pre-filled messages
    - Test mobile detection (native app vs web)
    - Test message encoding with special characters
    - Verify phone number configuration
    - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5, 9.6, 9.7_

  - [ ] 23.3 Test carousel interactions
    - Test touch swipe gestures on mobile
    - Test navigation buttons (prev/next)
    - Test pagination dots
    - Test button disable states at boundaries
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 6.6, 6.7, 6.8_

  - [ ] 23.4 Test progressive disclosure in Art section
    - Verify 3 featured programs display as full cards
    - Verify 11 other programs hidden in `<details>` element
    - Test "Lihat N program lainnya" expand/collapse
    - Test chip layout wrapping
    - _Requirements: Custom (program curation)_

  - [ ] 23.5 Test school partners section
    - Verify 7 school cards display with monogram fallbacks
    - Test "+N sekolah lainnya" card links to full list page
    - Test full list page with pagination
    - _Requirements: Custom (school partners)_

  - [ ] 23.6 Test all visual adjustments
    - Verify new color palette applied: forest, forest-d, gold, cream, line, ink
    - Verify hero location badge, buttons, statistics strip
    - Verify Platform Fieldtrip Tabler icon badges
    - Verify testimonial dark card design
    - Verify footer CTA bar styling and placement
    - Verify card borders: 1px solid line, no box-shadow, 12px radius
    - _Requirements: Custom (all visual adjustments)_

  - [ ] 23.7 Run accessibility audit
    - Use axe DevTools or similar to scan for a11y issues
    - Verify ARIA labels on carousel controls
    - Test keyboard navigation
    - Verify color contrast ratios
    - Test with screen reader
    - _Requirements: 15.1, 15.2, 15.3, 15.4, 15.5, 15.6, 15.7_

  - [ ] 23.8 Run performance audit
    - Use Lighthouse to test performance score
    - Verify page load time < 3 seconds
    - Check CLS, FCP, LCP metrics
    - Verify image optimization
    - _Requirements: 16.1, 16.2, 16.3, 16.4, 16.5_

- [ ] 24. Final checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.


## Notes

- This implementation plan includes comprehensive visual adjustments from the base design:
  - **New color palette**: `--forest: #1E4636`, `--forest-d: #0F2A1F`, `--gold: #C98A3E`, `--cream: #FAF6ED`, `--line: #EAE4D6`, `--ink: #22301E`
  - **Hero enhancements**: Location badge, dual buttons ("About us" outline, "Reservasi" gold WhatsApp), statistics strip
  - **Platform Fieldtrip icons**: Tabler icons (`ti-device-laptop`, `ti-bus`, `ti-trees`) in squared badges with cream background
  - **Program curation with progressive disclosure**: Art section (14 programs) displays 3 featured as cards, 11 others in `<details>` as chips
  - **Testimonial redesign**: Single dark card (forest background) with gold quote icon, italic serif quote, gold source
  - **New "Dipercaya oleh" section**: 7 school partner cards with monogram fallback, "+N sekolah lainnya" link to full list
  - **Footer CTA bar**: Forest-d background, text left, gold WhatsApp button right

- The `program-group.blade.php` component is reusable for any program category that grows large in the future

- Edukasi Lingkungan (3 programs) and Science (2 programs) display all programs as normal cards without disclosure UI

- Card styling uses 1px border with `--line` color and NO box-shadow, border-radius 12px

- H1 hero uses serif font (Fraunces), rest of text uses sans-serif (Sora)

- All other aspects of the original design.md remain unchanged: carousel functionality, lazy loading, responsive breakpoints, SEO, accessibility, error handling

- School partners data structure allows logo upload in the future, with monogram fallback as initial state

- The `/wisata-edukasi/sekolah-mitra` route provides full list page for school partners

- Tasks are ordered to establish data layer first (migration, model, controller), then UI components, then integration and testing

- Each task references specific requirements from requirements.md for traceability

- Checkpoint tasks at reasonable breaks ensure incremental validation


## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1.1", "2.1", "13.3", "19.1", "21.1", "21.2", "21.3", "21.4", "21.5"] },
    { "id": 1, "tasks": ["2.2", "2.3"] },
    { "id": 2, "tasks": ["3.1", "3.2", "3.3", "3.4", "3.5", "3.6", "19.2"] },
    { "id": 3, "tasks": ["4.1", "5.1", "6.1", "7.1", "8.1", "9.1", "10.1", "11.1", "12.1", "13.1"] },
    { "id": 4, "tasks": ["4.2", "5.2", "6.2", "6.3", "6.4", "7.2", "11.2", "19.3"] },
    { "id": 5, "tasks": ["4.3", "6.5", "7.3", "7.4", "8.2", "9.2", "10.2", "11.3", "12.2", "13.2"] },
    { "id": 6, "tasks": ["14.1", "15.1", "16.1", "17.1"] },
    { "id": 7, "tasks": ["14.2", "14.3", "15.2", "15.3", "16.2", "17.2", "18.1", "18.2", "18.3", "18.4"] },
    { "id": 8, "tasks": ["22.1", "22.2", "22.3", "22.4"] },
    { "id": 9, "tasks": ["23.1", "23.2", "23.3", "23.4", "23.5", "23.6", "23.7", "23.8"] }
  ]
}
```
