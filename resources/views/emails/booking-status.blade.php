<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Status Booking Diperbarui</title></head>
<body style="margin:0;background:#f4f7f4;color:#26352f;font-family:Arial,sans-serif;line-height:1.6;">
<div style="max-width:620px;margin:32px auto;background:#fff;border:1px solid #dfe9e1;border-radius:12px;overflow:hidden;">
    <div style="padding:26px 30px;background:#087f5b;color:#fff;"><div style="font-size:11px;letter-spacing:2px;font-weight:bold;">GODONG IJO</div><h1 style="margin:8px 0 0;font-size:24px;">Status Booking Diperbarui</h1></div>
    <div style="padding:28px 30px;"><p>Halo {{ $pemesanan->nama_lengkap }},</p><p>Status booking Anda telah diperbarui oleh tim Godong Ijo.</p>
        <div style="padding:18px;background:#f4faf6;border:1px solid #d6eadb;border-radius:8px;"><p style="margin:0 0 8px;"><strong>Kode booking:</strong> {{ $pemesanan->kode_booking }}</p><p style="margin:0 0 8px;"><strong>Status sebelumnya:</strong> {{ ucfirst($oldStatus) }}</p><p style="margin:0;"><strong>Status sekarang:</strong> {{ ucfirst($pemesanan->status) }}</p></div>
        <p style="margin-top:24px;">Silakan simpan email ini sebagai referensi. Jika membutuhkan bantuan, hubungi WhatsApp {{ config('app.whatsapp.display') }}.</p>
    </div><div style="padding:16px 30px;border-top:1px solid #edf2ee;color:#718078;font-size:12px;">Email otomatis Godong Ijo.</div>
</div></body></html>
