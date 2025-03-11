<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index()
    {
        // Get all users with role_id 2 (students)
        $students = User::where('role_id', 2)
            ->withCount(['answers as total_answered_questions'])
            ->with(['materials' => function($query) {
                $query->select('materials.*')
                    ->withCount(['questions', 'answers as completed_questions']);
            }])
            ->get()
            ->map(function($student) {
                // Calculate overall progress
                $materials_progress = $student->materials->map(function($material) {
                    return [
                        'total' => $material->questions_count,
                        'completed' => $material->completed_questions
                    ];
                });
                
                $total_questions = $materials_progress->sum('total');
                $completed_questions = $materials_progress->sum('completed');
                
                $student->materials_progress = $total_questions > 0 
                    ? round(($completed_questions / $total_questions) * 100) 
                    : 0;
                
                return $student;
            });

        return view('students.index', [
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
            ->leftJoin('answers', function($join) use ($student) {
                $join->on('questions.id', '=', 'answers.question_id')
                    ->where('answers.user_id', '=', $student->id);
            })
            ->select(
                'materials.id',
                'materials.title',
                DB::raw('COUNT(DISTINCT questions.id) as total_questions'),
                DB::raw('COUNT(DISTINCT answers.question_id) as answered_questions')
            )
            ->groupBy('materials.id', 'materials.title')
            ->get()
            ->map(function($material) {
                $material->student_progress = $material->total_questions > 0 
                    ? round(($material->answered_questions / $material->total_questions) * 100)
                    : 0;
                return $material;
            });

        // Get recent activities
        $recent_activities = DB::table('answers')
            ->join('questions', 'answers.question_id', '=', 'questions.id')
            ->join('materials', 'questions.material_id', '=', 'materials.id')
            ->where('answers.user_id', $student->id)
            ->select(
                'materials.title as material_title',
                'questions.title as question_title',
                'answers.is_correct',
                'answers.created_at'
            )
            ->orderBy('answers.created_at', 'desc')
            ->limit(10)
            ->get();

        return view('students.progress', [
            'student' => $student,
            'materials' => $materials,
            'recent_activities' => $recent_activities,
            'userName' => auth()->user()->name,
            'userRole' => auth()->user()->role_id == 1 ? 'Admin' : 'Mahasiswa'
        ]);
    }
}