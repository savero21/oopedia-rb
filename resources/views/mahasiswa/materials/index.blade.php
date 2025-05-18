@extends('mahasiswa.layouts.app')

@section('title', 'Materi Pembelajaran')

@section('content')
@if(auth()->check() && auth()->user() === null)
<!-- Hidden forms for guest logout and redirect -->
<form id="guest-logout-login-form" action="{{ route('guest.logout') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="redirect" value="{{ route('login') }}">
</form>

<form id="guest-logout-register-form" action="{{ route('guest.logout') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="redirect" value="{{ route('register') }}">
</form>
@endif

<div class="dashboard-header text-center">
    <h1 class="main-title">Materi Pemrograman Berorientasi Objek</h1>
    <div class="title-underline"></div>
    <p class="subtitle mt-3">Pelajari konsep dasar dan lanjutan tentang Pemrograman Berorientasi Objek</p>
</div>

<div class="row mt-5">
    @foreach($materials as $material)
    <div class="col-md-4 mb-4">
        <div class="material-card">
            <!-- Badge status di pojok kiri atas -->
            <div class="material-badge">
                <span class="badge-text">Tersedia</span>
            </div>
            
            <!-- Menampilkan gambar jika ada -->
            @if($material->media && $material->media->isNotEmpty())
                <div class="material-image">
                    <img src="{{ $material->media->first()->media_url }}" alt="{{ $material->title }}" class="img-fluid">
                </div>
            @else
                <div class="material-image default-image">
                    <div class="no-image-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                </div>
            @endif
            
            <div class="material-icon">
                <i class="fas fa-book"></i>
            </div>
            
            <div class="material-content">
                <div class="material-title">
                    {{ $material->title }}
                </div>
                
                <div class="material-meta">
                    <div class="meta-item">
                        <i class="fas fa-user"></i> {{ $material->creator ? $material->creator->name : 'Admin' }}
                    </div>
                    <div class="meta-item">
                        <i class="far fa-calendar-alt"></i> {{ $material->updated_at->format('d M Y') }}
                    </div>
                </div>
                
                <div class="content-divider"></div>
                
                <div class="material-stats">
                    <div class="stats-pill">
                        <i class="fas fa-question-circle"></i> {{ $material->questions->count() }} Soal
                    </div>
                </div>
                
                <a href="{{ route('mahasiswa.materials.show', $material->id) }}" class="material-link">
                    Baca Materi <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection

@push('css')
<style>
    .dashboard-header {
        margin-bottom: 50px;
    }
    
    .main-title {
        color: #0057B8;
        font-weight: 700;
        font-size: 2.5rem;
        margin-bottom: 10px;
    }
    
    .title-underline {
        width: 180px;
        height: 4px;
        background-color: #0057B8;
        margin: 0 auto;
    }
    
    .subtitle {
        color: #555;
        font-size: 1.2rem;
    }
    
    .material-card {
        background-color: white;
        border-radius: 15px;
        box-shadow: 0 0 0 4px rgba(0,87,184,0.2), 0 6px 16px rgba(0,87,184,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
        overflow: hidden;
        border: none;
    }
    
    .material-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0 0 4px rgba(0,87,184,0.4), 0 12px 30px rgba(0,87,184,0.15);
    }
    
    .material-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        z-index: 3;
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        box-shadow: 0 2px 10px rgba(40, 167, 69, 0.3);
    }
    
    .material-image {
        height: 180px;
        position: relative;
        border-top-left-radius: 13px;
        border-top-right-radius: 13px;
        border-bottom: 1px solid #e0e6ed;
        background-color: #f8f9fa;
        overflow: hidden;
    }
    
    .material-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
        padding: 0;
    }
    
    .material-image::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 20px;
        background: linear-gradient(to top, rgba(248,249,250,0.8), transparent);
        z-index: 1;
    }
    
    .material-card:hover .material-image img {
        transform: scale(1.05);
    }
    
    .material-icon {
        position: absolute;
        top: 180px;
        right: 20px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #0057B8;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        z-index: 2;
        border: 3px solid white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .material-card:hover .material-icon {
        transform: rotate(15deg);
    }
    
    .material-content {
        padding: 25px 20px 20px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    
    .material-title {
        font-weight: 700;
        font-size: 1.3rem;
        color: #0057B8;
        margin-bottom: 10px;
        line-height: 1.4;
    }
    
    .material-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 5px;
    }
    
    .meta-item {
        font-size: 0.85rem;
        color: #6c757d;
        display: flex;
        align-items: center;
    }
    
    .meta-item i {
        margin-right: 5px;
        color: #0057B8;
        opacity: 0.8;
    }
    
    .content-divider {
        width: 100%;
        height: 1px;
        background: linear-gradient(to right, rgba(0,87,184,0.1), rgba(0,87,184,0.2), rgba(0,87,184,0.1));
        margin: 10px 0 15px;
    }
    
    .material-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .stats-pill {
        background-color: #f0f7ff;
        color: #0057B8;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        border: 1px solid rgba(0,87,184,0.1);
    }
    
    .stats-pill i {
        margin-right: 5px;
    }
    
    .material-link {
        display: inline-block;
        background: linear-gradient(135deg, #0057B8, #0074D9);
        color: white;
        padding: 10px 20px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 500;
        margin-top: auto;
        transition: all 0.3s ease;
        text-align: center;
        box-shadow: 0 4px 10px rgba(0,87,184,0.2);
        border: none;
    }
    
    .material-link:hover {
        background: linear-gradient(135deg, #004a9e, #0068c3);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,87,184,0.3);
    }
    
    .material-link i {
        margin-left: 5px;
        transition: transform 0.3s ease;
    }
    
    .material-link:hover i {
        transform: translateX(3px);
    }
    
    /* Responsivitas */
    @media (max-width: 767px) {
        .material-image {
            height: 180px;
        }
        
        .material-icon {
            top: 155px;
            width: 45px;
            height: 45px;
        }
        
        .material-content {
            padding: 20px 15px 15px;
        }
        
        .material-title {
            font-size: 1.2rem;
        }
    }
    
    /* Untuk gambar default */
    .default-image {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f0f7ff, #e6f2ff);
    }
    
    .no-image-icon {
        font-size: 48px;
        color: #0057B8;
        opacity: 0.4;
    }

    /* Perbaikan Gaya untuk Tour Guide */
    .introjs-tooltip {
        border-radius: 12px !important;
        padding: 20px !important;
        max-width: 400px !important;
        box-shadow: 0 8px 25px rgba(0, 78, 152, 0.15) !important;
        border: 1px solid rgba(0, 78, 152, 0.1) !important;
    }

    .introjs-tooltip-header {
        padding-bottom: 10px !important;
        border-bottom: 1px solid rgba(0, 78, 152, 0.1) !important;
        margin-bottom: 15px !important;
    }

    .introjs-tooltiptext {
        font-size: 15px !important;
        line-height: 1.6 !important;
        color: var(--text-dark) !important;
    }

    .introjs-tooltipbuttons {
        border-top: 1px solid rgba(0, 78, 152, 0.1) !important;
        padding-top: 15px !important;
        margin-top: 15px !important;
        text-align: right !important;
    }

    .introjs-button {
        padding: 8px 16px !important;
        border-radius: 8px !important;
        font-weight: 500 !important;
        transition: all 0.3s ease !important;
        margin-left: 8px !important;
    }

    .introjs-skipbutton {
        background-color: #f8f9fa !important;
        color: var(--color-1) !important;
        border: 1px solid rgba(0, 78, 152, 0.2) !important;
    }

    .introjs-skipbutton:hover {
        background-color: #e9ecef !important;
    }

    .introjs-nextbutton {
        background: var(--gradient-primary) !important;
        color: white !important;
    }

    .introjs-nextbutton:hover {
        background: var(--gradient-secondary) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 8px rgba(0, 78, 152, 0.2) !important;
    }

    .introjs-prevbutton {
        background-color: white !important;
        color: var(--color-1) !important;
        border: 1px solid rgba(0, 78, 152, 0.2) !important;
    }

    .introjs-prevbutton:hover {
        background-color: #f8f9fa !important;
    }

    .introjs-bullets {
        bottom: -25px !important;
    }

    .introjs-bullets ul li a {
        background: rgba(0, 78, 152, 0.2) !important;
    }

    .introjs-bullets ul li a.active {
        background: var(--color-1) !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/intro.js/minified/intro.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (!sessionStorage.getItem('material_index_tour_complete')) {
            setTimeout(startMaterialsIndexTour, 800);
        }
    });

    function startMaterialsIndexTour() {
        const steps = [
            {
                intro: `
                    <div class="text-center">
                        <h4 style="margin-bottom: 10px; color: var(--color-1);">Selamat Datang</h4>
                        <p>Di halaman Materi OOPEDIA!</p>
                    </div>
                `,
                position: 'center'
            },
            {
                element: document.querySelector('.material-card:first-child'),
                intro: `
                    <div>
                        <h5 style="margin-bottom: 8px; color: var(--color-1);">Kartu Materi</h5>
                        <p>Ini adalah kartu materi pembelajaran. Pilih salah satu materi untuk mulai belajar.</p>
                    </div>
                `,
                position: 'auto'
            },
            {
                element: document.querySelector('.material-actions .btn-read-material'),
                intro: `
                    <div>
                        <h5 style="margin-bottom: 8px; color: var(--color-1);">Tombol Baca</h5>
                        <p>Klik tombol ini untuk mulai mempelajari materi yang dipilih.</p>
                    </div>
                `,
                position: 'auto'
            },
            {
                intro: `
                    <div class="text-center">
                        <h4 style="margin-bottom: 10px; color: var(--color-1);">Selamat Belajar!</h4>
                        <p>Mari eksplorasi dunia Pemrograman Berorientasi Objek bersama OOPEDIA.</p>
                    </div>
                `,
                position: 'center'
            }
        ];

        introJs().setOptions({
            steps: steps,
            showProgress: true,
            exitOnOverlayClick: true,
            showBullets: true,
            scrollToElement: true,
            nextLabel: 'Berikutnya',
            prevLabel: 'Sebelumnya',
            skipLabel: 'Lewati',
            doneLabel: 'Selesai',
            tooltipClass: 'custom-tour',
            highlightClass: 'custom-highlight',
            hidePrev: true,
            exitOnEsc: true
        }).oncomplete(function() {
            sessionStorage.setItem('material_index_tour_complete', 'true');
        }).onexit(function() {
            sessionStorage.setItem('material_index_tour_complete', 'true');
        }).start();
    }
</script>
@endpush