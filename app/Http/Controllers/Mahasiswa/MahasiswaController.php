<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\User;
use App\Models\Question;
use App\Models\Progress;
use Illuminate\Support\Facades\DB;

class MahasiswaController extends Controller
{
    public function dashboard()
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

        $materials = Material::with(['questions'])->get()
            ->map(function($material) use ($progressStats) {
                $totalQuestions = $material->questions->count();
                $materialProgress = $progressStats->firstWhere('material_id', $material->id);
                
                $correctAnswers = $materialProgress ? $materialProgress->correct_answers : 0;
                $progressPercentage = $totalQuestions > 0 
                    ? min(100, round(($correctAnswers / $totalQuestions) * 100))
                    : 0;

                return (object)[
                    'id' => $material->id,
                    'title' => $material->title,
                    'description' => $material->description,
                    'progress_percentage' => $progressPercentage,
                    'total_questions' => $totalQuestions,
                    'completed_questions' => $correctAnswers
                ];
            });

        return view('mahasiswa.dashboard.dashboard', [
            'materials' => Material::all(), // For navbar
            'dashboardMaterials' => $materials // For dashboard cards
        ]);
    }

    public function materi($slug = null)
    {
        $materials = Material::all();
        if ($slug) {
            $material = Material::where('title', str_replace('-', ' ', $slug))->firstOrFail();
            return view('mahasiswa.materi', compact('materials', 'material'));
        }
        return view('mahasiswa.materi', compact('materials'));
    }

    public function leaderboard()
    {
        // Hitung total pertanyaan
        $totalQuestions = Question::count();
        
        // Ambil data progress untuk semua mahasiswa
        $leaderboardData = DB::table('users')
            ->leftJoin('progress', 'users.id', '=', 'progress.user_id')
            ->leftJoin('questions', 'progress.question_id', '=', 'questions.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COUNT(DISTINCT CASE WHEN progress.is_correct = 1 THEN questions.id END) as total_correct_questions'),
                DB::raw('COUNT(DISTINCT questions.id) as total_attempted'),
                DB::raw('SUM(CASE WHEN progress.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers'),
                DB::raw('MAX(progress.updated_at) as completion_date'),
                DB::raw('COUNT(DISTINCT CASE WHEN progress.is_correct = 1 AND questions.difficulty = "beginner" THEN questions.id END) as beginner_completed'),
                DB::raw('COUNT(DISTINCT CASE WHEN progress.is_correct = 1 AND questions.difficulty = "medium" THEN questions.id END) as medium_completed'),
                DB::raw('COUNT(DISTINCT CASE WHEN progress.is_correct = 1 AND questions.difficulty = "hard" THEN questions.id END) as hard_completed'),
                DB::raw('COUNT(progress.id) as total_attempts'),
                DB::raw('SUM(
                    CASE 
                        WHEN progress.is_correct = 1 AND questions.difficulty = "beginner" THEN 
                            CASE 
                                WHEN (
                                    SELECT COUNT(*) 
                                    FROM progress p2 
                                    WHERE p2.user_id = users.id 
                                    AND p2.question_id = questions.id 
                                    AND p2.created_at <= progress.created_at
                                ) = 1 THEN 3
                                WHEN (
                                    SELECT COUNT(*) 
                                    FROM progress p2 
                                    WHERE p2.user_id = users.id 
                                    AND p2.question_id = questions.id 
                                    AND p2.created_at <= progress.created_at
                                ) = 2 THEN 2
                                ELSE 1
                            END
                        WHEN progress.is_correct = 1 AND questions.difficulty = "medium" THEN 
                            CASE 
                                WHEN (
                                    SELECT COUNT(*) 
                                    FROM progress p2 
                                    WHERE p2.user_id = users.id 
                                    AND p2.question_id = questions.id 
                                    AND p2.created_at <= progress.created_at
                                ) = 1 THEN 6
                                WHEN (
                                    SELECT COUNT(*) 
                                    FROM progress p2 
                                    WHERE p2.user_id = users.id 
                                    AND p2.question_id = questions.id 
                                    AND p2.created_at <= progress.created_at
                                ) = 2 THEN 4
                                ELSE 2
                            END
                        WHEN progress.is_correct = 1 AND questions.difficulty = "hard" THEN 
                            CASE 
                                WHEN (
                                    SELECT COUNT(*) 
                                    FROM progress p2 
                                    WHERE p2.user_id = users.id 
                                    AND p2.question_id = questions.id 
                                    AND p2.created_at <= progress.created_at
                                ) = 1 THEN 9
                                WHEN (
                                    SELECT COUNT(*) 
                                    FROM progress p2 
                                    WHERE p2.user_id = users.id 
                                    AND p2.question_id = questions.id 
                                    AND p2.created_at <= progress.created_at
                                ) = 2 THEN 6
                                WHEN (
                                    SELECT COUNT(*) 
                                    FROM progress p2 
                                    WHERE p2.user_id = users.id 
                                    AND p2.question_id = questions.id 
                                    AND p2.created_at <= progress.created_at
                                ) = 3 THEN 4
                                ELSE 2
                            END
                        ELSE 0 
                    END) as weighted_score'),
                DB::raw('AVG(TIMESTAMPDIFF(SECOND, progress.created_at, progress.updated_at)) as avg_completion_time')
            )
            ->where('users.role_id', 3)
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('weighted_score')
            ->orderByDesc('correct_answers')
            ->orderBy('avg_completion_time')
            ->get();
        
        // Hitung total soal per tingkat kesulitan
        $totalBeginner = Question::where('difficulty', 'beginner')->count();
        $totalMedium = Question::where('difficulty', 'medium')->count();
        $totalHard = Question::where('difficulty', 'hard')->count();
        
        // Tambahkan peringkat dan persentase
        $rank = 1;
        foreach ($leaderboardData as $index => $data) {
            $data->rank = $rank++;
            
            $data->percentage = $totalQuestions > 0 
                ? round(($data->total_correct_questions / $totalQuestions) * 100, 1) 
                : 0;
            
            if ($data->total_correct_questions >= $totalQuestions) {
                $data->percentage = 100;
            }
            
            $data->formatted_score = number_format($data->weighted_score, 0, ',', '.');
            
            if ($data->hard_completed >= $totalHard && $totalHard > 0) {
                $data->badge = 'Hard';
                $data->badge_color = 'danger';
            } elseif ($data->medium_completed >= $totalMedium && $totalMedium > 0) {
                $data->badge = 'Medium';
                $data->badge_color = 'warning';
            } elseif ($data->beginner_completed >= $totalBeginner && $totalBeginner > 0) {
                $data->badge = 'Beginner';
                $data->badge_color = 'success';
            } else {
                $data->badge = 'Learner';
                $data->badge_color = 'secondary';
            }
            
            // Tambahkan perhitungan attempts per question
            $data->attempts_per_question = DB::table('progress')
                ->select('question_id', DB::raw('COUNT(*) as attempts'))
                ->where('user_id', $data->id)
                ->groupBy('question_id')
                ->get()
                ->avg('attempts');
            
            $data->show_attempts = $data->attempts_per_question > 1;
        }
        
        // Cari posisi user saat ini
        $currentUserRank = null;
        foreach ($leaderboardData as $index => $data) {
            if ($data->id === auth()->id()) {
                $currentUserRank = $data;
                break;
            }
        }
        
        return view('mahasiswa.leaderboard', [
            'leaderboardData' => $leaderboardData,
            'currentUserRank' => $currentUserRank,
            'totalQuestions' => $totalQuestions,
            'materials' => Material::all() // Untuk navbar
        ]);
    }
}