@extends('mahasiswa.layouts.app')

@section('title', 'Materi Pembelajaran')

@section('content')
<div class="dashboard-header text-center">
    <h1 class="main-title">Materi PBO</h1>
    <div class="title-underline"></div>
</div>

<div class="materials-container">
    <div class="row g-4">
        @foreach($materials as $material)
        <div class="col-md-6 col-lg-4">
            <div class="progress-item-card">
                <h4 class="progress-item-title">{{ $material->title }}</h4>
                <div class="materi-description">
                    {{ $material->description }}
                </div>
                <div class="progress-container mt-3">
                    <div class="progress-info d-flex justify-content-between">
                        <span class="progress-text">Progress</span>
                        <span class="progress-percentage">
                            @php
                                $totalQuestions = $material->total_questions;
                                $correctAnswers = $material->completed_questions;
                                $percentage = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;
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
                        </small>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('mahasiswa.materials.show', $material) }}" 
                       class="btn btn-primary w-100">
                        <i class="fas fa-book-reader me-2"></i>
                        {{ $percentage == 100 ? 'Lihat Kembali Materi' : 'Mulai Belajar' }}
                    </a>
                </div>
            </div>
        </div>
        @endforeach
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

.progress-item-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.materi-description {
    color: #666;
    margin-bottom: 1.5rem;
    font-size: 0.95rem;
}

.progress-bar-container {
    width: 100%;
    height: 8px;
    background: #f0f0f0;
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(to right, #FF0080, #7928CA);
    border-radius: 4px;
    transition: width 0.3s ease;
}

.btn-primary {
    background: linear-gradient(to right, #FF0080, #7928CA);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    opacity: 0.9;
    transform: translateY(-2px);
}

.progress-details {
    color: #666;
    text-align: center;
}

.progress-info {
    margin-bottom: 0.5rem;
}

.progress-percentage {
    font-weight: 600;
    color: #FF0080;
}
</style>
@endpush
@endsection 