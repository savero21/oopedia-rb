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
            <div class="materi-card">
                <div class="materi-card-body">
                    <h3 class="materi-title">{{ $material['title'] }}</h3>
                    <div class="materi-description">
                        {{ $material['description'] }}
                    </div>
                    <div class="progress-container">
                        <div class="progress-info">
                            <span class="progress-text">Progres</span>
                            <span class="progress-percentage">{{ $material->progress_percentage }}%</span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar" style="width: {{ $material->progress_percentage }}%"></div>
                        </div>
                        <div class="progress-details mt-2">
                            <small>
                                {{ $material->completed_questions }} dari {{ $material->total_questions }} soal selesai
                            </small>
                        </div>
                        <a href="{{ route('mahasiswa.materials.show', $material) }}" class="btn btn-update w-100 mt-3">
                            {{ $material->progress_percentage == 100 ? 'Lihat Kembali Materi' : 'Mulai Belajar' }}
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
.progress-bar-container {
    width: 100%;
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(to right, #4e73df, #224abe);
    transition: width 0.3s ease;
}

.progress-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.progress-details {
    text-align: center;
    color: #6c757d;
}
</style>
@endpush
@endsection 