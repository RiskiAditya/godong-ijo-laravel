@props([
    'iconName' => '',
    'backgroundColor' => '#FAF6ED',
    'borderRadius' => '8px',
    'size' => '48px',
    'iconSize' => '24px',
    'iconColor' => '#1E4636'
])

<div 
    class="icon-badge" 
    style="
        background-color: {{ $backgroundColor }};
        border-radius: {{ $borderRadius }};
        width: {{ $size }};
        height: {{ $size }};
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    "
    role="img"
    aria-label="{{ $iconName }}"
>
    <i 
        class="ti {{ $iconName }}" 
        style="
            font-size: {{ $iconSize }};
            color: {{ $iconColor }};
            line-height: 1;
        "
        aria-hidden="true"
    ></i>
</div>
