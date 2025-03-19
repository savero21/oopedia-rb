<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{
    RegisteredUserController,
    SessionsController
};
use App\Http\Controllers\Admin\{
    DashboardController as AdminDashboardController,
    MaterialController as AdminMaterialController,
    StudentController as AdminStudentController,
    QuestionController as AdminQuestionController
};
use App\Http\Controllers\Mahasiswa\{
    DashboardController as MahasiswaDashboardController,
    MaterialController as MahasiswaMaterialController,
    ProfileController as MahasiswaProfileController,
    LogoutController,
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
    Route::get('login', [SessionsController::class, 'create'])->name('login');
    Route::post('login', [SessionsController::class, 'store']);
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('logout', [LogoutController::class, 'logout'])->name('logout');

    // Admin Routes
    Route::middleware('role:1')->prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('materials', AdminMaterialController::class);
        Route::resource('questions', AdminQuestionController::class);
        Route::resource('materials.questions', AdminQuestionController::class)->except(['show']);
        Route::get('students', [AdminStudentController::class, 'index'])->name('students.index');
        Route::get('students/{student}/progress', [AdminStudentController::class, 'progress'])
            ->name('students.progress');
    });

    // Mahasiswa Routes
    Route::middleware(['auth', 'role:2'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        // Dashboard Routes
        Route::get('/dashboard', [MahasiswaDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/in-progress', [MahasiswaDashboardController::class, 'inProgress'])->name('dashboard.in-progress');
        Route::get('/dashboard/complete', [MahasiswaDashboardController::class, 'complete'])->name('dashboard.complete');
        
        Route::get('/materials', [MahasiswaMaterialController::class, 'index'])->name('materials.index');
        Route::get('/materials/{material}', [MahasiswaMaterialController::class, 'show'])->name('materials.show');
        Route::get('/profile', [MahasiswaProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [MahasiswaProfileController::class, 'update'])->name('profile.update');
        
        // Add this new route
        Route::post('/questions/check-answer', [MahasiswaQuestionController::class, 'checkAnswer'])
            ->name('questions.check-answer');

        Route::post('materials/{material}/reset', [MahasiswaMaterialController::class, 'reset'])->name('materials.reset');
    });
});

// General Routes
Route::get('/home', function () {
    if (auth()->check()) {
        return redirect()->route(auth()->user()->role_id == 1 ? 'admin.dashboard' : 'mahasiswa.dashboard');
    }
    return redirect()->route('login');
})->name('home');

Route::get('/verify', [SessionsController::class, 'verify'])->name('verify');

Route::middleware(['web'])->group(function () {
   
});

// Tambahkan atau pastikan route ini sudah ada
Route::post('/mahasiswa/questions/check-answer', [App\Http\Controllers\Mahasiswa\MahasiswaQuestionController::class, 'checkAnswer'])->name('mahasiswa.questions.check-answer');

Route::get('/mahasiswa/questions/{question}', [MahasiswaQuestionController::class, 'show'])->name('mahasiswa.questions.show');


