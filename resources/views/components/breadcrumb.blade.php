@props(['items' => []])

@if(count($items) > 1)
<nav class="breadcrumb-nav" aria-label="Breadcrumb">
    <div class="container">
        <ol class="breadcrumb">
            @foreach($items as $index => $item)
                @if($item['current'])
                    <li class="breadcrumb-item active" aria-current="page">
                        <span>{{ $item['label'] }}</span>
                    </li>
                @else
                    <li class="breadcrumb-item">
                        @if($item['url'])
                            <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                        @else
                            <span>{{ $item['label'] }}</span>
                        @endif
                    </li>
                @endif
                
                @if(!$loop->last)
                    <li class="breadcrumb-separator" aria-hidden="true">/</li>
                @endif
            @endforeach
        </ol>
    </div>
</nav>

{{-- BreadcrumbList Structured Data --}}
@if(isset($structuredData))
<script type="application/ld+json">
{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endif
@endif
