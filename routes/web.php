<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{
    RegisteredUserController,
    SessionsController,
    GuestLoginController,
    LogoutController
};
use App\Http\Controllers\Admin\{
    DashboardController as AdminDashboardController,
    MaterialController as AdminMaterialController,
    StudentController as AdminStudentController,
    QuestionController as AdminQuestionController,
    AdminUserController,
    PendingApprovalController,
    LogoutController as AdminLogoutController
};
use App\Http\Controllers\Mahasiswa\{
    DashboardController as MahasiswaDashboardController,
    MaterialController as MahasiswaMaterialController,
    ProfileController as MahasiswaProfileController,
    QuestionController as MahasiswaQuestionController,
    MahasiswaController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect root to login or materials page based on authentication
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        
        if ($user->role_id <= 2) {
            return redirect()->route('admin.dashboard');
        } else if ($user->role_id == 3) {
            return redirect()->route('mahasiswa.dashboard');
        } else {
            return redirect()->route('mahasiswa.materials.index');
        }
    }
    
    return redirect()->route('login');
})->name('home');

// Guest Routes (Unauthenticated)
Route::middleware('guest')->group(function () {
    // Authentication
    Route::get('/login', [SessionsController::class, 'create'])->name('login');
    Route::post('/login', [SessionsController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    
    // Guest login
    Route::get('/guest-login', [GuestLoginController::class, 'login'])->name('guest.login');
});

// Logout routes (for all authenticated users)
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
Route::post('/mahasiswa/logout', [LogoutController::class, 'logout'])->name('mahasiswa.logout');

// Verification route
Route::get('/verify', [SessionsController::class, 'verify'])->name('verify');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Pending Approval Route - accessible by any authenticated user
    Route::get('admin/pending-approval', [PendingApprovalController::class, 'index'])
        ->name('admin.pending-approval');
        
    // Admin Routes (role 1 = superadmin, role 2 = admin)
    Route::middleware(['role:1|2', 'admin.approved'])->name('admin.')->prefix('admin')->group(function () {
        // Dashboard
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Materials & Questions
        Route::resource('materials', AdminMaterialController::class);
        Route::resource('questions', AdminQuestionController::class);
        Route::resource('materials.questions', AdminQuestionController::class)->except(['show']);
        
        // Students
        Route::get('students', [AdminStudentController::class, 'index'])->name('students.index');
        Route::get('students/{student}/progress', [AdminStudentController::class, 'progress'])
            ->name('students.progress');

        // Admin management routes (only for superadmin - role 1)
        Route::middleware(['superadmin'])->group(function () {
            // Admin approval
            Route::get('/pending-admins', [AdminUserController::class, 'pendingAdmins'])->name('pending-admins');
            Route::post('/users/{user}/approve', [AdminUserController::class, 'approveAdmin'])->name('users.approve');
            Route::post('/users/{user}/reject', [AdminUserController::class, 'rejectAdmin'])->name('users.reject');
            
            // User import
            Route::get('/users/import', [AdminUserController::class, 'showImportForm'])->name('users.import');
            Route::post('/users/import', [AdminUserController::class, 'processImport'])->name('users.process-import');
            Route::get('/users/download-template', [AdminUserController::class, 'downloadTemplate'])->name('users.download-template');
            
            // User management
            Route::resource('users', AdminUserController::class)->except(['show']);
        });
    });

    // Mahasiswa & Guest Routes (role 3 = mahasiswa, role 4 = guest)
    Route::middleware(['role:3|4'])->name('mahasiswa.')->prefix('mahasiswa')->group(function () {
        // Dashboard (only for mahasiswa)
        Route::middleware(['role:3'])->group(function () {
            Route::get('dashboard', [MahasiswaDashboardController::class, 'index'])->name('dashboard');
            Route::get('dashboard/in-progress', [MahasiswaDashboardController::class, 'inProgress'])->name('dashboard.in-progress');
            Route::get('dashboard/completed', [MahasiswaDashboardController::class, 'complete'])->name('dashboard.completed');
            
            // Profile
            Route::get('profile', [MahasiswaProfileController::class, 'show'])->name('profile');
            Route::put('profile', [MahasiswaProfileController::class, 'update'])->name('profile.update');
        });
        
        // Materials (for both mahasiswa and guest)
        Route::get('materials', [MahasiswaMaterialController::class, 'index'])->name('materials.index');
        Route::get('materials/{material}', [MahasiswaMaterialController::class, 'show'])->name('materials.show');
        
        // Reset (conditional based on role)
        Route::post('materials/{material}/reset', function($material) {
            if (auth()->user()->role_id == 3) {
                $controller = app()->make(App\Http\Controllers\Mahasiswa\MaterialController::class);
                return $controller->reset($material);
            } else {
                $controller = app()->make(App\Http\Controllers\Mahasiswa\MaterialController::class);
                return $controller->guestReset($material);
            }
        })->name('materials.reset');
        
        // Questions
        Route::post('questions/check-answer', [MahasiswaQuestionController::class, 'checkAnswer'])->name('questions.check-answer');
        Route::get('questions/{question}', [MahasiswaQuestionController::class, 'show'])->name('questions.show');

        // Mahasiswa-specific routes
        Route::get('/leaderboard', [MahasiswaController::class, 'leaderboard'])->name('leaderboard');
    });

    // Admin logout route - keep this one
    Route::post('/admin/logout', [AdminLogoutController::class, 'logout'])
        ->name('admin.logout')
        ->middleware('auth'); // Only require authentication
});

// Fallback route for 404 errors
Route::fallback(function () {
    if (auth()->check()) {
        $user = auth()->user();
        
        if ($user->role_id <= 2) {
            return redirect()->route('admin.dashboard');
        } else if ($user->role_id == 3) {
            return redirect()->route('mahasiswa.dashboard');
        } else {
            // Guest users (role_id = 4)
            return redirect()->route('mahasiswa.materials.index')
                ->with('info', 'Halaman yang Anda cari tidak tersedia untuk akun tamu.');
        }
    }
    
    return redirect()->route('login');
});

// Add this with your other guest routes
Route::post('/guest-logout', [GuestLoginController::class, 'logout'])->name('guest.logout');



