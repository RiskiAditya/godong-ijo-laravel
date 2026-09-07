<?php

namespace App\Services;

use App\Models\Pemesanan;
use App\Models\ETiket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

/**
 * E-Ticket PDF Generation Service
 * 
 * NOTE: Using HTML view as fallback until packages are installed
 * - barryvdh/laravel-dompdf (for PDF generation)
 */

// Packages not yet installed - using fallback methods

class ETicketService
{
    /**
     * Generate E-Ticket PDF for a booking
     *
     * @param string $kodeBooking
     * @return string Path to generated PDF
     * @throws \Exception
     */
    public function generate(string $kodeBooking): string
    {
        // Parse booking data from database
        $bookingData = $this->parseBookingData($kodeBooking);
        
        if (!$bookingData) {
            throw new \Exception("Booking not found: {$kodeBooking}");
        }
        
        // Validate required fields
        $this->validateBookingData($bookingData);
        
        // Try to serialize to PDF
        $pdfPath = null;
        $pdfException = null;
        
        try {
            $pdfPath = $this->serializeToPdf($bookingData);
        } catch (\Exception $e) {
            // Store exception to rethrow later, but continue to persist e-tiket record
            $pdfException = $e;
            $pdfPath = null; // No PDF generated
        }
        
        // Persist e-ticket record to database (even if PDF generation failed)
        // This ensures audit trail and tracking
        $pemesanan = Pemesanan::where('kode_booking', $kodeBooking)->first();
        ETiket::updateOrCreate(
            ['pemesanan_id' => $pemesanan->id],
            [
                'file_path' => $pdfPath,
                'diterbitkan_pada' => now(),
            ]
        );
        
        // Rethrow PDF exception if it occurred
        if ($pdfException) {
            throw $pdfException;
        }
        
        return $pdfPath;
    }
    
    /**
     * Parse booking data from database into E-Ticket object
     *
     * @param string $kodeBooking
     * @return array|null
     */
    private function parseBookingData(string $kodeBooking): ?array
    {
        try {
            $pemesanan = Pemesanan::with(['jadwal.paket', 'pembayaran'])
                ->where('kode_booking', $kodeBooking)
                ->first();
                
            if (!$pemesanan) {
                return null;
            }

            if ($pemesanan->status !== 'paid' || !$pemesanan->pembayaran || $pemesanan->pembayaran->status !== 'success') {
                throw new \Exception('E-ticket hanya tersedia setelah pembayaran berhasil');
            }
            
            // Get payment info with null safety
            $pembayaran = $pemesanan->pembayaran;
            $paymentStatus = $pembayaran ? $this->getPaymentStatusLabel($pembayaran->status) : 'Pending';
            $paymentMethod = $pembayaran && $pembayaran->payment_type ? 
                $this->getPaymentMethodLabel($pembayaran->payment_type) : 'N/A';
            
            return [
                'kode_booking' => $pemesanan->kode_booking,
                'customer_name' => $pemesanan->nama_lengkap,
                'email' => $pemesanan->email,
                'phone' => $pemesanan->no_hp,
                'package_name' => $pemesanan->jadwal->paket->nama_paket ?? 'N/A',
                'package_details' => $pemesanan->jadwal->paket->deskripsi ?? 'Paket wisata ke The Waterfall',
                'visit_date' => $pemesanan->jadwal->tanggal->format('d F Y'),
                'visit_time' => '09:00 WIB', // Default check-in time
                'number_of_guests' => $pemesanan->jumlah_orang,
                'total_amount' => $pemesanan->total_harga,
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethod,
                'booking_date' => $pemesanan->created_at->format('d F Y H:i:s'),
            ];
            
        } catch (\Exception $e) {
            Log::error('E-Ticket parsing error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Validate that all required fields are present
     *
     * @param array $booking
     * @return void
     * @throws \Exception
     */
    private function validateBookingData(array $booking): void
    {
        $requiredFields = [
            'kode_booking', 'customer_name', 'package_name',
            'visit_date', 'number_of_guests', 'total_amount'
        ];
        
        foreach ($requiredFields as $field) {
            if (empty($booking[$field])) {
                throw new \Exception("Missing required field: {$field}");
            }
        }
    }
    
    /**
     * Serialize booking data to PDF
     *
     * @param array $booking
     * @return string Path to generated PDF
     * @throws \Exception
     */
    private function serializeToPdf(array $booking): string
    {
        try {
            if (!class_exists(Pdf::class)) {
                throw new \Exception('DomPDF package not installed. Run: composer require barryvdh/laravel-dompdf');
            }

            $pdf = Pdf::loadView('pdf.e-ticket', $booking)
                ->setPaper('a4', 'portrait')
                ->setOption('margin-top', 10)
                ->setOption('margin-bottom', 10);
            $path = "etickets/E-Ticket-{$booking['kode_booking']}.pdf";
            $absolutePath = storage_path('app/' . $path);
            $directory = dirname($absolutePath);

            if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
                throw new \RuntimeException("Unable to create e-ticket directory: {$directory}");
            }

            file_put_contents($absolutePath, $pdf->output());

            return $path;
        } catch (\Exception $e) {
            Log::error('PDF serialization error: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Download E-Ticket PDF (or HTML fallback)
     *
     * @param string $kodeBooking
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function download(string $kodeBooking)
    {
        // Parse booking data
        $bookingData = $this->parseBookingData($kodeBooking);
        
        if (!$bookingData) {
            throw new \Exception("Booking not found: {$kodeBooking}");
        }
        
        // Validate required fields
        $this->validateBookingData($bookingData);
        
        if (!class_exists(Pdf::class)) {
            throw new \Exception('PDF generator is not installed.');
        }

        return Pdf::loadView('pdf.e-ticket', $bookingData)
            ->setPaper('a4', 'portrait')
            ->download("E-Ticket-{$kodeBooking}.pdf");
    }
    
    /**
     * Get friendly payment status label
     *
     * @param string $status
     * @return string
     */
    private function getPaymentStatusLabel(string $status): string
    {
        return match($status) {
            'paid' => 'Lunas',
            'pending' => 'Menunggu Pembayaran',
            'failed' => 'Gagal',
            'expired' => 'Kadaluarsa',
            default => ucfirst($status),
        };
    }
    
    /**
     * Get friendly payment method label
     *
     * @param string $method
     * @return string
     */
    private function getPaymentMethodLabel(string $method): string
    {
        $labels = [
            'credit_card' => 'Kartu Kredit',
            'bca_va' => 'BCA Virtual Account',
            'bni_va' => 'BNI Virtual Account',
            'bri_va' => 'BRI Virtual Account',
            'permata_va' => 'Permata Virtual Account',
            'gopay' => 'GoPay',
            'shopeepay' => 'ShopeePay',
            'qris' => 'QRIS',
            'bank_transfer' => 'Transfer Bank',
        ];
        
        return $labels[$method] ?? ucwords(str_replace('_', ' ', $method));
    }
}
