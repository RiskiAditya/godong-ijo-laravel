<!-- Booking Modal Component -->
<div x-data="bookingModal()" 
     @booking-modal:open.window="openModal($event.detail)"
     x-init="console.log('Booking modal component initialized')"
     x-cloak>
    <!-- Modal Backdrop -->
    <div x-show="isOpen" 
         @click="closeModal()"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        
        <!-- Modal Content -->
        <div @click.stop
             x-show="isOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-green-700 to-green-800 text-white p-6 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold">Reservasi Booking</h2>
                        <p class="text-green-100 text-sm mt-1" x-text="selectedPaket?.nama"></p>
                    </div>
                    <button @click="closeModal()" 
                            class="text-white hover:bg-white/20 rounded-full p-2 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <form @submit.prevent="submitBooking" class="p-6 space-y-6">
                <!-- Paket Info (readonly) -->
                <div class="bg-green-50 border-l-4 border-green-600 p-4 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Paket yang dipilih:</p>
                            <p class="font-semibold text-gray-900" x-text="selectedPaket?.nama"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Harga:</p>
                            <p class="font-bold text-green-700" x-text="formatRupiah(selectedPaket?.harga)"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Form Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nama Lengkap -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               x-model="form.nama_lengkap"
                               @input="validateField('nama_lengkap')"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600"
                               :class="{'border-red-500': errors.nama_lengkap}"
                               placeholder="Masukkan nama lengkap"
                               required>
                        <p x-show="errors.nama_lengkap" 
                           x-text="errors.nama_lengkap" 
                           class="text-red-500 text-sm mt-1"></p>
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               x-model="form.email"
                               @input="validateField('email')"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600"
                               :class="{'border-red-500': errors.email}"
                               placeholder="contoh@email.com"
                               required>
                        <p x-show="errors.email" 
                           x-text="errors.email" 
                           class="text-red-500 text-sm mt-1"></p>
                    </div>
                    
                    <!-- No. HP/WhatsApp -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            No. WhatsApp <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" 
                               x-model="form.no_hp"
                               @input="validateField('no_hp')"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600"
                               :class="{'border-red-500': errors.no_hp}"
                               placeholder="081234567890"
                               required>
                        <p x-show="errors.no_hp" 
                           x-text="errors.no_hp" 
                           class="text-red-500 text-sm mt-1"></p>
                    </div>
                    
                    <!-- Tanggal Kunjungan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Kunjungan <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               x-model="form.tanggal_kunjungan"
                               @input="validateField('tanggal_kunjungan')"
                               :min="minDate"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600"
                               :class="{'border-red-500': errors.tanggal_kunjungan}"
                               required>
                        <p x-show="errors.tanggal_kunjungan" 
                           x-text="errors.tanggal_kunjungan" 
                           class="text-red-500 text-sm mt-1"></p>
                    </div>
                    
                    <!-- Jumlah Orang -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Jumlah Orang <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               x-model.number="form.jumlah_orang"
                               @input="calculateTotal(); validateField('jumlah_orang')"
                               min="1"
                               max="100"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600"
                               :class="{'border-red-500': errors.jumlah_orang}"
                               placeholder="1"
                               required>
                        <p x-show="errors.jumlah_orang" 
                           x-text="errors.jumlah_orang" 
                           class="text-red-500 text-sm mt-1"></p>
                    </div>
                </div>
                
                <!-- Total Harga -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700 font-medium">Total Harga:</span>
                        <div class="text-right">
                            <p class="text-3xl font-bold text-green-700" x-text="formatRupiah(totalHarga)"></p>
                            <p class="text-sm text-gray-500" x-show="form.jumlah_orang > 0">
                                <span x-text="form.jumlah_orang"></span> orang × 
                                <span x-text="formatRupiah(selectedPaket?.harga)"></span>
                            </p>
                            <p x-show="selectedPaket?.harga == 0" class="text-sm text-gray-600 italic">
                                Hubungi kami untuk harga
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Loading State -->
                <div x-show="loading" class="text-center py-4">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-green-600 border-t-transparent"></div>
                    <p class="text-gray-600 mt-2" x-text="loadingMessage"></p>
                </div>
                
                <!-- Error Message -->
                <div x-show="errorMessage" 
                     class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <p x-text="errorMessage"></p>
                </div>
                
                <!-- Submit Button -->
                <div class="flex gap-3">
                    <button type="submit"
                            :disabled="loading || !isFormValid"
                            :class="loading || !isFormValid ? 'opacity-50 cursor-not-allowed' : 'hover:from-green-800 hover:to-green-900'"
                            class="flex-1 bg-gradient-to-r from-green-700 to-green-800 text-white font-bold py-4 px-6 rounded-lg transition duration-300 shadow-lg">
                        <span x-show="!loading">🎫 Lanjut ke Pembayaran</span>
                        <span x-show="loading">Memproses...</span>
                    </button>
                    <button type="button"
                            @click="closeModal()"
                            :disabled="loading"
                            class="px-6 py-4 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function bookingModal() {
    return {
        isOpen: false,
        loading: false,
        loadingMessage: 'Memproses pemesanan...',
        errorMessage: '',
        selectedPaket: null,
        minDate: new Date().toISOString().split('T')[0],
        
        form: {
            paket_wisata_id: null,
            nama_lengkap: '',
            email: '',
            no_hp: '',
            tanggal_kunjungan: '',
            jumlah_orang: 1
        },
        
        errors: {},
        totalHarga: 0,
        
        get isFormValid() {
            return this.form.nama_lengkap.length >= 3 &&
                   this.form.email.includes('@') &&
                   this.form.no_hp.length >= 10 &&
                   this.form.tanggal_kunjungan &&
                   this.form.jumlah_orang >= 1;
        },
        
        openModal(paket) {
            this.selectedPaket = paket;
            this.form.paket_wisata_id = paket.id;
            this.calculateTotal();
            this.isOpen = true;
            document.body.style.overflow = 'hidden';
        },
        
        closeModal() {
            this.isOpen = false;
            this.resetForm();
            document.body.style.overflow = '';
        },
        
        resetForm() {
            this.form = {
                paket_wisata_id: null,
                nama_lengkap: '',
                email: '',
                no_hp: '',
                tanggal_kunjungan: '',
                jumlah_orang: 1
            };
            this.errors = {};
            this.errorMessage = '';
            this.totalHarga = 0;
        },
        
        calculateTotal() {
            if (this.selectedPaket && this.form.jumlah_orang > 0) {
                this.totalHarga = this.selectedPaket.harga * this.form.jumlah_orang;
            } else {
                this.totalHarga = 0;
            }
        },
        
        validateField(field) {
            switch(field) {
                case 'nama_lengkap':
                    if (this.form.nama_lengkap.length < 3) {
                        this.errors.nama_lengkap = 'Nama minimal 3 karakter';
                    } else {
                        delete this.errors.nama_lengkap;
                    }
                    break;
                case 'email':
                    if (!this.form.email.includes('@')) {
                        this.errors.email = 'Format email tidak valid';
                    } else {
                        delete this.errors.email;
                    }
                    break;
                case 'no_hp':
                    const phoneRegex = /^[0-9]{10,15}$/;
                    if (!phoneRegex.test(this.form.no_hp.replace(/[^0-9]/g, ''))) {
                        this.errors.no_hp = 'No. HP harus 10-15 digit angka';
                    } else {
                        delete this.errors.no_hp;
                    }
                    break;
                case 'tanggal_kunjungan':
                    if (new Date(this.form.tanggal_kunjungan) < new Date(this.minDate)) {
                        this.errors.tanggal_kunjungan = 'Tanggal tidak boleh di masa lalu';
                    } else {
                        delete this.errors.tanggal_kunjungan;
                    }
                    break;
                case 'jumlah_orang':
                    if (this.form.jumlah_orang < 1 || this.form.jumlah_orang > 100) {
                        this.errors.jumlah_orang = 'Jumlah orang harus antara 1-100';
                    } else {
                        delete this.errors.jumlah_orang;
                    }
                    break;
            }
        },
        
        formatRupiah(angka) {
            if (!angka || angka == 0) return 'Hubungi Kami';
            return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        },
        
        async submitBooking() {
            this.loading = true;
            this.errorMessage = '';
            this.loadingMessage = 'Memproses pemesanan...';
            
            try {
                const response = await fetch('/api/booking/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    // Success - trigger Midtrans payment
                    this.loadingMessage = 'Membuka halaman pembayaran...';
                    
                    // Check if Midtrans Snap is available
                    if (typeof window.snap === 'undefined') {
                        this.errorMessage = 'Payment gateway tidak tersedia. Silakan refresh halaman.';
                        this.loading = false;
                        return;
                    }
                    
                    // Open Midtrans Snap popup
                    window.snap.pay(data.data.snap_token, {
                        onSuccess: (result) => {
                            console.log('Payment success:', result);
                            this.loading = false;
                            this.closeModal();
                            
                            // Show success message
                            alert('✅ Pembayaran berhasil!\n\nKode Booking: ' + data.data.kode_booking + '\n\nAnda akan diarahkan ke halaman konfirmasi...');
                            
                            // Force redirect to confirmation page
                            window.location.replace(`/booking/confirmation/${data.data.kode_booking}?from_payment=1`);
                        },
                        onPending: (result) => {
                            console.log('Payment pending:', result);
                            this.loading = false;
                            this.closeModal();
                            
                            // Show pending message
                            alert('⏳ Pembayaran sedang diproses.\n\nKode Booking: ' + data.data.kode_booking + '\n\nAnda akan diarahkan ke halaman konfirmasi...');
                            
                            // Force redirect to confirmation page
                            window.location.replace(`/booking/confirmation/${data.data.kode_booking}?from_payment=1`);
                        },
                        onError: (result) => {
                            console.error('Payment error:', result);
                            this.errorMessage = 'Pembayaran gagal. Silakan coba lagi.';
                            this.loading = false;
                        },
                        onClose: () => {
                            console.log('Payment popup closed');
                            this.loading = false;
                        }
                    });
                } else {
                    this.errorMessage = data.message || 'Terjadi kesalahan. Silakan coba lagi.';
                    this.loading = false;
                }
            } catch (error) {
                this.errorMessage = 'Koneksi gagal. Silakan cek koneksi internet Anda.';
                console.error('Booking error:', error);
                this.loading = false;
            }
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
