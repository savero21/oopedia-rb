<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;
use Illuminate\Support\Facades\DB;
use App\Models\Progress;

class MateriController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        
        $materials = Material::with(['questions', 'progress' => function($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('is_correct', true);
        }])->get()->map(function($material) {
            // Hitung total soal
            $totalQuestions = $material->questions->count();
            
            // Hitung jawaban benar
            $correctAnswers = $material->progress->count();
            
            // Hitung persentase
            $progressPercentage = $totalQuestions > 0 
                ? round(($correctAnswers / $totalQuestions) * 100) 
                : 0;
                
            $material->progress_percentage = $progressPercentage;
            return $material;
        });
        
        return view('mahasiswa.materials.index', compact('materials'));
    }

    public function show($id)
    {
        $material = Material::with(['questions' => function($query) {
            $query->with('answers')->orderBy('id', 'asc');
        }])->findOrFail($id);
        
        // Get user's progress
        $answeredQuestionIds = Progress::where('user_id', auth()->id())
            ->where('material_id', $id)
            ->where('is_correct', true)
            ->pluck('question_id')
            ->toArray();
        
        // Get first unanswered question or first question if all answered
        $currentQuestion = $material->questions
            ->whereNotIn('id', $answeredQuestionIds)
            ->first();
        
        // If all questions are answered, show the first one
        if (!$currentQuestion && $material->questions->count() > 0) {
            $currentQuestion = $material->questions->first();
        }
        
        // Calculate current question number based on answered questions
        $answeredCount = count($answeredQuestionIds);
        $currentQuestionNumber = $answeredCount + 1;

        // If all questions are answered, show "Review" instead of a number
        if ($answeredCount >= $material->questions->count()) {
            $currentQuestionNumber = "Review";
        }
        
        // Get all materials for sidebar
        $materials = Material::all();
        
        return view('mahasiswa.materials.show', compact('material', 'materials', 'currentQuestion', 'currentQuestionNumber'));
    }

    public function updateProgress($id)
    {
        // Add your progress update logic here
    }

    public function dashboard()
    {
        $dashboardMaterials = Material::select([
            'id',
            'title',
            DB::raw('SUBSTRING(description, 1, 150) as description'), // Batasi deskripsi ke 150 karakter
            // ... field lainnya
        ])->get()->map(function($material) {
            // Bersihkan HTML tags dan batasi panjang teks
            $material->description = strip_tags($material->description);
            // Tambahkan ellipsis jika teks terpotong
            if(strlen($material->description) >= 150) {
                $material->description .= '...';
            }
            return $material;
        });

        return view('mahasiswa.dashboard.dashboard', compact('dashboardMaterials'));
    }
} 