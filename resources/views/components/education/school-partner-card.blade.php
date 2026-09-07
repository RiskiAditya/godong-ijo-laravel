@props([
    'partner',
])

@php
    $name = $partner->name ?? 'Sekolah Mitra';
    $level = $partner->level ?? null;
    $logoPath = $partner->logo_path ?? null;
    
    // Generate initials for fallback
    $words = explode(' ', $name);
    $initials = '';
    if (count($words) >= 2) {
        $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
    } else {
        $initials = strtoupper(substr($name, 0, 2));
    }
@endphp

<article class="school-partner-card">
    <div class="school-partner-logo-container">
        <div class="school-partner-logo-wrapper" aria-hidden="true">
            @if($logoPath)
                <img
                    src="{{ asset($logoPath) }}"
                    alt="Logo {{ $name }}"
                    loading="lazy"
                    class="school-partner-logo-img"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'"
                >
                <div class="school-partner-logo-fallback" style="display: none;">
                    <span class="school-partner-initials">{{ $initials }}</span>
                </div>
            @else
                <div class="school-partner-logo-fallback">
                    <span class="school-partner-initials">{{ $initials }}</span>
                </div>
            @endif
        </div>
    </div>

    <div class="school-partner-info">
        <h3 class="school-partner-name">{{ $name }}</h3>
        @if($level)
            <span class="school-partner-level">
                <i class="ti ti-school" aria-hidden="true"></i>
                {{ $level }}
            </span>
        @endif
    </div>
</article>
