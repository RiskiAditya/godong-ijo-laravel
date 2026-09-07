@props([
    'title' => '',
    'featured' => [],
    'others' => [],
    'sectionId' => 'program-group',
])

<section class="program-group" aria-labelledby="{{ $sectionId }}-title">
    @if($title)
        <div class="section-header compact">
            <h2 class="section-title" id="{{ $sectionId }}-title">{{ $title }}</h2>
        </div>
    @endif

    @if(count($featured) > 0)
        <div class="program-group-featured">
            @foreach($featured as $program)
                <x-education.program-card
                    :card="$program"
                    :section-id="$sectionId"
                />
            @endforeach
        </div>
    @endif
</section>
