<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Material;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Material $material)
    {
        $questions = $material->questions;
        return view('questions.index', compact('material', 'questions'));
    }

    public function create(Material $material)
    {
        return view('questions.create', compact('material'));
    }

    public function store(Request $request, Material $material)
    {
        $validated = $request->validate([
            'question_text' => 'required',
            'answer_text' => 'required',
        ]);

        $material->questions()->create($validated);

        return redirect()->route('materials.questions.index', $material)
            ->with('success', 'Question created successfully.');
    }

    public function edit(Material $material, Question $question)
    {
        return view('questions.edit', compact('material', 'question'));
    }

    public function update(Request $request, Material $material, Question $question)
    {
        $validated = $request->validate([
            'question_text' => 'required',
            'answer_text' => 'required',
        ]);

        $question->update($validated);

        return redirect()->route('materials.questions.index', $material)
            ->with('success', 'Question updated successfully.');
    }

    public function destroy(Material $material, Question $question)
    {
        $question->delete();

        return redirect()->route('materials.questions.index', $material)
            ->with('success', 'Question deleted successfully.');
    }
}