@extends('mahasiswa.layouts.app')

@section('title', 'Materi Selesai')

@section('content')
<div class="dashboard-header text-center">
    <h1 class="main-title">Materi Selesai</h1>
    <div class="title-underline"></div>
</div>

<div class="container-fluid">
    <div class="row g-4">
        @foreach($materials as $material)
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
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection 