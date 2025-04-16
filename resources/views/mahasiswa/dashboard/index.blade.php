@extends('mahasiswa.layouts.app')

@php
use Illuminate\Support\Str;
@endphp

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-header text-center">
    <h1 class="main-title">Dashboard</h1>
    <div class="title-underline"></div>
</div>

<div class="container-fluid">
    <div class="row g-4">
        <!-- All Progress Overview Card -->
        <div class="col-md-4">
            <div class="materi-card">
                <div class="materi-card-body">
                    <h3 class="materi-title">Progress Keseluruhan</h3>
                    <div class="progress-container">
                        <div class="progress-info">
                            <span class="progress-text">Total Materi</span>
                            <span class="progress-percentage">{{ $totalMaterials }}</span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar" style="width: {{ $totalProgress }}%"></div>
                        </div>
                        <p class="progress-detail">{{ $completedCount }} dari {{ $totalMaterials }} materi selesai</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- In Progress Card -->
        <div class="col-md-4">
            <div class="materi-card">
                <div class="materi-card-body">
                    <h3 class="materi-title">Sedang Dipelajari</h3>
                    <div class="materi-description">
                        Materi yang sedang dipelajari
                    </div>
                    <div class="progress-container">
                        <div class="progress-info">
                            <span class="progress-text">Materi Aktif</span>
                            <span class="progress-percentage">{{ $inProgressCount }}</span>
                        </div>
                        <a href="{{ route('mahasiswa.materials.index') }}" class="btn btn-primary w-100 mt-3">
                            <i class="fas fa-book-reader me-2"></i>Jelajahi Materi
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Card -->
        <div class="col-md-4">
            <div class="materi-card">
                <div class="materi-card-body">
                    <h3 class="materi-title">Selesai</h3>
                    <div class="materi-description">
                        Materi yang berhasil diselesaikan
                    </div>
                    <div class="progress-container">
                        <div class="progress-info">
                            <span class="progress-text">Materi Selesai</span>
                            <span class="progress-percentage">{{ $completedCount }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- All Materials Progress Card -->
        <div class="col-12">
            <div class="materi-card">
                <div class="materi-card-body">
                    <h3 class="materi-title">Progress Semua Materi</h3>
                    <div class="materi-description mb-4">
                        Pantau progress Anda di semua materi yang tersedia
                    </div>
                    
                    <div class="row g-4">
                        @foreach($allMaterials as $materi)
                            <div class="col-md-4">
                                <div class="progress-item-card">
                                    <h4 class="progress-item-title">{{ $materi->title }}</h4>
                                    <div class="progress-container mt-3">
                                        <div class="progress-info d-flex justify-content-between">
                                            <span class="progress-text">Progress</span>
                                            <span class="progress-percentage">{{ $materi->progress }}%</span>
                                        </div>
                                        <div class="progress-bar-container">
                                            <div class="progress-bar" style="width: {{ $materi->progress }}%"></div>
                                        </div>
                                    </div>
                                    <div class="question-info mt-3">
                                        <i class="fas fa-question-circle"></i>
                                        <span>{{ $materi->questions_count }} Soal</span>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('mahasiswa.materials.show', $materi->id) }}" 
                                           class="btn btn-primary w-100">
                                            <i class="fas fa-book-reader me-2"></i>
                                            {{ $materi->progress == 100 ? 'Lihat Kembali Materi' : 'Mulai Belajar' }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('css')
<link href="{{ asset('css/mahasiswa.css') }}" rel="stylesheet">
@endpush
@endsection 