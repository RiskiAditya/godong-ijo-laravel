# 🚀 Quick Start - Admin Panel Godong Ijo

## Login Admin

**URL**: `http://localhost/admin/login` (atau sesuai domain Anda)

```
Username: admin
Password: admin123
```

⚠️ **Ganti password setelah login pertama!**

---

## Menu Utama

### 🏠 Dashboard (`/admin/dashboard`)
- Statistik booking hari ini & bulan ini
- Total pendapatan
- Booking pending
- List booking terbaru

### 📋 Booking (`/admin/bookings`)
- **Filter**: Status, tanggal, pencarian
- **Detail**: Klik ikon mata 👁️
- **Ubah Status**: Pending → Paid/Cancelled
- **Hapus**: Klik ikon sampah 🗑️

### 📦 Paket Wisata (`/admin/paket-wisata`)
- **Tambah**: Klik "Tambah Paket Wisata"
- **Edit**: Klik ikon pensil ✏️
- **Detail**: Klik ikon mata 👁️
- **Hapus**: Klik ikon sampah 🗑️
- **Upload Foto**: Max 2MB, format JPG/PNG/WEBP

---

## Fitur Penting

### ✅ Konfirmasi Pembayaran Manual
1. Buka detail booking
2. Ubah status dari "Pending" ke "Lunas"
3. Klik "Simpan Status"

### 📞 Hubungi Pelanggan
1. Buka detail booking
2. Klik "Kirim Pesan" di bawah nomor WhatsApp
3. Otomatis buka WhatsApp Web

### 📸 Upload Foto Paket
1. Saat tambah/edit paket
2. Pilih file foto
3. Preview otomatis muncul
4. Simpan

---

## Troubleshooting Cepat

**Problem**: 404 Not Found
```bash
php artisan route:clear
php artisan cache:clear
```

**Problem**: Foto tidak muncul
```bash
# Pastikan folder writable
chmod -R 775 public/images/paket
```

**Problem**: Login loop
```bash
# Clear session & cache
php artisan cache:clear
php artisan session:flush
```

---

## Ganti Password Admin

Via Tinker:
```bash
php artisan tinker
$admin = App\Models\Admin::where('username', 'admin')->first();
$admin->password = bcrypt('password_baru_anda');
$admin->save();
exit
```

---

## Struktur Route Admin

```
/admin/login              → Halaman login
/admin/dashboard          → Dashboard utama
/admin/bookings           → List booking
/admin/bookings/{id}      → Detail booking
/admin/paket-wisata       → List paket
/admin/paket-wisata/create → Tambah paket
/admin/paket-wisata/{id}/edit → Edit paket
/admin/logout             → Logout (POST)
```

---

## Status Booking

| Status | Arti | Kapan Digunakan |
|--------|------|-----------------|
| **Pending** | Menunggu pembayaran | Default saat booking dibuat |
| **Paid** | Sudah dibayar/lunas | Setelah payment gateway konfirmasi ATAU admin verifikasi transfer manual |
| **Cancelled** | Dibatalkan | Customer membatalkan atau admin cancel |
| **Expired** | Kadaluarsa | Booking sudah lewat waktu |

---

## Tips Penggunaan

💡 **Filter Booking Pending**  
Di halaman booking, filter status = "Pending" untuk lihat booking yang perlu dikonfirmasi

💡 **Nonaktifkan Paket Sementara**  
Edit paket → uncheck "Paket Aktif" → paket tidak muncul di website customer

💡 **Statistik Dashboard**  
Dashboard update real-time setiap refresh

💡 **Responsive Design**  
Admin panel bisa diakses dari tablet/laptop. Mobile ada hamburger menu.

---

## Tech Stack

- **Framework**: Laravel 10.x
- **Auth**: Laravel Guards (guard 'admin')
- **Frontend**: Tailwind CSS + Alpine.js
- **Icons**: Font Awesome 6
- **Database**: MySQL
- **PHP**: 8.1+

---

## File Penting

```
app/Http/Controllers/Admin/      → Admin controllers
app/Models/Admin.php              → Admin model
app/Http/Middleware/AdminMiddleware.php → Protection
resources/views/admin/            → Admin views
config/auth.php                   → Guard config
routes/web.php                    → Admin routes
```

---

**Dokumentasi Lengkap**: Lihat `ADMIN_PANEL_SETUP.md`

**Selamat mengelola website booking! 🎉**
