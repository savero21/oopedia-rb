<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            // Generate unique filename
            $fileName = Str::uuid() . '_' . $file->getClientOriginalName();
            
            // Store file in public/storage/uploads/tinymce
            $path = $file->storeAs('public/uploads/tinymce', $fileName);
            
            // Return location for TinyMCE
            return response()->json([
                'location' => asset('storage/uploads/tinymce/' . $fileName)
            ]);
        }
        
        return response()->json(['error' => 'No file uploaded.'], 400);
    }
} 