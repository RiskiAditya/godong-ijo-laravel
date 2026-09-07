=================================================
PEMBAYARAN BERHASIL - E-TICKET
=================================================

Halo {{ $pemesanan->nama_lengkap }},

Pembayaran Anda untuk booking {{ $pemesanan->kode_booking }} telah berhasil dikonfirmasi. Terima kasih atas pembayaran Anda!

=================================================
DETAIL PEMESANAN
=================================================

Kode Booking      : {{ $pemesanan->kode_booking }}
Paket Wisata      : {{ $paket->nama_paket }}
Tanggal Kunjungan : {{ \Carbon\Carbon::parse($pemesanan->tanggal_kunjungan ?? $pemesanan->jadwal?->tanggal ?? now())->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
Jumlah Orang      : {{ $pemesanan->jumlah_orang }} orang
Metode Pembayaran : {{ strtoupper($pembayaran->payment_type ?? 'N/A') }}

-------------------------------------------------
TOTAL PEMBAYARAN  : Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}
-------------------------------------------------

=================================================
LIHAT DETAIL BOOKING
=================================================

Anda dapat melihat detail lengkap booking Anda dengan mengakses link berikut:
{{ route('booking.confirmation', $pemesanan->kode_booking) }}

=================================================
INFORMASI PENTING
=================================================

- Harap datang 15 menit sebelum waktu kunjungan
- Simpan email ini sebagai bukti pembayaran
- Tunjukkan kode booking Anda saat kedatangan di lokasi
- Hubungi kami jika ada pertanyaan: info@thewaterfall.com

=================================================
KONTAK KAMI
=================================================

The Waterfall Tourism
Alamat  : Jl. Raya Waterfall No. 123, Bandung
Email   : info@thewaterfall.com
Telepon : (022) 1234-5678

=================================================

Email ini dikirim secara otomatis, mohon tidak membalas email ini.

Terima kasih telah memilih The Waterfall Tourism!
