# 🎣 Testing Form Pemancingan - Panduan Lengkap

## ✅ Yang Sudah Dilakukan

### 1. **File yang Dibuat/Dimodifikasi:**

#### Komponen Baru:
- ✅ `resources/views/components/fishing-booking-modal.blade.php` - Modal form pemancingan
- ✅ `app/Http/Requests/FishingBookingRequest.php` - Validasi server-side
- ✅ `app/Http/Controllers/BookingController.php` - Method `storeFishingBooking()` dan `calculateFishingPrice()`
- ✅ `routes/web.php` - Route `POST /api/booking/fishing`
- ✅ `resources/views/test/fishing-booking-test.blade.php` - Testing page standalone

#### File yang Dimodifikasi:
- ✅ `resources/views/landing/index.blade.php` - Integrasi fishing modal
  - Deteksi "Monster Fish" di destination cards
  - Call `openFishingModal()` untuk Monster Fish
  - Call `openBookingModal()` untuk paket lain (tetap pakai form generic)

### 2. **Logika Pemisahan:**

```
IF destination.name contains "Monster Fish" OR "Fishing"
    → Button "Pesan" opens Fishing Modal (detailed form)
ELSE
    → Button "Pesan" opens Generic Modal (existing system)
```

## 🧪 Cara Testing

### **Opsi 1: Testing di Landing Page (Integrasi Penuh)**

1. **Buka browser dan akses:**
   ```
   http://localhost/
   ```

2. **Scroll ke section "Featured Destinations"**

3. **Cari card "Monster Fish Fishing Lake"**

4. **Klik tombol hijau "Pesan"**
   - ✅ Harus membuka modal fishing (dengan field jenis pemancingan, durasi, dll)
   - ❌ BUKAN modal generic (yang hanya punya Jumlah Orang)

5. **Test Form:**
   - Pilih jenis: Tarikan
   - Pilih durasi: 4 jam
   - Tambahkan jam tambahan: 1
   - Jumlah joran: 2
   - Tambah umpan: 3 Anak Ikan Komet, 2 Umpan Jadi
   - Centang checkbox persetujuan aturan
   
6. **Perhatikan estimasi harga:**
   - Harus update otomatis (real-time)
   - Format: Rp 322.000 (untuk contoh di atas)

7. **Test paket lain (The Waterfall Resto):**
   - Klik "Pesan" di card The Waterfall
   - ✅ Harus membuka modal GENERIC (existing system)
   - Form hanya punya: Nama, Email, WhatsApp, Tanggal, Jumlah Orang

### **Opsi 2: Testing Page Standalone**

1. **Buka:**
   ```
   http://localhost/test/fishing-booking
   ```

2. **Klik "🎣 Buka Form Pemancingan"**

3. **Test semua jenis pemancingan:**

#### A. **Sewa Joran**
- Pilih: Sewa Joran
- Ukuran: Standar (Rp 20.000)
- Jumlah: 2 joran
- **Expected:** Rp 40.000

#### B. **Tarikan**
- Pilih: Tarikan
- Durasi: 4 jam (Rp 110.000)
- Tambahan: 1 jam (Rp 40.000)
- Jumlah: 2 joran
- **Expected:** Rp 300.000

#### C. **Jackpot**
- Pilih: Jackpot
- Jumlah: 2 joran
- **Expected:** Rp 420.000

#### D. **Kiloan**
- Pilih: Kiloan
- **Expected:** "Dihitung saat ditimbang"

#### E. **Dengan Umpan**
- Tambah: 3 Anak Ikan Komet (Rp 33.000)
- Tambah: 2 Umpan Jadi (Rp 22.000)
- **Expected:** Harga sebelumnya + Rp 55.000

### **Test Validasi:**

1. **Submit form kosong:**
   - ❌ Harus muncul error "Nama wajib diisi", etc

2. **No. HP invalid:**
   - Input: "12345"
   - ❌ Error: "Format nomor WhatsApp tidak valid"

3. **Jam kunjungan di luar range:**
   - Input: "22:00"
   - ❌ Error: "Jam kunjungan harus antara 09:00 - 21:00"

4. **Checkbox aturan tidak dicentang:**
   - ❌ Error: "Anda harus menyetujui aturan pemancingan"

### **Test Submit (End-to-End):**

1. **Isi form dengan data valid:**
   ```
   Nama: John Doe
   No. HP: 08123456789
   Tanggal: [pilih besok]
   Jam: 10:00
   Jenis: Tarikan
   Durasi: 4 jam
   Jumlah Joran: 2
   [Centang checkbox persetujuan]
   ```

2. **Klik "Konfirmasi Pesan"**

3. **Expected Response:**
   ```json
   {
       "success": true,
       "message": "Booking berhasil dibuat",
       "kode_booking": "GOD-20260801-0001",
       "estimasi_total": 220000,
       "jenis_pemancingan": "tarikan"
   }
   ```

4. **Modal harus close dan redirect ke confirmation page**

## 🔍 Debug Checklist

### **Jika modal tidak muncul:**
1. Buka Browser Console (F12)
2. Check error JavaScript
3. Pastikan Alpine.js loaded: `window.Alpine` harus ada
4. Test manual: Ketik di console:
   ```javascript
   window.openFishingModal()
   ```

### **Jika harga tidak update:**
1. Buka Console
2. Check error di `calculatePrice()` method
3. Test manual:
   ```javascript
   Alpine.$data(document.querySelector('[x-data="fishingBookingModal()"]')).calculatePrice()
   ```

### **Jika submit gagal:**
1. Check Network tab (F12 → Network)
2. Lihat POST request ke `/api/booking/fishing`
3. Check response:
   - 422: Validation error (normal, fix form input)
   - 500: Server error (check Laravel log)
4. Laravel log location: `storage/logs/laravel.log`

### **Jika CSRF error:**
1. Check meta tag di `<head>`:
   ```html
   <meta name="csrf-token" content="...">
   ```
2. Clear browser cache
3. Refresh page

## 📊 Test Scenarios Table

| Jenis | Durasi | Joran | Tambahan | Umpan Komet | Umpan Jadi | Expected Total |
|-------|--------|-------|----------|-------------|------------|----------------|
| Sewa Joran (standar) | - | 1 | - | 0 | 0 | Rp 20.000 |
| Sewa Joran (besar) | - | 2 | - | 0 | 0 | Rp 100.000 |
| Tarikan | 2 jam | 1 | 0 | 0 | 0 | Rp 80.000 |
| Tarikan | 4 jam | 1 | 0 | 0 | 0 | Rp 110.000 |
| Tarikan | 4 jam | 1 | 1 jam | 0 | 0 | Rp 150.000 |
| Tarikan | 4 jam | 2 | 1 jam | 0 | 0 | Rp 300.000 |
| Jackpot | - | 1 | - | 0 | 0 | Rp 210.000 |
| Jackpot | - | 2 | - | 0 | 0 | Rp 420.000 |
| Kiloan | - | 1 | - | 0 | 0 | "Dihitung saat ditimbang" |
| Tarikan | 4 jam | 2 | 0 | 3 | 2 | Rp 275.000 |

## ✅ Expected Behavior Summary

### **Di Landing Page:**
- ✅ Monster Fish → Fishing Modal (detailed)
- ✅ The Waterfall → Generic Modal (simple)
- ✅ Private Room → Generic Modal (simple)
- ✅ Mini Zoo → Generic Modal (simple)

### **Modal Fishing Features:**
- ✅ Segmented button untuk 4 jenis pemancingan
- ✅ Dynamic fields (tampil/hilang sesuai jenis)
- ✅ Real-time price calculation
- ✅ Quantity stepper untuk umpan
- ✅ Info box untuk Kiloan
- ✅ Jam kunjungan validation (09:00-21:00)
- ✅ Loading state saat submit
- ✅ Error handling & display

### **Database:**
- ✅ Booking disimpan ke table `pemesanan`
- ✅ Kode booking: GOD-YYYYMMDD-XXXX
- ✅ `package_specific_data` berisi detail fishing
- ✅ `total_harga`: estimasi (null jika kiloan)

## 🚀 Next Steps Setelah Testing

1. **Jika testing OK:**
   - ✅ Form siap production
   - Setup Midtrans (jika belum)
   - Customize styling jika perlu

2. **Jika ada bug:**
   - Check console untuk error
   - Check Laravel log
   - Review file `FISHING_BOOKING_FORM.md` untuk troubleshooting

3. **Integration ke halaman lain:**
   - Copy pattern dari landing page
   - Include modal: `@include('components.fishing-booking-modal')`
   - Call: `openFishingModal()` via button

## 📞 Support

**Files to check:**
- Main Modal: `resources/views/components/fishing-booking-modal.blade.php`
- Controller: `app/Http/Controllers/BookingController.php` (method `storeFishingBooking`)
- Validation: `app/Http/Requests/FishingBookingRequest.php`
- Route: `routes/web.php` (search "fishing")

**Logs:**
- Laravel: `storage/logs/laravel.log`
- Browser Console: F12 → Console tab
- Network: F12 → Network tab

---

## 🎉 Ready to Test!

**Start here:**
1. ✅ `http://localhost/` → Scroll to Monster Fish → Click "Pesan"
2. ✅ `http://localhost/test/fishing-booking` → Click "Buka Form"

Good luck! 🎣
