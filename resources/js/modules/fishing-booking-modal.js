function fishingBookingModal() {
  return {
    isOpen: false,
    loading: false,
    step: 1,
    formData: {
      nama_lengkap: '',
      email: '',
      no_hp: '',
      tanggal_kunjungan: '',
      jam_kunjungan: '',
      jenis_pemancingan: 'tarikan',
      durasi: '',
      tambahan_jam: 0,
      jumlah_joran: 1,
      ukuran_joran: '',
      perlu_sewa_alat: false,
      qty_komet: 0,
      qty_umpan_jadi: 0,
      setuju_aturan: false
    },
    errors: {},
    estimatedPrice: 0,
    priceDisplay: 'Rp 0',

    init() {
      this.calculatePrice();
    },

    openModal() {
      this.isOpen = true;
      document.body.style.overflow = 'hidden';
      this.resetForm();
      this.calculatePrice();
    },

    closeModal() {
      this.isOpen = false;
      document.body.style.overflow = '';
      this.resetForm();
    },

    resetForm() {
      this.formData = {
        nama_lengkap: '',
        email: '',
        no_hp: '',
        tanggal_kunjungan: '',
        jam_kunjungan: '',
        jenis_pemancingan: 'tarikan',
        durasi: '',
        tambahan_jam: 0,
        jumlah_joran: 1,
        ukuran_joran: '',
        perlu_sewa_alat: false,
        qty_komet: 0,
        qty_umpan_jadi: 0,
        setuju_aturan: false
      };
      this.errors = {};
      this.step = 1;
      this.estimatedPrice = 0;
      this.priceDisplay = 'Rp 0';
    },

    nextStep() {
      this.errors = {};
      if (this.step === 1) {
        if (!this.formData.nama_lengkap || this.formData.nama_lengkap.trim().length < 3) this.errors.nama_lengkap = 'Masukkan nama lengkap Anda';
        if (!this.formData.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.formData.email)) this.errors.email = 'Masukkan email yang valid';
        if (!this.formData.no_hp || this.formData.no_hp.trim().length < 10) this.errors.no_hp = 'Masukkan nomor WhatsApp yang aktif';
        if (!this.formData.tanggal_kunjungan) this.errors.tanggal_kunjungan = 'Pilih tanggal kunjungan';
        if (!this.formData.jam_kunjungan) this.errors.jam_kunjungan = 'Pilih jam kunjungan';
      }
      if (this.step === 2) {
        if (!this.formData.jenis_pemancingan) this.errors.jenis_pemancingan = 'Pilih jenis pemancingan';
        if (this.formData.jenis_pemancingan === 'tarikan' && !this.formData.durasi) this.errors.durasi = 'Pilih durasi memancing';
        if ((this.formData.jenis_pemancingan === 'sewa_joran' || this.formData.perlu_sewa_alat) && !this.formData.ukuran_joran) this.errors.ukuran_joran = 'Pilih ukuran joran';
      }
      if (Object.keys(this.errors).length) {
        const field = document.querySelector(`[x-model="formData.${Object.keys(this.errors)[0]}"]`);
        if (field) { field.scrollIntoView({ behavior: 'smooth', block: 'center' }); field.focus(); }
        return;
      }
      this.step += 1;
      this.scrollToTop();
    },

    previousStep() {
      this.errors = {};
      this.step = Math.max(1, this.step - 1);
      this.scrollToTop();
    },

    scrollToTop() {
      this.$nextTick(() => this.$el.querySelector('.fishing-modal-body')?.scrollTo({ top: 0, behavior: 'smooth' }));
    },

    calculatePrice() {
      const jumlahJoran = Math.max(1, Number(this.formData.jumlah_joran || 1));
      const umpanTotal = (Number(this.formData.qty_komet) * 11000) + (Number(this.formData.qty_umpan_jadi) * 11000);
      let sewaTotal = 0;
      if (this.formData.jenis_pemancingan === 'sewa_joran' || this.formData.perlu_sewa_alat) {
        const hargaUkuran = this.formData.ukuran_joran === 'standar' ? 20000 : this.formData.ukuran_joran === 'besar' ? 50000 : 0;
        sewaTotal = hargaUkuran * jumlahJoran;
      }
      let mancingTotal = 0;
      if (this.formData.jenis_pemancingan === 'tarikan') {
        const hargaDurasi = this.formData.durasi === '2' ? 80000 : this.formData.durasi === '4' ? 110000 : 0;
        mancingTotal = (hargaDurasi + (Number(this.formData.tambahan_jam) * 40000)) * jumlahJoran;
      } else if (this.formData.jenis_pemancingan === 'jackpot') {
        mancingTotal = 210000 * jumlahJoran;
      }
      this.estimatedPrice = mancingTotal + sewaTotal + umpanTotal;
      this.priceDisplay = this.formData.jenis_pemancingan === 'kiloan'
        ? (sewaTotal + umpanTotal ? `Dihitung saat ditimbang (${this.formatRupiah(sewaTotal + umpanTotal)} tambahan)` : 'Dihitung saat ditimbang')
        : this.formatRupiah(this.estimatedPrice);
    },

    formatRupiah(amount) {
      return 'Rp ' + Number(amount).toLocaleString('id-ID');
    },

    validateJamKunjungan() {
      this.clearError('jam_kunjungan');
      if (this.formData.jam_kunjungan) {
        const [hour, minute] = this.formData.jam_kunjungan.split(':').map(Number);
        if (hour < 9 || hour > 21 || (hour === 21 && minute > 0)) this.errors.jam_kunjungan = 'Jam kunjungan harus antara 09:00 - 21:00 WIB';
      }
    },

    clearError(field) {
      delete this.errors[field];
    },

    async submitBooking() {
      this.errors = {};
      if (!this.formData.nama_lengkap || this.formData.nama_lengkap.trim() === '') this.errors.nama_lengkap = 'Nama wajib diisi';
      if (!this.formData.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.formData.email)) this.errors.email = 'Email yang valid wajib diisi';
      if (!this.formData.no_hp || this.formData.no_hp.trim() === '') this.errors.no_hp = 'No. WhatsApp wajib diisi';
      if (!this.formData.tanggal_kunjungan) this.errors.tanggal_kunjungan = 'Tanggal wajib diisi';
      if (!this.formData.jam_kunjungan) this.errors.jam_kunjungan = 'Jam wajib diisi';
      if (!this.formData.setuju_aturan) this.errors.setuju_aturan = 'Anda harus menyetujui aturan pemancingan';
      if (Object.keys(this.errors).length) return;

      this.loading = true;
      try {
        const response = await fetch('/booking/fishing', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({ ...this.formData, estimasi_total: this.formData.jenis_pemancingan === 'kiloan' ? null : this.estimatedPrice })
        });
        const data = await response.json();
        if (!response.ok) {
          this.errors = Object.fromEntries(Object.entries(data.errors || {}).map(([key, messages]) => [key, Array.isArray(messages) ? messages[0] : messages]));
          return;
        }
        this.closeModal();
        if (typeof window.snap !== 'undefined' && data.snap_token) {
          window.snap.pay(data.snap_token, {
            onSuccess: async () => {
              try { await fetch(`/midtrans/check-payment/${encodeURIComponent(data.order_id)}`); } catch (error) { console.error('Payment status check failed:', error); }
              window.location.href = data.redirect_url + '?from_payment=1';
            },
            onPending: () => alert('Pembayaran masih menunggu konfirmasi Midtrans.'),
            onError: () => alert('Pembayaran gagal. Silakan coba lagi atau hubungi kami.'),
          });
        } else {
          window.location.href = data.redirect_url;
        }
      } catch (error) {
        console.error(error);
        alert('Koneksi gagal. Periksa internet Anda dan coba lagi.');
      } finally {
        this.loading = false;
      }
    }
  };
}

window.fishingBookingModal = fishingBookingModal;
