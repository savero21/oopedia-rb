<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Progress;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function checkAnswer(Request $request)
    {
        $question = Question::findOrFail($request->question_id);
        $isCorrect = false;
        $selectedAnswerText = '';
        $correctAnswerText = null;
        $explanation = null;
        
        // Validasi input
        if ($request->has('answer_text')) {
            // Logic for text-based answers
            $correctAnswer = Answer::where('question_id', $question->id)
                                 ->where('is_correct', true)
                                 ->first();
            
            $isCorrect = strtolower(trim($request->answer_text)) === strtolower(trim($correctAnswer->answer_text));
            $selectedAnswerText = $request->answer_text;
            
            if (!$isCorrect) {
                $correctAnswerText = $correctAnswer->answer_text;
            }
            
            $explanation = $correctAnswer->explanation;
        } else {
            $request->validate([
                'answer' => 'required|exists:answers,id',
            ]);

            $selectedAnswer = Answer::findOrFail($request->answer);
            $isCorrect = $selectedAnswer->is_correct;
            $selectedAnswerText = $selectedAnswer->answer_text;

            // Get correct answer if answer is wrong
            if (!$isCorrect) {
                $correctAnswer = Answer::where('question_id', $question->id)
                                     ->where('is_correct', true)
                                     ->first();
                $correctAnswerText = $correctAnswer->answer_text ?? null;
            } else {
                // Only set explanation if answer is correct
                $explanation = $selectedAnswer->explanation;
            }
        }

        // Update progress
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

        // Get next question based on difficulty if provided
        $nextQuestionQuery = Question::where('material_id', $request->material_id)
            ->whereNotIn('id', Progress::where('user_id', auth()->id())->pluck('question_id'));
            
        // Filter by difficulty if provided
        if ($request->has('difficulty') && $request->difficulty != 'all') {
            $nextQuestionQuery->where('difficulty', $request->difficulty);
        }
        
        $nextQuestion = $nextQuestionQuery->first();

        // Count answered questions
        $answeredCount = Progress::where('user_id', auth()->id())
            ->where('material_id', $request->material_id)
            ->where('is_correct', true)
            ->count();

        // Build the next URL with difficulty parameter if needed
        $nextUrl = route('mahasiswa.materials.questions.show', ['material' => $request->material_id]);

        return response()->json([
            'status' => $isCorrect ? 'success' : 'error',
            'message' => $isCorrect ? 'Jawaban Benar!' : 'Jawaban Salah!',
            'selectedAnswer' => $selectedAnswerText,
            'correctAnswer' => $isCorrect ? null : $correctAnswerText,
            'explanation' => $explanation,
            'hasNextQuestion' => !is_null($nextQuestion),
            'nextUrl' => $nextUrl,
            'answeredCount' => $answeredCount,
            'difficulty' => $request->difficulty ?? 'all'
        ]);
    }
}
