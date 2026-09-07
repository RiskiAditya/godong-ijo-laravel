<?php
/**
 * The Waterfall Landing Page - Standalone Version
 * Eco-Luxury Tourism Landing Page
 * No dependencies required - just open in browser!
 */

// Data for the landing page
$pageData = [
    'title' => 'The Waterfall - Eco-Luxury Tourism | Godong Ijo Experience',
    'description' => 'Experience Godong Ijo eco-luxury tourism at The Waterfall. Where nature meets wonder in sustainable luxury.',
    'images' => [
        [
            'src' => 'images/collage/forest.svg',
            'alt' => 'Lush green forest canopy with diverse tropical vegetation',
            'category' => 'forest',
        ],
        [
            'src' => 'images/collage/waterfall.svg',
            'alt' => 'Misty waterfall cascading through rocks surrounded by lush greenery',
            'category' => 'waterfall',
        ],
        [
            'src' => 'images/collage/aerial.svg',
            'alt' => 'Breathtaking aerial view of the eco-luxury resort surrounded by nature',
            'category' => 'aerial',
        ],
        [
            'src' => 'images/collage/adventure.svg',
            'alt' => 'Exciting adventure activities including zip-lining through the forest canopy',
            'category' => 'adventure',
        ],
        [
            'src' => 'images/collage/wildlife.svg',
            'alt' => 'Diverse wildlife including colorful birds and exotic animals',
            'category' => 'wildlife',
        ],
        [
            'src' => 'images/collage/relaxation.svg',
            'alt' => 'Peaceful relaxation spaces with natural surroundings for wellness',
            'category' => 'relaxation',
        ],
        [
            'src' => 'images/collage/luxury.svg',
            'alt' => 'Sustainable luxury accommodations blending comfort with eco-consciousness',
            'category' => 'luxury',
        ],
    ],
    'navigation' => [
        ['label' => 'Home', 'href' => '#hero'],
        ['label' => 'Experience', 'href' => '#experience'],
        ['label' => 'Gallery', 'href' => '#gallery'],
        ['label' => 'Contact', 'href' => '#contact'],
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="">
    
    <!-- SEO Meta Tags -->
    <title><?= htmlspecialchars($pageData['title']) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageData['description']) ?>">
    <meta name="keywords" content="eco-luxury tourism, sustainable travel, Costa Rica, Pura Vida, nature retreat, eco resort">
    
    <!-- Open Graph / Facebook Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
    <meta property="og:title" content="The Waterfall - Where Nature Meets Wonder">
    <meta property="og:description" content="Immerse yourself in eco-luxury tourism with stunning waterfalls, lush forests, and sustainable adventures.">
    <meta property="og:image" content="images/og-image.svg">
    
    <!-- Structured Data JSON-LD for TouristAttraction -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "TouristAttraction",
        "name": "The Waterfall",
        "description": "Eco-luxury tourism destination offering sustainable travel experiences",
        "image": [
            "images/collage/waterfall.svg",
            "images/collage/forest.svg",
            "images/collage/aerial.svg"
        ],
        "address": {
            "@type": "PostalAddress",
            "addressCountry": "CR"
        }
    }
    </script>
    
    <!-- Font Preconnect -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Styles -->
    <link rel="stylesheet" href="build/assets/app-C97zBPQw.css">
</head>
<body class="antialiased">
    <!-- Navigation -->
    <nav class="navbar" role="navigation" aria-label="Main navigation">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="#hero" aria-label="The Waterfall - Home">
                    🌿 The Waterfall
                </a>
            </div>
            
            <button 
                class="nav-toggle" 
                aria-label="Toggle navigation menu" 
                aria-expanded="false"
                aria-controls="nav-menu"
                type="button"
            >
                <span class="hamburger-icon" aria-hidden="true">☰</span>
            </button>
            
            <ul class="nav-menu" id="nav-menu" role="list">
                <?php foreach ($pageData['navigation'] as $item): ?>
                    <li role="listitem">
                        <a href="<?= htmlspecialchars($item['href']) ?>" class="nav-link">
                            <?= htmlspecialchars($item['label']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </nav>
    
    <main>
        <!-- Hero Section -->
        <section class="hero-section" id="hero">
            <div class="hero-content">
                <h1 class="hero-heading">🌿 Godong Ijo - Eco-Luxury Tourism</h1>
                <p class="hero-tagline gradient-text">Where Nature Meets Wonder</p>
                <div class="hero-cta">
                    <button class="btn btn-primary" type="button">Get Inspired</button>
                    <button class="btn btn-secondary" type="button">Start Planning</button>
                </div>
            </div>
            <div class="hero-background"></div>
        </section>

        <!-- Image Collage Section -->
        <section class="image-collage" id="gallery">
            <div class="container">
                <div class="collage-header">
                    <h2 class="section-heading text-center">Experience The Beauty</h2>
                    <p class="section-description text-center">Discover the diverse wonders of eco-luxury tourism</p>
                </div>
                
                <div class="collage-grid">
                    <?php foreach ($pageData['images'] as $image): ?>
                        <figure class="collage-item" data-category="<?= htmlspecialchars($image['category']) ?>">
                            <img 
                                src="<?= htmlspecialchars($image['src']) ?>"
                                alt="<?= htmlspecialchars($image['alt']) ?>" 
                                loading="lazy"
                                class="collage-image"
                            >
                            <figcaption class="collage-caption">
                                <span class="category-badge"><?= htmlspecialchars(ucfirst($image['category'])) ?></span>
                            </figcaption>
                        </figure>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Experience Section -->
        <section class="experience-section" id="experience">
            <div class="container">
                <div class="experience-content">
                    <h2 class="section-heading text-center">Discover Your Experience</h2>
                    <p class="section-description text-center">Immerse yourself in the perfect blend of luxury and nature</p>
                    
                    <div class="experience-grid">
                        <div class="experience-card">
                            <div class="experience-icon">🌳</div>
                            <h3>Nature Immersion</h3>
                            <p>Explore pristine forests and discover diverse ecosystems</p>
                        </div>
                        <div class="experience-card">
                            <div class="experience-icon">🏔️</div>
                            <h3>Adventure Activities</h3>
                            <p>Zip-lining, hiking, and eco-friendly adventures</p>
                        </div>
                        <div class="experience-card">
                            <div class="experience-icon">🧘</div>
                            <h3>Wellness & Relaxation</h3>
                            <p>Rejuvenate in harmony with nature</p>
                        </div>
                        <div class="experience-card">
                            <div class="experience-icon">🌟</div>
                            <h3>Sustainable Luxury</h3>
                            <p>Comfort meets environmental consciousness</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section class="contact-section" id="contact">
            <div class="container">
                <div class="contact-content">
                    <h2 class="section-heading text-center">Start Your Journey</h2>
                    <p class="section-description text-center">Get in touch to plan your eco-luxury adventure</p>
                    
                    <div class="contact-cta">
                        <a href="#contact" class="btn btn-primary">Book Your Experience</a>
                        <a href="#gallery" class="btn btn-secondary">View Gallery</a>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <!-- Scripts -->
    <script src="build/js/app-B4Alh14X.js"></script>
</body>
</html>
