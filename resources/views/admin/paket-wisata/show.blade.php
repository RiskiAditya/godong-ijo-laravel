@extends('admin.layouts.dashboard')

@section('title', 'Detail Paket Wisata')
@section('page-title', 'Detail Paket Wisata')

@section('page-content')
<div class="mb-4">
    <a href="{{ route('admin.paket-wisata.index') }}" class="text-gray-600 hover:text-gray-900">
        <i class="fas fa-arrow-left mr-2"></i>Kembali ke List Paket
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Info -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-md p-6">
            <!-- Foto -->
            @if($paket->foto)
            <div class="mb-6">
                <img src="{{ asset($paket->foto) }}" 
                     alt="{{ $paket->nama_paket }}"
                     class="w-full h-64 object-cover rounded-lg">
            </div>
            @endif
            
            <!-- Nama Paket -->
            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $paket->nama_paket }}</h2>
            
            <!-- Jenis Paket Badge -->
            @if($paket->jenis_paket)
            <div class="mb-4">
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                    @if($paket->jenis_paket == 'The Waterfall Resto') bg-blue-100 text-blue-800
                    @elseif($paket->jenis_paket == 'Private Room') bg-purple-100 text-purple-800
                    @elseif($paket->jenis_paket == 'Fishing Lake') bg-teal-100 text-teal-800
                    @else bg-green-100 text-green-800
                    @endif">
                    <i class="fas fa-tag mr-1"></i> {{ $paket->jenis_paket }}
                </span>
            </div>
            @endif
            
            <!-- Status Badge -->
            <div class="mb-4">
                @if($paket->is_active)
                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    <i class="fas fa-check-circle mr-1"></i> Aktif
                </span>
                @else
                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                    <i class="fas fa-times-circle mr-1"></i> Nonaktif
                </span>
                @endif
            </div>
            
            <!-- Deskripsi -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Deskripsi</h3>
                <p class="text-gray-700 whitespace-pre-line">{{ $paket->deskripsi }}</p>
            </div>
            
            <!-- Detail -->
            <div class="grid grid-cols-2 gap-4 border-t border-gray-200 pt-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Harga</p>
                    <p class="text-2xl font-bold text-green-600">Rp {{ number_format($paket->harga, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500 mt-1">per orang</p>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Kuota Per Hari</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $paket->kuota }}</p>
                    <p class="text-xs text-gray-500 mt-1">orang/hari maksimal</p>
                </div>
            </div>
            
            <!-- Timestamps -->
            <div class="mt-6 pt-6 border-t border-gray-200 text-sm text-gray-600">
                <p><i class="fas fa-calendar-plus mr-2"></i>Dibuat: {{ $paket->created_at->format('d M Y H:i') }}</p>
                <p class="mt-1"><i class="fas fa-calendar-edit mr-2"></i>Terakhir diupdate: {{ $paket->updated_at->format('d M Y H:i') }}</p>
            </div>
        </div>
    </div>
    
    <!-- Action Panel -->
    <div class="space-y-6">
        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi</h3>
            
            <div class="space-y-3">
                <a href="{{ route('admin.paket-wisata.edit', $paket) }}" 
                   class="w-full bg-yellow-600 text-white py-2 px-4 rounded-lg hover:bg-yellow-700 transition inline-flex items-center justify-center">
                    <i class="fas fa-edit mr-2"></i>Edit Paket
                </a>
                
                <form action="{{ route('admin.paket-wisata.destroy', $paket) }}" 
                      method="POST"
                      onsubmit="return confirm('Yakin ingin menghapus paket ini?')">
                    @csrf
                    @method('DELETE')
                    
                    <button type="submit" 
                            class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-trash mr-2"></i>Hapus Paket
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Statistics (Optional - for future) -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistik</h3>
            
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">Total Booking</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $paket->bookings()->count() }}</p>
                </div>
                
                <div>
                    <p class="text-sm text-gray-600">Booking Aktif</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $paket->bookings()->whereIn('status', ['pending', 'paid'])->count() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
