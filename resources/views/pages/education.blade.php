@extends('layouts.app')

@section('content')
<div class="education-page">
    @if(isset($breadcrumbs) && count($breadcrumbs) > 0)
        <nav class="breadcrumb-nav" aria-label="Breadcrumb">
            <div class="breadcrumb-container">
                <ol class="breadcrumb-list">
                    @foreach($breadcrumbs as $breadcrumb)
                        <li class="breadcrumb-item">
                            @if($breadcrumb['current'])
                                <span class="breadcrumb-current" aria-current="page">{{ $breadcrumb['label'] }}</span>
                            @else
                                <a href="{{ $breadcrumb['url'] }}" class="breadcrumb-link">{{ $breadcrumb['label'] }}</a>
                            @endif

                            @if(!$loop->last)
                                <span class="breadcrumb-separator" aria-hidden="true">/</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>
        </nav>
    @endif

    @if(isset($pageData['hero']))
        <x-education.hero-ecotainment
            :background-image="$pageData['hero']['image']"
            :background-image-fallback="$pageData['hero']['imageFallback']"
            :title="$pageData['hero']['title']"
            :description="$pageData['hero']['description']"
            :location-badge="$pageData['hero']['locationBadge']"
            :buttons="$pageData['hero']['buttons']"
            :statistics="$pageData['hero']['statistics']"
            :alt="$pageData['hero']['alt']"
        />
    @endif

    @if(isset($pageData['programCategories']))
        <section class="education-section education-section-alt" id="program-categories" aria-labelledby="program-categories-title">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title" id="program-categories-title">Tiga Jalur Eksplorasi Siswa</h2>
                    <p class="section-subtitle">Setiap kategori dirancang untuk membuat siswa bergerak, bertanya, mencoba, dan pulang dengan pengalaman yang mudah diingat.</p>
                </div>

                <x-education.card-grid
                    :cards="$pageData['programCategories']"
                    :columns="3"
                    section-id="program-categories-grid"
                />

                <x-education.card-carousel
                    :cards="$pageData['programCategories']"
                    carousel-id="program-categories-carousel"
                />
            </div>
        </section>
    @endif

    @if(isset($pageData['environmentalPrograms']))
        <x-education.program-detail-alternating
            :programs="$pageData['environmentalPrograms']"
            section-title="Edukasi Lingkungan"
        />
    @endif

    @if(isset($pageData['sciencePrograms']))
        <section class="education-section" id="science-programs" aria-labelledby="science-programs-title">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title" id="science-programs-title">Sains yang Bisa Dicoba Langsung</h2>
                    <p class="section-subtitle">Aktivitas praktis untuk membantu siswa memahami konsep dengan cara yang sederhana dan menyenangkan.</p>
                </div>

                <x-education.card-grid
                    :cards="$pageData['sciencePrograms']"
                    :columns="2"
                    section-id="science-programs-grid"
                />

                <x-education.card-carousel
                    :cards="$pageData['sciencePrograms']"
                    carousel-id="science-programs-carousel"
                />
            </div>
        </section>
    @endif

    @if(isset($pageData['artPrograms']))
        <section class="education-section education-section-alt" id="art-programs">
            <div class="container">
                <x-education.program-group
                    :featured="$pageData['artPrograms']['featured'] ?? []"
                    :others="$pageData['artPrograms']['others'] ?? []"
                    section-id="art-programs"
                />
            </div>
        </section>
    @endif

    @if(isset($pageData['testimonial']))
        <x-education.testimonial-section
            :photo="$pageData['testimonial']['photo']"
            :photo-fallback="$pageData['testimonial']['photoFallback']"
            :photo-alt="$pageData['testimonial']['photoAlt']"
            :quote="$pageData['testimonial']['quote']"
            :source="$pageData['testimonial']['source']"
        />
    @endif

    @if(isset($pageData['schoolPartners']))
        <section class="education-section education-section-alt school-partners-section" id="school-partners" aria-labelledby="school-partners-title">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title" id="school-partners-title">Mitra Sekolah Kami</h2>
                    <p class="section-subtitle">Sekolah dan institusi pendidikan yang sudah mempercayakan pengalaman belajar luar kelas bersama Godong Ijo.</p>
                </div>

                @if($pageData['schoolPartners']->count() > 0)
                    <div class="school-partners-carousel-wrapper">
                        {{-- Row 1: Scroll from right to left --}}
                        <div class="school-partners-carousel-row" data-direction="left">
                            <div class="school-partners-carousel-track">
                                @foreach($pageData['schoolPartners']->take(7) as $partner)
                                    <x-education.school-partner-card :partner="$partner" />
                                @endforeach
                                {{-- Duplicate for seamless loop --}}
                                @foreach($pageData['schoolPartners']->take(7) as $partner)
                                    <x-education.school-partner-card :partner="$partner" />
                                @endforeach
                            </div>
                        </div>

                        {{-- Row 2: Scroll from left to right --}}
                        <div class="school-partners-carousel-row" data-direction="right">
                            <div class="school-partners-carousel-track">
                                @foreach($pageData['schoolPartners']->skip(7) as $partner)
                                    <x-education.school-partner-card :partner="$partner" />
                                @endforeach
                                {{-- Duplicate for seamless loop --}}
                                @foreach($pageData['schoolPartners']->skip(7) as $partner)
                                    <x-education.school-partner-card :partner="$partner" />
                                @endforeach
                            </div>
                        </div>

                        {{-- Link to the full partner list. Shows a "+N" count only when the rows above don't already cover every partner. --}}
                        <div class="school-partners-more-container">
                            <a href="{{ route('education.school-partners') }}" class="school-partner-card school-partner-more">
                                @if(($pageData['remainingSchoolsCount'] ?? 0) > 0)
                                    <span class="school-partner-more-count">+{{ $pageData['remainingSchoolsCount'] }}</span>
                                    <span>sekolah lainnya</span>
                                @else
                                    <span>Lihat Semua Mitra Sekolah</span>
                                @endif
                            </a>
                        </div>
                    </div>
                @else
                    <p class="school-partners-empty">Data sekolah mitra siap ditampilkan setelah seeding database.</p>
                @endif
            </div>
        </section>
    @endif

    @if(isset($pageData['footerCTA']))
        <x-education.footer-cta-bar
            :text="$pageData['footerCTA']['text']"
            :button-label="$pageData['footerCTA']['buttonLabel']"
            :whatsapp-message="$pageData['footerCTA']['whatsappMessage']"
        />
    @endif
</div>
@endsection
