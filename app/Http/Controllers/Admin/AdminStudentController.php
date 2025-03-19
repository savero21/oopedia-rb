<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Material;
use App\Models\Progress;
use App\Models\Question;
use Illuminate\Http\Request;

class AdminStudentController extends Controller
{
    public function index()
    {
        $students = User::where('role_id', 2)
            ->get()
            ->map(function ($student) {
                $student->overall_progress = $this->calculateOverallProgress($student);
                return $student;
            });

        return view('students.index', compact('students'));
    }

    public function progress(User $student)
    {
        if ($student->role_id !== 2) {
            abort(404);
        }

        $materials = Material::all()->map(function ($material) use ($student) {
            // Calculate progress for each material
            $totalQuestions = $material->questions()->count();
            $correctAnswers = Progress::where('user_id', $student->id)
                ->where('material_id', $material->id)
                ->where('is_correct', true)
                ->count();
            
            $material->progress = $totalQuestions > 0 
                ? round(($correctAnswers / $totalQuestions) * 100) 
                : 0;
                
            $material->last_accessed = Progress::where('user_id', $student->id)
                ->where('material_id', $material->id)
                ->latest()
                ->value('updated_at');
                
            return $material;
        });

        return view('students.progress', compact('student', 'materials'));
    }

    private function calculateOverallProgress($student)
    {
        $totalQuestions = Question::count();
        if ($totalQuestions === 0) return 0;

        $correctAnswers = Progress::where('user_id', $student->id)
            ->where('is_correct', true)
            ->count();

        return round(($correctAnswers / $totalQuestions) * 100);
    }
}