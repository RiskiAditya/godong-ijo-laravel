<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Booking - {{ $pemesanan->kode_booking }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f4f7fa;">
    <!-- Main Container -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f7fa; padding: 20px 0;">
        <tr>
            <td align="center">
                <!-- Email Content Container -->
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    
                    <!-- Header with Gradient -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 30px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">
                                Konfirmasi Booking Berhasil
                            </h1>
                            <p style="margin: 10px 0 0 0; color: #ffffff; font-size: 16px;">
                                Terima kasih atas booking Anda!
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
                                Booking Anda dengan kode <strong>{{ $pemesanan->kode_booking }}</strong> telah berhasil dibuat untuk <strong>{{ $packageDisplayName ?? $paket?->nama_paket ?? 'Paket Wisata' }}</strong>. Silakan lanjutkan pembayaran untuk mengkonfirmasi kunjungan Anda.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Booking Details Card -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h2 style="margin: 0 0 15px 0; font-size: 18px; color: #111827; font-weight: bold;">
                                            Detail Pemesanan
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
                                            @if(($packageType ?? null) === 'private-room')
                                                <tr>
                                                    <td style="padding: 8px 0; font-size: 14px; color: #6b7280;">Jenis Acara</td>
                                                    <td style="padding: 8px 0; font-size: 14px; color: #111827; text-align: right;">{{ ucfirst($pemesanan->package_specific_data['event_type'] ?? '-') }}</td>
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
                                                    <td style="padding: 8px 0; font-size: 14px; color: #6b7280;">Jenis Pemancingan</td>
                                                    <td style="padding: 8px 0; font-size: 14px; color: #111827; text-align: right;">{{ $fishingTypeLabel }}</td>
                                                </tr>
                                                @if($fishingDurasi)
                                                    <tr>
                                                        <td style="padding: 8px 0; font-size: 14px; color: #6b7280;">Durasi</td>
                                                        <td style="padding: 8px 0; font-size: 14px; color: #111827; text-align: right;">{{ $fishingDurasi }} jam</td>
                                                    </tr>
                                                @endif
                                                @if($fishingTambahanJam > 0)
                                                    <tr>
                                                        <td style="padding: 8px 0; font-size: 14px; color: #6b7280;">Tambahan Jam</td>
                                                        <td style="padding: 8px 0; font-size: 14px; color: #111827; text-align: right;">{{ $fishingTambahanJam }} jam</td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <td style="padding: 8px 0; font-size: 14px; color: #6b7280;">Jumlah Joran</td>
                                                    <td style="padding: 8px 0; font-size: 14px; color: #111827; text-align: right;">{{ $fishingJoran }} joran</td>
                                                </tr>
                                                @if($requiresRental)
                                                    <tr>
                                                        <td style="padding: 8px 0; font-size: 14px; color: #6b7280;">Sewa Alat</td>
                                                        <td style="padding: 8px 0; font-size: 14px; color: #111827; text-align: right;">{{ $fishingRodSizeLabel ? 'Ya • ' . $fishingRodSizeLabel : 'Ya' }}</td>
                                                    </tr>
                                                    @if($fishingRodSizeLabel)
                                                        <tr>
                                                            <td style="padding: 8px 0; font-size: 14px; color: #6b7280;">Ukuran Joran</td>
                                                            <td style="padding: 8px 0; font-size: 14px; color: #111827; text-align: right;">{{ $fishingRodSizeLabel }}</td>
                                                        </tr>
                                                    @endif
                                                @endif
                                                @if(! empty($fishingExtraBait))
                                                    <tr>
                                                        <td style="padding: 8px 0; font-size: 14px; color: #6b7280; vertical-align: top;">Tambahan Umpan</td>
                                                        <td style="padding: 8px 0; font-size: 14px; color: #111827; text-align: right;">{{ implode(' • ', $fishingExtraBait) }}</td>
                                                    </tr>
                                                @endif
                                            @else
                                                <tr>
                                                    <td style="padding: 8px 0; font-size: 14px; color: #6b7280;">Jenis Kunjungan</td>
                                                    <td style="padding: 8px 0; font-size: 14px; color: #111827; text-align: right;">The Waterfall Resto</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td colspan="2" style="border-bottom: 1px solid #e5e7eb;"></td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px; color: #6b7280;">Paket Wisata</td>
                                                <td style="padding: 8px 0; font-size: 14px; color: #111827; text-align: right;">{{ $packageDisplayName ?? $paket?->nama_paket ?? 'Paket Wisata' }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="border-bottom: 1px solid #e5e7eb;"></td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px; color: #6b7280;">Tanggal Kunjungan</td>
                                                <td style="padding: 8px 0; font-size: 14px; color: #111827; text-align: right;">
                                                    {{ \Carbon\Carbon::parse($pemesanan->tanggal_kunjungan ?? $jadwal->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="border-bottom: 1px solid #e5e7eb;"></td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px; color: #6b7280;">Jumlah Orang</td>
                                                <td style="padding: 8px 0; font-size: 14px; color: #111827; text-align: right;">{{ $pemesanan->jumlah_orang }} orang</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="border-bottom: 2px solid #667eea;"></td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0 0 0; font-size: 16px; color: #111827; font-weight: bold;">Total Pembayaran</td>
                                                <td style="padding: 12px 0 0 0; font-size: 18px; color: #667eea; font-weight: bold; text-align: right;">
                                                    {{ $pemesanan->total_harga !== null ? 'Rp ' . number_format($pemesanan->total_harga, 0, ',', '.') : 'Dihitung saat ditimbang' }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Payment Instructions -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #fef3c7; border-radius: 8px; border-left: 4px solid #f59e0b;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="margin: 0 0 10px 0; font-size: 16px; color: #92400e; font-weight: bold;">
                                            📋 Langkah Selanjutnya
                                        </h3>
                                        <p style="margin: 0; font-size: 14px; color: #78350f; line-height: 1.6;">
                                            Silakan lakukan pembayaran untuk mengkonfirmasi booking Anda. Anda akan menerima e-ticket setelah pembayaran berhasil.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Payment Button -->
                    <tr>
                        <td style="padding: 0 30px 40px 30px; text-align: center;">
                            <a href="{{ route('booking.confirmation', $pemesanan->kode_booking) }}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; text-decoration: none; padding: 14px 40px; border-radius: 6px; font-size: 16px; font-weight: bold;">
                                Lanjutkan Pembayaran
                            </a>
                        </td>
                    </tr>
                    
                    <!-- Important Information -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px;">
                            <h3 style="margin: 0 0 15px 0; font-size: 16px; color: #111827; font-weight: bold;">
                                Informasi Penting
                            </h3>
                            <ul style="margin: 0; padding-left: 20px; font-size: 14px; color: #374151; line-height: 1.8;">
                                <li>Booking ini akan otomatis dibatalkan jika pembayaran tidak dilakukan dalam 24 jam</li>
                                <li>Harap datang 15 menit sebelum waktu kunjungan</li>
                                <li>Simpan kode booking Anda untuk referensi</li>
                                <li>Hubungi kami jika ada pertanyaan: rizkyfahri081@gmail.com</li>
                            </ul>
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
