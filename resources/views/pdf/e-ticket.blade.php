<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>E-Ticket - {{ $kode_booking }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #1f2937;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            border-bottom: 4px solid #059669;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #059669;
            margin-bottom: 5px;
        }
        
        .subtitle {
            font-size: 11px;
            color: #6b7280;
        }
        
        .e-ticket-label {
            display: inline-block;
            background: #059669;
            color: white;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            margin-top: 10px;
        }
        
        .booking-code-section {
            text-align: center;
            background: #f0fdf4;
            border: 2px solid #059669;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .booking-code-label {
            font-size: 11px;
            color: #047857;
            margin-bottom: 5px;
        }
        
        .booking-code {
            font-size: 32px;
            font-weight: bold;
            color: #047857;
            letter-spacing: 3px;
            margin: 10px 0;
        }
        
        .booking-hint {
            font-size: 10px;
            color: #065f46;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-row {
            border-bottom: 1px dashed #e5e7eb;
        }
        
        .info-row td {
            padding: 10px 0;
        }
        
        .label {
            font-weight: 600;
            color: #6b7280;
            width: 40%;
        }
        
        .value {
            color: #111827;
            font-weight: 500;
        }
        
        .value.highlight {
            font-size: 16px;
            font-weight: bold;
            color: #059669;
        }
        
        .qr-section {
            text-align: center;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        
        .qr-title {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 10px;
        }
        
        .qr-code {
            display: inline-block;
            padding: 10px;
            background: white;
            border: 2px solid #059669;
            border-radius: 8px;
        }
        
        .qr-code img {
            display: block;
            width: 180px;
            height: 180px;
        }
        
        .qr-instruction {
            font-size: 10px;
            color: #6b7280;
            margin-top: 10px;
        }
        
        .terms {
            font-size: 10px;
            color: #6b7280;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        
        .terms-title {
            font-weight: 700;
            color: #374151;
            margin-bottom: 8px;
        }
        
        .terms ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .terms li {
            margin-bottom: 4px;
        }
        
        .footer {
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        
        .footer .contact {
            color: #059669;
            font-weight: 600;
            margin-top: 5px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
        }
        
        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        /* Print optimization */
        @page {
            margin: 15mm;
        }
        
        @media print {
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">GODONG IJO</div>
            <div class="logo" style="font-size: 20px; color: #047857;">The Waterfall Resto</div>
            <p class="subtitle">Curug Nangka, Bogor - Wisata Alam & Kuliner</p>
            <div class="e-ticket-label">E-TICKET</div>
        </div>
        
        <!-- Booking Code -->
        <div class="booking-code-section">
            <div class="booking-code-label">KODE BOOKING ANDA</div>
            <div class="booking-code">{{ $kode_booking }}</div>
            <div class="booking-hint">Simpan dan tunjukkan kode ini saat check-in</div>
        </div>
        
        <!-- Customer Information -->
        <div class="section">
            <div class="section-title">📋 Informasi Pemesan</div>
            <table class="info-table">
                <tr class="info-row">
                    <td class="label">Nama Lengkap</td>
                    <td class="value">{{ $customer_name }}</td>
                </tr>
                <tr class="info-row">
                    <td class="label">Email</td>
                    <td class="value">{{ $email }}</td>
                </tr>
                <tr class="info-row">
                    <td class="label">No. WhatsApp</td>
                    <td class="value">{{ $phone }}</td>
                </tr>
            </table>
        </div>
        
        <!-- Booking Details -->
        <div class="section">
            <div class="section-title">🎫 Detail Pemesanan</div>
            <table class="info-table">
                <tr class="info-row">
                    <td class="label">Paket Wisata</td>
                    <td class="value"><strong>{{ $package_name }}</strong></td>
                </tr>
                @if(!empty($package_details))
                <tr class="info-row">
                    <td class="label">Keterangan</td>
                    <td class="value">{{ $package_details }}</td>
                </tr>
                @endif
                <tr class="info-row">
                    <td class="label">Tanggal Kunjungan</td>
                    <td class="value"><strong>{{ $visit_date }}</strong></td>
                </tr>
                <tr class="info-row">
                    <td class="label">Waktu Check-in</td>
                    <td class="value">{{ $visit_time }}</td>
                </tr>
                <tr class="info-row">
                    <td class="label">Jumlah Orang</td>
                    <td class="value"><strong>{{ $number_of_guests }} orang</strong></td>
                </tr>
            </table>
        </div>
        
        <!-- Payment Information -->
        <div class="section">
            <div class="section-title">💳 Informasi Pembayaran</div>
            <table class="info-table">
                <tr class="info-row">
                    <td class="label">Total Pembayaran</td>
                    <td class="value highlight">Rp {{ number_format($total_amount, 0, ',', '.') }}</td>
                </tr>
                <tr class="info-row">
                    <td class="label">Status Pembayaran</td>
                    <td class="value">
                        <span class="status-badge {{ strpos(strtolower($payment_status), 'lunas') !== false ? 'status-paid' : 'status-pending' }}">
                            {{ $payment_status }}
                        </span>
                    </td>
                </tr>
                <tr class="info-row">
                    <td class="label">Metode Pembayaran</td>
                    <td class="value">{{ $payment_method }}</td>
                </tr>
                <tr class="info-row">
                    <td class="label">Tanggal Booking</td>
                    <td class="value">{{ $booking_date }}</td>
                </tr>
            </table>
        </div>
        
        <!-- Terms and Conditions -->
        <div class="terms">
            <div class="terms-title">Syarat & Ketentuan:</div>
            <ul>
                <li>E-ticket ini berlaku untuk 1 kali kunjungan sesuai tanggal yang tertera</li>
                <li>Tunjukkan kode booking ini saat check-in di lokasi</li>
                <li>E-ticket tidak dapat dipindahtangankan atau diuangkan kembali</li>
                <li>Mohon datang 15 menit sebelum waktu kunjungan untuk proses check-in</li>
                <li>Pembatalan atau perubahan tanggal harus dilakukan minimal 24 jam sebelum tanggal kunjungan</li>
                <li>Harga sudah termasuk fasilitas yang tercantum dalam paket</li>
                <li>Manajemen berhak menolak kedatangan tanpa pemberitahuan sebelumnya</li>
                <li>Dengan menggunakan e-ticket ini, pengunjung dianggap telah menyetujui semua syarat dan ketentuan yang berlaku</li>
            </ul>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Terima kasih telah memilih <strong>Godong Ijo - The Waterfall Resto</strong></p>
            <p>Nikmati pengalaman wisata alam dan kuliner terbaik bersama kami</p>
            <p class="contact">📞 WhatsApp: 0812-3456-7890 | 📧 info@godongijo.com</p>
            <p style="margin-top: 10px;">Alamat: Curug Nangka, Bogor, Jawa Barat</p>
        </div>
    </div>
</body>
</html>
