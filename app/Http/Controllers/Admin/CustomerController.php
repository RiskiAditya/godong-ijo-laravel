<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers
     */
    public function index(Request $request)
    {
        // Changed: Show both registered users and guest customers from pemesanan
        // Group guest customers by email (or phone if no email)
        
        $query = User::withCount('pemesanan');
        
        // Search by name or email
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('no_hp', 'like', '%' . $request->search . '%');
            });
        }
        
        // Sort
        $allowedSortColumns = ['created_at', 'name', 'email', 'no_hp'];
        $sortBy = $request->get('sort_by', 'created_at');
        $sortBy = in_array($sortBy, $allowedSortColumns, true) ? $sortBy : 'created_at';
        $sortOrder = strtolower($request->get('sort_order', 'desc'));
        $sortOrder = in_array($sortOrder, ['asc', 'desc'], true) ? $sortOrder : 'desc';
        $query->orderBy($sortBy, $sortOrder);
        
        $registeredCustomers = $query->get();
        
        // Get guest customers (bookings without user_id)
        $guestQuery = \App\Models\Pemesanan::whereNull('user_id')
            ->selectRaw('
                MAX(id) as latest_booking_id,
                nama_lengkap,
                email,
                no_hp,
                COUNT(*) as pemesanan_count,
                MAX(created_at) as created_at
            ')
            ->groupBy('email', 'no_hp', 'nama_lengkap')
            ->havingRaw('email IS NOT NULL OR no_hp IS NOT NULL');
        
        // Apply search to guest customers too
        if ($request->filled('search')) {
            $guestQuery->where(function($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('no_hp', 'like', '%' . $request->search . '%');
            });
        }
        
        $guestCustomers = $guestQuery->get();
        
        // Combine registered and guest customers
        $allCustomers = $registeredCustomers->map(function($user) {
            return (object) [
                'id' => $user->id,
                'type' => 'registered',
                'name' => $user->name,
                'email' => $user->email,
                'no_hp' => $user->no_hp,
                'pemesanan_count' => $user->pemesanan_count,
                'created_at' => $user->created_at,
            ];
        })->concat(
            $guestCustomers->map(function($guest) {
                return (object) [
                    'id' => 'guest-' . $guest->latest_booking_id,
                    'type' => 'guest',
                    'name' => $guest->nama_lengkap,
                    'email' => $guest->email,
                    'no_hp' => $guest->no_hp,
                    'pemesanan_count' => $guest->pemesanan_count,
                    'created_at' => $guest->created_at,
                ];
            })
        );
        
        // Sort combined results
        if ($sortBy === 'created_at') {
            $allCustomers = $sortOrder === 'desc' 
                ? $allCustomers->sortByDesc('created_at') 
                : $allCustomers->sortBy('created_at');
        }
        
        // Manual pagination
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $total = $allCustomers->count();
        $customers = new \Illuminate\Pagination\LengthAwarePaginator(
            $allCustomers->forPage($currentPage, $perPage)->values(),
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Display the specified customer
     */
    public function show(Request $request, $identifier)
    {
        // Handle both registered users (numeric ID) and guest customers (guest-{booking_id})
        if (str_starts_with($identifier, 'guest-')) {
            // Guest customer - get booking details
            $bookingId = (int) str_replace('guest-', '', $identifier);
            $booking = \App\Models\Pemesanan::findOrFail($bookingId);
            
            // Get all bookings for this guest (by email or phone)
            $bookingsQuery = \App\Models\Pemesanan::whereNull('user_id')
                ->where(function($q) use ($booking) {
                    if ($booking->email) {
                        $q->where('email', $booking->email);
                    }
                    if ($booking->no_hp) {
                        $q->orWhere('no_hp', $booking->no_hp);
                    }
                })
                ->with(['paketWisata', 'jadwal']);
            
            $bookings = $bookingsQuery->latest()->get();
            
            // Create a virtual user object for the view
            $user = (object) [
                'id' => $identifier,
                'type' => 'guest',
                'name' => $booking->nama_lengkap,
                'email' => $booking->email,
                'no_hp' => $booking->no_hp,
                'created_at' => $bookings->min('created_at'),
                'pemesanan' => $bookings,
            ];
            
            // Calculate statistics
            $stats = [
                'total_bookings' => $bookings->count(),
                'total_spent' => $bookings->where('status', 'paid')->sum('total_harga'),
                'pending_bookings' => $bookings->where('status', 'pending')->count(),
                'completed_bookings' => $bookings->where('status', 'paid')->count(),
            ];
            
        } else {
            // Registered user
            $user = User::findOrFail($identifier);
            $user->load(['pemesanan.paketWisata', 'pemesanan.jadwal']);
            
            // Statistics
            $stats = [
                'total_bookings' => $user->pemesanan()->count(),
                'total_spent' => $user->pemesanan()->where('status', 'paid')->sum('total_harga'),
                'pending_bookings' => $user->pemesanan()->where('status', 'pending')->count(),
                'completed_bookings' => $user->pemesanan()->where('status', 'paid')->count(),
            ];
        }
        
        return view('admin.customers.show', compact('user', 'stats'));
    }
}
