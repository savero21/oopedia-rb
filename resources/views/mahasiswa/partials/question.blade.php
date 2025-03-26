<div class="materi-card">
    <div class="materi-card-body">
        <div id="questionContainer">
            <form id="questionForm" action="{{ route('mahasiswa.questions.check-answer') }}" method="POST">
                @csrf
                <input type="hidden" name="question_id" value="{{ $currentQuestion->id }}">
                <input type="hidden" name="material_id" value="{{ $material->id }}">
                
                <div class="question-header">
                    <span class="badge bg-gradient-primary">
                        <i class="fas fa-question-circle me-2"></i>
                        Soal {{ $currentQuestionNumber }} dari {{ $material->questions->count() }}
                    </span>
                </div>

                <div class="question-content">
                    <h5 class="mb-3"><i class="fas fa-question me-2"></i>Pertanyaan</h5>
                    <div class="question-text">
                        {{ $currentQuestion->question_text }}
                    </div>
                    
                    <h5 class="mt-4 mb-3"><i class="fas fa-list-ul me-2"></i>Pilihan Jawaban</h5>
                    <div class="answers-container">
                    @if($currentQuestion->question_type === 'fill_in_the_blank')
                            <div class="form-group">
                                <label for="fillInTheBlankAnswer" class="form-label">Jawaban:</label>
                                <div class="input-group">
                                    <input type="text" 
                                           name="fill_in_the_blank_answer" 
                                           id="fillInTheBlankAnswer" 
                                           class="form-control" 
                                           required 
                                           placeholder="Isi jawaban di sini..."
                                           autocomplete="off">
                                </div>
                            </div>
                        @else
                        @foreach($currentQuestion->answers as $answer)
                            <div class="form-check answer-option">
                                <input class="form-check-input" 
                                       type="radio" 
                                       name="answer" 
                                       id="answer{{ $answer->id }}" 
                                       value="{{ $answer->id }}"
                                       required>
                                <label class="form-check-label w-100" for="answer{{ $answer->id }}">
                                    {{ $answer->answer_text }}
                                </label>
                            </div>
                            
                            
                        @endforeach
                    @endif
                    </div>

                    <button type="submit" class="btn btn-check-answer w-100" id="checkAnswerBtn">
                        <i class="fas fa-check-circle me-2"></i>Periksa Jawaban
                    </button>
                </div>
            </form>
        </div>
        
        @include('mahasiswa.partials.exercise-feedback')
    </div>
</div> 