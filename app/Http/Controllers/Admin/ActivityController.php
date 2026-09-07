<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Models\PaketWisata;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Display activity log
     */
    public function index(Request $request)
    {
        // For now, we'll show recent activities from bookings and packages
        // In a real application, you might want a dedicated activity_logs table
        
        $activities = collect();
        
        // Recent bookings
        $recentBookings = Pemesanan::with(['paketWisata'])
            ->latest()
            ->take(20)
            ->get()
            ->map(function($booking) {
                $paketName = $booking->paketWisata ? $booking->paketWisata->nama_paket : 'N/A';
                return [
                    'type' => 'booking',
                    'icon' => 'calendar',
                    'message' => "Booking baru {$booking->kode_booking} untuk {$paketName}",
                    'metadata' => "oleh {$booking->nama_lengkap}",
                    'created_at' => $booking->created_at,
                    'status' => $booking->status,
                ];
            });
        
        // Recent package updates (created or updated in last 7 days)
        $recentPackages = PaketWisata::where('updated_at', '>=', now()->subDays(7))
            ->latest('updated_at')
            ->take(10)
            ->get()
            ->map(function($package) {
                $action = $package->created_at->eq($package->updated_at) ? 'ditambahkan' : 'diperbarui';
                return [
                    'type' => 'package',
                    'icon' => 'box',
                    'message' => "Paket {$package->nama_paket} {$action}",
                    'metadata' => "Harga: Rp " . number_format($package->harga, 0, ',', '.'),
                    'created_at' => $package->updated_at,
                    'status' => null,
                ];
            });
        
        // Merge and sort by date
        $activities = $recentBookings->toBase()->merge($recentPackages->toBase())
            ->sortByDesc('created_at')
            ->take(50);
        
        // Filter by type if requested
        if ($request->filled('type')) {
            $activities = $activities->where('type', $request->type);
        }
        
        return view('admin.activity.index', compact('activities'));
    }
}
