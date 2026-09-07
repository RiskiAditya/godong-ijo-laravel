@extends('layouts.admin')

@section('title', 'Detail Paket - ' . $paket->nama_paket)
@section('page-title', 'Detail Paket')

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $paket->nama_paket }}</h1>
        <p class="page-sub">
            <a href="{{ route('admin.paket-wisata.index') }}" style="color: var(--brand); font-weight: 600;">← Kembali ke Daftar Paket</a>
        </p>
    </div>
    <div class="head-actions">
        <button class="btn-ghost" onclick="window.location.href='{{ route('admin.paket-wisata.edit', $paket->id) }}'">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.1 2.1 0 0 1 3 3L12 15l-4 1 1-4Z"/>
            </svg>
            Edit Paket
        </button>
    </div>
</div>

<div class="grid-main">
    <!-- Main Content -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <!-- Package Photo -->
        @if($paket->foto)
        <div class="panel" style="padding: 0; overflow: hidden;">
            <img src="{{ asset($paket->foto) }}" alt="{{ $paket->nama_paket }}" style="width: 100%; height: 400px; object-fit: cover;">
        </div>
        @endif
        
        <!-- Package Info -->
        <div class="panel">
            <div class="panel-head">
                <h2>Informasi Paket</h2>
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
            </div>
            
            <div style="padding: 20px 18px;">
                <table style="width: 100%;">
                    <tr>
                        <td style="padding: 10px 0; color: var(--ink-45); font-size: 12.5px; font-weight: 600; width: 180px;">Jenis Paket</td>
                        <td style="padding: 10px 0; font-weight: 600;">{{ $paket->jenis_paket }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: var(--ink-45); font-size: 12.5px; font-weight: 600; vertical-align: top;">Deskripsi</td>
                        <td style="padding: 10px 0; line-height: 1.7;">{{ $paket->deskripsi }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: var(--ink-45); font-size: 12.5px; font-weight: 600;">Harga per Orang</td>
                        <td style="padding: 10px 0; font-family: 'IBM Plex Mono', monospace; font-size: 18px; font-weight: 700; color: var(--brand);">
                            Rp {{ number_format($paket->harga, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: var(--ink-45); font-size: 12.5px; font-weight: 600;">Kuota</td>
                        <td style="padding: 10px 0;">{{ $paket->kuota }} orang</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Recent Bookings -->
        <div class="panel">
            <div class="panel-head">
                <h2>Booking Terkait <span class="count">({{ $paket->pemesanan_count ?? 0 }})</span></h2>
            </div>
            
            @php
                $recentBookings = $paket->pemesanan()->with('jadwal')->latest()->take(5)->get();
            @endphp
            
            @if($recentBookings->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Kode Booking</th>
                            <th>Pelanggan</th>
                            <th>Tanggal</th>
                            <th class="num">Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentBookings as $booking)
                        <tr>
                            <td>
                                <a href="{{ route('admin.bookings.show', $booking->id) }}" class="code" style="color: var(--brand); font-weight: 600;">
                                    {{ $booking->kode_booking }}
                                </a>
                            </td>
                            <td>{{ $booking->nama_lengkap }}</td>
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
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="placeholder-panel" style="padding: 60px 20px;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="4" width="18" height="17" rx="1.5"/>
                        <path d="M3 9h18M8 3v3M16 3v3"/>
                    </svg>
                    <h3>Belum Ada Booking</h3>
                    <p>Paket ini belum memiliki booking</p>
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
                            {{ str_pad($paket->pemesanan_count ?? 0, 2, '0', STR_PAD_LEFT) }}
                        </div>
                    </div>
                    
                    <div style="border-top: 1px solid var(--line); padding-top: 14px;">
                        <div style="font-size: 11px; color: var(--ink-45); font-weight: 600; margin-bottom: 4px;">TOTAL PENDAPATAN</div>
                        <div style="font-size: 18px; font-weight: 700; font-family: 'IBM Plex Mono', monospace;">
                            @php
                                $totalRevenue = $paket->pemesanan()->where('status', 'paid')->sum('total_harga');
                            @endphp
                            Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Metadata -->
        <div class="panel">
            <div class="panel-head">
                <h2>Metadata</h2>
            </div>
            
            <div style="padding: 16px 18px;">
                <table style="width: 100%; font-size: 12.5px;">
                    <tr>
                        <td style="padding: 6px 0; color: var(--ink-45);">Dibuat</td>
                        <td style="padding: 6px 0; text-align: right; font-family: 'IBM Plex Mono', monospace;">
                            {{ $paket->created_at->format('d M Y, H:i') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: var(--ink-45);">Terakhir diubah</td>
                        <td style="padding: 6px 0; text-align: right; font-family: 'IBM Plex Mono', monospace;">
                            {{ $paket->updated_at->format('d M Y, H:i') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: var(--ink-45);">ID Paket</td>
                        <td style="padding: 6px 0; text-align: right; font-family: 'IBM Plex Mono', monospace;">
                            #{{ str_pad($paket->id, 4, '0', STR_PAD_LEFT) }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="panel">
            <div class="panel-head">
                <h2>Aksi</h2>
            </div>
            <div class="quick-list">
                <button class="quick-item" onclick="window.location.href='{{ route('admin.paket-wisata.edit', $paket->id) }}'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.1 2.1 0 0 1 3 3L12 15l-4 1 1-4Z"/>
                    </svg>
                    Edit Paket
                    <svg class="arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </button>
                <button class="quick-item" onclick="window.location.href='{{ route('admin.bookings.index') }}?paket={{ $paket->id }}'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="4" width="18" height="17" rx="1.5"/>
                        <path d="M3 9h18"/>
                    </svg>
                    Lihat Semua Booking
                    <svg class="arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </button>
                <button class="quick-item" onclick="confirmDelete()" style="color: var(--red);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    </svg>
                    Hapus Paket
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
function confirmDelete() {
    if (!confirm('Apakah Anda yakin ingin menghapus paket "{{ $paket->nama_paket }}"? Tindakan ini tidak dapat dibatalkan.')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('admin.paket-wisata.destroy', $paket->id) }}';
    
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
</script>
@endpush
@endsection
