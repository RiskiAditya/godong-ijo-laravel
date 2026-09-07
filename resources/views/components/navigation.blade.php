@props(['items' => [], 'cta' => [], 'currentRoute' => ''])

@php
    if ($items instanceof \Illuminate\Support\Collection) {
        $items = $items->toArray();
    }

    $items = is_array($items) ? $items : [];

    $items = array_map(function ($item) {
        if (!is_array($item)) {
            return [];
        }

        if (isset($item['href']) && !isset($item['url'])) {
            $item['url'] = $item['href'];
        }

        if (!isset($item['children']) || !is_array($item['children'])) {
            $item['children'] = [];
        } else {
            $item['children'] = array_map(function ($child) {
                if (!is_array($child)) {
                    return [];
                }

                if (isset($child['href']) && !isset($child['url'])) {
                    $child['url'] = $child['href'];
                }

                return $child;
            }, $item['children']);
        }

        return $item;
    }, $items);
@endphp

<nav class="navbar" role="navigation" aria-label="Main navigation" x-data="navigationDropdown()">
    <div class="nav-container">
        {{-- Brand Logo --}}
        <a href="{{ url('/') }}" class="nav-brand" aria-label="Godong Ijo - Home">
            <img src="{{ asset('images/placeholders/The-Waterfall-Logo-removebg-preview.png') }}" alt="Godong Ijo Logo" class="nav-brand-logo">
        </a>

        {{-- Desktop Navigation Menu --}}
        <ul class="nav-menu desktop-nav" id="nav-menu" role="list">
            @foreach($items as $index => $item)
                @php
                    $itemLabel = $item['label'] ?? '';
                    $itemChildren = $item['children'] ?? [];
                    $itemUrl = $item['url'] ?? $item['href'] ?? '#';
                    $itemRoute = $item['route'] ?? '';
                    $itemSlug = $item['slug'] ?? null;
                @endphp

                @if(is_array($itemChildren) && count($itemChildren) > 0)
                    {{-- Dropdown Menu Item --}}
                    <li class="nav-item has-dropdown" role="listitem" x-data="{ open: false }">
                        <button 
                            class="nav-link dropdown-trigger {{ app('App\Services\NavigationService')->getActiveParent($currentRoute) === $itemLabel ? 'active' : '' }}"
                            @mouseenter="openDropdown('{{ $index }}')"
                            @mouseleave="closeWithDelay()"
                            @click="toggleDropdown('{{ $index }}')"
                            @keydown.escape="closeDropdown()"
                            @keydown.arrow-down.prevent="focusFirstChild($event)"
                            aria-haspopup="true"
                            :aria-expanded="activeDropdown === '{{ $index }}'"
                        >
                            {{ $itemLabel }}
                            <svg class="dropdown-icon" width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M1 1L6 6L11 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        
                        <ul 
                            class="dropdown-menu" 
                            x-show="activeDropdown === '{{ $index }}'"
                            x-transition:enter="dropdown-enter"
                            x-transition:enter-start="dropdown-enter-start"
                            x-transition:enter-end="dropdown-enter-end"
                            x-transition:leave="dropdown-leave"
                            x-transition:leave-start="dropdown-leave-start"
                            x-transition:leave-end="dropdown-leave-end"
                            @mouseenter="clearCloseTimeout()"
                            @mouseleave="closeWithDelay()"
                            role="menu"
                        >
                            @foreach($itemChildren as $child)
                                @php
                                    $childUrl = data_get($child, 'url', data_get($child, 'href', '#'));
                                    $childRoute = data_get($child, 'route', '');
                                    $childSlug = data_get($child, 'slug');
                                @endphp
                                <li role="presentation">
                                    <a 
                                        href="{{ $childUrl }}" 
                                        class="dropdown-item {{ app('App\Services\NavigationService')->isActive($childRoute, $childSlug) ? 'active' : '' }}"
                                        role="menuitem"
                                        @keydown.arrow-down.prevent="focusNext($event)"
                                        @keydown.arrow-up.prevent="focusPrev($event)"
                                        @keydown.escape="closeDropdown()"
                                    >
                                        {{ data_get($child, 'label', '') }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @else
                    {{-- Regular Menu Item --}}
                    <li class="nav-item" role="listitem">
                        <a 
                            href="{{ $itemUrl }}" 
                            class="nav-link {{ app('App\Services\NavigationService')->isActive($itemRoute, $itemSlug) ? 'active' : '' }}"
                            @if(app('App\Services\NavigationService')->isActive($itemRoute, $itemSlug))
                                aria-current="page"
                            @endif
                        >
                            {{ $itemLabel }}
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>

        {{-- CTA Button --}}
        @if(isset($cta['label']) && isset($cta['action']))
            <button 
                class="nav-cta" 
                onclick="{{ $cta['action'] }}()"
                type="button"
            >
                {{ $cta['label'] }}
            </button>
        @endif

        {{-- Mobile Hamburger Toggle --}}
        <button
            class="nav-toggle"
            aria-label="Toggle navigation menu"
            :aria-expanded="mobileOpen"
            aria-controls="mobile-nav"
            @click="toggleMobile()"
            type="button"
        >
            <span class="hamburger-line" aria-hidden="true"></span>
            <span class="hamburger-line" aria-hidden="true"></span>
            <span class="hamburger-line" aria-hidden="true"></span>
        </button>
    </div>

    {{-- Mobile Navigation Menu --}}
    <div 
        class="mobile-nav" 
        id="mobile-nav"
        x-show="mobileOpen"
        x-transition:enter="mobile-enter"
        x-transition:enter-start="mobile-enter-start"
        x-transition:enter-end="mobile-enter-end"
        x-transition:leave="mobile-leave"
        x-transition:leave-start="mobile-leave-start"
        x-transition:leave-end="mobile-leave-end"
        @click.away="closeMobile()"
    >
        <ul class="mobile-menu" role="list">
            @foreach($items as $index => $item)
                @php
                    $itemLabel = $item['label'] ?? '';
                    $itemChildren = $item['children'] ?? [];
                    $itemUrl = $item['url'] ?? $item['href'] ?? '#';
                    $itemRoute = $item['route'] ?? '';
                    $itemSlug = $item['slug'] ?? null;
                @endphp

                @if(is_array($itemChildren) && count($itemChildren) > 0)
                    {{-- Mobile Accordion Item --}}
                    <li class="mobile-item has-accordion" role="listitem">
                        <button 
                            class="mobile-link accordion-trigger {{ app('App\Services\NavigationService')->getActiveParent($currentRoute) === $itemLabel ? 'active' : '' }}"
                            @click="toggleAccordion('{{ $index }}')"
                            :aria-expanded="activeAccordion === '{{ $index }}'"
                        >
                            {{ $itemLabel }}
                            <svg class="accordion-icon" width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M1 1L6 6L11 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        
                        <ul 
                            class="accordion-content"
                            x-show="activeAccordion === '{{ $index }}'"
                            x-collapse
                            role="list"
                        >
                            @foreach($itemChildren as $child)
                                @php
                                    $childUrl = data_get($child, 'url', data_get($child, 'href', '#'));
                                    $childRoute = data_get($child, 'route', '');
                                    $childSlug = data_get($child, 'slug');
                                @endphp
                                <li role="listitem">
                                    <a 
                                        href="{{ $childUrl }}" 
                                        class="mobile-sublink {{ app('App\Services\NavigationService')->isActive($childRoute, $childSlug) ? 'active' : '' }}"
                                    >
                                        {{ data_get($child, 'label', '') }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @else
                    {{-- Regular Mobile Item --}}
                    <li class="mobile-item" role="listitem">
                        <a 
                            href="{{ $itemUrl }}" 
                            class="mobile-link {{ app('App\Services\NavigationService')->isActive($itemRoute, $itemSlug) ? 'active' : '' }}"
                            @if(app('App\Services\NavigationService')->isActive($itemRoute, $itemSlug))
                                aria-current="page"
                            @endif
                        >
                            {{ $itemLabel }}
                        </a>
                    </li>
                @endif
            @endforeach
            
            {{-- Mobile CTA Button --}}
            @if(isset($cta['label']) && isset($cta['action']))
                <li class="mobile-item mobile-cta-item" role="listitem">
                    <button 
                        class="mobile-cta" 
                        onclick="{{ $cta['action'] }}(); closeMobile()"
                        type="button"
                    >
                        {{ $cta['label'] }}
                    </button>
                </li>
            @endif
        </ul>
    </div>
</nav>


