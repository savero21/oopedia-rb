<?php

namespace App\Http\Controllers\Admin;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get the search query from the request
        $search = $request->input('search');

        // Query materials and filter by title or creator's name
        $materials = Material::with('createdBy')
            ->when($search, function ($query) use ($search) {
                return $query->where('title', 'like', "%{$search}%")
                             ->orWhereHas('createdBy', function ($q) use ($search) {
                                 $q->where('name', 'like', "%{$search}%");
                             });
            })
            ->get();

        return view('materials.index', [
            'materials' => $materials,
            'userName' => $user->name,
            'userRole' => $user->role->role_name
        ]);
    }

    public function create()
    {
        return view('materials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'created_by' => 'required|exists:users,id',
        ]);

        Material::create($request->all());
        return redirect()->route('materials.index')->with('success', 'Material created successfully.');
    }

    public function edit(Material $material)
    {
        return view('materials.edit', compact('material'));
    }

    public function update(Request $request, Material $material)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $material->update($request->all());
        return redirect()->route('materials.index')->with('success', 'Material updated successfully.');
    }

    public function destroy(Material $material)
    {
        $material->delete();
        return redirect()->route('materials.index')->with('success', 'Material deleted successfully.');
    }
}