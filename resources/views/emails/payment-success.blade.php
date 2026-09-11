<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f4f7fa;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f7fa; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; padding: 40px 20px; text-align: center;">
                            <div style="width: 60px; height: 60px; background-color: #10b981; border-radius: 50%; display: inline-block; margin-bottom: 15px; line-height: 60px;">
                                <span style="color: white; font-size: 30px;">✓</span>
                            </div>
                            <h1 style="margin: 10px 0; font-size: 24px; font-weight: bold;">Pembayaran Berhasil</h1>
                            <p style="margin: 5px 0; font-size: 14px;">Terima kasih atas pembayaran Anda</p>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td style="padding: 30px 20px;">
                            <p style="margin: 0 0 10px 0; font-size: 16px; color: #333;">
                                <strong>Halo {{ $pemesanan->nama_lengkap }},</strong>
                            </p>
                            
                            <p style="margin: 0 0 20px 0; font-size: 14px; color: #4b5563; line-height: 1.6;">
                                Pembayaran Anda untuk booking <strong>{{ $pemesanan->kode_booking }}</strong> telah berhasil dikonfirmasi. 
                                Berikut adalah detail pemesanan Anda:
                            </p>
                            
                            <!-- Info Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f8fafc; border-left: 4px solid #667eea; margin: 20px 0;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table width="100%" cellpadding="8" cellspacing="0" border="0">
                                            <tr>
                                                <td style="font-size: 14px; color: #6b7280; font-weight: 600;">Kode Booking</td>
                                                <td style="font-size: 14px; color: #111827; font-weight: 600; text-align: right;">{{ $pemesanan->kode_booking }}</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 14px; color: #6b7280; font-weight: 600; border-top: 1px solid #e5e7eb; padding-top: 10px;">Paket Wisata</td>
                                                <td style="font-size: 14px; color: #111827; font-weight: 600; text-align: right; border-top: 1px solid #e5e7eb; padding-top: 10px;">{{ $packageDisplayName ?? $paket?->nama_paket ?? 'Paket Wisata' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 14px; color: #6b7280; font-weight: 600; border-top: 1px solid #e5e7eb; padding-top: 10px;">Tanggal Kunjungan</td>
                                                <td style="font-size: 14px; color: #111827; font-weight: 600; text-align: right; border-top: 1px solid #e5e7eb; padding-top: 10px;">{{ \Carbon\Carbon::parse($pemesanan->tanggal_kunjungan ?? $pemesanan->jadwal?->tanggal ?? now())->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 14px; color: #6b7280; font-weight: 600; border-top: 1px solid #e5e7eb; padding-top: 10px;">Jumlah Orang</td>
                                                <td style="font-size: 14px; color: #111827; font-weight: 600; text-align: right; border-top: 1px solid #e5e7eb; padding-top: 10px;">{{ $pemesanan->jumlah_orang }} orang</td>
                                            </tr>
                                            @if(($packageType ?? null) === 'private-room')
                                                <tr>
                                                    <td style="font-size: 14px; color: #6b7280; font-weight: 600; border-top: 1px solid #e5e7eb; padding-top: 10px;">Jenis Acara</td>
                                                    <td style="font-size: 14px; color: #111827; font-weight: 600; text-align: right; border-top: 1px solid #e5e7eb; padding-top: 10px;">{{ ucfirst($pemesanan->package_specific_data['event_type'] ?? '-') }}</td>
                                                </tr>
                                            @elseif(($packageType ?? null) === 'fishing-lake')
                                                @php
                                                    $fishingPackageData = $pemesanan->package_specific_data ?? [];
                                                    $fishingType = $fishingPackageData['jenis_pemancingan'] ?? null;
                                                    $fishingTypeLabel = $fishingType ? ucfirst(str_replace('_', ' ', $fishingType)) : '-';
                                                    $fishingDurasi = $fishingPackageData['durasi'] ?? null;
                                                    $fishingTambahanJam = (int) ($fishingPackageData['tambahan_jam'] ?? 0);
                                                    $fishingJoran = $fishingPackageData['jumlah_joran'] ?? $pemesanan->jumlah_orang;
                                                    $requiresRental = (bool) ($fishingPackageData['perlu_sewa_alat'] ?? false) || $fishingType === 'sewa_joran';
                                                    $fishingRodSize = $fishingPackageData['ukuran_joran'] ?? null;
                                                    $fishingRodSizeLabel = match ($fishingRodSize) {
                                                        'standar' => 'Standar',
                                                        'besar' => 'Besar',
                                                        default => null,
                                                    };
                                                    $fishingUmpan = $fishingPackageData['umpan'] ?? [];
                                                    $fishingExtraBait = [];
                                                    if (($fishingUmpan['anak_ikan_komet'] ?? 0) > 0) {
                                                        $fishingExtraBait[] = 'Anak Ikan Komet: ' . (int) $fishingUmpan['anak_ikan_komet'] . ' pack';
                                                    }
                                                    if (($fishingUmpan['umpan_jadi_godongijo'] ?? 0) > 0) {
                                                        $fishingExtraBait[] = 'Umpan Jadi Godongijo: ' . (int) $fishingUmpan['umpan_jadi_godongijo'] . ' pack';
                                                    }
                                                @endphp
                                                <tr>
                                                    <td style="font-size: 14px; color: #6b7280; font-weight: 600; border-top: 1px solid #e5e7eb; padding-top: 10px;">Jenis Pemancingan</td>
                                                    <td style="font-size: 14px; color: #111827; font-weight: 600; text-align: right; border-top: 1px solid #e5e7eb; padding-top: 10px;">{{ $fishingTypeLabel }}</td>
                                                </tr>
                                                @if($fishingDurasi)
                                                    <tr>
                                                        <td style="font-size: 14px; color: #6b7280; font-weight: 600; border-top: 1px solid #e5e7eb; padding-top: 10px;">Durasi</td>
                                                        <td style="font-size: 14px; color: #111827; font-weight: 600; text-align: right; border-top: 1px solid #e5e7eb; padding-top: 10px;">{{ $fishingDurasi }} jam</td>
                                                    </tr>
                                                @endif
                                                @if($fishingTambahanJam > 0)
                                                    <tr>
                                                        <td style="font-size: 14px; color: #6b7280; font-weight: 600; border-top: 1px solid #e5e7eb; padding-top: 10px;">Tambahan Jam</td>
                                                        <td style="font-size: 14px; color: #111827; font-weight: 600; text-align: right; border-top: 1px solid #e5e7eb; padding-top: 10px;">{{ $fishingTambahanJam }} jam</td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <td style="font-size: 14px; color: #6b7280; font-weight: 600; border-top: 1px solid #e5e7eb; padding-top: 10px;">Jumlah Joran</td>
                                                    <td style="font-size: 14px; color: #111827; font-weight: 600; text-align: right; border-top: 1px solid #e5e7eb; padding-top: 10px;">{{ $fishingJoran }} joran</td>
                                                </tr>
                                                @if($requiresRental)
                                                    <tr>
                                                        <td style="font-size: 14px; color: #6b7280; font-weight: 600; border-top: 1px solid #e5e7eb; padding-top: 10px;">Sewa Alat</td>
                                                        <td style="font-size: 14px; color: #111827; font-weight: 600; text-align: right; border-top: 1px solid #e5e7eb; padding-top: 10px;">{{ $fishingRodSizeLabel ? 'Ya • ' . $fishingRodSizeLabel : 'Ya' }}</td>
                                                    </tr>
                                                    @if($fishingRodSizeLabel)
                                                        <tr>
                                                            <td style="font-size: 14px; color: #6b7280; font-weight: 600; border-top: 1px solid #e5e7eb; padding-top: 10px;">Ukuran Joran</td>
                                                            <td style="font-size: 14px; color: #111827; font-weight: 600; text-align: right; border-top: 1px solid #e5e7eb; padding-top: 10px;">{{ $fishingRodSizeLabel }}</td>
                                                        </tr>
                                                    @endif
                                                @endif
                                                @if(! empty($fishingExtraBait))
                                                    <tr>
                                                        <td style="font-size: 14px; color: #6b7280; font-weight: 600; border-top: 1px solid #e5e7eb; padding-top: 10px; vertical-align: top;">Tambahan Umpan</td>
                                                        <td style="font-size: 14px; color: #111827; font-weight: 600; text-align: right; border-top: 1px solid #e5e7eb; padding-top: 10px;">{{ implode(' • ', $fishingExtraBait) }}</td>
                                                    </tr>
                                                @endif
                                            @endif
                                            <tr>
                                                <td style="font-size: 14px; color: #6b7280; font-weight: 600; border-top: 1px solid #e5e7eb; padding-top: 10px;">Metode Pembayaran</td>
                                                <td style="font-size: 14px; color: #111827; font-weight: 600; text-align: right; border-top: 1px solid #e5e7eb; padding-top: 10px;">{{ strtoupper($pembayaran->payment_type ?? 'N/A') }}</td>
                                            </tr>
                                        </table>
                                        
                                        <!-- Total -->
                                        <table width="100%" cellpadding="15" cellspacing="0" border="0" style="background-color: #667eea; margin-top: 20px;">
                                            <tr>
                                                <td style="font-size: 18px; color: white; font-weight: 600;">Total Pembayaran</td>
                                                <td style="font-size: 18px; color: white; font-weight: 600; text-align: right;">
                                                    {{ $pemesanan->total_harga !== null ? 'Rp ' . number_format($pemesanan->total_harga, 0, ',', '.') : 'Dihitung saat ditimbang' }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="margin: 20px 0; font-size: 14px; color: #4b5563; line-height: 1.6;">
                                Silakan tunjukkan email ini atau kode booking Anda saat kedatangan di lokasi The Waterfall.
                            </p>
                            
                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ route('booking.confirmation', $pemesanan->kode_booking) }}" style="display: inline-block; background-color: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 16px;">
                                            Lihat Detail Booking
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Important Info -->
                            <table width="100%" cellpadding="20" cellspacing="0" border="0" style="border-top: 1px solid #e5e7eb; margin-top: 30px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 10px 0; font-size: 13px; color: #6b7280;"><strong>Informasi Penting:</strong></p>
                                        <ul style="margin: 0; padding-left: 20px; font-size: 13px; color: #6b7280; line-height: 1.8;">
                                            <li>Harap datang 15 menit sebelum waktu kunjungan</li>
                                            <li>Simpan email ini sebagai bukti pembayaran</li>
                                            <li>Hubungi kami jika ada pertanyaan: <a href="mailto:info@thewaterfall.com" style="color: #667eea; text-decoration: none;">info@thewaterfall.com</a></li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 20px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 5px 0; font-size: 14px; color: #374151; font-weight: 600;">The Waterfall Tourism</p>
                            <p style="margin: 5px 0; font-size: 13px; color: #6b7280;">Jl. Raya Waterfall No. 123, Bandung</p>
                            <p style="margin: 5px 0; font-size: 13px; color: #6b7280;">Email: info@thewaterfall.com | Telepon: (022) 1234-5678</p>
                            <p style="margin: 15px 0 5px 0; font-size: 12px; color: #9ca3af;">
                                Email ini dikirim secara otomatis, mohon tidak membalas email ini.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
