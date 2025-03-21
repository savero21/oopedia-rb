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
            'answer' => 'required|exists:answers,id',
            'material_id' => 'required|exists:materials,id'
        ]);

        $question = Question::findOrFail($request->question_id);
        $selectedAnswer = Answer::findOrFail($request->answer);
        $correctAnswer = Answer::where('question_id', $question->id)
                              ->where('is_correct', true)
                              ->first();

        $isCorrect = $selectedAnswer->is_correct;

        if ($isCorrect) {
            Progress::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'material_id' => $request->material_id,
                    'question_id' => $question->id
                ],
                [
                    'is_correct' => true,
                    'is_answered' => true
                ]
            );
            
            $nextQuestion = Question::where('material_id', $request->material_id)
                ->whereNotIn('id', [
                    $question->id,
                    ...Progress::where('user_id', auth()->id())
                        ->where('is_correct', true)
                        ->pluck('question_id')
                ])
                ->first();
            
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Jawaban Benar!',
                    'explanation' => $selectedAnswer->explanation,
                    'hasNextQuestion' => !is_null($nextQuestion),
                    'nextUrl' => route('mahasiswa.materials.show', ['material' => $request->material_id])
                ]);
            }
        }
        
        // Jika jawaban salah
        Progress::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'material_id' => $request->material_id,
                'question_id' => $question->id
            ],
            [
                'is_correct' => false,
                'is_answered' => true
            ]
        );
        
        $correctExplanation = $correctAnswer->explanation && trim($correctAnswer->explanation) !== 'benar' 
            ? $correctAnswer->explanation 
            : null;
            
        $selectedExplanation = $selectedAnswer->explanation && trim($selectedAnswer->explanation) !== 'benar' 
            ? $selectedAnswer->explanation 
            : null;
        
        return response()->json([
            'status' => 'error',
            'message' => 'Jawaban Salah!',
            'selectedExplanation' => $selectedExplanation
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
                'correct_answer' => $isCorrect ? null : $question->answers->where('is_correct', true)->first()->answer_text
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