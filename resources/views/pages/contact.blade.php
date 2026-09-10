@extends('layouts.page')

@section('content')
<div class="contact-page">
    {{-- Hero Section --}}
    <section class="contact-hero">
        <div class="contact-hero-media" aria-hidden="true">
            <img src="{{ asset('images/placeholders/asset 1.jpg') }}" alt="" fetchpriority="high">
        </div>
        <div class="container">
            <div class="contact-hero-content scroll-reveal">
                <span class="contact-hero-kicker">Godong Ijo · Depok</span>
                <h1 class="contact-hero-title">Hubungi Kami</h1>
                <p class="contact-hero-subtitle">
                    Punya pertanyaan atau ingin merencanakan kunjungan? Tim kami siap membantu Anda
                </p>
            </div>
        </div>
    </section>

    {{-- Contact Information & Form --}}
    <section class="contact-main-section">
        <div class="container">
            <div class="contact-grid">
                {{-- Contact Information --}}
                <div class="contact-info">
                    <div class="contact-info-heading scroll-reveal">
                        <span class="contact-info-kicker">Datang dan terhubung</span>
                        <h2 class="contact-info-title">Informasi Kontak</h2>
                        <p class="contact-info-intro">Butuh bantuan sebelum berkunjung? Kami siap membantu merencanakan pengalaman Anda di Godong Ijo.</p>
                    </div>
                    
                    {{-- Operating Hours --}}
                    <div class="contact-card scroll-reveal scroll-reveal-delay-1">
                        <div class="contact-card-icon">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div class="contact-card-content">
                            <h3 class="contact-card-title">Jam Operasional</h3>
                            <div class="contact-hours-list">
                                <div class="contact-hours-row">
                                    <span>Senin - Jumat</span>
                                    <strong>08:00 - 17:00</strong>
                                </div>
                                <div class="contact-hours-row">
                                    <span>Sabtu - Minggu</span>
                                    <strong>07:00 - 18:00</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Phone --}}
                    <div class="contact-card scroll-reveal scroll-reveal-delay-2">
                        <div class="contact-card-icon">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"></path>
                            </svg>
                        </div>
                        <div class="contact-card-content">
                            <h3 class="contact-card-title">Telepon</h3>
                            <a href="tel:+6281234567890" class="contact-card-link">+62 812-3456-7890</a>
                            <span class="contact-card-action">Hubungi tim kami <span aria-hidden="true">↗</span></span>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="contact-card scroll-reveal scroll-reveal-delay-3">
                        <div class="contact-card-icon">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                        </div>
                        <div class="contact-card-content">
                            <h3 class="contact-card-title">Email</h3>
                            <a href="mailto:info@godongijo.com" class="contact-card-link">info@godongijo.com</a>
                            <span class="contact-card-action">Kirim pertanyaan detail <span aria-hidden="true">↗</span></span>
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="contact-card scroll-reveal scroll-reveal-delay-4">
                        <div class="contact-card-icon">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                        </div>
                        <div class="contact-card-content">
                            <h3 class="contact-card-title">Alamat</h3>
                            <p class="contact-card-text">Jl. Wisata Alam No. 123</p>
                            <p class="contact-card-text">Bogor, Jawa Barat 16610</p>
                            <span class="contact-card-action">Lihat lokasi kami <span aria-hidden="true">↗</span></span>
                        </div>
                    </div>

                    {{-- Social Media --}}
                    <div class="contact-socials scroll-reveal scroll-reveal-delay-5">
                        <h3 class="contact-socials-title">Ikuti Kami</h3>
                        <div class="contact-socials-links">
                            <a href="https://www.instagram.com/godongijo_official?stkn=d2IxZzVrMmZvcXVr" class="social-link" aria-label="Instagram" target="_blank" rel="noopener">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
                                </svg>
                            </a>
                            <a href="https://www.tiktok.com/@godongijo_official" class="social-link" aria-label="TikTok" target="_blank" rel="noopener">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14 3c.3 2.7 2.2 4.6 4.8 4.8v2.8a7.2 7.2 0 0 1-4.8-1.7v5.2c0 3.4-2.9 6.2-6.3 6.2s-6.3-2.8-6.3-6.2 2.8-6.2 6.3-6.2c.4 0 .8.1 1.2.2v2.8c-.4-.1-.8-.2-1.2-.2a3.7 3.7 0 0 0 0 7.4 3.7 3.7 0 0 0 3.7-3.7V3h-2.8z"/>
                                </svg>
                            </a>
                            <a href="#" class="social-link" aria-label="WhatsApp">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Contact Form --}}
                <div class="contact-form-wrapper scroll-reveal scroll-reveal-delay-2">
                    <h2 class="contact-form-title">Kirim Pesan</h2>
                    <p class="contact-form-subtitle">Isi formulir di bawah ini dan kami akan segera menghubungi Anda</p>
                    
                    <form class="contact-form" id="contactForm">
                        <div class="form-group">
                            <label for="contactName" class="form-label">Nama Lengkap</label>
                            <input 
                                type="text" 
                                id="contactName" 
                                name="name" 
                                class="form-input" 
                                placeholder="Masukkan nama Anda"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="contactEmail" class="form-label">Email</label>
                            <input 
                                type="email" 
                                id="contactEmail" 
                                name="email" 
                                class="form-input" 
                                placeholder="nama@email.com"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="contactPhone" class="form-label">Nomor Telepon</label>
                            <input 
                                type="tel" 
                                id="contactPhone" 
                                name="phone" 
                                class="form-input" 
                                placeholder="+62 812-3456-7890"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="contactSubject" class="form-label">Subjek</label>
                            <input 
                                type="text" 
                                id="contactSubject" 
                                name="subject" 
                                class="form-input" 
                                placeholder="Tentang apa pesan Anda?"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="contactMessage" class="form-label">Pesan</label>
                            <textarea 
                                id="contactMessage" 
                                name="message" 
                                class="form-textarea" 
                                rows="6" 
                                placeholder="Tulis pesan Anda di sini..."
                                required
                            ></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-full">
                            Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- Location Map --}}
    <section class="contact-map-section">
        <div class="container">
            <div class="contact-map-header scroll-reveal">
                <h2 class="section-title">Lokasi Kami</h2>
                <p class="section-subtitle">Kunjungi kami dan rasakan pengalaman wisata alam yang tak terlupakan</p>
            </div>
            
            <div class="contact-map scroll-reveal scroll-reveal-delay-1">
                {{-- Google Maps Embed - Replace with actual coordinates --}}
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.0!2d106.73!3d-6.398!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69e99999999999%3A0x9999999999999999!2sJl.%20Cinangka%20Raya%20No.km%2010%2C%20Serua%2C%20Kec.%20Bojongsari%2C%20Kota%20Depok%2C%20Jawa%20Barat%2016517!5e0!3m2!1sen!2sid!4v1620000000001!5m2!1sen!2sid"
                    width="100%" 
                    height="450" 
                    style="border:0; border-radius: 16px;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Lokasi Godong Ijo - Jl. Cinangka Raya No.km 10, Serua, Bojongsari, Depok"
                ></iframe>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
// Simple contact form handling
document.getElementById('contactForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Show success message (in real implementation, send to backend)
    alert('Terima kasih! Pesan Anda telah terkirim. Kami akan segera menghubungi Anda.');
    this.reset();
});
</script>
@endpush
