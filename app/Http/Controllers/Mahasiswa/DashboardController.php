<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Progress;

class DashboardController extends Controller
{
    public function index()
    {
        // Get authenticated user ID
        $userId = auth()->id();

        // Get total materials
        $totalMaterials = Material::count();
        
        // Get progress statistics from the progress table
        $progressStats = DB::table('progress')
            ->select(
                'material_id',
                DB::raw('COUNT(DISTINCT question_id) as answered_questions'),
                DB::raw('SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
            )
            ->where('user_id', $userId)
            ->groupBy('material_id')
            ->get();

        // Calculate completed and in progress counts
        $completedCount = 0;
        $inProgressCount = 0;
        $totalProgress = 0;

        $allMaterials = Material::with(['questions'])
            ->select('id', 'title', 'content')
            ->get()
            ->map(function ($material) use ($userId, $progressStats, &$completedCount, &$inProgressCount) {
                $materialProgress = $progressStats->firstWhere('material_id', $material->id);
                $totalQuestions = $material->questions->count();
                
                if ($materialProgress) {
                    $answeredQuestions = $materialProgress->answered_questions;
                    $correctAnswers = $materialProgress->correct_answers;
                    
                    // Calculate progress percentage
                    $progress = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;
                    
                    // Update counts
                    if ($progress == 100) {
                        $completedCount++;
                    } elseif ($progress > 0) {
                        $inProgressCount++;
                    }
                    
                    $material->progress = round($progress);
                } else {
                    $material->progress = 0;
                }
                
                $material->questions_count = $totalQuestions;
                return $material;
            });

        // Calculate total progress
        if ($totalMaterials > 0) {
            $totalProgress = round(($completedCount / $totalMaterials) * 100);
        }

        // Tambahkan variable baru untuk mahasiswa aktif
        $activeStudents = $this->getActiveStudentsCount();
        
        return view('mahasiswa.dashboard.index', compact(
            'totalMaterials',
            'completedCount',
            'inProgressCount',
            'totalProgress',
            'allMaterials',
            'activeStudents'
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
}
