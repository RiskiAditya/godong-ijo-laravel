# Requirements Document

## Introduction

This feature creates dedicated package category pages for the Godong Ijo tourism website. Each of the three package categories (The Waterfall Resto, Private Room, and Fishing Lake) will have its own dedicated page displaying filtered packages in a card layout with horizontal scrolling support. These pages are accessible from the navbar dropdown menu and allow users to browse packages by category type.

## Glossary

- **Package_Category_System**: The web application component responsible for displaying package category pages
- **Navigation_Dropdown**: The navbar dropdown menu containing links to package category pages
- **Package_Card**: A visual card component displaying package information (photo, name, badge, price, button)
- **jenis_paket**: Database ENUM column storing package category type ('The Waterfall Resto', 'Private Room', 'Fishing Lake')
- **Package_Repository**: Database access layer for retrieving package data
- **Category_Page**: A dedicated web page displaying packages filtered by a specific jenis_paket value
- **Horizontal_Scroller**: UI component enabling horizontal scrolling for multiple package cards
- **SEO_Service**: Service class responsible for generating SEO metadata for pages
- **Breadcrumb_Service**: Service class responsible for generating navigation breadcrumbs

## Requirements

### Requirement 1: Category Page Route and URL Structure

**User Story:** As a user, I want to access package category pages through clean URLs, so that I can easily navigate and share links to specific package categories.

#### Acceptance Criteria

1. THE Package_Category_System SHALL map the route name 'packages.category' to the URL pattern '/paket/{category}'
2. WHEN a user visits '/paket/the-waterfall-resto', THE Package_Category_System SHALL display The Waterfall Resto category page
3. WHEN a user visits '/paket/private-room', THE Package_Category_System SHALL display Private Room category page
4. WHEN a user visits '/paket/fishing-lake', THE Package_Category_System SHALL display Fishing Lake category page
5. THE Package_Category_System SHALL validate the category parameter against allowed values ('the-waterfall-resto', 'private-room', 'fishing-lake')
6. WHEN an invalid category URL is accessed, THE Package_Category_System SHALL return a 404 error page

### Requirement 2: Package Data Filtering

**User Story:** As a user, I want to see only packages relevant to the selected category, so that I can focus on the package type I'm interested in.

#### Acceptance Criteria

1. WHEN a category page is loaded, THE Package_Repository SHALL query the paket_wisata table filtered by jenis_paket
2. THE Package_Repository SHALL map URL slug 'the-waterfall-resto' to jenis_paket value 'The Waterfall Resto'
3. THE Package_Repository SHALL map URL slug 'private-room' to jenis_paket value 'Private Room'
4. THE Package_Repository SHALL map URL slug 'fishing-lake' to jenis_paket value 'Fishing Lake'
5. THE Package_Repository SHALL only return packages where is_active equals true
6. THE Package_Repository SHALL order results by created_at descending
7. WHEN no packages exist for a category, THE Category_Page SHALL display an empty state message

### Requirement 3: Category Page Header Content

**User Story:** As a user, I want to see a clear heading and description on category pages, so that I understand what type of packages are available.

#### Acceptance Criteria

1. THE Category_Page SHALL display the heading "Pilih Paket Sesuai Kebutuhan" at the top of the page
2. THE Category_Page SHALL display the subtitle "Dari kuliner keluarga hingga event perusahaan, kami punya paket untuk Anda" below the heading
3. THE Category_Page SHALL display the specific category name as a secondary heading
4. THE Category_Page SHALL style the heading with consistent typography matching the landing page section headers

### Requirement 4: Package Card Display

**User Story:** As a user, I want to see package details in a visual card format, so that I can quickly compare packages and make a decision.

#### Acceptance Criteria

1. THE Package_Card SHALL display the package photo as the card background or primary image
2. WHEN a package has no photo, THE Package_Card SHALL display a placeholder image with an icon
3. THE Package_Card SHALL display a "Fleksibel" badge overlay on the package image
4. THE Package_Card SHALL display the package name (nama_paket) as the card title
5. THE Package_Card SHALL display the price in the format "Mulai Rp XX.XXX /orang" where XX.XXX is formatted with thousand separators
6. THE Package_Card SHALL display a "Hubungi" button at the bottom of each card
7. WHEN the "Hubungi" button is clicked, THE Package_Card SHALL link to the contact section ('#contact')
8. THE Package_Card SHALL apply consistent styling matching the landing page package cards

### Requirement 5: Grid Layout and Horizontal Scrolling

**User Story:** As a user, I want packages displayed in an organized grid that scrolls horizontally, so that I can browse through many packages smoothly.

#### Acceptance Criteria

1. THE Category_Page SHALL display Package_Cards in a horizontal scrolling layout
2. THE Horizontal_Scroller SHALL display multiple cards per row based on viewport width
3. THE Horizontal_Scroller SHALL enable touch/swipe scrolling on mobile devices
4. THE Horizontal_Scroller SHALL enable mouse wheel scrolling on desktop devices
5. THE Horizontal_Scroller SHALL display a scrollbar indicator when content overflows
6. WHEN viewport width is >= 1024px, THE Category_Page SHALL display 4 cards per row
7. WHEN viewport width is between 768px and 1023px, THE Category_Page SHALL display 3 cards per row
8. WHEN viewport width is < 768px, THE Category_Page SHALL display 2 cards per row

### Requirement 6: Navigation Integration

**User Story:** As a user, I want the navbar dropdown to highlight the current category page, so that I know which category I'm viewing.

#### Acceptance Criteria

1. WHEN a user is on a category page, THE Navigation_Dropdown SHALL apply an 'active' CSS class to the corresponding menu item
2. THE Navigation_Dropdown SHALL display the "Paket Wisata" dropdown menu as expanded when on a category page
3. THE Navigation_Dropdown SHALL maintain existing navigation functionality for all other pages
4. THE Navigation_Dropdown SHALL use the NavigationService to determine active menu items

### Requirement 7: SEO and Metadata

**User Story:** As a website owner, I want category pages to have proper SEO metadata, so that search engines can index and rank the pages effectively.

#### Acceptance Criteria

1. THE SEO_Service SHALL generate a unique page title for each category page in format "{Category Name} - Paket Wisata | Godong Ijo"
2. THE SEO_Service SHALL generate a meta description containing the category name and "Pilih Paket Sesuai Kebutuhan"
3. THE SEO_Service SHALL generate Open Graph metadata with og:title, og:description, og:image, og:url
4. THE SEO_Service SHALL use the category page canonical URL for og:url
5. WHEN available, THE SEO_Service SHALL use the first package photo from the category as og:image
6. WHEN no package photos exist, THE SEO_Service SHALL use a default category image for og:image
7. THE SEO_Service SHALL generate Twitter Card metadata matching the Open Graph data

### Requirement 8: Breadcrumb Navigation

**User Story:** As a user, I want to see breadcrumb navigation on category pages, so that I can understand my location in the site hierarchy and navigate back easily.

#### Acceptance Criteria

1. THE Breadcrumb_Service SHALL generate breadcrumb items: Home > Paket Wisata > {Category Name}
2. THE Breadcrumb_Service SHALL render breadcrumb JSON-LD structured data for search engines
3. THE Category_Page SHALL display visual breadcrumbs at the top of the page
4. THE Breadcrumb_Service SHALL mark the current category page as the final breadcrumb item
5. THE Breadcrumb_Service SHALL make "Home" breadcrumb clickable linking to '/'
6. THE Breadcrumb_Service SHALL make "Paket Wisata" breadcrumb non-clickable (no parent packages page exists)

### Requirement 9: Responsive Design

**User Story:** As a mobile user, I want category pages to display properly on my device, so that I can browse packages comfortably on any screen size.

#### Acceptance Criteria

1. WHEN viewport width is < 640px, THE Category_Page SHALL adjust heading font size to mobile-appropriate scale
2. WHEN viewport width is < 640px, THE Package_Card SHALL stack content vertically with optimized spacing
3. WHEN viewport width is < 640px, THE Horizontal_Scroller SHALL display 1 card at a time with snap scrolling
4. THE Category_Page SHALL maintain minimum touch target sizes of 44x44px for all interactive elements
5. THE Category_Page SHALL load package images with responsive srcset attributes
6. THE Category_Page SHALL use lazy loading for package images below the fold

### Requirement 10: Performance and Loading

**User Story:** As a user, I want category pages to load quickly, so that I can start browsing packages without delay.

#### Acceptance Criteria

1. THE Package_Repository SHALL use database indexing on the jenis_paket column for fast filtering
2. THE Category_Page SHALL implement lazy loading for package images using loading="lazy" attribute
3. THE Category_Page SHALL cache database queries for package lists with a 5-minute TTL
4. THE Category_Page SHALL preload critical CSS for above-the-fold content
5. THE Category_Page SHALL defer non-critical JavaScript execution
6. THE Package_Card SHALL optimize images with appropriate dimensions and compression

### Requirement 11: Empty State Handling

**User Story:** As a user, I want to see a helpful message when no packages are available in a category, so that I understand the situation and know what to do next.

#### Acceptance Criteria

1. WHEN no packages exist for a category, THE Category_Page SHALL display an empty state section
2. THE Category_Page SHALL display the message "Belum ada paket tersedia untuk kategori ini"
3. THE Category_Page SHALL display a secondary message "Silakan hubungi kami untuk informasi paket custom"
4. THE Category_Page SHALL display a "Hubungi Kami" button linking to the contact section
5. THE Category_Page SHALL hide the package grid section when showing empty state
6. THE Category_Page SHALL maintain consistent page layout and header when showing empty state

### Requirement 12: Accessibility

**User Story:** As a user with disabilities, I want category pages to be accessible, so that I can navigate and understand the content using assistive technologies.

#### Acceptance Criteria

1. THE Category_Page SHALL use semantic HTML5 elements (section, article, nav, header)
2. THE Package_Card SHALL include alt text for all package images describing the package
3. THE Category_Page SHALL maintain proper heading hierarchy (h1, h2, h3)
4. THE Package_Card SHALL ensure sufficient color contrast ratio (WCAG AA 4.5:1 for text)
5. THE Horizontal_Scroller SHALL be keyboard navigable using arrow keys and tab
6. THE Category_Page SHALL include ARIA labels for screen readers on interactive elements
7. THE Category_Page SHALL include skip-to-content links for keyboard navigation
