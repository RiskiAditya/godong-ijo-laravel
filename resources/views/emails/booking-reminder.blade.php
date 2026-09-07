<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengingat Kunjungan Godong Ijo</title>
</head>
<body style="margin:0;background:#f4f7f4;color:#26352f;font-family:Arial,sans-serif;line-height:1.6;">
    <div style="max-width:620px;margin:32px auto;background:#fff;border:1px solid #dfe9e1;border-radius:12px;overflow:hidden;">
        <div style="padding:26px 30px;background:#087f5b;color:#fff;">
            <div style="font-size:11px;letter-spacing:2px;font-weight:bold;">GODONG IJO</div>
            <h1 style="margin:8px 0 0;font-size:24px;">Kunjungan Anda Besok</h1>
        </div>
        <div style="padding:28px 30px;">
            <p>Halo {{ $pemesanan->nama_lengkap }},</p>
            <p>Ini pengingat bahwa kunjungan Anda ke Godong Ijo dijadwalkan besok.</p>
            <div style="padding:18px;background:#f4faf6;border:1px solid #d6eadb;border-radius:8px;">
                <p style="margin:0 0 8px;"><strong>Kode booking:</strong> {{ $pemesanan->kode_booking }}</p>
                <p style="margin:0 0 8px;"><strong>Paket:</strong> {{ $paket->nama_paket ?? 'Godong Ijo' }}</p>
                <p style="margin:0 0 8px;"><strong>Tanggal:</strong> {{ $visitDate }}</p>
                <p style="margin:0;"><strong>Jumlah:</strong> {{ $pemesanan->jumlah_orang ?? 0 }} orang</p>
            </div>
            <p style="margin-top:24px;">Simpan kode booking ini dan tunjukkan saat tiba di lokasi. Kami tunggu kedatangan Anda.</p>
            <p style="margin-bottom:0;">Alamat: Jalan Cinangka Raya Km 10 No. 60, Serua, Bojongsari, Depok.</p>
        </div>
        <div style="padding:16px 30px;border-top:1px solid #edf2ee;color:#718078;font-size:12px;">Email otomatis Godong Ijo. Mohon tidak membalas email ini.</div>
    </div>
</body>
</html>
