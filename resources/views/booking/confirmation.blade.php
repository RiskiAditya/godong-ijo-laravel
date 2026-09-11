@extends('layouts.app')

@section('title', 'Konfirmasi Booking - ' . $booking->kode_booking)

@php
    $packageData = $booking->package_specific_data ?? [];
    $fishingType = $packageData['jenis_pemancingan'] ?? null;
    $fishingTypeLabel = $fishingType ? ucwords(str_replace('_', ' ', $fishingType)) : null;
    $needsRental = (bool) ($packageData['perlu_sewa_alat'] ?? false) || $fishingType === 'sewa_joran';
    $rodQuantity = $packageData['jumlah_joran'] ?? $booking->jumlah_orang;
    $rodSize = $packageData['ukuran_joran'] ?? null;
    $rodSizeLabel = match ($rodSize) {
        'standar' => 'Standar',
        'besar' => 'Besar',
        default => null,
    };
    $durasi = $packageData['durasi'] ?? null;
    $tambahanJam = (int) ($packageData['tambahan_jam'] ?? 0);
    $umpanData = $packageData['umpan'] ?? [];
    $tambahanUmpan = [];

    if (($umpanData['anak_ikan_komet'] ?? 0) > 0) {
        $tambahanUmpan[] = 'Anak Ikan Komet: ' . (int) $umpanData['anak_ikan_komet'] . ' pack';
    }

    if (($umpanData['umpan_jadi_godongijo'] ?? 0) > 0) {
        $tambahanUmpan[] = 'Umpan Jadi Godongijo: ' . (int) $umpanData['umpan_jadi_godongijo'] . ' pack';
    }
@endphp

@section('content')
<div class="confirmation-page">
    <div class="page-container">
        
        {{-- Success Alert --}}
        @if(request()->has('from_payment'))
        <div class="alert-success">
            <div class="alert-success-inner">
                <div class="alert-success-icon">
                    <svg viewBox="0 0 24 24" stroke="currentColor" fill="none">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="alert-title">Pembayaran Berhasil!</p>
                    <p class="alert-description">Booking Anda telah dikonfirmasi dan e-ticket sudah dikirim ke email.</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Header Success Icon --}}
        <section class="confirmation-hero">
            <div class="hero-icon">
                <svg viewBox="0 0 24 24" stroke="currentColor" fill="none">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1>Booking Berhasil!</h1>
            <p>Terima kasih telah memesan di Godong Ijo</p>
        </section>

        {{-- Booking Code Highlight --}}
        <div class="confirmation-code-card">
            <p class="label">Kode Booking Anda</p>
            <div class="code-section">
                <p class="code">{{ $booking->kode_booking }}</p>
            </div>
            <p class="description">Simpan kode ini untuk check-in</p>
        </div>

        {{-- Receipt Summary --}}
        <div class="receipt-panel">
            <div class="receipt-header">
                <div>
                    <p class="receipt-label">Ringkasan Struk</p>
                    <h2>Detail Pembayaran</h2>
                </div>
                <div class="receipt-icon">
                    <svg viewBox="0 0 24 24" stroke="currentColor" fill="none">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m2 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="receipt-grid">
                <div class="receipt-item receipt-item-highlight">
                    <p class="receipt-item-title">Total</p>
                    <p class="receipt-item-value">{{ $booking->total_harga !== null ? 'Rp ' . number_format($booking->total_harga, 0, ',', '.') : 'Dihitung saat ditimbang' }}</p>
                </div>
                <div class="receipt-item">
                    <p class="receipt-item-title">Orang</p>
                    <p class="receipt-item-value">{{ $booking->jumlah_orang }} orang</p>
                </div>
                <div class="receipt-item">
                    <p class="receipt-item-title">Tanggal</p>
                    <p class="receipt-item-value">{{ $booking->tanggal_kunjungan ? $booking->tanggal_kunjungan->format('d F Y') : ($booking->jadwal?->tanggal?->format('d F Y') ?? '-') }}</p>
                </div>
                <div class="receipt-item">
                    <p class="receipt-item-title">Status</p>
                    <p class="receipt-item-value">{{ $booking->pembayaran && $booking->pembayaran->status === 'success' ? 'Lunas' : 'Belum Lunas' }}</p>
                </div>
            </div>
        </div>

        @if($booking->pembayaran?->status === 'pending' && $booking->pembayaran?->snap_token)
            <div class="payment-resume-panel">
                <div>
                    <p class="payment-resume-label">Pembayaran belum selesai</p>
                    <p class="payment-resume-text">Lanjutkan pembayaran untuk mengonfirmasi booking {{ $booking->kode_booking }}.</p>
                </div>
                <button
                    type="button"
                    id="resume-payment-button"
                    class="button button-primary"
                    data-snap-token="{{ $booking->pembayaran->snap_token }}"
                    data-order-id="{{ $booking->pembayaran->order_id }}"
                    data-redirect-url="{{ route('booking.confirmation', ['kode_booking' => $booking->kode_booking]) }}"
                >
                    Lanjutkan Pembayaran
                </button>
            </div>
        @endif

        {{-- Booking Details --}}
        <div class="booking-card">
            <div class="booking-card-header">
                <svg viewBox="0 0 24 24" stroke="currentColor" fill="none">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h2>Detail Pemesanan</h2>
            </div>
            <div class="booking-grid">
                <div class="booking-item booking-item-full">
                    <p class="booking-item-label">Paket Wisata</p>
                    <p class="booking-item-value">{{ $booking->paketWisata?->nama_paket ?? 'Pemancingan' }}</p>
                </div>
                <div class="booking-item">
                    <p class="booking-item-label">Tanggal Kunjungan</p>
                    <p class="booking-item-value">{{ $booking->tanggal_kunjungan ? $booking->tanggal_kunjungan->format('d F Y') : ($booking->jadwal?->tanggal?->format('d F Y') ?? '-') }}</p>
                </div>
                @if($booking->paketWisata?->jenis_paket === 'Fishing Lake')
                    <div class="booking-item">
                        <p class="booking-item-label">Jam Kunjungan</p>
                        <p class="booking-item-value">{{ $booking->jam_kunjungan ?? ($packageData['jam_kunjungan'] ?? '-') }}</p>
                    </div>
                    <div class="booking-item">
                        <p class="booking-item-label">Jenis Pemancingan</p>
                        <p class="booking-item-value">{{ $fishingTypeLabel ?? '-' }}</p>
                    </div>
                    @if($fishingType === 'tarikan' && $durasi)
                        <div class="booking-item">
                            <p class="booking-item-label">Durasi</p>
                            <p class="booking-item-value">{{ $durasi }} jam</p>
                        </div>
                        @if($tambahanJam > 0)
                            <div class="booking-item">
                                <p class="booking-item-label">Tambahan Jam</p>
                                <p class="booking-item-value">{{ $tambahanJam }} jam</p>
                            </div>
                        @endif
                    @endif
                    <div class="booking-item">
                        <p class="booking-item-label">Jumlah Joran</p>
                        <p class="booking-item-value">{{ $rodQuantity }} joran</p>
                    </div>
                    @if($needsRental)
                        <div class="booking-item">
                            <p class="booking-item-label">Sewa Alat</p>
                            <p class="booking-item-value">
                                @if($rodSizeLabel)
                                    Ya • {{ $rodSizeLabel }}
                                @else
                                    Ya
                                @endif
                            </p>
                        </div>
                        @if($rodSizeLabel)
                            <div class="booking-item">
                                <p class="booking-item-label">Ukuran Joran</p>
                                <p class="booking-item-value">{{ $rodSizeLabel }}</p>
                            </div>
                        @endif
                    @endif
                    @if(! empty($tambahanUmpan))
                        <div class="booking-item booking-item-full">
                            <p class="booking-item-label">Tambahan Umpan</p>
                            <p class="booking-item-value">{{ implode(' • ', $tambahanUmpan) }}</p>
                        </div>
                    @endif
                @endif
                <div class="booking-item booking-item-highlight">
                    <p class="booking-item-label">Total Pembayaran</p>
                    <p class="booking-item-value">{{ $booking->total_harga !== null ? 'Rp ' . number_format($booking->total_harga, 0, ',', '.') : 'Dihitung saat ditimbang' }}</p>
                </div>
                <div class="booking-item booking-item-status">
                    <p class="booking-item-label">Status Pembayaran</p>
                    <p class="booking-item-value">
                        @if($booking->pembayaran && $booking->pembayaran->status === 'success')
                            <span class="confirmation-badge badge-success">Lunas</span>
                        @else
                            <span class="confirmation-badge badge-pending">Menunggu Pembayaran</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Customer Info --}}
        <div class="customer-card">
            <div class="customer-card-header">
                <svg viewBox="0 0 24 24" stroke="currentColor" fill="none">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <h2>Informasi Pemesan</h2>
            </div>
            <div class="customer-card-body">
                <div class="customer-detail-row">
                    <span class="detail-label">Nama Lengkap</span>
                    <span class="detail-value">{{ $booking->nama_lengkap }}</span>
                </div>
                @if($booking->email)
                <div class="customer-detail-row">
                    <span class="detail-label">Email</span>
                    <a href="mailto:{{ $booking->email }}" class="detail-link">{{ $booking->email }}</a>
                </div>
                @endif
                <div class="customer-detail-row">
                    <span class="detail-label">No. WhatsApp</span>
                    <a href="https://wa.me/62{{ ltrim($booking->no_hp, '0') }}" target="_blank" class="detail-link">{{ $booking->no_hp }}</a>
                </div>
            </div>
        </div>

        <div class="print-only-document" aria-label="Dokumen booking untuk cetak PDF">
            <div class="print-brand-row">
                <div class="print-brand-wrap">
                    <img src="{{ asset('images/placeholders/The-Waterfall-Logo-removebg-preview.png') }}" alt="Godong Ijo Logo" class="print-brand-logo">
                </div>
                <div class="print-status-pill">Booking Berhasil</div>
            </div>

            <div class="print-header-card">
                <div class="print-header-label">Kode Booking</div>
                <div class="print-booking-code">{{ $booking->kode_booking }}</div>
                <div class="print-header-note">Bukti reservasi pelanggan • {{ $booking->tanggal_kunjungan ? $booking->tanggal_kunjungan->format('d F Y') : ($booking->jadwal?->tanggal?->format('d F Y') ?? '-') }}</div>
            </div>

            <div class="print-summary-grid">
                <div class="print-summary-card print-summary-card--primary">
                    <div class="print-summary-label">Total Pembayaran</div>
                    <div class="print-summary-value">{{ $booking->total_harga !== null ? 'Rp ' . number_format($booking->total_harga, 0, ',', '.') : 'Dihitung saat ditimbang' }}</div>
                </div>
                <div class="print-summary-card">
                    <div class="print-summary-label">Status</div>
                    <div class="print-summary-value small">{{ $booking->pembayaran && $booking->pembayaran->status === 'success' ? 'Lunas' : 'Menunggu Pembayaran' }}</div>
                </div>
                <div class="print-summary-card">
                    <div class="print-summary-label">Jumlah Orang</div>
                    <div class="print-summary-value small">{{ $booking->jumlah_orang }} orang</div>
                </div>
                <div class="print-summary-card">
                    <div class="print-summary-label">Waktu Kunjungan</div>
                    <div class="print-summary-value small">{{ $booking->jam_kunjungan ?? ($packageData['jam_kunjungan'] ?? '-') }}</div>
                </div>
            </div>

            <div class="print-detail-grid">
                <div class="print-detail-card">
                    <div class="print-card-title">Informasi Pemesan</div>
                    <div class="print-row"><span>Nama</span><strong>{{ $booking->nama_lengkap }}</strong></div>
                    <div class="print-row"><span>Email</span><strong>{{ $booking->email ?? '-' }}</strong></div>
                    <div class="print-row"><span>WhatsApp</span><strong>{{ $booking->no_hp ?? '-' }}</strong></div>
                </div>

                <div class="print-detail-card">
                    <div class="print-card-title">Detail Pemesanan</div>
                    <div class="print-row"><span>Paket</span><strong>{{ $booking->paketWisata?->nama_paket ?? 'Pemancingan' }}</strong></div>
                    <div class="print-row"><span>Tanggal</span><strong>{{ $booking->tanggal_kunjungan ? $booking->tanggal_kunjungan->format('d F Y') : ($booking->jadwal?->tanggal?->format('d F Y') ?? '-') }}</strong></div>
                    <div class="print-row"><span>Jam</span><strong>{{ $booking->jam_kunjungan ?? ($packageData['jam_kunjungan'] ?? '-') }}</strong></div>
                    <div class="print-row"><span>Metode</span><strong>{{ $booking->pembayaran?->payment_method ?? 'Midtrans' }}</strong></div>
                </div>
            </div>

            <div class="print-note-box">
                <div class="print-note-title">Informasi penting</div>
                <ul>
                    <li>Tunjukkan kode booking ini saat check-in di lokasi.</li>
                    <li>Mohon datang 15 menit sebelum jadwal kunjungan.</li>
                    <li>Konfirmasi detail booking ini telah dikirim ke email pemesan.</li>
                </ul>
            </div>

            <div class="print-footer">
                <div>Godong Ijo · Curug Nangka, Bogor, Jawa Barat</div>
                <div>Email: {{ $booking->email ?? 'info@godongijo.com' }} · WhatsApp: {{ $booking->no_hp ?? '0812-3456-7890' }}</div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="button-grid mb-6">
            <a href="https://wa.me/62{{ ltrim($booking->no_hp, '0') }}?text=Halo!%20Booking%20saya%20sudah%20dikonfirmasi.%20Kode%20Booking:%20{{ $booking->kode_booking }}" target="_blank" class="button button-outline">
                <svg style="width: 20px; height: 20px;" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                Hubungi WhatsApp
            </a>
            <button type="button" onclick="window.print()" class="button button-muted">
                <svg style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak
            </button>
            <a href="{{ route('landing') }}" class="button button-outline">
                <svg style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Kembali ke Home
            </a>
        </div>

        {{-- Important Info --}}
        <div class="info-card">
            <div class="info-card-inner">
                <svg style="width: 20px; height: 20px; display: inline-block; color: #2563eb; margin-top: 0.125rem; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="info-content">
                    <h3>Informasi Penting</h3>
                    <ul class="info-list">
                        <li>Tunjukkan kode booking atau e-ticket saat check-in</li>
                        <li>Mohon datang 15 menit sebelum waktu kunjungan</li>
                        <li>Konfirmasi booking telah dikirim ke email Anda</li>
                        <li>Untuk pertanyaan, hubungi kami via WhatsApp</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Contact Support --}}
        <div class="support-block">
            <p>Butuh bantuan?</p>
            <a href="https://wa.me/6281234567890" target="_blank" class="support-link">
                <svg style="width: 20px; height: 20px;" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                WhatsApp: 0812-3456-7890
            </a>
        </div>

    </div>
</div>

<style>
.confirmation-page {
    min-height: calc(100vh - 88px);
    background: #f7faf6;
    padding: 48px 0 60px;
}
.page-container {
    max-width: 920px;
    margin: 0 auto;
    padding: 0 24px;
}
.alert-success {
    background: #ffffff;
    border-left: 4px solid #16a34a;
    border-radius: 18px;
    box-shadow: 0 15px 40px rgba(15, 23, 42, 0.06);
    padding: 18px 22px;
    margin-bottom: 32px;
}
.alert-success-inner {
    display: flex;
    gap: 16px;
    align-items: flex-start;
}
.alert-success-icon {
    min-width: 36px;
    min-height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #ecfdf5;
    border-radius: 50%;
    color: #16a34a;
}
.alert-success-icon svg {
    width: 20px;
    height: 20px;
}
.alert-title {
    margin: 0 0 4px;
    font-weight: 700;
    color: #0f172a;
}
.alert-description {
    margin: 0;
    color: #475569;
}
.payment-resume-panel {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 20px;
    padding: 20px 24px;
    margin-bottom: 28px;
}
.payment-resume-label {
    margin: 0 0 4px;
    color: #92400e;
    font-weight: 700;
}
.payment-resume-text {
    margin: 0;
    color: #78350f;
}
@media (max-width: 640px) {
    .payment-resume-panel {
        align-items: stretch;
        flex-direction: column;
    }
}
.confirmation-hero {
    background: linear-gradient(180deg, rgba(236, 253, 245, 0.95) 0%, rgba(220, 252, 231, 0.95) 100%);
    border: 1px solid rgba(16, 185, 129, 0.18);
    border-radius: 32px;
    padding: 40px 32px;
    margin-bottom: 28px;
    box-shadow: 0 30px 70px rgba(15, 23, 42, 0.08);
    text-align: center;
}
.hero-icon {
    width: 84px;
    height: 84px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    margin: 0 auto 20px;
    color: #ffffff;
}
.hero-icon svg {
    width: 32px;
    height: 32px;
}
.confirmation-hero h1 {
    font-size: clamp(2rem, 2.4vw, 2.6rem);
    color: #0f172a;
    margin: 0 0 10px;
}
.confirmation-hero p {
    margin: 0;
    color: #4b5563;
    font-size: 1rem;
}
.confirmation-code-card {
    background: #ffffff;
    border: 1px solid rgba(209, 250, 229, 0.9);
    border-radius: 28px;
    padding: 28px;
    text-align: center;
    box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
    margin-bottom: 28px;
}
.confirmation-code-card .label {
    color: #16a34a;
    font-weight: 700;
    margin-bottom: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-size: 0.9rem;
}
.code-section {
    background: rgba(236, 253, 245, 0.95);
    border-radius: 22px;
    padding: 20px 18px;
    margin: 0 auto 12px;
    display: inline-block;
}
.confirmation-code-card .code {
    color: #065f46;
    font-size: 2rem;
    font-weight: 800;
    letter-spacing: 0.15em;
    margin: 0;
}
.description {
    margin: 0;
    color: #16a34a;
    font-weight: 600;
}
.receipt-panel {
    background: #ffffff;
    border-radius: 28px;
    border: 1px solid rgba(209, 250, 229, 0.9);
    padding: 28px;
    box-shadow: 0 24px 60px rgba(15, 23, 42, 0.06);
    margin-bottom: 28px;
}
.receipt-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 22px;
}
.receipt-header h2 {
    margin: 0;
    color: #0f172a;
    font-size: 1.75rem;
}
.receipt-label {
    margin: 0 0 6px;
    color: #16a34a;
    font-weight: 700;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}
.receipt-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: rgba(4, 120, 87, 0.12);
    color: #047857;
}
.receipt-icon svg {
    width: 22px;
    height: 22px;
}
.receipt-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
}
.receipt-item {
    background: #f8faf8;
    border-radius: 24px;
    padding: 18px;
}
.receipt-item-highlight {
    background: #ecfdf5;
}
.receipt-item-title {
    margin: 0 0 6px;
    color: #4b5563;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}
.receipt-item-value {
    margin: 0;
    color: #065f46;
    font-size: 1.5rem;
    font-weight: 700;
}
.customer-card {
    background: #ffffff;
    border: 1px solid rgba(226, 232, 240, 0.95);
    border-radius: 28px;
    overflow: hidden;
    box-shadow: 0 20px 50px rgba(15, 23, 42, 0.06);
    margin-bottom: 28px;
}
.customer-card-header {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 22px 26px;
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    border-bottom: 1px solid rgba(209, 250, 229, 0.9);
}
.customer-card-header svg {
    width: 22px;
    height: 22px;
    color: #059669;
}
.customer-card-header h2 {
    margin: 0;
    font-size: 1.05rem;
    color: #134e4a;
}
.customer-card-body {
    padding: 26px 26px 32px;
    display: grid;
    gap: 16px;
}
.customer-detail-row {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    align-items: center;
    padding: 14px 0;
    border-bottom: 1px solid #f1f5f9;
}
.customer-detail-row:last-child {
    border-bottom: none;
}
.detail-label {
    color: #475569;
    font-weight: 600;
}
.detail-value,
.detail-link {
    color: #0f172a;
    font-weight: 700;
    text-align: right;
}
.detail-link {
    text-decoration: none;
}
.detail-link:hover {
    text-decoration: underline;
}
.booking-card {
    background: #ffffff;
    border-radius: 28px;
    border: 1px solid rgba(226, 232, 240, 0.95);
    box-shadow: 0 20px 55px rgba(15, 23, 42, 0.06);
    margin-bottom: 28px;
    overflow: hidden;
}
.booking-card-header {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 24px 26px;
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    border-bottom: 1px solid rgba(209, 250, 229, 0.9);
}
.booking-card-header h2 {
    margin: 0;
    font-size: 1.05rem;
    color: #134e4a;
}
.booking-card-header svg {
    width: 22px;
    height: 22px;
    color: #059669;
}
.booking-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
    padding: 24px 26px 28px;
}
.booking-item {
    background: #f8faf8;
    border-radius: 22px;
    padding: 18px 20px;
}
.booking-item-full {
    grid-column: span 2;
}
.booking-item-highlight {
    background: #ecfdf5;
}
.booking-item-status {
    grid-column: span 2;
}
.booking-item-label {
    margin: 0 0 6px;
    color: #475569;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-weight: 700;
}
.booking-item-value {
    margin: 0;
    color: #0f172a;
    font-size: 1.1rem;
    font-weight: 700;
}
.button-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
    margin-bottom: 28px;
}
.button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 16px 22px;
    border-radius: 24px;
    font-weight: 700;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    min-height: 56px;
    text-align: center;
}
.button svg {
    flex-shrink: 0;
}
.button:hover {
    transform: translateY(-1px);
}
.button-primary {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    color: #ffffff;
    box-shadow: 0 18px 40px rgba(5, 150, 105, 0.18);
}
.button-outline {
    background: #ffffff;
    border: 1px solid #d1fae5;
    color: #065f46;
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
}
.button-muted {
    background: #f8faf8;
    border: 1px solid #e5e7eb;
    color: #1f2937;
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
}
.info-card {
    background: #eff6ff;
    border-radius: 24px;
    border: 1px solid rgba(147, 197, 253, 0.5);
    padding: 24px;
    margin-bottom: 24px;
}
.info-card-inner {
    display: flex;
    gap: 16px;
    align-items: flex-start;
}
.info-content h3 {
    margin: 0 0 10px;
    color: #1d4ed8;
    font-size: 1rem;
}
.info-list {
    margin: 0;
    padding-left: 0;
    list-style: none;
    color: #1e3a8a;
    display: grid;
    gap: 10px;
}
.info-list li {
    position: relative;
    padding-left: 20px;
}
.info-list li::before {
    content: '•';
    position: absolute;
    left: 0;
    color: #2563eb;
}
.support-block {
    text-align: center;
    margin-bottom: 32px;
}
.support-block p {
    margin: 0 0 10px;
    color: #475569;
}
.support-link {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    color: #16a34a;
    font-weight: 700;
    text-decoration: none;
}
.support-link:hover {
    color: #04864f;
}
.print-only-document {
    display: none;
}
@media (max-width: 768px) {
    .page-container {
        padding: 0 16px;
    }
    .button-grid,
    .receipt-grid {
        grid-template-columns: 1fr;
    }
}
@media print {
    :root {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }

    @page {
        size: A4;
        margin: 12mm;
    }

    body {
        background: #ffffff !important;
        color: #111827 !important;
        font-size: 11px;
    }

    header,
    nav,
    footer,
    .skip-nav,
    .whatsapp-float,
    .ai-chatbot,
    .button-grid,
    .alert-success,
    .payment-resume-panel,
    .info-card,
    .support-block,
    .no-print,
    .button,
    .detail-link,
    .support-link,
    .receipt-icon,
    .customer-card-header svg,
    .booking-card-header svg {
        display: none !important;
    }

    .confirmation-page {
        min-height: auto;
        background: #ffffff;
        padding: 0;
    }

    .page-container {
        max-width: 100%;
        padding: 0;
    }

    .confirmation-hero,
    .confirmation-code-card,
    .receipt-panel,
    .customer-card,
    .booking-card,
    .button-grid,
    .info-card,
    .support-block,
    .alert-success,
    .payment-resume-panel {
        display: none !important;
    }

    .print-only-document {
        display: block !important;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 18px 20px;
        box-shadow: none;
        color: #111827;
    }

    .print-brand-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-bottom: 16px;
        border-bottom: 2px solid #e5e7eb;
        margin-bottom: 16px;
    }

    .print-brand-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .print-brand-logo {
        width: 120px;
        height: auto;
        max-height: 72px;
        object-fit: contain;
        display: block;
    }

    .print-status-pill {
        background: #ecfdf5;
        border: 1px solid #bbf7d0;
        color: #166534;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .print-header-card {
        background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 16px;
    }

    .print-header-label {
        margin: 0 0 6px;
        color: #6b7280;
        font-size: 9px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .print-booking-code {
        font-size: 28px;
        font-weight: 800;
        letter-spacing: 0.18em;
        color: #166534;
    }

    .print-header-note {
        margin-top: 6px;
        color: #475569;
        font-size: 10px;
    }

    .print-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 16px;
    }

    .print-summary-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px 12px;
    }

    .print-summary-card--primary {
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    .print-summary-label {
        color: #6b7280;
        font-size: 9px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .print-summary-value {
        color: #111827;
        font-size: 14px;
        font-weight: 800;
    }

    .print-summary-value.small {
        font-size: 12px;
    }

    .print-detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 16px;
    }

    .print-detail-card {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 12px 14px;
        background: #ffffff;
    }

    .print-card-title {
        margin: 0 0 10px;
        font-size: 13px;
        font-weight: 800;
        color: #111827;
    }

    .print-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 8px 0;
        border-bottom: 1px solid #f3f4f6;
        font-size: 11px;
    }

    .print-row:last-child {
        border-bottom: none;
    }

    .print-row span {
        color: #6b7280;
    }

    .print-row strong {
        color: #111827;
        text-align: right;
        font-weight: 700;
    }

    .print-note-box {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 12px 14px;
        background: #f9fafb;
        margin-bottom: 16px;
    }

    .print-note-title {
        margin: 0 0 8px;
        font-size: 12px;
        font-weight: 800;
        color: #111827;
    }

    .print-note-box ul {
        margin: 0;
        padding-left: 18px;
        color: #475569;
        font-size: 11px;
        line-height: 1.6;
    }

    .print-footer {
        border-top: 1px solid #e5e7eb;
        padding-top: 12px;
        color: #475569;
        font-size: 10px;
        line-height: 1.6;
        text-align: center;
    }

    a[href]:after {
        content: none !important;
    }
}
</style>
@endsection
