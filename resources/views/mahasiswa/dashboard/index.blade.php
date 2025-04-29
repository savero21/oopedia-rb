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
        <!-- Material Progress Overview -->
        <div class="col-md-6">
            <div class="materi-card">
                <div class="materi-card-body">
                    <h3 class="materi-title">Progress Materi</h3>
                    <div class="progress-container">
                        <div class="progress-info">
                            <span class="progress-text">Total Materi</span>
                            <span class="progress-percentage">{{ $materialProgressPercentage }}%</span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar" style="width: {{ $materialProgressPercentage }}%"></div>
                        </div>
                        <p class="progress-detail">{{ $completedMaterials }} dari {{ $totalMaterials }} materi selesai</p>
                        <a href="{{ route('mahasiswa.materials.index') }}" class="btn btn-primary w-100 mt-3">
                            <i class="fas fa-book me-2"></i>Lihat Semua Materi
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Question Progress Overview -->
        <div class="col-md-6">
            <div class="materi-card">
                <div class="materi-card-body">
                    <h3 class="materi-title">Progress Soal</h3>
                    <div class="progress-container">
                        <div class="progress-info">
                            <span class="progress-text">Total Soal</span>
                            <span class="progress-percentage">{{ $questionProgressPercentage }}%</span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar" style="width: {{ $questionProgressPercentage }}%"></div>
                        </div>
                        <p class="progress-detail">{{ $totalCorrectQuestions }} dari {{ $totalQuestions }} soal selesai</p>
                        <a href="{{ route('mahasiswa.materials.questions.index') }}" class="btn btn-primary w-100 mt-3">
                            <i class="fas fa-question-circle me-2"></i>Latihan Soal
                        </a>
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
                                        <i class="fas fa-trophy"></i>
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
        Berhasil menyelesaikan soal level sulit di materi 
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

        <!-- All Materials Progress -->
        <div class="col-12">
            <div class="materi-card">
                <div class="materi-card-body">
                    <h3 class="materi-title">Progress Per Materi</h3>
                    <div class="row g-4">
                        @foreach($allMaterials as $material)
                            <div class="col-md-4">
                                <div class="progress-item-card">
                                    <h4 class="progress-item-title">{{ $material->title }}</h4>
                                    <div class="progress-container mt-3">
                                        <div class="progress-info d-flex justify-content-between">
                                            <span class="progress-text">Progress</span>
                                            <span class="progress-percentage">{{ $material->progress }}%</span>
                                        </div>
                                        <div class="progress-bar-container">
                                            <div class="progress-bar" style="width: {{ $material->progress }}%"></div>
                                        </div>
                                    </div>
                                    <div class="question-info mt-3">
                                        <i class="fas fa-check-circle text-success"></i>
                                        <span>{{ $material->correct_answers }} / {{ $material->total_questions }} Soal</span>
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