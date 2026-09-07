@extends('layouts.admin')

@section('title', 'Riwayat Aktivitas')
@section('page-title', 'Riwayat Aktivitas')

@push('styles')
<style>
/* Activity Log Styles - Professional & Refined */
.activity-feed {
    position: relative;
}

.activity-timeline {
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 1px;
    background: #E5E7EB;
}

.activity-item {
    display: flex;
    gap: 16px;
    padding: 16px 0;
    border-bottom: 1px solid #F3F4F6;
    position: relative;
    transition: opacity 0.15s ease;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-item:hover {
    opacity: 0.8;
}

.activity-icon-wrapper {
    position: relative;
    flex-shrink: 0;
    z-index: 1;
}

.activity-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    border: 1px solid #E5E7EB;
    background: white;
}

.activity-icon svg {
    width: 16px;
    height: 16px;
    stroke-width: 2;
}

.act-booking {
    background: #F9FAFB;
    border-color: #E5E7EB;
}

.act-booking svg {
    color: #6366F1;
}

.act-package {
    background: #F9FAFB;
    border-color: #E5E7EB;
}

.act-package svg {
    color: #8B5CF6;
}

.act-payment {
    background: #F9FAFB;
    border-color: #E5E7EB;
}

.act-payment svg {
    color: #06B6D4;
}

.act-system {
    background: #F9FAFB;
    border-color: #E5E7EB;
}

.act-system svg {
    color: #10B981;
}

.activity-content {
    flex: 1;
    min-width: 0;
}

.activity-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 4px;
}

.activity-title {
    font-size: 13.5px;
    font-weight: 500;
    color: #111827;
    line-height: 1.5;
}

.activity-time {
    font-size: 12px;
    color: #9CA3AF;
    font-family: 'IBM Plex Mono', monospace;
    white-space: nowrap;
}

.activity-description {
    font-size: 13px;
    color: #6B7280;
    line-height: 1.5;
    margin-bottom: 8px;
}

.activity-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.activity-tag {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11.5px;
    padding: 3px 8px;
    border-radius: 5px;
    background: #F3F4F6;
    color: #6B7280;
    font-weight: 500;
}

.activity-tag svg {
    width: 11px;
    height: 11px;
    opacity: 0.6;
}

.activity-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    margin-bottom: 20px;
}

.activity-stat-card {
    background: white;
    border: 1px solid #E5E7EB;
    border-radius: 10px;
    padding: 20px;
    display: flex;
    align-items: flex-start;
    gap: 14px;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.activity-stat-card:hover {
    border-color: #D1D5DB;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.activity-stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 1px solid;
}

.activity-stat-icon svg {
    width: 20px;
    height: 20px;
}

.activity-stat-icon.icon-booking {
    background: #EEF2FF;
    border-color: #E0E7FF;
    color: #6366F1;
}

.activity-stat-icon.icon-package {
    background: #F5F3FF;
    border-color: #EDE9FE;
    color: #8B5CF6;
}

.activity-stat-icon.icon-today {
    background: #ECFDF5;
    border-color: #D1FAE5;
    color: #10B981;
}

.activity-stat-content {
    flex: 1;
    min-width: 0;
}

.activity-stat-label {
    font-size: 12px;
    font-weight: 500;
    color: #6B7280;
    margin-bottom: 4px;
}

.activity-stat-value {
    font-size: 28px;
    font-weight: 600;
    font-family: 'IBM Plex Mono', monospace;
    color: #111827;
    line-height: 1;
}

.filter-chips {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.filter-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 12px;
    border-radius: 7px;
    font-size: 13px;
    font-weight: 500;
    border: 1px solid #E5E7EB;
    background: white;
    color: #6B7280;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
}

.filter-chip:hover {
    background: #F9FAFB;
    border-color: #D1D5DB;
    color: #374151;
}

.filter-chip.active {
    background: #111827;
    border-color: #111827;
    color: white;
}

.filter-chip svg {
    width: 13px;
    height: 13px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 16px;
    border-radius: 12px;
    background: #F9FAFB;
    border: 1px solid #E5E7EB;
    display: flex;
    align-items: center;
    justify-content: center;
}

.empty-state-icon svg {
    width: 28px;
    height: 28px;
    color: #9CA3AF;
}

.empty-state h3 {
    font-size: 15px;
    font-weight: 600;
    color: #111827;
    margin: 0 0 8px;
}

.empty-state p {
    font-size: 13px;
    color: #6B7280;
    margin: 0 0 20px;
}
</style>
@endpush

@section('content')
<div class="page-head">
    <div>
        <h1>Riwayat Aktivitas</h1>
        <p class="page-sub">Monitor aktivitas sistem dan perubahan data secara real-time</p>
    </div>
    <div class="head-actions">
        <button class="btn-ghost" onclick="window.location.reload()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/>
            </svg>
            <span>Refresh</span>
        </button>
        <button class="btn-ghost" onclick="window.print()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                <rect x="6" y="14" width="12" height="8"/>
            </svg>
            <span>Export</span>
        </button>
    </div>
</div>

<!-- Activity Stats -->
<div class="activity-stats-grid">
    <div class="activity-stat-card">
        <div class="activity-stat-icon icon-booking">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="17" rx="2"/>
                <path d="M3 9h18M8 3v3M16 3v3"/>
            </svg>
        </div>
        <div class="activity-stat-content">
            <div class="activity-stat-label">Total Booking</div>
            <div class="activity-stat-value">{{ $activities->where('type', 'booking')->count() }}</div>
        </div>
    </div>
    
    <div class="activity-stat-card">
        <div class="activity-stat-icon icon-package">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 8 12 3 3 8l9 5 9-5Z"/>
                <path d="M3 8v8l9 5 9-5V8"/>
            </svg>
        </div>
        <div class="activity-stat-content">
            <div class="activity-stat-label">Update Paket</div>
            <div class="activity-stat-value">{{ $activities->where('type', 'package')->count() }}</div>
        </div>
    </div>
    
    <div class="activity-stat-card">
        <div class="activity-stat-icon icon-today">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 7v5l3.5 2"/>
            </svg>
        </div>
        <div class="activity-stat-content">
            <div class="activity-stat-label">Aktivitas Hari Ini</div>
            <div class="activity-stat-value">{{ $activities->filter(fn($a) => $a['created_at']->isToday())->count() }}</div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-head">
        <h2>Timeline Aktivitas</h2>
        <div class="filter-chips">
            <a href="{{ route('admin.activity') }}" class="filter-chip {{ !request('type') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="2"/>
                    <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48 2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48 2.83-2.83"/>
                </svg>
                Semua
            </a>
            <a href="{{ route('admin.activity', ['type' => 'booking']) }}" class="filter-chip {{ request('type') === 'booking' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="17" rx="2"/>
                    <path d="M3 9h18"/>
                </svg>
                Booking
            </a>
            <a href="{{ route('admin.activity', ['type' => 'package']) }}" class="filter-chip {{ request('type') === 'package' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 8 12 3 3 8l9 5 9-5Z"/>
                </svg>
                Paket Wisata
            </a>
        </div>
    </div>

    @if($activities->count() > 0)
        <div style="padding: 20px 18px;">
            <div class="activity-feed">
                <div class="activity-timeline"></div>
                
                @foreach($activities as $activity)
                <div class="activity-item">
                    <div class="activity-icon-wrapper">
                        <div class="activity-icon {{ $activity['type'] === 'booking' ? 'act-booking' : 'act-package' }}">
                            @if($activity['icon'] === 'calendar')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="17" rx="2"/>
                                    <path d="M3 9h18M8 3v3M16 3v3"/>
                                </svg>
                            @elseif($activity['icon'] === 'box')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 8 12 3 3 8l9 5 9-5Z"/>
                                    <path d="M3 8v8l9 5 9-5V8"/>
                                </svg>
                            @endif
                        </div>
                    </div>
                    
                    <div class="activity-content">
                        <div class="activity-header">
                            <div class="activity-title">{{ $activity['message'] }}</div>
                            <div class="activity-time">{{ $activity['created_at']->format('H:i') }}</div>
                        </div>
                        
                        <div class="activity-description">
                            {{ $activity['metadata'] }}
                        </div>
                        
                        <div class="activity-meta">
                            <div class="activity-tag">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path d="M12 7v5l3.5 2"/>
                                </svg>
                                {{ $activity['created_at']->diffForHumans() }}
                            </div>
                            
                            @if(isset($activity['status']))
                                <span class="status {{ $activity['status'] }}" style="font-size: 11px;">
                                    <span class="status-dot"></span>
                                    {{ ucfirst($activity['status']) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 7v5l3.5 2"/>
                </svg>
            </div>
            <h3>Tidak Ada Aktivitas</h3>
            <p>
                @if(request('type'))
                    Tidak ada aktivitas tipe "{{ ucfirst(request('type')) }}" yang ditemukan.
                @else
                    Belum ada aktivitas yang tercatat dalam sistem.
                @endif
            </p>
            @if(request('type'))
                <a href="{{ route('admin.activity') }}" class="btn-ghost">
                    <span>Lihat Semua Aktivitas</span>
                </a>
            @endif
        </div>
    @endif
</div>

<div class="note" style="margin-top: 20px;">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="9"/>
        <path d="M12 8v4M12 16h.01"/>
    </svg>
    <span>Activity log menampilkan 50 aktivitas terbaru. Untuk analisis mendalam, gunakan menu <a href="{{ route('admin.reports') }}" style="color: var(--brand); font-weight: 600;">Laporan</a>.</span>
</div>
@endsection
