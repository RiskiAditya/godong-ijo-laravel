<!-- Simple Booking Modal with Pure JavaScript -->
<style>
.booking-modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    overflow-y: auto;
}

.booking-modal-overlay.active {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.booking-modal-content {
    background: linear-gradient(180deg, #fffdfb 0%, #f5f7f2 100%);
    border-radius: 28px;
    max-width: 620px;
    width: 100%;
    max-height: 92vh;
    overflow-y: auto;
    box-shadow: 0 36px 80px rgba(15, 23, 42, 0.18);
    border: 1px solid rgba(15, 118, 110, 0.08);
}

.booking-modal-header {
    background: linear-gradient(135deg, #0f766e 0%, #14532d 100%);
    color: white;
    padding: 26px 24px 20px;
    border-radius: 28px 28px 0 0;
    position: relative;
    box-shadow: inset 0 -1px 0 rgba(255,255,255,0.14);
}

.booking-modal-header h2 {
    margin: 0;
    font-size: clamp(1.5rem, 2vw, 2rem);
    font-weight: 800;
    letter-spacing: -0.02em;
}

.booking-modal-header p {
    margin: 8px 0 0 0;
    opacity: 0.92;
    font-size: 0.95rem;
    font-weight: 500;
}

.booking-modal-close {
    position: absolute;
    top: 20px;
    right: 20px;
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 24px;
    line-height: 1;
    transition: background 0.3s;
}

.booking-modal-close:hover {
    background: rgba(255, 255, 255, 0.3);
}

.booking-modal-body {
    padding: 22px;
}

.booking-hero-card {
    background: linear-gradient(135deg, rgba(15,118,110,0.06) 0%, rgba(251,191,36,0.08) 100%);
    border: 1px solid rgba(15,118,110,0.10);
    border-radius: 18px;
    padding: 18px;
    margin-bottom: 22px;
    box-shadow: 0 10px 24px rgba(15,118,110,0.04);
}

.booking-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.35rem 0.7rem;
    border-radius: 999px;
    background: rgba(15,118,110,0.10);
    color: #0f766e;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.booking-summary-grid {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-top: 14px;
}

.booking-summary-name {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 800;
    color: #18342d;
}

.booking-summary-price {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f766e;
}

.booking-benefits {
    margin-top: 16px;
    display: flex;
    flex-wrap: wrap;
    gap: 0.55rem;
}

.booking-benefits span {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: rgba(255,255,255,0.8);
    border: 1px solid rgba(15,118,110,0.10);
    color: #345149;
    border-radius: 999px;
    padding: 0.42rem 0.7rem;
    font-size: 0.72rem;
    font-weight: 700;
}

.booking-form-shell {
    background: rgba(255,255,255,0.44);
    border: 1px solid rgba(15,118,110,0.08);
    border-radius: 20px;
    padding: 18px;
}

.booking-form-group {
    margin-bottom: 22px;
}

.booking-form-label {
    display: block;
    font-size: 0.76rem;
    font-weight: 800;
    margin-bottom: 9px;
    color: #2f3d37;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

.booking-form-label .required {
    color: #ef4444;
}

.booking-form-input {
    width: 100%;
    padding: 0.95rem 1rem;
    border: 1px solid #dfe8e3;
    border-radius: 14px;
    font-size: 1rem;
    color: #1f2937;
    background: rgba(255,255,255,0.9);
    transition: all 0.2s ease;
    box-shadow: inset 0 1px 2px rgba(15,23,42,0.02);
}

.booking-form-input:focus {
    outline: none;
    border-color: #0f766e;
    box-shadow: 0 0 0 4px rgba(15,118,110,0.09);
    background: #fff;
}

.booking-form-input.error {
    border-color: #ef4444;
    box-shadow: 0 0 0 4px rgba(239,68,68,0.08);
}

.booking-form-error {
    color: #dc2626;
    font-size: 0.8rem;
    margin-top: 6px;
    font-weight: 700;
}

.booking-info-box {
    background: linear-gradient(135deg, #ecfeff 0%, #f0fdf4 100%);
    border: 1px solid rgba(14, 165, 233, 0.17);
    padding: 18px 16px;
    border-radius: 16px;
    margin-bottom: 22px;
    box-shadow: 0 8px 20px rgba(15, 118, 110, 0.05);
}

.booking-total-box {
    background: linear-gradient(180deg, rgba(15,118,110,0.04) 0%, rgba(15,118,110,0.02) 100%);
    border: 1px solid rgba(15,118,110,0.08);
    border-radius: 16px;
    padding: 18px 16px;
    margin: 22px 0 18px;
}

.booking-total-price {
    font-size: clamp(1.8rem, 3vw, 2.4rem);
    font-weight: 900;
    color: #0f766e;
    line-height: 1.1;
}

.booking-submit-btn {
    width: 100%;
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
    color: white;
    border: none;
    padding: 0;
    border-radius: 18px;
    font-size: 1.03rem;
    font-weight: 800;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
    box-shadow: 0 18px 30px rgba(15,118,110,0.2);
}

.booking-submit-btn::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(120deg, rgba(255,255,255,0.22), rgba(255,255,255,0));
    opacity: 0.9;
}

.booking-submit-btn > .booking-submit-content {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    width: 100%;
    padding: 1rem 1.1rem;
}

.booking-submit-btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 22px 34px rgba(15,118,110,0.28);
    filter: brightness(1.02);
}

.booking-submit-btn:focus-visible {
    outline: none;
    box-shadow: 0 0 0 4px rgba(15,118,110,0.18), 0 22px 34px rgba(15,118,110,0.28);
}

.booking-submit-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.booking-submit-arrow {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: rgba(255,255,255,0.14);
    border: 1px solid rgba(255,255,255,0.18);
    font-size: 1.2rem;
    line-height: 1;
    transition: transform 0.2s ease;
}

.booking-submit-btn:hover:not(:disabled) .booking-submit-arrow {
    transform: translateX(2px);
}

.booking-loading {
    text-align: center;
    padding: 20px;
}

.booking-spinner {
    border: 4px solid #f3f4f6;
    border-top: 4px solid #059669;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<!-- Modal Overlay -->
<div id="bookingModalOverlay" class="booking-modal-overlay" onclick="closeBookingModalIfOutside(event)">
    <div class="booking-modal-content" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="booking-modal-header" style="position: relative;">
            <h2>Reservasi Booking</h2>
            <p id="bookingModalPackageName"></p>
            <button class="booking-modal-close" onclick="closeBookingModal()">&times;</button>
        </div>

        <!-- Body -->
        <div class="booking-modal-body">
            <div class="booking-hero-card">
                <span class="booking-badge">Paket pilihan</span>
                <div class="booking-summary-grid">
                    <div>
                        <p class="booking-summary-name" id="bookingModalPackageNameInfo"></p>
                    </div>
                    <div style="text-align: right;">
                        <p class="booking-summary-price" id="bookingModalPackagePrice"></p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="booking-form-shell">
            <form id="bookingForm" onsubmit="submitBookingForm(event)">
                <input type="hidden" id="bookingPackageId" name="paket_wisata_id">

                <!-- Nama Lengkap -->
                <div class="booking-form-group">
                    <label class="booking-form-label">
                        Nama Lengkap <span class="required">*</span>
                    </label>
                    <input type="text" 
                           class="booking-form-input" 
                           id="bookingNama"
                           name="nama_lengkap"
                           placeholder="Masukkan nama lengkap"
                           required
                           minlength="3">
                    <div class="booking-form-error" id="errorNama"></div>
                </div>

                <!-- Email & WhatsApp -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="booking-form-group">
                        <label class="booking-form-label">
                            Email <span class="required">*</span>
                        </label>
                        <input type="email" 
                               class="booking-form-input"
                               id="bookingEmail"
                               name="email"
                               placeholder="contoh@email.com"
                               required>
                        <div class="booking-form-error" id="errorEmail"></div>
                    </div>

                    <div class="booking-form-group">
                        <label class="booking-form-label">
                            No. WhatsApp <span class="required">*</span>
                        </label>
                        <input type="tel" 
                               class="booking-form-input"
                               id="bookingPhone"
                               name="no_hp"
                               placeholder="081234567890"
                               required
                               minlength="10"
                               maxlength="15">
                        <div class="booking-form-error" id="errorPhone"></div>
                    </div>
                </div>

                <!-- Tanggal & Jumlah -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="booking-form-group">
                        <label class="booking-form-label">
                            Tanggal Kunjungan <span class="required">*</span>
                        </label>
                        <input type="date" 
                               class="booking-form-input"
                               id="bookingDate"
                               name="tanggal_kunjungan"
                               required>
                        <div class="booking-form-error" id="errorDate"></div>
                    </div>

                    <div class="booking-form-group">
                        <label class="booking-form-label">
                            Jumlah Orang <span class="required">*</span>
                        </label>
                        <input type="number" 
                               class="booking-form-input"
                               id="bookingQty"
                               name="jumlah_orang"
                               placeholder="1"
                               min="1"
                               max="100"
                               value="1"
                               required
                               oninput="updateTotalPrice()">
                        <div class="booking-form-error" id="errorQty"></div>
                    </div>
                </div>

                <!-- Total Price -->
                <div class="booking-total-box">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 600; color: #374151;">Total Harga:</span>
                        <div style="text-align: right;">
                            <div class="booking-total-price" id="bookingTotalPrice">Rp 0</div>
                            <div style="font-size: 14px; color: #6b7280; margin-top: 4px;" id="bookingPriceDetail"></div>
                        </div>
                    </div>
                </div>

                <!-- Loading Container (for LoadingStateManager) -->
                <div id="loadingContainer" style="min-height: 100px;"></div>

                <!-- Loading State (fallback) -->
                <div id="bookingLoading" class="booking-loading" style="display: none;">
                    <div class="booking-spinner"></div>
                    <p style="margin-top: 12px; color: #6b7280;">Memproses pemesanan...</p>
                </div>

                <!-- Error Message -->
                <div id="bookingErrorMessage" style="display: none; background: #fee2e2; border: 1px solid #ef4444; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 16px;"></div>

                <!-- Submit Button -->
                <button type="submit" class="booking-submit-btn" id="bookingSubmitBtn">
                    <span class="booking-submit-content">
                        <span>Lanjut ke Pembayaran</span>
                        <span class="booking-submit-arrow" aria-hidden="true">→</span>
                    </span>
                </button>
            </form>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables for booking
let currentBookingData = {
    id: null,
    nama: '',
    harga: 0
};

// Set minimum date to today
document.addEventListener('DOMContentLoaded', function() {
    // Failsafe: Clear body overflow on page load (in case previous modal wasn't closed properly)
    document.body.style.overflow = '';
    console.log('Page loaded, body overflow cleared as failsafe');
    
    const today = new Date().toISOString().split('T')[0];
    const dateInput = document.getElementById('bookingDate');
    if (dateInput) {
        dateInput.setAttribute('min', today);
    }
    
    // Auto-format phone number (remove non-numeric characters)
    const phoneInput = document.getElementById('bookingPhone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            // Remove all non-numeric characters
            let cleaned = e.target.value.replace(/[^0-9]/g, '');
            // Limit to 15 digits
            if (cleaned.length > 15) {
                cleaned = cleaned.slice(0, 15);
            }
            e.target.value = cleaned;
        });
    }
});

// Add beforeunload event to ensure body overflow is cleared before navigation
window.addEventListener('beforeunload', function() {
    document.body.style.overflow = '';
    console.log('Page unloading, body overflow cleared');
});

// Add pagehide event for better browser back/forward navigation handling
window.addEventListener('pagehide', function() {
    document.body.style.overflow = '';
    console.log('Page hiding, body overflow cleared');
});

// Open booking modal
function openBookingModal(id, nama, harga) {
    console.log('Opening modal:', { id, nama, harga });
    
    // Ensure DOM is ready
    if (document.readyState === 'loading') {
        console.warn('DOM not ready, waiting...');
        document.addEventListener('DOMContentLoaded', function() {
            openBookingModal(id, nama, harga);
        });
        return;
    }
    
    // Validate required elements exist
    const modalOverlay = document.getElementById('bookingModalOverlay');
    const packageNameEl = document.getElementById('bookingModalPackageName');
    const packageNameInfoEl = document.getElementById('bookingModalPackageNameInfo');
    const packagePriceEl = document.getElementById('bookingModalPackagePrice');
    const packageIdInput = document.getElementById('bookingPackageId');
    const bookingForm = document.getElementById('bookingForm');
    const qtyInput = document.getElementById('bookingQty');
    
    if (!modalOverlay || !packageNameEl || !packageNameInfoEl || !packagePriceEl || !packageIdInput || !bookingForm || !qtyInput) {
        console.error('❌ Required modal elements not found in DOM');
        alert('Modal belum siap. Silakan refresh halaman.');
        return;
    }
    
    // Store data
    currentBookingData = { id, nama, harga };
    
    // Update modal content
    packageNameEl.textContent = nama;
    packageNameInfoEl.textContent = nama;
    packagePriceEl.textContent = formatRupiah(harga);
    packageIdInput.value = id;
    
    // Reset form
    bookingForm.reset();
    qtyInput.value = 1;
    updateTotalPrice();
    
    // Clear errors
    clearFormErrors();
    
    // Show modal
    modalOverlay.classList.add('active');
    document.body.style.overflow = 'hidden';
    console.log('Generic modal opened, body overflow set to hidden');
}

// Close booking modal
function closeBookingModal() {
    document.getElementById('bookingModalOverlay').classList.remove('active');
    document.body.style.overflow = '';
    console.log('Generic modal closed, body overflow cleared');
}

// Close if clicking outside
function closeBookingModalIfOutside(event) {
    if (event.target.id === 'bookingModalOverlay') {
        closeBookingModal();
    }
}

// Update total price
function updateTotalPrice() {
    const qtyInput = document.getElementById('bookingQty');
    const totalPriceEl = document.getElementById('bookingTotalPrice');
    const priceDetailEl = document.getElementById('bookingPriceDetail');
    
    if (!qtyInput || !totalPriceEl || !priceDetailEl) {
        console.warn('Price update elements not found');
        return;
    }
    
    const qty = parseInt(qtyInput.value) || 0;
    const price = currentBookingData.harga;
    
    // Validate qty range to prevent overflow
    if (qty < 0) {
        qtyInput.value = 1;
        return;
    }
    if (qty > 1000) {
        qtyInput.value = 1000;
        alert('Jumlah maksimal 1000 orang. Untuk pesanan lebih besar, silakan hubungi kami.');
        return;
    }
    
    const total = qty * price;
    
    // Check for overflow (JavaScript safe integer)
    if (total > Number.MAX_SAFE_INTEGER) {
        console.error('Price calculation overflow');
        totalPriceEl.textContent = 'Hubungi Kami';
        priceDetailEl.textContent = 'Jumlah terlalu besar';
        return;
    }
    
    totalPriceEl.textContent = formatRupiah(total);
    priceDetailEl.textContent = qty > 0 ? `${qty} orang × ${formatRupiah(price)}` : '';
}

// Format rupiah
function formatRupiah(angka) {
    if (!angka || angka == 0) return 'Hubungi Kami';
    return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Clear form errors
function clearFormErrors() {
    document.querySelectorAll('.booking-form-error').forEach(el => el.textContent = '');
    document.querySelectorAll('.booking-form-input').forEach(el => el.classList.remove('error'));
    document.getElementById('bookingErrorMessage').style.display = 'none';
}

// Submit booking form
async function submitBookingForm(event) {
    event.preventDefault();
    
    // Clear previous errors
    clearFormErrors();
    
    // Get loading manager if available
    const loadingManager = window.bookingLoadingManager;
    const loadingContainer = document.getElementById('loadingContainer');
    const fallbackLoading = document.getElementById('bookingLoading');
    
    // Show loading (use LoadingManager if available, fallback otherwise)
    if (loadingManager && loadingContainer) {
        loadingContainer.style.display = 'block';
        loadingManager.startLoading('availability');
    } else {
        fallbackLoading.style.display = 'block';
    }
    
    document.getElementById('bookingSubmitBtn').disabled = true;
    
    // Get form data
    const formData = {
        paket_wisata_id: document.getElementById('bookingPackageId').value,
        nama_lengkap: document.getElementById('bookingNama').value,
        email: document.getElementById('bookingEmail').value,
        no_hp: document.getElementById('bookingPhone').value,
        tanggal_kunjungan: document.getElementById('bookingDate').value,
        jumlah_orang: parseInt(document.getElementById('bookingQty').value)
    };
    
    console.log('Submitting booking:', formData);
    
    try {
        // Stage 1: Check availability
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken || !csrfToken.content) {
            throw new Error('CSRF token not found. Please refresh the page.');
        }
        
        const response = await fetch('/api/booking/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        console.log('Response:', data);
        
        if (response.ok && data.success) {
            // Stage 2: Booking created
            if (loadingManager) {
                loadingManager.nextStage('booking');
                await new Promise(resolve => setTimeout(resolve, 500));
            }
            
            // Stage 3: Payment
            if (loadingManager) {
                loadingManager.nextStage('payment');
            }
            
            // Check if Midtrans Snap is available and snap_token exists
            const isSimulationToken = typeof data.data?.snap_token === 'string' && data.data.snap_token.startsWith('SIMULATION-');
            if (typeof window.snap !== 'undefined' && data.data.snap_token && !isSimulationToken) {
                // Open Midtrans payment
                window.snap.pay(data.data.snap_token, {
                    onSuccess: async function(result) {
                        if (loadingManager) {
                            loadingManager.showSuccess('Pembayaran berhasil!');
                        }

                        // Confirm the successful Sandbox transaction server-side before redirecting.
                        try {
                            await fetch(`/midtrans/check-payment/${encodeURIComponent(data.data.order_id)}`);
                        } catch (statusError) {
                            console.error('Payment status check failed:', statusError);
                        }
                        
                        setTimeout(() => {
                            window.location.href = `/booking/confirmation/${data.data.kode_booking}?from_payment=1`;
                        }, 1500);
                    },
                    onPending: function(result) {
                        if (loadingManager) {
                            loadingManager.showToast('Menunggu pembayaran...', 'info');
                        }
                    },
                    onError: function(result) {
                        if (loadingManager) {
                            loadingManager.showError('Pembayaran gagal', 'Silakan coba lagi');
                        } else {
                            document.getElementById('bookingErrorMessage').textContent = 'Pembayaran gagal. Silakan coba lagi.';
                            document.getElementById('bookingErrorMessage').style.display = 'block';
                        }
                    },
                    onClose: function() {
                        if (loadingManager) {
                            loadingManager.stopLoading();
                        }
                        if (loadingContainer) loadingContainer.style.display = 'none';
                        fallbackLoading.style.display = 'none';
                        document.getElementById('bookingSubmitBtn').disabled = false;
                        // Ensure body overflow is cleared when payment modal is closed
                        document.body.style.overflow = '';
                        console.log('Payment modal closed, body overflow cleared');
                    }
                });
            } else {
                const isSimulationToken = typeof data.data?.snap_token === 'string' && data.data.snap_token.startsWith('SIMULATION-');
                if (isSimulationToken) {
                    window.location.href = `/booking/confirmation/${data.data.kode_booking}?from_payment=1`;
                    return;
                }

                if (loadingManager) {
                    loadingManager.showError('Pembayaran tidak tersedia.', 'Silakan refresh halaman dan coba lagi');
                } else {
                    const paymentError = document.getElementById('bookingErrorMessage');
                    paymentError.textContent = 'Pembayaran tidak tersedia. Silakan refresh halaman dan coba lagi.';
                    paymentError.style.display = 'block';
                }
                document.getElementById('bookingSubmitBtn').disabled = false;
            }
        } else {
            // Show error
            const errorMsg = data.message || 'Terjadi kesalahan. Silakan coba lagi.';
            const validationErrors = data.errors ? Object.values(data.errors).flat().join(' ') : '';
            const detailedError = validationErrors || errorMsg;
            
            if (loadingManager) {
                loadingManager.showError(detailedError, 'Silakan coba lagi');
            } else {
                document.getElementById('bookingErrorMessage').textContent = detailedError;
                document.getElementById('bookingErrorMessage').style.display = 'block';
            }
            document.getElementById('bookingSubmitBtn').disabled = false;
        }
    } catch (error) {
        console.error('Error:', error);
        
        // Distinguish between network errors and application errors
        let errorMsg = 'Koneksi gagal. Silakan cek koneksi internet Anda.';
        if (error.message) {
            if (error.message.includes('CSRF')) {
                errorMsg = 'Sesi Anda telah berakhir. Silakan refresh halaman.';
            } else if (error.message.includes('Failed to fetch')) {
                errorMsg = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
            } else if (error.message.includes('NetworkError')) {
                errorMsg = 'Terjadi kesalahan jaringan. Silakan coba lagi.';
            }
        }
        
        if (loadingManager) {
            loadingManager.showError(errorMsg, 'Coba lagi');
        } else {
            document.getElementById('bookingErrorMessage').textContent = errorMsg;
            document.getElementById('bookingErrorMessage').style.display = 'block';
        }
        document.getElementById('bookingSubmitBtn').disabled = false;
    } finally {
        // Cleanup if error occurred before payment
        if (!loadingManager || !window.snap) {
            if (loadingContainer) loadingContainer.style.display = 'none';
            fallbackLoading.style.display = 'none';
            document.getElementById('bookingSubmitBtn').disabled = false;
        }
    }
}
</script>
