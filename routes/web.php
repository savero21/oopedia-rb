<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentMaterialController;
use App\Http\Controllers\StudentExerciseController;
use App\Http\Controllers\StudentProgressController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
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

