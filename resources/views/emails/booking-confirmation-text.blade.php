=================================================
KONFIRMASI BOOKING - THE WATERFALL TOURISM
=================================================

Halo {{ $pemesanan->nama_lengkap }},

Terima kasih atas booking Anda! Booking dengan kode {{ $pemesanan->kode_booking }} telah berhasil dibuat untuk paket {{ $paket->nama_paket }}.

=================================================
DETAIL PEMESANAN
=================================================

Kode Booking      : {{ $pemesanan->kode_booking }}
Paket Wisata      : {{ $paket->nama_paket }}
Tanggal Kunjungan : {{ \Carbon\Carbon::parse($pemesanan->tanggal_kunjungan ?? $jadwal->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
Jumlah Orang      : {{ $pemesanan->jumlah_orang }} orang

-------------------------------------------------
TOTAL PEMBAYARAN  : Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}
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
