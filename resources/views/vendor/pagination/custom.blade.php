@if ($paginator->hasPages())
    <nav class="custom-pagination" role="navigation" aria-label="Pagination Navigation">
        <div class="pagination-info">
            <p class="pagination-text">
                Menampilkan <span class="font-semibold">{{ $paginator->firstItem() }}</span> 
                sampai <span class="font-semibold">{{ $paginator->lastItem() }}</span> 
                dari <span class="font-semibold">{{ $paginator->total() }}</span> hasil
            </p>
        </div>

        <div class="pagination-controls">
            {{-- Previous Button --}}
            @if ($paginator->onFirstPage())
                <span class="pagination-btn disabled" aria-disabled="true" aria-label="Previous">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                    <span>Sebelumnya</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="pagination-btn" rel="prev" aria-label="Previous">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                    <span>Sebelumnya</span>
                </a>
            @endif

            {{-- Page Numbers with Custom Logic --}}
            <div class="pagination-numbers">
                @php
                    $currentPage = $paginator->currentPage();
                    $lastPage = $paginator->lastPage();
                    $showPages = [];
                    
                    // Always show first 2 pages
                    if ($lastPage >= 1) $showPages[] = 1;
                    if ($lastPage >= 2) $showPages[] = 2;
                    
                    // Show pages around current page
                    for ($i = max(3, $currentPage - 1); $i <= min($lastPage - 1, $currentPage + 1); $i++) {
                        if (!in_array($i, $showPages)) {
                            $showPages[] = $i;
                        }
                    }
                    
                    // Always show last page
                    if ($lastPage > 2 && !in_array($lastPage, $showPages)) {
                        $showPages[] = $lastPage;
                    }
                    
                    sort($showPages);
                @endphp

                @foreach ($showPages as $index => $page)
                    {{-- Show dots if there's a gap --}}
                    @if ($index > 0 && $page - $showPages[$index - 1] > 1)
                        <span class="pagination-dots" aria-disabled="true">...</span>
                    @endif
                    
                    {{-- Page number --}}
                    @if ($page == $currentPage)
                        <span class="pagination-number active" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $paginator->url($page) }}" class="pagination-number" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                    @endif
                @endforeach
            </div>

            {{-- Next Button --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="pagination-btn" rel="next" aria-label="Next">
                    <span>Selanjutnya</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            @else
                <span class="pagination-btn disabled" aria-disabled="true" aria-label="Next">
                    <span>Selanjutnya</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
