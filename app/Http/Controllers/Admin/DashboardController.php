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
        
        // Active Students (students who have made progress in the last 7 days)
        $activeStudents = Progress::select('user_id')
            ->where('created_at', '>=', now()->subDays(7))
            ->distinct()
            ->count();

        // Most Active Materials (materials with most student completion)
        $popularMaterials = Material::withCount(['questions', 'progress' => function($query) {
                $query->where('is_correct', true);
            }])
            ->orderBy('progress_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function($material) {
                if ($material->questions_count > 0) {
                    $material->completion_rate = round(($material->progress_count / $material->questions_count) * 100, 1);
                } else {
                    $material->completion_rate = 0;
                }
                return $material;
            });

        // Recent Student Progress (only showing successful completions)
        $recentProgress = Progress::with(['user', 'material'])
            ->where('is_correct', true)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Student Progress Overview (focusing on completion rather than accuracy)
        $studentProgress = User::where('role_id', 2)
            ->withCount(['progress as completed_questions' => function($query) {
                $query->where('is_correct', true);
            }])
            ->having('completed_questions', '>', 0)
            ->orderByDesc('completed_questions')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'userName',
            'userRole',
            'totalStudents',
            'totalMaterials',
            'totalQuestions',
            'activeStudents',
            'popularMaterials',
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
                    ? round(($answeredQuestions / $totalQuestions) * 100) 
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
}