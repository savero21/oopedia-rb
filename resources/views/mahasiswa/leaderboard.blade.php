@extends('mahasiswa.layouts.app')

@section('title', 'Leaderboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4 leaderboard-card">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <div class="d-flex align-items-center justify-content-between px-3">
                            <div class="d-flex align-items-center">
                                <div class="trophy-icon-container me-3">
                                    <i class="fas fa-trophy trophy-icon"></i>
                                </div>
                                <div>
                                    <h4 class="text-dark text-capitalize mb-0 leaderboard-title">Leaderboard</h4>
                                    <p class="text-dark text-sm mb-0 opacity-8">Peringkat Terbaik Mahasiswa</p>
                                </div>
                            </div>
                            <div class="leaderboard-decoration">
                                <span class="medal-badge medal-gold">🥇</span>
                                <span class="medal-badge medal-silver">🥈</span>
                                <span class="medal-badge medal-bronze">🥉</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body px-0 pb-2">
                    @if($currentUserRank)
                    <div class="current-user-rank p-4 mb-4 rounded mx-3">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-3 text-dark fw-bold">Peringkat Anda</h5>
                                <div class="d-flex align-items-center">
                                    <div class="rank-number me-3">
                                        <div class="rank-badge">
                                            <span>{{ $currentUserRank->rank }}</span>
                                        </div>
                                    </div>
                                    <div class="user-info">
                                        <h6 class="mb-0">{{ $currentUserRank->name }}</h6>
                                        <p class="mb-0">{{ $currentUserRank->email }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                <div class="stats">
                                    <div class="d-flex justify-content-md-end align-items-center mb-2">
                                        <span class="level-badge level-{{ $currentUserRank->badge_color }}">{{ $currentUserRank->badge }}</span>
                                    </div>
                                    <div class="progress-container">
                                        <div class="progress leaderboard-progress">
                                            <div class="progress-bar leaderboard-progress-bar bg-gradient-{{ $currentUserRank->badge_color }}" role="progressbar" style="width: {{ $currentUserRank->percentage }}%;"></div>
                                        </div>
                                        <div class="progress-percentage text-end mt-1">
                                            <span class="text-sm font-weight-bold">{{ $currentUserRank->percentage }}%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                   
                    @endif
                    
                    <div class="table-responsive p-0 mx-3">
                        <div class="animated-border-table">
                            <table class="table leaderboard-table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">PERINGKAT</th>
                                        <th class="text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">MAHASISWA</th>
                                        <th class="text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">LEVEL</th>
                                        <th class="text-center text-uppercase text-xxs font-weight-bolder opacity-7">TANGGAL SELESAI</th>
                                        <th class="text-center text-uppercase text-xxs font-weight-bolder opacity-7">PROGRESS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leaderboardData as $data)
                                    <tr class="leaderboard-row @if($data->id === auth()->id()) highlight-row @endif">
                                        <td>
                                            <div class="d-flex px-3 py-2 justify-content-center">
                                                @if($data->rank <= 3)
                                                <div class="top-rank rank-{{ $data->rank }}">{{ $data->rank }}</div>
                                                @else
                                                <span class="font-weight-bold">{{ $data->rank }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex px-2 py-2">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $data->name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="px-2 py-2">
                                                <span class="level-badge level-{{ $data->badge_color }}">{{ $data->badge }}</span>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            @if($data->completion_date)
                                                <span class="completion-date completed">
                                                    <i class="fas fa-calendar-check"></i> {{ date('d M Y', strtotime($data->completion_date)) }}
                                                </span>
                                            @else
                                                <span class="completion-date not-completed">
                                                    <i class="fas fa-hourglass-half"></i> Belum selesai
                                                </span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <div class="progress-wrapper mx-auto" style="width: 80%">
                                                <div class="progress leaderboard-progress">
                                                    <div class="progress-bar leaderboard-progress-bar bg-gradient-{{ $data->badge_color }}" style="width: {{ $data->percentage }}%;"></div>
                                                </div>
                                                <div class="progress-percentage text-center mt-1">
                                                    <span class="text-sm font-weight-bold">{{ $data->percentage }}%</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{ asset('css/mahasiswa.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
<script>
    // Konfeti untuk peringkat teratas
    document.addEventListener('DOMContentLoaded', function() {
        @if($currentUserRank && $currentUserRank->rank <= 3)
            // Konfeti untuk peringkat 1-3
            const colors = [
                ['#FFD700', '#FFC107'], // Gold
                ['#C0C0C0', '#9E9E9E'], // Silver
                ['#CD7F32', '#BF360C']  // Bronze
            ];
            
            const selectedColors = colors[{{ $currentUserRank->rank - 1 }}];
            
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 },
                colors: selectedColors,
                disableForReducedMotion: true
            });
        @endif
    });
</script>
@endpush 