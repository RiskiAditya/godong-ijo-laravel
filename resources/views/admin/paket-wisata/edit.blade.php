@extends('layouts.admin')

@section('title', 'Edit Paket Wisata')
@section('page-title', 'Edit Paket')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-edit-paket.css') }}?v={{ @filemtime(public_path('css/admin-edit-paket.css')) }}">
@endpush

@section('content')
<div class="edit-package-container">
    <a href="{{ route('admin.paket-wisata.index') }}" class="back-link">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>Kembali ke Daftar Paket</span>
    </a>

    <div class="edit-card">
        <div class="card-header">
            <div class="card-header-content">
                <h1 class="card-title">
                    <span class="card-title-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                        </svg>
                    </span>
                    Edit Paket Wisata
                </h1>
                <p class="card-subtitle">Perbarui informasi paket wisata "{{ $paket->nama_paket }}"</p>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.paket-wisata.update', $paket) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Informasi Dasar Section -->
                <div class="form-section">
                    <h2 class="section-title">
                        <span class="section-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M13 7h-2v4H7v2h4v4h2v-4h4v-2h-4V7zm-1-5C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                            </svg>
                        </span>
                        Informasi Dasar
                    </h2>
                    
                    <div class="form-grid">
                        <!-- Nama Paket -->
                        <div class="form-group full-width">
                            <label class="form-label">
                                Nama Paket
                                <span class="required-badge">*</span>
                            </label>
                            <input type="text" 
                                   name="nama_paket" 
                                   value="{{ old('nama_paket', $paket->nama_paket) }}"
                                   class="form-input @error('nama_paket') error @enderror"
                                   placeholder="Contoh: Paket Kuliner Keluarga"
                                   required>
                            @error('nama_paket')
                            <p class="error-message">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                </svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <!-- Jenis Paket -->
                        <div class="form-group full-width">
                            <label class="form-label">
                                Jenis Paket
                                <span class="required-badge">*</span>
                            </label>
                            <select name="jenis_paket" 
                                    class="form-select @error('jenis_paket') error @enderror"
                                    required>
                                <option value="">-- Pilih Kategori Paket --</option>
                                <option value="The Waterfall Resto" {{ old('jenis_paket', $paket->jenis_paket) == 'The Waterfall Resto' ? 'selected' : '' }}>The Waterfall Resto</option>
                                <option value="Private Room" {{ old('jenis_paket', $paket->jenis_paket) == 'Private Room' ? 'selected' : '' }}>Private Room</option>
                                <option value="Fishing Lake" {{ old('jenis_paket', $paket->jenis_paket) == 'Fishing Lake' ? 'selected' : '' }}>Fishing Lake</option>
                            </select>
                            <p class="form-hint">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                                </svg>
                                Pilih kategori untuk mengelompokkan paket berdasarkan jenis layanan
                            </p>
                            @error('jenis_paket')
                            <p class="error-message">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                </svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>
                        
                        <!-- Deskripsi -->
                        <div class="form-group full-width">
                            <label class="form-label">
                                Deskripsi Paket
                                <span class="required-badge">*</span>
                            </label>
                            <textarea name="deskripsi" 
                                      class="form-textarea @error('deskripsi') error @enderror"
                                      placeholder="Deskripsikan paket wisata ini secara detail, termasuk fasilitas yang tersedia, menu kuliner, pemandangan, dan layanan yang diberikan..."
                                      required>{{ old('deskripsi', $paket->deskripsi) }}</textarea>
                            <p class="form-hint">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                                </svg>
                                Jelaskan secara detail untuk membantu pengunjung memahami paket ini
                            </p>
                            @error('deskripsi')
                            <p class="error-message">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                </svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Harga & Kapasitas Section -->
                <div class="form-section">
                    <h2 class="section-title">
                        <span class="section-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                            </svg>
                        </span>
                        Harga & Kapasitas
                    </h2>
                    
                    <div class="form-grid">
                        <!-- Harga -->
                        <div class="form-group">
                            <label class="form-label">
                                Harga Per Orang (Rp)
                                <span class="required-badge">*</span>
                            </label>
                            <input type="number" 
                                   name="harga" 
                                   value="{{ old('harga', $paket->harga) }}"
                                   min="0"
                                   step="1000"
                                   class="form-input @error('harga') error @enderror"
                                   placeholder="75000"
                                   required>
                            <p class="form-hint">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                                </svg>
                                Gunakan kelipatan 1000 untuk kemudahan transaksi
                            </p>
                            @error('harga')
                            <p class="error-message">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                </svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>
                        
                        <!-- Kuota -->
                        <div class="form-group">
                            <label class="form-label">
                                Kuota Per Hari
                                <span class="required-badge">*</span>
                            </label>
                            <input type="number" 
                                   name="kuota" 
                                   value="{{ old('kuota', $paket->kuota) }}"
                                   min="1"
                                   class="form-input @error('kuota') error @enderror"
                                   placeholder="100"
                                   required>
                            <p class="form-hint">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                                </svg>
                                Jumlah maksimal pengunjung yang dapat diterima per hari
                            </p>
                            @error('kuota')
                            <p class="error-message">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                </svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Foto Paket Section -->
                <div class="form-section">
                    <h2 class="section-title">
                        <span class="section-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                            </svg>
                        </span>
                        Foto Paket
                    </h2>
                    
                    <div class="form-group full-width">
                        <div class="image-upload-zone">
                            @if($paket->foto)
                            <div class="current-image-wrapper">
                                <p class="current-image-label">🖼️ Foto Saat Ini</p>
                                <img src="{{ asset($paket->foto) }}" 
                                     alt="{{ $paket->nama_paket }}"
                                     class="current-image">
                            </div>
                            @endif
                            
                            <div class="file-input-wrapper">
                                <label class="form-label">
                                    {{ $paket->foto ? 'Ganti Foto Paket' : 'Upload Foto Paket' }}
                                </label>
                                <input type="file" 
                                       name="foto"
                                       accept="image/*"
                                       class="file-input @error('foto') error @enderror"
                                       onchange="previewImage(event)">
                                <p class="form-hint">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                                    </svg>
                                    Format: JPEG, PNG, JPG, WEBP • Maksimal 2MB • {{ $paket->foto ? 'Biarkan kosong jika tidak ingin mengubah' : 'Gunakan foto berkualitas tinggi' }}
                                </p>
                                @error('foto')
                                <p class="error-message">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                            
                            <div id="imagePreview" class="preview-wrapper">
                                <p class="preview-label">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                                    </svg>
                                    Preview Foto Baru
                                </p>
                                <img id="preview" src="" alt="Preview" class="preview-image">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Status Section -->
                <div class="form-section status-section">
                    <h2 class="section-title">
                        <span class="section-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </span>
                        Status & Publikasi
                    </h2>
                    
                    <div class="form-group full-width">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', $paket->is_active) ? 'checked' : '' }}
                                   class="checkbox-input">
                            <span class="checkbox-label">Tampilkan paket di website publik</span>
                        </label>
                        <p class="form-hint status-hint">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                            </svg>
                            Paket yang tidak aktif tidak akan ditampilkan kepada pengunjung
                        </p>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/>
                        </svg>
                        <span>Simpan Perubahan</span>
                    </button>
                    <a href="{{ route('admin.paket-wisata.index') }}" class="btn-secondary">
                        <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        </svg>
                        <span>Batal</span>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview');
    const previewWrapper = document.getElementById('imagePreview');
    
    if (file) {
        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('⚠️ Ukuran file terlalu besar! Maksimal 2MB.');
            event.target.value = '';
            previewWrapper.classList.remove('show');
            return;
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            alert('⚠️ Format file tidak didukung! Gunakan JPEG, PNG, JPG, atau WEBP.');
            event.target.value = '';
            previewWrapper.classList.remove('show');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewWrapper.classList.add('show');
        }
        reader.readAsDataURL(file);
    } else {
        previewWrapper.classList.remove('show');
    }
}

// Form validation feedback
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const requiredInputs = form.querySelectorAll('[required]');
    
    requiredInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.classList.add('error');
            } else {
                this.classList.remove('error');
            }
        });
        
        input.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.classList.remove('error');
            }
        });
    });
    
    // Number input formatting
    const hargaInput = document.querySelector('input[name="harga"]');
    if (hargaInput) {
        hargaInput.addEventListener('blur', function() {
            const value = parseInt(this.value);
            if (!isNaN(value)) {
                // Round to nearest 1000
                this.value = Math.round(value / 1000) * 1000;
            }
        });
    }
});
</script>
@endpush
@endsection
