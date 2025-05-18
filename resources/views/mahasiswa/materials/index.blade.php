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

<div class="materials-container">
    <div class="row g-4">
        @foreach($materials as $material)
        <div class="col-md-6 col-lg-4">
            <div class="material-card">
                <div class="material-card-header">
                    <div class="material-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h4 class="material-title">{{ $material->title }}</h4>
                </div>
                <div class="material-card-body">
                    
                    <div class="material-description">
                        {{ Str::limit($material->description, 150) }}
                    </div>
                    
                    <div class="material-meta">
                        <div class="meta-item">
                            <i class="fas fa-book-reader"></i>
                            <span>{{ $material->content ? 'Materi Tersedia' : 'Belum Tersedia' }}</span>
                        </div>
                        
                        <div class="meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Update: {{ $material->updated_at->format('d M Y') }}</span>
                        </div>
                    </div>

                    @if(auth()->check()==null)
                        <div class="guest-notice">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                Mode Tamu - Akses Terbatas
                            </small>
                        </div>
                    @endif

                    <div class="material-actions">
                        <a href="{{ route('mahasiswa.materials.show', $material->id) }}" class="btn-read-material">
                            <span>Baca Materi</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@push('css')
<style>
/* Perbaikan Gaya Halaman Materi */
.dashboard-header {
    padding: 2.5rem 0;
    margin-bottom: 2rem;
}

.main-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--color-1);
    margin-bottom: 1rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.title-underline {
    width: 180px;
    height: 5px;
    background: var(--gradient-primary);
    margin: 0 auto;
    border-radius: 3px;
}

.subtitle {
    font-size: 1.1rem;
    color: var(--text-dark);
    margin-top: 1rem;
    font-weight: 500;
}

.materials-container {
    max-width: 1320px;
    margin: 0 auto;
    padding: 0 15px;
}

/* Material Card Redesign */
.material-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0, 78, 152, 0.1);
    transition: all 0.3s ease;
    height: 100%;
    position: relative;
    border: 1px solid rgba(0, 78, 152, 0.1);
    display: flex;
    flex-direction: column;
}

.material-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0, 78, 152, 0.15);
    border-color: rgba(0, 78, 152, 0.2);
}

.material-card-header {
    padding: 1.5rem;
    border-bottom: 1px solid rgba(0, 78, 152, 0.1);
    position: relative;
    background: rgba(0, 78, 152, 0.02);
    display: flex;
    align-items: center;
}

.material-icon {
    width: 40px;
    height: 40px;
    background: var(--gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    color: white;
    font-size: 1.2rem;
    box-shadow: 0 4px 10px rgba(0, 78, 152, 0.2);
}

.material-title {
    color: var(--color-1);
    font-size: 1.3rem;
    font-weight: 700;
    margin: 0;
    flex: 1;
}

.material-card-body {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.material-description {
    color: var(--text-dark);
    font-size: 0.95rem;
    line-height: 1.7;
    margin-bottom: 1.5rem;
    flex-grow: 1;
}

.material-meta {
    margin: 1rem 0;
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

.meta-item {
    display: flex;
    align-items: center;
    font-size: 0.9rem;
    color: var(--text-dark);
    background-color: rgba(0, 78, 152, 0.05);
    padding: 0.5rem 0.8rem;
    border-radius: 6px;
}

.meta-item i {
    color: var(--color-1);
    margin-right: 8px;
    font-size: 1rem;
}

.guest-notice {
    background-color: #fff8e1;
    border-left: 4px solid #ffc107;
    color: #856404;
    padding: 0.8rem;
    border-radius: 6px;
    margin: 1rem 0;
}

.material-actions {
    margin-top: auto;
    padding-top: 1rem;
}

.btn-read-material {
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--gradient-primary);
    color: white !important;
    padding: 0.8rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    text-align: center;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 78, 152, 0.2);
}

.btn-read-material span {
    transition: all 0.3s ease;
    position: relative;
    z-index: 2;
}

.btn-read-material i {
    margin-left: 10px;
    transition: all 0.3s ease;
    position: relative;
    z-index: 2;
}

.btn-read-material:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 78, 152, 0.3);
}

.btn-read-material:hover i {
    transform: translateX(5px);
}

.btn-read-material::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: var(--gradient-secondary);
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 1;
}

.btn-read-material:hover::after {
    opacity: 1;
}

/* Responsiveness Improvements */
@media (max-width: 768px) {
    .main-title {
        font-size: 2rem;
    }
    
    .material-card-header {
        padding: 1.2rem;
    }
    
    .material-card-body {
        padding: 1.2rem;
    }
}

@media (max-width: 576px) {
    .main-title {
        font-size: 1.8rem;
    }
    
    .material-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
}/* Perbaikan Gaya untuk Tour Guide */
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
@endsection 