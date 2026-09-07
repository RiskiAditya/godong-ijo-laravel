# Implementation Plan: Sistem Pemesanan Wanatirta

## Overview

Membangun sistem pemesanan paket wisata berbasis web "Wanatirta" dengan Laravel + MySQL. Sistem mencakup: pemilihan paket wisata, pembayaran online via payment gateway (Midtrans), generate e-tiket otomatis, dan validasi tiket manual oleh admin.

## Tasks

- [x] 1. Setup Database Schema & Models: Buat migration dan Eloquent models untuk semua tabel (users dengan no_hp, paket_wisata, jadwal, pemesanan, pembayaran, e_tiket). Migration harus berisi foreign keys dan index pada booking_code, gateway_order_id, kode_tiket. Model Eloquent memiliki relasi hasMany/belongsTo yang benar. php artisan migrate berjalan tanpa error.

- [ ] 2. Implement Landing Page: Buat landing page Wanatirta dengan route GET / -> LandingController@index. File tirta-landing.html dipindahkan ke resources/views/landing/index.blade.php. CSS dipindahkan ke public/css/landing.css, JavaScript ke public/js/landing.js. 3 blob diganti dengan foto: hero-hutan.jpg, hero-danau.jpg, hero-mancing.jpg. Design tokens (--forest, --mint, --gold, dll) dipertahankan. Responsive di mobile (~375px) dan desktop. Font Fraunces dan Sora digunakan.

- [ ] 3. Build Booking Flow Controllers: Buat controller untuk alur pemesanan (pilih paket -> pilih jadwal -> checkout). Booking menghasilkan kode_booking unik (bukan auto increment). Status awal pemesanan adalah 'pending'. Total harga dihitung berdasarkan jumlah_orang × harga paket. Kuota jadwal berkurang saat pemesanan dibuat.

- [ ] 4. Implement Payment Gateway Integration: Buat PaymentController dengan method createTransaction() untuk integrasi Midtrans. Generate gateway_order_id unik. Redirect/kirim request ke payment gateway. Simpan record pembayaran dengan status 'pending'.

- [ ] 5. Build Payment Webhook Handler: Buat webhook handler POST /webhook/payment dengan security dan idempotency. Verifikasi signature payment gateway (tolak jika invalid, log percobaan). Update status pembayaran dalam DB::transaction dengan row locking. Idempotent: webhook dobel dengan gateway_order_id sama tidak diproses dua kali. Setelah status 'success', otomatis generate e-tiket dengan kode unik. Route dikecualikan dari CSRF middleware tapi tetap verified via signature.

- [ ] 6. Build E-Ticket Generation System: Implement auto-generate e-tiket setelah pembayaran sukses. Generate kode_tiket 10 karakter acak (huruf+angka) pakai Str::random. Cek collision untuk memastikan kode unik. Status awal: belum_checkin. Timestamp issued_at diset saat pembuatan.

- [ ] 7. Build Admin Ticket Validation Interface: Buat AdminTicketController dengan form validasi tiket manual. Halaman form dengan input text dan tombol Cari. Method validasiTiket() menampilkan status: tidak ditemukan, sudah terpakai, kadaluarsa, atau valid. Method konfirmasiCheckin() update status_checkin dan checkin_at dengan pengecekan ulang. Middleware auth + role:admin + throttle:10,1. Tidak bisa double-confirm.

- [ ] 8. Testing & Verification: Jalankan testing untuk memastikan semua acceptance criteria terpenuhi. Migration jalan tanpa error. Landing page tampil dengan 3 foto asli. Alur pemesanan menghasilkan kode_booking unik. Webhook menolak signature invalid. Webhook idempotent (tidak buat e-tiket dobel). E-tiket terbuat otomatis setelah pembayaran sukses. Form validasi admin menampilkan semua status dengan benar. Route admin tidak bisa diakses tanpa login. Tampilan responsive di mobile dan desktop. Tidak ada error di console browser atau log Laravel.

## Notes

- Payment gateway menggunakan Midtrans (struktur dapat disesuaikan untuk gateway lain)
- Admin validasi tiket TIDAK menggunakan scan QR/kamera, hanya input manual
- Semua kode harus menggunakan Bahasa Indonesia untuk copy/text
- Design tokens warna WAJIB dipertahankan persis seperti spesifikasi

## Task Dependency Graph

```json
{
  "waves": [
    {
      "wave": 1,
      "tasks": [1]
    },
    {
      "wave": 2,
      "tasks": [2, 3]
    },
    {
      "wave": 3,
      "tasks": [4]
    },
    {
      "wave": 4,
      "tasks": [5]
    },
    {
      "wave": 5,
      "tasks": [6]
    },
    {
      "wave": 6,
      "tasks": [7]
    },
    {
      "wave": 7,
      "tasks": [8]
    }
  ]
}
```