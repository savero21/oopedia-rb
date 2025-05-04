<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Progress;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // Get counts
        $totalMaterials = Material::count();
        $totalQuestions = Question::count();
        
        // Count questions by difficulty
        $easyQuestions = Question::where('difficulty', 'beginner')->count();
        $mediumQuestions = Question::where('difficulty', 'medium')->count();
        $hardQuestions = Question::where('difficulty', 'hard')->count();
        
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

        // Calculate material statistics
        $completedMaterials = 0;
        $inProgressMaterials = 0;
        $totalMaterialProgress = 0;

        // Calculate question statistics
        $totalAnsweredQuestions = 0;
        $totalCorrectQuestions = 0;

        $allMaterials = Material::with(['questions'])
            ->select('id', 'title', 'content')
            ->get()
            ->map(function ($material) use ($progressStats, &$completedMaterials, &$inProgressMaterials, &$totalAnsweredQuestions, &$totalCorrectQuestions) {
                $totalQuestions = $material->questions->count();
                $materialProgress = $progressStats->firstWhere('material_id', $material->id);
                
                if ($materialProgress) {
                    $answeredQuestions = $materialProgress->answered_questions;
                    $correctAnswers = $materialProgress->correct_answers;
                    
                    // Update question statistics
                    $totalAnsweredQuestions += $answeredQuestions;
                    $totalCorrectQuestions += $correctAnswers;
                    
                    // Calculate progress percentage
                    $progress = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;
                    $progress = round($progress);
                    
                    // Update material counts
                    if ($progress == 100) {
                        $completedMaterials++;
                    } elseif ($progress > 0) {
                        $inProgressMaterials++;
                    }
                    
                    $material->progress = $progress;
                    $material->answered_questions = $answeredQuestions;
                    $material->correct_answers = $correctAnswers;
                } else {
                    $material->progress = 0;
                    $material->answered_questions = 0;
                    $material->correct_answers = 0;
                }
                
                $material->total_questions = $totalQuestions;
                return $material;
            });

        // Calculate overall progress percentages
        $materialProgressPercentage = $totalMaterials > 0 ? round(($completedMaterials / $totalMaterials) * 100) : 0;
        $questionProgressPercentage = $totalQuestions > 0 ? round(($totalCorrectQuestions / $totalQuestions) * 100) : 0;

        // Get recent activities
        $recentActivities = $this->getRecentActivities($userId);

        return view('mahasiswa.dashboard.index', compact(
            'totalMaterials',
            'totalQuestions',
            'easyQuestions',
            'mediumQuestions',
            'hardQuestions',
            'materialProgressPercentage',
            'questionProgressPercentage',
            'completedMaterials',
            'totalCorrectQuestions',
            'recentActivities',
            'allMaterials'
        ));
    }

    public function inProgress()
    {
        $userId = auth()->id();
        
        // Get progress statistics grouped by material and difficulty
        $progressStats = DB::table('progress')
            ->select(
                'progress.material_id',
                'questions.difficulty',
                DB::raw('COUNT(DISTINCT progress.question_id) as total_answered'),
                DB::raw('SUM(CASE WHEN progress.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
            )
            ->join('questions', 'progress.question_id', '=', 'questions.id')
            ->where('progress.user_id', $userId)
            ->groupBy('progress.material_id', 'questions.difficulty')
            ->get();
    
        // Get stats for all progress (used for filtering materials)
        $materialProgress = DB::table('progress')
            ->select(
                'material_id',
                DB::raw('COUNT(DISTINCT question_id) as total_answered'),
                DB::raw('SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
            )
            ->where('user_id', $userId)
            ->groupBy('material_id')
            ->get();
    
        $materials = Material::with(['questions'])
            ->get()
            ->filter(function($material) use ($materialProgress) {
                $progress = $materialProgress->firstWhere('material_id', $material->id);
                $totalQuestions = $material->questions->count();
                
                if ($progress && $totalQuestions > 0) {
                    $correctAnswers = $progress->correct_answers;
                    return $correctAnswers > 0 && $correctAnswers < $totalQuestions;
                }
                
                return false;
            });
    
        // Calculate stats for each difficulty level
        $materialsWithStats = $materials->map(function($material) use ($progressStats) {
            // Get questions by difficulty
            $beginnerQuestions = $material->questions->where('difficulty', 'beginner');
            $mediumQuestions = $material->questions->where('difficulty', 'medium');
            $hardQuestions = $material->questions->where('difficulty', 'hard');
            
            // Calculate stats for beginner questions
            $beginnerStats = $progressStats->where('material_id', $material->id)
                ->where('difficulty', 'beginner')
                ->first();
            $beginnerCorrect = $beginnerStats ? $beginnerStats->correct_answers : 0;
            $beginnerTotal = $beginnerQuestions->count();
            $beginnerPercentage = $beginnerTotal > 0 ? round(($beginnerCorrect / $beginnerTotal) * 100) : 0;
            
            // Calculate stats for medium questions
            $mediumStats = $progressStats->where('material_id', $material->id)
                ->where('difficulty', 'medium')
                ->first();
            $mediumCorrect = $mediumStats ? $mediumStats->correct_answers : 0;
            $mediumTotal = $mediumQuestions->count();
            $mediumPercentage = $mediumTotal > 0 ? round(($mediumCorrect / $mediumTotal) * 100) : 0;
            
            // Calculate stats for hard questions
            $hardStats = $progressStats->where('material_id', $material->id)
                ->where('difficulty', 'hard')
                ->first();
            $hardCorrect = $hardStats ? $hardStats->correct_answers : 0;
            $hardTotal = $hardQuestions->count();
            $hardPercentage = $hardTotal > 0 ? round(($hardCorrect / $hardTotal) * 100) : 0;
            
            // Overall stats
            $totalCorrect = $beginnerCorrect + $mediumCorrect + $hardCorrect;
            $totalQuestions = $beginnerTotal + $mediumTotal + $hardTotal;
            $overallPercentage = $totalQuestions > 0 ? round(($totalCorrect / $totalQuestions) * 100) : 0;
            
            return [
                'material' => $material,
                'stats' => [
                    'overall' => [
                        'correct' => $totalCorrect,
                        'total' => $totalQuestions,
                        'percentage' => $overallPercentage
                    ],
                    'beginner' => [
                        'correct' => $beginnerCorrect,
                        'total' => $beginnerTotal,
                        'percentage' => $beginnerPercentage
                    ],
                    'medium' => [
                        'correct' => $mediumCorrect,
                        'total' => $mediumTotal,
                        'percentage' => $mediumPercentage
                    ],
                    'hard' => [
                        'correct' => $hardCorrect,
                        'total' => $hardTotal,
                        'percentage' => $hardPercentage
                    ]
                ]
            ];
        });
    
        return view('mahasiswa.dashboard.in-progress', [
            'materialsWithStats' => $materialsWithStats
        ]);
    }

    public function complete()
    {
        $userId = auth()->id();
        
        // Get progress statistics grouped by material and difficulty
        $progressStats = DB::table('progress')
            ->select(
                'progress.material_id',
                'questions.difficulty',
                DB::raw('COUNT(DISTINCT progress.question_id) as total_answered'),
                DB::raw('SUM(CASE WHEN progress.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
            )
            ->join('questions', 'progress.question_id', '=', 'questions.id')
            ->where('progress.user_id', $userId)
            ->groupBy('progress.material_id', 'questions.difficulty')
            ->get();

        // Get stats for all progress (used for filtering materials)
        $materialProgress = DB::table('progress')
            ->select(
                'material_id',
                DB::raw('COUNT(DISTINCT question_id) as total_answered'),
                DB::raw('SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
            )
            ->where('user_id', $userId)
            ->groupBy('material_id')
            ->get();

        $materials = Material::with(['questions'])
            ->get()
            ->filter(function($material) use ($materialProgress) {
                $progress = $materialProgress->firstWhere('material_id', $material->id);
                $totalQuestions = $material->questions->count();
                
                if ($progress && $totalQuestions > 0) {
                    $correctAnswers = $progress->correct_answers;
                    return $correctAnswers == $totalQuestions; // Only 100% completed materials
                }
                
                return false;
            });

        // Calculate stats for each difficulty level
        $materialsWithStats = $materials->map(function($material) use ($progressStats) {
            // Get questions by difficulty
            $beginnerQuestions = $material->questions->where('difficulty', 'beginner');
            $mediumQuestions = $material->questions->where('difficulty', 'medium');
            $hardQuestions = $material->questions->where('difficulty', 'hard');
            
            // Calculate stats for beginner questions
            $beginnerStats = $progressStats->where('material_id', $material->id)
                ->where('difficulty', 'beginner')
                ->first();
            $beginnerCorrect = $beginnerStats ? $beginnerStats->correct_answers : 0;
            $beginnerTotal = $beginnerQuestions->count();
            
            // Calculate stats for medium questions
            $mediumStats = $progressStats->where('material_id', $material->id)
                ->where('difficulty', 'medium')
                ->first();
            $mediumCorrect = $mediumStats ? $mediumStats->correct_answers : 0;
            $mediumTotal = $mediumQuestions->count();
            
            // Calculate stats for hard questions
            $hardStats = $progressStats->where('material_id', $material->id)
                ->where('difficulty', 'hard')
                ->first();
            $hardCorrect = $hardStats ? $hardStats->correct_answers : 0;
            $hardTotal = $hardQuestions->count();
            
            // Overall stats
            $totalCorrect = $beginnerCorrect + $mediumCorrect + $hardCorrect;
            $totalQuestions = $beginnerTotal + $mediumTotal + $hardTotal;
            
            return [
                'material' => $material,
                'stats' => [
                    'overall' => [
                        'correct' => $totalQuestions, // For completed materials, correct = total
                        'total' => $totalQuestions,
                        'percentage' => 100 // Always 100% for completed materials
                    ],
                    'beginner' => [
                        'correct' => $beginnerTotal, // For completed materials, correct = total
                        'total' => $beginnerTotal,
                        'percentage' => 100 // Always 100%
                    ],
                    'medium' => [
                        'correct' => $mediumTotal, // For completed materials, correct = total
                        'total' => $mediumTotal,
                        'percentage' => 100 // Always 100%
                    ],
                    'hard' => [
                        'correct' => $hardTotal, // For completed materials, correct = total
                        'total' => $hardTotal,
                        'percentage' => 100 // Always 100%
                    ]
                ]
            ];
        });

        return view('mahasiswa.dashboard.complete', [
            'materialsWithStats' => $materialsWithStats
        ]);
    }

    public function completed()
    {
        $materials = Material::all();
        return view('mahasiswa.dashboard.completed', compact('materials'));
    }

    private function getActiveStudentsCount()
    {
        // Ambil mahasiswa yang memiliki aktivitas dalam 7 hari terakhir
        return DB::table('users')
            ->join('progress', 'users.id', '=', 'progress.user_id')
            ->where('users.role_id', 3) // Role mahasiswa sekarang adalah 3
            ->where('progress.created_at', '>=', now()->subDays(7))
            ->distinct('users.id')
            ->count('users.id');
    }

    private function determineActivityType($activity)
    {
        if ($activity->total_correct >= 5) {
            return 'achievement';
        } elseif ($activity->difficulty === 'hard' && $activity->is_correct) {
            return 'milestone';
        } else {
            return 'progress';
        }
    }

    private function getRecentActivities($userId)
    {
        // Implementasi untuk mendapatkan recent activities
        // Contoh: Menggunakan query yang sama dengan metode index()
        return DB::table('progress as p1')
            ->join('materials', 'p1.material_id', '=', 'materials.id')
            ->join('questions', 'p1.question_id', '=', 'questions.id')
            ->where('p1.user_id', $userId)
            ->where('p1.is_correct', true)
            ->whereRaw('p1.created_at = (
                SELECT MAX(p2.created_at)
                FROM progress p2
                WHERE p2.material_id = p1.material_id
                AND p2.user_id = p1.user_id
                AND p2.is_correct = true
            )')
            ->select(
                'materials.title as material_title',
                'materials.id as material_id',
                'questions.difficulty',
                'p1.created_at',
                'p1.is_correct',
                DB::raw('(
                    SELECT COUNT(DISTINCT p3.question_id) 
                    FROM progress p3 
                    WHERE p3.material_id = materials.id 
                    AND p3.user_id = ' . $userId . '
                    AND p3.is_correct = 1
                ) as total_correct')
            )
            ->orderBy('p1.created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($activity) {
                $activity->type = $this->determineActivityType($activity);
                return $activity;
            });
    }
}
