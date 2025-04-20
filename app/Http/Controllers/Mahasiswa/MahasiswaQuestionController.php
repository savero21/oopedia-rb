<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Progress;
use App\Models\Material;
use App\Models\Answer;
use Illuminate\Http\Request;

class MahasiswaQuestionController extends Controller
{
    public function checkAnswer(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'material_id' => 'required|exists:materials,id'
        ]);
    
        $question = Question::findOrFail($request->question_id);
        $isCorrect = false;
        $correctAnswerText = null;
        $selectedAnswerText = null;
        $explanation = null;
        $selectedExplanation = null;
    
        if ($question->question_type === 'fill_in_the_blank') {
            // Ambil jawaban dari input teks
            $selectedAnswerText = trim($request->input('fill_in_the_blank_answer'));
    
            // Ambil jawaban yang benar dari database
            $correctAnswer = Answer::where('question_id', $question->id)
                                 ->where('is_correct', true)
                                 ->first();
            
            if ($correctAnswer) {
                $correctAnswerText = trim($correctAnswer->answer_text);
                $explanation = $correctAnswer->explanation;
                // Bandingkan jawaban pengguna dengan yang benar (case insensitive)
                $isCorrect = strcasecmp($selectedAnswerText, $correctAnswerText) === 0;
            }
    
        } else {
            // Jika soal pilihan ganda
            $request->validate([
                'answer' => 'required|exists:answers,id',
            ]);
    
            $selectedAnswer = Answer::findOrFail($request->answer);
            $isCorrect = $selectedAnswer->is_correct;
            $selectedAnswerText = $selectedAnswer->answer_text;
            $selectedExplanation = $selectedAnswer->explanation;
    
            // Ambil jawaban yang benar jika jawaban salah
            if (!$isCorrect) {
                $correctAnswer = Answer::where('question_id', $question->id)
                                     ->where('is_correct', true)
                                     ->first();
                $correctAnswerText = $correctAnswer->answer_text ?? null;
                $explanation = $correctAnswer->explanation ?? null;
            } else {
                $explanation = $selectedAnswer->explanation;
            }
        }
    
        // Simpan progress mahasiswa
        Progress::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'material_id' => $request->material_id,
                'question_id' => $question->id
            ],
            [
                'is_correct' => $isCorrect,
                'is_answered' => true
            ]
        );
    
        // Ambil soal berikutnya
        $nextQuestion = Question::where('material_id', $request->material_id)
            ->whereNotIn('id', Progress::where('user_id', auth()->id())->pluck('question_id'))
            ->first();
    
        return response()->json([
            'status' => $isCorrect ? 'success' : 'error',
            'message' => $isCorrect ? 'Jawaban Benar!' : 'Jawaban Salah!',
            'selectedAnswer' => $selectedAnswerText,
            'correctAnswer' => $isCorrect ? null : $correctAnswerText,
            'explanation' => $explanation,
            'selectedExplanation' => $selectedExplanation,
            'hasNextQuestion' => !is_null($nextQuestion),
            'nextUrl' => route('mahasiswa.materials.show', ['material' => $request->material_id])
        ]);
    }
    /**
     * Check all answers for a material
     */
    public function checkAllAnswers(Request $request)
{
    $request->validate([
        'material_id' => 'required|exists:materials,id',
        'answers' => 'required|array'
    ]);

    $materialId = $request->material_id;
    $answers = $request->answers;
    
    $totalQuestions = count($answers);
    $correctAnswers = 0;
    $results = [];
    
    // Check each answer
    foreach ($answers as $questionId => $answerId) {
        $question = Question::with('answers')->findOrFail($questionId);
        $selectedAnswer = $question->answers->find($answerId);
        
        $isCorrect = $selectedAnswer && $selectedAnswer->is_correct;
        $correctAnswer = $question->answers->where('is_correct', true)->first();
        
        // Save progress regardless of correctness
        Progress::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'material_id' => $materialId,
                'question_id' => $questionId
            ],
            [
                'is_correct' => $isCorrect,
                'is_answered' => true
            ]
        );
        
        if ($isCorrect) {
            $correctAnswers++;
        }
        
        // Store result for feedback
        $results[$questionId] = [
            'is_correct' => $isCorrect,
            'question_text' => $question->question_text,
            'selected_answer' => $selectedAnswer ? $selectedAnswer->answer_text : null,
            'correct_answer' => $isCorrect ? null : ($correctAnswer->answer_text ?? null),
            'explanation' => $correctAnswer->explanation ?? null,
            'selected_explanation' => $selectedAnswer ? $selectedAnswer->explanation : null
        ];
    }
    
    $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;
    
    // Return JSON response for AJAX request
    if ($request->ajax()) {
        return response()->json([
            'status' => 'success',
            'message' => "Anda menjawab benar $correctAnswers dari $totalQuestions soal (Skor: $score%)",
            'score' => $score,
            'results' => $results,
            'nextUrl' => route('mahasiswa.dashboard')
        ]);
    }
    
    return redirect()->route('mahasiswa.dashboard')
        ->with('success', "Anda menjawab benar $correctAnswers dari $totalQuestions soal (Skor: $score%)");
}
}