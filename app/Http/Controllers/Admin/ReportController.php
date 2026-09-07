<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Models\PaketWisata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display reports and analytics
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ]);

        $startDate = $validated['start_date'] ?? now()->subDays(30)->format('Y-m-d');
        $endDate = $validated['end_date'] ?? now()->format('Y-m-d');

        // Keep the selected dates inclusive, including the whole end date.
        $startDateTime = \Carbon\Carbon::parse($startDate)->startOfDay();
        $endDateTime = \Carbon\Carbon::parse($endDate)->endOfDay();
        
        // Revenue statistics
        $revenueStats = [
            'total' => Pemesanan::where('status', 'paid')
                ->whereBetween('created_at', [$startDateTime, $endDateTime])
                ->sum('total_harga'),
            'average' => Pemesanan::where('status', 'paid')
                ->whereBetween('created_at', [$startDateTime, $endDateTime])
                ->avg('total_harga'),
            'count' => Pemesanan::where('status', 'paid')
                ->whereBetween('created_at', [$startDateTime, $endDateTime])
                ->count(),
        ];
        
        // Booking by status
        $bookingByStatus = Pemesanan::select('status', DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();

        // Keep the status summary visible even when the selected period has no bookings.
        $bookingByStatus = array_merge([
            'pending' => 0,
            'paid' => 0,
            'cancelled' => 0,
            'expired' => 0,
        ], $bookingByStatus);
        
        // Top packages
        $topPackages = PaketWisata::withCount([
            'pemesanan' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [
                    \Carbon\Carbon::parse($startDate)->startOfDay(),
                    \Carbon\Carbon::parse($endDate)->endOfDay(),
                ]);
            }
        ])
        ->withSum([
            'pemesanan as revenue' => function($query) use ($startDate, $endDate) {
                $query->where('status', 'paid')
                      ->whereBetween('created_at', [
                          \Carbon\Carbon::parse($startDate)->startOfDay(),
                          \Carbon\Carbon::parse($endDate)->endOfDay(),
                      ]);
            }
        ], 'total_harga')
        ->having('pemesanan_count', '>', 0)
        ->orderByDesc('pemesanan_count')
        ->take(5)
        ->get();
        
        // Daily revenue (last 30 days)
        $dailyRevenueRows = Pemesanan::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_harga) as revenue'),
                DB::raw('COUNT(*) as bookings')
            )
            ->where('status', 'paid')
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Keep every date in the selected period so the chart reflects the filter
        // even when one or more days have no paid transactions.
        $dailyRevenueByDate = $dailyRevenueRows->keyBy('date');
        $dailyRevenue = collect();
        for ($date = $startDateTime->copy(); $date->lte($endDateTime); $date->addDay()) {
            $dateKey = $date->toDateString();
            $row = $dailyRevenueByDate->get($dateKey);

            $dailyRevenue->push((object) [
                'date' => $dateKey,
                'revenue' => (float) ($row->revenue ?? 0),
                'bookings' => (int) ($row->bookings ?? 0),
            ]);
        }
        
        return view('admin.reports.index', compact(
            'revenueStats',
            'bookingByStatus',
            'topPackages',
            'dailyRevenue',
            'startDate',
            'endDate'
        ));
    }
}
