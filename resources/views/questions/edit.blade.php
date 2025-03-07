<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="questions" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Edit Soal" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Edit Soal</h6>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <form method="POST" action="{{ route('questions.update', $question) }}" class="p-4">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="material_id">Material</label>
                                            @if(isset($material))
                                                <input type="hidden" name="material_id" value="{{ $material->id }}">
                                                <input type="text" class="form-control" value="{{ $material->title }}" disabled>
                                            @else
                                                <select name="material_id" id="material_id" class="form-control" required>
                                                    <option value="">Select Material</option>
                                                    @foreach($materials as $mat)
                                                        <option value="{{ $mat->id }}" {{ $question->material_id == $mat->id ? 'selected' : '' }}>
                                                            {{ $mat->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="input-group input-group-outline my-3">
                                            <textarea name="question_text" class="form-control" rows="3" placeholder="Pertanyaan" required>{{ $question->question_text }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="input-group input-group-outline my-3">
                                            <select name="question_type" class="form-control" required>
                                                <option value="radio_button" {{ $question->question_type == 'radio_button' ? 'selected' : '' }}>Radio Button</option>
                                                <option value="drag_and_drop" {{ $question->question_type == 'drag_and_drop' ? 'selected' : '' }}>Drag and Drop</option>
                                                <option value="fill_in_the_blank" {{ $question->question_type == 'fill_in_the_blank' ? 'selected' : '' }}>Fill in the Blank</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div id="answers-container">
                                    <h6 class="mb-3">Jawaban</h6>
                                    @foreach($question->answers as $index => $answer)
                                        <div class="answer-entry mb-3">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="input-group input-group-outline">
                                                        <input type="text" name="answers[{{ $index }}][answer_text]" class="form-control" placeholder="Jawaban" required value="{{ $answer->answer_text }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        @if($question->question_type === 'radio_button')
                                                            <input class="form-check-input" type="radio" name="correct_answer" value="{{ $index }}" {{ $answer->is_correct ? 'checked' : '' }}>
                                                            <label class="form-check-label">Jawaban Benar</label>
                                                            <input type="hidden" name="answers[{{ $index }}][is_correct]" value="{{ $answer->is_correct ? '1' : '0' }}">
                                                        @else
                                                            <input class="form-check-input" type="checkbox" name="answers[{{ $index }}][is_correct]" value="1" {{ $answer->is_correct ? 'checked' : '' }}>
                                                            <label class="form-check-label">Jawaban Benar</label>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addAnswer()">
                                    Tambah Jawaban
                                </button>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        @if($material)
                                            <a href="{{ route('materials.questions.index', $material) }}" class="btn btn-outline-secondary">Batal</a>
                                        @else
                                            <a href="{{ route('questions.index') }}" class="btn btn-outline-secondary">Batal</a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @push('js')
    <script>
        let answerCount = {{ count($question->answers) }};

        function handleQuestionTypeChange() {
            const questionType = document.querySelector('[name="question_type"]').value;
            const container = document.getElementById('answers-container');
            const existingAnswers = container.getElementsByClassName('answer-entry');
            
            Array.from(existingAnswers).forEach((answerEntry, index) => {
                const formCheck = answerEntry.querySelector('.form-check');
                
                if (questionType === 'radio_button') {
                    const isCorrect = formCheck.querySelector('input[name$="[is_correct]"]')?.value === '1';
                    formCheck.innerHTML = `
                        <input class="form-check-input" type="radio" name="correct_answer" value="${index}" ${isCorrect ? 'checked' : ''}>
                        <label class="form-check-label">Jawaban Benar</label>
                        <input type="hidden" name="answers[${index}][is_correct]" value="${isCorrect ? '1' : '0'}">
                    `;
                } else {
                    const isCorrect = formCheck.querySelector('input[name$="[is_correct]"]')?.value === '1';
                    formCheck.innerHTML = `
                        <input class="form-check-input" type="checkbox" name="answers[${index}][is_correct]" value="1" ${isCorrect ? 'checked' : ''}>
                        <label class="form-check-label">Jawaban Benar</label>
                    `;
                }
            });
        }

        function addAnswer() {
            const container = document.getElementById('answers-container');
            const questionType = document.querySelector('[name="question_type"]').value;
            const newAnswer = document.createElement('div');
            newAnswer.className = 'answer-entry mb-3';
            
            const currentIndex = answerCount;
            
            newAnswer.innerHTML = `
                <div class="row">
                    <div class="col-md-8">
                        <div class="input-group input-group-outline">
                            <input type="text" name="answers[${currentIndex}][answer_text]" class="form-control" placeholder="Jawaban" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            ${questionType === 'radio_button' ? 
                                `<input class="form-check-input" type="radio" name="correct_answer" value="${currentIndex}">
                                 <label class="form-check-label">Jawaban Benar</label>
                                 <input type="hidden" name="answers[${currentIndex}][is_correct]" value="0">` :
                                `<input class="form-check-input" type="checkbox" name="answers[${currentIndex}][is_correct]" value="1">
                                 <label class="form-check-label">Jawaban Benar</label>`
                            }
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(newAnswer);
            answerCount++;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const questionTypeSelect = document.querySelector('[name="question_type"]');
            const form = document.querySelector('form');
            
            questionTypeSelect.addEventListener('change', handleQuestionTypeChange);
            
            document.addEventListener('change', function(e) {
                if (e.target.type === 'radio' && e.target.name === 'correct_answer') {
                    const container = document.getElementById('answers-container');
                    const answers = container.getElementsByClassName('answer-entry');
                    
                    Array.from(answers).forEach((answer, index) => {
                        const hiddenInput = answer.querySelector('input[name$="[is_correct]"]');
                        hiddenInput.value = (index.toString() === e.target.value) ? '1' : '0';
                    });
                }
            });

            form.addEventListener('submit', function(e) {
                const questionType = questionTypeSelect.value;
                if (questionType === 'radio_button') {
                    const selectedRadio = document.querySelector('input[name="correct_answer"]:checked');
                    if (!selectedRadio) {
                        e.preventDefault();
                        alert('Pilih satu jawaban yang benar untuk tipe soal Radio Button');
                    }
                }
            });
        });
    </script>
    @endpush
</x-layout>