<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Progress;
use App\Models\Question;
use App\Models\Answer;
use App\Models\QuestionBankConfig;
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
            // Jika user adalah guest, tampilkan hanya 3 soal per kesulitan
            if ($isGuest) {
                $beginnerCount = 3;
                $mediumCount = 3;
                $hardCount = 3;
            } else {
                // Ambil konfigurasi dari QuestionBankConfig yang aktif
                $config = QuestionBankConfig::where('material_id', $material->id)
                    ->where('is_active', true)
                    ->first();
                
                // Jika ada konfigurasi, gunakan nilai dari konfigurasi
                if ($config) {
                    $beginnerCount = $config->beginner_count;
                    $mediumCount = $config->medium_count;
                    $hardCount = $config->hard_count;
                } else {
                    // Default jika tidak ada konfigurasi
                    $beginnerCount = $material->questions->where('difficulty', 'beginner')->count();
                    $mediumCount = $material->questions->where('difficulty', 'medium')->count();
                    $hardCount = $material->questions->where('difficulty', 'hard')->count();
                }
            }

            return [
                'material' => $material,
                'config' => [
                    'beginner' => $beginnerCount,
                    'medium' => $mediumCount,
                    'hard' => $hardCount
                ]
            ];
        });

        return view('mahasiswa.materials.questions.index', compact('materials', 'isGuest'));
    }

    public function show(Material $material, $difficulty = 'all')
    {
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
        $materials = Material::orderBy('created_at', 'asc')->get();
        
        // Filter soal berdasarkan difficulty jika parameter difficulty diberikan
        $questionsQuery = $material->questions();
        if ($difficulty !== 'all') {
            $questionsQuery->where('difficulty', $difficulty);
        }
        
        // Ambil semua soal yang tersedia
        $availableQuestions = $questionsQuery->get();
        
        // Jika user adalah guest, batasi hanya 3 soal per kesulitan
        if ($isGuest) {
            if ($difficulty === 'all') {
                $beginnerQuestions = $availableQuestions->where('difficulty', 'beginner')->take(3);
                $mediumQuestions = $availableQuestions->where('difficulty', 'medium')->take(3);
                $hardQuestions = $availableQuestions->where('difficulty', 'hard')->take(3);
                
                $questions = $beginnerQuestions->concat($mediumQuestions)->concat($hardQuestions);
            } else {
                $questions = $availableQuestions->take(3);
            }
        } else {
            // Untuk user terdaftar, gunakan konfigurasi dari admin
            $config = QuestionBankConfig::where('material_id', $material->id)
                ->where('is_active', true)
                ->first();
                
            if ($config) {
                if ($difficulty === 'all') {
                    $beginnerQuestions = $availableQuestions->where('difficulty', 'beginner')->take($config->beginner_count);
                    $mediumQuestions = $availableQuestions->where('difficulty', 'medium')->take($config->medium_count);
                    $hardQuestions = $availableQuestions->where('difficulty', 'hard')->take($config->hard_count);
                    
                    $questions = $beginnerQuestions->concat($mediumQuestions)->concat($hardQuestions);
                } else {
                    // Tentukan jumlah soal berdasarkan tingkat kesulitan
                    $countField = $difficulty . '_count';
                    $limit = $config->$countField;
                    $questions = $availableQuestions->take($limit);
                }
            } else {
                // Jika tidak ada konfigurasi, tampilkan semua soal
                $questions = $availableQuestions;
            }
        }
        
        return view('mahasiswa.materials.questions.show', compact('material', 'materials', 'questions', 'difficulty', 'isGuest'));
    }

    public function levels(Material $material, Request $request)
    {
        $materials = Material::orderBy('created_at', 'asc')->get();
        $difficulty = $request->query('difficulty', 'beginner');
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
        
        // Filter soal berdasarkan difficulty
        $questions = $material->questions()->where('difficulty', $difficulty)->get();
        
        // Jika user adalah guest, batasi hanya 3 soal
        if ($isGuest) {
            $questions = $questions->take(3);
        } else {
            // Untuk user terdaftar, gunakan konfigurasi dari admin
            $config = QuestionBankConfig::where('material_id', $material->id)
                ->where('is_active', true)
                ->first();
                
            if ($config) {
                $countField = $difficulty . '_count';
                $limit = $config->$countField;
                $questions = $questions->take($limit);
            }
        }
        
        // Get answered questions for progress tracking
        $answeredQuestionIds = Progress::where('user_id', auth()->id() ?? session()->getId())
            ->where('material_id', $material->id)
            ->where('is_correct', true)
            ->pluck('question_id');
        
        $levels = [];
        $questionsArray = $questions->toArray();
        
        foreach ($questions as $index => $question) {
            $questionIndex = $index + 1;
            $isAnswered = $answeredQuestionIds->contains($question->id);
            
            if ($isAnswered) {
                // Soal sudah dijawab benar, tandai sebagai completed
                $status = 'completed';
            } elseif ($questionIndex === 1) {
                // Soal pertama selalu terbuka
                $status = 'unlocked';
            } elseif ($index > 0 && $answeredQuestionIds->contains($questions[$index-1]->id)) {
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

    /**
     * Display question levels for a material
     */
    public function showLevels(Material $material, Request $request)
    {
        $materials = Material::orderBy('created_at', 'asc')->get();
        $difficulty = $request->query('difficulty', 'beginner');
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
        
        // Filter soal berdasarkan difficulty
        $questions = $material->questions()->where('difficulty', $difficulty)->get();
        
        // Jika user adalah guest, batasi hanya 3 soal
        if ($isGuest) {
            $questions = $questions->take(3);
        } else {
            // Untuk user terdaftar, gunakan konfigurasi dari admin
            $config = QuestionBankConfig::where('material_id', $material->id)
                ->where('is_active', true)
                ->first();
                
            if ($config) {
                $countField = $difficulty . '_count';
                $limit = $config->$countField;
                $questions = $questions->take($limit);
            }
        }
        
        // Get answered questions for progress tracking
        $answeredQuestionIds = Progress::where('user_id', auth()->id() ?? session()->getId())
            ->where('material_id', $material->id)
            ->where('is_correct', true)
            ->pluck('question_id');
        
        $levels = [];
        $questionsArray = $questions->toArray();
        
        foreach ($questions as $index => $question) {
            $questionIndex = $index + 1;
            $isAnswered = $answeredQuestionIds->contains($question->id);
            
            if ($isAnswered) {
                // Soal sudah dijawab benar, tandai sebagai completed
                $status = 'completed';
            } elseif ($questionIndex === 1) {
                // Soal pertama selalu terbuka
                $status = 'unlocked';
            } elseif ($index > 0 && $answeredQuestionIds->contains($questions[$index-1]->id)) {
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

    public function dashboard()
{
    $userId = auth()->id();
    $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
    
    // Get all materials
    $allMaterials = Material::with(['questions'])->get();
    $totalMaterials = $allMaterials->count();
    
    // Variables to store configured question counts
    $configuredTotalQuestions = 0;
    $configuredEasyQuestions = 0;
    $configuredMediumQuestions = 0;
    $configuredHardQuestions = 0;
    
    // Calculate configured question counts
    foreach ($allMaterials as $material) {
        if ($isGuest) {
            // For guests, use fixed values (3 per difficulty)
            $configuredEasyQuestions += min(3, $material->questions->where('difficulty', 'beginner')->count());
            $configuredMediumQuestions += min(3, $material->questions->where('difficulty', 'medium')->count());
            $configuredHardQuestions += min(3, $material->questions->where('difficulty', 'hard')->count());
        } else {
            // For registered users, use admin configuration
            $config = QuestionBankConfig::where('material_id', $material->id)
                ->where('is_active', true)
                ->first();
                
            if ($config) {
                $configuredEasyQuestions += $config->beginner_count;
                $configuredMediumQuestions += $config->medium_count;
                $configuredHardQuestions += $config->hard_count;
            } else {
                // Default if no configuration exists
                $configuredEasyQuestions += $material->questions->where('difficulty', 'beginner')->count();
                $configuredMediumQuestions += $material->questions->where('difficulty', 'medium')->count();
                $configuredHardQuestions += $material->questions->where('difficulty', 'hard')->count();
            }
        }
    }
    
    $configuredTotalQuestions = $configuredEasyQuestions + $configuredMediumQuestions + $configuredHardQuestions;
    
    // Get other data you need for the dashboard...
    
    return view('mahasiswa.dashboard.index', [
        'totalMaterials' => $totalMaterials,
        'configuredTotalQuestions' => $configuredTotalQuestions,
        'configuredEasyQuestions' => $configuredEasyQuestions,
        'configuredMediumQuestions' => $configuredMediumQuestions,
        'configuredHardQuestions' => $configuredHardQuestions,
        // other variables...
    ]);
}
} 