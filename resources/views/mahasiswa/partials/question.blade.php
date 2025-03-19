<div class="materi-card">
    <div class="materi-card-body">
        <div id="questionContainer">
            <form id="questionForm" action="{{ route('mahasiswa.questions.check-answer') }}" method="POST">
                @csrf
                <input type="hidden" name="question_id" value="{{ $currentQuestion->id }}">
                <input type="hidden" name="material_id" value="{{ $material->id }}">
                
                <div class="question-header mb-4">
                    <span class="badge bg-gradient-primary">Soal {{ $currentQuestionNumber }} dari {{ $material->questions->count() }}</span>
                </div>

                <div class="mb-4">
                    <div class="question-text mb-4">
                        {{ $currentQuestion->question_text }}
                    </div>
                    <div class="answers-container">
                    @if($currentQuestion->question_type === 'fill_in_the_blank')
                            <div class="form-group">
                                <label for="fillInTheBlankAnswer">Jawaban:</label>
                                <input type="text" name="fill_in_the_blank_answer" id="fillInTheBlankAnswer" class="form-control" required placeholder="Isi jawaban di sini...">
                            </div>
                        @else
                        @foreach($currentQuestion->answers as $answer)
                            <div class="form-check answer-option mb-3">
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
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn bg-gradient-primary" id="checkAnswerBtn">
                        <i class="fas fa-check-circle me-2"></i>Periksa Jawaban
                    </button>
                </div>
            </form>

            @include('mahasiswa.partials.exercise-feedback')
        </div>
    </div>
</div> 