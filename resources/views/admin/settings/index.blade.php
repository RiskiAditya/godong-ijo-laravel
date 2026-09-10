@extends('layouts.admin')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')

@push('styles')
<style>
/* Settings Page - Professional Design */
.settings-nav {
    display: flex;
    gap: 4px;
    margin-bottom: 24px;
    border-bottom: 1px solid #E5E7EB;
    overflow-x: auto;
}

.settings-nav-item {
    padding: 12px 16px;
    font-size: 13.5px;
    font-weight: 500;
    color: #6B7280;
    border: none;
    background: transparent;
    cursor: pointer;
    transition: all 0.15s ease;
    border-bottom: 2px solid transparent;
    white-space: nowrap;
    text-decoration: none;
}

.settings-nav-item:hover {
    color: #111827;
    background: #F9FAFB;
}

.settings-nav-item.active {
    color: #111827;
    border-bottom-color: #111827;
}

.settings-section {
    display: none;
}

.settings-section.active {
    display: block;
}

.form-section {
    border-bottom: 1px solid #F3F4F6;
    padding: 24px 0;
}

.form-section:first-child {
    padding-top: 0;
}

.form-section:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.form-section-header {
    margin-bottom: 16px;
}

.form-section-title {
    font-size: 14px;
    font-weight: 600;
    color: #111827;
    margin: 0 0 4px;
}

.form-section-description {
    font-size: 13px;
    color: #6B7280;
    margin: 0;
}

.form-field {
    margin-bottom: 20px;
}

.form-field:last-child {
    margin-bottom: 0;
}

.form-label {
    display: block;
    font-size: 13px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 6px;
}

.form-label-required::after {
    content: '*';
    color: #EF4444;
    margin-left: 4px;
}

.form-input {
    width: 100%;
    padding: 9px 12px;
    border: 1px solid #D1D5DB;
    border-radius: 7px;
    font-size: 13.5px;
    color: #111827;
    transition: all 0.15s ease;
}

.form-input:focus {
    outline: none;
    border-color: #111827;
    box-shadow: 0 0 0 3px rgba(17, 24, 39, 0.05);
}

.form-input:disabled {
    background: #F9FAFB;
    color: #9CA3AF;
}

.password-field {
    position: relative;
}

.password-field .form-input {
    padding-right: 42px;
}

.password-toggle {
    position: absolute;
    right: 8px;
    bottom: 7px;
    display: grid;
    place-items: center;
    width: 30px;
    height: 30px;
    border: 0;
    border-radius: 6px;
    background: transparent;
    color: #6B7280;
    cursor: pointer;
}

.password-toggle:hover {
    background: #F3F4F6;
    color: #111827;
}

.password-toggle svg {
    width: 17px;
    height: 17px;
}

.form-hint {
    display: block;
    font-size: 12px;
    color: #6B7280;
    margin-top: 6px;
}

.form-error {
    display: block;
    font-size: 12px;
    color: #EF4444;
    margin-top: 6px;
}

.toggle-field {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 16px;
    background: #F9FAFB;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.15s ease;
}

.toggle-field:hover {
    background: #F3F4F6;
}

.toggle-switch {
    position: relative;
    width: 40px;
    height: 24px;
    flex-shrink: 0;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #D1D5DB;
    border-radius: 12px;
    transition: 0.2s;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    border-radius: 50%;
    transition: 0.2s;
}

.toggle-switch input:checked + .toggle-slider {
    background-color: #111827;
}

.toggle-switch input:checked + .toggle-slider:before {
    transform: translateX(16px);
}

.toggle-content {
    flex: 1;
}

.toggle-title {
    font-size: 13.5px;
    font-weight: 500;
    color: #111827;
    margin: 0 0 4px;
}

.toggle-description {
    font-size: 12.5px;
    color: #6B7280;
    margin: 0;
}

.info-card {
    padding: 18px;
    background: #F9FAFB;
    border: 1px solid #E5E7EB;
    border-radius: 10px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #E5E7EB;
}

.info-row:first-child {
    padding-top: 0;
}

.info-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.info-label {
    font-size: 12.5px;
    color: #6B7280;
}

.info-value {
    font-size: 13px;
    color: #111827;
    font-weight: 500;
    text-align: right;
}

.action-buttons {
    display: flex;
    gap: 8px;
    padding-top: 20px;
}

.btn-save {
    padding: 9px 16px;
    background: #111827;
    color: white;
    border: 1px solid #111827;
    border-radius: 7px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-save:hover {
    background: #1F2937;
    border-color: #1F2937;
}

.btn-cancel {
    padding: 9px 16px;
    background: white;
    color: #6B7280;
    border: 1px solid #D1D5DB;
    border-radius: 7px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
}

.btn-cancel:hover {
    background: #F9FAFB;
    color: #111827;
}

.alert-success {
    padding: 12px 16px;
    background: #ECFDF5;
    border: 1px solid #A7F3D0;
    border-radius: 8px;
    color: #065F46;
    font-size: 13px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-error {
    padding: 12px 16px;
    background: #FEF2F2;
    border: 1px solid #FECACA;
    border-radius: 8px;
    color: #991B1B;
    font-size: 13px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.profile-avatar {
    width: 72px;
    height: 72px;
    border-radius: 12px;
    background: #F3F4F6;
    border: 2px solid #E5E7EB;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    font-weight: 600;
    color: #6B7280;
    font-family: 'IBM Plex Mono', monospace;
}

.profile-info {
    flex: 1;
}

.profile-name {
    font-size: 18px;
    font-weight: 600;
    color: #111827;
    margin: 0 0 4px;
}

.profile-role {
    font-size: 13px;
    color: #6B7280;
    margin: 0;
}
</style>
@endpush

@section('content')
<div class="page-head">
    <div>
        <h1>Pengaturan</h1>
        <p class="page-sub">Kelola profil admin, keamanan, dan konfigurasi sistem</p>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M20 6L9 17l-5-5"/>
        </svg>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="alert-error">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <circle cx="12" cy="12" r="9"/>
            <path d="M12 8v4M12 16h.01"/>
        </svg>
        <span>{{ session('error') }}</span>
    </div>
@endif

<div class="grid-main">
    <!-- Main Content -->
    <div>
        <!-- Navigation Tabs -->
        <div class="settings-nav">
            <button class="settings-nav-item active" data-settings-tab="profile">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle; margin-right: 6px;">
                    <circle cx="9" cy="8" r="4"/>
                    <path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/>
                </svg>
                Profil
            </button>
            <button class="settings-nav-item" data-settings-tab="security">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle; margin-right: 6px;">
                    <rect x="3" y="11" width="18" height="11" rx="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                Keamanan
            </button>
            <button class="settings-nav-item" data-settings-tab="system">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle; margin-right: 6px;">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M12 1v6m0 6v6M5.6 5.6l4.2 4.2m4.4 4.4l4.2 4.2M1 12h6m6 0h6M5.6 18.4l4.2-4.2m4.4-4.4l4.2-4.2"/>
                </svg>
                Sistem
            </button>
        </div>

        <!-- Profile Section -->
        <div id="profile-section" class="settings-section active">
            <div class="panel">
                <form method="POST" action="{{ route('admin.settings.update') }}" style="padding: 28px;">
                    @csrf
                    
                    <div class="form-section">
                        <div class="form-section-header">
                            <h3 class="form-section-title">Informasi Profil</h3>
                            <p class="form-section-description">Update informasi dasar akun administrator Anda</p>
                        </div>

                        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 24px;">
                            <div class="profile-avatar">
                                {{ strtoupper(substr($admin->name, 0, 2)) }}
                            </div>
                            <div class="profile-info">
                                <h4 class="profile-name">{{ $admin->name }}</h4>
                                <p class="profile-role">Administrator</p>
                            </div>
                        </div>

                        <div class="form-field">
                            <label class="form-label form-label-required">Nama Lengkap</label>
                            <input type="text" name="name" class="form-input" value="{{ old('name', $admin->name) }}" required>
                            @error('name')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-field">
                            <label class="form-label form-label-required">Email</label>
                            <input type="email" name="email" class="form-input" value="{{ old('email', $admin->email) }}" required>
                            <span class="form-hint">Email digunakan untuk login dan notifikasi sistem</span>
                            @error('email')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        <button type="submit" class="btn-save">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <path d="M17 21v-8H7v8M7 3v5h8"/>
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Security Section -->
        <div id="security-section" class="settings-section">
            <div class="panel">
                <form method="POST" action="{{ route('admin.settings.update') }}" style="padding: 28px;">
                    @csrf
                    
                    <div class="form-section">
                        <div class="form-section-header">
                            <h3 class="form-section-title">Ubah Password</h3>
                            <p class="form-section-description">Pastikan password Anda kuat dan unik untuk keamanan maksimal</p>
                        </div>

                        <div class="form-field">
                            <label class="form-label form-label-required">Password Lama</label>
                            <div class="password-field">
                                <input type="password" name="current_password" class="form-input" required>
                                <button type="button" class="password-toggle" aria-label="Tampilkan password lama" title="Tampilkan password">
                                    <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/>
                                        <circle cx="12" cy="12" r="2.5"/>
                                    </svg>
                                </button>
                            </div>
                            @error('current_password')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-field">
                            <label class="form-label form-label-required">Password Baru</label>
                            <div class="password-field">
                                <input type="password" name="new_password" class="form-input" required minlength="6">
                                <button type="button" class="password-toggle" aria-label="Tampilkan password baru" title="Tampilkan password">
                                    <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/>
                                        <circle cx="12" cy="12" r="2.5"/>
                                    </svg>
                                </button>
                            </div>
                            <span class="form-hint">Minimal 6 karakter, kombinasi huruf dan angka disarankan</span>
                            @error('new_password')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-field">
                            <label class="form-label form-label-required">Konfirmasi Password Baru</label>
                            <div class="password-field">
                                <input type="password" name="new_password_confirmation" class="form-input" required minlength="6">
                                <button type="button" class="password-toggle" aria-label="Tampilkan konfirmasi password" title="Tampilkan password">
                                    <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/>
                                        <circle cx="12" cy="12" r="2.5"/>
                                    </svg>
                                </button>
                            </div>
                            @error('new_password_confirmation')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        <button type="submit" class="btn-save">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- System Section -->
        <div id="system-section" class="settings-section">
            <div class="panel">
                <form method="POST" action="{{ route('admin.settings.update') }}" style="padding: 28px;">
                    @csrf
                    
                    <div class="form-section">
                        <div class="form-section-header">
                            <h3 class="form-section-title">Informasi Kontak</h3>
                            <p class="form-section-description">Informasi kontak yang ditampilkan di website</p>
                        </div>

                        <div class="form-field">
                            <label class="form-label form-label-required">Nama Website</label>
                            <input type="text" name="site_name" class="form-input" value="{{ old('site_name', $settings['site_name']) }}" required>
                        </div>
                        
                        <div class="form-field">
                            <label class="form-label form-label-required">Email Kontak</label>
                            <input type="email" name="contact_email" class="form-input" value="{{ old('contact_email', $settings['contact_email']) }}" required>
                        </div>
                        
                        <div class="form-field">
                            <label class="form-label form-label-required">No. Telepon</label>
                            <input type="text" name="contact_phone" class="form-input" value="{{ old('contact_phone', $settings['contact_phone']) }}" required>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-header">
                            <h3 class="form-section-title">Notifikasi</h3>
                            <p class="form-section-description">Kelola preferensi notifikasi sistem</p>
                        </div>

                        <div class="form-field">
                            <label class="toggle-field">
                                <div class="toggle-switch">
                                    <input type="hidden" name="booking_notification" value="0">
                                    <input type="checkbox" name="booking_notification" value="1" {{ $settings['booking_notification'] ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </div>
                                <div class="toggle-content">
                                    <div class="toggle-title">Notifikasi Booking Baru</div>
                                    <div class="toggle-description">Terima notifikasi setiap ada booking baru masuk ke sistem</div>
                                </div>
                            </label>
                        </div>

                        <div class="form-field">
                            <label class="toggle-field">
                                <div class="toggle-switch">
                                    <input type="hidden" name="email_notification" value="0">
                                    <input type="checkbox" name="email_notification" value="1" {{ $settings['email_notification'] ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </div>
                                <div class="toggle-content">
                                    <div class="toggle-title">Email Konfirmasi Otomatis</div>
                                    <div class="toggle-description">Kirim email konfirmasi otomatis ke pelanggan setelah booking</div>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        <button type="submit" class="btn-save">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <path d="M17 21v-8H7v8M7 3v5h8"/>
                            </svg>
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div style="display: flex; flex-direction: column; gap: 16px;">
        <!-- Account Info -->
        <div class="panel">
            <div class="panel-head">
                <h2>Informasi Akun</h2>
            </div>
            
            <div class="info-card">
                <div class="info-row">
                    <span class="info-label">Status</span>
                    <span class="info-value">
                        <span class="status lunas" style="font-size: 11px;">
                            <span class="status-dot"></span>
                            Active
                        </span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Role</span>
                    <span class="info-value">Administrator</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Login Terakhir</span>
                    <span class="info-value" style="font-family: 'IBM Plex Mono', monospace; font-size: 12px;">
                        {{ now()->format('d M, H:i') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="panel">
            <div class="panel-head">
                <h2>Sistem</h2>
            </div>
            
            <div class="info-card">
                <div class="info-row">
                    <span class="info-label">Laravel</span>
                    <span class="info-value" style="font-family: 'IBM Plex Mono', monospace; font-size: 12px;">
                        {{ app()->version() }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">PHP</span>
                    <span class="info-value" style="font-family: 'IBM Plex Mono', monospace; font-size: 12px;">
                        {{ PHP_VERSION }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Environment</span>
                    <span class="info-value">
                        <span class="status {{ app()->environment('production') ? 'lunas' : 'pending' }}" style="font-size: 11px;">
                            <span class="status-dot"></span>
                            {{ ucfirst(app()->environment()) }}
                        </span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Help -->
        <div class="panel">
            <div class="panel-head">
                <h2>Bantuan</h2>
            </div>
            <div class="quick-list">
                <button class="quick-item" onclick="alert('Dokumentasi akan segera tersedia')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                    </svg>
                    Dokumentasi
                    <svg class="arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </button>
                <button class="quick-item" onclick="window.location.href='mailto:support@godongijo.com'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="2" y="4" width="20" height="16" rx="2"/>
                        <path d="m2 7 10 6 10-6"/>
                    </svg>
                    Support Email
                    <svg class="arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
