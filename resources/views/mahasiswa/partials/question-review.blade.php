<div class="materi-card">
    <div class="materi-card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Review Semua Soal</h3>
            <form action="{{ route('mahasiswa.materials.reset', $material->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-redo me-2"></i>Kerjakan Ulang
                </button>
            </form>
        </div>
        @foreach($material->questions as $index => $question)
            <div class="question-review mb-4 p-4 border rounded">
                <div class="question-header mb-3">
                    <span class="badge bg-gradient-primary">Soal {{ $index + 1 }} dari {{ $material->questions->count() }}</span>
                </div>
                
                <div class="question-text mb-3">
                    {{ $question->question_text }}
                </div>

                <div class="answers-container">
                    @foreach($question->answers as $answer)
                        <div class="answer-option mb-2 {{ $answer->is_correct ? 'correct-answer' : '' }}">
                            <div class="d-flex align-items-center">
                                @if($answer->is_correct)
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                @endif
                                <span>{{ $answer->answer_text }}</span>
                            </div>
                            @if($answer->explanation)
                                <div class="answer-explanation mt-1">
                                    <small class="text-muted"><i class="fas fa-info-circle me-1"></i>{{ $answer->explanation }}</small>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div> 