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
        // Get all materials first
        $allMaterials = Material::with(['questions'])->orderBy('created_at', 'asc')->get();
        
        // Determine if user is guest (not logged in or role_id = 4)
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
        
        // If user is guest, only show half of the materials
        if ($isGuest) {
            $totalMaterials = $allMaterials->count();
            $materialsToShow = ceil($totalMaterials / 2);
            $allMaterials = $allMaterials->take($materialsToShow);
        }

        $materials = $allMaterials->map(function($material) use ($isGuest) {
            $totalQuestions = $material->questions->count();
            
            if ($isGuest) {
                // For guest users, handle progress from session
                $guestProgress = session('guest_progress.' . $material->id, []);
                $correctAnswers = count($guestProgress);
                
                // Limit to 3 questions for guest users
                $limitedTotalQuestions = min(3, $totalQuestions);
                $progressPercentage = $limitedTotalQuestions > 0 
                    ? min(100, round(($correctAnswers / $limitedTotalQuestions) * 100))
                    : 0;
                    
                $material->progress_percentage = $progressPercentage;
                $material->total_questions = $limitedTotalQuestions;
                $material->completed_questions = $correctAnswers;
            } else {
                // For logged-in users, get from database
                $materialProgress = Progress::where('user_id', auth()->id())
                    ->where('material_id', $material->id)
                    ->where('is_correct', true)
                    ->count();
                    
                $progressPercentage = $totalQuestions > 0 
                    ? min(100, round(($materialProgress / $totalQuestions) * 100))
                    : 0;
                    
                $material->progress_percentage = $progressPercentage;
                $material->total_questions = $totalQuestions;
                $material->completed_questions = $materialProgress;
            }
            
            return $material;
        });
        
        return view('mahasiswa.materials.questions.index', compact('materials'));
    }

    public function show($id, Request $request)
    {
        $material = Material::with(['questions.answers'])->findOrFail($id);
        $difficulty = $request->query('difficulty', 'all');
        
        // Get all materials for sidebar
        $materials = Material::orderBy('created_at', 'asc')->get();
        
        // Filter questions by difficulty if specified
        $questions = $material->questions;
        if ($difficulty !== 'all') {
            $questions = $questions->where('difficulty', $difficulty);
        }
        
        // Convert to collection and get values
        $filteredQuestions = $questions->values();
        
        // Determine if user is guest (not logged in or role_id = 4)
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
        
        // Jika user tamu, batasi hanya 3 soal pertama
        if ($isGuest) {
            $filteredQuestions = $filteredQuestions->take(3)->values();
        }
        
        // Get the answered questions for this difficulty
        if ($isGuest) {
            // For guests, get from session
            $answeredQuestionIds = collect(session('guest_progress.' . $material->id, []));
        } else {
            // For logged-in users, get from database
            $answeredQuestionIds = Progress::where('user_id', auth()->id())
                ->where('material_id', $material->id)
                ->where('is_correct', true)
                ->pluck('question_id');
        }
        
        // If there's a question_id parameter, use that
        if ($request->has('question')) {
            $questionId = $request->query('question');
            $currentQuestion = $filteredQuestions->firstWhere('id', $questionId);
            
            // If question not found in current difficulty, redirect
            if (!$currentQuestion) {
                return redirect()->route('mahasiswa.materials.questions.levels', [
                    'material' => $material->id,
                    'difficulty' => $difficulty
                ])->with('error', 'Soal tidak ditemukan.');
            }
            
            // Check if this question can be accessed
            $questionIndex = $filteredQuestions->search(function($q) use ($questionId) {
                return $q->id == $questionId;
            });
            
            // If question already answered, redirect
            if ($answeredQuestionIds->contains($questionId)) {
                return redirect()->route('mahasiswa.materials.questions.levels', [
                    'material' => $material->id,
                    'difficulty' => $difficulty
                ])->with('info', 'Soal ini sudah Anda jawab dengan benar.');
            }
            
            // If not first question and previous question not answered, redirect
            if ($questionIndex > 0) {
                $previousQuestion = $filteredQuestions[$questionIndex - 1];
                if (!$answeredQuestionIds->contains($previousQuestion->id)) {
                    return redirect()->route('mahasiswa.materials.questions.levels', [
                        'material' => $material->id,
                        'difficulty' => $difficulty
                    ])->with('error', 'Anda harus menyelesaikan soal sebelumnya terlebih dahulu.');
                }
            }
        } else {
            // If no question_id parameter, get first unanswered question
            $currentQuestion = $filteredQuestions->first(function($question) use ($answeredQuestionIds) {
                return !$answeredQuestionIds->contains($question->id);
            });
        }
        
        if (!$currentQuestion) {
            return redirect()->route('mahasiswa.materials.questions.levels', [
                'material' => $material->id,
                'difficulty' => $difficulty
            ])->with('success', 'Selamat! Anda telah menyelesaikan semua soal.');
        }
        
        $currentQuestionNumber = $filteredQuestions->search(function($q) use ($currentQuestion) {
            return $q->id == $currentQuestion->id;
        }) + 1;
        
        $totalFilteredQuestions = $filteredQuestions->count();
        
        if ($request->ajax()) {
            return view('mahasiswa.partials.question', compact(
                'material',
                'materials',
                'currentQuestion',
                'currentQuestionNumber',
                'totalFilteredQuestions',
                'difficulty',
                'isGuest'
            ));
        }
        
        return view('mahasiswa.materials.questions.show', compact(
            'materials',
            'material',
            'currentQuestion',
            'currentQuestionNumber',
            'totalFilteredQuestions',
            'difficulty',
            'isGuest'
        ));
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
        
        // Determine if user is guest
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
        
        // Get only questions that the user has answered
        if ($isGuest) {
            // For guests, filter questions based on session data
            $answeredQuestionIds = collect(session('guest_progress.' . $material->id, []));
            $questions = $questions->whereIn('id', $answeredQuestionIds);
        } else {
            // For logged-in users, get from database
            $answeredQuestionIds = Progress::where('user_id', auth()->id())
                ->where('material_id', $material->id)
                ->where('is_answered', true)
                ->pluck('question_id');
            $questions = $questions->whereIn('id', $answeredQuestionIds);
        }
        
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
            'materials' => $materials,
            'questions' => $questions,
            'difficulty' => $difficulty,
            'isGuest' => $isGuest
        ]);
    }

    public function getAttempts(Material $material, Question $question)
    {
        // Determine if user is guest
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
        
        if ($isGuest) {
            // For guest users, get attempts from session
            $progressKey = $material->id . '_' . $question->id;
            $guestProgress = session('guest_progress', []);
            $attempts = isset($guestProgress[$progressKey]) ? $guestProgress[$progressKey]['attempt_number'] : 0;
        } else {
            // For logged-in users, get from database
            $attempts = Progress::where('user_id', auth()->id())
                ->where('material_id', $material->id)
                ->where('question_id', $question->id)
                ->count();
        }
        
        return response()->json(['attempts' => $attempts]);
    }

    public function checkAnswer($materialId, $questionId, Request $request)
    {
        $material = Material::findOrFail($materialId);
        $question = Question::findOrFail($questionId);
        $difficulty = $request->query('difficulty', 'all');
        
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
            // Redirect back to levels page with the same difficulty
            $nextUrl = route('mahasiswa.materials.questions.levels', [
                'material' => $material->id,
                'difficulty' => $difficulty
            ]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Jawaban benar! Kembali ke halaman level.',
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
        
        // Determine if user is guest
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
        
        // Jika user tamu, batasi hanya 3 soal pertama
        if ($isGuest) {
            $questions = $questions->take(3);
        }
        
        // Get answered question IDs based on user type
        if ($isGuest) {
            // For guests, get answered questions from session
            $answeredQuestionIds = collect(session('guest_progress.' . $materialId, []));
        } else {
            // For logged-in users, get from database
            $answeredQuestionIds = Progress::where('user_id', auth()->id())
                ->where('material_id', $materialId)
                ->where('is_correct', true)
                ->pluck('question_id');
        }
        
        // Buat array level dengan status (terkunci/terbuka/selesai)
        $levels = [];
        $questionIndex = 0;
        
        // Konversi ke array untuk memudahkan pengecekan indeks
        $questionsArray = $questions->values()->all();
        
        foreach ($questionsArray as $index => $question) {
            $questionIndex++;
            $isAnswered = $answeredQuestionIds->contains($question->id);
            
            if ($isAnswered) {
                // Soal sudah dijawab benar, tandai sebagai completed
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
                'difficulty' => $question->difficulty
            ];
        }
        
        return view('mahasiswa.materials.questions.levels', compact(
            'material', 
            'materials', 
            'levels', 
            'difficulty',
            'isGuest'
        ));
    }
} 