@extends('admin.layouts.dashboard')

@section('title', 'Manajemen Paket Wisata')
@section('page-title', 'Manajemen Paket Wisata')

@section('page-content')
<!-- Add Button -->
<div class="mb-6">
    <a href="{{ route('admin.paket-wisata.create') }}" 
       class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition inline-flex items-center">
        <i class="fas fa-plus mr-2"></i>
        Tambah Paket Wisata
    </a>
</div>

<!-- Paket Wisata Table -->
<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Paket</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Paket</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kuota</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($pakets as $paket)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($paket->foto)
                        <img src="{{ asset($paket->foto) }}" 
                             alt="{{ $paket->nama_paket }}"
                             class="w-20 h-20 object-cover rounded-lg">
                        @else
                        <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 text-2xl"></i>
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $paket->nama_paket }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ Str::limit($paket->deskripsi, 60) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($paket->jenis_paket)
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($paket->jenis_paket == 'The Waterfall Resto') bg-blue-100 text-blue-800
                            @elseif($paket->jenis_paket == 'Private Room') bg-purple-100 text-purple-800
                            @elseif($paket->jenis_paket == 'Fishing Lake') bg-teal-100 text-teal-800
                            @else bg-green-100 text-green-800
                            @endif">
                            {{ $paket->jenis_paket }}
                        </span>
                        @else
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            Belum diset
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-gray-900">Rp {{ number_format($paket->harga, 0, ',', '.') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $paket->kuota }} orang/hari</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($paket->is_active)
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Aktif
                        </span>
                        @else
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            Nonaktif
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('admin.paket-wisata.show', $paket) }}" 
                           class="text-blue-600 hover:text-blue-900"
                           title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.paket-wisata.edit', $paket) }}" 
                           class="text-yellow-600 hover:text-yellow-900"
                           title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.paket-wisata.destroy', $paket) }}" 
                              method="POST" 
                              class="inline"
                              onsubmit="return confirm('Yakin ingin menghapus paket ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="text-red-600 hover:text-red-900"
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-box text-5xl mb-4 text-gray-400"></i>
                        <p class="text-lg font-medium">Belum ada paket wisata</p>
                        <p class="text-sm mt-2">Mulai dengan menambahkan paket wisata pertama Anda</p>
                        <a href="{{ route('admin.paket-wisata.create') }}" 
                           class="mt-4 inline-block bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-plus mr-2"></i>Tambah Paket Wisata
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($pakets->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $pakets->links() }}
    </div>
    @endif
</div>
@endsection
