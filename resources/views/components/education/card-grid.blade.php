@props([
    'cards' => [],
    'columns' => 3,
    'sectionId' => 'card-grid'
])

<div class="card-grid" id="{{ $sectionId }}" data-columns="{{ $columns }}">
    @foreach($cards as $card)
        <x-education.program-card 
            :card="$card"
            :section-id="$sectionId"
        />
    @endforeach
</div>

<style>
.card-grid {
  display: grid;
  gap: 24px;
  width: 100%;
}

.card-grid[data-columns="3"] {
  grid-template-columns: repeat(3, 1fr);
}

.card-grid[data-columns="2"] {
  grid-template-columns: repeat(2, 1fr);
}

@media (max-width: 1024px) and (min-width: 768px) {
  .card-grid[data-columns="3"] {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 767px) {
  .card-grid {
    display: none; /* Hidden on mobile, replaced by carousel */
  }
}
</style>
