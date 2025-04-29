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
    <p class="text-muted mt-3">Pelajari konsep dasar dan lanjutan tentang Pemrograman Berorientasi Objek</p>
</div>

<div class="materials-container">
    <div class="row g-4">
        @foreach($materials as $material)
        <div class="col-md-6 col-lg-4">
            <div class="material-card">
                <div class="material-card-header">
                    <h4 class="material-title">{{ $material->title }}</h4>
                </div>
                <div class="material-card-body">
                    <div class="material-description">
                        {{ Str::limit($material->description, 150) }}
                    </div>
                    
                    <div class="material-meta mt-3">
                        <div class="meta-item">
                            <i class="fas fa-book-reader text-success"></i>
                            <span>{{ $material->content ? 'Materi Tersedia' : 'Belum Tersedia' }}</span>
                        </div>
                    </div>

                    @if(auth()->check()==null)
                        <div class="guest-notice mt-3">
                            <small class="text-warning">
                                <i class="fas fa-info-circle"></i>
                                Mode Tamu - Akses Terbatas
                            </small>
                        </div>
                    @endif

                    <div class="material-actions mt-3">
                        <a href="{{ route('mahasiswa.materials.show', $material->id) }}" 
                           class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-book-open me-2"></i>Baca Materi
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
.material-card {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    transition: transform 0.2s;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.material-card:hover {
    transform: translateY(-5px);
}

.material-card-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
}

.material-title {
    color: #2c3e50;
    font-size: 1.25rem;
    margin: 0;
}

.material-card-body {
    padding: 20px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.material-description {
    color: #666;
    font-size: 0.95rem;
    line-height: 1.6;
    flex-grow: 1;
}

.material-meta {
    margin-top: auto;
    padding-top: 15px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
}

.material-actions {
    margin-top: 15px;
}

.guest-notice {
    padding: 8px;
    background: #fff3cd;
    border-radius: 5px;
    text-align: center;
}
</style>
@endpush
@endsection 