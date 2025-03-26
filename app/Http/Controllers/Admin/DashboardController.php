<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Material;
use App\Models\Question;
use App\Models\Progress;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get authenticated user data
        $user = auth()->user();
        $userName = $user->name;
        $userRole = $user->role->role_name;

        // Statistics Cards
        $totalStudents = User::where('role_id', 2)->count();
        $totalMaterials = Material::count();
        $totalQuestions = Question::count();
        
        $activeStudents = $this->getActiveStudentsCount();

        // Recent Student Progress
        $recentProgress = Progress::with(['user', 'material'])
            ->where('is_correct', true)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Student Progress Overview with completion percentage
        $studentProgress = User::where('role_id', 2)
            ->withCount(['progress as completed_questions' => function($query) {
                $query->where('is_correct', true);
            }])
            ->with(['progress' => function($query) {
                $query->where('is_correct', true)
                      ->with('material:id,title');
            }])
            ->having('completed_questions', '>', 0)
            ->orderByDesc('completed_questions')
            ->limit(5)
            ->get()
            ->map(function($student) use ($totalMaterials) {
                // Count unique completed materials
                $completedMaterialsCount = $student->progress
                    ->pluck('material')
                    ->unique('id')
                    ->count();
                
                // Calculate progress percentage
                $student->materials_progress = $totalMaterials > 0 
                    ? round(($completedMaterialsCount / $totalMaterials) * 100) 
                    : 0;
                
                return $student;
            });

        return view('admin.dashboard.index', compact(
            'userName',
            'userRole',
            'totalStudents',
            'totalMaterials',
            'totalQuestions',
            'activeStudents',
            'recentProgress',
            'studentProgress'
        ));
    }

    public function dashboard()
    {
        // Get all students (assuming role_id 2 is for students)
        $students = User::where('role_id', 2)
            ->with(['answers']) // Assuming you have an answers relationship
            ->get()
            ->map(function ($student) {
                // Calculate progress percentage
                $totalQuestions = Question::count();
                $answeredQuestions = $student->answers->unique('question_id')->count();
                
                $progress = $totalQuestions > 0 
                    ? min(100, round(($answeredQuestions / $totalQuestions) * 100)) 
                    : 0;

                $student->progress = $progress;
                return $student;
            });

        return view('admin.dashboard', [
            'students' => $students,
            'activePage' => 'dashboard',
            'userName' => auth()->user()->name,
            'userRole' => 'Admin'
        ]);
    }

    private function getActiveStudentsCount()
    {
        // Ambil mahasiswa yang memiliki aktivitas dalam 7 hari terakhir
        return DB::table('users')
            ->join('progress', 'users.id', '=', 'progress.user_id')
            ->where('users.role_id', 2) // role mahasiswa
            ->where('progress.created_at', '>=', now()->subDays(7))
            ->distinct('users.id')
            ->count('users.id');
    }

    private function getMaterialStatistics()
    {
        return DB::table('materials')
            ->leftJoin('questions', 'materials.id', '=', 'questions.material_id')
            ->leftJoin('progress', 'questions.id', '=', 'progress.question_id')
            ->select(
                'materials.id',
                'materials.title',
                DB::raw('COUNT(DISTINCT questions.id) as questions_count'),
                DB::raw('COUNT(DISTINCT CASE 
                    WHEN (
                        SELECT COUNT(*) 
                        FROM progress p2 
                        JOIN questions q2 ON p2.question_id = q2.id 
                        WHERE q2.material_id = materials.id 
                        AND p2.user_id = progress.user_id 
                        AND p2.is_correct = 1
                    ) = COUNT(DISTINCT questions.id) 
                    THEN progress.user_id 
                    END) as completed_students_count'),
                DB::raw('ROUND(COUNT(DISTINCT CASE 
                    WHEN (
                        SELECT COUNT(*) 
                        FROM progress p2 
                        JOIN questions q2 ON p2.question_id = q2.id 
                        WHERE q2.material_id = materials.id 
                        AND p2.user_id = progress.user_id 
                        AND p2.is_correct = 1
                    ) = COUNT(DISTINCT questions.id) 
                    THEN progress.user_id 
                    END) * 100.0 / 
                    (SELECT COUNT(*) FROM users WHERE role_id = 2), 1) as completion_rate')
            )
            ->groupBy('materials.id', 'materials.title')
            ->get();
    }
}