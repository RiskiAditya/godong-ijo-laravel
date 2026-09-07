PENGINGAT KUNJUNGAN GODONG IJO

Halo {{ $pemesanan->nama_lengkap }},

Kunjungan Anda ke Godong Ijo dijadwalkan besok.

Kode booking : {{ $pemesanan->kode_booking }}
Paket       : {{ $paket->nama_paket ?? 'Godong Ijo' }}
Tanggal     : {{ $visitDate }}
Jumlah      : {{ $pemesanan->jumlah_orang ?? 0 }} orang

Simpan kode booking ini dan tunjukkan saat tiba di lokasi.
Alamat: Jalan Cinangka Raya Km 10 No. 60, Serua, Bojongsari, Depok.
