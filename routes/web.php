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
    PendingApprovalController
};
use App\Http\Controllers\Mahasiswa\{
    DashboardController as MahasiswaDashboardController,
    MaterialController as MahasiswaMaterialController,
    ProfileController as MahasiswaProfileController,
    LogoutController as MahasiswaLogoutController,
    QuestionController as MahasiswaQuestionController
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

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [SessionsController::class, 'create'])->name('login');
    Route::post('/login', [SessionsController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/guest-login', [SessionsController::class, 'guestLogin'])->name('guest.login');
});

// Logout route
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Pending Approval Route - accessible by any authenticated user
    Route::get('admin/pending-approval', [PendingApprovalController::class, 'index'])
        ->name('admin.pending-approval');

    // Admin Routes
    Route::middleware(['auth', 'role:1|2', 'admin.approved'])->name('admin.')->prefix('admin')->group(function () {
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('materials', AdminMaterialController::class);
        Route::resource('questions', AdminQuestionController::class);
        Route::resource('materials.questions', AdminQuestionController::class)->except(['show']);
        Route::get('students', [AdminStudentController::class, 'index'])->name('students.index');
        Route::get('students/{student}/progress', [AdminStudentController::class, 'progress'])
            ->name('students.progress');

        // Admin management routes (only for superadmin)
        Route::middleware(['superadmin'])->group(function () {
            Route::get('/pending-admins', [AdminUserController::class, 'pendingAdmins'])->name('pending-admins');
            Route::post('/users/{user}/approve', [AdminUserController::class, 'approveAdmin'])->name('users.approve');
            Route::post('/users/{user}/reject', [AdminUserController::class, 'rejectAdmin'])->name('users.reject');
            
            Route::resource('users', AdminUserController::class)->except(['show']);
        });
    });

    // Mahasiswa Routes
    Route::middleware(['auth', 'role:3'])->name('mahasiswa.')->prefix('mahasiswa')->group(function () {
        Route::get('dashboard', [MahasiswaDashboardController::class, 'index'])->name('dashboard');
        
        // Add these new routes for dashboard sections
        Route::get('dashboard/in-progress', [MahasiswaDashboardController::class, 'inProgress'])->name('dashboard.in-progress');
        Route::get('dashboard/completed', [MahasiswaDashboardController::class, 'completed'])->name('dashboard.completed');
        
        Route::get('materials', [MahasiswaMaterialController::class, 'index'])->name('materials.index');
        Route::get('materials/{material}', [MahasiswaMaterialController::class, 'show'])->name('materials.show');
        Route::post('materials/{material}/reset', [MahasiswaMaterialController::class, 'reset'])->name('materials.reset');
        Route::get('profile', [MahasiswaProfileController::class, 'show'])->name('profile');
        Route::put('profile', [MahasiswaProfileController::class, 'update'])->name('profile.update');
    });
});

// Tambahkan route untuk approve dan reject admin
Route::middleware(['auth', 'superadmin'])->group(function() {
    Route::post('/admin/users/{user}/approve', [AdminUserController::class, 'approveAdmin'])->name('admin.users.approve');
    Route::post('/admin/users/{user}/reject', [AdminUserController::class, 'rejectAdmin'])->name('admin.users.reject');
});

// General Routes
Route::get('/home', function () {
    if (auth()->check()) {
        if (auth()->user()->role_id == 2 && !auth()->user()->is_approved) {
            return redirect()->route('admin.pending-approval');
        }
        return redirect()->route(auth()->user()->role_id <= 2 ? 'admin.dashboard' : 'mahasiswa.dashboard');
    }
    return redirect()->route('login');
})->name('home');

Route::get('/verify', [SessionsController::class, 'verify'])->name('verify');

Route::middleware(['web'])->group(function () {
   
});

// Tambahkan atau pastikan route ini sudah ada
Route::post('/mahasiswa/questions/check-answer', [MahasiswaQuestionController::class, 'checkAnswer'])
    ->name('mahasiswa.questions.check-answer')
    ->middleware('auth');

Route::get('/mahasiswa/questions/{question}', [MahasiswaQuestionController::class, 'show'])
    ->name('mahasiswa.questions.show')
    ->middleware('auth');

// Guest Routes
Route::middleware(['guest.access'])->name('guest.')->prefix('guest')->group(function () {
    Route::get('/materials', [MahasiswaMaterialController::class, 'index'])->name('materials.index');
    Route::get('/materials/{material}', [MahasiswaMaterialController::class, 'guestShow'])->name('materials.show');
    Route::post('/materials/{material}/reset', [MahasiswaMaterialController::class, 'guestReset'])->name('materials.reset');
    Route::post('/questions/check-answer', [MahasiswaQuestionController::class, 'checkAnswer'])->name('questions.check-answer');
});

// Guest login route
Route::get('guest-login', [GuestLoginController::class, 'login'])->name('guest.login');

// Rute untuk tamu (role_id = 4)
Route::middleware(['auth'])->group(function () {
    Route::get('/mahasiswa/materials', [MahasiswaMaterialController::class, 'index'])
        ->name('mahasiswa.materials.index');
    
    Route::get('/mahasiswa/materials/{material}', [MahasiswaMaterialController::class, 'show'])
        ->name('mahasiswa.materials.show');
    
    Route::post('/mahasiswa/questions/check-answer', [MahasiswaQuestionController::class, 'checkAnswer'])
        ->name('mahasiswa.questions.check-answer');
    
    Route::post('/mahasiswa/materials/{material}/reset', [MahasiswaMaterialController::class, 'reset'])
        ->name('mahasiswa.materials.reset');
});



