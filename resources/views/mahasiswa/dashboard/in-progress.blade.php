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
                        <div class="mt-3">
                            <a href="{{ route('mahasiswa.materials.show', $material->id) }}" class="btn-continue-material">
                                <i class="fas fa-play-circle me-2"></i>Lanjutkan Belajar
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
@endpush
@endsection