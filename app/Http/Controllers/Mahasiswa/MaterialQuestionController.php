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
        
        // Filter questions by difficulty if specified
        $difficulty = $request->query('difficulty');
        $filteredQuestions = $material->questions;
        
        if ($difficulty) {
            $filteredQuestions = $filteredQuestions->where('difficulty', $difficulty);
        }
        
        // Get the answered questions
        $answeredQuestionIds = Progress::where('user_id', auth()->id())
            ->where('material_id', $material->id)
            ->where('is_correct', true)
            ->pluck('question_id');
        
        $unansweredQuestions = $filteredQuestions->whereNotIn('id', $answeredQuestionIds);
        
        // Check if all questions in this difficulty are answered
        $allAnswered = ($unansweredQuestions->count() === 0);
        
        // Only set currentQuestion if there are unanswered questions
        $currentQuestion = $allAnswered ? null : $unansweredQuestions->first();
        
        $currentQuestionNumber = 1;
        $totalFilteredQuestions = $filteredQuestions->count();
        
        // Calculate current question number if needed
        if (!$allAnswered && $unansweredQuestions->count() > 0) {
            // Your calculation logic here
        }
        
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

    public function checkAnswer(Request $request, Material $material, Question $question)
    {
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

        // Get next question
        $nextQuestion = Question::where('material_id', $material->id)
            ->where('id', '>', $question->id)
            ->when($request->difficulty && $request->difficulty !== 'all', function($query) use ($request) {
                return $query->where('difficulty', $request->difficulty);
            })
            ->first();

        return response()->json([
            'status' => $isCorrect ? 'success' : 'error',
            'message' => $isCorrect ? 'Jawaban benar!' : 'Jawaban salah, silakan coba lagi.',
            'hasNextQuestion' => !is_null($nextQuestion),
            'nextUrl' => $nextQuestion ? route('mahasiswa.materials.questions.show', [
                'material' => $material->id,
                'question' => $nextQuestion->id
            ]) : null,
            'difficulty' => $request->difficulty ?? 'all'
        ]);
    }
} 