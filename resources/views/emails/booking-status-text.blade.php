STATUS BOOKING DIPERBARUI

Halo {{ $pemesanan->nama_lengkap }},

Status booking Anda telah diperbarui oleh tim Godong Ijo.

Kode booking       : {{ $pemesanan->kode_booking }}
Status sebelumnya  : {{ ucfirst($oldStatus) }}
Status sekarang    : {{ ucfirst($pemesanan->status) }}

Jika membutuhkan bantuan, hubungi WhatsApp {{ config('app.whatsapp.display') }}.
