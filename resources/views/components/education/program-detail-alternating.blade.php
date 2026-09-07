@props([
    'programs' => [],
    'sectionTitle' => 'Program Details',
])

<section class="program-detail-section" aria-labelledby="program-detail-title">
    <div class="container">
        <div class="section-header compact">
            <h2 class="section-title" id="program-detail-title">{{ $sectionTitle }}</h2>
        </div>

        <div class="program-detail-list">
            @foreach($programs as $index => $program)
                @php
                    $title = $program['title'] ?? 'Program Edukasi';
                    $isImageLeft = $index % 2 === 0;
                @endphp

                <article class="program-detail-item {{ $isImageLeft ? 'image-left' : 'image-right' }}">
                    <figure class="program-detail-image">
                        <picture>
                            <source srcset="{{ $program['image'] ?? asset('images/education/placeholder-blur.webp') }}" type="image/webp">
                            <img
                                src="{{ $program['imageFallback'] ?? $program['image'] ?? asset('images/education/placeholder-education.jpg') }}"
                                alt="{{ $program['imageAlt'] ?? $title }}"
                                loading="lazy"
                                onerror="this.src='{{ asset('images/education/placeholder-education.jpg') }}'"
                            >
                        </picture>
                    </figure>

                    <div class="program-detail-content">
                        <span class="program-detail-kicker">Program Edukasi</span>
                        <h3 class="program-detail-title">{{ $title }}</h3>
                        <p class="program-detail-description">{{ $program['description'] ?? '' }}</p>

                        <x-education.whatsapp-button
                            :message="$program['whatsappMessage'] ?? 'Halo, saya tertarik dengan program ' . $title . ' di Godong Ijo'"
                            label="Tanya Program"
                            variant="secondary"
                        />
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
