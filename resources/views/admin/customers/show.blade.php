@extends('layouts.admin')

@section('title', 'Detail Pelanggan')
@section('page-title', 'Detail Pelanggan')

@section('content')
<div class="page-head">
    <div>
        <h1>Detail Pelanggan</h1>
        <p class="page-sub">
            <a href="{{ route('admin.customers.index') }}" style="color: var(--brand); font-weight: 600;">← Kembali ke Daftar Pelanggan</a>
        </p>
    </div>
</div>

<div class="grid-main">
    <!-- Main Content -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <!-- Customer Info -->
        <div class="panel">
            <div class="panel-head">
                <h2>Informasi Pelanggan</h2>
            </div>
            
            <div style="padding: 20px 18px;">
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
                    <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--brand-soft); color: var(--brand); display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; font-family: 'IBM Plex Mono', monospace;">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div>
                        <div style="font-size: 20px; font-weight: 700; margin-bottom: 4px;">
                            {{ $user->name }}
                            @if(isset($user->type) && $user->type === 'guest')
                                <span class="status pending" style="font-size: 11px; margin-left: 8px;">
                                    <span class="status-dot"></span>
                                    Guest Customer
                                </span>
                            @else
                                <span class="status lunas" style="font-size: 11px; margin-left: 8px;">
                                    <span class="status-dot"></span>
                                    Registered
                                </span>
                            @endif
                        </div>
                        <div style="font-size: 13px; color: var(--ink-45);">
                            Pelanggan sejak {{ \Carbon\Carbon::parse($user->created_at)->format('d M Y') }}
                        </div>
                    </div>
                </div>
                
                <table style="width: 100%;">
                    <tr>
                        <td style="padding: 8px 0; color: var(--ink-45); font-size: 12.5px; font-weight: 600; width: 140px;">Email</td>
                        <td style="padding: 8px 0;">
                            <a href="mailto:{{ $user->email }}" style="color: var(--brand); font-weight: 500;">{{ $user->email }}</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: var(--ink-45); font-size: 12.5px; font-weight: 600;">No. Handphone</td>
                        <td style="padding: 8px 0; font-family: 'IBM Plex Mono', monospace;">
                            @if($user->no_hp)
                                <a href="tel:{{ $user->no_hp }}" style="color: var(--brand); font-weight: 500;">{{ $user->no_hp }}</a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: var(--ink-45); font-size: 12.5px; font-weight: 600;">Terdaftar</td>
                        <td style="padding: 8px 0; font-family: 'IBM Plex Mono', monospace;">
                            {{ \Carbon\Carbon::parse($user->created_at)->format('d M Y, H:i') }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Booking History -->
        <div class="panel">
            <div class="panel-head">
                <h2>Riwayat Booking <span class="count">({{ $user->pemesanan->count() }})</span></h2>
            </div>
            
            @if($user->pemesanan->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Kode Booking</th>
                            <th>Paket</th>
                            <th>Tanggal</th>
                            <th class="num">Total</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user->pemesanan as $booking)
                        <tr>
                            <td>
                                <span class="code">{{ $booking->kode_booking }}</span>
                            </td>
                            <td>{{ $booking->paketWisata->nama_paket ?? 'N/A' }}</td>
                            <td>
                                <span class="date-txt">{{ $booking->jadwal ? $booking->jadwal->tanggal->format('d M Y') : '-' }}</span>
                            </td>
                            <td class="num">Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                            <td>
                                <span class="status {{ $booking->status }}">
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
            @else
                <div class="placeholder-panel">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="4" width="18" height="17" rx="1.5"/>
                        <path d="M3 9h18M8 3v3M16 3v3"/>
                    </svg>
                    <h3>Belum Ada Booking</h3>
                    <p>Pelanggan ini belum pernah melakukan booking</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Sidebar -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <!-- Statistics -->
        <div class="panel">
            <div class="panel-head">
                <h2>Statistik</h2>
            </div>
            
            <div style="padding: 18px;">
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <div style="font-size: 11px; color: var(--ink-45); font-weight: 600; margin-bottom: 4px;">TOTAL BOOKING</div>
                        <div style="font-size: 28px; font-weight: 700; font-family: 'IBM Plex Mono', monospace; color: var(--brand);">
                            {{ str_pad($stats['total_bookings'], 2, '0', STR_PAD_LEFT) }}
                        </div>
                    </div>
                    
                    <div style="border-top: 1px solid var(--line); padding-top: 14px;">
                        <div style="font-size: 11px; color: var(--ink-45); font-weight: 600; margin-bottom: 4px;">TOTAL PENGELUARAN</div>
                        <div style="font-size: 16px; font-weight: 700; font-family: 'IBM Plex Mono', monospace;">
                            Rp {{ number_format($stats['total_spent'], 0, ',', '.') }}
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; border-top: 1px solid var(--line); padding-top: 14px;">
                        <div>
                            <div style="font-size: 11px; color: var(--ink-45); font-weight: 600; margin-bottom: 4px;">PENDING</div>
                            <div style="font-size: 20px; font-weight: 700; font-family: 'IBM Plex Mono', monospace;">
                                {{ $stats['pending_bookings'] }}
                            </div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: var(--ink-45); font-weight: 600; margin-bottom: 4px;">SELESAI</div>
                            <div style="font-size: 20px; font-weight: 700; font-family: 'IBM Plex Mono', monospace;">
                                {{ $stats['completed_bookings'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="panel">
            <div class="panel-head">
                <h2>Aksi</h2>
            </div>
            <div class="quick-list">
                <button class="quick-item" onclick="window.location.href='mailto:{{ $user->email }}'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="2" y="4" width="20" height="16" rx="2"/>
                        <path d="m2 7 10 6 10-6"/>
                    </svg>
                    Kirim Email
                    <svg class="arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </button>
                @if($user->no_hp)
                <button class="quick-item" onclick="window.location.href='https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->no_hp) }}'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8v.5z"/>
                    </svg>
                    Hubungi via WhatsApp
                    <svg class="arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </button>
                @endif
                <button class="quick-item" onclick="window.location.href='{{ route('admin.bookings.index') }}?customer={{ $user->id }}'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="4" width="18" height="17" rx="1.5"/>
                        <path d="M3 9h18"/>
                    </svg>
                    Lihat Semua Booking
                    <svg class="arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
