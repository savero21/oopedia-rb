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

<div class="welcome-banner">
    <div class="welcome-content">
        <div class="welcome-icon">
            <i class="fas fa-hand-sparkles"></i>
        </div>
        <div class="welcome-text">
            <h2 class="welcome-title">Selamat Datang Kembali,</h2>
            <h3 class="welcome-name">{{ auth()->user()->name }}</h3>
            <p class="welcome-message">Lanjutkan perjalanan belajar Anda hari ini!</p>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row g-4">
        <!-- Material Overview (Modified) -->
        <div class="col-md-6">
            <div class="materi-card">
                <div class="materi-card-body">
                    <h3 class="materi-title">Materi Pembelajaran</h3>
                    <div class="materi-overview">
                        <div class="materi-count">
                            <i class="fas fa-book me-2"></i>
                            <span class="count-number">{{ $totalMaterials }}</span>
                        </div>
                        <p class="materi-description">Total materi tersedia untuk dipelajari</p>
                        <div class="button-container">
                            <a href="{{ route('mahasiswa.materials.index') }}" class="btn btn-primary w-100">
                                <i class="fas fa-book me-2"></i>Lihat Semua Materi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Question Progress Overview -->
        <div class="col-md-6">
            <div class="materi-card">
                <div class="materi-card-body">
                    <h3 class="materi-title">Latihan Soal</h3>
                    <div class="materi-overview">
                        <div class="materi-count">
                            <i class="fas fa-question-circle me-2"></i>
                            <span class="count-number">{{ $totalQuestions }}</span>
                        </div>
                        <p class="materi-description">Total soal tersedia untuk latihan</p>
                        <div class="difficulty-breakdown">
                            <div class="difficulty-item">
                                <span class="badge bg-success">Beginner</span>
                                <span class="difficulty-count">{{ $easyQuestions }}</span>
                            </div>
                            <div class="difficulty-item">
                                <span class="badge bg-warning">Medium</span>
                                <span class="difficulty-count">{{ $mediumQuestions }}</span>
                            </div>
                            <div class="difficulty-item">
                                <span class="badge bg-danger">Hard</span>
                                <span class="difficulty-count">{{ $hardQuestions }}</span>
                            </div>
                        </div>
                        <div class="button-container">
                            <a href="{{ route('mahasiswa.materials.questions.index') }}" class="btn btn-primary w-100">
                                <i class="fas fa-question-circle me-2"></i>Latihan Soal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-12">
            <div class="materi-card">
                <div class="materi-card-body">
                    <h3 class="materi-title">Aktivitas Terbaru</h3>
                    <div class="activity-timeline">
                        @forelse($recentActivities as $activity)
                            <div class="activity-item">
                                <div class="activity-icon 
                                    @if($activity->type === 'achievement') bg-success
                                    @elseif($activity->type === 'milestone') bg-warning
                                    @else bg-info @endif">
                                    @if($activity->type === 'achievement')
                                        <i class="fas fa-trophy" style="color: white;"></i>
                                    @elseif($activity->type === 'milestone')
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="fas fa-tasks"></i>
                                    @endif
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">
                                        @if($activity->type === 'achievement')
                                            Pencapaian Baru!
                                        @elseif($activity->type === 'milestone')
                                            Milestone Tercapai!
                                        @else
                                            Progress Pembelajaran
                                        @endif
                                    </div>
                                    <div class="activity-details">
    @if($activity->type === 'achievement')
        Menyelesaikan {{ $activity->total_correct }} soal di materi 
        <span class="fw-bold">{{ $activity->material_title }}</span>
    @elseif($activity->type === 'milestone')
        Berhasil menyelesaikan soal level hard di materi 
        <span class="fw-bold">{{ $activity->material_title }}</span>
    @else
        Mengerjakan soal {{ $activity->difficulty }} di materi 
        <span class="fw-bold">{{ $activity->material_title }}</span>
    @endif
</div>
                                    <div class="activity-time">
                                        {{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">
                                Belum ada aktivitas
                            </div>
                        @endforelse
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