<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Material;
use App\Models\Question;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->role;

        $mahasiswaCount = User::whereHas('role', function ($query) {
            $query->where('role_name', 'Mahasiswa');
        })->count();

        $materialCount = Material::count(); // Menghitung jumlah materi

        // Updated to use the progress table
        $students = User::where('role_id', 2)
            ->withCount(['progress as progress' => function($query) {
                $query->selectRaw('ROUND((COUNT(CASE WHEN is_answered = 1 THEN 1 END) * 100.0) / 
                    (SELECT COUNT(*) FROM questions), 2)');
            }])
            ->get();

        return view('dashboard.index', [
            'userName' => $user->name,
            'userRole' => $role->role_name, // Get the role name
            'mahasiswaCount' => $mahasiswaCount, // Tambahkan jumlah mahasiswa ke view
            'materialCount' => $materialCount, // Tambahkan jumlah materi ke view
            'students' => $students
        ]);
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