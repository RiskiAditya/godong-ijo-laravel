@extends('layouts.admin')

@section('title', 'Tambah Paket Wisata')
@section('page-title', 'Tambah Paket')

@section('content')
<div class="page-head package-page-head">
    <div>
        <span class="page-kicker">KATALOG &middot; PAKET WISATA</span>
        <h1>Tambah Paket Wisata Baru</h1>
        <p class="page-sub">
            Buat paket baru yang siap ditampilkan dan dipesan pelanggan.
        </p>
    </div>
    <a class="page-back-link" href="{{ route('admin.paket-wisata.index') }}">&larr; Daftar Paket</a>
</div>

<form class="package-form" method="POST" action="{{ route('admin.paket-wisata.store') }}" enctype="multipart/form-data">
    @csrf
    
    <div class="grid-main">
        <!-- Main Form -->
        <div class="package-form-main">
            <div class="panel package-form-card">
                <div class="panel-head">
                    <div>
                        <span class="section-kicker">LANGKAH 01</span>
                        <h2>Informasi Dasar</h2>
                    </div>
                    <span class="required-note"><b>*</b> Wajib diisi</span>
                </div>
                
                <div class="package-form-body">
                    <div class="form-group package-field package-field-wide">
                        <label>Nama Paket <span style="color: var(--red);">*</span></label>
                        <input type="text" name="nama_paket" value="{{ old('nama_paket') }}" required maxlength="255" placeholder="Contoh: Paket Edukasi Anak">
                        @error('nama_paket')
                            <span class="hint" style="color: var(--red);">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group package-field package-field-wide">
                        <label>Jenis Paket <span style="color: var(--red);">*</span></label>
                        <select name="jenis_paket" required>
                            <option value="">-- Pilih Jenis Paket --</option>
                            <option value="The Waterfall Resto" {{ old('jenis_paket') === 'The Waterfall Resto' ? 'selected' : '' }}>The Waterfall Resto</option>
                            <option value="Private Room" {{ old('jenis_paket') === 'Private Room' ? 'selected' : '' }}>Private Room</option>
                            <option value="Fishing Lake" {{ old('jenis_paket') === 'Fishing Lake' ? 'selected' : '' }}>Fishing Lake</option>
                        </select>
                        @error('jenis_paket')
                            <span class="hint" style="color: var(--red);">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group package-field package-field-wide">
                        <label>Deskripsi <span style="color: var(--red);">*</span></label>
                        <textarea name="deskripsi" rows="5" required placeholder="Jelaskan detail paket wisata ini...">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <span class="hint" style="color: var(--red);">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-row package-form-row">
                        <div class="form-group package-field">
                            <label>Harga per Orang (Rp) <span style="color: var(--red);">*</span></label>
                            <input type="number" name="harga" value="{{ old('harga') }}" required min="0" step="1000" placeholder="50000">
                            @error('harga')
                                <span class="hint" style="color: var(--red);">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group package-field">
                            <label>Kuota <span style="color: var(--red);">*</span></label>
                            <input type="number" name="kuota" value="{{ old('kuota', 100) }}" required min="1" placeholder="100">
                            @error('kuota')
                                <span class="hint" style="color: var(--red);">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="panel package-form-card package-photo-card">
                <div class="panel-head">
                    <div>
                        <span class="section-kicker">LANGKAH 02</span>
                        <h2>Foto Paket</h2>
                    </div>
                    <span class="optional-note">Opsional</span>
                </div>
                
                <div class="package-form-body">
                    <div class="form-group package-field">
                        <label class="photo-upload" for="package-photo">
                            <span class="photo-upload-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M12 16V4M7 9l5-5 5 5"/><path d="M4 15v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4"/>
                                </svg>
                            </span>
                            <span class="photo-upload-copy"><strong>Pilih foto paket</strong><small>Tarik file ke sini atau klik untuk memilih</small></span>
                            <span class="photo-upload-action">Pilih File</span>
                        </label>
                        <input id="package-photo" class="photo-file-input" type="file" name="foto" accept="image/jpeg,image/png,image/jpg,image/webp" onchange="previewImage(this)">
                        <span class="hint photo-hint">JPG, PNG, atau WEBP &middot; Maksimal 2MB</span>
                        @error('foto')
                            <span class="hint" style="color: var(--red);">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div id="imagePreview" style="display: none; margin-top: 12px;">
                        <img id="preview" src="" alt="Preview" style="max-width: 100%; height: auto; border-radius: 8px; border: 1px solid var(--line);">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="package-form-sidebar">
            <div class="panel package-form-card package-status-card">
                <div class="panel-head">
                    <div>
                        <span class="section-kicker">LANGKAH 03</span>
                        <h2>Publikasi</h2>
                    </div>
                </div>
                
                <div class="package-form-body package-status-body">
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="is_active" value="1" checked style="width: 16px; height: 16px;">
                            <span>Aktifkan paket ini</span>
                        </label>
                        <span class="hint">Paket aktif akan ditampilkan di website</span>
                    </div>
                    
                    <div class="package-form-actions">
                        <button type="submit" class="btn-solid" style="width: 100%; justify-content: center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <path d="M17 21v-8H7v8M7 3v5h8"/>
                            </svg>
                            <span>Simpan Paket</span>
                        </button>
                        <a href="{{ route('admin.paket-wisata.index') }}" class="btn-ghost" style="width: 100%; justify-content: center; text-align: center;">
                            <span>Batal</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="panel package-form-card package-guide-card">
                <div class="panel-head">
                    <div>
                        <span class="section-kicker">CATATAN</span>
                        <h2>Checklist</h2>
                    </div>
                </div>
                
                <div class="note" style="margin: 12px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M12 8v4M12 16h.01"/>
                    </svg>
                    <div style="line-height: 1.6;">
                        <strong>Tips Mengisi Form:</strong><br>
                        • Pastikan nama paket jelas dan menarik<br>
                        • Deskripsi lengkap membantu pelanggan memahami paket<br>
                        • Foto berkualitas tinggi meningkatkan konversi<br>
                        • Set harga yang kompetitif
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
function previewImage(input) {
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('imagePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        };
        
        reader.readAsDataURL(input.files[0]);
    } else {
        previewContainer.style.display = 'none';
    }
}
</script>
@endpush
@endsection
