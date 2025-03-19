@extends('mahasiswa.layouts.app')

@section('title', 'Materi Sedang Dipelajari')

@section('content')
<div class="dashboard-header text-center">
    <h1 class="main-title">Sedang Dipelajari</h1>
    <div class="title-underline"></div>
</div>

<div class="container-fluid">
    @if($materials->isEmpty())
        <div class="text-center mt-5">
            <div class="materi-card">
                <div class="materi-card-body">
                    <h3 class="materi-title">Belum Ada Materi yang Sedang Dipelajari</h3>
                    <div class="materi-description">
                        Anda belum mulai mempelajari materi apapun.
                    </div>
                    <a href="{{ route('mahasiswa.materials.index') }}" class="btn btn-update mt-3">
                        Jelajahi Materi
                    </a>
                </div>
            </div>
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
                                    {{ round(($material->progress->where('is_correct', true)->count() / $material->questions->count()) * 100) }}%
                                </span>
                            </div>
                            <div class="progress-bar-container">
                                <div class="progress-bar" style="width: {{ round(($material->progress->where('is_correct', true)->count() / $material->questions->count()) * 100) }}%"></div>
                            </div>
                        </div>
                        <div class="question-info mt-3">
                            <i class="fas fa-question-circle"></i>
                            <span>{{ $material->questions->count() }} Soal</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection