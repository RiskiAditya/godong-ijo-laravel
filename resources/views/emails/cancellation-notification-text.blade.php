=================================================
PEMBATALAN BOOKING - THE WATERFALL TOURISM
=================================================

Halo {{ $pemesanan->nama_lengkap }},

Kami informasikan bahwa booking Anda dengan kode {{ $pemesanan->kode_booking }} telah dibatalkan.

=================================================
ALASAN PEMBATALAN
=================================================

{{ $reason }}

=================================================
DETAIL BOOKING YANG DIBATALKAN
=================================================

Kode Booking      : {{ $pemesanan->kode_booking }}
Paket Wisata      : {{ $paket->nama_paket }}
Tanggal Kunjungan : {{ \Carbon\Carbon::parse($pemesanan->tanggal_kunjungan ?? $pemesanan->jadwal?->tanggal ?? now())->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
Jumlah Orang      : {{ $pemesanan->jumlah_orang }} orang

@if($refundInfo)
=================================================
INFORMASI PENGEMBALIAN DANA
=================================================

{{ $refundInfo }}

@endif
=================================================
INGIN BOOKING LAGI?
=================================================

Kami siap membantu Anda melakukan booking baru!
Kunjungi: {{ config('app.url') }}

=================================================
BUTUH BANTUAN?
=================================================

Jika Anda memiliki pertanyaan mengenai pembatalan ini atau ingin melakukan booking baru, silakan hubungi kami:

Email   : rizkyfahri081@gmail.com
Telepon : (022) 1234-5678

=================================================
KONTAK KAMI
=================================================

The Waterfall Tourism
Alamat  : Jl. Raya Waterfall No. 123, Bandung
Email   : rizkyfahri081@gmail.com
Telepon : (022) 1234-5678

=================================================

Email ini dikirim secara otomatis, mohon tidak membalas email ini.

Terima kasih,
The Waterfall Tourism
