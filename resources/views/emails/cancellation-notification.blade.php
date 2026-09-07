<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembatalan Booking - {{ $pemesanan->kode_booking }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f4f7fa;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f7fa; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding: 40px 30px; text-align: center;">
                            <div style="font-size: 48px; margin-bottom: 15px;">⚠️</div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">
                                Booking Dibatalkan
                            </h1>
                            <p style="margin: 10px 0 0 0; color: #ffffff; font-size: 16px;">
                                Pemberitahuan Pembatalan Booking
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Greeting -->
                    <tr>
                        <td style="padding: 30px 30px 20px 30px;">
                            <p style="margin: 0; font-size: 16px; color: #111827; line-height: 1.6;">
                                Halo <strong>{{ $pemesanan->nama_lengkap }}</strong>,
                            </p>
                            <p style="margin: 15px 0 0 0; font-size: 15px; color: #374151; line-height: 1.6;">
                                Kami informasikan bahwa booking Anda dengan kode <strong>{{ $pemesanan->kode_booking }}</strong> telah dibatalkan.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Cancellation Reason Box -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #fef2f2; border-radius: 8px; border-left: 4px solid #ef4444;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="margin: 0 0 10px 0; font-size: 16px; color: #7f1d1d; font-weight: bold;">
                                            Alasan Pembatalan
                                        </h3>
                                        <p style="margin: 0; font-size: 14px; color: #991b1b; line-height: 1.6;">
                                            {{ $reason }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Booking Details Card -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h2 style="margin: 0 0 15px 0; font-size: 18px; color: #111827; font-weight: bold;">
                                            Detail Booking yang Dibatalkan
                                        </h2>
                                        
                                        <!-- Detail Row -->
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px; color: #6b7280;">Kode Booking</td>
                                                <td style="padding: 8px 0; font-size: 14px; color: #111827; font-weight: bold; text-align: right;">{{ $pemesanan->kode_booking }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="border-bottom: 1px solid #e5e7eb;"></td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px; color: #6b7280;">Paket Wisata</td>
                                                <td style="padding: 8px 0; font-size: 14px; color: #111827; text-align: right;">{{ $paket->nama_paket }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="border-bottom: 1px solid #e5e7eb;"></td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px; color: #6b7280;">Tanggal Kunjungan</td>
                                                <td style="padding: 8px 0; font-size: 14px; color: #111827; text-align: right;">
                                                    {{ \Carbon\Carbon::parse($pemesanan->tanggal_kunjungan ?? $pemesanan->jadwal?->tanggal ?? now())->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="border-bottom: 1px solid #e5e7eb;"></td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px; color: #6b7280;">Jumlah Orang</td>
                                                <td style="padding: 8px 0; font-size: 14px; color: #111827; text-align: right;">{{ $pemesanan->jumlah_orang }} orang</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    @if($refundInfo)
                    <!-- Refund Information -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ecfdf5; border-radius: 8px; border-left: 4px solid #10b981;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="margin: 0 0 10px 0; font-size: 16px; color: #065f46; font-weight: bold;">
                                            💰 Informasi Pengembalian Dana
                                        </h3>
                                        <p style="margin: 0; font-size: 14px; color: #047857; line-height: 1.6;">
                                            {{ $refundInfo }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endif
                    
                    <!-- Rebooking CTA -->
                    <tr>
                        <td style="padding: 0 30px 40px 30px; text-align: center;">
                            <p style="margin: 0 0 20px 0; font-size: 15px; color: #374151;">
                                Ingin melakukan booking lagi? Kami siap membantu Anda!
                            </p>
                            <a href="{{ config('app.url') }}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; text-decoration: none; padding: 14px 40px; border-radius: 6px; font-size: 16px; font-weight: bold;">
                                Booking Sekarang
                            </a>
                        </td>
                    </tr>
                    
                    <!-- Contact Information -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px;">
                            <h3 style="margin: 0 0 15px 0; font-size: 16px; color: #111827; font-weight: bold;">
                                Butuh Bantuan?
                            </h3>
                            <p style="margin: 0; font-size: 14px; color: #374151; line-height: 1.6;">
                                Jika Anda memiliki pertanyaan mengenai pembatalan ini atau ingin melakukan booking baru, silakan hubungi kami:
                            </p>
                            <p style="margin: 10px 0 0 0; font-size: 14px; color: #374151;">
                                📧 Email: <a href="mailto:rizkyfahri081@gmail.com" style="color: #667eea; text-decoration: none;">rizkyfahri081@gmail.com</a><br>
                                📞 Telepon: (022) 1234-5678
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px; border-top: 1px solid #e5e7eb;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="text-align: center;">
                                        <p style="margin: 0 0 10px 0; font-size: 16px; color: #111827; font-weight: bold;">
                                            The Waterfall Tourism
                                        </p>
                                        <p style="margin: 0; font-size: 13px; color: #6b7280; line-height: 1.6;">
                                            Jl. Raya Waterfall No. 123, Bandung<br>
                                            Email: rizkyfahri081@gmail.com | Telepon: (022) 1234-5678
                                        </p>
                                        <p style="margin: 15px 0 0 0; font-size: 12px; color: #9ca3af;">
                                            Email ini dikirim secara otomatis, mohon tidak membalas email ini.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
