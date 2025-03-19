<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Progress;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function show($id)
    {
        $material = Material::with(['questions' => function($query) {
            $query->with('answers')->orderBy('id', 'asc');
        }])->findOrFail($id);
        
        $answeredQuestionIds = Progress::where('user_id', auth()->id())
            ->where('material_id', $id)
            ->where('is_correct', true)
            ->pluck('question_id')
            ->toArray();
        
        $currentQuestion = $material->questions
            ->whereNotIn('id', $answeredQuestionIds)
            ->first();
        
        if (!$currentQuestion && $material->questions->count() > 0) {
            $currentQuestion = $material->questions->first();
        }
        
        $answeredCount = count($answeredQuestionIds);
        $currentQuestionNumber = $answeredCount + 1;

        if ($answeredCount >= $material->questions->count()) {
            $currentQuestionNumber = "Review";
        }
        
        $materials = Material::all();
        
        return view('mahasiswa.materials.show', compact('material', 'materials', 'currentQuestion', 'currentQuestionNumber'));
    }

    public function index()
    {
        $userId = auth()->id();
        
        $materials = Material::with(['questions', 'progress' => function($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('is_correct', true);
        }])->get()->map(function($material) {
            // Hitung total soal
            $totalQuestions = $material->questions->count();
            
            // Hitung jawaban benar - pastikan progress tidak null
            $correctAnswers = $material->progress ? $material->progress->count() : 0;
            
            // Pastikan correctAnswers tidak melebihi totalQuestions
            $correctAnswers = min($correctAnswers, $totalQuestions);
            
            // Hitung persentase
            $progressPercentage = $totalQuestions > 0 
                ? min(100, round(($correctAnswers / $totalQuestions) * 100))
                : 0;
                
            $material->progress_percentage = $progressPercentage;
            $material->completed_questions = $correctAnswers;
            $material->total_questions = $totalQuestions;
            
            return $material;
        });
        
        return view('mahasiswa.materials.index', compact('materials'));
    }

    public function reset($id)
    {
        // Delete all progress for this material
        Progress::where('user_id', auth()->id())
            ->where('material_id', $id)
            ->delete();

        return redirect()->route('mahasiswa.materials.show', ['material' => $id])
            ->with('success', 'Progress direset. Anda dapat mengerjakan soal kembali.');
    }
} 