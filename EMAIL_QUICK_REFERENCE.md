# Email Commands - Quick Reference

## 📧 Kirim Email ke Booking Tertentu

```bash
# Booking confirmation
php artisan email:send-booking BK20260125001 booking

# Payment confirmation
php artisan email:send-booking BK20260125001 payment

# Cancellation notification
php artisan email:send-booking BK20260125001 cancellation --reason="Alasan pembatalan"
```

---

## 📨 Kirim Email Bulk

```bash
# Kirim ke 10 booking pending
php artisan email:bulk booking --status=pending --limit=10

# Kirim ke 20 booking paid
php artisan email:bulk payment --status=paid --limit=20

# Preview (dry run) tanpa mengirim
php artisan email:bulk booking --limit=50 --dry-run
```

---

## 🧪 Test Email

```bash
# Test booking email
php artisan email:test booking --email=your@email.com

# Test payment email
php artisan email:test payment --email=your@email.com

# Test cancellation email
php artisan email:test cancellation --email=your@email.com
```

---

## 🔍 Check Rate Limit

```bash
php artisan tinker
app(\App\Services\EmailService::class)->getRateLimitStatus();
exit
```

---

## 📊 Common Scenarios

### Kirim Ulang Email yang Gagal
```bash
php artisan email:send-booking BK20260125001 booking
```

### Kirim Email ke Booking Lama (Belum Pernah Dapat Email)
```bash
php artisan email:bulk booking --status=pending --limit=50
```

### Notifikasi Pembatalan Manual oleh Admin
```bash
php artisan email:send-booking BK20260125001 cancellation --reason="Dibatalkan admin"
```

### Test Sebelum Go Live
```bash
php artisan email:test booking --email=admin@company.com
php artisan email:test payment --email=admin@company.com
php artisan email:test cancellation --email=admin@company.com
```

---

## 📝 Rate Limits

- **Gmail Free**: 100 emails/hari
- **Google Workspace**: 2,000 emails/hari
- **Reset**: Otomatis setiap jam 00:00

---

## 🚨 Common Errors & Solutions

| Error | Solution |
|-------|----------|
| Booking not found | Cek kode booking benar |
| No email address | Booking tidak punya email (fishing/walk-in) |
| Rate limit exceeded | Tunggu besok atau upgrade Google Workspace |
| Missing payment data | Hanya kirim payment email ke booking yang sudah bayar |
| Email masuk spam | Mark "Not Spam" beberapa kali untuk training |

---

## 💡 Best Practices

1. ✅ Always test first dengan `email:test`
2. ✅ Use `--dry-run` untuk preview bulk email
3. ✅ Check rate limit sebelum kirim banyak email
4. ✅ Kirim bertahap (batch) untuk volume besar
5. ✅ Monitor logs: `tail -f storage/logs/laravel.log`

---

**Need detailed guide?** See `EMAIL_COMMANDS_GUIDE.md`
