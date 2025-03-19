<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;

class MahasiswaController extends Controller
{
    public function dashboard()
    {
        $materials = Material::with(['progress' => function($query) {
            $query->where('user_id', auth()->id());
        }, 'questions'])->get();

        $dashboardMaterials = $materials->map(function($material) {
            $totalQuestions = $material->questions->count();
            
            // Get progress
            $progress = $material->progress;
            
            // Calculate progress percentage
            $progressPercentage = $progress ? $progress->value : 0;

            return (object)[
                'id' => $material->id,
                'title' => $material->title,
                'description' => $material->description,
                'progress_percentage' => $progressPercentage,
                'total_questions' => $totalQuestions,
                'completed_questions' => round(($progressPercentage / 100) * $totalQuestions)
            ];
        });

        return view('mahasiswa.dashboard.dashboard', [
            'materials' => $materials, // For navbar
            'dashboardMaterials' => $dashboardMaterials // For dashboard cards
        ]);
    }

    public function materi($slug = null)
    {
        $materials = Material::all();
        if ($slug) {
            $material = Material::where('title', str_replace('-', ' ', $slug))->firstOrFail();
            return view('mahasiswa.materi', compact('materials', 'material'));
        }
        return view('mahasiswa.materi', compact('materials'));
    }
}