@props([
    'photo' => asset('images/education/testimonial-photo.webp'),
    'photoFallback' => asset('images/education/testimonial-photo.jpg'),
    'photoAlt' => 'Aktivitas wisata edukasi di Godong Ijo',
    'quote' => 'Ada banyak wahana yang ditawarkan dan cocok untuk karyawisata sekolah.',
    'source' => '- kapanlagi.com',
])

<section class="testimonial-section" aria-labelledby="testimonial-title">
    <div class="container">
        <div class="section-header compact">
            <h2 class="section-title" id="testimonial-title">Cerita Pengunjung</h2>
        </div>

        <article class="testimonial-card-dark">
            <figure class="testimonial-photo">
                <picture>
                    <source srcset="{{ $photo }}" type="image/webp">
                    <img
                        src="{{ $photoFallback }}"
                        alt="{{ $photoAlt }}"
                        loading="lazy"
                        onerror="this.src='{{ asset('images/education/placeholder-education.jpg') }}'"
                    >
                </picture>
            </figure>

            <div class="testimonial-copy">
                <svg class="quote-icon" width="44" height="44" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 7H5.5C4.67 7 4 7.67 4 8.5V12C4 14.76 6.24 17 9 17V14C7.9 14 7 13.1 7 12H9C9.83 12 10.5 11.33 10.5 10.5V8.5C10.5 7.67 9.83 7 9 7ZM18.5 7H15C14.17 7 13.5 7.67 13.5 8.5V12C13.5 14.76 15.74 17 18.5 17V14C17.4 14 16.5 13.1 16.5 12H18.5C19.33 12 20 11.33 20 10.5V8.5C20 7.67 19.33 7 18.5 7Z" fill="currentColor"/>
                </svg>
                <blockquote class="testimonial-quote">
                    <p>{{ $quote }}</p>
                    <cite>{{ $source }}</cite>
                </blockquote>
            </div>
        </article>
    </div>
</section>
