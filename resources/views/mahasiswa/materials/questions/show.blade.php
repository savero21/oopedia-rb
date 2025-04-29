@extends('mahasiswa.layouts.app')

@section('title', 'Latihan Soal - ' . $material->title)

@push('css')
<link rel="stylesheet" href="{{ asset('css/material-show.css') }}">
<link rel="stylesheet" href="{{ asset('css/question-review.css') }}">
<style>
    .materi-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
    }
    
    .materi-card-body {
        padding: 25px;
    }
    
    .question-text {
        font-size: 16px;
        line-height: 1.6;
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
    }
    
    .answer-option {
        transition: all 0.2s ease;
        cursor: pointer;
        background-color: #f8f9fa;
    }
    
    .answer-option:hover {
        background-color: #e9ecef;
    }
    
    /* Enhanced feedback styling */
    .exercise-feedback {
        max-width: 600px;
        margin: 0 auto;
        background-color: #1e2a3a;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        color: white;
    }
    
    .feedback-container {
        padding: 30px;
        text-align: center;
    }
    
    .feedback-icon {
        margin: 0 auto 20px;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .feedback-icon.success {
        background-color: #00c07f;
    }
    
    .feedback-icon.error {
        background-color: #ff5a5a;
    }
    
    .feedback-icon i {
        font-size: 50px;
        color: white;
    }
    
    .feedback-status h3 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 20px;
    }
    
    .text-success {
        color: #00c07f !important;
    }
    
    .text-danger {
        color: #ff5a5a !important;
    }
    
    .explanation-box {
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        color: #e0e0e0;
        margin-top: 20px;
        padding: 15px;
    }
    
    .explanation-box h5 {
        color: white;
        font-size: 18px;
        margin-bottom: 10px;
    }
    
    .feedback-actions {
        margin-top: 25px;
    }
    
    .feedback-actions .btn {
        padding: 10px 25px;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    
    .feedback-actions .btn:hover {
        transform: translateY(-2px);
    }
    
    .btn-outline-light {
        color: white;
        border-color: rgba(255, 255, 255, 0.5);
    }
    
    .btn-outline-light:hover {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: white;
    }
    
    .btn-success {
        background-color: #00c07f;
        border-color: #00c07f;
    }
    
    .btn-success:hover {
        background-color: #00a06a;
        border-color: #00a06a;
    }
    
    /* Filter buttons */
    .filter-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .filter-buttons .btn {
        border-radius: 8px;
        padding: 8px 20px;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .filter-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    /* Button colors */
    .btn-primary, .btn-outline-primary:hover {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .btn-success, .btn-outline-success:hover {
        background-color: #198754;
        border-color: #198754;
    }
    
    .btn-warning, .btn-outline-warning:hover {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #212529;
    }
    
    .btn-danger, .btn-outline-danger:hover {
        background-color: #dc3545;
        border-color: #dc3545;
    }
    
    /* Trophy styles */
    .trophy-circle {
        background-color: #444;
        transition: all 0.3s ease;
    }
    
    .trophy-icon-disabled {
        color: #777;
        font-size: 20px;
    }
    
    .trophy-icon {
        color: #FFD700;
        font-size: 20px;
        text-shadow: 0 0 10px #FFD700;
        animation: glow 1.5s infinite alternate;
    }
    
    @keyframes glow {
        from {
            text-shadow: 0 0 5px #FFD700, 0 0 10px #FFD700;
        }
        to {
            text-shadow: 0 0 10px #FFD700, 0 0 20px #FFD700, 0 0 30px #FFD700;
        }
    }
    
    .trophy.completed .trophy-circle {
        background-color: #444;
        border: 2px solid #FFD700;
        box-shadow: 0 0 15px rgba(255, 215, 0, 0.5);
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <h1 class="materi-heading">Latihan Soal: {{ $material->title }}</h1>
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
            Anda hanya dapat melihat sebagian dari soal latihan ini. Untuk akses penuh, silakan 
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

    @if($currentQuestion)
        @include('mahasiswa.partials.question')
    @else
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
            </div>
            <h3 class="mb-3">Tidak Ada Soal Tersedia</h3>
            <p class="text-muted mb-4">
                @if(request()->query('difficulty'))
                    Tidak ada soal tersisa untuk tingkat kesulitan ini.
                @else
                    Anda telah menyelesaikan semua soal pada materi ini.
                @endif
            </p>
            <div class="mt-4">
                <a href="{{ route('mahasiswa.materials.show', $material->id) }}" class="btn btn-primary me-2">
                    <i class="fas fa-book me-2"></i>Kembali ke Materi
                </a>
                <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-home me-2"></i>Dashboard
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function redirectAfterCompletion() {
    window.location.href = "{{ route('mahasiswa.materials.show', $material->id) }}";
}

function initializeQuestionForm() {
    const questionForm = document.getElementById('questionForm');
    const checkAnswerBtn = document.getElementById('checkAnswerBtn');
    const feedbackElement = document.querySelector('.exercise-feedback');
    const feedbackIcon = document.getElementById('feedbackIcon');
    const feedbackStatus = document.getElementById('feedbackStatus');
    const tryAgainBtn = document.getElementById('tryAgainBtn');
    const nextQuestionBtn = document.getElementById('nextQuestionBtn');
    const explanationBox = document.getElementById('explanationBox');
    const explanationText = document.getElementById('explanationText');
    const isGuest = {{ auth()->user()->role_id === 4 ? 'true' : 'false' }};
    const currentQuestionNumber = {{ $currentQuestionNumber ?? 1 }};
    
    // Fungsi untuk menampilkan pesan semua soal telah terjawab
    function showAllQuestionsCompleted() {
        // Sembunyikan form soal dan feedback
        if (questionForm) {
            questionForm.style.display = 'none';
        }
        if (feedbackElement) {
            feedbackElement.style.display = 'none';
        }
        
        // Tampilkan pesan semua soal telah terjawab
        const questionContainer = document.getElementById('questionContainer');
        if (questionContainer) {
            questionContainer.innerHTML = `
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h3 class="mb-3">Selamat! Semua Soal Telah Terjawab</h3>
                    <p class="text-muted mb-4">Anda telah menyelesaikan semua soal pada materi ini.</p>
                    <div class="mt-4">
                        <a href="{{ route('mahasiswa.materials.show', $material->id) }}" class="btn btn-primary me-2">
                            <i class="fas fa-book me-2"></i>Kembali ke Materi
                        </a>
                        <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a>
                    </div>
                </div>
            `;
        }
    }
    
    if (questionForm) {
        questionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (checkAnswerBtn) {
                checkAnswerBtn.disabled = true;
                checkAnswerBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memeriksa...';
            }
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Set icon dan status berdasarkan hasil
                if (data.status === 'success') {
                    feedbackIcon.innerHTML = `<div class="rounded-circle bg-success d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;"><i class="fas fa-check-circle text-white" style="font-size: 40px;"></i></div>`;
                    feedbackStatus.innerHTML = `
                        <h3 class="feedback-title correct">Jawaban Benar!</h3>
                        <p class="feedback-message">${data.message || 'Jawaban Anda benar.'}</p>
                    `;
                    feedbackElement.classList.add('correct');
                    feedbackElement.classList.remove('incorrect');
                } else {
                    feedbackIcon.innerHTML = `<div class="rounded-circle bg-danger d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;"><i class="fas fa-times-circle text-white" style="font-size: 40px;"></i></div>`;
                    feedbackStatus.innerHTML = `
                        <h3 class="feedback-title incorrect">Jawaban Salah</h3>
                        <p class="feedback-message">${data.message || 'Jawaban Anda salah.'}</p>
                    `;
                    feedbackElement.classList.add('incorrect');
                    feedbackElement.classList.remove('correct');
                }
                
                // Sembunyikan penjelasan (tidak ditampilkan lagi)
                explanationBox.style.display = 'none';

                // Tampilkan tombol yang sesuai
                if (data.status === 'success') {
                    tryAgainBtn.style.display = 'none';
                    nextQuestionBtn.style.display = 'inline-block';
                    
                    if (data.hasNextQuestion) {
                        // Jika masih ada soal berikutnya
                        nextQuestionBtn.innerHTML = 'Lanjut ke Soal Berikutnya <i class="fas fa-arrow-right ms-2"></i>';
                        
                        // Tambahkan parameter difficulty jika ada
                        let nextUrl = data.nextUrl;
                        if (data.difficulty && data.difficulty !== 'all') {
                            nextUrl += `?difficulty=${data.difficulty}`;
                        }
                        
                        nextQuestionBtn.onclick = () => window.location.href = nextUrl;
                    } else {
                        // Jika tidak ada soal berikutnya, tampilkan tombol Selesai
                        nextQuestionBtn.innerHTML = '<i class="fas fa-check me-2"></i>Selesai';
                        nextQuestionBtn.onclick = () => showAllQuestionsCompleted();
                    }
                } else {
                    tryAgainBtn.style.display = 'inline-block';
                    nextQuestionBtn.style.display = 'none';
                    tryAgainBtn.onclick = () => {
                        feedbackElement.style.display = 'none';
                        questionForm.style.display = 'block';
                        checkAnswerBtn.disabled = false;
                        checkAnswerBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Periksa Jawaban';
                    };
                }

                feedbackElement.style.display = 'block';
                questionForm.style.display = 'none';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memeriksa jawaban. Silakan coba lagi.');
                if (checkAnswerBtn) {
                    checkAnswerBtn.disabled = false;
                    checkAnswerBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Periksa Jawaban';
                }
            });
        });
    }
    
    // Make the entire answer option clickable
    const answerOptions = document.querySelectorAll('.answer-option');
    answerOptions.forEach(option => {
        option.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
            }
        });
    });
}

// Cek apakah tidak ada soal yang ditampilkan (berarti semua sudah terjawab)
document.addEventListener('DOMContentLoaded', function() {
    // Jika tidak ada form soal, berarti semua soal sudah terjawab
    const questionForm = document.getElementById('questionForm');
    const noQuestionsMessage = document.querySelector('.alert.alert-info');
    
    if (!questionForm && noQuestionsMessage) {
        // Ganti pesan default dengan tampilan yang lebih menarik
        const materiCardBody = document.querySelector('.materi-card-body');
        if (materiCardBody) {
            materiCardBody.innerHTML = `
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h3 class="mb-3">Selamat! Semua Soal Telah Terjawab</h3>
                    <p class="text-muted mb-4">Anda telah menyelesaikan semua soal pada materi ini.</p>
                    <div class="mt-4">
                        <a href="{{ route('mahasiswa.materials.show', $material->id) }}" class="btn btn-primary me-2">
                            <i class="fas fa-book me-2"></i>Kembali ke Materi
                        </a>
                        <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a>
                    </div>
                </div>
            `;
        }
    }
    
    // Initialize form
    initializeQuestionForm();
});

function showQuestionReview(difficulty) {
    const mainContainer = document.querySelector('.container-fluid');
    
    // Show loading state
    mainContainer.innerHTML = `
        <div class="text-center my-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Memuat review soal...</p>
        </div>
    `;
    
    // Fetch the review content
    const url = `{{ route('mahasiswa.materials.questions.review', $material->id) }}${difficulty && difficulty !== 'all' ? `?difficulty=${difficulty}` : ''}`;
    
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        mainContainer.innerHTML = `
            <h1 class="materi-heading">Review Soal: {{ $material->title }}</h1>
            <div class="heading-underline mb-4"></div>
            
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="review-content">
                                ${html}
                            </div>
                            <div class="mt-4 text-center">
                                <a href="{{ route('mahasiswa.materials.show', $material->id) }}" class="btn btn-primary">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Materi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    })
    .catch(error => {
        console.error('Error:', error);
        mainContainer.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                Terjadi kesalahan saat memuat review soal. 
                <a href="{{ route('mahasiswa.materials.show', $material->id) }}" class="btn btn-sm btn-primary ms-3">
                    Kembali ke Materi
                </a>
            </div>
        `;
    });
}

// Only include question-specific JavaScript if there is a current question
@if($currentQuestion)
function submitAnswer() {
    const selectedOption = document.querySelector('input[name="answer"]:checked');
    if (!selectedOption) {
        showAlert('Silakan pilih jawaban terlebih dahulu', 'warning');
        return;
    }

    // Disable submit button
    const submitButton = document.getElementById('checkAnswerBtn');
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memeriksa...';
    }

    // Get attempts count first
    fetch(`{{ route('mahasiswa.materials.questions.attempts', ['material' => $material->id, 'question' => $currentQuestion->id]) }}`)
    .then(response => response.json())
    .then(data => {
        const attemptCount = data.attempts;
        
        // Calculate potential score based on attempts
        let potentialScore = 0;
        const difficulty = '{{ $currentQuestion->difficulty }}';
        
        if (difficulty === 'beginner') {
            potentialScore = attemptCount === 0 ? 3 : (attemptCount === 1 ? 2 : 1);
        } else if (difficulty === 'medium') {
            potentialScore = attemptCount === 0 ? 6 : (attemptCount === 1 ? 4 : 2);
        } else if (difficulty === 'hard') {
            potentialScore = attemptCount === 0 ? 9 : (attemptCount === 1 ? 6 : (attemptCount === 2 ? 4 : 2));
        }

        // Submit answer
        fetch('{{ route('mahasiswa.materials.questions.check', ['material' => $material->id, 'question' => $currentQuestion->id]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                answer: selectedOption.value,
                attempts: attemptCount + 1,
                potential_score: potentialScore,
                difficulty: '{{ request()->query('difficulty', 'all') }}'
            })
        })
        .then(response => response.json())
        .then(result => {
            showFeedback(result, potentialScore, attemptCount + 1);
            
            if (submitButton && !result.status === 'success') {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-check-circle me-2"></i>Periksa Jawaban';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan saat memeriksa jawaban', 'error');
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-check-circle me-2"></i>Periksa Jawaban';
            }
        });
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Terjadi kesalahan saat mengambil data percobaan', 'error');
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fas fa-check-circle me-2"></i>Periksa Jawaban';
        }
    });
}
@endif

function showFeedback(result, score, attemptNumber) {
    const feedbackElement = document.querySelector('.exercise-feedback');
    
    // Jika jawaban benar, langsung arahkan ke halaman level
    if (result.status === 'success') {
        // Tampilkan pesan sukses sebentar
        Swal.fire({
            title: 'Jawaban Benar!',
            text: result.message,
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            // Redirect ke halaman level
            window.location.href = result.nextUrl;
        });
    } else {
        // Jika jawaban salah, tampilkan feedback seperti biasa
        feedbackElement.innerHTML = `
            <div class="feedback-container ${result.status}">
                <div class="feedback-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h3 class="feedback-title">Jawaban Salah</h3>
                <p class="feedback-message">${result.message}</p>
                <div class="feedback-actions">
                    <button onclick="retryQuestion()" class="btn btn-warning">
                        <i class="fas fa-redo me-2"></i>Coba Lagi
                    </button>
                </div>
            </div>
        `;
        feedbackElement.style.display = 'block';
    }
}
</script>
@endpush 