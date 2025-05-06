@extends('mahasiswa.layouts.app')

@section('title', 'Latihan Soal')

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
    Anda hanya dapat melihat sebagian materi dan hanya 3 soal latihan dari setiap materi yang ditampilkan. 
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
    <div class="row g-4">
        @foreach($materials as $material)
        <div class="col-md-6 col-lg-4">
            <div class="material-card">
                <div class="material-card-header">
                    <div class="material-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h4 class="material-title">{{ $material->title }}</h4>
                </div>
                <div class="material-card-body">
                    <div class="material-description">
                        Latihan soal untuk materi {{ $material->title }}
                    </div>
                    
                    <div class="progress-container mt-3">
                        <div class="progress-info d-flex justify-content-between">
                            <span class="progress-text">Progress</span>
                            <span class="progress-percentage">
                                @php
                                    $totalQuestions = $material->total_questions;
                                    
                                    // Check if user is logged in first before checking role_id
                                    if(auth()->check() && auth()->user()->role_id === 4) {
                                        $totalQuestions = ceil($totalQuestions / 2);
                                    }
                                    // If not logged in, treat as guest with limited access
                                    elseif(!auth()->check()) {
                                        $totalQuestions = min(3, $totalQuestions);
                                    }
                                    
                                    $correctAnswers = $material->completed_questions ?? 0;
                                    $percentage = $totalQuestions > 0 ? min(100, round(($correctAnswers / $totalQuestions) * 100)) : 0;
                                @endphp
                                {{ $percentage }}%
                            </span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                        </div>
                        <div class="progress-details mt-2">
                            <small>
                                {{ $correctAnswers }} dari {{ $totalQuestions }} soal selesai
                                @if(auth()->check() && auth()->user()->role_id === 4)
                                    (Mode Tamu)
                                @elseif(!auth()->check())
                                    (Mode Tamu)
                                @endif
                            </small>
                        </div>
                    </div>

                    <div class="material-meta">
                        <div class="meta-item">
                            <i class="fas fa-tasks"></i>
                            <span>{{ $material->is_guest_limited ? '3' : $material->total_questions }} Soal</span>
                        </div>
                        
                        <div class="meta-item">
                            <i class="fas fa-signal"></i>
                            <span>Tersedia Berbagai Tingkat Kesulitan</span>
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
                        <a href="{{ route('mahasiswa.materials.questions.levels', ['material' => $material->id, 'difficulty' => 'beginner']) }}" 
                           class="btn-read-material">
                            <span>Mulai Latihan</span>
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
    background: var(--gradient-primary);
    color: white;
    padding: 1.5rem;
    position: relative;
    text-align: center;
}

.material-icon {
    font-size: 2.5rem;
    margin-bottom: 0.8rem;
}

.material-title {
    font-size: 1.3rem;
    font-weight: 700;
    margin: 0;
}

.material-card-body {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.material-description {
    color: var(--text-dark);
    font-size: 0.95rem;
    margin-bottom: 1rem;
}

.progress-container {
    margin-bottom: 1.5rem;
}

.progress-info {
    margin-bottom: 0.5rem;
}

.progress-text {
    font-weight: 600;
    color: var(--text-dark);
}

.progress-percentage {
    font-weight: 700;
    color: var(--color-1);
}

.progress-bar-container {
    height: 10px;
    background-color: #e9ecef;
    border-radius: 5px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: var(--gradient-primary);
    border-radius: 5px;
    transition: width 0.6s ease;
}

.progress-details {
    color: var(--text-muted);
    text-align: right;
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
    position: relative;
    padding-top: 5px;
    margin-top: auto;
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
}

.guest-limited {
    background: linear-gradient(135deg, #ffa000, #ff6f00) !important;
    position: relative;
    padding-top: 12px !important;
    margin-top: 10px !important;
}

.guest-limited::before {
    content: "Terbatas";
    position: absolute;
    top: -10px;
    right: 10px;
    background: #ff3d00;
    color: white;
    font-size: 10px;
    padding: 2px 8px;
    border-radius: 10px;
    font-weight: bold;
    z-index: 1;
}

.guest-limited span, 
.guest-limited i {
    position: relative;
    z-index: 0;
}
</style>
@endpush
@endsection 