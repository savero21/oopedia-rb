@extends('mahasiswa.layouts.app')

@section('title', $material->title)

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

@push('css')
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
/* Updated styles */
.container-fluid {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 4rem;
}

.materi-heading {
    font-size: 2.5rem;
    font-weight: 700;
    color: #344767;
    margin-bottom: 0.5rem;
}

.heading-underline {
    width: 100px;
    height: 4px;
    background: linear-gradient(to right, #e91e63, #f5365c);
    border-radius: 2px;
}

.materi-card {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
    border-left: 5px solid transparent;
    transition: all 0.4s ease;
}

.materi-card:hover {
    border-left-color: #e91e63;
    transform: translateX(5px);
}

.materi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
    transform: translateX(-100%);
    transition: 0.6s;
}

.materi-card:hover::before {
    transform: translateX(100%);
}

.materi-card-body {
    padding: 2rem;
}

.content-text {
    color: #67748e;
    line-height: 1.8;
    font-size: 1.1rem;
    word-wrap: break-word;
    word-break: break-word;
    overflow-wrap: break-word;
    white-space: pre-wrap;
    max-width: 100%;
}

/* Tambahan untuk memastikan teks panjang wrap dengan baik */
.content-text p, 
.content-text div,
.content-text span {
    white-space: pre-wrap;
    word-wrap: break-word;
    overflow-wrap: break-word;
    max-width: 100%;
}

/* Styling untuk konten yang di-generate dari TinyMCE */
.content-text p {
    margin-bottom: 1.5rem;
}

.content-text img {
    max-width: 100%;
    height: auto;
    margin: 1rem 0;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.content-text img:hover {
    transform: scale(1.02);
}

.content-text ul,
.content-text ol {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}

.content-text li {
    margin-bottom: 0.5rem;
}

.content-text pre,
.content-text code {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    margin: 1rem 0;
    overflow-x: auto;
    font-size: 0.9rem;
}

.content-text table {
    width: 100%;
    margin: 1rem 0;
    border-collapse: collapse;
}

.content-text table th,
.content-text table td {
    padding: 0.75rem;
    border: 1px solid #dee2e6;
}

.content-text blockquote {
    background: linear-gradient(to right, rgba(233, 30, 99, 0.1), transparent);
    border-left: 4px solid #e91e63;
    padding: 1.5rem;
    margin: 1.5rem 0;
    border-radius: 0 10px 10px 0;
    font-style: italic;
    position: relative;
}

.content-text blockquote::before {
    content: '"';
    position: absolute;
    top: 0;
    left: 1rem;
    font-size: 3rem;
    color: #e91e63;
    opacity: 0.2;
}

/* Keep other existing styles */
.materi-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #344767;
}

.answer-option {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    border-left: 4px solid transparent;
    transform-origin: left;
}

.answer-option:hover {
    background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-left-color: #e91e63;
    transform: scale(1.01);
}

.answer-option input:checked + label {
    color: #e91e63;
    font-weight: 600;
}

.question-text {
    font-size: 1.1rem;
    color: #344767;
    font-weight: 500;
}

.badge {
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
    font-weight: 500;
}

.bg-gradient-primary {
    background: linear-gradient(to right, #e91e63, #f5365c);
    color: white;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(233, 30, 99, 0.4);
}

/* Animasi loading untuk progress */
@keyframes shimmer {
    0% { background-position: -1000px 0; }
    100% { background-position: 1000px 0; }
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: #e91e63;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #d81b60;
}

/* Animasi untuk feedback container */
@keyframes slideDown {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

#feedbackContainer {
    animation: slideDown 0.3s ease forwards;
}

/* Updated Feedback Styles */
.feedback-box {
    padding: 1.5rem;
    border-radius: 10px;
    margin-top: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.3s ease;
    opacity: 0;
    transform: translateY(-20px);
}

.feedback-box.show {
    opacity: 1;
    transform: translateY(0);
}

.feedback-box.correct {
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
}

.feedback-box.incorrect {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2);
}

.feedback-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.feedback-icon {
    font-size: 1.5rem;
}

.feedback-icon.correct {
    color: #28a745;
}

.feedback-icon.incorrect {
    color: #dc3545;
}

.feedback-message {
    font-weight: 500;
    font-size: 1.1rem;
}

.feedback-message.correct {
    color: #155724;
}

.feedback-message.incorrect {
    color: #721c24;
}

.feedback-button {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.feedback-button.correct {
    background: #28a745;
    color: white;
}

.feedback-button.incorrect {
    background: #dc3545;
    color: white;
}

.feedback-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

/* Styles for detailed results */
.results-container {
    background-color: #f8f9fa;
    border-radius: 10px;
    padding: 1.5rem;
    margin-top: 2rem;
}

.question-result {
    background-color: #fff;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    border-left: 4px solid #e9ecef;
}

.question-result.correct {
    border-left-color: #28a745;
}

.question-result.incorrect {
    border-left-color: #dc3545;
}

.answer-result, .correct-answer {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.text-success {
    color: #28a745;
}

.text-danger {
    color: #dc3545;
}

.text-info {
    color: #17a2b8;
}

.feedback-icon {
    margin: 2rem 0;
}

.feedback-icon i {
    font-size: 5rem;
}

.feedback-icon.correct i {
    color: #28a745;
}

.feedback-icon.incorrect i {
    color: #dc3545;
}

.btn-warning {
    color: #fff;
    background-color: #f6c23e;
    border-color: #f6c23e;
    transition: all 0.3s ease;
}

.btn-warning:hover {
    background-color: #e0a800;
    border-color: #d39e00;
    transform: translateY(-1px);
}

.btn-success {
    background-color: #28a745;
    color: #fff;
}

/* Tambahan style untuk review mode */
.question-review {
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.question-review:hover {
    background-color: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.correct-answer {
    background-color: #d4edda !important;
    border-left: 4px solid #28a745 !important;
}

.answer-option {
    background: #fff;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
    border-left: 4px solid transparent;
}

.answer-explanation {
    padding-left: 24px;
    border-left: 2px solid #e9ecef;
}

.correct-answer .answer-explanation {
    border-left-color: #28a745;
}

.alert-info {
    background-color: #e8f4f8;
    border-color: #bee5eb;
    color: #0c5460;
    border-radius: 8px;
    padding: 1rem;
    margin: 1rem 0;
}

.alert-info i {
    color: #17a2b8;
}

.alert {
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

.alert-danger {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

.feedback-container {
    transition: all 0.3s ease;
}

.feedback-icon {
    margin-bottom: 1.5rem;
}

.feedback-icon i {
    font-size: 3rem;
}

.floating-warning {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 2.5rem;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    z-index: 1000;
    min-width: 400px;
    max-width: 90%;
    animation: floatIn 0.3s ease-out;
}

.explanation {
    background-color: #f8f9fa;
    border-left: 4px solid #e9ecef;
    padding: 1rem;
    margin: 1rem 0;
    border-radius: 8px;
}

.explanation strong {
    color: #495057;
}

.text-success strong {
    color: #28a745;
}

.text-danger strong {
    color: #dc3545;
}

@keyframes floatIn {
    from {
        opacity: 0;
        transform: translate(-50%, -60%);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%);
    }
}

.overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 999;
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.exercise-feedback {
    position: relative;
    display: none;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background-color: #1a2035;
    color: white;
    border-radius: 10px;
    padding: 2rem;
    margin-top: 1rem;
    transition: none !important;
    animation: none !important;
    will-change: auto !important;
    transform: translateZ(0);
    backface-visibility: hidden;
}

.feedback-container {
    width: 100%;
    max-width: 600px;
    text-align: center;
    transition: none !important;
    animation: none !important;
    will-change: auto !important;
    transform: translateZ(0);
    backface-visibility: hidden;
}

.feedback-icon-container {
    display: flex;
    justify-content: center;
    margin: 1.5rem 0;
    transition: none !important;
    animation: none !important;
}

.feedback-icon {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: none !important;
    animation: none !important;
    will-change: auto !important;
    transform: translateZ(0);
    backface-visibility: hidden;
}

.feedback-icon.success {
    background-color: #00A67E;
}

.feedback-icon.error {
    background-color: #E94F37;
}

.feedback-icon i {
    font-size: 60px;
    color: white;
}

#tryAgainBtn, #nextQuestionBtn {
    background-color: #00A67E;
    border: none;
    padding: 0.5rem 2rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const questionForm = document.getElementById('questionForm');
    const feedbackElement = document.querySelector('.exercise-feedback');
    const feedbackStatus = document.getElementById('feedbackStatus');
    const feedbackIcon = document.getElementById('feedbackIcon');
    const feedbackDetails = document.getElementById('feedbackDetails');
    const tryAgainBtn = document.getElementById('tryAgainBtn');
    const retryQuestionBtn = document.getElementById('retryQuestionBtn');
    const nextQuestionBtn = document.getElementById('nextQuestionBtn');
    const checkAnswerBtn = document.getElementById('checkAnswerBtn');
    
    // Prevent any event bubbling that might cause flickering
    document.body.addEventListener('mouseover', function(e) {
        if (feedbackElement && feedbackElement.style.display === 'block') {
            e.stopPropagation();
        }
    }, true);
    
    if (questionForm && feedbackElement) {
        questionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            if (checkAnswerBtn) {
                checkAnswerBtn.disabled = true;
                checkAnswerBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memeriksa...';
            }
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const isSuccess = data.status === 'success';
                
                feedbackStatus.innerHTML = `<h3>${data.message}</h3>`;
                feedbackIcon.className = `feedback-icon ${isSuccess ? 'success' : 'error'}`;
                feedbackIcon.innerHTML = `<i class="fas ${isSuccess ? 'fa-check' : 'fa-times'} fa-3x"></i>`;
                
                let explanationHtml = '';
                
                if (!isSuccess && data.selectedExplanation) {
                    explanationHtml += `<div class="p-3" style="background: rgba(255,255,255,0.05); border-radius: 8px;">
                        <h5 class="mb-2">Penjelasan jawaban Anda:</h5>
                        <p>${data.selectedExplanation}</p>
                    </div>`;
                }
                
                feedbackDetails.innerHTML = explanationHtml;
                
                if (isSuccess) {
                    tryAgainBtn.style.display = 'none';
                    retryQuestionBtn.style.display = 'none';
                    nextQuestionBtn.style.display = 'inline-block';
                    nextQuestionBtn.onclick = function() {
                        window.location.href = data.nextUrl;
                    };
                } else {
                    tryAgainBtn.style.display = 'inline-block';
                    retryQuestionBtn.style.display = 'inline-block';
                    nextQuestionBtn.style.display = 'none';
                    tryAgainBtn.onclick = function() {
                        feedbackElement.style.display = 'none';
                        questionForm.style.display = 'block';
                    };
                    retryQuestionBtn.onclick = function() {
                        feedbackElement.style.display = 'none';
                        questionForm.style.display = 'block';
                    };
                }
                
                // Use requestAnimationFrame to ensure smooth rendering
                requestAnimationFrame(function() {
                    // Use block display instead of flex to reduce rendering complexity
                    feedbackElement.style.display = 'block';
                    questionForm.style.display = 'none';
                });
                
                // Reset button state
                if (checkAnswerBtn) {
                    checkAnswerBtn.disabled = false;
                    checkAnswerBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Periksa Jawaban';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                // Show error message
                alert('Terjadi kesalahan saat memeriksa jawaban. Silakan coba lagi.');
                
                // Reset button state
                if (checkAnswerBtn) {
                    checkAnswerBtn.disabled = false;
                    checkAnswerBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Periksa Jawaban';
                }
            });
        });
    }
});
</script>
@endpush
@endsection 