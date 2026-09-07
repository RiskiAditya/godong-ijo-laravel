<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function index()
    {
        // Total booking hari ini
        $bookingHariIni = Pemesanan::whereDate('created_at', today())->count();
        
        // Total booking bulan ini
        $bookingBulanIni = Pemesanan::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Total pendapatan (dari booking yang sudah paid)
        $totalPendapatan = Pemesanan::where('status', 'paid')
            ->sum('total_harga');
        
        // Booking pending yang perlu dikonfirmasi
        $bookingPending = Pemesanan::where('status', 'pending')->count();
        $pendingBookingsCount = $bookingPending; // For sidebar badge
        
        // Recent bookings (5 terbaru)
        // Post-migration: use direct paketWisata relationship for better performance
        // Eager load jadwal.paket to prevent N+1 queries
        $recentBookings = Pemesanan::with(['paketWisata', 'jadwal.paket'])
            ->latest()
            ->take(5)
            ->get();
        
        return view('admin.dashboard', compact(
            'bookingHariIni',
            'bookingBulanIni',
            'totalPendapatan',
            'bookingPending',
            'recentBookings',
            'pendingBookingsCount'
        ));
    }
}
