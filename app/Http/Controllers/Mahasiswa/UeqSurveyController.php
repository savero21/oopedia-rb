<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\UeqSurvey;
use Illuminate\Http\Request;
use App\Models\Material;

class UeqSurveyController extends Controller
{
    public function create()
    {
        // Check if user has already submitted a survey
        $existingSurvey = UeqSurvey::where('user_id', auth()->id())->first();
        if ($existingSurvey) {
            return redirect()->route('mahasiswa.ueq.thankyou');
        }

        return view('mahasiswa.ueq.create');
    }

    public function store(Request $request)
    {
        // Check if user has already submitted a survey
        $existingSurvey = UeqSurvey::where('user_id', auth()->id())->first();
        if ($existingSurvey) {
            return redirect()->route('mahasiswa.ueq.thankyou');
        }

        // Validate survey data
        $validatedData = $request->validate([
            'annoying_enjoyable' => 'required|integer|between:1,7',
            'not_understandable_understandable' => 'required|integer|between:1,7',
            'creative_dull' => 'required|integer|between:1,7',
            'easy_difficult' => 'required|integer|between:1,7',
            'valuable_inferior' => 'required|integer|between:1,7',
            'boring_exciting' => 'required|integer|between:1,7',
            'not_interesting_interesting' => 'required|integer|between:1,7',
            'unpredictable_predictable' => 'required|integer|between:1,7',
            'fast_slow' => 'required|integer|between:1,7',
            'inventive_conventional' => 'required|integer|between:1,7',
            'obstructive_supportive' => 'required|integer|between:1,7',
            'good_bad' => 'required|integer|between:1,7',
            'complicated_easy' => 'required|integer|between:1,7',
            'unlikable_pleasing' => 'required|integer|between:1,7',
            'usual_leading_edge' => 'required|integer|between:1,7',
            'unpleasant_pleasant' => 'required|integer|between:1,7',
            'secure_not_secure' => 'required|integer|between:1,7',
            'motivating_demotivating' => 'required|integer|between:1,7',
            'meets_expectations_does_not_meet' => 'required|integer|between:1,7',
            'inefficient_efficient' => 'required|integer|between:1,7',
            'clear_confusing' => 'required|integer|between:1,7',
            'impractical_practical' => 'required|integer|between:1,7',
            'organized_cluttered' => 'required|integer|between:1,7',
            'attractive_unattractive' => 'required|integer|between:1,7',
            'friendly_unfriendly' => 'required|integer|between:1,7',
            'conservative_innovative' => 'required|integer|between:1,7',
        ]);

        $validatedData['user_id'] = auth()->id();
        
        try {
            UeqSurvey::create($validatedData);
            
            // Redirect to thank you page
            return redirect()->route('mahasiswa.ueq.thankyou');
        } catch (\Exception $e) {
            \Log::error('Error creating survey', ['error' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan saat menyimpan survey: ' . $e->getMessage());
        }
    }
    
    public function thankyou()
    {
        return view('mahasiswa.ueq.thankyou');
    }
} 