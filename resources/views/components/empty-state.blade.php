@props(['message', 'submessage', 'ctaText', 'ctaLink'])

<div class="empty-state">
    {{-- Empty State Icon --}}
    <svg class="empty-icon" xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    
    {{-- Message --}}
    <h3 class="empty-message">{{ $message }}</h3>
    
    {{-- Sub-message --}}
    <p class="empty-submessage">{{ $submessage }}</p>
    
    {{-- CTA Button --}}
    <a href="{{ $ctaLink }}" class="btn btn-primary empty-cta">{{ $ctaText }}</a>
</div>
