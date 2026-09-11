@extends('layouts.admin')

@section('title', 'Detail Booking')
@section('page-title', 'Booking Detail')

@section('content')
<div class="page-head">
    <div>
        <h1>Detail Booking</h1>
        <p class="page-sub">
            <a href="{{ route('admin.bookings.index') }}" style="color: var(--brand); font-weight: 600;">← Kembali ke Daftar Booking</a>
        </p>
    </div>
    <div class="head-actions">
        @if($booking->status === 'pending')
        <button class="btn-ghost" onclick="updateStatus('paid')">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 6L9 17l-5-5"/>
            </svg>
            Tandai Lunas
        </button>
        <button class="btn-ghost" onclick="updateStatus('cancelled')" style="color: var(--red); border-color: var(--red);">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="9"/>
                <path d="m15 9-6 6M9 9l6 6"/>
            </svg>
            Batalkan
        </button>
        @endif
        <button class="btn-solid" onclick="window.print()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                <rect x="6" y="14" width="12" height="8"/>
            </svg>
            Print
        </button>
    </div>
</div>

<div class="grid-main">
    <!-- Main Info -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <!-- Booking Info -->
        <div class="panel">
            <div class="panel-head">
                <div>
                    <h2>Informasi Booking</h2>
                    <div style="margin-top: 4px;">
                        <span class="code" style="font-size: 13px;">{{ $booking->kode_booking }}</span>
                    </div>
                </div>
                <span class="status {{ $booking->status }}" style="font-size: 13px;">
                    <span class="status-dot"></span>
                    {{ ucfirst($booking->status) }}
                </span>
            </div>
            
            <div style="padding: 20px 18px;">
                <table style="width: 100%;">
                    <tr>
                        <td style="padding: 10px 0; color: var(--ink-45); font-size: 12.5px; font-weight: 600; width: 180px;">Paket Wisata</td>
                        <td style="padding: 10px 0; font-weight: 600;">{{ $booking->paketWisata->nama_paket ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: var(--ink-45); font-size: 12.5px; font-weight: 600;">Tanggal Kunjungan</td>
                        <td style="padding: 10px 0; font-family: 'IBM Plex Mono', monospace;">
                            {{ $booking->tanggal_kunjungan
                                ? $booking->tanggal_kunjungan->format('l, d F Y')
                                : ($booking->jadwal ? $booking->jadwal->tanggal->format('l, d F Y') : '-') }}
                        </td>
                    </tr>
                    @if(data_get($booking->package_specific_data, 'jam_kunjungan'))
                    <tr>
                        <td style="padding: 10px 0; color: var(--ink-45); font-size: 12.5px; font-weight: 600;">Jam Kunjungan</td>
                        <td style="padding: 10px 0; font-family: 'IBM Plex Mono', monospace;">
                            {{ data_get($booking->package_specific_data, 'jam_kunjungan') }} WIB
                        </td>
                    </tr>
                    @endif
                    @if(($booking->paketWisata?->jenis_paket ?? null) === 'Fishing Lake')
                        @php
                            $adminFishingData = $booking->package_specific_data ?? [];
                            $adminFishingType = $adminFishingData['jenis_pemancingan'] ?? null;
                            $adminFishingTypeLabel = $adminFishingType ? ucfirst(str_replace('_', ' ', $adminFishingType)) : '-';
                            $adminFishingDurasi = $adminFishingData['durasi'] ?? null;
                            $adminFishingTambahanJam = (int) ($adminFishingData['tambahan_jam'] ?? 0);
                            $adminFishingJoran = $adminFishingData['jumlah_joran'] ?? $booking->jumlah_orang;
                            $adminFishingRodSize = $adminFishingData['ukuran_joran'] ?? null;
                            $adminFishingRodSizeLabel = match ($adminFishingRodSize) {
                                'standar' => 'Standar',
                                'besar' => 'Besar',
                                default => null,
                            };
                            $adminFishingRequiresRental = (bool) ($adminFishingData['perlu_sewa_alat'] ?? false) || $adminFishingType === 'sewa_joran';
                            $adminFishingBait = $adminFishingData['umpan'] ?? [];
                            $adminFishingExtraBait = [];
                            if (($adminFishingBait['anak_ikan_komet'] ?? 0) > 0) {
                                $adminFishingExtraBait[] = 'Anak Ikan Komet: ' . (int) $adminFishingBait['anak_ikan_komet'] . ' pack';
                            }
                            if (($adminFishingBait['umpan_jadi_godongijo'] ?? 0) > 0) {
                                $adminFishingExtraBait[] = 'Umpan Jadi Godongijo: ' . (int) $adminFishingBait['umpan_jadi_godongijo'] . ' pack';
                            }
                        @endphp
                        <tr>
                            <td style="padding: 10px 0; color: var(--ink-45); font-size: 12.5px; font-weight: 600;">Jenis Pemancingan</td>
                            <td style="padding: 10px 0;">{{ $adminFishingTypeLabel }}</td>
                        </tr>
                        @if($adminFishingDurasi)
                        <tr>
                            <td style="padding: 10px 0; color: var(--ink-45); font-size: 12.5px; font-weight: 600;">Durasi</td>
                            <td style="padding: 10px 0;">{{ $adminFishingDurasi }} jam</td>
                        </tr>
                        @endif
                        @if($adminFishingTambahanJam > 0)
                        <tr>
                            <td style="padding: 10px 0; color: var(--ink-45); font-size: 12.5px; font-weight: 600;">Tambahan Jam</td>
                            <td style="padding: 10px 0;">{{ $adminFishingTambahanJam }} jam</td>
                        </tr>
                        @endif
                        <tr>
                            <td style="padding: 10px 0; color: var(--ink-45); font-size: 12.5px; font-weight: 600;">Jumlah Joran</td>
                            <td style="padding: 10px 0;">{{ $adminFishingJoran }} joran</td>
                        </tr>
                        @if($adminFishingRequiresRental)
                        <tr>
                            <td style="padding: 10px 0; color: var(--ink-45); font-size: 12.5px; font-weight: 600;">Sewa Alat</td>
                            <td style="padding: 10px 0;">{{ $adminFishingRodSizeLabel ? 'Ya • ' . $adminFishingRodSizeLabel : 'Ya' }}</td>
                        </tr>
                        @endif
                        @if($adminFishingRodSizeLabel)
                        <tr>
                            <td style="padding: 10px 0; color: var(--ink-45); font-size: 12.5px; font-weight: 600;">Ukuran Joran</td>
                            <td style="padding: 10px 0;">{{ $adminFishingRodSizeLabel }}</td>
                        </tr>
                        @endif
                        @if(! empty($adminFishingExtraBait))
                        <tr>
                            <td style="padding: 10px 0; color: var(--ink-45); font-size: 12.5px; font-weight: 600; vertical-align: top;">Tambahan Umpan</td>
                            <td style="padding: 10px 0;">{{ implode(' • ', $adminFishingExtraBait) }}</td>
                        </tr>
                        @endif
                    @endif
                    <tr>
                        <td style="padding: 10px 0; color: var(--ink-45); font-size: 12.5px; font-weight: 600;">Jumlah Orang</td>
                        <td style="padding: 10px 0;">{{ $booking->jumlah_orang }} orang</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: var(--ink-45); font-size: 12.5px; font-weight: 600;">Harga per Orang</td>
                        <td style="padding: 10px 0; font-family: 'IBM Plex Mono', monospace;">
                            Rp {{ number_format($booking->paketWisata->harga ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr style="border-top: 2px solid var(--line);">
                        <td style="padding: 14px 0 10px; color: var(--ink); font-size: 13.5px; font-weight: 700;">Total Harga</td>
                        <td style="padding: 14px 0 10px; font-family: 'IBM Plex Mono', monospace; font-weight: 700; font-size: 18px; color: var(--brand);">
                            {{ $booking->total_harga !== null ? 'Rp ' . number_format($booking->total_harga, 0, ',', '.') : 'Dihitung saat ditimbang' }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        @if(($booking->package_specific_data['jenis_pemancingan'] ?? null) === 'kiloan')
        <div class="panel">
            <div class="panel-head">
                <div>
                    <h2>Finalisasi Harga Kiloan</h2>
                    <div style="margin-top: 4px; font-size: 11.5px; color: var(--ink-45);">
                        Masukkan hasil timbangan dan harga final setelah ikan ditimbang.
                    </div>
                </div>
            </div>

            <div style="padding: 20px 18px;">
                <form method="POST" action="{{ route('admin.bookings.finalize-kiloan', $booking) }}">
                    @csrf
                    <div style="display: grid; grid-template-columns: repeat(3, minmax(180px, 1fr)); gap: 12px; align-items: end;">
                        <div class="form-group" style="margin: 0;">
                            <label for="berat_kg">Berat Ikan (kg)</label>
                            <input type="number" name="berat_kg" min="0" step="0.01" value="{{ data_get($booking->package_specific_data, 'berat_kg') ?? '' }}">
                        </div>
                        <div class="form-group" style="margin: 0;">
                            <label for="hasil_timbangan">Hasil Timbangan</label>
                            <input type="text" name="hasil_timbangan" value="{{ data_get($booking->package_specific_data, 'hasil_timbangan') ?? '' }}" placeholder="Jenis/hasil timbangan">
                        </div>
                        <div class="form-group" style="margin: 0;">
                            <label for="total_harga">Harga Final (Rp)</label>
                            <input type="number" name="total_harga" min="0" step="1000" value="{{ $booking->total_harga ?? '' }}">
                        </div>
                    </div>
                    <div style="margin-top: 14px;">
                        <button type="submit" class="btn-solid">Simpan Harga Final</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- Customer Info -->
        <div class="panel">
            <div class="panel-head">
                <h2>Informasi Pelanggan</h2>
            </div>
            
            <div style="padding: 20px 18px;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                    <div style="width: 48px; height: 48px; border-radius: 8px; background: var(--brand-soft); color: var(--brand); display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 700; font-family: 'IBM Plex Mono', monospace;">
                        {{ strtoupper(substr($booking->nama_lengkap, 0, 2)) }}
                    </div>
                    <div>
                        <div style="font-size: 15px; font-weight: 600; margin-bottom: 2px;">{{ $booking->nama_lengkap }}</div>
                        <div style="font-size: 12px; color: var(--ink-45);">
                            @if($booking->user_id)
                                Pelanggan terdaftar
                            @else
                                Guest checkout
                            @endif
                        </div>
                    </div>
                </div>
                
                <table style="width: 100%;">
                    <tr>
                        <td style="padding: 8px 0; color: var(--ink-45); font-size: 12.5px; font-weight: 600; width: 140px;">Email</td>
                        <td style="padding: 8px 0;">
                            <a href="mailto:{{ $booking->email }}" style="color: var(--brand); font-weight: 500;">{{ $booking->email }}</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: var(--ink-45); font-size: 12.5px; font-weight: 600;">No. Handphone</td>
                        <td style="padding: 8px 0; font-family: 'IBM Plex Mono', monospace;">
                            <a href="tel:{{ $booking->no_hp }}" style="color: var(--brand); font-weight: 500;">{{ $booking->no_hp }}</a>
                        </td>
                    </tr>
                    @if($booking->catatan)
                    <tr>
                        <td style="padding: 8px 0; color: var(--ink-45); font-size: 12.5px; font-weight: 600; vertical-align: top;">Catatan</td>
                        <td style="padding: 8px 0; color: var(--ink-70); line-height: 1.6;">{{ $booking->catatan }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Payment Info -->
        @if($booking->pembayaran)
        <div class="panel">
            <div class="panel-head">
                <h2>Informasi Pembayaran</h2>
            </div>
            
            <div style="padding: 20px 18px;">
                <table style="width: 100%;">
                    <tr>
                        <td style="padding: 8px 0; color: var(--ink-45); font-size: 12.5px; font-weight: 600; width: 180px;">Order ID</td>
                        <td style="padding: 8px 0; font-family: 'IBM Plex Mono', monospace; font-size: 12px;">
                            {{ $booking->pembayaran->order_id }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: var(--ink-45); font-size: 12.5px; font-weight: 600;">Metode Pembayaran</td>
                        <td style="padding: 8px 0;">{{ strtoupper($booking->pembayaran->payment_type ?? 'N/A') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: var(--ink-45); font-size: 12.5px; font-weight: 600;">Status</td>
                        <td style="padding: 8px 0;">
                            <span class="status {{ $booking->pembayaran->status === 'success' ? 'lunas' : 'pending' }}">
                                <span class="status-dot"></span>
                                {{ $booking->pembayaran->status === 'success' ? 'Lunas' : ucfirst($booking->pembayaran->status) }}
                            </span>
                        </td>
                    </tr>
                    @if($booking->pembayaran->settlement_time)
                    <tr>
                        <td style="padding: 8px 0; color: var(--ink-45); font-size: 12.5px; font-weight: 600;">Waktu Pembayaran</td>
                        <td style="padding: 8px 0; font-family: 'IBM Plex Mono', monospace;">
                            {{ \Carbon\Carbon::parse($booking->pembayaran->settlement_time)->format('d M Y, H:i') }}
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <!-- Timeline -->
        <div class="panel">
            <div class="panel-head">
                <h2>Timeline</h2>
            </div>
            
            <div style="padding: 18px;">
                <div class="activity-item" style="border: none; padding: 0 0 14px;">
                    <div class="activity-icon act-booking">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                    </div>
                    <div class="activity-body">
                        <div class="activity-msg">Booking dibuat</div>
                        <div class="activity-meta">{{ $booking->created_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>
                
                @if($booking->status === 'paid')
                <div class="activity-item" style="border: none; padding: 14px 0;">
                    <div class="activity-icon" style="background: var(--brand); color: #fff;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M20 6L9 17l-5-5"/>
                        </svg>
                    </div>
                    <div class="activity-body">
                        <div class="activity-msg">Pembayaran berhasil</div>
                        <div class="activity-meta">{{ $booking->updated_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>
                @endif
                
                @if($booking->status === 'cancelled')
                <div class="activity-item" style="border: none; padding: 14px 0;">
                    <div class="activity-icon" style="background: var(--red-soft); color: var(--red);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="m15 9-6 6M9 9l6 6"/>
                        </svg>
                    </div>
                    <div class="activity-body">
                        <div class="activity-msg">Booking dibatalkan</div>
                        <div class="activity-meta">{{ $booking->updated_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="panel">
            <div class="panel-head">
                <h2>Aksi</h2>
            </div>
            <div class="quick-list">
                @if($booking->status === 'paid')
                <button class="quick-item" onclick="alert('Fitur download e-ticket akan segera tersedia')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                    </svg>
                    Download E-Ticket
                    <svg class="arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </button>
                @endif
                <button class="quick-item" onclick="sendEmailNotification()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="2" y="4" width="20" height="16" rx="2"/>
                        <path d="m2 7 10 6 10-6"/>
                    </svg>
                    Kirim Email
                    <svg class="arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </button>
                <button class="quick-item" onclick="window.location.href='https://wa.me/{{ preg_replace('/[^0-9]/', '', $booking->no_hp) }}'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8v.5z"/>
                    </svg>
                    Hubungi via WhatsApp
                    <svg class="arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </button>
                <button class="quick-item" onclick="confirmDelete()" style="color: var(--red);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    </svg>
                    Hapus Booking
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
function updateStatus(status) {
    const statusText = status === 'paid' ? 'lunas' : 'dibatalkan';
    if (!confirm(`Apakah Anda yakin ingin mengubah status booking ini menjadi ${statusText}?`)) {
        return;
    }
    
    fetch(`/admin/bookings/{{ $booking->id }}/update-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => {
        if (response.ok) {
            showToast(`Status berhasil diubah menjadi ${statusText}`, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            throw new Error('Failed to update status');
        }
    })
    .catch(error => {
        showToast('Gagal mengubah status', 'error');
        console.error('Error:', error);
    });
}

function sendEmailNotification() {
    fetch('/admin/bookings/{{ $booking->id }}/send-email', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ type: 'booking' })
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Email gagal dikirim');
        }
        showToast(data.message, 'success');
    })
    .catch(error => {
        showToast(error.message, 'error');
        console.error('Error:', error);
    });
}

function confirmDelete() {
    if (!confirm('Apakah Anda yakin ingin menghapus booking ini? Tindakan ini tidak dapat dibatalkan.')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/bookings/{{ $booking->id }}';
    
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
</script>
@endpush
@endsection
