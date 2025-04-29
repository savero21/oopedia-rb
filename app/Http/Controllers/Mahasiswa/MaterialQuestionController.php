<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Progress;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialQuestionController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        
        // Get progress statistics
        $progressStats = DB::table('progress')
            ->select(
                'material_id',
                DB::raw('COUNT(DISTINCT question_id) as answered_questions'),
                DB::raw('SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
            )
            ->where('user_id', $userId)
            ->groupBy('material_id')
            ->get();

        // Get all materials first
        $allMaterials = Material::with(['questions'])->orderBy('created_at', 'asc')->get();
        
        // If user is guest, only show half of the materials
        if (auth()->user()->role_id === 4) {
            $totalMaterials = $allMaterials->count();
            $materialsToShow = ceil($totalMaterials / 2);
            $allMaterials = $allMaterials->take($materialsToShow);
        }

        $materials = $allMaterials->map(function($material) use ($progressStats) {
            $totalQuestions = $material->questions->count();
            $materialProgress = $progressStats->firstWhere('material_id', $material->id);
            
            $correctAnswers = $materialProgress ? $materialProgress->correct_answers : 0;
            $progressPercentage = $totalQuestions > 0 
                ? min(100, round(($correctAnswers / $totalQuestions) * 100))
                : 0;

            $material->progress_percentage = $progressPercentage;
            $material->total_questions = $totalQuestions;
            $material->completed_questions = $correctAnswers;
            
            return $material;
        });
        
        return view('mahasiswa.materials.questions.index', compact('materials'));
    }

    public function show($id, Request $request)
    {
        $material = Material::with(['questions.answers'])->findOrFail($id);
        
        // Get all materials for sidebar
        $materials = Material::orderBy('created_at', 'asc')->get();
        
        // Get all questions without difficulty filtering
        $filteredQuestions = $material->questions;
        
        // Convert to array with sequential indices
        $filteredQuestions = $filteredQuestions->values();
        
        // Get the answered questions
        $answeredQuestionIds = Progress::where('user_id', auth()->id())
            ->where('material_id', $material->id)
            ->where('is_correct', true)
            ->pluck('question_id');
        
        // Find index of last answered question
        $lastAnsweredIndex = -1;
        foreach ($filteredQuestions as $index => $question) {
            if ($answeredQuestionIds->contains($question->id)) {
                $lastAnsweredIndex = $index;
            }
        }
        
        // If there's a question_id parameter, use that
        if ($request->has('question')) {
            $questionId = $request->query('question');
            $currentQuestion = $filteredQuestions->firstWhere('id', $questionId);
            
            // If question not found, redirect to levels page
            if (!$currentQuestion) {
                return redirect()->route('mahasiswa.materials.questions.levels', [
                    'material' => $material->id
                ])->with('error', 'Soal tidak ditemukan.');
            }
            
            // Check if this question can be accessed
            $questionIndex = $filteredQuestions->search(function($q) use ($questionId) {
                return $q->id == $questionId;
            });
            
            // If question already answered, redirect to levels page
            if ($answeredQuestionIds->contains($questionId)) {
                return redirect()->route('mahasiswa.materials.questions.levels', [
                    'material' => $material->id
                ])->with('info', 'Soal ini sudah Anda jawab dengan benar. Silakan pilih soal berikutnya.');
            }
            
            // If not first question and previous question not answered, redirect
            if ($questionIndex > 0 && $questionIndex > $lastAnsweredIndex + 1) {
                return redirect()->route('mahasiswa.materials.questions.levels', [
                    'material' => $material->id
                ])->with('error', 'Anda harus menyelesaikan soal sebelumnya terlebih dahulu.');
            }
        } else {
            // If no question_id parameter, direct to next unanswered question
            $nextQuestionIndex = $lastAnsweredIndex + 1;
            
            // If all questions answered, return to levels page
            if ($nextQuestionIndex >= $filteredQuestions->count()) {
                return redirect()->route('mahasiswa.materials.questions.levels', [
                    'material' => $material->id
                ])->with('success', 'Selamat! Anda telah menyelesaikan semua soal.');
            }
            
            $currentQuestion = $filteredQuestions[$nextQuestionIndex];
        }
        
        // If no questions available
        if (!$currentQuestion) {
            return redirect()->route('mahasiswa.materials.questions.levels', [
                'material' => $material->id
            ])->with('info', 'Tidak ada soal yang tersedia.');
        }
        
        $currentQuestionNumber = 1;
        $totalFilteredQuestions = $filteredQuestions->count();
        
        if ($request->ajax()) {
            return view('mahasiswa.partials.question', compact('material', 'materials', 'currentQuestion', 'currentQuestionNumber', 'totalFilteredQuestions'));
        }
        
        return view('mahasiswa.materials.questions.show', compact('materials', 'material', 'currentQuestion', 'currentQuestionNumber', 'totalFilteredQuestions'));
    }

    public function review($id, Request $request)
    {
        $material = Material::with(['questions.answers'])->findOrFail($id);
        
        // Get all materials for sidebar
        $materials = Material::orderBy('created_at', 'asc')->get();
        
        // Filter questions by difficulty if specified
        $difficulty = $request->query('difficulty', 'all');
        $questions = $material->questions;
        
        if ($difficulty && $difficulty !== 'all') {
            $questions = $questions->where('difficulty', $difficulty);
        }
        
        // Get only questions that the user has answered
        $answeredQuestionIds = Progress::where('user_id', auth()->id())
            ->where('material_id', $material->id)
            ->where('is_answered', true)
            ->pluck('question_id');
        
        $questions = $questions->whereIn('id', $answeredQuestionIds);
        
        if ($request->ajax()) {
            return view('mahasiswa.partials.question-review-filtered', [
                'material' => $material,
                'questions' => $questions,
                'difficulty' => $difficulty
            ])->render();
        }
        
        // For direct access, return the full review page
        return view('mahasiswa.materials.questions.review', [
            'material' => $material,
            'materials' => $materials,  // Make sure to pass this variable
            'questions' => $questions,
            'difficulty' => $difficulty
        ]);
    }

    public function getAttempts(Material $material, Question $question)
    {
        $attempts = Progress::where('user_id', auth()->id())
            ->where('material_id', $material->id)
            ->where('question_id', $question->id)
            ->count();
        
        return response()->json(['attempts' => $attempts]);
    }

    public function checkAnswer($materialId, $questionId, Request $request)
    {
        $material = Material::findOrFail($materialId);
        $question = Question::findOrFail($questionId);
        
        $request->validate([
            'answer' => 'required',
            'attempts' => 'required|integer',
            'potential_score' => 'required|integer'
        ]);

        $selectedAnswer = Answer::findOrFail($request->answer);
        $isCorrect = $selectedAnswer->is_correct;

        // Update progress
        Progress::create([
            'user_id' => auth()->id(),
            'material_id' => $material->id,
            'question_id' => $question->id,
            'is_correct' => $isCorrect,
            'score' => $isCorrect ? $request->potential_score : 0,
            'attempt_number' => $request->attempts
        ]);

        if ($isCorrect) {
            $nextUrl = route('mahasiswa.materials.questions.levels', [
                'material' => $material->id
            ]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Jawaban benar! Kembali ke halaman level untuk melanjutkan.',
                'hasNextQuestion' => false,
                'nextUrl' => $nextUrl
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Jawaban salah, silakan coba lagi.',
                'hasNextQuestion' => false,
                'nextUrl' => null
            ]);
        }
    }

    public function showLevels($materialId, Request $request)
    {
        $material = Material::with(['questions'])->findOrFail($materialId);
        $materials = Material::orderBy('created_at', 'asc')->get();
        
        $difficulty = $request->query('difficulty', 'all');
        
        // Filter pertanyaan berdasarkan tingkat kesulitan
        $questions = $material->questions;
        if ($difficulty !== 'all') {
            $questions = $questions->where('difficulty', $difficulty);
        }
        
        // Dapatkan ID pertanyaan yang sudah dijawab dengan benar
        $answeredQuestionIds = Progress::where('user_id', auth()->id())
            ->where('material_id', $materialId)
            ->where('is_correct', true)
            ->pluck('question_id');
        
        // Buat array level dengan status (terkunci/terbuka/selesai)
        $levels = [];
        $questionIndex = 0;
        
        // Konversi ke array untuk memudahkan pengecekan indeks
        $questionsArray = $questions->values()->all();
        
        foreach ($questionsArray as $index => $question) {
            $questionIndex++;
            $isAnswered = $answeredQuestionIds->contains($question->id);
            
            // Logika baru:
            // 1. Level pertama selalu terbuka
            // 2. Level berikutnya terbuka hanya jika level sebelumnya sudah dijawab
            // 3. Level yang sudah dijawab ditandai sebagai completed (disabled)
            
            if ($isAnswered) {
                // Soal sudah dijawab benar, tandai sebagai completed (disabled)
                $status = 'completed';
            } elseif ($questionIndex === 1) {
                // Soal pertama selalu terbuka
                $status = 'unlocked';
            } elseif ($index > 0 && $answeredQuestionIds->contains($questionsArray[$index-1]->id)) {
                // Soal sebelumnya sudah dijawab benar, buka soal ini
                $status = 'unlocked';
            } else {
                // Soal sebelumnya belum dijawab benar, kunci soal ini
                $status = 'locked';
            }
            
            $levels[] = [
                'level' => $questionIndex,
                'question_id' => $question->id,
                'status' => $status,
            ];
        }
        
        return view('mahasiswa.materials.questions.levels', compact(
            'material', 
            'materials', 
            'levels', 
            'difficulty'
        ));
    }
} 