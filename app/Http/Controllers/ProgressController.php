<?php

namespace App\Http\Controllers;

use App\Models\Progress;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function index()
    {
        $progresses = Progress::with(['user', 'material', 'question'])->get();
        return view('progress.index', compact('progresses'));
    }

    public function create()
    {
        return view('progress.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'material_id' => 'required|exists:materials,id',
            'question_id' => 'required|exists:questions,id',
            'is_answered' => 'required|boolean',
            'is_correct' => 'required|boolean',
        ]);

        Progress::create($request->all());
        return redirect()->route('progress.index')->with('success', 'Progress created successfully.');
    }

    public function edit(Progress $progress)
    {
        return view('progress.edit', compact('progress'));
    }

    public function update(Request $request, Progress $progress)
    {
        $request->validate([
            'is_answered' => 'required|boolean',
            'is_correct' => 'required|boolean',
        ]);

        $progress->update($request->all());
        return redirect()->route('progress.index')->with('success', 'Progress updated successfully.');
    }

    public function destroy(Progress $progress)
    {
        $progress->delete();
        return redirect()->route('progress.index')->with('success', 'Progress deleted successfully.');
    }
}