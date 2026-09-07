# Email Commands Guide

Panduan lengkap untuk mengirim email notifikasi ke booking yang sudah ada di database.

---

## 📧 Command yang Tersedia

### 1. **Kirim Email ke Booking Spesifik**
Mengirim email ke satu booking berdasarkan kode booking.

```bash
php artisan email:send-booking {kode_booking} {type} [--reason=]
```

### 2. **Kirim Email Secara Bulk**
Mengirim email ke banyak booking sekaligus dengan filter.

```bash
php artisan email:bulk {type} [--status=] [--limit=] [--dry-run]
```

### 3. **Kirim Test Email**
Mengirim test email dengan data mock (untuk testing).

```bash
php artisan email:test {type} [--email=]
```

---

## 🎯 1. Email ke Booking Spesifik

### **Syntax:**
```bash
php artisan email:send-booking {kode_booking} {type} [--reason=]
```

### **Parameters:**
- `kode_booking` : Kode booking (contoh: BK20260125001)
- `type` : Jenis email
  - `booking` - Email konfirmasi booking
  - `payment` - Email konfirmasi payment
  - `cancellation` - Email notifikasi pembatalan
- `--reason` : Alasan pembatalan (wajib untuk type cancellation)

### **Contoh Penggunaan:**

#### **Kirim Email Konfirmasi Booking:**
```bash
php artisan email:send-booking BK20260125001 booking
```

#### **Kirim Email Konfirmasi Payment:**
```bash
php artisan email:send-booking BK20260125001 payment
```

#### **Kirim Email Pembatalan:**
```bash
php artisan email:send-booking BK20260125001 cancellation --reason="Pembatalan oleh admin"
```

### **Output:**
```
Searching for booking: BK20260125001...

📋 Booking Details:
  Code        : BK20260125001
  Customer    : John Doe
  Email       : john@example.com
  Package     : Edukasi Air Terjun
  Status      : pending
  Total       : Rp 750,000

📧 Send booking email to john@example.com? (yes/no) [yes]:
> yes

Sending booking email...

✓ booking email sent successfully!
  To: john@example.com
  Booking: BK20260125001

📬 Check the inbox (and spam folder if needed)
```

---

## 📨 2. Bulk Email

### **Syntax:**
```bash
php artisan email:bulk {type} [--status=] [--limit=] [--dry-run]
```

### **Parameters:**
- `type` : Jenis email (`booking` atau `payment`)
- `--status` : Filter berdasarkan status booking (opsional)
  - `pending` - Booking pending
  - `paid` - Booking terbayar
  - `cancelled` - Booking dibatalkan
- `--limit` : Maksimal jumlah email yang dikirim (default: 10)
- `--dry-run` : Preview tanpa mengirim email (testing mode)

### **Contoh Penggunaan:**

#### **Kirim Email ke 10 Booking Pending:**
```bash
php artisan email:bulk booking --status=pending --limit=10
```

#### **Kirim Email Payment ke 20 Booking Paid:**
```bash
php artisan email:bulk payment --status=paid --limit=20
```

#### **Preview Bulk Email (Dry Run):**
```bash
php artisan email:bulk booking --status=pending --limit=50 --dry-run
```

### **Output:**
```
Searching for bookings...

📋 Found 10 booking(s):

+------------------+--------------+----------------------+---------+-------------+
| Booking Code     | Customer     | Email                | Status  | Amount      |
+------------------+--------------+----------------------+---------+-------------+
| BK20260125001    | John Doe     | john@example.com     | pending | Rp 750,000  |
| BK20260125002    | Jane Smith   | jane@example.com     | pending | Rp 500,000  |
| ...              | ...          | ...                  | ...     | ...         |
+------------------+--------------+----------------------+---------+-------------+

📊 Rate Limit Status:
  Used: 5/100 emails today
  Remaining: 95 emails

📧 Send booking emails to 10 recipient(s)? (yes/no) [yes]:
> yes

Sending emails...
[▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

✓ Bulk email sending completed!

  Success  : 10 email(s) sent
  Failed   : 0 email(s) failed
  Skipped  : 0 email(s) skipped

📊 Final Rate Limit: 15/100 used
```

---

## 🧪 3. Test Email

### **Syntax:**
```bash
php artisan email:test {type} [--email=]
```

### **Parameters:**
- `type` : Jenis email (`booking`, `payment`, atau `cancellation`)
- `--email` : Email tujuan (opsional, default dari config)

### **Contoh Penggunaan:**

#### **Test Booking Email:**
```bash
php artisan email:test booking --email=your.email@gmail.com
```

#### **Test Payment Email:**
```bash
php artisan email:test payment --email=your.email@gmail.com
```

#### **Test Cancellation Email:**
```bash
php artisan email:test cancellation --email=your.email@gmail.com
```

---

## ⚙️ Use Cases

### **Scenario 1: Kirim Ulang Email yang Gagal**
Jika email booking gagal terkirim saat customer booking:

```bash
# Cek kode booking di database
# Kirim ulang email konfirmasi booking
php artisan email:send-booking BK20260125001 booking
```

### **Scenario 2: Kirim Email ke Booking Lama**
Untuk booking lama yang belum pernah dapat email (sebelum sistem email dibuat):

```bash
# Kirim email konfirmasi ke semua booking pending (max 50)
php artisan email:bulk booking --status=pending --limit=50
```

### **Scenario 3: Notifikasi Pembatalan Manual**
Admin membatalkan booking secara manual:

```bash
php artisan email:send-booking BK20260125001 cancellation --reason="Dibatalkan karena cuaca buruk"
```

### **Scenario 4: Test Email Sebelum Go Live**
Sebelum launching, test dulu email notification:

```bash
# Test semua jenis email
php artisan email:test booking --email=admin@company.com
php artisan email:test payment --email=admin@company.com
php artisan email:test cancellation --email=admin@company.com
```

### **Scenario 5: Preview Bulk Email**
Cek dulu berapa email yang akan dikirim tanpa benar-benar mengirim:

```bash
# Preview dengan dry-run
php artisan email:bulk payment --status=paid --limit=100 --dry-run
```

---

## 🚨 Error Handling

### **Error: Booking Not Found**
```
✗ Booking not found: BK20260125999
  Please check the booking code and try again.
```
**Solution:** Pastikan kode booking benar dan ada di database.

### **Error: No Email Address**
```
✗ No email address found for this booking.
  This booking doesn't have an email address (might be a walk-in or fishing booking).
```
**Solution:** Booking ini tidak punya email (biasanya fishing booking atau walk-in). Skip saja.

### **Error: Rate Limit Exceeded**
```
✗ Rate Limit Exceeded
  Daily email limit reached. Try again tomorrow or upgrade to Google Workspace.
```
**Solution:** 
- Tunggu sampai besok (reset otomatis jam 00:00)
- Atau upgrade ke Google Workspace (2000 email/hari)

### **Error: Missing Payment Data**
```
✗ Cannot send payment email: No payment record found
  This booking doesn't have payment data yet.
```
**Solution:** Email payment hanya bisa dikirim ke booking yang sudah ada data pembayaran.

### **Error: Validation Error**
```
✗ Validation Error: Missing required template data: email, kode_booking
  The booking data is missing required fields for this email type.
```
**Solution:** Data booking tidak lengkap. Periksa database dan lengkapi data yang kurang.

---

## 📊 Rate Limiting

### **Limits:**
- **Gmail Free**: 100 emails/hari
- **Google Workspace**: 2,000 emails/hari

### **Check Current Usage:**
```bash
php artisan tinker
app(\App\Services\EmailService::class)->getRateLimitStatus();
```

### **Output:**
```php
=> App\Services\RateLimitStatus {
     +currentCount: 45,
     +dailyLimit: 100,
     +remainingQuota: 55,
     +usagePercentage: 45.0,
     +canSend: true,
     +warningThreshold: false,
   }
```

---

## 💡 Tips & Best Practices

### **1. Always Test First**
Sebelum kirim ke customer, test dulu:
```bash
php artisan email:test booking --email=your-test-email@gmail.com
```

### **2. Use Dry Run for Bulk**
Preview dulu sebelum kirim bulk:
```bash
php artisan email:bulk booking --limit=50 --dry-run
```

### **3. Monitor Rate Limit**
Cek quota sebelum kirim banyak email:
```bash
php artisan tinker
app(\App\Services\EmailService::class)->getRateLimitStatus();
```

### **4. Check Logs**
Jika ada error, cek logs:
```bash
tail -f storage/logs/laravel.log
```

### **5. Send in Batches**
Untuk banyak email, kirim bertahap:
```bash
# Kirim 20 email pertama
php artisan email:bulk booking --limit=20

# Tunggu 5 menit, kirim 20 berikutnya
php artisan email:bulk booking --limit=20
```

### **6. Verify Email Headers**
Setelah kirim, cek email di Gmail:
1. Buka email
2. Klik "Show Original"
3. Cek SPF: PASS, DKIM: PASS

---

## 🔍 Troubleshooting

### **Email Masuk Spam?**
1. Mark beberapa email sebagai "Not Spam" untuk training Gmail filter
2. Verifikasi SPF/DKIM pass di email headers
3. Cek subject line tidak ada spam trigger words

### **Email Tidak Terkirim?**
1. Cek MAIL_PASSWORD di .env (harus App Password, bukan password Gmail biasa)
2. Cek logs: `tail -f storage/logs/laravel.log`
3. Test koneksi SMTP: `php artisan tinker` > `Mail::raw('Test', function($m) { $m->to('test@example.com')->subject('Test'); });`

### **Rate Limit Terus Kena?**
1. Upgrade ke Google Workspace untuk 2000 email/hari
2. Atau gunakan service email profesional (SendGrid, Mailgun, AWS SES)

---

## 📚 Related Documentation

- `EMAIL_DELIVERABILITY_SUMMARY.md` - Implementasi lengkap email deliverability
- `.env` - Konfigurasi email SMTP
- `config/mail.php` - Konfigurasi rate limiting dan headers
- `app/Services/EmailService.php` - Service untuk mengirim email

---

## 🆘 Need Help?

Jika masih ada masalah:
1. Cek logs: `storage/logs/laravel.log`
2. Jalankan dengan verbose: `php artisan email:send-booking {kode} {type} -vvv`
3. Test dengan tinker: `php artisan tinker` > manual call EmailService

---

**Happy Emailing! 📧** 🎉
