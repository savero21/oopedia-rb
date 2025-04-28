@extends('mahasiswa.layouts.app')

@section('title', $material->title)

@push('css')
<link rel="stylesheet" href="{{ asset('css/material-show.css') }}">
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Judul Materi -->
    <h1 class="materi-heading">{{ $material->title }}</h1>
    <div class="heading-underline mb-4"></div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(auth()->check() && auth()->user()->role_id === 4)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Mode Tamu Aktif!</strong> 
            Anda hanya dapat melihat sebagian dari konten materi ini. Untuk akses penuh, silakan 
            <a href="{{ route('login') }}" class="alert-link" onclick="event.preventDefault(); document.getElementById('guest-logout-login-form').submit();">login</a> 
            atau 
            <a href="{{ route('register') }}" class="alert-link" onclick="event.preventDefault(); document.getElementById('guest-logout-register-form').submit();">daftar</a> 
            sebagai mahasiswa.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <!-- Hidden forms for guest logout and redirect -->
        <form id="guest-logout-login-form" action="{{ route('guest.logout') }}" method="POST" style="display: none;">
            @csrf
            <input type="hidden" name="redirect" value="{{ route('login') }}">
        </form>

        <form id="guest-logout-register-form" action="{{ route('guest.logout') }}" method="POST" style="display: none;">
            @csrf
            <input type="hidden" name="redirect" value="{{ route('register') }}">
        </form>
    @endif

    <!-- Content Section -->
    <div class="materi-card mb-4">
        <div class="materi-card-body">
            <div class="content-text">
                {!! $material->content !!}
            </div>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="d-flex justify-content-between mt-4 mb-5">
        <a href="{{ route('mahasiswa.materials.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Materi
        </a>
        <a href="{{ route('mahasiswa.materials.questions.show', $material->id) }}" class="btn btn-primary">
            Latihan Soal<i class="fas fa-arrow-right ms-2"></i>
        </a>
    </div>
</div>
@endsection 