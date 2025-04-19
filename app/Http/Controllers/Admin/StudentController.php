<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index()
    {
        // Get all users with role_id 2 (students)
        $students = User::where('role_id', 2)
            ->withCount(['answeredQuestions as total_answered_questions'])
            ->with(['progress', 'materials' => function($query) {
                $query->withCount('questions');
            }])
            ->get()
            ->map(function($student) {
                // Hitung total soal dari semua materi
                $totalQuestions = $student->materials->sum('questions_count');
                
                // Hitung total jawaban benar
                $correctAnswers = $student->progress()
                    ->where('is_correct', true)
                    ->count();
                
                // Hitung persentase keseluruhan
                $student->overall_progress = $totalQuestions > 0 
                    ? min(100, round(($correctAnswers / $totalQuestions) * 100))
                    : 0;
                
                return $student;
            });

        return view('admin.students.index', [
            'students' => $students,
            'userName' => auth()->user()->name,
            'userRole' => auth()->user()->role_id == 1 ? 'Admin' : 'Mahasiswa'
        ]);
    }

    public function progress(User $student)
    {
        // Ensure we're looking at a student
        abort_if($student->role_id != 2, 404);

        // Get materials with progress
        $materials = DB::table('materials')
            ->leftJoin('questions', 'materials.id', '=', 'questions.material_id')
            ->leftJoin('progress', function($join) use ($student) {
                $join->on('questions.id', '=', 'progress.question_id')
                    ->where('progress.user_id', '=', $student->id)
                    ->where('progress.is_correct', '=', true);
            })
            ->select(
                'materials.id',
                'materials.title',
                DB::raw('COUNT(DISTINCT questions.id) as total_questions'),
                DB::raw('COUNT(DISTINCT progress.question_id) as answered_questions'),
                DB::raw('MAX(progress.updated_at) as last_accessed')
            )
            ->groupBy('materials.id', 'materials.title')
            ->get()
            ->map(function($material) {
                $material->progress = $material->total_questions > 0 
                    ? round(($material->answered_questions / $material->total_questions) * 100)
                    : 0;
                
                // Convert last_accessed to Carbon instance if it exists
                $material->last_accessed = $material->last_accessed 
                    ? \Carbon\Carbon::parse($material->last_accessed)
                    : null;
                
                return $material;
            });

        // Get recent activities
        $recent_activities = DB::table('progress')
            ->join('questions', 'progress.question_id', '=', 'questions.id')
            ->join('materials', 'questions.material_id', '=', 'materials.id')
            ->where('progress.user_id', $student->id)
            ->select(
                'materials.title as material_title',
                'questions.question_text as question_title',
                'progress.is_correct',
                'progress.created_at'
            )
            ->orderBy('progress.created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.students.progress', [
            'student' => $student,
            'materials' => $materials,
            'recent_activities' => $recent_activities,
            'userName' => auth()->user()->name,
            'userRole' => auth()->user()->role_id == 1 ? 'Admin' : 'Mahasiswa'
        ]);
    }
}