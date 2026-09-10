@extends('layouts.admin')

@section('title', 'Edit Paket Wisata')
@section('page-title', 'Edit Paket')

@section('content')
<div class="page-head package-page-head">
    <div>
        <span class="page-kicker">KATALOG &middot; PAKET WISATA</span>
        <h1>Edit Paket Wisata</h1>
        <p class="page-sub">Perbarui informasi paket, harga, foto, dan status publikasi.</p>
    </div>
    <a class="page-back-link" href="{{ route('admin.paket-wisata.index') }}">
        <span class="page-back-icon" aria-hidden="true">&larr;</span>
        <span>Daftar Paket</span>
    </a>
</div>

<form class="package-form" method="POST" action="{{ route('admin.paket-wisata.update', $paket->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="grid-main">
        <!-- Main Form -->
        <div class="package-form-main">
            <div class="panel package-form-card">
                <div class="panel-head">
                    <div>
                        <span class="section-kicker">INFORMASI</span>
                        <h2>Detail Paket</h2>
                    </div>
                    <span class="required-note"><b>*</b> Wajib diisi</span>
                </div>
                
                <div class="package-form-body">
                    <div class="form-group package-field package-field-wide">
                        <label>Nama Paket <span style="color: var(--red);">*</span></label>
                        <input type="text" name="nama_paket" value="{{ old('nama_paket', $paket->nama_paket) }}" required maxlength="255">
                        @error('nama_paket')
                            <span class="hint" style="color: var(--red);">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group package-field package-field-wide">
                        <label>Jenis Paket <span style="color: var(--red);">*</span></label>
                        <select name="jenis_paket" required>
                            <option value="">-- Pilih Jenis Paket --</option>
                            <option value="The Waterfall Resto" {{ old('jenis_paket', $paket->jenis_paket) === 'The Waterfall Resto' ? 'selected' : '' }}>The Waterfall Resto</option>
                            <option value="Private Room" {{ old('jenis_paket', $paket->jenis_paket) === 'Private Room' ? 'selected' : '' }}>Private Room</option>
                            <option value="Fishing Lake" {{ old('jenis_paket', $paket->jenis_paket) === 'Fishing Lake' ? 'selected' : '' }}>Fishing Lake</option>
                        </select>
                        @error('jenis_paket')
                            <span class="hint" style="color: var(--red);">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group package-field package-field-wide">
                        <label>Deskripsi <span style="color: var(--red);">*</span></label>
                        <textarea name="deskripsi" rows="5" required>{{ old('deskripsi', $paket->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <span class="hint" style="color: var(--red);">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-row package-form-row">
                        <div class="form-group package-field">
                            <label>Harga per Orang (Rp) <span style="color: var(--red);">*</span></label>
                            <input type="number" name="harga" value="{{ old('harga', $paket->harga) }}" required min="0" step="1000">
                            @error('harga')
                                <span class="hint" style="color: var(--red);">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group package-field">
                            <label>Kuota <span style="color: var(--red);">*</span></label>
                            <input type="number" name="kuota" value="{{ old('kuota', $paket->kuota) }}" required min="1">
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
                        <span class="section-kicker">MEDIA</span>
                        <h2>Foto Paket</h2>
                    </div>
                    <span class="optional-note">Opsional</span>
                </div>
                
                <div class="package-form-body">
                    @if($paket->foto)
                        <div class="current-photo">
                            <div class="current-photo-label">Foto saat ini</div>
                            <img src="{{ asset($paket->foto) }}" alt="{{ $paket->nama_paket }}">
                        </div>
                    @endif
                    
                    <div class="form-group package-field">
                        <label class="photo-upload" for="package-photo">
                            <span class="photo-upload-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M12 16V4M7 9l5-5 5 5"/><path d="M4 15v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4"/>
                                </svg>
                            </span>
                            <span class="photo-upload-copy"><strong>{{ $paket->foto ? 'Ganti foto paket' : 'Pilih foto paket' }}</strong><small>Klik untuk memilih file baru</small></span>
                            <span class="photo-upload-action">Pilih File</span>
                        </label>
                        <input id="package-photo" class="photo-file-input" type="file" name="foto" accept="image/jpeg,image/png,image/jpg,image/webp" onchange="previewImage(this)">
                        <span class="hint photo-hint">JPG, PNG, atau WEBP &middot; Maksimal 2MB{{ $paket->foto ? ' &middot; Kosongkan jika tidak ingin mengganti' : '' }}</span>
                        @error('foto')
                            <span class="hint" style="color: var(--red);">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div id="imagePreview" class="new-photo-preview">
                        <div class="current-photo-label">Preview foto baru</div>
                        <img id="preview" src="" alt="Preview">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="package-form-sidebar">
            <div class="panel package-form-card package-status-card">
                <div class="panel-head">
                    <div>
                        <span class="section-kicker">PUBLIKASI</span>
                        <h2>Status & Aksi</h2>
                    </div>
                </div>
                
                <div class="package-form-body package-status-body">
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $paket->is_active) ? 'checked' : '' }} style="width: 16px; height: 16px;">
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
                            <span>Simpan Perubahan</span>
                        </button>
                        <a href="{{ route('admin.paket-wisata.index') }}" class="btn-ghost" style="width: 100%; justify-content: center; text-align: center;">
                            <span>Batal</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="panel package-form-card">
                <div class="panel-head">
                    <div>
                        <span class="section-kicker">DETAIL</span>
                        <h2>Riwayat Paket</h2>
                    </div>
                </div>
                
                <div class="package-meta-body">
                    <table class="package-meta-table">
                        <tr>
                            <td style="padding: 6px 0; color: var(--ink-45);">Dibuat</td>
                            <td style="padding: 6px 0; text-align: right; font-family: 'IBM Plex Mono', monospace;">{{ $paket->created_at->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 0; color: var(--ink-45);">Terakhir diubah</td>
                            <td style="padding: 6px 0; text-align: right; font-family: 'IBM Plex Mono', monospace;">{{ $paket->updated_at->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 0; color: var(--ink-45);">Total Booking</td>
                            <td style="padding: 6px 0; text-align: right; font-weight: 600;">{{ $paket->pemesanan_count ?? 0 }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="panel package-danger-card">
                <div class="panel-head">
                    <div>
                        <span class="section-kicker">PERMANEN</span>
                        <h2>Hapus Paket</h2>
                    </div>
                </div>
                
                <div class="package-danger-body">
                    <p>Hapus paket ini secara permanen dari sistem.</p>
                    <button type="button" onclick="confirmDelete()" class="btn-danger-outline">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                        <span>Hapus Paket</span>
                    </button>
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

function confirmDelete() {
    if (!confirm('Apakah Anda yakin ingin menghapus paket "{{ $paket->nama_paket }}"? Tindakan ini tidak dapat dibatalkan.')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('admin.paket-wisata.destroy', $paket->id) }}';
    
    const csrfField = document.createElement('input');
    csrfField.type = 'hidden';
    csrfField.name = '_token';
    csrfField.value = document.querySelector('meta[name="csrf-token"]').content;
    
    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'DELETE';
    
    form.appendChild(csrfField);
    form.appendChild(methodField);
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
@endsection
