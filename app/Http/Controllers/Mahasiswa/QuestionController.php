<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Progress;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function checkAnswer(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'material_id' => 'required|exists:materials,id',
            'answer' => 'required|exists:answers,id'
        ]);

        $question = Question::with('answers')->findOrFail($request->question_id);
        $selectedAnswer = $question->answers->find($request->answer);
        
        $isCorrect = $selectedAnswer && $selectedAnswer->is_correct;
        
        if ($isCorrect) {
            // Save progress
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
            
            // Get next question that hasn't been answered correctly
            $nextQuestion = Question::where('material_id', $request->material_id)
                ->whereNotIn('id', [
                    $question->id,
                    ...Progress::where('user_id', auth()->id())
                        ->where('is_correct', true)
                        ->pluck('question_id')
                ])
                ->first();
                
            if ($nextQuestion) {
                return redirect()->route('mahasiswa.materials.show', ['material' => $request->material_id])
                    ->with('success', 'Jawaban benar! Lanjut ke soal berikutnya.');
            } else {
                return redirect()->route('mahasiswa.materials.show', ['material' => $request->material_id])
                    ->with('success', 'Selamat! Anda telah menyelesaikan semua soal.');
            }
        }
        
        return back()->with('error', 'Jawaban salah, silakan coba lagi!');
    }
} 