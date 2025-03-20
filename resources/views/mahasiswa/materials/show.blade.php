@extends('mahasiswa.layouts.app')

@section('title', $material->title)

@push('css')
<link rel="stylesheet" href="{{ asset('css/material-show.css') }}">
<link rel="stylesheet" href="{{ asset('css/question-review.css') }}">
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

    <!-- Content Section -->
    <div class="materi-card mb-4">
        <div class="materi-card-body">
            <div class="content-text">
                {!! $material->content !!}
            </div>
        </div>
    </div>

    <!-- Exercise Section -->
    <h2 class="section-heading mb-3">Latihan Soal</h2>
    
    @if($currentQuestionNumber === "Review")
        @include('mahasiswa.partials.question-review')
    @else
        @if($currentQuestion)
            @include('mahasiswa.partials.question')
        @else
            <div class="alert alert-info">
                Tidak ada soal tersedia untuk materi ini.
            </div>
        @endif
    @endif
</div>

@push('scripts')
<script>
function initializeQuestionForm() {
    const questionForm = document.getElementById('questionForm');
    if (questionForm) {
        questionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const checkAnswerBtn = document.getElementById('checkAnswerBtn');
            if (checkAnswerBtn) {
                checkAnswerBtn.disabled = true;
                checkAnswerBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memeriksa...';
            }

            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                const feedbackElement = document.querySelector('.exercise-feedback');
                const feedbackStatus = document.getElementById('feedbackStatus');
                const feedbackIcon = document.getElementById('feedbackIcon');
                const explanationBox = document.getElementById('explanationBox');
                const explanationText = document.getElementById('explanationText');
                const tryAgainBtn = document.getElementById('tryAgainBtn');
                const nextQuestionBtn = document.getElementById('nextQuestionBtn');

                // Set status dan icon
                feedbackStatus.innerHTML = `<h3 class="${data.status === 'success' ? 'text-success' : 'text-danger'}">${data.message}</h3>`;
                feedbackIcon.className = `feedback-icon ${data.status === 'success' ? 'success' : 'error'}`;
                feedbackIcon.innerHTML = `<i class="fas ${data.status === 'success' ? 'fa-check-circle' : 'fa-times-circle'} fa-3x"></i>`;

                // Tampilkan penjelasan
                if (data.status === 'success' && data.explanation) {
                    explanationText.innerHTML = data.explanation;
                    explanationBox.style.display = 'block';
                } else if (data.status === 'error' && data.selectedExplanation) {
                    explanationText.innerHTML = data.selectedExplanation;
                    explanationBox.style.display = 'block';
                } else {
                    explanationBox.style.display = 'none';
                }

                // Tampilkan tombol yang sesuai
                if (data.status === 'success') {
                    tryAgainBtn.style.display = 'none';
                    nextQuestionBtn.style.display = 'inline-block';
                    if (data.hasNextQuestion) {
                        nextQuestionBtn.onclick = () => {
                            // Save current scroll position
                            const currentScroll = window.scrollY;
                            
                            // Fetch next question via AJAX
                            fetch(data.nextUrl, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => response.text())
                            .then(html => {
                                // Find and update only the question container
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                const newQuestionContainer = doc.querySelector('#questionContainer');
                                const currentQuestionContainer = document.querySelector('#questionContainer');
                                
                                if (newQuestionContainer && currentQuestionContainer) {
                                    currentQuestionContainer.innerHTML = newQuestionContainer.innerHTML;
                                    
                                    // Hide feedback element
                                    const feedbackElement = document.querySelector('.exercise-feedback');
                                    if (feedbackElement) {
                                        feedbackElement.style.display = 'none';
                                    }
                                    
                                    // Show question form
                                    const questionForm = document.querySelector('#questionForm');
                                    if (questionForm) {
                                        questionForm.style.display = 'block';
                                    }
                                    
                                    // Restore scroll position
                                    window.scrollTo(0, currentScroll);
                                    
                                    // Reinitialize event listeners for the new form
                                    initializeQuestionForm();
                                } else {
                                    // Fallback to full page reload if containers not found
                                    window.location.href = data.nextUrl;
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                window.location.href = data.nextUrl; // Fallback to full page reload
                            });
                        };
                    } else {
                        nextQuestionBtn.innerHTML = '<i class="fas fa-check me-2"></i>Selesai';
                        nextQuestionBtn.onclick = () => window.location.reload();
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
}

document.addEventListener('DOMContentLoaded', function() {
    initializeQuestionForm();
});
</script>
@endpush
@endsection 