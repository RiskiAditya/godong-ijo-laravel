@extends('layouts.app')

@section('content')
<div class="error-page-404">
    <div class="container">
        <div class="error-content">
            {{-- Error Illustration --}}
            <div class="error-illustration">
                <svg class="error-icon" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Leaf Icon -->
                    <path d="M100 40C120 40 140 50 150 70C160 90 160 110 150 130C140 150 120 160 100 160C80 160 60 150 50 130C40 110 40 90 50 70C60 50 80 40 100 40Z" fill="var(--mint)" opacity="0.3"/>
                    <path d="M100 60C90 60 80 65 75 75C70 85 70 95 75 105C80 115 90 120 100 120C110 120 120 115 125 105C130 95 130 85 125 75C120 65 110 60 100 60Z" fill="var(--forest)"/>
                    <path d="M100 80L100 140" stroke="var(--mint)" stroke-width="4" stroke-linecap="round"/>
                    <path d="M85 100C85 100 95 90 100 80" stroke="var(--mint)" stroke-width="2" stroke-linecap="round"/>
                    <path d="M115 100C115 100 105 90 100 80" stroke="var(--mint)" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <div class="error-code">404</div>
            </div>

            {{-- Error Message --}}
            <div class="error-message-section">
                <h1 class="error-title">Halaman Tidak Ditemukan</h1>
                <p class="error-description">
                    Maaf, halaman yang Anda cari tidak ditemukan. Halaman mungkin telah dipindahkan atau URL yang Anda masukkan salah.
                </p>
                
                {{-- Quick Actions --}}
                <div class="error-actions">
                    <a href="{{ route('landing') }}" class="btn btn-primary btn-lg">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        Kembali ke Beranda
                    </a>
                    
                    <button onclick="history.back()" class="btn btn-secondary btn-lg">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        Kembali
                    </button>
                </div>

                {{-- Quick Links --}}
                <div class="error-quick-links">
                    <h3 class="quick-links-title">Atau kunjungi halaman lainnya:</h3>
                    <div class="quick-links-grid">
                        <a href="{{ route('destination.show', ['slug' => 'the-waterfall']) }}" class="quick-link-card">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <span>Destinasi</span>
                        </a>

                        <a href="{{ route('package.show', ['slug' => 'paket-kuliner-keluarga']) }}" class="quick-link-card">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="8" width="18" height="4" rx="1"></rect>
                                <path d="M12 8v13"></path>
                                <path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"></path>
                                <path d="M7.5 8a2.5 2.5 0 0 1 0-5A4.8 8 0 0 1 12 8a4.8 8 0 0 1 4.5-5 2.5 2.5 0 0 1 0 5"></path>
                            </svg>
                            <span>Paket Wisata</span>
                        </a>

                        <a href="{{ route('education') }}" class="quick-link-card">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                            </svg>
                            <span>Wisata Edukasi</span>
                        </a>

                        <a href="{{ route('contact') }}" class="quick-link-card">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                            <span>Kontak</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.error-page-404 {
    min-height: calc(100vh - 72px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 80px 24px 60px;
    margin-top: 72px;
    background: linear-gradient(180deg, #f8faf8 0%, #FFFFFF 100%);
}

.error-content {
    max-width: 800px;
    text-align: center;
}

.error-illustration {
    margin-bottom: 40px;
    position: relative;
}

.error-icon {
    width: 180px;
    height: 180px;
    margin: 0 auto 24px;
    animation: floatLeaf 3s ease-in-out infinite;
}

.error-code {
    font-family: 'Fraunces', Georgia, serif;
    font-size: clamp(4rem, 12vw, 8rem);
    font-weight: 800;
    color: var(--forest);
    line-height: 1;
    opacity: 0.1;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: -1;
}

.error-message-section {
    margin-bottom: 48px;
}

.error-title {
    font-family: 'Fraunces', Georgia, serif;
    font-size: clamp(2rem, 4vw, 3rem);
    color: var(--forest);
    margin-bottom: 16px;
    font-weight: 700;
}

.error-description {
    font-size: 16px;
    color: #6B6B6B;
    line-height: 1.6;
    max-width: 500px;
    margin: 0 auto 32px;
}

.error-actions {
    display: flex;
    gap: 16px;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 48px;
}

.error-actions .btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.error-quick-links {
    padding-top: 48px;
    border-top: 2px solid #E5E5E5;
}

.quick-links-title {
    font-size: 18px;
    color: var(--forest);
    margin-bottom: 24px;
    font-weight: 600;
}

.quick-links-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 16px;
    max-width: 600px;
    margin: 0 auto;
}

.quick-link-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    padding: 24px 16px;
    background-color: #FFFFFF;
    border: 2px solid #E5E5E5;
    border-radius: 16px;
    color: var(--forest);
    text-decoration: none;
    transition: all 0.3s ease;
}

.quick-link-card:hover {
    border-color: var(--mint);
    background-color: var(--mint);
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

.quick-link-card svg {
    color: var(--forest);
}

.quick-link-card span {
    font-size: 14px;
    font-weight: 600;
}

@keyframes floatLeaf {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
    }
    50% {
        transform: translateY(-10px) rotate(5deg);
    }
}

@media (max-width: 768px) {
    .error-page-404 {
        padding: 60px 20px 40px;
    }

    .error-icon {
        width: 140px;
        height: 140px;
    }

    .error-actions {
        flex-direction: column;
        align-items: stretch;
    }

    .error-actions .btn {
        width: 100%;
        justify-content: center;
    }

    .quick-links-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
@endsection
