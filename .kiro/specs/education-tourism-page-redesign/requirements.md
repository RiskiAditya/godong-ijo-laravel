# Requirements Document

## Introduction

Halaman Wisata Edukasi (Educational Tourism) untuk Godong Ijo yang menampilkan program Ecotainment Godongijo secara komprehensif. Halaman ini dirancang untuk sekolah dan institusi pendidikan dengan tiga platform fieldtrip (Virtual, Goes To School Field Trip, At Godongijo) dan tiga kategori program (Edukasi Lingkungan, Science, Art). Fokus pada pengalaman visual yang menarik, navigasi yang lancar, integrasi WhatsApp untuk inquiry, dan desain responsif yang optimal di semua perangkat.

## Glossary

- **Education_Page**: Halaman web untuk Wisata Edukasi Ecotainment Godongijo
- **Hero_Section**: Bagian utama di atas fold dengan background image dan teks overlay
- **Card_Grid**: Tata letak kartu dalam format grid untuk tampilan desktop
- **Card_Carousel**: Tata letak kartu dalam format carousel yang dapat digeser untuk tampilan mobile
- **Program_Card**: Komponen kartu yang menampilkan informasi program atau platform dengan aksi WhatsApp
- **Platform_Fieldtrip**: Kategori platform karyawisata (Virtual, Goes To School Field Trip, At Godongijo)
- **Program_Category**: Kategori program edukasi utama (Edukasi Lingkungan, Science, Art)
- **Alternating_Layout**: Tata letak bergantian kiri-kanan untuk konten detail program
- **WhatsApp_Button**: Tombol untuk menghubungi via WhatsApp dengan pesan yang sudah diisi
- **Responsive_Layout**: Tata letak yang beradaptasi dengan berbagai ukuran layar
- **Lazy_Loading**: Teknik memuat gambar secara bertahap saat dibutuhkan
- **WebP_Format**: Format gambar modern dengan kompresi lebih baik
- **Alpine_JS**: Framework JavaScript lightweight untuk interaktivitas
- **SEOService**: Service untuk menghasilkan metadata SEO dan structured data
- **BreadcrumbService**: Service untuk menghasilkan navigasi breadcrumb
- **NavigationService**: Service untuk menghasilkan navigasi utama

## Requirements

### Requirement 1: Hero Section dengan Background Image

**User Story:** Sebagai pengunjung website, saya ingin melihat hero section yang menarik dengan background image dan informasi Ecotainment Godongijo, sehingga saya segera memahami konsep dan nilai edukasi yang ditawarkan.

#### Acceptance Criteria

1. THE Hero_Section SHALL display a full-width background image
2. THE Hero_Section SHALL display a semi-transparent overlay on the background image
3. THE Hero_Section SHALL display the title "Ecotainment Godongijo" with centered alignment
4. THE Hero_Section SHALL display description text explaining the educational tourism concept
5. WHEN a background image fails to load, THE Hero_Section SHALL display a fallback image
6. THE Hero_Section SHALL occupy minimum 50% viewport height on desktop
7. THE Hero_Section SHALL occupy minimum 40% viewport height on mobile

### Requirement 2: Platform Fieldtrip Card Display

**User Story:** Sebagai pengunjung website, saya ingin melihat tiga platform fieldtrip yang tersedia (Virtual, Goes To School Field Trip, At Godongijo), sehingga saya dapat memilih platform yang sesuai dengan kebutuhan.

#### Acceptance Criteria

1. THE Education_Page SHALL display three Platform_Fieldtrip cards (Virtual Fieldtrip, Goes To School Field Trip, At Godongijo)
2. THE Platform_Fieldtrip cards SHALL display an icon representing each platform
3. THE Platform_Fieldtrip cards SHALL display a title for each platform
4. THE Platform_Fieldtrip cards SHALL display a description for each platform
5. THE Platform_Fieldtrip cards SHALL include a WhatsApp_Button for inquiry
6. WHEN viewport width is greater than 1024px, THE Education_Page SHALL display Platform_Fieldtrip cards in a Card_Grid with 3 columns
7. WHEN viewport width is 768px or less, THE Education_Page SHALL display Platform_Fieldtrip cards in a Card_Carousel

### Requirement 3: Program Category Display

**User Story:** Sebagai pengunjung website, saya ingin melihat tiga kategori program edukasi (Edukasi Lingkungan, Science, Art) dengan gambar yang menarik, sehingga saya dapat memahami jenis program yang tersedia.

#### Acceptance Criteria

1. THE Education_Page SHALL display three Program_Category cards (Edukasi Lingkungan, Science, Art)
2. THE Program_Category cards SHALL display a featured image with 16:9 aspect ratio
3. THE Program_Category cards SHALL display an icon badge on the image
4. THE Program_Category cards SHALL display a title for each category
5. THE Program_Category cards SHALL display a description for each category
6. THE Program_Category cards SHALL include a WhatsApp_Button for inquiry
7. WHEN viewport width is greater than 1024px, THE Education_Page SHALL display Program_Category cards in a Card_Grid with 3 columns
8. WHEN viewport width is 768px or less, THE Education_Page SHALL display Program_Category cards in a Card_Carousel

### Requirement 4: Environmental Education Program Detail

**User Story:** Sebagai pengunjung website, saya ingin melihat detail program Edukasi Lingkungan (Learning About Animals, Urban Farming, Renewable Energy) dalam layout yang menarik, sehingga saya dapat memahami konten setiap program dengan jelas.

#### Acceptance Criteria

1. THE Education_Page SHALL display three environmental education programs (Learning About Animals, Urban Farming, Renewable Energy)
2. THE environmental education programs SHALL use Alternating_Layout with image and text
3. WHEN the program index is even, THE Alternating_Layout SHALL position the image on the left
4. WHEN the program index is odd, THE Alternating_Layout SHALL position the image on the right
5. THE environmental education program SHALL display a featured image with 4:3 aspect ratio
6. THE environmental education program SHALL display a title
7. THE environmental education program SHALL display a detailed description paragraph
8. WHEN viewport width is 768px or less, THE Alternating_Layout SHALL stack image and text vertically

### Requirement 5: Art and Science Program Display

**User Story:** Sebagai pengunjung website, saya ingin melihat program seni dan sains yang tersedia, sehingga saya dapat mengetahui variasi program yang ditawarkan.

#### Acceptance Criteria

1. THE Education_Page SHALL display art programs (Art Painting, Traditional Dance, Fine Art) in Program_Card format
2. THE Education_Page SHALL display science programs (Aero Glider, Profesor Cilik) in Program_Card format
3. THE art and science program cards SHALL display a title
4. THE art and science program cards SHALL display a description
5. THE art and science program cards SHALL include a WhatsApp_Button for inquiry
6. WHEN viewport width is greater than 1024px, THE art and science programs SHALL display in a Card_Grid
7. WHEN viewport width is 768px or less, THE art and science programs SHALL display in a Card_Carousel

### Requirement 6: Mobile Carousel Navigation

**User Story:** Sebagai pengunjung website di perangkat mobile, saya ingin dapat menggeser carousel untuk melihat semua kartu program, sehingga saya dapat menjelajahi konten dengan mudah di layar kecil.

#### Acceptance Criteria

1. WHEN viewport width is 768px or less, THE Card_Carousel SHALL be visible
2. THE Card_Carousel SHALL support touch swipe gestures for navigation
3. THE Card_Carousel SHALL display navigation arrow buttons (previous and next)
4. THE Card_Carousel SHALL display pagination dots indicating current slide
5. WHEN the user swipes left by more than 50 pixels, THE Card_Carousel SHALL advance to the next slide
6. WHEN the user swipes right by more than 50 pixels, THE Card_Carousel SHALL return to the previous slide
7. WHEN the user clicks a pagination dot, THE Card_Carousel SHALL navigate to the corresponding slide
8. THE Card_Carousel SHALL transition between slides with smooth animation

### Requirement 7: Desktop Grid Layout

**User Story:** Sebagai pengunjung website di desktop, saya ingin melihat kartu program dalam grid layout, sehingga saya dapat melihat semua opsi secara sekaligus tanpa harus scroll atau geser.

#### Acceptance Criteria

1. WHEN viewport width is greater than 1024px, THE Card_Grid SHALL be visible
2. THE Card_Grid SHALL arrange cards in a 3-column grid layout
3. THE Card_Grid SHALL maintain equal spacing between cards
4. WHEN the user hovers over a Program_Card, THE Program_Card SHALL display a lift animation
5. WHEN the user hovers over a Program_Card image, THE image SHALL display a zoom animation

### Requirement 8: Responsive Breakpoint Adaptation

**User Story:** Sebagai pengunjung website dari berbagai perangkat, saya ingin halaman menyesuaikan layout sesuai ukuran layar, sehingga konten tetap nyaman dibaca di semua perangkat.

#### Acceptance Criteria

1. WHEN viewport width is 768px or less, THE Responsive_Layout SHALL apply mobile layout
2. WHEN viewport width is between 769px and 1024px, THE Responsive_Layout SHALL apply tablet layout with 2-column grid
3. WHEN viewport width is greater than 1024px, THE Responsive_Layout SHALL apply desktop layout with 3-column grid
4. WHEN viewport width is 768px or less, THE Hero_Section SHALL adjust text size for readability
5. WHEN viewport width is 768px or less, THE Alternating_Layout SHALL stack vertically

### Requirement 9: WhatsApp Integration untuk Inquiry

**User Story:** Sebagai pengunjung website yang tertarik dengan program tertentu, saya ingin dapat langsung menghubungi via WhatsApp dengan pesan yang sudah diisi, sehingga proses inquiry menjadi lebih mudah dan cepat.

#### Acceptance Criteria

1. THE Program_Card SHALL include a WhatsApp_Button
2. WHEN a user clicks the WhatsApp_Button, THE button SHALL open WhatsApp with a pre-filled message
3. THE pre-filled message SHALL include the specific program name
4. THE pre-filled message SHALL be URL-encoded properly to handle special characters
5. WHEN the user is on a mobile device, THE WhatsApp_Button SHALL open the native WhatsApp app
6. WHEN the user is on a desktop device, THE WhatsApp_Button SHALL open WhatsApp Web in a new tab
7. THE WhatsApp_Button SHALL use the phone number from application configuration

### Requirement 10: Image Optimization dan Loading

**User Story:** Sebagai pengunjung website, saya ingin gambar memuat dengan cepat tanpa mengorbankan kualitas visual, sehingga pengalaman browsing tetap lancar.

#### Acceptance Criteria

1. THE Education_Page SHALL use WebP_Format for all images with fallback to JPEG or PNG
2. THE Hero_Section background image SHALL use eager loading
3. THE images below the fold SHALL use Lazy_Loading
4. WHEN an image fails to load, THE Education_Page SHALL display a placeholder fallback image
5. THE images SHALL include proper alt text for accessibility
6. THE images SHALL maintain aspect ratio during loading to prevent layout shift

### Requirement 11: Testimonial Section Display

**User Story:** Sebagai pengunjung website, saya ingin melihat testimonial tentang program edukasi Godong Ijo, sehingga saya dapat memahami pengalaman pengunjung sebelumnya.

#### Acceptance Criteria

1. THE Education_Page SHALL display a testimonial section
2. THE testimonial section SHALL display a photo with 4:3 aspect ratio
3. THE testimonial section SHALL display a quote text
4. THE testimonial section SHALL display a source attribution
5. WHEN viewport width is greater than 1024px, THE testimonial SHALL use horizontal layout with photo on left and quote on right
6. WHEN viewport width is 768px or less, THE testimonial SHALL stack photo and quote vertically

### Requirement 12: Brand Visual Consistency

**User Story:** Sebagai pengunjung website, saya ingin desain halaman wisata edukasi konsisten dengan identitas visual The Waterfall, sehingga pengalaman brand terasa koheren.

#### Acceptance Criteria

1. THE Education_Page SHALL use the existing brand color palette (forest, mint, gold, clay, sand, ink)
2. THE Education_Page SHALL use the Fraunces font family for headings
3. THE Education_Page SHALL use the Sora font family for body text
4. THE Program_Card SHALL use rounded corners with 16px border-radius
5. THE WhatsApp_Button SHALL use rounded pill shape with 50px border-radius
6. THE Education_Page SHALL use consistent spacing following existing design system

### Requirement 13: Navigation Integration

**User Story:** Sebagai pengunjung website, saya ingin dapat menavigasi kembali ke halaman lain dengan mudah, sehingga saya tidak merasa terjebak di halaman wisata edukasi.

#### Acceptance Criteria

1. THE Education_Page SHALL include the main navigation bar from NavigationService
2. THE Education_Page SHALL display breadcrumb navigation (Home > Wisata Edukasi)
3. THE breadcrumb navigation SHALL be generated by BreadcrumbService
4. THE Education_Page SHALL include the footer section
5. THE navigation elements SHALL maintain consistent styling with other pages

### Requirement 14: SEO Optimization

**User Story:** Sebagai pemilik website, saya ingin halaman wisata edukasi dioptimalkan untuk search engine, sehingga sekolah dan institusi pendidikan dapat menemukan program Ecotainment dengan mudah.

#### Acceptance Criteria

1. THE Education_Page SHALL include a title tag "Wisata Edukasi - Ecotainment Godongijo | The Waterfall"
2. THE Education_Page SHALL include a meta description describing educational tourism programs
3. THE Education_Page SHALL include Open Graph meta tags for social media sharing
4. THE Education_Page SHALL include structured data markup for EducationalOrganization
5. THE Education_Page SHALL use appropriate heading hierarchy (h1 for hero, h2 for sections, h3 for cards)
6. THE SEO metadata SHALL be generated by SEOService

### Requirement 15: Accessibility Compliance

**User Story:** Sebagai pengunjung dengan kebutuhan aksesibilitas, saya ingin halaman wisata edukasi dapat diakses dengan mudah, sehingga saya dapat menikmati konten tanpa hambatan.

#### Acceptance Criteria

1. THE Education_Page SHALL include appropriate alt text for all images
2. THE WhatsApp_Button SHALL be keyboard accessible
3. THE Card_Carousel navigation buttons SHALL include aria-label attributes
4. THE Card_Carousel pagination dots SHALL include aria-current attributes
5. THE Education_Page SHALL use semantic HTML5 elements (section, article, figure)
6. THE Education_Page SHALL maintain color contrast ratio of at least 4.5:1 for normal text
7. WHEN a user navigates with keyboard, THE interactive elements SHALL provide visible focus indicators

### Requirement 16: Performance Optimization

**User Story:** Sebagai pengunjung website, saya ingin halaman wisata edukasi memuat dengan cepat, sehingga saya tidak perlu menunggu lama untuk melihat informasi program.

#### Acceptance Criteria

1. WHEN a user accesses the Education_Page, THE page SHALL load critical content within 3 seconds
2. THE Education_Page SHALL minimize render-blocking resources
3. THE Education_Page SHALL implement lazy loading for images below the fold
4. THE Education_Page SHALL use optimized WebP images with appropriate compression
5. WHEN images are loading, THE Education_Page SHALL display skeleton or placeholder to prevent layout shift

### Requirement 17: Interactive Card Hover Effects

**User Story:** Sebagai pengunjung website, saya ingin mendapatkan feedback visual saat mengarahkan kursor ke kartu program, sehingga interaksi terasa lebih responsif dan engaging.

#### Acceptance Criteria

1. WHEN a user hovers over a Program_Card, THE card SHALL elevate with translateY animation
2. WHEN a user hovers over a Program_Card, THE card SHALL display enhanced box shadow
3. WHEN a user hovers over a Program_Card image, THE image SHALL scale up by 5%
4. WHEN a user hovers over a WhatsApp_Button, THE button SHALL display darker background color
5. WHEN a user hovers over a WhatsApp_Button, THE button SHALL elevate with translateY animation
6. THE hover animations SHALL use smooth transitions with appropriate easing

### Requirement 18: Alpine.js Interactive Components

**User Story:** Sebagai pengunjung website, saya ingin interaksi carousel berfungsi dengan lancar tanpa perlu reload halaman, sehingga pengalaman browsing terasa fluid dan modern.

#### Acceptance Criteria

1. THE Card_Carousel SHALL use Alpine_JS for interactive state management
2. THE Card_Carousel SHALL maintain currentIndex state for active slide
3. WHEN a user clicks the next button, THE Card_Carousel SHALL increment currentIndex
4. WHEN a user clicks the previous button, THE Card_Carousel SHALL decrement currentIndex
5. WHEN a user clicks a pagination dot, THE Card_Carousel SHALL set currentIndex to the corresponding slide
6. THE Card_Carousel SHALL apply transform translateX based on currentIndex
7. THE Card_Carousel SHALL disable previous button when currentIndex is 0
8. THE Card_Carousel SHALL disable next button when currentIndex is at the last slide

### Requirement 19: Laravel Controller Data Preparation

**User Story:** Sebagai developer, saya ingin controller menyiapkan data yang terstruktur dengan baik untuk view, sehingga template Blade dapat render konten dengan mudah dan konsisten.

#### Acceptance Criteria

1. THE StaticPageController education method SHALL prepare hero section data with backgroundImage, title, and description
2. THE StaticPageController education method SHALL prepare platformFieldtrip data with 3 cards
3. THE StaticPageController education method SHALL prepare programCategory data with 3 cards
4. THE StaticPageController education method SHALL prepare environmentalPrograms data with 3 program details
5. THE StaticPageController education method SHALL prepare artPrograms data with 3 cards
6. THE StaticPageController education method SHALL prepare sciencePrograms data with 2 cards
7. THE StaticPageController education method SHALL prepare testimonial data with photo and quote
8. THE StaticPageController education method SHALL prepare breadcrumb data using BreadcrumbService
9. THE StaticPageController education method SHALL prepare SEO metadata using SEOService
10. THE StaticPageController education method SHALL prepare navigation data using NavigationService

### Requirement 20: Blade Component Reusability

**User Story:** Sebagai developer, saya ingin komponen Blade yang dapat digunakan kembali untuk berbagai bagian halaman, sehingga kode lebih maintainable dan konsisten.

#### Acceptance Criteria

1. THE Education_Page SHALL use a hero-ecotainment component for the hero section
2. THE Education_Page SHALL use a card-grid component for desktop card layouts
3. THE Education_Page SHALL use a card-carousel component for mobile card layouts
4. THE Education_Page SHALL use a program-card component for all card displays
5. THE Education_Page SHALL use a whatsapp-button component for all WhatsApp CTAs
6. THE Education_Page SHALL use a program-detail-alternating component for environmental program details
7. THE Education_Page SHALL use a testimonial-section component for testimonial display
8. THE Blade components SHALL accept props for customization
9. THE Blade components SHALL include default values for optional props

### Requirement 21: Error Handling dan Fallback States

**User Story:** Sebagai pengunjung website, saya ingin halaman tetap berfungsi dengan baik meskipun terjadi error pada asset atau data, sehingga pengalaman browsing tidak terganggu.

#### Acceptance Criteria

1. WHEN an image fails to load, THE Education_Page SHALL display a placeholder image
2. WHEN the WebP format is not supported, THE Education_Page SHALL fall back to JPEG or PNG format
3. WHEN Alpine_JS fails to load, THE Card_Carousel SHALL degrade to a static vertical list
4. WHEN controller data is missing, THE Education_Page SHALL use fallback default values
5. WHEN the WhatsApp phone number is not configured, THE Education_Page SHALL use a fallback number and log a warning
6. WHEN an asset path is invalid, THE Education_Page SHALL use a fallback asset and log a warning

### Requirement 22: CSS Progressive Enhancement

**User Story:** Sebagai pengunjung website dengan browser lama, saya ingin halaman tetap dapat dibaca dan digunakan meskipun tidak semua fitur modern didukung, sehingga konten tetap accessible.

#### Acceptance Criteria

1. THE Education_Page SHALL use Flexbox fallback for browsers that do not support CSS Grid
2. THE Education_Page SHALL provide static color fallbacks for browsers that do not support CSS custom properties
3. THE Education_Page SHALL remain functional when JavaScript is disabled with noscript fallback
4. THE Education_Page SHALL use font-display swap for web fonts to prevent invisible text
5. THE Education_Page SHALL use @supports queries for progressive enhancement features
6. THE Card_Grid SHALL adapt gracefully in older browsers using Flexbox

