<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaketWisata;
use App\Support\PrivateRoomPackageCatalog;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PaketWisataController extends Controller
{
    /**
     * Display a listing of paket wisata
     */
    public function index(Request $request)
    {
        $this->ensurePrivateRoomPackagesExist();

        $query = PaketWisata::withCount('pemesanan');

        $query->where(function ($packageQuery) {
            $packageQuery
                ->where('jenis_paket', '!=', 'Private Room')
                ->orWhereIn('nama_paket', [
                    'Meeting Package',
                    'Gathering Package',
                    'Wedding Package',
                ]);
        });

        $query->whereNotIn('nama_paket', [
            'Wedding Intimate Package',
            'Engagement Intimate Package',
            'Paket Half Day Gathering',
            'Paket Full Day Gathering',
            'Paket Half Day Meeting',
            'Paket Full Day Meeting',
            'Paket VIP Meeting',
        ]);

        // Search
        if ($request->filled('search')) {
            $query->where('nama_paket', 'like', '%' . $request->search . '%')
                  ->orWhere('jenis_paket', 'like', '%' . $request->search . '%');
        }
        
        // Filter by jenis_paket
        if ($request->filled('jenis')) {
            $query->where('jenis_paket', $request->jenis);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $isActive = $request->status === 'active' ? 1 : 0;
            $query->where('is_active', $isActive);
        }
        
        $allPackages = $query->latest()->get()->unique('nama_paket')->values();
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;
        $pakets = new LengthAwarePaginator(
            $allPackages->forPage($currentPage, $perPage)->values(),
            $allPackages->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
        $pakets->appends($request->query());
        
        return view('admin.packages.index', compact('pakets'));
    }

    private function ensurePrivateRoomPackagesExist(): void
    {
        foreach (PrivateRoomPackageCatalog::cards() as $card) {
            PaketWisata::firstOrCreate(
                [
                    'nama_paket' => $card['name'],
                    'jenis_paket' => 'Private Room',
                ],
                [
                    'deskripsi' => $card['description'],
                    'foto' => $card['image'],
                    'harga' => 0,
                    'kuota' => 100,
                    'is_active' => true,
                    'booking_config' => [
                        'price_type' => 'package',
                        'minimum_pax' => 1,
                        'event_type' => $card['options'][0]['event'],
                        'duration' => 'package',
                        'private_room_options' => $card['options'],
                    ],
                ]
            );
        }
    }

    /**
     * Show the form for creating a new paket
     */
    public function create()
    {
        return view('admin.packages.create');
    }

    /**
     * Store a newly created paket
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_paket' => 'required|string|max:255',
            'jenis_paket' => 'required|in:The Waterfall Resto,Private Room,Fishing Lake',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'kuota' => 'required|integer|min:1',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
        ]);
        
        // Handle foto upload
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $fotoName = $foto->hashName();
            $foto->move(public_path('images/paket'), $fotoName);
            $validated['foto'] = 'images/paket/' . $fotoName;
        }
        
        $validated['is_active'] = $request->has('is_active') ? true : false;
        
        PaketWisata::create($validated);
        
        return redirect()->route('admin.paket-wisata.index')
            ->with('success', 'Paket wisata berhasil ditambahkan');
    }
    /**
     * Display the specified paket
     */
    public function show(PaketWisata $paketWisata)
    {
        $paketWisata->loadCount('pemesanan');
        return view('admin.packages.show', ['paket' => $paketWisata]);
    }

    /**
     * Show the form for editing the specified paket
     */
    public function edit(PaketWisata $paketWisata)
    {
        return view('admin.paket-wisata.edit', ['paket' => $paketWisata]);
    }

    /**
     * Update the specified paket
     */
    public function update(Request $request, PaketWisata $paketWisata)
    {
        $validated = $request->validate([
            'nama_paket' => 'required|string|max:255',
            'jenis_paket' => 'required|in:The Waterfall Resto,Private Room,Fishing Lake',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'kuota' => 'required|integer|min:1',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
        ]);
        
        // Remove foto from validated data if no new foto uploaded
        // This prevents overwriting existing foto with null
        unset($validated['foto']);
        
        // Handle foto upload only if new foto is provided
        if ($request->hasFile('foto')) {
            // Delete old foto if exists
            if ($paketWisata->foto && file_exists(public_path($paketWisata->foto))) {
                unlink(public_path($paketWisata->foto));
            }
            
            $foto = $request->file('foto');
            $fotoName = time() . '_' . $foto->getClientOriginalName();
            $foto->move(public_path('images/paket'), $fotoName);
            $validated['foto'] = 'images/paket/' . $fotoName;
        }
        
        $validated['is_active'] = $request->has('is_active') ? true : false;
        
        $paketWisata->update($validated);
        
        return redirect()->route('admin.paket-wisata.index')
            ->with('success', 'Paket wisata berhasil diperbarui');
    }

    /**
     * Remove the specified paket
     */
    public function destroy(PaketWisata $paketWisata)
    {
        if ($paketWisata->pemesanan()->exists()) {
            return redirect()->route('admin.paket-wisata.index')
                ->with('error', 'Paket tidak dapat dihapus karena sudah memiliki histori booking. Nonaktifkan paket saja.');
        }

        // Delete foto if exists
        if ($paketWisata->foto && file_exists(public_path($paketWisata->foto))) {
            unlink(public_path($paketWisata->foto));
        }

        $namaPaket = $paketWisata->nama_paket;
        $paketWisata->delete();
        
        return redirect()->route('admin.paket-wisata.index')
            ->with('success', "Paket {$namaPaket} berhasil dihapus");
    }
}
