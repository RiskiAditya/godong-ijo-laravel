@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="page-head">
    <div>
        <div class="page-title-row">
            <h1>Dashboard</h1>
            <div class="live-chip">
                <span class="live-dot"></span>
                Live
            </div>
        </div>
        <p class="page-sub">Ringkasan performa sistem booking Godong Ijo</p>
    </div>
    <div class="head-actions">
        <button class="btn-ghost" onclick="window.location.reload()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/>
            </svg>
            Refresh
        </button>
        <button class="btn-solid" onclick="window.location.href='{{ route('admin.bookings.index') }}'">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="17" rx="1.5"/>
                <path d="M3 9h18M8 3v3M16 3v3"/>
            </svg>
            Lihat Semua Booking
        </button>
    </div>
</div>

<!-- Stats -->
<div class="stats">
    <div class="stat">
        <div class="stat-label">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="17" rx="1.5"/>
                <path d="M3 9h18"/>
            </svg>
            Booking Hari Ini
        </div>
        <div class="stat-value">{{ str_pad($bookingHariIni, 2, '0', STR_PAD_LEFT) }}</div>
        <div class="stat-foot">
            <span class="stat-delta pos">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M18 15l-6-6-6 6"/>
                </svg>
                Live
            </span>
        </div>
    </div>
    
    <div class="stat">
        <div class="stat-label">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="17" rx="1.5"/>
            </svg>
            Total Bulan Ini
        </div>
        <div class="stat-value">{{ str_pad($bookingBulanIni, 2, '0', STR_PAD_LEFT) }}</div>
        <div class="stat-foot">
            <span class="stat-delta flat">
                {{ now()->format('F Y') }}
            </span>
        </div>
    </div>
    
    <div class="stat">
        <div class="stat-label">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
            </svg>
            Total Pendapatan
        </div>
        <div class="stat-value" style="font-size: 20px;">{{ number_format($totalPendapatan / 1000000, 1) }}M</div>
        <div class="stat-foot">
            <span class="stat-delta pos">
                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
            </span>
        </div>
    </div>
    
    <div class="stat">
        <div class="stat-label">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 7v5l3.5 2"/>
            </svg>
            Perlu Konfirmasi
        </div>
        <div class="stat-value">{{ str_pad($bookingPending, 2, '0', STR_PAD_LEFT) }}</div>
        <div class="stat-foot">
            <span class="stat-delta {{ $bookingPending > 0 ? 'warn' : 'flat' }}">
                @if($bookingPending > 0)
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M12 8v4M12 16h.01"/>
                    </svg>
                    Perlu tindakan
                @else
                    Semua selesai
                @endif
            </span>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid-main">
    <!-- Recent Bookings Table -->
    <div class="panel">
        <div class="panel-head">
            <h2>Booking Terbaru <span class="count">({{ $recentBookings->count() }})</span></h2>
            <div class="tabs">
                <div class="tab active">Semua</div>
                <div class="tab">Lunas</div>
                <div class="tab">Pending</div>
            </div>
        </div>
        
        @if($recentBookings->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 110px;">Kode</th>
                        <th>Pelanggan</th>
                        <th>Paket</th>
                        <th style="width: 90px;">Tanggal</th>
                        <th class="num" style="width: 100px;">Total</th>
                        <th style="width: 80px;">Status</th>
                        <th style="width: 40px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentBookings as $booking)
                    <tr>
                        <td>
                            <span class="code">{{ $booking->kode_booking }}</span>
                        </td>
                        <td>
                            <div class="cust">
                                <div class="cust-avatar">{{ strtoupper(substr($booking->nama_lengkap, 0, 2)) }}</div>
                                <div style="min-width: 0;">
                                    <div class="cust-name" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $booking->nama_lengkap }}</div>
                                    <div class="date-txt" style="font-size: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $booking->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="max-width: 150px;">
                            <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $booking->paketWisata->nama_paket ?? 'N/A' }}">
                                {{ $booking->paketWisata->nama_paket ?? 'N/A' }}
                            </div>
                        </td>
                        <td>
                            <span class="date-txt" style="font-size: 11px;">{{ $booking->jadwal ? $booking->jadwal->tanggal->format('d M Y') : '-' }}</span>
                        </td>
                        <td class="num" style="font-size: 11.5px;">{{ number_format($booking->total_harga / 1000, 0) }}K</td>
                        <td>
                            <span class="status {{ $booking->status }}" style="font-size: 11px;">
                                <span class="status-dot"></span>
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                        <td>
                            <button class="row-action" onclick="window.location.href='{{ route('admin.bookings.show', $booking->id) }}'" title="Lihat detail">
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
                <span>Menampilkan {{ $recentBookings->count() }} booking terbaru</span>
                <a href="{{ route('admin.bookings.index') }}" style="color: var(--brand); font-weight: 600;">Lihat semua →</a>
            </div>
        @else
            <div class="placeholder-panel">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="4" width="18" height="17" rx="1.5"/>
                    <path d="M3 9h18M8 3v3M16 3v3"/>
                </svg>
                <h3>Belum Ada Booking</h3>
                <p>Booking baru akan muncul di sini secara otomatis</p>
            </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="side-stack">
        <!-- Popular Packages -->
        <div class="panel">
            <div class="panel-head">
                <h2>Paket Populer</h2>
            </div>
            
            @php
                $popularPackages = \App\Models\PaketWisata::withCount('pemesanan')
                    ->orderByDesc('pemesanan_count')
                    ->take(3)
                    ->get();
            @endphp
            
            @if($popularPackages->count() > 0)
                @foreach($popularPackages as $package)
                <div class="pkg-row">
                    <div class="pkg-top">
                        <div class="pkg-name">{{ $package->nama_paket }}</div>
                        <div class="pkg-rev">{{ $package->pemesanan_count }} booking</div>
                    </div>
                    <div class="pkg-meta">Rp {{ number_format($package->harga, 0, ',', '.') }} per orang</div>
                    <div class="bar-track">
                        <div class="bar-fill" style="width: {{ min(($package->pemesanan_count / max($popularPackages->max('pemesanan_count'), 1)) * 100, 100) }}%"></div>
                    </div>
                </div>
                @endforeach
                
                <div class="note">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M12 8v4M12 16h.01"/>
                    </svg>
                    <span>Data berdasarkan total booking sepanjang waktu</span>
                </div>
            @else
                <div class="placeholder-panel" style="padding: 40px 20px;">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M21 8 12 3 3 8l9 5 9-5Z"/>
                        <path d="M3 8v8l9 5 9-5V8"/>
                    </svg>
                    <p style="margin: 10px 0 0;">Belum ada data paket</p>
                </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="panel">
            <div class="panel-head">
                <h2>Aksi Cepat</h2>
            </div>
            <div class="quick-list">
                <button class="quick-item" onclick="window.location.href='{{ route('admin.bookings.index', ['status' => 'pending']) }}'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M12 7v5l3.5 2"/>
                    </svg>
                    Lihat Booking Pending
                    @if($bookingPending > 0)
                        <span class="nav-count" style="margin-left: auto;">{{ str_pad($bookingPending, 2, '0', STR_PAD_LEFT) }}</span>
                    @else
                        <svg class="arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    @endif
                </button>
                <button class="quick-item" onclick="window.location.href='{{ route('admin.paket-wisata.index') }}'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M21 8 12 3 3 8l9 5 9-5Z"/>
                        <path d="M3 8v8l9 5 9-5V8"/>
                    </svg>
                    Kelola Paket Wisata
                    <svg class="arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </button>
                <button class="quick-item" onclick="window.location.href='{{ route('admin.reports') }}'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M3 3v18h18"/>
                        <path d="M7 15v3M12 10v8M17 6v12"/>
                    </svg>
                    Lihat Laporan
                    <svg class="arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </button>
                <button class="quick-item" onclick="window.location.href='{{ route('admin.customers.index') }}'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="9" cy="8" r="3"/>
                        <path d="M3.5 20c1-3.5 3.3-5.3 5.5-5.3s4.5 1.8 5.5 5.3"/>
                    </svg>
                    Data Pelanggan
                    <svg class="arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Tab filtering untuk booking table
document.querySelectorAll('.tabs .tab').forEach(tab => {
    tab.addEventListener('click', function() {
        // Remove active from all tabs
        document.querySelectorAll('.tabs .tab').forEach(t => t.classList.remove('active'));
        // Add active to clicked tab
        this.classList.add('active');
        
        const filter = this.textContent.trim().toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const statusCell = row.querySelector('.status');
            if (!statusCell) return;
            
            const statusText = statusCell.textContent.trim().toLowerCase();
            
            if (filter === 'semua') {
                row.style.display = '';
            } else if (filter === 'lunas') {
                // Show rows with 'paid' status
                row.style.display = statusText.includes('paid') ? '' : 'none';
            } else if (filter === 'pending') {
                // Show rows with 'pending' status
                row.style.display = statusText.includes('pending') ? '' : 'none';
            } else {
                // Fallback: check if status contains filter text
                row.style.display = statusText.includes(filter) ? '' : 'none';
            }
        });
    });
});

// Auto refresh every 60 seconds
let autoRefreshInterval = setInterval(() => {
    console.log('Auto-refreshing dashboard data...');
    // In production, you'd use AJAX to refresh just the data
    // For now, we'll skip full page reload to avoid interrupting the user
}, 60000);

// Clear interval on page unload
window.addEventListener('beforeunload', () => {
    clearInterval(autoRefreshInterval);
});
</script>
@endpush
@endsection
