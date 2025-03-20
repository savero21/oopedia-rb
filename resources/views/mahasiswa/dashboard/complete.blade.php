@extends('mahasiswa.layouts.app')

@section('title', 'Materi Selesai')

@section('content')
<div class="dashboard-header text-center">
    <h1 class="main-title">Materi Selesai</h1>
    <div class="title-underline"></div>
</div>

<div class="container-fluid">
    <div class="row g-4">
        @forelse($materials as $material)
            <div class="col-md-4">
                <div class="progress-item-card">
                    <h4 class="progress-item-title">{{ $material->title }}</h4>
                    <div class="progress-container mt-3">
                        <div class="progress-info d-flex justify-content-between">
                            <span class="progress-text">Progress</span>
                            <span class="progress-percentage">100%</span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar" style="width: 100%"></div>
                        </div>
                    </div>
                    <div class="question-info mt-3">
                        <i class="fas fa-question-circle"></i>
                        <span>{{ $material->questions->count() }} Soal</span>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('mahasiswa.materials.show', $material->id) }}" 
                           class="btn btn-primary w-100">
                            <i class="fas fa-eye me-2"></i>Lihat Materi
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>
                    Belum ada materi yang selesai dikerjakan.
                </div>
            </div>
        @endforelse
    </div>
</div>

@push('css')
<style>
.progress-item-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.progress-item-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.progress-item-title {
    color: #344767;
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.progress-bar-container {
    background: #f0f2f5;
    border-radius: 10px;
    height: 10px;
    overflow: hidden;
    margin-top: 0.5rem;
}

.progress-bar {
    background: linear-gradient(to right, #FF0080, #7928CA);
    height: 100%;
    border-radius: 10px;
    transition: width 0.3s ease;
}

.question-info {
    color: #677788;
    font-size: 0.9rem;
}

.question-info i {
    color: #e91e63;
    margin-right: 0.5rem;
}

.btn-primary {
    background: linear-gradient(to right, #FF0080, #7928CA);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(to right, #FF0080, #7928CA);
    opacity: 0.9;
}
</style>
@endpush
@endsection 