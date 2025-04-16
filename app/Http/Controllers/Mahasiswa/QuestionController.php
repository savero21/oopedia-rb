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
        // Validasi awal
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'material_id' => 'required|exists:materials,id'
        ]);

        // Ambil pertanyaan
        $question = Question::findOrFail($request->question_id);
        $isCorrect = false;
        $correctAnswerText = null;
        $selectedAnswerText = null;

        if ($question->question_type === 'fill_in_the_blank') {
            // Ambil jawaban pengguna
            $selectedAnswerText = trim($request->input('fill_in_the_blank_answer', ''));

            // Ambil jawaban yang benar dari database
            $correctAnswer = Answer::where('question_id', $question->id)
                                   ->where('is_correct', true)
                                   ->first();

            if ($correctAnswer) {
                $correctAnswerText = trim($correctAnswer->answer_text);
                // Bandingkan jawaban pengguna dengan yang benar (case insensitive)
                $isCorrect = strcasecmp($selectedAnswerText, $correctAnswerText) === 0;
            }

        } else {
            // Validasi untuk soal pilihan ganda
            $request->validate([
                'answer' => 'required|exists:answers,id',
            ]);

            // Ambil jawaban yang dipilih
            $selectedAnswer = Answer::findOrFail($request->answer);
            $isCorrect = $selectedAnswer->is_correct;
            $selectedAnswerText = $selectedAnswer->answer_text;

            // Jika jawaban salah, ambil jawaban yang benar
            if (!$isCorrect) {
                $correctAnswer = Answer::where('question_id', $question->id)
                                       ->where('is_correct', true)
                                       ->first();
                $correctAnswerText = $correctAnswer->answer_text ?? null;
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

        // Ambil soal berikutnya yang belum dijawab
        $nextQuestion = Question::where('material_id', $request->material_id)
            ->whereNotIn('id', Progress::where('user_id', auth()->id())->pluck('question_id'))
            ->first();

        // Response dalam format JSON
        return response()->json([
            'status' => $isCorrect ? 'success' : 'error',
            'message' => $isCorrect ? 'Jawaban Benar!' : 'Jawaban Salah!',
            'selectedAnswer' => $selectedAnswerText,
            'correctAnswer' => !$isCorrect ? $correctAnswerText : null,
            'hasNextQuestion' => (bool) $nextQuestion,
            'nextUrl' => route('mahasiswa.materials.show', ['material' => $request->material_id])
        ]);
    }
}
