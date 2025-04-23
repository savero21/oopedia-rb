@extends('mahasiswa.layouts.app')

@section('title', 'Materi Sedang Dipelajari')

@section('content')
<div class="dashboard-header">
    <h1 class="main-title">Materi Sedang Dipelajari</h1>
    <div class="title-underline"></div>
</div>

<div class="dashboard-content">
    @if($materials->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <h3 class="empty-state-title">Belum Ada Materi yang Sedang Dipelajari</h3>
            <p class="empty-state-description">
                Anda belum memulai belajar materi apapun atau semua materi sudah selesai.
            </p>
            <a href="{{ route('mahasiswa.materials.index') }}" class="btn btn-primary">
                <i class="fas fa-book me-2"></i>Lihat Daftar Materi
            </a>
        </div>
    @else
        <div class="row g-4">
            @foreach($materials as $material)
                <div class="col-md-4">
                    <div class="progress-item-card">
                        <h4 class="progress-item-title">{{ $material->title }}</h4>
                        <div class="progress-container mt-3">
                            <div class="progress-info d-flex justify-content-between">
                                <span class="progress-text">Progress</span>
                                <span class="progress-percentage">
                                    @php
                                        $progress = $progressStats->firstWhere('material_id', $material->id);
                                        $totalQuestions = $material->questions->count();
                                        $correctAnswers = $progress ? $progress->correct_answers : 0;
                                        $percentage = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;
                                    @endphp
                                    {{ $percentage }}%
                                </span>
                            </div>
                            <div class="progress-bar-container">
                                <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        <div class="question-info mt-3">
                            <i class="fas fa-question-circle"></i>
                            <span>{{ $material->questions->count() }} Soal</span>
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('mahasiswa.materials.show', $material->id) }}" class="continue-learning-btn">
                                <span class="continue-icon"><i class="fas fa-play-circle"></i></span>
                                <span class="continue-text">Lanjutkan Belajar</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@push('css')
<link href="{{ asset('css/mahasiswa.css') }}" rel="stylesheet">
<style>
    .continue-learning-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 20px;
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3);
        transition: all 0.3s ease;
        width: 100%;
        max-width: 250px;
    }
    
    .continue-learning-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(78, 115, 223, 0.4);
        color: white;
        background: linear-gradient(135deg, #3a5fcc 0%, #1a3a9c 100%);
    }
    
    .continue-icon {
        margin-right: 10px;
        font-size: 1.2em;
    }
    
    .continue-text {
        font-size: 0.95em;
    }
    
    .progress-item-card {
        border-radius: 12px;
        padding: 20px;
        background-color: white;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .progress-item-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    
    .progress-item-title {
        font-weight: 600;
        color: #333;
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
    }
    
    .mt-4 {
        margin-top: auto !important;
        padding-top: 1rem;
    }
</style>
@endpush
@endsection