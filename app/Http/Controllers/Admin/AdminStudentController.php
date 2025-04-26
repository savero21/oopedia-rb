<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Material;
use App\Models\Progress;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class AdminStudentController extends Controller
{
    public function index(Request $request)
    {
        // Admin dan superadmin dapat mengakses daftar mahasiswa
        if (auth()->user()->role_id > 2) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk melihat daftar mahasiswa');
        }
        
        $query = User::where('role_id', 3); // Hanya tampilkan mahasiswa
        
        // Filter berdasarkan pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $students = $query->paginate(10);
        
        return view('admin.students.index', compact('students'));
    }

    public function progress(User $student)
    {
        if ($student->role_id !== 3) {
            abort(404);
        }

        // Get progress statistics
        $progressStats = DB::table('progress')
            ->select(
                'material_id',
                DB::raw('COUNT(DISTINCT question_id) as answered_questions'),
                DB::raw('SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
            )
            ->where('user_id', $student->id)
            ->groupBy('material_id')
            ->get();

        $materials = Material::withCount('questions')
            ->get()
            ->map(function($material) use ($progressStats) {
                $progress = $progressStats->firstWhere('material_id', $material->id);
                
                $correctAnswers = $progress ? $progress->correct_answers : 0;
                $progressPercentage = $material->questions_count > 0 
                    ? min(100, round(($correctAnswers / $material->questions_count) * 100))
                    : 0;

                $material->progress_percentage = $progressPercentage;
                $material->correct_answers = $correctAnswers;
                
                return $material;
            });

        return view('admin.students.progress', compact('student', 'materials'));
    }

    private function calculateOverallProgress($student)
    {
        $totalQuestions = Question::count();
        if ($totalQuestions === 0) return 0;

        $correctAnswers = Progress::where('user_id', $student->id)
            ->where('is_correct', true)
            ->count();

        return round(($correctAnswers / $totalQuestions) * 100);
    }

    public function destroy(User $student)
    {
        // Admin dan superadmin dapat menghapus mahasiswa
        if (auth()->user()->role_id > 2) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus mahasiswa');
        }
        
        // Pastikan yang dihapus adalah mahasiswa
        if ($student->role_id != 3) {
            return redirect()->route('admin.students.index')
                ->with('error', 'User ini bukan mahasiswa');
        }
        
        try {
            // Gunakan transaksi database untuk memastikan perubahan tersimpan
            DB::beginTransaction();
            
            // Hapus data terkait jika ada (misalnya progress, submissions, dll)
            // Contoh: DB::table('progress')->where('user_id', $student->id)->delete();
            
            // Hapus user
            $student->delete();
            
            // Commit transaksi
            DB::commit();
            
            return redirect()->route('admin.students.index')
                ->with('success', 'Mahasiswa berhasil dihapus');
        } catch (\Exception $e) {
            // Rollback jika terjadi error
            DB::rollBack();
            
            return redirect()->route('admin.students.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function showImportForm()
    {
        // Admin dan superadmin dapat mengakses fitur ini
        if (auth()->user()->role_id > 2) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk fitur ini');
        }
        
        return view('admin.students.import');
    }
    
    public function processImport(Request $request)
    {
        // Admin dan superadmin dapat mengakses fitur ini
        if (auth()->user()->role_id > 2) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk fitur ini');
        }
        
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv,txt|max:2048', // 2MB max size
        ]);
        
        $file = $request->file('excel_file');
        $path = $file->getRealPath();
        
        // Read the Excel/CSV file
        $data = [];
        if (($handle = fopen($path, 'r')) !== false) {
            // Read the header row
            $header = fgetcsv($handle, 1000, ',');
            
            // Check if the file has the required columns
            $requiredColumns = ['name', 'email', 'password'];
            $missingColumns = array_diff($requiredColumns, $header);
            
            if (!empty($missingColumns)) {
                return redirect()->back()->with('error', 'File tidak memiliki kolom yang diperlukan: ' . implode(', ', $missingColumns));
            }
            
            // Map column indexes
            $nameIndex = array_search('name', $header);
            $emailIndex = array_search('email', $header);
            $passwordIndex = array_search('password', $header);
            
            // Process each row
            $rowNumber = 1; // Start from 1 to account for header row
            $successCount = 0;
            $errorRows = [];
            
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $rowNumber++;
                
                // Skip empty rows
                if (empty($row[$nameIndex]) && empty($row[$emailIndex])) {
                    continue;
                }
                
                // Validate the row data
                $rowData = [
                    'name' => $row[$nameIndex] ?? '',
                    'email' => $row[$emailIndex] ?? '',
                    'password' => $row[$passwordIndex] ?? '',
                ];
                
                $validator = Validator::make($rowData, [
                    'name' => 'required|string|max:255',
                    'email' => [
                        'required',
                        'string',
                        'email',
                        'max:255',
                        Rule::unique('users'),
                    ],
                    'password' => 'required|string|min:8',
                ]);
                
                if ($validator->fails()) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'errors' => $validator->errors()->all(),
                    ];
                    continue;
                }
                
                // Create the user with is_approved set to true
                try {
                    $user = new User();
                    $user->name = $row[$nameIndex];
                    $user->email = $row[$emailIndex];
                    $user->password = Hash::make($row[$passwordIndex]);
                    $user->role_id = 3; // Mahasiswa role
                    $user->is_approved = true; // Explicitly set to true
                    $user->save();
                    
                    $successCount++;
                } catch (\Exception $e) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'errors' => [$e->getMessage()],
                    ];
                }
            }
            fclose($handle);
            
            // Prepare the result message
            $message = "Berhasil menambahkan {$successCount} mahasiswa.";
            if (!empty($errorRows)) {
                $message .= " Terdapat " . count($errorRows) . " baris dengan error.";
            }
            
            return redirect()->route('admin.students.index')
                ->with('success', $message)
                ->with('importErrors', $errorRows);
        }
        
        return redirect()->back()->with('error', 'Gagal membaca file. Pastikan format file benar.');
    }
    
    public function downloadTemplate()
    {
        // Admin dan superadmin dapat mengakses fitur ini
        if (auth()->user()->role_id > 2) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk fitur ini');
        }
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="mahasiswa_template.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $callback = function() {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['name', 'email', 'password']);
            fputcsv($file, ['Nama Mahasiswa', 'mahasiswa@example.com', 'password123']);
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function show(User $student)
    {
        if ($student->role_id !== 3) {
            abort(404);
        }

        // Get progress statistics
        $progressStats = DB::table('progress')
            ->select(
                'material_id',
                DB::raw('COUNT(DISTINCT question_id) as answered_questions'),
                DB::raw('SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
            )
            ->where('user_id', $student->id)
            ->groupBy('material_id')
            ->get();

        // Get all materials with their questions
        $materials = Material::withCount('questions')
            ->get()
            ->map(function($material) use ($progressStats) {
                $progress = $progressStats->firstWhere('material_id', $material->id);
                
                $correctAnswers = $progress ? $progress->correct_answers : 0;
                $progressPercentage = $material->questions_count > 0 
                    ? min(100, round(($correctAnswers / $material->questions_count) * 100))
                    : 0;

                $material->progress_percentage = $progressPercentage;
                $material->correct_answers = $correctAnswers;
                
                return $material;
            });

        return view('admin.students.show', compact('student', 'materials'));
    }
}