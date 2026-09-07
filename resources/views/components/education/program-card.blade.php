@props([
    'card' => [],
    'sectionId' => 'default',
])

@php
    $title = $card['title'] ?? 'Program Edukasi';
    $description = $card['description'] ?? null;
    $icon = $card['icon'] ?? null;
    $hasImage = isset($card['image']);
    $isTablerIcon = is_string($icon) && str_starts_with($icon, 'ti-');
@endphp

<article class="program-card" data-section="{{ $sectionId }}">
    @if($hasImage)
        <figure class="card-media card-image-wrapper">
            <picture>
                <source srcset="{{ $card['image'] }}" type="image/webp">
                <img
                    src="{{ $card['imageFallback'] ?? $card['image'] }}"
                    alt="{{ $card['imageAlt'] ?? $title }}"
                    loading="lazy"
                    class="card-image"
                    onerror="this.src='{{ asset('images/education/placeholder-education.jpg') }}'"
                >
            </picture>
        </figure>
    @elseif($icon)
        <div class="card-media card-icon-wrapper">
            @if($isTablerIcon)
                <x-education.icon-badge
                    :icon-name="$icon"
                    :background-color="$card['iconStyle']['backgroundColor'] ?? '#FAF6ED'"
                    :border-radius="$card['iconStyle']['borderRadius'] ?? '8px'"
                    size="80px"
                    icon-size="40px"
                />
            @else
                <span class="card-icon" aria-hidden="true">{{ $icon }}</span>
            @endif
        </div>
    @endif

    <div class="card-content">
        <h3 class="card-title">{{ $title }}</h3>
        @if($description)
            <p class="card-description">{{ $description }}</p>
        @endif
    </div>

    <div class="card-action">
        <x-education.whatsapp-button
            :message="$card['whatsappMessage'] ?? 'Halo, saya tertarik dengan informasi program ' . $title . ' di Godong Ijo'"
            label="Hubungi via WhatsApp"
            variant="primary"
        />
    </div>
</article>
