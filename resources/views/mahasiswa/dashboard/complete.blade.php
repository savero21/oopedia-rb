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
<link href="{{ asset('css/mahasiswa.css') }}" rel="stylesheet">
@endpush
@endsection 