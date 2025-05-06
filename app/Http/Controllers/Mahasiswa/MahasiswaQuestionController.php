<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Progress;
use App\Models\Material;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MahasiswaQuestionController extends Controller
{
    public function checkAnswer(Request $request)
    {
        // Debugging - tampilkan semua data request
        \Log::info('Request data for checkAnswer:', $request->all());
        
        try {
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
            
            // Update progress jika user login, simpan ke session jika guest
            if (auth()->check() && auth()->user()->role_id !== 4) {
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
            } else {
                // For guest users (not logged in or role_id = 4), track in session
                // Format untuk session guest_progress
                $sessionKey = 'guest_progress';
                $guestProgress = session($sessionKey, []);
                
                $progressKey = $request->material_id . '_' . $question->id;
                $guestProgress[$progressKey] = [
                    'is_correct' => $isCorrect,
                    'attempt_number' => isset($guestProgress[$progressKey]) ? 
                        $guestProgress[$progressKey]['attempt_number'] + 1 : 1
                ];
                
                session([$sessionKey => $guestProgress]);
                
                // Update format untuk level unlocking jika jawaban benar
                if ($isCorrect) {
                    $materialProgress = session('guest_progress.' . $request->material_id, []);
                    if (!in_array($question->id, $materialProgress)) {
                        $materialProgress[] = $question->id;
                        session(['guest_progress.' . $request->material_id => $materialProgress]);
                    }
                    
                    // Log debug untuk memeriksa data
                    \Log::debug('Guest progress updated', [
                        'material_id' => $request->material_id,
                        'question_id' => $question->id,
                        'session_data' => $materialProgress
                    ]);
                }
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
            \Log::error('Error in checkAnswer: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
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