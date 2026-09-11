=================================================
KONFIRMASI BOOKING - THE WATERFALL TOURISM
=================================================

Halo {{ $pemesanan->nama_lengkap }},

Terima kasih atas booking Anda! Booking dengan kode {{ $pemesanan->kode_booking }} telah berhasil dibuat untuk paket {{ $paket->nama_paket }}.

=================================================
DETAIL PEMESANAN
=================================================

Kode Booking      : {{ $pemesanan->kode_booking }}
Paket Wisata      : {{ $packageDisplayName ?? $paket?->nama_paket ?? 'Paket Wisata' }}
@if(($packageType ?? null) === 'private-room')
Jenis Acara       : {{ ucfirst($pemesanan->package_specific_data['event_type'] ?? '-') }}
@elseif(($packageType ?? null) === 'fishing-lake')
@php
$fishingPackageData = $pemesanan->package_specific_data ?? [];
$fishingType = $fishingPackageData['jenis_pemancingan'] ?? null;
$fishingDurasi = $fishingPackageData['durasi'] ?? null;
$fishingTambahanJam = (int) ($fishingPackageData['tambahan_jam'] ?? 0);
$fishingJoran = $fishingPackageData['jumlah_joran'] ?? $pemesanan->jumlah_orang;
$fishingRodSize = $fishingPackageData['ukuran_joran'] ?? null;
$fishingRodSizeLabel = match ($fishingRodSize) {
    'standar' => 'Standar',
    'besar' => 'Besar',
    default => null,
};
$fishingUmpan = $fishingPackageData['umpan'] ?? [];
$fishingExtraBait = [];
if (($fishingUmpan['anak_ikan_komet'] ?? 0) > 0) {
    $fishingExtraBait[] = 'Anak Ikan Komet: ' . (int) $fishingUmpan['anak_ikan_komet'] . ' pack';
}
if (($fishingUmpan['umpan_jadi_godongijo'] ?? 0) > 0) {
    $fishingExtraBait[] = 'Umpan Jadi Godongijo: ' . (int) $fishingUmpan['umpan_jadi_godongijo'] . ' pack';
}
@endphp
Jenis Pemancingan : {{ ucfirst(str_replace('_', ' ', $fishingType ?? '-')) }}
@if($fishingDurasi)
Durasi           : {{ $fishingDurasi }} jam
@endif
@if($fishingTambahanJam > 0)
Tambahan Jam     : {{ $fishingTambahanJam }} jam
@endif
Jumlah Joran     : {{ $fishingJoran }} joran
@if((bool) ($fishingPackageData['perlu_sewa_alat'] ?? false) || $fishingType === 'sewa_joran')
Sewa Alat        : {{ $fishingRodSizeLabel ? 'Ya • ' . $fishingRodSizeLabel : 'Ya' }}
@if($fishingRodSizeLabel)
Ukuran Joran     : {{ $fishingRodSizeLabel }}
@endif
@endif
@if(! empty($fishingExtraBait))
Tambahan Umpan   : {{ implode(' • ', $fishingExtraBait) }}
@endif
@else
Jenis Kunjungan   : The Waterfall Resto
@endif
Tanggal Kunjungan : {{ \Carbon\Carbon::parse($pemesanan->tanggal_kunjungan ?? $jadwal->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
Jumlah Orang      : {{ $pemesanan->jumlah_orang }} orang

-------------------------------------------------
TOTAL PEMBAYARAN  : {{ $pemesanan->total_harga !== null ? 'Rp ' . number_format($pemesanan->total_harga, 0, ',', '.') : 'Dihitung saat ditimbang' }}
-------------------------------------------------

=================================================
LANGKAH SELANJUTNYA
=================================================

Silakan lakukan pembayaran untuk mengkonfirmasi booking Anda. Anda akan menerima e-ticket setelah pembayaran berhasil.

Lanjutkan Pembayaran:
{{ route('booking.confirmation', $pemesanan->kode_booking) }}

=================================================
INFORMASI PENTING
=================================================

- Booking ini akan otomatis dibatalkan jika pembayaran tidak dilakukan dalam 24 jam
- Harap datang 15 menit sebelum waktu kunjungan
- Simpan kode booking Anda untuk referensi
- Hubungi kami jika ada pertanyaan: rizkyfahri081@gmail.com

=================================================
KONTAK KAMI
=================================================

The Waterfall Tourism
Alamat  : Jl. Raya Waterfall No. 123, Bandung
Email   : rizkyfahri081@gmail.com
Telepon : (022) 1234-5678

=================================================

Email ini dikirim secara otomatis, mohon tidak membalas email ini.

Terima kasih telah memilih The Waterfall Tourism!
