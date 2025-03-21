<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            ->map(function($material) use ($progressStats) {
                $totalQuestions = $material->questions->count();
                $materialProgress = $progressStats->firstWhere('material_id', $material->id);
                
                $correctAnswers = $materialProgress ? $materialProgress->correct_answers : 0;
                $progressPercentage = $totalQuestions > 0 
                    ? min(100, round(($correctAnswers / $totalQuestions) * 100))
                    : 0;

                // Kembalikan model Material asli dengan properti tambahan
                $material->progress_percentage = $progressPercentage;
                $material->total_questions = $totalQuestions;
                $material->completed_questions = $correctAnswers;
                
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