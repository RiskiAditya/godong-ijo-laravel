@extends('layouts.app')

@section('content')
<div class="education-page school-partners-page">
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

    <section class="education-section school-partners-list-section">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Sekolah Mitra</span>
                <h1 class="section-title">Sekolah Mitra Kami</h1>
                <p class="section-subtitle">Daftar sekolah dan institusi pendidikan yang telah mengikuti program wisata edukasi Godong Ijo.</p>
            </div>

            @if($schoolPartners->count() > 0)
                <div class="school-partners-grid full-list">
                    @foreach($schoolPartners as $partner)
                        <x-education.school-partner-card :partner="$partner" />
                    @endforeach
                </div>

                <div class="school-partners-pagination">
                    {{ $schoolPartners->onEachSide(2)->links('vendor.pagination.custom') }}
                </div>
            @else
                <p class="school-partners-empty">Data sekolah mitra belum tersedia.</p>
            @endif
        </div>
    </section>
</div>
@endsection
