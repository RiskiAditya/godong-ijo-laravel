<?php

namespace App\Services;

class BookingExportService
{
    /**
     * Export bookings as CSV.
     */
    public function exportCsv($bookings)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="bookings_export_' . now()->format('Y-m-d_His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($bookings) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'Kode Booking',
                'Tanggal Booking',
                'Nama Lengkap',
                'Email',
                'No HP',
                'Paket Wisata',
                'Tanggal Kunjungan',
                'Jumlah Orang',
                'Total Harga',
                'Status',
                'Metode Pembayaran',
            ]);

            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->kode_booking,
                    $booking->created_at->format('d/m/Y H:i'),
                    $booking->nama_lengkap,
                    $booking->email,
                    $booking->no_hp,
                    $booking->paketWisata->nama_paket ?? 'N/A',
                    $booking->jadwal ? $booking->jadwal->tanggal->format('d/m/Y') : '-',
                    $booking->jumlah_orang,
                    $booking->total_harga,
                    ucfirst($booking->status),
                    $booking->pembayaran->payment_type ?? 'Belum bayar',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export bookings as Excel-compatible HTML table.
     */
    public function exportExcel($bookings)
    {
        $filename = 'bookings_export_' . now()->format('Y-m-d_His') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head><body>';
        $html .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">';
        $html .= '<thead><tr style="background-color: #2d5a27; color: white; font-weight: bold;">';
        $html .= '<th>Kode Booking</th>';
        $html .= '<th>Tanggal Booking</th>';
        $html .= '<th>Nama Lengkap</th>';
        $html .= '<th>Email</th>';
        $html .= '<th>No HP</th>';
        $html .= '<th>Paket Wisata</th>';
        $html .= '<th>Tanggal Kunjungan</th>';
        $html .= '<th>Jumlah Orang</th>';
        $html .= '<th>Total Harga</th>';
        $html .= '<th>Status</th>';
        $html .= '<th>Metode Pembayaran</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($bookings as $booking) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($booking->kode_booking) . '</td>';
            $html .= '<td>' . $booking->created_at->format('d/m/Y H:i') . '</td>';
            $html .= '<td>' . htmlspecialchars($booking->nama_lengkap) . '</td>';
            $html .= '<td>' . htmlspecialchars($booking->email) . '</td>';
            $html .= '<td>' . htmlspecialchars($booking->no_hp) . '</td>';
            $html .= '<td>' . htmlspecialchars($booking->paketWisata->nama_paket ?? 'N/A') . '</td>';
            $html .= '<td>' . ($booking->jadwal ? $booking->jadwal->tanggal->format('d/m/Y') : '-') . '</td>';
            $html .= '<td>' . $booking->jumlah_orang . '</td>';
            $html .= '<td>Rp ' . number_format($booking->total_harga, 0, ',', '.') . '</td>';
            $html .= '<td>' . ucfirst($booking->status) . '</td>';
            $html .= '<td>' . ($booking->pembayaran->payment_type ?? 'Belum bayar') . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></body></html>';

        return response($html, 200, $headers);
    }

    /**
     * Export bookings as HTML view for PDF generation fallback.
     */
    public function exportPdf($bookings)
    {
        return response()->view('admin.bookings.export-pdf', compact('bookings'), 200)
            ->header('Content-Type', 'text/html');
    }
}
