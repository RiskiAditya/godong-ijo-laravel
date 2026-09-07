@props([
    'heroImages' => [],
])

<section class="hero-section" id="hero">
    {{-- Bento Grid with Images + Content --}}
    <div class="hero-bento-grid">
        {{-- Bento Image Items --}}
        @foreach($heroImages as $image)
            <div class="hero-bento-item {{ $image['class'] }}">
                <img
                    src="{{ $image['src'] }}"
                    alt="{{ $image['alt'] }}"
                    loading="{{ $loop->index < 3 ? 'eager' : 'lazy' }}"
                    width="800"
                    height="600"
                >
                @if($image['class'] === 'bento-4')
                    <div class="bento-glass-overlay"></div>
                @endif
            </div>
        @endforeach

        {{-- Hero Content (Center of Grid) --}}
        <div class="hero-content">
            <span class="hero-badge">
                ✨ The Waterfall Resto n Monster Fish Fishing Lake
            </span>

            <h1 class="hero-heading">
                Nikmati Kuliner & Rekreasi
                <br>
                <span class="text-green">di Tengah Alam yang Asri</span>
            </h1>

            <p class="hero-description">
                Kuliner premium dengan konsep Dine in Nature dan tantangan seru Monster Fish Fishing. 
                Satu lokasi, pengalaman tak terlupakan.
            </p>

            <div class="hero-cta">
                <a href="#destinations" class="btn btn-primary">Jelajahi Fasilitas</a>
                <a href="#contact" class="btn btn-glass">Hubungi Kami</a>
            </div>
        </div>
    </div>
</section>
