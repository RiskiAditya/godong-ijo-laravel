@props([
    'variant' => 'primary',
    'href' => '#',
    'type' => 'button'
])

@if($type === 'link')
  <a href="{{ $href }}" {{ $attributes->merge(['class' => "btn btn-{$variant}"]) }}>
    {{ $slot }}
  </a>
@else
  <button {{ $attributes->merge(['class' => "btn btn-{$variant}", 'type' => 'button']) }}>
    {{ $slot }}
  </button>
@endif
