# Admin Panel - Godong Ijo Booking System

## 📋 Daftar Isi
1. [Fitur Admin Panel](#fitur-admin-panel)
2. [Cara Mengakses](#cara-mengakses)
3. [Struktur Database](#struktur-database)
4. [Panduan Penggunaan](#panduan-penggunaan)
5. [Catatan Keamanan](#catatan-keamanan)

---

## 🎯 Fitur Admin Panel

### 1. Autentikasi Admin
✅ Login khusus admin di route `/admin/login`  
✅ Terpisah dari sistem customer (guest checkout)  
✅ Password di-hash dengan bcrypt  
✅ Semua route `/admin/*` dilindungi middleware  
✅ Redirect otomatis ke login jika belum autentikasi  
✅ Tombol logout tersedia di sidebar  

### 2. Dashboard Utama (`/admin/dashboard`)
✅ Statistik ringkasan:
- Total booking hari ini
- Total booking bulan ini
- Total pendapatan (dari booking status 'paid')
- Jumlah booking pending yang perlu dikonfirmasi

✅ Tabel booking terbaru (5 terbaru)  
✅ Card/summary box yang rapi dengan ikon  

### 3. Manajemen Booking (`/admin/bookings`)
✅ **List semua booking** dalam tabel dengan kolom:
- Kode booking
- Nama pelanggan
- No. WhatsApp
- Tanggal kunjungan
- Jumlah orang
- Paket yang dipilih
- Status (pending/paid/cancelled/expired)
- Tanggal booking dibuat
- Aksi (detail, hapus)

✅ **Filter & Pencarian**:
- Filter berdasarkan status
- Filter berdasarkan rentang tanggal kunjungan
- Pencarian berdasarkan kode booking, nama, atau email

✅ **Detail Booking** (`/admin/bookings/{id}`):
- Informasi lengkap booking
- Informasi pelanggan (nama, email, WhatsApp)
- Link untuk mengirim WhatsApp langsung
- Detail paket wisata
- Informasi pembayaran (jika ada)

✅ **Ubah Status Booking**:
- Form untuk mengubah status (pending → paid/cancelled/expired)
- Notifikasi sukses setelah update
- ⚠️ Placeholder untuk WhatsApp/Email notification (implementasi future)

### 4. Manajemen Paket Wisata (`/admin/paket-wisata`)
✅ **CRUD Lengkap**:
- Create: Tambah paket baru
- Read: List & detail paket
- Update: Edit paket
- Delete: Hapus paket

✅ **Data Paket**:
- Nama paket
- Deskripsi
- Harga
- Foto (upload gambar)
- Kuota maksimal per hari
- Status aktif/nonaktif

✅ **Fitur Upload Gambar**:
- Preview gambar sebelum upload
- Validasi format (JPEG, PNG, JPG, WEBP)
- Maksimal ukuran 2MB
- Otomatis hapus foto lama saat update

✅ **Statistik Paket** (di halaman detail):
- Total booking untuk paket tersebut
- Booking aktif

### 5. Desain UI
✅ Tema konsisten dengan halaman utama (hijau gelap/alam)  
✅ Layout dashboard dengan:
- Sidebar navigasi (Dashboard, Booking, Paket Wisata, Logout)
- Area konten utama
- Top bar dengan nama admin

✅ Responsive untuk tablet/laptop  
✅ Mobile-friendly dengan hamburger menu  
✅ Menggunakan Tailwind CSS  
✅ Ikon dari Font Awesome  
✅ Animasi smooth dengan Alpine.js  

---

## 🔐 Cara Mengakses

### Kredensial Default

Akun admin default sudah dibuat otomatis:

```
URL Login: http://localhost/admin/login
Username: admin
Email: admin@godongijo.com
Password: admin123
```

⚠️ **PENTING**: Ganti password ini setelah login pertama kali!

### Langkah-langkah Login:
1. Buka browser dan akses `http://localhost/admin/login` (sesuaikan dengan domain Anda)
2. Masukkan username: `admin`
3. Masukkan password: `admin123`
4. Klik tombol "Login"
5. Anda akan diarahkan ke Dashboard Admin

---

## 💾 Struktur Database

### Tabel `admins`
```sql
- id (primary key)
- name (varchar 255)
- username (varchar 255, unique)
- email (varchar 255, unique)
- password (varchar 255, hashed)
- remember_token
- created_at
- updated_at
```

### Update Tabel `paket_wisata`
Ditambahkan kolom:
```sql
- foto (varchar 255, nullable)
```

### Tabel yang Sudah Ada (Digunakan):
- `users` - Customer (untuk booking dengan akun)
- `pemesanan` - Data booking
- `jadwal` - Jadwal paket wisata per tanggal
- `paket_wisata` - Master paket wisata
- `pembayaran` - Data pembayaran (Midtrans)

---

## 📖 Panduan Penggunaan

### A. Mengelola Paket Wisata

#### Menambah Paket Baru:
1. Masuk ke menu **Paket Wisata** di sidebar
2. Klik tombol **"Tambah Paket Wisata"**
3. Isi form:
   - Nama Paket (wajib)
   - Deskripsi (wajib)
   - Harga dalam Rupiah (wajib)
   - Kuota per hari (wajib)
   - Upload foto (opsional)
   - Centang "Paket Aktif" jika ingin langsung ditampilkan
4. Klik **"Simpan Paket"**

#### Mengedit Paket:
1. Di list paket, klik ikon **edit (pensil)** pada paket yang ingin diedit
2. Ubah data yang diperlukan
3. Untuk mengganti foto, upload foto baru (foto lama akan otomatis terhapus)
4. Klik **"Update Paket"**

#### Menghapus Paket:
1. Di list paket, klik ikon **hapus (tong sampah)**
2. Konfirmasi penghapusan
3. Paket dan foto-nya akan dihapus permanen

⚠️ **Peringatan**: Menghapus paket akan mempengaruhi data booking yang terkait!

### B. Mengelola Booking

#### Melihat List Booking:
1. Masuk ke menu **Booking** di sidebar
2. Gunakan filter untuk menyaring data:
   - **Cari**: Ketik kode booking, nama, atau email
   - **Status**: Pilih status tertentu
   - **Dari Tanggal** dan **Sampai Tanggal**: Filter berdasarkan tanggal kunjungan
3. Klik **"Filter"** untuk menerapkan filter
4. Klik **"Reset"** untuk menghapus semua filter

#### Melihat Detail Booking:
1. Di list booking, klik ikon **mata** pada booking yang ingin dilihat
2. Anda akan melihat:
   - Informasi booking lengkap
   - Data pelanggan
   - Detail paket dan harga
   - Status pembayaran (jika ada)
   - Tombol untuk kirim WhatsApp langsung

#### Mengubah Status Booking:
1. Buka detail booking
2. Di panel kanan, ubah dropdown **"Status Booking"**
3. Pilih status baru:
   - **Pending**: Booking masih menunggu pembayaran
   - **Lunas**: Pembayaran sudah dikonfirmasi/diterima
   - **Dibatalkan**: Booking dibatalkan
   - **Kadaluarsa**: Booking sudah lewat waktu
4. Klik **"Simpan Status"**

📌 **Use Case**: 
- Jika pelanggan transfer manual (bukan via Midtrans), admin bisa mengubah status dari "Pending" ke "Lunas" setelah verifikasi pembayaran
- Jika pelanggan membatalkan, admin bisa ubah ke "Dibatalkan"

#### Menghubungi Pelanggan:
1. Buka detail booking
2. Di bagian "Informasi Pelanggan", klik link **"Kirim Pesan"** di bawah nomor WhatsApp
3. Akan membuka WhatsApp Web/App langsung ke nomor pelanggan

#### Menghapus Booking:
1. Buka detail booking atau di list booking
2. Klik tombol/ikon **"Hapus Booking"**
3. Konfirmasi penghapusan
4. Data booking akan dihapus permanen

### C. Memantau Statistik Dashboard

Dashboard menampilkan ringkasan real-time:

- **Booking Hari Ini**: Jumlah booking yang dibuat hari ini
- **Booking Bulan Ini**: Total booking bulan berjalan
- **Total Pendapatan**: Sum dari total_harga booking dengan status 'paid'
- **Booking Pending**: Jumlah booking yang masih pending (perlu follow-up)

### D. Logout

1. Klik tombol **"Logout"** di sidebar (paling bawah)
2. Atau klik menu di top bar (jika ada)
3. Anda akan diarahkan kembali ke halaman login

---

## 🔒 Catatan Keamanan

### ✅ Yang Sudah Diimplementasikan:
1. **Password Hashing**: Semua password admin di-hash dengan bcrypt
2. **Guard Terpisah**: Admin menggunakan guard 'admin', terpisah dari customer
3. **Middleware Protection**: Semua route `/admin/*` dilindungi middleware `admin`
4. **Session Management**: Session admin terpisah dari user biasa
5. **CSRF Protection**: Semua form dilindungi CSRF token Laravel
6. **Auto Redirect**: User yang belum login otomatis diarahkan ke halaman login

### 🔐 Best Practices yang Harus Dilakukan:

#### 1. Ganti Password Default
```php
// Setelah login pertama kali, jalankan di tinker:
php artisan tinker
$admin = App\Models\Admin::where('username', 'admin')->first();
$admin->password = bcrypt('password_baru_yang_kuat');
$admin->save();
```

#### 2. Jangan Commit Kredensial ke Git
File `.env` sudah ada di `.gitignore`, pastikan tetap seperti itu.

#### 3. Gunakan HTTPS di Production
Jangan gunakan HTTP untuk halaman admin di production!

#### 4. Tambah Admin Baru (Opsional)
```php
// Via tinker:
php artisan tinker
App\Models\Admin::create([
    'name' => 'Nama Admin Baru',
    'username' => 'username_baru',
    'email' => 'email@example.com',
    'password' => bcrypt('password_kuat')
]);
```

#### 5. Batasi Akses IP (Opsional)
Untuk keamanan ekstra, Anda bisa restrict akses `/admin` hanya dari IP tertentu di `.htaccess` atau server config.

---

## 🛠️ Troubleshooting

### Problem: "404 Not Found" saat akses `/admin/login`
**Solusi**:
```bash
php artisan route:clear
php artisan cache:clear
php artisan config:clear
```

### Problem: "Middleware admin not found"
**Solusi**: Pastikan middleware sudah terdaftar di `app/Http/Kernel.php`:
```php
'admin' => \App\Http\Middleware\AdminMiddleware::class,
```

### Problem: Login selalu redirect ke login lagi
**Solusi**: 
1. Cek session config di `config/session.php`
2. Pastikan `SESSION_DRIVER` di `.env` adalah `file` atau `database`
3. Clear session: `php artisan session:table` (jika pakai database)

### Problem: Foto tidak muncul setelah upload
**Solusi**:
1. Pastikan folder `public/images/paket` ada dan writable
2. Jalankan: `php artisan storage:link` (jika belum)
3. Cek permissions folder: `chmod -R 775 public/images/paket`

### Problem: Error "Class Admin not found"
**Solusi**:
```bash
composer dump-autoload
```

---

## 📁 Struktur File yang Dibuat

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Admin/
│   │       ├── AuthController.php
│   │       ├── DashboardController.php
│   │       ├── BookingController.php
│   │       └── PaketWisataController.php
│   └── Middleware/
│       └── AdminMiddleware.php
├── Models/
│   ├── Admin.php
│   ├── PaketWisata.php
│   ├── Jadwal.php
│   └── Pemesanan.php
config/
└── auth.php (updated - added admin guard)
database/
├── migrations/
│   ├── xxxx_create_admins_table.php
│   └── xxxx_add_foto_to_paket_wisata_table.php
└── seeders/
    └── AdminSeeder.php
resources/
└── views/
    └── admin/
        ├── layouts/
        │   ├── app.blade.php
        │   └── dashboard.blade.php
        ├── auth/
        │   └── login.blade.php
        ├── dashboard.blade.php
        ├── bookings/
        │   ├── index.blade.php
        │   └── show.blade.php
        └── paket-wisata/
            ├── index.blade.php
            ├── create.blade.php
            ├── edit.blade.php
            └── show.blade.php
routes/
└── web.php (updated - added admin routes)
public/
└── images/
    └── paket/ (folder for package photos)
```

---

## 🚀 Fitur yang Bisa Ditambahkan Nanti

### 1. Notifikasi WhatsApp/Email
Saat ini ada placeholder untuk kirim notifikasi ke pelanggan saat status berubah. Bisa diimplementasi dengan:
- WhatsApp API (Twilio, Fonnte, Wablas, dll)
- Email (Laravel Mail dengan SMTP)

### 2. Laporan & Analitik
- Grafik pendapatan per bulan
- Paket wisata terpopuler
- Export data ke Excel/PDF
- Laporan keuangan

### 3. Manajemen Jadwal
- CRUD untuk jadwal per tanggal
- Set harga khusus untuk tanggal tertentu (weekend, holiday)
- Manage kuota per tanggal

### 4. Multi-Admin dengan Role
- Role-based access control (Super Admin, Staff)
- Permissions granular

### 5. Customer Management
- List semua customer
- Riwayat booking per customer
- Customer loyalty program

---

## 📞 Support

Jika ada pertanyaan atau issue, silakan dokumentasikan:
- URL yang diakses
- Screenshot error (jika ada)
- Laravel log (`storage/logs/laravel.log`)

---

**Dibuat pada**: 25 Juli 2026  
**Laravel Version**: 10.x  
**PHP Version**: 8.1+  
**Database**: MySQL

---

## ✅ Checklist Setup

- [x] Migration database executed
- [x] Admin seeder executed
- [x] Middleware registered
- [x] Routes configured
- [x] Views created
- [x] Controllers implemented
- [x] Models created
- [x] Auth guard configured
- [x] Upload folder created
- [ ] Ganti password default admin (TODO)
- [ ] Test semua fitur di browser (TODO)

**Status**: ✅ Ready to Use!

Silakan akses `/admin/login` dan mulai mengelola website booking wisata Anda! 🎉
