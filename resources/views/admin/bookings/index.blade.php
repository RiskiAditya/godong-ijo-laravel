@extends('layouts.admin')

@section('title', 'Booking Management')
@section('page-title', 'Booking')

@section('content')
<div class="page-head">
    <div>
        <h1>Booking Management</h1>
        <p class="page-sub">Kelola semua booking dan reservasi pelanggan</p>
    </div>
    <div class="head-actions">
        <div class="dropdown-wrapper" style="position: relative;">
            <button class="btn-ghost" onclick="toggleExportDropdown(event)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Export
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-left: 4px;">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </button>
            <div class="export-dropdown" id="exportDropdown" style="display: none;">
                <button onclick="exportBookings('csv')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                    Export CSV
                </button>
                <button onclick="exportBookings('excel')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="9" y1="15" x2="15" y2="15"/>
                    </svg>
                    Export Excel
                </button>
                <button onclick="exportBookings('pdf')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="9" y1="13" x2="15" y2="13"/>
                        <line x1="9" y1="17" x2="15" y2="17"/>
                    </svg>
                    Export PDF
                </button>
            </div>
        </div>
        <button class="btn-solid" onclick="window.location.reload()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/>
            </svg>
            Refresh
        </button>
    </div>
</div>

<div class="panel">
    <div class="panel-head">
        <h2>Semua Booking <span class="count">({{ $bookings->total() }})</span></h2>
        <div class="tabs">
            <a href="{{ route('admin.bookings.index') }}" class="tab {{ !request('status') ? 'active' : '' }}">Semua</a>
            <a href="{{ route('admin.bookings.index', ['status' => 'paid']) }}" class="tab {{ request('status') === 'paid' ? 'active' : '' }}">Lunas</a>
            <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" class="tab {{ request('status') === 'pending' ? 'active' : '' }}">Pending</a>
            <a href="{{ route('admin.bookings.index', ['status' => 'cancelled']) }}" class="tab {{ request('status') === 'cancelled' ? 'active' : '' }}">Batal</a>
        </div>
    </div>
    
    <div class="subbar">
        <form method="GET" action="{{ route('admin.bookings.index') }}" class="search" style="margin: 0;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="7"/>
                <path d="m21 21-4.3-4.3"/>
            </svg>
            <input type="text" name="search" placeholder="Cari kode booking, nama, email…" value="{{ request('search') }}">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
        </form>
        
        <button class="btn-ghost" onclick="toggleFilterPanel()" style="margin-left: auto;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
            </svg>
            Filter
        </button>
    </div>
    
    <!-- Filter Panel (hidden by default) -->
    <div id="filterPanel" style="display: none; padding: 16px 18px; border-bottom: 1px solid var(--line); background: var(--bg);">
        <form method="GET" action="{{ route('admin.bookings.index') }}" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; align-items: end;">
            <div class="form-group" style="margin: 0;">
                <label>Tanggal Dari</label>
                <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" style="padding: 7px 10px; font-size: 12.5px;">
            </div>
            <div class="form-group" style="margin: 0;">
                <label>Tanggal Sampai</label>
                <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" style="padding: 7px 10px; font-size: 12.5px;">
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn-solid" style="flex: 1;">Terapkan</button>
                <a href="{{ route('admin.bookings.index') }}" class="btn-ghost" style="text-align: center;">Reset</a>
            </div>
        </form>
    </div>

    @if($bookings->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Kode Booking</th>
                    <th>Pelanggan</th>
                    <th>Paket Wisata</th>
                    <th>Tanggal Kunjungan</th>
                    <th>Jumlah</th>
                    <th class="num">Total Harga</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                <tr>
                    <td>
                        <a href="{{ route('admin.bookings.show', $booking->id) }}" class="code" style="color: var(--brand); font-weight: 600;">
                            {{ $booking->kode_booking }}
                        </a>
                    </td>
                    <td>
                        <div class="cust">
                            <div class="cust-avatar">{{ strtoupper(substr($booking->nama_lengkap, 0, 2)) }}</div>
                            <div>
                                <div class="cust-name">{{ $booking->nama_lengkap }}</div>
                                <div class="date-txt" style="font-size: 11px;">{{ $booking->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $booking->paketWisata->nama_paket ?? 'N/A' }}</td>
                    <td>
                        <span class="date-txt">
                            {{ $booking->tanggal_kunjungan
                                ? $booking->tanggal_kunjungan->format('d M Y')
                                : ($booking->jadwal ? $booking->jadwal->tanggal->format('d M Y') : '-') }}
                        </span>
                        @if(data_get($booking->package_specific_data, 'jam_kunjungan'))
                            <span class="date-txt" style="display: block; margin-top: 3px;">
                                {{ data_get($booking->package_specific_data, 'jam_kunjungan') }} WIB
                            </span>
                        @endif
                    </td>
                    <td>{{ $booking->jumlah_orang }} orang</td>
                    <td class="num">Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                    <td>
                        <span class="status {{ $booking->status }}">
                            <span class="status-dot"></span>
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                    <td>
                        <span class="date-txt">{{ $booking->created_at->format('d M Y') }}</span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 4px;">
                            <button class="row-action" onclick="window.location.href='{{ route('admin.bookings.show', $booking->id) }}'" title="Lihat detail">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                            @if($booking->status === 'pending')
                            <button class="row-action" onclick="updateStatus({{ $booking->id }}, 'paid')" title="Tandai lunas">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6L9 17l-5-5"/>
                                </svg>
                            </button>
                            @endif
                            <button class="row-action danger" onclick="confirmDelete({{ $booking->id }})" title="Hapus booking">
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
            <span>Menampilkan {{ $bookings->firstItem() }}-{{ $bookings->lastItem() }} dari {{ $bookings->total() }} booking</span>
            <div class="pager">
                @if($bookings->onFirstPage())
                    <button disabled>‹</button>
                @else
                    <button onclick="window.location.href='{{ $bookings->previousPageUrl() }}'">‹</button>
                @endif
                
                @foreach($bookings->getUrlRange(1, $bookings->lastPage()) as $page => $url)
                    @if($page == $bookings->currentPage())
                        <button class="active">{{ $page }}</button>
                    @elseif($page == 1 || $page == $bookings->lastPage() || abs($page - $bookings->currentPage()) < 3)
                        <button onclick="window.location.href='{{ $url }}'">{{ $page }}</button>
                    @elseif(abs($page - $bookings->currentPage()) == 3)
                        <button disabled>…</button>
                    @endif
                @endforeach
                
                @if($bookings->hasMorePages())
                    <button onclick="window.location.href='{{ $bookings->nextPageUrl() }}'">›</button>
                @else
                    <button disabled>›</button>
                @endif
            </div>
        </div>
    @else
        <div class="placeholder-panel">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="3" y="4" width="18" height="17" rx="1.5"/>
                <path d="M3 9h18M8 3v3M16 3v3"/>
            </svg>
            <h3>Tidak Ada Booking</h3>
            <p>
                @if(request('search') || request('status') || request('tanggal_dari'))
                    Tidak ada booking yang sesuai dengan filter Anda.
                @else
                    Belum ada booking yang masuk. Booking baru akan muncul di sini.
                @endif
            </p>
            @if(request()->hasAny(['search', 'status', 'tanggal_dari', 'tanggal_sampai']))
                <a href="{{ route('admin.bookings.index') }}" class="btn-solid">Reset Filter</a>
            @endif
        </div>
    @endif
</div>

<style>
.export-dropdown {
    position: absolute;
    top: calc(100% + 4px);
    right: 0;
    background: var(--card);
    border: 1px solid var(--line);
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    min-width: 160px;
    z-index: 1000;
    overflow: hidden;
}

.export-dropdown button {
    width: 100%;
    padding: 10px 14px;
    border: none;
    background: transparent;
    text-align: left;
    font-size: 13px;
    color: var(--ink);
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: background 0.15s ease;
}

.export-dropdown button:hover {
    background: var(--bg);
}

.export-dropdown button svg {
    flex-shrink: 0;
    color: var(--ink-60);
}

.dropdown-wrapper {
    display: inline-block;
}
</style>

@push('scripts')
<script>
function toggleExportDropdown(event) {
    event.stopPropagation();
    const dropdown = document.getElementById('exportDropdown');
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('exportDropdown');
    if (dropdown && !e.target.closest('.dropdown-wrapper')) {
        dropdown.style.display = 'none';
    }
});

function exportBookings(format) {
    // Close dropdown
    document.getElementById('exportDropdown').style.display = 'none';
    
    // Get current URL params (status, search, date filters)
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('format', format);
    
    const exportUrl = '{{ route('admin.bookings.export') }}?' + urlParams.toString();
    
    // Open export URL to download file
    window.location.href = exportUrl;
    
    const formatNames = {
        'csv': 'CSV',
        'excel': 'Excel',
        'pdf': 'PDF'
    };
    
    showToast('Mengunduh data booking dalam format ' + formatNames[format] + '...', 'success');
}

function toggleFilterPanel() {
    const panel = document.getElementById('filterPanel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
}

function updateStatus(bookingId, status) {
    if (!confirm('Apakah Anda yakin ingin mengubah status booking ini menjadi ' + status + '?')) {
        return;
    }
    
    fetch(`/admin/bookings/${bookingId}/update-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        showToast('Status berhasil diubah', 'success');
        setTimeout(() => window.location.reload(), 1000);
    })
    .catch(error => {
        showToast('Gagal mengubah status', 'error');
        console.error('Error:', error);
    });
}

function confirmDelete(bookingId) {
    if (!confirm('Apakah Anda yakin ingin menghapus booking ini? Tindakan ini tidak dapat dibatalkan.')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/bookings/${bookingId}`;
    
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

function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            ${type === 'success' ? '<path d="M20 6L9 17l-5-5"/>' : '<circle cx="12" cy="12" r="9"/><path d="M12 8v4M12 16h.01"/>'}
        </svg>
        ${message}
    `;
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
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
