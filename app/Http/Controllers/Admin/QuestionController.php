<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        $difficulty = $request->input('difficulty');

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
            ->when($difficulty, function ($query) use ($difficulty) {
                return $query->where('difficulty', $difficulty);
            })
            ->when($material, function ($query) use ($material) {
                return $query->where('material_id', $material->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Format question types for display
        $questions->transform(function ($question) {
            $question->formatted_type = match($question->question_type) {
                'fill_in_the_blank' => 'Fill in the Blank',
                'radio_button' => 'Radio Button',
                'drag_and_drop' => 'Drag and Drop',
                default => $question->question_type
            };
            return $question;
        });

        return view('admin.questions.index', [
            'questions' => $questions,
            'userName' => $user->name,
            'userRole' => $user->role->role_name,
            'material' => $material,
            'search' => $search,
            'difficulty' => $difficulty
        ]);
    }


    public function create(Material $material = null)
    {
        if ($material) {
            // If material is provided, only show that material
            $materials = collect([$material]);
            return view('admin.questions.create', compact('materials', 'material'));
        } else {
            // Otherwise show all materials (for the general create route)
            $materials = Material::all();
            return view('admin.questions.create', compact('materials'));
        }
    }

    public function store(Request $request, Material $material = null)
{
    // Validasi dasar untuk semua field kecuali answers
    $baseValidation = [
        'question_text' => 'required|string',
        'question_type' => 'required|in:radio_button,drag_and_drop,fill_in_the_blank',
        'difficulty' => 'required|in:beginner,medium,hard',
        'material_id' => 'required|exists:materials,id',
    ];
    
    // Validasi khusus untuk answers berdasarkan tipe soal
    if ($request->question_type === 'fill_in_the_blank') {
        $answersValidation = ['answers' => 'required|array|min:1'];
    } else {
        $answersValidation = ['answers' => 'required|array|min:2'];
    }
    
    // Gabungkan validasi
    $validationRules = array_merge($baseValidation, $answersValidation);
    
    // Tambahkan validasi untuk setiap jawaban
    $validationRules['answers.*.answer_text'] = 'required|string';
    
    $request->validate($validationRules);

    // Proses correct_answer untuk radio_button dan fill_in_the_blank
    $answers = $request->answers;
    
    if (in_array($request->question_type, ['radio_button', 'fill_in_the_blank'])) {
        if ($request->has('correct_answer')) {
            $correctIndex = $request->correct_answer;
            
            // Create a new array instead of trying to modify by reference
            $processedAnswers = [];
            foreach ($answers as $index => $answer) {
                $answer['is_correct'] = ($index == $correctIndex) ? 1 : 0;
                $processedAnswers[] = $answer;
            }
            $answers = $processedAnswers;
            
            // Pastikan hanya ada 1 jawaban benar
            $correctAnswersCount = collect($answers)->sum(function ($answer) {
                return $answer['is_correct'];
            });
            
            if ($correctAnswersCount !== 1) {
                return redirect()->back()->withInput()->with('error', ucfirst(str_replace('_', ' ', $request->question_type)) . ' questions must have exactly one correct answer.');
            }
        } else {
            return redirect()->back()->withInput()->with('error', 'Please select the correct answer.');
        }
    }

    $question = Question::create([
        'question_text' => $request->question_text,
        'question_type' => $request->question_type,
        'difficulty' => $request->difficulty,
        'material_id' => $request->material_id,
        'created_by' => auth()->id(),
    ]);

    foreach ($answers as $answer) {
        Answer::create([
            'question_id' => $question->id,
            'answer_text' => $answer['answer_text'],
            'is_correct' => $answer['is_correct'] ?? 0,
            'explanation' => $answer['explanation'] ?? null,
        ]);
    }

    if ($material) {
        return redirect()
            ->route('admin.materials.questions.index', $material)
            ->with('success', 'Soal berhasil ditambahkan.');
    }

    return redirect()
        ->route('admin.questions.index')
        ->with('success', 'Soal berhasil ditambahkan.');
}

    public function edit(Material $material = null, Question $question)
    {
        $materials = Material::all();

        $material = $question->material; // Get the question's material
        return view('admin.questions.edit', compact('question', 'materials', 'material'));
    }

    public function update(Request $request, Material $material = null, Question $question)
    {
        // Validasi dasar untuk semua field kecuali answers
        $baseValidation = [
            'question_text' => 'required|string',
            'question_type' => 'required|in:radio_button,drag_and_drop,fill_in_the_blank',
            'difficulty' => 'required|in:beginner,medium,hard',
            'material_id' => 'required|exists:materials,id',
        ];
        
        // Validasi khusus untuk answers berdasarkan tipe soal
        if ($request->question_type === 'fill_in_the_blank') {
            $answersValidation = ['answers' => 'required|array|min:1'];
        } else {
            $answersValidation = ['answers' => 'required|array|min:2'];
        }
        
        // Gabungkan validasi
        $validationRules = array_merge($baseValidation, $answersValidation);
        
        // Tambahkan validasi untuk setiap jawaban
        $validationRules['answers.*.answer_text'] = 'required|string';
        $validationRules['answers.*.is_correct'] = 'required|boolean';
        $validationRules['answers.*.explanation'] = 'nullable|string|max:500';
        
        $request->validate($validationRules);

        $questionType = $request->question_type;

        if (in_array($questionType, ['radio_button', 'fill_in_the_blank'])) {
            $correctAnswersCount = collect($request->answers)->where('is_correct', '1')->count();
            if ($correctAnswersCount !== 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => ucfirst(str_replace('_', ' ', $questionType)) . ' Pertanyaan hanya boleh memliki 1 jawaban.'
                ], 422);
            }
        }

        $question->update([
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
            'difficulty' => $request->difficulty,
            'material_id' => $request->material_id,
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

        $material = $question->material;
        
        if ($material) {
            return redirect()
                ->route('admin.materials.questions.index', $material)
                ->with('success', 'Question updated successfully.');
        }

        return redirect()
            ->route('admin.questions.index')
            ->with('success', 'Question updated successfully.');
    }

    public function destroy(Material $material = null, Question $question)
    {
        $material_id = $question->material_id;
        $question->answers()->delete();
        $question->delete();

        if ($material) {
            return redirect()
                ->route('admin.materials.questions.index', $material)
                ->with('success', 'Soal berhasil dihapus.');
        }

        return redirect()
            ->route('admin.questions.index')
            ->with('success', 'Soal berhasil dihapus.');
    }
}
    