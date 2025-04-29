<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionController extends Controller
{
    public function checkAnswer(Request $request)
    {
        // Log semua data request untuk debugging
        Log::info('Request data for checkAnswer:', $request->all());
        
        try {
            // Validasi dasar
            $request->validate([
                'question_id' => 'required|exists:questions,id',
                'material_id' => 'required|exists:materials,id'
            ]);
            
            $question = Question::findOrFail($request->question_id);
            $isCorrect = false;
            $correctAnswerText = null;
            $selectedAnswerText = null;
            $explanation = null;
            
            if ($question->question_type === 'fill_in_the_blank') {
                // Validasi untuk soal fill in the blank
                if (!$request->has('fill_in_the_blank_answer')) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'The answer field is required.'
                    ], 422);
                }
                
                $userAnswer = $request->fill_in_the_blank_answer;
                if (empty(trim($userAnswer))) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'The answer field is required.'
                    ], 422);
                }
                
                $correctAnswer = Answer::where('question_id', $question->id)
                                    ->where('is_correct', true)
                                    ->first();
                
                if ($correctAnswer) {
                    // Normalisasi jawaban untuk perbandingan yang lebih fleksibel
                    $normalizedUserAnswer = strtolower(trim($userAnswer));
                    $normalizedCorrectAnswer = strtolower(trim($correctAnswer->answer_text));
                    
                    // Cek apakah ada beberapa jawaban yang benar (dipisahkan dengan pipe |)
                    $acceptableAnswers = explode('|', $normalizedCorrectAnswer);
                    $isCorrect = false;
                    
                    foreach ($acceptableAnswers as $answer) {
                        if (trim($normalizedUserAnswer) === trim($answer)) {
                            $isCorrect = true;
                            break;
                        }
                    }
                    
                    $selectedAnswerText = $userAnswer;
                    
                    if (!$isCorrect) {
                        $correctAnswerText = $correctAnswer->answer_text;
                    }
                    
                    $explanation = $correctAnswer->explanation;
                }
            } else {
                // Validasi untuk soal pilihan ganda
                if (!$request->has('answer')) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Pilih salah satu jawaban.'
                    ], 422);
                }
                
                $selectedAnswer = Answer::findOrFail($request->answer);
                $isCorrect = $selectedAnswer->is_correct;
                $selectedAnswerText = $selectedAnswer->answer_text;
                
                if (!$isCorrect) {
                    $correctAnswer = Answer::where('question_id', $question->id)
                                        ->where('is_correct', true)
                                        ->first();
                    $correctAnswerText = $correctAnswer->answer_text ?? null;
                }
                
                $explanation = $selectedAnswer->explanation;
            }
            
            // Update progress jika user login
            if (auth()->check()) {
                Progress::updateOrCreate(
                    [
                        'user_id' => auth()->id(),
                        'material_id' => $request->material_id,
                        'question_id' => $question->id
                    ],
                    [
                        'is_correct' => $isCorrect,
                        'attempt_number' => DB::raw('attempt_number + 1')
                    ]
                );
            }
            
            // Cek apakah ada soal berikutnya
            $nextQuestion = Question::where('material_id', $request->material_id)
                                ->where('id', '>', $question->id)
                                ->orderBy('id')
                                ->first();
            
            return response()->json([
                'status' => $isCorrect ? 'success' : 'error',
                'message' => $isCorrect ? 'Jawaban Benar!' : 'Jawaban Salah',
                'selectedAnswerText' => $selectedAnswerText,
                'correctAnswerText' => $correctAnswerText,
                'explanation' => $explanation,
                'hasNextQuestion' => !is_null($nextQuestion),
                'nextUrl' => $nextQuestion ? route('mahasiswa.materials.questions.show', [
                    'material' => $request->material_id,
                    'question' => $nextQuestion->id,
                    'difficulty' => $request->difficulty ?? 'all'
                ]) : null
            ]);
        } catch (\Exception $e) {
            Log::error('Error in checkAnswer: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
