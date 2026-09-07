@props([
    'text' => 'Siap merencanakan kunjungan sekolah?',
    'buttonLabel' => 'Hubungi via WhatsApp',
    'whatsappMessage' => 'Halo, saya ingin merencanakan kunjungan sekolah ke Godong Ijo untuk program wisata edukasi',
])

<section class="education-footer-cta" aria-label="Reservasi wisata edukasi">
    <div class="container">
        <div class="education-footer-cta-inner">
            <p>{{ $text }}</p>
            <x-education.whatsapp-button
                :message="$whatsappMessage"
                :label="$buttonLabel"
                variant="gold"
            />
        </div>
    </div>
</section>
