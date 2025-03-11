<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RegisterController;
use App\Http\Controllers\Admin\SessionsController;
use App\Http\Controllers\Admin\UserProfileController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\StudentMaterialController;
use App\Http\Controllers\Admin\StudentExerciseController;
use App\Http\Controllers\Admin\StudentProgressController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\AdminStudentController;
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

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);
Route::get('/', [SessionsController::class, 'create'])->name('login');
Route::post('/', [SessionsController::class, 'store']);
Route::get('/verify', [SessionsController::class, 'verify'])->name('verify');
Route::get('/user-profile', [UserProfileController::class, 'show'])->name('user-profile');
Route::get('/user-management', [UserManagementController::class, 'index'])->name('user-management');
Route::get('/profile', [ProfileController::class, 'create'])->middleware('auth')->name('profile');
Route::resource('materials', MaterialController::class);
Route::get('/materials/{material}/questions', [QuestionController::class, 'index'])->name('materials.questions.index');
Route::get('/materials/{material}/questions/create', [QuestionController::class, 'create'])->name('materials.questions.create');
Route::post('/materials/{material}/questions', [QuestionController::class, 'store'])->name('materials.questions.store');
Route::resource('questions', QuestionController::class)->except(['create', 'store']);

// Login routes
Route::get('login', [SessionsController::class, 'create'])->name('login');
Route::post('login', [SessionsController::class, 'store']);

// Logout route
Route::post('logout', [SessionsController::class, 'destroy'])->name('logout');

Route::get('/students', [AdminStudentController::class, 'index'])->name('admin.students.index');
Route::get('/students/{student}/progress', [AdminStudentController::class, 'progress'])->name('admin.students.progress');

