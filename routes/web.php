<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\QuestionController;
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
Route::resource('materials.questions', QuestionController::class);