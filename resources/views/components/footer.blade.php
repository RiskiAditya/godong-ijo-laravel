<footer class="footer" id="contact">
    <div class="container">
        <div class="footer-grid">
            {{-- Brand Column --}}
            <div class="footer-brand">
                <div class="footer-logo">
                    <img src="{{ asset('images/placeholders/The-Waterfall-Logo-removebg-preview.png') }}" alt="Godong Ijo Logo" style="height: 40px; width: auto; border-radius: 8px; object-fit: contain;">
                </div>
                <p class="footer-description">
                    Destinasi kuliner & rekreasi paling ekologis di Indonesia. 
                    The Waterfall Resto, Monster Fish Fishing Lake, Private Room, dan Mini Zoo 
                    dalam satu lokasi di Depok, Jawa Barat.
                </p>
                <div class="footer-social">
                    <a href="https://www.instagram.com/godongijo_official?stkn=d2IxZzVrMmZvcXVr" aria-label="Instagram" title="Instagram" target="_blank" rel="noopener">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                    <a href="https://www.tiktok.com/@godongijo_official" aria-label="TikTok" title="TikTok" target="_blank" rel="noopener">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M14 3c.3 2.7 2.2 4.6 4.8 4.8v2.8a7.2 7.2 0 0 1-4.8-1.7v5.2c0 3.4-2.9 6.2-6.3 6.2s-6.3-2.8-6.3-6.2 2.8-6.2 6.3-6.2c.4 0 .8.1 1.2.2v2.8c-.4-.1-.8-.2-1.2-.2a3.7 3.7 0 0 0 0 7.4 3.7 3.7 0 0 0 3.7-3.7V3h-2.8z"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="footer-links">
                <h4 class="footer-heading">Menu Utama</h4>
                <ul>
                    <li><a href="#hero">Beranda</a></li>
                    <li><a href="#destinations">Destinasi</a></li>
                    <li><a href="#packages">Paket</a></li>
                    <li><a href="#contact">Kontak</a></li>
                </ul>
            </div>

            {{-- Facilities --}}
            <div class="footer-links">
                <h4 class="footer-heading">Fasilitas</h4>
                <ul>
                    <li><a href="#destinations">The Waterfall Resto</a></li>
                    <li><a href="#destinations">Monster Fish Fishing</a></li>
                    <li><a href="#destinations">Private Room</a></li>
                    <li><a href="#destinations">Mini Zoo</a></li>
                    <li><a href="#contact">Musholla & Parkir</a></li>
                </ul>
            </div>

            {{-- Contact Info --}}
            <div class="footer-newsletter">
                <h4 class="footer-heading">Hubungi Kami</h4>
                <p>Jl. Cinangka Raya Km 10 No. 60<br>Serua, Bojongsari, Depok 16517</p>
                <div style="margin-top: 1rem;">
                    <p><strong>Telp:</strong> +62 21 7471 0678</p>
                    <p><strong>WA:</strong> {{ config('app.whatsapp.display') }}</p>
                    <p><strong>Email:</strong> info@godongijo.com</p>
                </div>
            </div>
        </div>

        {{-- Footer Bottom --}}
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Godong Ijo Eco-Tourism. All rights reserved.</p>
            <div class="footer-bottom-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Cookie Policy</a>
            </div>
        </div>
    </div>
</footer>
