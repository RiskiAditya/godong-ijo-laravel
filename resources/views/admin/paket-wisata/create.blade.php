@extends('admin.layouts.dashboard')

@section('title', 'Tambah Paket Wisata')
@section('page-title', 'Tambah Paket Wisata')

@section('page-content')
<div class="mb-4">
    <a href="{{ route('admin.paket-wisata.index') }}" class="text-gray-600 hover:text-gray-900">
        <i class="fas fa-arrow-left mr-2"></i>Kembali ke List Paket
    </a>
</div>

<div class="bg-white rounded-xl shadow-md p-6">
    <form action="{{ route('admin.paket-wisata.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nama Paket -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Paket <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="nama_paket" 
                       value="{{ old('nama_paket') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600 @error('nama_paket') border-red-500 @enderror"
                       placeholder="Contoh: Paket Petualangan Alam"
                       required>
                @error('nama_paket')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jenis Paket -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Jenis Paket <span class="text-red-500">*</span>
                </label>
                <select name="jenis_paket" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600 @error('jenis_paket') border-red-500 @enderror"
                        required>
                    <option value="">-- Pilih Jenis Paket --</option>
                    <option value="The Waterfall Resto" {{ old('jenis_paket') == 'The Waterfall Resto' ? 'selected' : '' }}>The Waterfall Resto</option>
                    <option value="Private Room" {{ old('jenis_paket') == 'Private Room' ? 'selected' : '' }}>Private Room</option>
                    <option value="Fishing Lake" {{ old('jenis_paket') == 'Fishing Lake' ? 'selected' : '' }}>Fishing Lake</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Pilih kategori jenis paket untuk mengelompokkan penawaran</p>
                @error('jenis_paket')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Deskripsi -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi <span class="text-red-500">*</span>
                </label>
                <textarea name="deskripsi" 
                          rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600 @error('deskripsi') border-red-500 @enderror"
                          placeholder="Deskripsikan paket wisata ini..."
                          required>{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Harga -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Harga (Rp) <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       name="harga" 
                       value="{{ old('harga') }}"
                       min="0"
                       step="1000"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600 @error('harga') border-red-500 @enderror"
                       placeholder="100000"
                       required>
                @error('harga')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Kuota -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Kuota Per Hari <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       name="kuota" 
                       value="{{ old('kuota') }}"
                       min="1"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600 @error('kuota') border-red-500 @enderror"
                       placeholder="50"
                       required>
                @error('kuota')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Foto -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Foto Paket
                </label>
                <input type="file" 
                       name="foto"
                       accept="image/*"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600 @error('foto') border-red-500 @enderror"
                       onchange="previewImage(event)">
                <p class="text-xs text-gray-500 mt-1">Format: JPEG, PNG, JPG, WEBP. Maksimal 2MB</p>
                @error('foto')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                
                <!-- Image Preview -->
                <div id="imagePreview" class="mt-3 hidden">
                    <img id="preview" src="" alt="Preview" class="w-48 h-48 object-cover rounded-lg">
                </div>
            </div>
            
            <!-- Status Aktif -->
            <div class="md:col-span-2">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-green-600 focus:ring-green-600">
                    <span class="ml-2 text-sm text-gray-700">Paket Aktif (Ditampilkan di website)</span>
                </label>
            </div>
        </div>
        
        <!-- Buttons -->
        <div class="flex gap-3 mt-6 pt-6 border-t border-gray-200">
            <button type="submit" 
                    class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-save mr-2"></i>Simpan Paket
            </button>
            <a href="{{ route('admin.paket-wisata.index') }}" 
               class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-times mr-2"></i>Batal
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('imagePreview').classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endpush
@endsection
