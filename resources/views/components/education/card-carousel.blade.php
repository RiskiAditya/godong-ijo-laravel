{{--
  Education Card Carousel Component
  
  Purpose: Display cards in swipeable carousel format for mobile view
  
  Props:
  - cards: array - Array of card data objects
  - carouselId: string - Unique identifier for this carousel instance
  
  Features:
  - Touch swipe gesture support
  - Navigation arrow buttons
  - Pagination dots with active state
  - Alpine.js state management
  - Mobile-only display (< 768px)
  
  Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 6.6, 6.7, 6.8
--}}

@props([
    'cards' => [],
    'carouselId' => 'carousel'
])

<div 
    class="card-carousel" 
    id="{{ $carouselId }}"
    x-data="educationCarousel({{ count($cards) }})"
    x-init="init()"
>
    {{-- Carousel Track Container --}}
    <div class="carousel-track-container">
        <div 
            class="carousel-track"
            :style="`transform: translateX(-${currentIndex * 100}%)`"
            @touchstart="handleTouchStart($event)"
            @touchmove="handleTouchMove($event)"
            @touchend="handleTouchEnd($event)"
        >
            @foreach($cards as $index => $card)
                <div class="carousel-slide">
                    <x-education.program-card 
                        :card="$card"
                        :section-id="$carouselId"
                    />
                </div>
            @endforeach
        </div>
    </div>
    
    {{-- Navigation Buttons --}}
    <button 
        class="carousel-nav carousel-prev"
        @click="prevSlide()"
        :disabled="currentIndex === 0"
        :aria-label="`Previous slide, currently on slide ${currentIndex + 1} of ${totalSlides}`"
        x-show="currentIndex > 0"
        x-transition
    >
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
    
    <button 
        class="carousel-nav carousel-next"
        @click="nextSlide()"
        :disabled="currentIndex === totalSlides - 1"
        :aria-label="`Next slide, currently on slide ${currentIndex + 1} of ${totalSlides}`"
        x-show="currentIndex < totalSlides - 1"
        x-transition
    >
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
    
    {{-- Pagination Dots --}}
    <div class="carousel-pagination" role="tablist" aria-label="Carousel pagination">
        @foreach($cards as $index => $card)
            <button 
                class="pagination-dot"
                :class="{ 'active': currentIndex === {{ $index }} }"
                @click="goToSlide({{ $index }})"
                role="tab"
                :aria-label="`Go to slide {{ $index + 1 }}`"
                :aria-selected="currentIndex === {{ $index }} ? 'true' : 'false'"
                :aria-current="currentIndex === {{ $index }} ? 'true' : 'false'"
            ></button>
        @endforeach
    </div>
</div>
