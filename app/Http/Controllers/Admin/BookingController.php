<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Services\AdminBookingService;
use App\Services\BookingExportService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        private AdminBookingService $adminBookingService,
        private BookingExportService $bookingExportService,
    ) {
    }

    /**
     * Display a listing of bookings
     */
    public function index(Request $request)
    {
        $bookings = $this->adminBookingService
            ->query($request, ['paketWisata', 'jadwal', 'user'])
            ->paginate(20);
        
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

        try {
            $result = $this->adminBookingService->updateStatus($booking, $request->status);
            $oldStatus = $result['old_status'];
            $newStatus = $result['new_status'];
            $emailNotice = $result['email_notice'];

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Status booking berhasil diubah dari {$oldStatus} menjadi {$newStatus}.{$emailNotice}",
                    'booking' => $booking->refresh(),
                ]);
            }

            return redirect()->back()
                ->with('success', "Status booking berhasil diubah dari {$oldStatus} menjadi {$newStatus}.{$emailNotice}");
        } catch (\InvalidArgumentException $exception) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage(),
                ], 422);
            }

            return redirect()->back()
                ->with('error', $exception->getMessage());
        }
    }

    /**
     * Finalize a kiloan booking after the fish has been weighed.
     */
    public function finalizeKiloan(Request $request, Pemesanan $booking)
    {
        $validated = $request->validate([
            'total_harga' => ['required', 'numeric', 'min:0'],
            'berat_kg' => ['nullable', 'numeric', 'min:0'],
            'hasil_timbangan' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $booking = $this->adminBookingService->finalizeKiloan($booking, $validated);

            return redirect()->route('admin.bookings.show', $booking)
                ->with('success', 'Harga final kiloan berhasil disimpan.');
        } catch (\InvalidArgumentException $exception) {
            \Log::error('Failed to finalize kiloan booking price', [
                'booking_code' => $booking->kode_booking,
                'error' => $exception->getMessage(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', $exception->getMessage());
        } catch (\Throwable $exception) {
            \Log::error('Failed to finalize kiloan booking price', [
                'booking_code' => $booking->kode_booking,
                'error' => $exception->getMessage(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan harga final kiloan. Periksa log aplikasi.');
        }
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

        $type = $validated['type'] ?? 'booking';

        try {
            $sent = $this->adminBookingService->sendEmail(
                $booking,
                $type,
                $validated['reason'] ?? null,
            );
        } catch (\InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
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

        if (! $sent) {
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
        $bookings = $this->adminBookingService
            ->query($request, ['paketWisata', 'jadwal', 'user', 'pembayaran'])
            ->get();
        $type = $request->query('type', $request->get('format', 'csv'));

        return match ($type) {
            'excel' => $this->bookingExportService->exportExcel($bookings),
            'pdf' => $this->bookingExportService->exportPdf($bookings),
            default => $this->bookingExportService->exportCsv($bookings),
        };
    }
}
