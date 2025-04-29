@extends('mahasiswa.layouts.app')

@section('title', 'Level Soal - ' . $material->title)

@section('content')
<div class="container-fluid">
    <div class="dashboard-header text-center">
        <h1 class="main-title">Level Soal: {{ $material->title }}</h1>
        <div class="title-underline"></div>
        <div class="difficulty-badge">
            <i class="fas fa-signal me-2"></i>
            <span>Tingkat Kesulitan: {{ ucfirst($difficulty) }}</span>
        </div>
    </div>

    <div class="level-container">
        <div class="level-legend mb-4">
            <div class="legend-title mb-3">Keterangan:</div>
            <div class="legend-items">
                <div class="legend-item">
                    <div class="legend-icon" style="background: #4CAF50;">
                        <span class="text-white"></span>
                    </div>
                    <div class="legend-text">Soal yang bisa dikerjakan</div>
                </div>
                <div class="legend-item">
                    <div class="legend-icon" style="background: #2196F3;">
                        <i class="fas fa-check text-white"></i>
                    </div>
                    <div class="legend-text">Soal yang sudah dijawab benar</div>
                </div>
                <div class="legend-item">
                    <div class="legend-icon" style="background: #e9ecef;">
                        <span style="color: #6c757d;"></span>
                    </div>
                    <div class="legend-text">Soal yang belum bisa diakses</div>
                </div>
                <div class="legend-item">
                    <div class="legend-icon trophy-circle" style="background: #e9ecef;">
                        <i class="fas fa-trophy" style="color: #adb5bd;"></i>
                    </div>
                    <div class="legend-text">Penghargaan setelah menyelesaikan semua soal</div>
                </div>
            </div>
        </div>

        <div class="level-header text-center mb-5">
            <div class="start-text">
                <span>START</span>
                <div class="start-line"></div>
            </div>
        </div>
        
        <div class="level-map">
            @foreach($levels as $index => $level)
                <div class="level-item {{ $level['status'] }}">
                    @if($level['status'] === 'locked')
                        <div class="level-circle">
                            <span class="level-number">{{ $level['level'] }}</span>
                        </div>
                    @elseif($level['status'] === 'completed')
                        <div class="level-circle completed">
                            <span class="level-number">{{ $level['level'] }}</span>
                            <i class="fas fa-check-circle completed-icon"></i>
                        </div>
                    @else
                        <a href="{{ route('mahasiswa.materials.questions.show', ['material' => $material->id, 'question' => $level['question_id'], 'difficulty' => $difficulty]) }}" class="level-link">
                            <div class="level-circle unlocked">
                                <span class="level-number">{{ $level['level'] }}</span>
                            </div>
                        </a>
                    @endif
                </div>
                
                @if($index < count($levels) - 1)
                    <div class="level-connector {{ $level['status'] === 'completed' ? 'completed' : '' }}"></div>
                @endif
            @endforeach
            
            <!-- Trophy icon at the end of levels -->
            <div class="level-item trophy {{ count(array_filter($levels, function($level) { return $level['status'] !== 'completed'; })) === 0 ? 'completed' : 'locked' }}">
                <div class="level-circle trophy-circle">
                    <i class="fas fa-trophy trophy-icon"></i>
                </div>
            </div>
        </div>
        
        <div class="level-actions mt-4">
            <a href="{{ route('mahasiswa.materials.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Materi
            </a>
        </div>
    </div>
</div>

<style>
    .level-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .level-header text-center mb-4 {
        background-color: #333;
        color: #4CAF50;
        padding: 10px 20px;
        border-radius: 10px;
        display: inline-block;
        font-weight: bold;
    }
    
    .level-map {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        margin: 40px 0;
        gap: 0;
    }
    
    .level-item {
        position: relative;
        z-index: 2;
        margin-bottom: 0;
    }
    
    /* Base styles untuk semua circle */
    .level-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: bold;
        transition: all 0.3s ease;
        position: relative;
        margin: 5px 0;
    }
    
    /* Soal yang sudah dijawab (completed) */
    .completed .level-circle {
        background: #4CAF50;
        color: white;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
    }
    
    .completed-icon {
        position: absolute;
        top: -8px;
        right: -8px;
        background: white;
        border-radius: 50%;
        padding: 4px;
        font-size: 16px;
        color: #4CAF50;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    /* Soal yang bisa dikerjakan (unlocked) */
    .unlocked .level-circle {
        background: #2196F3;
        color: white;
        box-shadow: 0 4px 15px rgba(33, 150, 243, 0.2);
        cursor: pointer;
    }
    
    .unlocked .level-circle:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(33, 150, 243, 0.3);
        background: #1976D2;
    }
    
    /* Connector styles */
    .level-connector {
        width: 4px;
        height: 40px;
        position: relative;
        background: #e9ecef;
        margin: -1px 0;
    }
    
    /* Connector yang completed */
    .level-connector.completed {
        background: linear-gradient(180deg, 
            #4CAF50 0%,
            #45a049 100%
        );
        box-shadow: 0 0 10px rgba(76, 175, 80, 0.2);
    }
    
    /* Connector yang belum completed (putus-putus) */
    .level-connector:not(.completed) {
        background: repeating-linear-gradient(
            to bottom,
            #e9ecef 0px,
            #e9ecef 4px,
            transparent 4px,
            transparent 8px
        );
        height: 40px;
    }
    
    /* Connector khusus ke trophy */
    .level-connector:last-of-type {
        height: 60px;
    }
    
    /* Connector terakhir ke trophy saat completed */
    .level-connector:last-of-type.completed {
        background: linear-gradient(180deg,
            #4CAF50 0%,
            #FFD700 100%
        );
        opacity: 1;
    }
    
    /* Connector terakhir ke trophy saat belum completed */
    .level-connector:last-of-type:not(.completed) {
        background: repeating-linear-gradient(
            to bottom,
            #e9ecef 0px,
            #e9ecef 4px,
            transparent 4px,
            transparent 8px
        );
        height: 60px;
    }
    
    /* Animasi subtle pada connector completed */
    @keyframes pulseConnector {
        0% { opacity: 0.8; }
        50% { opacity: 1; }
        100% { opacity: 0.8; }
    }
    
    .level-connector.completed {
        animation: pulseConnector 2s infinite;
    }
    
    .level-link {
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .level-actions {
        text-align: center;
        margin-top: 30px;
    }
    
    .btn-secondary {
        background-color: #6c757d;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        text-decoration: none;
    }
    
    .btn-secondary:hover {
        background-color: #5a6268;
    }

    .trophy-circle {
        width: 60px;
        height: 60px;
        margin-top: -5px;
    }

    .trophy.locked .trophy-circle {
        background: #e9ecef;
        border: 2px solid #dee2e6;
    }

    .trophy.locked .trophy-icon {
        color: #adb5bd;
        font-size: 24px;
    }

    .trophy.completed .trophy-circle {
        background: linear-gradient(145deg, #FFD700, #FFA500);
        box-shadow: 0 0 20px rgba(255, 215, 0, 0.6);
        animation: pulseTrophy 2s infinite;
    }

    .trophy-icon {
        font-size: 22px;
        color: #adb5bd;
    }

    .trophy.completed .trophy-icon {
        color: white;
        animation: rotateTrophy 3s infinite;
    }

    @keyframes pulseTrophy {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    @keyframes rotateTrophy {
        0% { transform: rotate(-15deg); }
        50% { transform: rotate(15deg); }
        100% { transform: rotate(-15deg); }
    }

    .trophy {
        margin-top: 20px;  /* Add space above the trophy */
    }

    .level-legend {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }

    .legend-items {
        display: flex;
        justify-content: center;
        gap: 25px;
        flex-wrap: wrap;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .legend-icon {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .difficulty-badge {
        display: inline-block;
        background: linear-gradient(135deg, #2196F3, #1976D2);
        color: white;
        padding: 8px 20px;
        border-radius: 25px;
        margin-top: 15px;
        box-shadow: 0 4px 15px rgba(33, 150, 243, 0.2);
        font-weight: 500;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .difficulty-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(33, 150, 243, 0.3);
    }

    .start-container {
        display: inline-block;
        padding: 10px 40px;
        background: linear-gradient(135deg, #4CAF50, #45a049);
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);
        transition: all 0.3s ease;
    }

    .start-title {
        color: white;
        font-size: 24px;
        font-weight: bold;
        margin: 0;
        letter-spacing: 2px;
    }

    .start-underline {
        width: 50%;
        height: 3px;
        background: rgba(255, 255, 255, 0.5);
        margin: 5px auto 0;
        border-radius: 2px;
    }

    .start-container:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3);
    }

    .start-button {
        background: #4CAF50;
        padding: 15px 60px;
        border-radius: 50px;
        display: inline-block;
        position: relative;
        transition: all 0.3s ease;
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.25);
        cursor: pointer;
        overflow: hidden;
    }

    .start-text {
        color: #0D47A1;
        font-size: 32px;
        font-weight: 800;
        letter-spacing: 6px;
        text-transform: uppercase;
        position: relative;
        display: inline-block;
        padding: 0 15px;
        text-shadow: 
            2px 2px 0 #1976D2,
            -2px -2px 0 #1976D2,
            2px -2px 0 #1976D2,
            -2px 2px 0 #1976D2;
        animation: startGlow 2s ease-in-out infinite;
    }

    .start-line {
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, 
            transparent 0%,
            #0D47A1 50%,
            transparent 100%
        );
        margin-top: 8px;
        opacity: 0.9;
    }

    @keyframes startGlow {
        0% {
            text-shadow: 
                2px 2px 0 #1976D2,
                -2px -2px 0 #1976D2,
                2px -2px 0 #1976D2,
                -2px 2px 0 #1976D2,
                0 0 10px rgba(25, 118, 210, 0.5);
        }
        50% {
            text-shadow: 
                2px 2px 0 #1976D2,
                -2px -2px 0 #1976D2,
                2px -2px 0 #1976D2,
                -2px 2px 0 #1976D2,
                0 0 20px rgba(25, 118, 210, 0.8);
        }
        100% {
            text-shadow: 
                2px 2px 0 #1976D2,
                -2px -2px 0 #1976D2,
                2px -2px 0 #1976D2,
                -2px 2px 0 #1976D2,
                0 0 10px rgba(25, 118, 210, 0.5);
        }
    }

    @keyframes gentlePulse {
        0% {
            opacity: 0.7;
            transform: scaleX(0.95);
        }
        50% {
            opacity: 1;
            transform: scaleX(1);
        }
        100% {
            opacity: 0.7;
            transform: scaleX(0.95);
        }
    }

    .start-line {
        animation: gentlePulse 3s ease-in-out infinite;
    }

    .start-button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(
            90deg,
            transparent,
            rgba(255, 255, 255, 0.2),
            transparent
        );
        transition: 0.5s;
    }

    .start-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(76, 175, 80, 0.35);
        background: #43A047;
    }

    .start-button:hover::before {
        left: 100%;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.25);
        }
        50% {
            box-shadow: 0 8px 25px rgba(76, 175, 80, 0.4);
        }
        100% {
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.25);
        }
    }

    .start-button {
        animation: pulse 2s infinite;
    }

    .start-label {
        background: #4CAF50;
        padding: 12px 50px;
        border-radius: 50px;
        display: inline-block;
        position: relative;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);
        pointer-events: none;
    }

    .start-text {
        color: white;
        font-size: 22px;
        font-weight: 600;
        letter-spacing: 2px;
        text-transform: uppercase;
    }

    /* Subtle animation untuk menunjukkan ini adalah label */
    @keyframes gentlePulse {
        0% {
            transform: scale(1);
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);
        }
        50% {
            transform: scale(1.02);
            box-shadow: 0 4px 20px rgba(76, 175, 80, 0.25);
        }
        100% {
            transform: scale(1);
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);
        }
    }

    .start-label {
        animation: gentlePulse 3s infinite ease-in-out;
    }
</style>
@endsection 