@extends('layouts.admin')

@section('title', 'Data Pelanggan')
@section('page-title', 'Pelanggan')

@section('content')
<div class="page-head">
    <div>
        <h1>Data Pelanggan</h1>
        <p class="page-sub">Daftar semua pelanggan yang pernah melakukan booking</p>
    </div>
    <div class="head-actions">
        <button class="btn-ghost" onclick="window.location.reload()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/>
            </svg>
            Refresh
        </button>
        <button class="btn-solid" onclick="window.print()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                <rect x="6" y="14" width="12" height="8"/>
            </svg>
            Export
        </button>
    </div>
</div>

<div class="panel">
    <div class="panel-head">
        <h2>Semua Pelanggan <span class="count">({{ $customers->total() }})</span></h2>
    </div>
    
    <div class="subbar">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="search" style="margin: 0;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="7"/>
                <path d="m21 21-4.3-4.3"/>
            </svg>
            <input type="text" name="search" placeholder="Cari nama, email, no HP…" value="{{ request('search') }}">
        </form>
    </div>

    @if($customers->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Pelanggan</th>
                    <th>Email</th>
                    <th>No. Handphone</th>
                    <th class="num">Total Booking</th>
                    <th>Tipe</th>
                    <th>Terdaftar</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $customer)
                <tr>
                    <td>
                        <div class="cust">
                            <div class="cust-avatar">{{ strtoupper(substr($customer->name, 0, 2)) }}</div>
                            <div>
                                <div class="cust-name">{{ $customer->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $customer->email ?? '-' }}</td>
                    <td>
                        <span style="font-family: 'IBM Plex Mono', monospace;">{{ $customer->no_hp ?? '-' }}</span>
                    </td>
                    <td class="num">{{ $customer->pemesanan_count ?? 0 }}</td>
                    <td>
                        @if($customer->type === 'guest')
                            <span class="status pending" style="font-size: 11px;">
                                <span class="status-dot"></span>
                                Guest
                            </span>
                        @else
                            <span class="status lunas" style="font-size: 11px;">
                                <span class="status-dot"></span>
                                Registered
                            </span>
                        @endif
                    </td>
                    <td>
                        <span class="date-txt">{{ \Carbon\Carbon::parse($customer->created_at)->format('d M Y') }}</span>
                    </td>
                    <td>
                        <button class="row-action" onclick="window.location.href='{{ route('admin.customers.show', $customer->id) }}'" title="Lihat detail">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="panel-foot">
            <span>Menampilkan {{ $customers->firstItem() }}-{{ $customers->lastItem() }} dari {{ $customers->total() }} pelanggan</span>
            <div class="pager">
                @if($customers->onFirstPage())
                    <button disabled>‹</button>
                @else
                    <button onclick="window.location.href='{{ $customers->previousPageUrl() }}'">‹</button>
                @endif
                
                @foreach($customers->getUrlRange(1, min($customers->lastPage(), 5)) as $page => $url)
                    <button class="{{ $page == $customers->currentPage() ? 'active' : '' }}" onclick="window.location.href='{{ $url }}'">{{ $page }}</button>
                @endforeach
                
                @if($customers->hasMorePages())
                    <button onclick="window.location.href='{{ $customers->nextPageUrl() }}'">›</button>
                @else
                    <button disabled>›</button>
                @endif
            </div>
        </div>
    @else
        <div class="placeholder-panel">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="9" cy="8" r="3"/>
                <path d="M3.5 20c1-3.5 3.3-5.3 5.5-5.3s4.5 1.8 5.5 5.3"/>
            </svg>
            <h3>Tidak Ada Pelanggan</h3>
            <p>
                @if(request('search'))
                    Tidak ada pelanggan yang sesuai dengan pencarian Anda.
                @else
                    Belum ada pelanggan terdaftar.
                @endif
            </p>
        </div>
    @endif
</div>

@push('scripts')
<script>
// Auto-submit search on Enter
document.querySelector('.search input[name="search"]').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        this.closest('form').submit();
    }
});
</script>
@endpush
@endsection
