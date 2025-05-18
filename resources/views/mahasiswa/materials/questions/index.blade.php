@extends('mahasiswa.layouts.app')

@section('title', 'Latihan Soal PBO')

@section('content')
@if(auth()->check() && auth()->user()->role_id === 4)
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

@if(!auth()->check() || (auth()->check() && auth()->user()->role_id === 4))
<div class="alert alert-warning mb-4">
    <strong>Mode Tamu Aktif!</strong> 
    Anda hanya dapat melihat sebagian materi dan hanya 3 soal latihan dari setiap tingkat kesulitan yang ditampilkan. 
    Untuk akses penuh, silakan 
    <a href="{{ route('login') }}" class="alert-link">login</a> 
    atau 
    <a href="{{ route('register') }}" class="alert-link">daftar</a> 
    sebagai mahasiswa.
</div>
@endif

<div class="dashboard-header text-center">
    <h1 class="main-title">Latihan Soal PBO</h1>
    <div class="title-underline"></div>
    <p class="subtitle mt-3">Uji pemahaman Anda dengan mengerjakan latihan soal untuk setiap materi</p>
</div>

<div class="materials-container">
    <div class="row">
        @foreach($materials as $material)
        <div class="col-md-12 mb-4">
            <a href="{{ route('mahasiswa.materials.questions.levels', $material->id) }}" class="card-link">
                <div class="material-question-card horizontal">
                    <!-- Bagian Gambar Material (Kiri) -->
                    <div class="material-left-section">
                        @if($material->media && $material->media->isNotEmpty())
                            <div class="material-question-image">
                                <img src="{{ $material->media->first()->media_url }}" alt="{{ $material->title }}">
                            </div>
                        @else
                            <div class="material-question-image default-image">
                                <div class="no-image-icon">
                                    <i class="fas fa-code"></i>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Bagian Konten (Kanan) -->
                    <div class="material-right-section">
                        <div class="material-top-section">
                            <div class="material-info">
                                <div class="material-badges">
                                    <div class="material-badge">
                                        <span class="badge-text">Tersedia</span>
                                    </div>
                                </div>
                                <h2 class="material-question-title">{{ $material->title }}</h2>
                                <!-- Material Meta Info dengan jumlah mahasiswa sebenarnya -->
                                <div class="material-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-users"></i>
                                        <span>{{ $material->student_count }} Mahasiswa</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Navigation Icon -->
                            <div class="nav-icon">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                        
                        <div class="material-bottom-section">
                            <!-- Progress Section -->
                            @if(!auth()->check() || (auth()->check() && auth()->user()->role_id === 4))
                                <!-- Guest Mode Display -->
                                <div class="guest-limit-section">
                                    <div class="guest-info-icon">
                                        <i class="fas fa-lock text-warning"></i>
                                    </div>
                                    <div class="guest-limit-text">
                                        <span>Mode Tamu: Akses Terbatas</span>
                                        <small>Hanya 3 soal per tingkat kesulitan. Login untuk akses penuh</small>
                                    </div>
                                </div>
                            @else
                                <!-- Regular Progress Section for Registered Users -->
                                <div class="progress-section">
                                    <div class="progress-header">
                                        <span class="progress-label">Progress</span>
                                        <span class="progress-percentage">{{ $material->progress_percentage }}%</span>
                                    </div>
                                    <div class="progress-bar-wrapper">
                                        <div class="progress-bar-bg">
                                            <div class="progress-bar-fill" style="width: {{ $material->progress_percentage }}%"></div>
                                        </div>
                                    </div>
                                    <div class="progress-detail">
                                        {{ $material->completed_questions }} dari {{ $material->total_questions }} soal selesai
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Action Button -->
                            <div class="btn-start-exercise">
                                <span>Detail</span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
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

/* Card Link Styling - Menghilangkan garis bawah */
.card-link {
    display: block;
    text-decoration: none !important;
    color: inherit;
}

.card-link:hover {
    text-decoration: none !important;
    color: inherit;
}

.card-link:focus {
    outline: none;
    text-decoration: none !important;
}

.card-link .material-question-title {
    text-decoration: none !important;
}

/* Horizontal Card Redesign */
.material-question-card.horizontal {
    background-color: white;
    border-radius: 15px;
    box-shadow: 0 0 0 4px rgba(0,87,184,0.1), 0 6px 16px rgba(0,87,184,0.08);
    overflow: hidden;
    position: relative;
    margin-bottom: 30px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: row;
    height: 180px;
}

.material-question-card.horizontal:hover {
    transform: translateY(-5px);
    box-shadow: 0 0 0 4px rgba(0,87,184,0.2), 0 12px 30px rgba(0,87,184,0.15);
}

/* Left Section - Image */
.material-left-section {
    width: 30%;
    overflow: hidden;
    position: relative;
}

.material-question-image {
    width: 100%;
    height: 100%;
    position: relative;
}

.material-question-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.material-question-card:hover .material-question-image img {
    transform: scale(1.05);
}

.default-image {
    background: linear-gradient(135deg, #e6f2ff, #d9e9ff);
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
}

.no-image-icon {
    font-size: 50px;
    color: #0057B8;
    opacity: 0.5;
}

/* Right Section - Content */
.material-right-section {
    width: 70%;
    padding: 15px 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
}

.material-top-section {
    display: flex;
    justify-content: space-between;
}

.material-info {
    flex: 1;
}

.material-badges {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
}

.material-badge {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    padding: 3px 10px;
    border-radius: 15px;
    font-size: 0.7rem;
    font-weight: 600;
    box-shadow: 0 2px 10px rgba(40, 167, 69, 0.3);
    margin-right: 10px;
}

.material-level {
    font-size: 0.8rem;
    color: #6c757d;
    font-weight: 600;
}

.material-question-title {
    font-weight: 700;
    font-size: 1.3rem;
    color: #0057B8;
    margin-bottom: 5px;
    line-height: 1.3;
}

.material-code {
    font-size: 0.85rem;
    color: #6c757d;
    margin-bottom: 5px;
    font-weight: 500;
}

.material-meta {
    display: flex;
    gap: 15px;
    font-size: 0.85rem;
}

.meta-item {
    display: flex;
    align-items: center;
    color: #495057;
}

.meta-item i {
    margin-right: 5px;
    color: #0057B8;
}

.nav-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background-color: #f0f7ff;
    border-radius: 50%;
    color: #0057B8;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.material-question-card:hover .nav-icon {
    background-color: #0057B8;
    color: white;
    transform: translateX(5px);
}

.material-bottom-section {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
}

/* Progress Section */
.progress-section {
    flex: 1;
    margin-right: 20px;
}

.progress-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
}

.progress-label {
    font-weight: 600;
    color: #4a5568;
    font-size: 0.8rem;
}

.progress-percentage {
    font-weight: 700;
    color: #0057B8;
    font-size: 0.8rem;
}

.progress-bar-wrapper {
    margin-bottom: 5px;
}

.progress-bar-bg {
    height: 8px;
    background-color: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(to right, #0057B8, #0074D9);
    border-radius: 4px;
}

.progress-detail {
    font-size: 0.75rem;
    color: #718096;
}

/* Action Button */
.btn-start-exercise {
    background: linear-gradient(135deg, #0057B8, #0074D9);
    color: white;
    padding: 8px 20px;
    border-radius: 20px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0,87,184,0.2);
    border: none;
    display: inline-block;
}

.btn-start-exercise:hover {
    background: linear-gradient(135deg, #004a9e, #0066c0);
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0,87,184,0.3);
    color: white;
    text-decoration: none;
}

/* Responsiveness */
@media (max-width: 992px) {
    .material-question-card.horizontal {
        flex-direction: column;
        height: auto;
    }
    
    .material-left-section, .material-right-section {
        width: 100%;
    }
    
    .material-left-section {
        height: 180px;
    }
    
    .material-bottom-section {
        margin-top: 15px;
    }
}

@media (max-width: 576px) {
    .material-question-title {
        font-size: 1.1rem;
    }
    
    .material-bottom-section {
        flex-direction: column;
        gap: 15px;
    }
    
    .progress-section {
        margin-right: 0;
    }
    
    .btn-start-exercise {
        width: 100%;
    }
    
    .material-left-section {
        height: 150px;
    }
}

/* Guest mode display styling */
.guest-limit-section {
    display: flex;
    align-items: center;
    padding: 10px 15px;
    background-color: #fff8e1;
    border-radius: 8px;
    border-left: 4px solid #ffc107;
}

.guest-info-icon {
    font-size: 1.5rem;
    margin-right: 15px;
}

.guest-limit-text {
    display: flex;
    flex-direction: column;
}

.guest-limit-text span {
    font-weight: 600;
    color: #555;
}

.guest-limit-text small {
    color: #777;
    font-size: 0.8rem;
}
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if tour has been shown for this page
        if (!sessionStorage.getItem('question_index_tour_complete')) {
            setTimeout(startQuestionsIndexTour, 500);
        }
    });

    function startQuestionsIndexTour() {
        const steps = [
            {
                intro: "Selamat datang di halaman Latihan Soal OOPEDIA!"
            },
            {
                element: document.querySelector('.material-question-card'),
                intro: "Ini adalah kartu materi latihan soal. Pilih salah satu materi untuk mulai berlatih."
            },
            {
                element: document.querySelector('.progress-section'),
                intro: "Di sini Anda dapat melihat progres pengerjaan soal untuk setiap materi."
            },
            {
                element: document.querySelector('.btn-start-exercise'),
                intro: "Klik tombol ini untuk melihat detail dan mulai mengerjakan latihan soal."
            },
            {
                intro: "Selamat berlatih soal PBO di OOPEDIA!"
            }
        ];

        // Start the tutorial
        introJs().setOptions({
            steps: steps,
            showProgress: true,
            exitOnOverlayClick: true,
            showBullets: false,
            scrollToElement: true,
            nextLabel: 'Berikutnya',
            prevLabel: 'Sebelumnya',
            doneLabel: 'Mulai'
        }).oncomplete(function() {
            // Mark as completed in session storage
            sessionStorage.setItem('question_index_tour_complete', 'true');
        }).start();
    }
</script>
@endpush
@endsection 