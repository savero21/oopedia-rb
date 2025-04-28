<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MaterialController extends Controller
{
    public function show($id)
    {
        $material = Material::findOrFail($id);
        
        // Untuk sidebar
        $materials = Material::orderBy('created_at', 'asc')->get();
        
        // If user is guest, only show half of the materials
        if (auth()->user()->role_id === 4) {
            $totalMaterials = $materials->count();
            $materialsToShow = ceil($totalMaterials / 2);
            $materials = $materials->take($materialsToShow);
        }
        
        return view('mahasiswa.materials.show', compact('material', 'materials'));
    }

    public function index()
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

        // Get all materials first
        $allMaterials = Material::with(['questions'])->orderBy('created_at', 'asc')->get();
        
        // If user is guest, only show half of the materials
        if (auth()->user()->role_id === 4) {
            $totalMaterials = $allMaterials->count();
            $materialsToShow = ceil($totalMaterials / 2);
            $allMaterials = $allMaterials->take($materialsToShow);
        }

        $materials = $allMaterials->map(function($material) use ($progressStats) {
            $totalQuestions = $material->questions->count();
            $materialProgress = $progressStats->firstWhere('material_id', $material->id);
            
            $correctAnswers = $materialProgress ? $materialProgress->correct_answers : 0;
            $progressPercentage = $totalQuestions > 0 
                ? min(100, round(($correctAnswers / $totalQuestions) * 100))
                : 0;

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

        return redirect()->route('mahasiswa.materials.questions.show', ['material' => $id])
            ->with('success', 'Progress direset. Anda dapat mengerjakan soal kembali.');
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