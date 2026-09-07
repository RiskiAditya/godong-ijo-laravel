<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Ceritakan pengalaman Anda di Godong Ijo</title></head>
<body style="margin:0;background:#eef4f0;color:#20352d;font-family:Arial,sans-serif;line-height:1.6;">
<div style="display:none;max-height:0;overflow:hidden;opacity:0;">Satu menit dari Anda bisa membantu Godong Ijo tumbuh lebih baik.</div>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#eef4f0;padding:32px 16px;">
    <tr><td align="center">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:620px;background:#fff;border:1px solid #dce9e0;border-radius:16px;overflow:hidden;">
            <tr><td style="padding:34px 36px;background:#087f5b;color:#fff;">
                <div style="font-size:11px;letter-spacing:3px;font-weight:bold;">GODONG IJO</div>
                <h1 style="margin:14px 0 8px;font-size:30px;line-height:1.2;">Boleh cerita sedikit?</h1>
                <p style="margin:0;color:#d9f4e6;font-size:16px;">Pengalaman Anda berarti buat kami.</p>
            </td></tr>
            <tr><td style="padding:34px 36px 30px;">
                <p style="margin:0 0 18px;font-size:17px;">Halo {{ $pemesanan->nama_lengkap }},</p>
                <p style="margin:0 0 18px;">Terima kasih sudah meluangkan waktu untuk berkunjung ke Godong Ijo. Kami ingin terus membuat setiap kunjungan terasa menyenangkan.</p>
                <p style="margin:0 0 26px;">Boleh bantu kami dengan membagikan pengalaman Anda di Google? Ulasan singkat dari Anda sangat membantu tamu lain dan memberi semangat untuk tim kami.</p>
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0"><tr><td align="center" style="padding:4px 0 30px;">
                    <a href="{{ $reviewUrl }}" style="display:inline-block;padding:15px 26px;border-radius:9px;background:#087f5b;color:#fff;text-decoration:none;font-weight:bold;font-size:16px;">Bagikan pengalaman di Google</a>
                </td></tr></table>
                <p style="margin:0;padding-top:20px;border-top:1px solid #e6eee8;color:#708078;font-size:13px;">Referensi kunjungan: {{ $pemesanan->kode_booking }}</p>
            </td></tr>
            <tr><td style="padding:18px 36px;border-top:1px solid #edf2ee;background:#f8fbf9;color:#708078;font-size:12px;">Salam hangat,<br><strong style="color:#395348;">Tim Godong Ijo</strong></td></tr>
        </table>
    </td></tr>
</table>
</body></html>
