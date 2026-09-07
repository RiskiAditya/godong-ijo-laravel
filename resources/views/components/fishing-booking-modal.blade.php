{{-- Fishing Booking Modal Component --}}

{{-- Add inline CSS for modal to ensure it works even if Tailwind fails --}}
<style>
[x-cloak] { display: none !important; }

.fishing-modal-overlay {
    position: fixed !important;
    inset: 0 !important;
    z-index: 9999 !important;
    overflow-y: auto !important;
    background-color: rgba(0, 0, 0, 0.5) !important;
    display: none !important;
}

.fishing-modal-overlay.is-open {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.fishing-modal-content {
    position: relative !important;
    background: linear-gradient(180deg, #fffdfb 0%, #f6f8f4 100%) !important;
    border-radius: 24px !important;
    max-width: 44rem !important;
    width: 100% !important;
    max-height: 90vh !important;
    overflow-y: auto !important;
    margin: 1rem !important;
    box-shadow: 0 32px 70px rgba(15, 23, 42, 0.18) !important;
    border: 1px solid rgba(15, 118, 110, 0.08) !important;
    padding: 0 !important;
}

/* Header styling */
.fishing-modal-header {
    background: linear-gradient(135deg, #0f766e 0%, #14532d 100%);
    color: white;
    padding: 1.5rem 1.5rem 1.25rem;
    border-radius: 24px 24px 0 0;
    position: relative;
    box-shadow: inset 0 -1px 0 rgba(255,255,255,0.12);
}

.fishing-modal-intro {
    margin: 0.35rem 0 0;
    color: #d7f4e6;
    font-size: 0.9rem;
}

.fishing-stepper {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.65rem;
    padding: 1rem 1.25rem 0;
    background: rgba(255,255,255,0.4);
}

.fishing-step {
    position: relative;
    padding: 0.8rem 0.45rem 0.7rem;
    border-bottom: 3px solid #dfe8e3;
    color: #788880;
    font-size: 0.72rem;
    font-weight: 700;
    text-align: center;
    letter-spacing: 0.02em;
    border-radius: 10px 10px 0 0;
}

.fishing-step.active {
    border-color: #fbbf24;
    color: #0f172a;
    background: rgba(251,191,36,0.08);
}

.fishing-step-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.6rem;
    height: 1.6rem;
    margin-right: 0.35rem;
    border-radius: 50%;
    background: #ecfdf5;
    color: #0f766e;
    font-weight: 800;
    font-size: 0.72rem;
    box-shadow: inset 0 0 0 1px rgba(15,118,110,0.1);
}

.fishing-step.active .fishing-step-number {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: #fff;
    box-shadow: none;
}

.fishing-section-title {
    margin: 0 0 1rem;
    color: #20352d;
    font-size: 1rem;
    font-weight: 700;
}

.fishing-modal-header h3 {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
}

.fishing-modal-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.3s;
}

.fishing-modal-close:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* Body styling */
.fishing-modal-body {
    padding: 1.5rem;
}

/* Form improvements */
.fishing-form-group {
    margin-bottom: 1.35rem;
}

.fishing-form-label {
    display: block;
    font-size: 0.78rem;
    font-weight: 700;
    color: #2f3d37;
    margin-bottom: 0.55rem;
    letter-spacing: 0.02em;
    text-transform: uppercase;
}

.fishing-form-label .required {
    color: #ef4444;
}

.fishing-form-input,
.fishing-form-select {
    width: 100%;
    padding: 0.85rem 1rem;
    border: 1px solid #dfe8e3;
    border-radius: 12px;
    font-size: 1rem;
    color: #1f2937;
    background: rgba(255,255,255,0.9);
    transition: all 0.2s ease;
    box-shadow: inset 0 1px 2px rgba(15,23,42,0.02);
}

.fishing-form-input:focus,
.fishing-form-select:focus {
    outline: none;
    border-color: #0f766e;
    box-shadow: 0 0 0 4px rgba(15,118,110,0.09);
    background: #fff;
}

.fishing-form-input.error {
    border-color: #ef4444;
    box-shadow: 0 0 0 4px rgba(239,68,68,0.08);
}

.fishing-form-error {
    color: #dc2626;
    font-size: 0.82rem;
    margin-top: 0.35rem;
    font-weight: 600;
}

/* Jenis Pemancingan buttons */
.jenis-pemancing-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.65rem;
    margin-top: 0.75rem;
}

.jenis-btn {
    padding: 0.8rem 0.5rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.8rem;
    border: 1px solid #dfe8e3;
    background: #f8faf7;
    color: #294038;
    cursor: pointer;
    transition: all 0.2s ease;
}

.jenis-btn.active {
    background: linear-gradient(135deg, #0f766e 0%, #0f766e 100%);
    color: white;
    border-color: #0f766e;
    box-shadow: 0 12px 22px rgba(15,118,110,0.16);
}

.jenis-btn:hover:not(.active) {
    background: #eefaf4;
    border-color: #0f766e;
    transform: translateY(-1px);
}

/* Umpan counter */
.umpan-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    background: linear-gradient(180deg, rgba(255,255,255,0.9) 0%, rgba(244,248,244,0.95) 100%);
    border: 1px solid #e6efeb;
    border-radius: 14px;
    margin-bottom: 0.75rem;
    box-shadow: 0 6px 18px rgba(15,23,42,0.02);
}

.umpan-counter {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.counter-btn {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    border: 1px solid #dfe8e3;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
    color: #0f766e;
}

.counter-btn:hover {
    border-color: #0f766e;
    background: #ecfdf5;
    transform: translateY(-1px);
}

.counter-value {
    min-width: 48px;
    text-align: center;
    font-weight: 700;
    font-size: 1.125rem;
    color: #1f2937;
}

/* Info box */
.info-box {
    background: linear-gradient(135deg, #ecfeff 0%, #f0fdf4 100%);
    border: 1px solid #bae6fd;
    border-radius: 14px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.info-box-content {
    display: flex;
    gap: 0.75rem;
}

.info-box-icon {
    flex-shrink: 0;
    color: #2563eb;
}

.info-box-text {
    font-size: 0.875rem;
    color: #1e40af;
    line-height: 1.5;
}

/* Footer */
.fishing-modal-footer {
    border-top: 1px solid #edf1ee;
    padding: 1.25rem 1.5rem 1.5rem;
    background: linear-gradient(180deg, rgba(255,255,255,0.95) 0%, rgba(247,250,248,1) 100%);
    border-radius: 0 0 24px 24px;
}

.fishing-footer-actions {
    display: flex;
    gap: 0.75rem;
}

.back-btn {
    flex: 0 0 7rem;
    padding: 0.95rem 1rem;
    border: 1px solid #dbe8df;
    border-radius: 12px;
    background: #fff;
    color: #315147;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
}

.back-btn:hover {
    border-color: #0f766e;
    background: #f2fbf7;
}

.fishing-footer-actions .submit-btn {
    flex: 1;
}

.price-display {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
    padding: 0.85rem 1rem;
    background: rgba(15,118,110,0.04);
    border: 1px solid rgba(15,118,110,0.08);
    border-radius: 12px;
}

.price-label {
    font-size: 0.95rem;
    font-weight: 700;
    color: #374151;
    letter-spacing: 0.02em;
}

.price-amount {
    font-size: clamp(1.4rem, 2vw, 1.8rem);
    font-weight: 800;
    color: #0f766e;
}

.submit-btn {
    width: 100%;
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1.05rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    box-shadow: 0 16px 26px rgba(15,118,110,0.18);
}

.submit-btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 18px 24px rgba(15,118,110,0.24);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Responsive */
@media (max-width: 640px) {
    .jenis-pemancing-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .fishing-modal-content {
        margin: 1rem 0.5rem !important;
    }

    .fishing-stepper,
    .fishing-modal-body,
    .fishing-modal-footer {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .fishing-step {
        font-size: 0.68rem;
    }

    .fishing-step-number {
        display: flex;
        margin: 0 auto 0.25rem;
    }
}

/* Spinner animation */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<div x-data="fishingBookingModal()" 
     x-init="init()"
     @open-fishing-modal.window="console.log('🎣 Event received!'); openModal()"
     x-show="isOpen" 
     x-cloak
     @keydown.escape.window="closeModal()"
     class="fishing-modal-overlay fixed inset-0 z-[9999] overflow-y-auto"
     :class="{ 'is-open': isOpen }"
     style="display: none !important;"
     :style="isOpen ? 'display: flex !important; align-items: center; justify-content: center; background-color: rgba(0, 0, 0, 0.5);' : 'display: none !important;'">
    
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity z-[9998]" 
         style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.5);"
         @click="closeModal()"></div>
    
    {{-- Modal Container --}}
    <div class="fishing-modal-content" style="z-index: 10000;"
         @click.stop>
            
            {{-- Header --}}
            <div class="fishing-modal-header">
                <h3>🎣 Pesan Pemancingan</h3>
                <p class="fishing-modal-intro">Isi data singkat, pilih pengalaman memancing, lalu kami siapkan kunjungan Anda.</p>
                <button @click="closeModal()" class="fishing-modal-close" type="button">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="fishing-stepper" aria-label="Tahapan pemesanan">
                <div class="fishing-step" :class="{ 'active': step === 1 }">
                    <span class="fishing-step-number">1</span> Data diri
                </div>
                <div class="fishing-step" :class="{ 'active': step === 2 }">
                    <span class="fishing-step-number">2</span> Pilihan mancing
                </div>
                <div class="fishing-step" :class="{ 'active': step === 3 }">
                    <span class="fishing-step-number">3</span> Konfirmasi
                </div>
            </div>

            {{-- Body --}}
            <div class="fishing-modal-body">
            {{-- Form --}}
            <form id="fishingBookingForm" @submit.prevent="submitBooking()">
                
                {{-- Nama Pemesan --}}
                <div class="fishing-form-group" x-show="step === 1">
                    <label class="fishing-form-label">
                        Nama Pemesan <span class="required">*</span>
                    </label>
                    <input type="text" 
                           x-model="formData.nama_lengkap"
                           @input="clearError('nama_lengkap')"
                           class="fishing-form-input"
                           :class="{ 'error': errors.nama_lengkap }"
                           placeholder="Masukkan nama lengkap"
                           maxlength="100"
                           minlength="3">
                    <p x-show="errors.nama_lengkap" 
                       x-text="errors.nama_lengkap" 
                       class="fishing-form-error"></p>
                    <p style="margin-top: 0.25rem; font-size: 0.75rem; color: #6b7280;">
                        <span x-text="formData.nama_lengkap.length"></span>/100 karakter
                    </p>
                </div>

                {{-- Email --}}
                <div class="fishing-form-group" x-show="step === 1">
                    <label class="fishing-form-label">
                        Email <span class="required">*</span>
                    </label>
                    <input type="email"
                           x-model="formData.email"
                           @input="clearError('email')"
                           class="fishing-form-input"
                           :class="{ 'error': errors.email }"
                           placeholder="nama@email.com"
                           maxlength="255">
                    <p x-show="errors.email"
                       x-text="errors.email"
                       class="fishing-form-error"></p>
                </div>

                {{-- No. WhatsApp --}}
                <div class="fishing-form-group" x-show="step === 1">
                    <label class="fishing-form-label">
                        No. WhatsApp <span class="required">*</span>
                    </label>
                    <input type="tel" 
                           x-model="formData.no_hp"
                           @input="clearError('no_hp')"
                           class="fishing-form-input"
                           :class="{ 'error': errors.no_hp }"
                           placeholder="08xxxxxxxxxx"
                           maxlength="15"
                           minlength="10">
                    <p x-show="errors.no_hp" 
                       x-text="errors.no_hp" 
                       class="fishing-form-error"></p>
                    <p style="margin-top: 0.25rem; font-size: 0.75rem; color: #6b7280;">
                        <span x-text="formData.no_hp.length"></span>/15 digit (min. 10)
                    </p>
                </div>

                {{-- Tanggal & Jam --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" class="fishing-form-group" x-show="step === 1">
                    <div>
                        <label class="fishing-form-label">
                            Tanggal <span class="required">*</span>
                        </label>
                        <input type="date" 
                               x-model="formData.tanggal_kunjungan"
                               @input="clearError('tanggal_kunjungan')"
                               :min="new Date().toISOString().split('T')[0]"
                               class="fishing-form-input"
                               :class="{ 'error': errors.tanggal_kunjungan }">
                        <p x-show="errors.tanggal_kunjungan" 
                           x-text="errors.tanggal_kunjungan" 
                           class="fishing-form-error"></p>
                    </div>
                    <div>
                        <label class="fishing-form-label">
                            Jam Kunjungan <span class="required">*</span>
                        </label>
                        <input type="time" 
                               x-model="formData.jam_kunjungan"
                               @input="validateJamKunjungan()"
                               min="09:00"
                               max="21:00"
                               class="fishing-form-input"
                               :class="{ 'error': errors.jam_kunjungan }">
                        <p x-show="errors.jam_kunjungan" 
                           x-text="errors.jam_kunjungan" 
                           class="fishing-form-error"></p>
                    </div>
                </div>

                {{-- Jenis Pemancingan (Segmented Button) --}}
                <div class="fishing-form-group" x-show="step === 2">
                    <h4 class="fishing-section-title">Pilih pengalaman memancing</h4>
                    <label class="fishing-form-label">
                        Jenis Pemancingan <span class="required">*</span>
                    </label>
                    <div class="jenis-pemancing-grid">
                        <button type="button"
                                @click="formData.jenis_pemancingan = 'sewa_joran'; calculatePrice()"
                                :class="{ 'active': formData.jenis_pemancingan === 'sewa_joran' }"
                                class="jenis-btn">
                            Sewa Joran
                        </button>
                        <button type="button"
                                @click="formData.jenis_pemancingan = 'tarikan'; calculatePrice()"
                                :class="{ 'active': formData.jenis_pemancingan === 'tarikan' }"
                                class="jenis-btn">
                            Tarikan
                        </button>
                        <button type="button"
                                @click="formData.jenis_pemancingan = 'jackpot'; calculatePrice()"
                                :class="{ 'active': formData.jenis_pemancingan === 'jackpot' }"
                                class="jenis-btn">
                            Jackpot
                        </button>
                        <button type="button"
                                @click="formData.jenis_pemancingan = 'kiloan'; calculatePrice()"
                                :class="{ 'active': formData.jenis_pemancingan === 'kiloan' }"
                                class="jenis-btn">
                            Kiloan
                        </button>
                    </div>
                </div>

                {{-- Durasi & Tambahan Jam (only for Tarikan) --}}
                <div x-show="step === 2 && formData.jenis_pemancingan === 'tarikan'" class="fishing-form-group">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="fishing-form-label">
                                Durasi <span class="required">*</span>
                            </label>
                            <select x-model="formData.durasi"
                                    @change="calculatePrice()"
                                    class="fishing-form-select">
                                <option value="">Pilih Durasi</option>
                                <option value="2">2 jam (Rp 80.000)</option>
                                <option value="4">4 jam (Rp 110.000)</option>
                            </select>
                        </div>
                        <div>
                            <label class="fishing-form-label">
                                Tambahan Jam
                            </label>
                            <input type="number" 
                                   x-model.number="formData.tambahan_jam"
                                   @input="calculatePrice()"
                                   min="0"
                                   class="fishing-form-input"
                                   placeholder="0">
                            <p style="margin-top: 0.25rem; font-size: 0.75rem; color: #6b7280;">Rp 40.000 per jam</p>
                        </div>
                    </div>
                </div>

                {{-- Jumlah Joran --}}
                <div class="fishing-form-group" x-show="step === 2">
                    <label class="fishing-form-label">
                        Jumlah Joran <span class="required">*</span>
                    </label>
                    <input type="number" 
                           x-model.number="formData.jumlah_joran"
                           @input="calculatePrice()"
                           min="1"
                           class="fishing-form-input"
                           placeholder="1">
                </div>

                {{-- Ukuran Joran (for Sewa Joran) --}}
                <div x-show="step === 2 && formData.jenis_pemancingan === 'sewa_joran'" class="fishing-form-group">
                    <label class="fishing-form-label">
                        Ukuran Joran <span class="required">*</span>
                    </label>
                    <select x-model="formData.ukuran_joran"
                            @change="calculatePrice()"
                            class="fishing-form-select">
                        <option value="">Pilih Ukuran</option>
                        <option value="standar">Standar (Rp 20.000)</option>
                        <option value="besar">Besar (Rp 50.000)</option>
                    </select>
                </div>

                {{-- Sewa Alat Pancing (for non-Sewa Joran) --}}
                <div x-show="step === 2 && formData.jenis_pemancingan !== 'sewa_joran'" class="fishing-form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" 
                               x-model="formData.perlu_sewa_alat"
                               @change="calculatePrice()"
                               style="width: 16px; height: 16px; accent-color: #059669;">
                        <span class="fishing-form-label" style="margin-bottom: 0;">Perlu sewa alat pancing?</span>
                    </label>
                    
                    <div x-show="formData.perlu_sewa_alat" style="margin-top: 0.75rem;">
                        <label class="fishing-form-label">
                            Ukuran Joran <span class="required">*</span>
                        </label>
                        <select x-model="formData.ukuran_joran"
                                @change="calculatePrice()"
                                class="fishing-form-select">
                            <option value="">Pilih Ukuran</option>
                            <option value="standar">Standar (Rp 20.000)</option>
                            <option value="besar">Besar (Rp 50.000)</option>
                        </select>
                    </div>
                </div>

                {{-- Info Box for Kiloan --}}
                <div x-show="step === 3 && formData.jenis_pemancingan === 'kiloan'" class="fishing-form-group">
                    <div class="info-box">
                        <div class="info-box-content">
                            <div class="info-box-icon">
                                <svg style="width: 20px; height: 20px;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <p style="font-weight: 600; margin-bottom: 0.25rem;" class="info-box-text">Informasi Mancing Kiloan</p>
                                <p class="info-box-text">
                                    Ikan wajib ditimbang & tidak boleh dilepas. Harga per kg dihitung setelah penimbangan sesuai jenis & berat ikan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tambah Umpan --}}
                <div class="fishing-form-group" x-show="step === 3">
                    <h4 class="fishing-section-title">Tambahan untuk kunjungan Anda <span style="font-size: 0.8rem; font-weight: 400; color: #718078;">(opsional)</span></h4>
                    <label class="fishing-form-label">
                        🎣 Tambah Umpan (Opsional)
                    </label>
                    
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        {{-- Anak Ikan Komet --}}
                        <div class="umpan-card">
                            <div>
                                <p style="font-weight: 600; font-size: 0.875rem; color: #1f2937; margin-bottom: 0.125rem;">Anak Ikan Komet</p>
                                <p style="font-size: 0.75rem; color: #6b7280;">Rp 11.000 / 3 ekor</p>
                            </div>
                            <div class="umpan-counter">
                                <button type="button" 
                                        @click="if(formData.qty_komet > 0) { formData.qty_komet--; calculatePrice(); }"
                                        class="counter-btn">
                                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </button>
                                <span class="counter-value" x-text="formData.qty_komet"></span>
                                <button type="button" 
                                        @click="formData.qty_komet++; calculatePrice()"
                                        class="counter-btn">
                                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Umpan Jadi Godongijo --}}
                        <div class="umpan-card">
                            <div>
                                <p style="font-weight: 600; font-size: 0.875rem; color: #1f2937; margin-bottom: 0.125rem;">Umpan Jadi Godongijo</p>
                                <p style="font-size: 0.75rem; color: #6b7280;">Rp 11.000 / pack</p>
                            </div>
                            <div class="umpan-counter">
                                <button type="button" 
                                        @click="if(formData.qty_umpan_jadi > 0) { formData.qty_umpan_jadi--; calculatePrice(); }"
                                        class="counter-btn">
                                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </button>
                                <span class="counter-value" x-text="formData.qty_umpan_jadi"></span>
                                <button type="button" 
                                        @click="formData.qty_umpan_jadi++; calculatePrice()"
                                        class="counter-btn">
                                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Checkbox Persetujuan --}}
                <div class="fishing-form-group" x-show="step === 3">
                    <label style="display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" 
                               x-model="formData.setuju_aturan"
                               @change="clearError('setuju_aturan')"
                               style="margin-top: 0.25rem; width: 16px; height: 16px; accent-color: #059669;">
                        <span style="font-size: 0.875rem; color: #374151; line-height: 1.4;">
                            Saya setuju aturan pemancingan (dilarang umpan lure/kroto/cuka/daging hewan, jam operasional 09.00–21.00 WIB) <span class="required">*</span>
                        </span>
                    </label>
                    <p x-show="errors.setuju_aturan" 
                       x-text="errors.setuju_aturan" 
                       class="fishing-form-error"></p>
                </div>

            </form>
            </div>

            {{-- Footer: Estimasi Total & Submit --}}
            <div class="fishing-modal-footer">
                <div class="price-display">
                    <span class="price-label">Estimasi total</span>
                    <span class="price-amount" x-text="priceDisplay"></span>
                </div>

                <div class="fishing-footer-actions">
                    <button type="button" x-show="step > 1" @click="previousStep()" class="back-btn">Kembali</button>
                    <button type="button" x-show="step < 3" @click="nextStep()" class="submit-btn">Lanjut</button>
                    <button type="submit"
                            form="fishingBookingForm"
                            x-show="step === 3"
                            :disabled="loading"
                            class="submit-btn"
                            :style="loading ? 'opacity: 0.6; cursor: not-allowed;' : ''">
                        <svg x-show="loading" style="width: 20px; height: 20px; animation: spin 1s linear infinite;" fill="none" viewBox="0 0 24 24">
                            <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="loading ? 'Memproses...' : 'Konfirmasi Pesanan'"></span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

