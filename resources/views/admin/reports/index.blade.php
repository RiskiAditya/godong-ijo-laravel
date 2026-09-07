@extends('layouts.admin')

@section('title', 'Laporan & Analitik')
@section('page-title', 'Laporan')

@section('content')
<div class="page-head">
    <div>
        <h1>Laporan & Analitik</h1>
        <p class="page-sub">Analisis performa bisnis dan tren booking</p>
    </div>
    <div class="head-actions">
        <form method="GET" action="{{ route('admin.reports') }}" style="display: flex; gap: 8px; align-items: center;">
            <input type="date" name="start_date" value="{{ $startDate }}" style="padding: 7px 10px; border: 1px solid var(--line); border-radius: 6px; font-size: 12.5px; font-family: inherit;">
            <span style="color: var(--ink-45);">—</span>
            <input type="date" name="end_date" value="{{ $endDate }}" style="padding: 7px 10px; border: 1px solid var(--line); border-radius: 6px; font-size: 12.5px; font-family: inherit;">
            <button type="submit" class="btn-solid">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
                <span>Filter</span>
            </button>
        </form>
    </div>
</div>

<!-- Revenue Stats -->
<div class="stats">
    <div class="stat">
        <div class="stat-label">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
            </svg>
            Total Pendapatan
        </div>
        <div class="stat-value" style="font-size: 20px;">{{ number_format($revenueStats['total'] / 1000000, 1) }}M</div>
        <div class="stat-foot">
            <span class="stat-delta flat">Rp {{ number_format($revenueStats['total'], 0, ',', '.') }}</span>
        </div>
    </div>
    
    <div class="stat">
        <div class="stat-label">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
            </svg>
            Rata-rata Transaksi
        </div>
        <div class="stat-value">{{ number_format($revenueStats['average'], 0, ',', '.') }}</div>
        <div class="stat-foot">
            <span class="stat-delta flat">Per booking</span>
        </div>
    </div>
    
    <div class="stat">
        <div class="stat-label">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="17" rx="1.5"/>
            </svg>
            Total Booking
        </div>
        <div class="stat-value">{{ str_pad($revenueStats['count'], 2, '0', STR_PAD_LEFT) }}</div>
        <div class="stat-foot">
            <span class="stat-delta pos">Dalam periode ini</span>
        </div>
    </div>
    
    <div class="stat">
        <div class="stat-label">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 7v5l3.5 2"/>
            </svg>
            Status Booking
        </div>
        <div class="status-summary">
            @foreach($bookingByStatus as $status => $count)
                <div class="status-summary-row status-summary-{{ $status }}">
                    <span class="status-summary-label">
                        <span class="status-summary-dot"></span>
                        {{ ucfirst($status) }}
                    </span>
                    <strong>{{ $count }}</strong>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="grid-main">
    <!-- Main Content -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <!-- Top Packages -->
        <div class="panel">
            <div class="panel-head">
                <h2>Paket Terpopuler <span class="count">(Top 5)</span></h2>
            </div>
            
            @if($topPackages->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Paket</th>
                            <th class="num">Total Booking</th>
                            <th class="num">Pendapatan</th>
                            <th style="width: 200px;">Grafik</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $maxBookings = $topPackages->max('pemesanan_count'); @endphp
                        @foreach($topPackages as $package)
                        <tr>
                            <td>
                                <div style="font-weight: 600; margin-bottom: 2px;">{{ $package->nama_paket }}</div>
                                <div style="font-size: 11px; color: var(--ink-45);">{{ $package->jenis_paket }}</div>
                            </td>
                            <td class="num">{{ $package->pemesanan_count }}</td>
                            <td class="num">Rp {{ number_format($package->revenue ?? 0, 0, ',', '.') }}</td>
                            <td>
                                <div class="bar-track">
                                    <div class="bar-fill" style="width: {{ ($package->pemesanan_count / max($maxBookings, 1)) * 100 }}%"></div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="placeholder-panel">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M21 8 12 3 3 8l9 5 9-5Z"/>
                        <path d="M3 8v8l9 5 9-5V8"/>
                    </svg>
                    <h3>Tidak Ada Data</h3>
                    <p>Belum ada booking dalam periode ini</p>
                </div>
            @endif
        </div>

        <!-- Daily Revenue Chart -->
        <div class="panel">
            <div class="panel-head">
                <h2>Tren Pendapatan Harian</h2>
            </div>
            
            @if($dailyRevenue->count() > 0)
                <div style="padding: 20px 18px;">
                    <div style="overflow-x: auto; padding-bottom: 6px;">
                        <div style="height: 300px; min-width: max(720px, {{ $dailyRevenue->count() * 42 }}px); display: grid; grid-template-columns: repeat({{ $dailyRevenue->count() }}, minmax(30px, 1fr)); align-items: end; gap: 8px; padding: 20px 8px 0; border-bottom: 1px solid var(--line); background: repeating-linear-gradient(to bottom, transparent 0, transparent 59px, var(--line) 60px);">
                            @php $maxRevenue = $dailyRevenue->max('revenue'); @endphp
                            @foreach($dailyRevenue as $day)
                                <div style="height: 100%; min-width: 30px; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; gap: 8px;">
                                    <div style="width: min(32px, 100%); background: linear-gradient(180deg, var(--brand) 0%, var(--brand-darker) 100%); border-radius: 5px 5px 0 0; height: {{ max(($day->revenue / max($maxRevenue, 1)) * 100, 2) }}%; min-height: 4px; position: relative; box-shadow: 0 2px 5px rgba(5, 150, 105, 0.18);" title="{{ \Carbon\Carbon::parse($day->date)->format('d/m/Y') }}: Rp {{ number_format($day->revenue, 0, ',', '.') }}">
                                    </div>
                                    <div style="font-size: 10px; color: var(--ink-45); font-family: 'IBM Plex Mono', monospace; white-space: nowrap;">
                                        {{ \Carbon\Carbon::parse($day->date)->format('d/m') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--line); font-size: 12px; color: var(--ink-45);">
                        <strong style="color: var(--ink);">Total:</strong> Rp {{ number_format($dailyRevenue->sum('revenue'), 0, ',', '.') }} dari {{ $dailyRevenue->sum('bookings') }} booking
                        @if($dailyRevenue->sum('bookings') === 0)
                            <span style="margin-left: 8px;">Belum ada transaksi paid pada periode ini.</span>
                        @endif
                    </div>
                </div>
            @else
                <div class="placeholder-panel">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M3 3v18h18"/>
                        <path d="M7 15v3M12 10v8M17 6v12"/>
                    </svg>
                    <h3>Tidak Ada Data</h3>
                    <p>Belum ada transaksi dalam periode ini</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Sidebar -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <!-- Quick Stats -->
        <div class="panel">
            <div class="panel-head">
                <h2>Periode Laporan</h2>
            </div>
            
            <div style="padding: 18px;">
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div>
                        <div style="font-size: 11px; color: var(--ink-45); font-weight: 600; margin-bottom: 4px;">DARI</div>
                        <div style="font-size: 14px; font-weight: 600; font-family: 'IBM Plex Mono', monospace;">
                            {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}
                        </div>
                    </div>
                    <div style="border-top: 1px solid var(--line); padding-top: 12px;">
                        <div style="font-size: 11px; color: var(--ink-45); font-weight: 600; margin-bottom: 4px;">SAMPAI</div>
                        <div style="font-size: 14px; font-weight: 600; font-family: 'IBM Plex Mono', monospace;">
                            {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                        </div>
                    </div>
                    <div style="border-top: 1px solid var(--line); padding-top: 12px;">
                        <div style="font-size: 11px; color: var(--ink-45); font-weight: 600; margin-bottom: 4px;">DURASI</div>
                        <div style="font-size: 14px; font-weight: 600;">
                            {{ \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1 }} hari
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Filters -->
        <div class="panel">
            <div class="panel-head">
                <h2>Filter Cepat</h2>
            </div>
            <div class="quick-list">
                <button class="quick-item" onclick="filterRange('today')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M12 7v5l3.5 2"/>
                    </svg>
                    Hari Ini
                    <svg class="arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </button>
                <button class="quick-item" onclick="filterRange('week')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="4" width="18" height="17" rx="1.5"/>
                    </svg>
                    7 Hari Terakhir
                    <svg class="arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </button>
                <button class="quick-item" onclick="filterRange('month')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="4" width="18" height="17" rx="1.5"/>
                    </svg>
                    30 Hari Terakhir
                    <svg class="arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </button>
                <button class="quick-item" onclick="filterRange('year')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="4" width="18" height="17" rx="1.5"/>
                    </svg>
                    Tahun Ini
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
function filterRange(range) {
    const today = new Date();
    const formatLocalDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };
    let startDate = new Date(today);
    const endDate = formatLocalDate(today);
    
    switch(range) {
        case 'today':
            break;
        case 'week':
            startDate.setDate(startDate.getDate() - 6);
            break;
        case 'month':
            startDate.setDate(startDate.getDate() - 29);
            break;
        case 'year':
            startDate = new Date(today.getFullYear(), 0, 1);
            break;
    }
    
    window.location.href = `{{ route('admin.reports') }}?start_date=${formatLocalDate(startDate)}&end_date=${endDate}`;
}
</script>
@endpush
@endsection
