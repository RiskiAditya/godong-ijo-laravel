<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Main Navigation Menu Structure
    |--------------------------------------------------------------------------
    |
    | This array defines the main navigation menu structure for the Godong Ijo
    | website. It supports nested menu structures up to 2 levels (parent → child).
    |
    | Structure:
    | - label: Display text for the menu item
    | - route: Named Laravel route (null for dropdown parents)
    | - url: Fallback URL for the menu item
    | - slug: Route parameter for dynamic routes (optional)
    | - children: Array of child menu items or 'dynamic' marker (optional)
    | - icon: Optional icon class (optional)
    |
    */

    'main' => [
        [
            'label' => 'Beranda',
            'route' => 'landing',
            'url' => '/',
            'slug' => null,
            'children' => null,
        ],
        [
            'label' => 'Destinasi',
            'route' => null,
            'url' => '#',
            'slug' => null,
            'children' => [
                [
                    'label' => 'The Waterfall',
                    'route' => 'destination.show',
                    'url' => '/destinasi/the-waterfall',
                    'slug' => 'the-waterfall',
                ],
                [
                    'label' => 'Monster Fish',
                    'route' => 'destination.show',
                    'url' => '/destinasi/monster-fish',
                    'slug' => 'monster-fish',
                ],
            ],
        ],
        [
            'label' => 'Paket Wisata',
            'route' => null,
            'url' => '#',
            'slug' => null,
            'children' => [
                [
                    'label' => 'The Waterfall Resto',
                    'route' => 'packages.category',
                    'url' => '/paket/the-waterfall-resto',
                    'slug' => 'the-waterfall-resto',
                ],
                [
                    'label' => 'Private Room',
                    'route' => 'packages.category',
                    'url' => '/paket/private-room',
                    'slug' => 'private-room',
                ],
                [
                    'label' => 'Fishing Lake',
                    'route' => 'packages.category',
                    'url' => '/paket/fishing-lake',
                    'slug' => 'fishing-lake',
                ],
            ],
        ],
        [
            'label' => 'Wisata Edukasi',
            'route' => 'education',
            'url' => '/wisata-edukasi',
            'slug' => null,
            'children' => null,
        ],
        [
            'label' => 'Kontak',
            'route' => 'contact',
            'url' => '/kontak',
            'slug' => null,
            'children' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Call-to-Action Button Configuration
    |--------------------------------------------------------------------------
    |
    | This defines the CTA button displayed in the navigation bar.
    |
    | Structure:
    | - label: Button display text
    | - action: JavaScript function name to call when clicked
    |
    */

    'cta' => [
        'label' => 'Pesan Sekarang',
        'action' => 'openBookingModal',
    ],
];
