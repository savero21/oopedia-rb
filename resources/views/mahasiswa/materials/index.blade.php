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
<link href="{{ asset('css/mahasiswa.css') }}" rel="stylesheet">
@endpush
@endsection 