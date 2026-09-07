@extends('layouts.admin')

@section('title', 'Paket Wisata')
@section('page-title', 'Paket Wisata')

@section('content')
<div class="page-head">
    <div>
        <h1>Paket Wisata</h1>
        <p class="page-sub">Kelola paket wisata dan layanan yang tersedia</p>
    </div>
    <div class="head-actions">
        <button class="btn-ghost" onclick="window.location.reload()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/>
            </svg>
            <span>Refresh</span>
        </button>
        <button class="btn-solid" onclick="window.location.href='{{ route('admin.paket-wisata.create') }}'">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            <span>Tambah Paket Baru</span>
        </button>
    </div>
</div>

<div class="panel">
    <div class="panel-head">
        <h2>Semua Paket <span class="count">({{ $pakets->total() }})</span></h2>
        <div class="tabs">
            <a href="{{ route('admin.paket-wisata.index') }}" class="tab {{ !request('status') ? 'active' : '' }}">Semua</a>
            <a href="{{ route('admin.paket-wisata.index', ['status' => 'active']) }}" class="tab {{ request('status') === 'active' ? 'active' : '' }}">Aktif</a>
            <a href="{{ route('admin.paket-wisata.index', ['status' => 'inactive']) }}" class="tab {{ request('status') === 'inactive' ? 'active' : '' }}">Nonaktif</a>
        </div>
    </div>
    
    <div class="subbar">
        <form method="GET" action="{{ route('admin.paket-wisata.index') }}" class="search" style="margin: 0;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="7"/>
                <path d="m21 21-4.3-4.3"/>
            </svg>
            <input type="text" name="search" placeholder="Cari nama paket…" value="{{ request('search') }}">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
        </form>
        
        <select name="jenis" onchange="filterByJenis(this.value)" style="padding: 7px 10px; border: 1px solid var(--line); border-radius: 6px; font-size: 12.5px; font-family: inherit; margin-left: auto;">
            <option value="">Semua Jenis</option>
            <option value="The Waterfall Resto" {{ request('jenis') === 'The Waterfall Resto' ? 'selected' : '' }}>The Waterfall Resto</option>
            <option value="Private Room" {{ request('jenis') === 'Private Room' ? 'selected' : '' }}>Private Room</option>
            <option value="Fishing Lake" {{ request('jenis') === 'Fishing Lake' ? 'selected' : '' }}>Fishing Lake</option>
        </select>
    </div>

    @if($pakets->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 60px;">Foto</th>
                    <th>Nama Paket</th>
                    <th>Jenis</th>
                    <th class="num">Harga</th>
                    <th class="num">Kuota</th>
                    <th class="num">Total Booking</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($pakets as $paket)
                <tr>
                    <td>
                        @if($paket->foto)
                            <img src="{{ asset($paket->foto) }}" alt="{{ $paket->nama_paket }}" style="width: 48px; height: 48px; object-fit: cover; border-radius: 6px; border: 1px solid var(--line);">
                        @else
                            <div style="width: 48px; height: 48px; background: var(--bg); border-radius: 6px; border: 1px solid var(--line); display: flex; align-items: center; justify-content: center;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color: var(--ink-45);">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <path d="M21 15l-5-5L5 21"/>
                                </svg>
                            </div>
                        @endif
                    </td>
                    <td>
                        <div>
                            <div style="font-weight: 600; margin-bottom: 2px;">{{ $paket->nama_paket }}</div>
                            <div style="font-size: 11px; color: var(--ink-45);">{{ Str::limit($paket->deskripsi, 50) }}</div>
                        </div>
                    </td>
                    <td>{{ $paket->jenis_paket }}</td>
                    <td class="num">Rp {{ number_format($paket->harga, 0, ',', '.') }}</td>
                    <td class="num">{{ $paket->kuota }}</td>
                    <td class="num">{{ $paket->pemesanan_count ?? 0 }}</td>
                    <td>
                        @if($paket->is_active)
                            <span class="status lunas">
                                <span class="status-dot"></span>
                                Aktif
                            </span>
                        @else
                            <span class="status pending" style="color: var(--ink-45);">
                                <span class="status-dot" style="background: var(--ink-45);"></span>
                                Nonaktif
                            </span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 4px;">
                            <button class="row-action" onclick="window.location.href='{{ route('admin.paket-wisata.show', $paket->id) }}'" title="Lihat detail">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                            <button class="row-action" onclick="window.location.href='{{ route('admin.paket-wisata.edit', $paket->id) }}'" title="Edit paket">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.1 2.1 0 0 1 3 3L12 15l-4 1 1-4Z"/>
                                </svg>
                            </button>
                            <button class="row-action danger" onclick="confirmDelete({{ $paket->id }}, '{{ $paket->nama_paket }}')" title="Hapus paket">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="panel-foot">
            <span>Menampilkan {{ $pakets->firstItem() }}-{{ $pakets->lastItem() }} dari {{ $pakets->total() }} paket</span>
            <div class="pager">
                @if($pakets->onFirstPage())
                    <button disabled>‹</button>
                @else
                    <button onclick="window.location.href='{{ $pakets->previousPageUrl() }}'">‹</button>
                @endif
                
                @foreach($pakets->getUrlRange(1, min($pakets->lastPage(), 5)) as $page => $url)
                    <button class="{{ $page == $pakets->currentPage() ? 'active' : '' }}" onclick="window.location.href='{{ $url }}'">{{ $page }}</button>
                @endforeach
                
                @if($pakets->hasMorePages())
                    <button onclick="window.location.href='{{ $pakets->nextPageUrl() }}'">›</button>
                @else
                    <button disabled>›</button>
                @endif
            </div>
        </div>
    @else
        <div class="placeholder-panel">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M21 8 12 3 3 8l9 5 9-5Z"/>
                <path d="M3 8v8l9 5 9-5V8"/>
            </svg>
            <h3>Tidak Ada Paket</h3>
            <p>
                @if(request('search') || request('status') || request('jenis'))
                    Tidak ada paket yang sesuai dengan filter Anda.
                @else
                    Belum ada paket wisata yang terdaftar.
                @endif
            </p>
            @if(request()->hasAny(['search', 'status', 'jenis']))
                <a href="{{ route('admin.paket-wisata.index') }}" class="btn-solid">Reset Filter</a>
            @else
                <button class="btn-solid" onclick="window.location.href='{{ route('admin.paket-wisata.create') }}'">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Tambah Paket Pertama
                </button>
            @endif
        </div>
    @endif
</div>

@push('scripts')
<script>
function filterByJenis(jenis) {
    const url = new URL(window.location.href);
    if (jenis) {
        url.searchParams.set('jenis', jenis);
    } else {
        url.searchParams.delete('jenis');
    }
    window.location.href = url.toString();
}

function confirmDelete(paketId, namapaket) {
    if (!confirm(`Apakah Anda yakin ingin menghapus paket "${namapaket}"? Tindakan ini tidak dapat dibatalkan.`)) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/packages/${paketId}`;
    
    const csrfField = document.createElement('input');
    csrfField.type = 'hidden';
    csrfField.name = '_token';
    csrfField.value = document.querySelector('meta[name="csrf-token"]').content;
    
    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'DELETE';
    
    form.appendChild(csrfField);
    form.appendChild(methodField);
    document.body.appendChild(form);
    form.submit();
}

// Auto-submit search on Enter
document.querySelector('.search input[name="search"]').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        this.closest('form').submit();
    }
});
</script>
@endpush
@endsection
