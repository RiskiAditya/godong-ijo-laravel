{{--
    WhatsApp Button Component
    
    A standardized WhatsApp contact button with pre-filled messages for the Education Tourism page.
    Includes mobile detection to open native WhatsApp app on mobile devices.
    
    Props:
    - message (string): Pre-filled WhatsApp message text (default: generic inquiry message)
    - phoneNumber (string): WhatsApp phone number in international format (default: from config)
    - label (string): Button text label (default: 'Hubungi via WhatsApp')
    - variant (string): Button style variant - 'primary', 'gold', or 'secondary' (default: 'primary')
    
    Variants:
    - primary: WhatsApp green (#25D366) - Standard WhatsApp color
    - gold: Brand gold color (var(--gold)) - For premium/featured programs
    - secondary: Outlined style with transparent background
    
    Mobile Behavior:
    - On mobile devices: Attempts to open native WhatsApp app first, falls back to WhatsApp Web
    - On desktop: Opens WhatsApp Web in new tab
    
    Usage:
    <x-education.whatsapp-button 
        message="Halo, saya tertarik dengan program Learning About Animals"
        label="Hubungi Kami"
        variant="gold"
    />
--}}

@props([
    'message' => 'Halo, saya tertarik dengan informasi program di Godongijo',
    'phoneNumber' => config('app.whatsapp_number', '6281234567890'),
    'label' => 'Hubungi via WhatsApp',
    'variant' => 'primary'
])

@php
    $encodedMessage = urlencode($message);
    // Mobile detection: Use api.whatsapp.com for mobile (native app), wa.me for desktop (web)
    // The WhatsApp API will handle device detection automatically, but we can help with link structure
    $whatsappUrl = "https://wa.me/{$phoneNumber}?text={$encodedMessage}";
@endphp

<a 
    href="{{ $whatsappUrl }}" 
    target="_blank"
    rel="noopener noreferrer"
    class="whatsapp-btn whatsapp-btn-{{ $variant }}"
    x-data="{ 
        handleClick(event) {
            // Track WhatsApp clicks if analytics available
            if (typeof gtag !== 'undefined') {
                gtag('event', 'whatsapp_click', {
                    'event_category': 'engagement',
                    'event_label': '{{ addslashes($message) }}'
                });
            }
            
            // Mobile detection for better UX
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            
            if (isMobile) {
                // On mobile, try to open native app first
                event.preventDefault();
                const mobileUrl = 'whatsapp://send?phone={{ $phoneNumber }}&text={{ $encodedMessage }}';
                window.location.href = mobileUrl;
                
                // Fallback to web version if app not installed
                setTimeout(() => {
                    window.open('{{ $whatsappUrl }}', '_blank');
                }, 1000);
            }
            // On desktop, default behavior (WhatsApp Web) works fine
        }
    }"
    @click="handleClick($event)"
>
    <svg class="whatsapp-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M17 9.5C17 13.6421 13.6421 17 9.5 17C8.23 17 7.03 16.69 6 16.14L3 17L3.86 14.14C3.28 13.09 3 11.84 3 10.5C3 6.35786 6.35786 3 10.5 3C14.6421 3 17 6.35786 17 9.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M13.5 11.5C13.5 11.5 13 11 12.5 11C12 11 11.5 11.5 11.5 11.5C11.5 11.5 10.5 11 9.5 10C8.5 9 8 8 8 8C8 8 8.5 7.5 8.5 7C8.5 6.5 8 6 8 6C8 6 7.5 5 7 5.5C6.5 6 6.5 6.5 6.5 7C6.5 8 7.5 10 9 11.5C10.5 13 12.5 13.5 13 13.5C13.5 13.5 14 13.5 14.5 13C15 12.5 14.5 12 14.5 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <span>{{ $label }}</span>
</a>
