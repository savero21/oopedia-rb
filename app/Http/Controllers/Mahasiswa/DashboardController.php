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
        $materialProgressPercentage = $totalMaterials > 0 
            ? round(($completedMaterials / $totalMaterials) * 100) 
            : 0;
            
        $questionProgressPercentage = $totalQuestions > 0 
            ? round(($totalCorrectQuestions / $totalQuestions) * 100) 
            : 0;

        // Get recent activities
        $recentActivities = DB::table('progress as p1')
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

        return view('mahasiswa.dashboard.index', compact(
            'totalMaterials',
            'totalQuestions',
            'completedMaterials',
            'inProgressMaterials',
            'materialProgressPercentage',
            'questionProgressPercentage',
            'totalAnsweredQuestions',
            'totalCorrectQuestions',
            'allMaterials',
            'recentActivities'
        ));
    }

    public function inProgress()
    {
        $userId = auth()->id();
        
        // Get progress statistics
        $progressStats = DB::table('progress')
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
            ->filter(function($material) use ($progressStats) {
                $materialProgress = $progressStats->firstWhere('material_id', $material->id);
                $totalQuestions = $material->questions->count();
                
                if ($materialProgress && $totalQuestions > 0) {
                    $correctAnswers = $materialProgress->correct_answers;
                    return $correctAnswers > 0 && $correctAnswers < $totalQuestions;
                }
                
                return false;
            });

        return view('mahasiswa.dashboard.in-progress', [
            'materials' => $materials,
            'progressStats' => $progressStats
        ]);
    }

    public function complete()
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

        $materials = Material::with(['questions'])
            ->get()
            ->filter(function($material) use ($progressStats) {
                $materialProgress = $progressStats->firstWhere('material_id', $material->id);
                $totalQuestions = $material->questions->count();
                
                if ($materialProgress && $totalQuestions > 0) {
                    $correctAnswers = $materialProgress->correct_answers;
                    return $correctAnswers == $totalQuestions;
                }
                
                return false;
            });

        return view('mahasiswa.dashboard.complete', compact('materials'));
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
}
