<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Answer;
use App\Models\Material;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Request $request, Material $material = null)
    {
        $user = auth()->user();
        $search = $request->input('search');

        $questions = Question::with(['createdBy', 'answers', 'material'])
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('question_text', 'like', "%{$search}%")
                        ->orWhere('question_type', 'like', "%{$search}%")
                        ->orWhereHas('createdBy', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('material', function ($materialQuery) use ($search) {
                            $materialQuery->where('title', 'like', "%{$search}%");
                        });
                });
            })
            ->when($material, function ($query) use ($material) {
                return $query->where('material_id', $material->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Format question types for display
        $questions->transform(function ($question) {
            $question->formatted_type = match($question->question_type) {
                'radio_button' => 'Radio Button',
                'drag_and_drop' => 'Drag and Drop',
                'fill_in_the_blank' => 'Fill in the Blank',
                default => $question->question_type
            };
            return $question;
        });

        return view('questions.index', [
            'questions' => $questions,
            'userName' => $user->name,
            'userRole' => $user->role->role_name,
            'material' => $material,
            'search' => $search // Pass the search term back to the view
        ]);
    }

    public function create(Material $material = null)
    {
        if ($material) {
            // If material is provided, only show that material
            $materials = collect([$material]);
            return view('questions.create', compact('materials', 'material'));
        } else {
            // Otherwise show all materials (for the general create route)
            $materials = Material::all();
            return view('questions.create', compact('materials'));
        }
    }

    public function store(Request $request, Material $material = null)
    {
        $request->validate([
            'material_id' => $material ? 'nullable' : 'required|exists:materials,id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:radio_button,drag_and_drop,fill_in_the_blank',
            'answers' => 'required|array|min:1',
            'answers.*.answer_text' => 'required|string',
            'answers.*.is_correct' => 'required|boolean'
        ]);

        // Ensure only one correct answer for radio button type
        if ($request->question_type === 'radio_button') {
            $correctAnswers = collect($request->answers)->where('is_correct', true)->count();
            if ($correctAnswers !== 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Radio button questions must have exactly one correct answer'
                ], 422);
            }
        }

        $question = Question::create([
            'material_id' => $material ? $material->id : $request->material_id,
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
            'created_by' => auth()->id()
        ]);

        foreach ($request->answers as $answer) {
            Answer::create([
                'question_id' => $question->id,
                'answer_text' => $answer['answer_text'],
                'is_correct' => $answer['is_correct'],
                'explanation' => $answer['explanation'] ?? null,
                'drag_source' => $answer['drag_source'] ?? null,
                'drag_target' => $answer['drag_target'] ?? null,
                'blank_position' => $answer['blank_position'] ?? null
            ]);
        }

        if ($material) {
            return redirect()->route('materials.questions.index', $material)
                ->with('success', 'Question created successfully.');
        }

        return redirect()->route('questions.index')
            ->with('success', 'Question created successfully.');
    }

    public function edit(Question $question)
    {
        $materials = Material::all();
        $material = $question->material; // Get the question's material
        return view('questions.edit', compact('question', 'materials', 'material'));
    }

    public function update(Request $request, Question $question)
    {
        $request->validate([
            'material_id' => 'required|exists:materials,id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:radio_button,drag_and_drop,fill_in_the_blank',
            'answers' => 'required|array|min:1',
            'answers.*.answer_text' => 'required|string',
            'answers.*.is_correct' => 'required|boolean'
        ]);

        // Ensure only one correct answer for radio button type
        if ($request->question_type === 'radio_button') {
            $correctAnswers = collect($request->answers)->where('is_correct', true)->count();
            if ($correctAnswers !== 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Radio button questions must have exactly one correct answer'
                ], 422);
            }
        }

        $question->update([
            'material_id' => $request->material_id,
            'question_text' => $request->question_text,
            'question_type' => $request->question_type
        ]);

        // Delete existing answers
        $question->answers()->delete();

        // Create new answers
        foreach ($request->answers as $answer) {
            Answer::create([
                'question_id' => $question->id,
                'answer_text' => $answer['answer_text'],
                'is_correct' => $answer['is_correct'],
                'explanation' => $answer['explanation'] ?? null,
                'drag_source' => $answer['drag_source'] ?? null,
                'drag_target' => $answer['drag_target'] ?? null,
                'blank_position' => $answer['blank_position'] ?? null
            ]);
        }

        // Redirect back to the material's questions page if it came from there
        if ($question->material_id) {
            return redirect()->route('materials.questions.index', $question->material_id)
                ->with('success', 'Question updated successfully.');
        }

        return redirect()->route('questions.index')
            ->with('success', 'Question updated successfully.');
    }

    public function destroy(Question $question)
    {
        $material_id = $question->material_id; // Store the material_id before deletion
        $question->answers()->delete();
        $question->delete();

        // Redirect back to the material's questions page if it came from there
        if ($material_id) {
            return redirect()->route('materials.questions.index', $material_id)
                ->with('success', 'Question deleted successfully.');
        }

        return redirect()->route('questions.index')
            ->with('success', 'Question deleted successfully.');
    }
}