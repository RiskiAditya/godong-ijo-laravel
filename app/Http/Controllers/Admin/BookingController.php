<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function __construct(private EmailService $emailService)
    {
    }

    /**
     * Display a listing of bookings
     */
    public function index(Request $request)
    {
        // Post-migration: use direct paketWisata relationship for better performance
        $query = Pemesanan::with(['paketWisata', 'jadwal', 'user']);
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('tanggal_kunjungan', '>=', $request->tanggal_dari)
                  ->orWhereHas('jadwal', function ($jadwalQuery) use ($request) {
                      $jadwalQuery->whereDate('tanggal', '>=', $request->tanggal_dari);
                  });
            });
        }
        
        if ($request->filled('tanggal_sampai')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('tanggal_kunjungan', '<=', $request->tanggal_sampai)
                  ->orWhereHas('jadwal', function ($jadwalQuery) use ($request) {
                      $jadwalQuery->whereDate('tanggal', '<=', $request->tanggal_sampai);
                  });
            });
        }
        
        // Search by kode booking or nama
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('kode_booking', 'like', '%' . $request->search . '%')
                  ->orWhere('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        $bookings = $query->latest()->paginate(20);
        
        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Display the specified booking
     */
    public function show(Pemesanan $booking)
    {
        // Post-migration: use direct paketWisata relationship for better performance
        $booking->load(['paketWisata', 'jadwal', 'user', 'pembayaran']);
        
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Update booking status
     */
    public function updateStatus(Request $request, Pemesanan $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,cancelled,expired',
        ]);
        
        $oldStatus = $booking->status;
        $newStatus = $request->status;

        DB::transaction(function () use ($booking, $oldStatus, $newStatus) {
            if ($oldStatus === $newStatus) {
                return;
            }

            $booking->update(['status' => $newStatus]);

            if ($newStatus === 'paid' && $booking->pembayaran) {
                $booking->pembayaran->update([
                    'status' => 'success',
                    'paid_at' => now(),
                ]);
            }

            if (in_array($newStatus, ['cancelled', 'expired'], true)
                && !in_array($oldStatus, ['cancelled', 'expired'], true)
                && $booking->jadwal_id
                && (!$booking->pembayaran
                    || !in_array($booking->pembayaran->status, ['failed', 'expired'], true))) {
                $booking->jadwal->incrementKuota($booking->jumlah_orang);
                if ($booking->pembayaran) {
                    $booking->pembayaran->update(['status' => 'failed']);
                }
            }
        });

        $booking->refresh();

        $emailNotice = null;
        if ($oldStatus !== $booking->status && $booking->email) {
            try {
                $this->emailService->sendBookingStatusUpdate($booking, $oldStatus);
                $emailNotice = ' Email status telah dikirim ke pelanggan.';
            } catch (\Throwable $exception) {
                \Log::error('Failed to send booking status email', [
                    'booking_code' => $booking->kode_booking,
                    'error' => $exception->getMessage(),
                ]);
                $emailNotice = ' Status berubah, tetapi email pelanggan gagal dikirim.';
            }
        }
        
        // Check if AJAX request
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Status booking berhasil diubah dari {$oldStatus} menjadi {$newStatus}.{$emailNotice}",
                'booking' => $booking
            ]);
        }
        
        return redirect()->back()
            ->with('success', "Status booking berhasil diubah dari {$oldStatus} menjadi {$newStatus}.{$emailNotice}");
    }

    /**
     * Send a booking email manually from the admin panel.
     */
    public function sendEmail(Request $request, Pemesanan $booking)
    {
        $validated = $request->validate([
            'type' => 'nullable|in:booking,payment,cancellation',
            'reason' => 'nullable|string|max:500',
        ]);

        if (empty($booking->email)) {
            return response()->json([
                'success' => false,
                'message' => 'Booking ini tidak memiliki alamat email.',
            ], 422);
        }

        $type = $validated['type'] ?? 'booking';

        try {
            $sent = match ($type) {
                'payment' => $this->emailService->sendPaymentSuccess($booking),
                'cancellation' => $this->emailService->sendCancellationNotification(
                    $booking,
                    $validated['reason'] ?? 'Booking dibatalkan oleh administrator.'
                ),
                default => $this->emailService->sendBookingConfirmation($booking),
            };
        } catch (\Throwable $exception) {
            \Log::error('Failed to send manual booking email', [
                'booking_code' => $booking->kode_booking,
                'type' => $type,
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Email gagal dikirim. Periksa log aplikasi.',
            ], 500);
        }

        if (!$sent) {
            return response()->json([
                'success' => false,
                'message' => 'Email gagal dikirim. Periksa log aplikasi.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Email berhasil dikirim ke ' . $booking->email . '.',
        ]);
    }
    
    /**
     * Remove the specified booking
     */
    public function destroy(Pemesanan $booking)
    {
        $kodeBooking = $booking->kode_booking;
        $booking->delete();

        return redirect()->route('admin.bookings.index')
            ->with('success', "Booking {$kodeBooking} berhasil dihapus");
    }

    /**
     * Export bookings to CSV/Excel/PDF
     */
    public function export(Request $request)
    {
        // Build query with same filters as index
        $query = Pemesanan::with(['paketWisata', 'jadwal', 'user', 'pembayaran']);
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('tanggal_dari')) {
            $query->whereHas('jadwal', function($q) use ($request) {
                $q->whereDate('tanggal', '>=', $request->tanggal_dari);
            });
        }
        
        if ($request->filled('tanggal_sampai')) {
            $query->whereHas('jadwal', function($q) use ($request) {
                $q->whereDate('tanggal', '<=', $request->tanggal_sampai);
            });
        }
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('kode_booking', 'like', '%' . $request->search . '%')
                  ->orWhere('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        $bookings = $query->latest()->get();
        
        // Determine export format
        $format = $request->get('format', 'csv');
        
        if ($format === 'pdf') {
            return $this->exportPDF($bookings, $request);
        } elseif ($format === 'excel') {
            return $this->exportExcel($bookings, $request);
        } else {
            return $this->exportCSV($bookings, $request);
        }
    }
    
    /**
     * Export to CSV
     */
    private function exportCSV($bookings, $request)
    {
        $filename = 'bookings_export_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV Headers
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
            
            // Data rows
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
                    $booking->pembayaran->metode_pembayaran ?? 'Belum bayar',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Export to Excel (HTML table that Excel can open)
     */
    private function exportExcel($bookings, $request)
    {
        $filename = 'bookings_export_' . now()->format('Y-m-d_His') . '.xls';
        
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
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
            $html .= '<td>' . ($booking->pembayaran->metode_pembayaran ?? 'Belum bayar') . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody></table></body></html>';
        
        return response($html, 200, $headers);
    }
    
    /**
     * Export to PDF
     */
    private function exportPDF($bookings, $request)
    {
        $filename = 'bookings_export_' . now()->format('Y-m-d_His') . '.pdf';
        
        // Generate HTML for PDF
        $html = view('admin.bookings.export-pdf', compact('bookings'))->render();
        
        // Use DomPDF or similar library
        // For now, we'll use browser's print to PDF functionality
        // You can install dompdf/dompdf package for better PDF generation
        
        return response()->view('admin.bookings.export-pdf', compact('bookings'), 200)
            ->header('Content-Type', 'text/html');
    }
}
