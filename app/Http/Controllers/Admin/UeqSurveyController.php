<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UeqSurvey;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class UeqSurveyController extends Controller
{
    public function __construct()
    {
        // Tambahkan middleware untuk memastikan hanya admin dan superadmin yang bisa mengakses
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role_id > 2) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Anda tidak memiliki akses untuk melihat hasil UEQ Survey');
            }
            return $next($request);
        });
    }

    public function index()
    {
        // Ambil semua data survey dengan relasi user
        $surveys = UeqSurvey::with('user')->get();
        
        // Hitung rata-rata untuk setiap dimensi UEQ
        $averages = $this->calculateAverages($surveys);
        
        // Untuk sidebar materials dropdown
        $materials = Material::all();
        
        return view('admin.ueq.index', [
            'surveys' => $surveys,
            'averages' => $averages,
            'materials' => $materials,
            'activePage' => 'ueq',
            'userName' => auth()->user()->name,
            'userRole' => auth()->user()->role->role_name
        ]);
    }
    
    private function calculateAverages($surveys)
    {
        if ($surveys->isEmpty()) {
            return [];
        }
        
        // Inisialisasi array untuk menyimpan total nilai
        $totals = [
            'attractiveness' => 0,
            'perspicuity' => 0,
            'efficiency' => 0,
            'dependability' => 0,
            'stimulation' => 0,
            'novelty' => 0
        ];
        
        foreach ($surveys as $survey) {
            // Attractiveness
            $totals['attractiveness'] += (
                $survey->annoying_enjoyable + 
                $survey->good_bad + 
                $survey->unlikable_pleasing + 
                $survey->unpleasant_pleasant + 
                $survey->attractive_unattractive + 
                $survey->friendly_unfriendly
            ) / 6;
            
            // Perspicuity
            $totals['perspicuity'] += (
                $survey->not_understandable_understandable + 
                $survey->easy_difficult + 
                $survey->complicated_easy + 
                $survey->clear_confusing
            ) / 4;
            
            // Efficiency
            $totals['efficiency'] += (
                $survey->fast_slow + 
                $survey->inefficient_efficient + 
                $survey->impractical_practical + 
                $survey->organized_cluttered
            ) / 4;
            
            // Dependability
            $totals['dependability'] += (
                $survey->unpredictable_predictable + 
                $survey->obstructive_supportive + 
                $survey->secure_not_secure + 
                $survey->meets_expectations_does_not_meet
            ) / 4;
            
            // Stimulation
            $totals['stimulation'] += (
                $survey->valuable_inferior + 
                $survey->boring_exciting + 
                $survey->not_interesting_interesting + 
                $survey->motivating_demotivating
            ) / 4;
            
            // Novelty
            $totals['novelty'] += (
                $survey->creative_dull + 
                $survey->inventive_conventional + 
                $survey->usual_leading_edge + 
                $survey->conservative_innovative
            ) / 4;
        }
        
        // Hitung rata-rata
        $count = $surveys->count();
        $averages = [];
        
        foreach ($totals as $key => $total) {
            $averages[$key] = $total / $count;
        }
        
        return $averages;
    }

    public function export()
    {
        $surveys = UeqSurvey::with('user')->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=ueq_survey_results.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($surveys) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'User ID',
                'User Name',
                'Timestamp',
                'Annoying/Enjoyable',
                'Not Understandable/Understandable',
                'Creative/Dull',
                'Easy/Difficult',
                'Valuable/Inferior',
                'Boring/Exciting',
                'Not Interesting/Interesting',
                'Unpredictable/Predictable',
                'Fast/Slow',
                'Inventive/Conventional',
                'Obstructive/Supportive',
                'Good/Bad',
                'Complicated/Easy',
                'Unlikable/Pleasing',
                'Usual/Leading Edge',
                'Unpleasant/Pleasant',
                'Secure/Not Secure',
                'Motivating/Demotivating',
                'Meets Expectations/Does Not Meet',
                'Inefficient/Efficient',
                'Clear/Confusing',
                'Impractical/Practical',
                'Organized/Cluttered',
                'Attractive/Unattractive',
                'Friendly/Unfriendly',
                'Conservative-Innovative',
                'Comments',
                'Suggestions'
            ]);

            foreach ($surveys as $survey) {
                fputcsv($file, [
                    $survey->user_id,
                    $survey->user->name,
                    $survey->created_at,
                    $survey->annoying_enjoyable,
                    $survey->not_understandable_understandable,
                    $survey->creative_dull,
                    $survey->easy_difficult,
                    $survey->valuable_inferior,
                    $survey->boring_exciting,
                    $survey->not_interesting_interesting,
                    $survey->unpredictable_predictable,
                    $survey->fast_slow,
                    $survey->inventive_conventional,
                    $survey->obstructive_supportive,
                    $survey->good_bad,
                    $survey->complicated_easy,
                    $survey->unlikable_pleasing,
                    $survey->usual_leading_edge,
                    $survey->unpleasant_pleasant,
                    $survey->secure_not_secure,
                    $survey->motivating_demotivating,
                    $survey->meets_expectations_does_not_meet,
                    $survey->inefficient_efficient,
                    $survey->clear_confusing,
                    $survey->impractical_practical,
                    $survey->organized_cluttered,
                    $survey->attractive_unattractive,
                    $survey->friendly_unfriendly,
                    $survey->conservative_innovative,
                    $survey->comments,
                    $survey->suggestions
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function detail($userId)
    {
        $survey = UeqSurvey::where('user_id', $userId)->firstOrFail();
        $user = $survey->user;

        return view('admin.ueq.detail', compact('survey', 'user'));
    }
} 