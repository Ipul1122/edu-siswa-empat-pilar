<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\Siswa\DashboardController;
use App\Http\Controllers\API\Siswa\MaterialController;
use App\Http\Controllers\API\Siswa\QuizController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/siswa/login', [AuthController::class, 'siswaLogin']);
Route::post('/admin/login', [AuthController::class, 'adminLogin']);
Route::post('/register', [AuthController::class, 'register']);

// Authenticated routes
Route::middleware('api.auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Profile Management
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    // Siswa Role Routes
    Route::middleware('role:siswa')->prefix('siswa')->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index']);
        
        // Materials
        Route::get('/materials', [MaterialController::class, 'index']);
        Route::get('/materials/{material}', [MaterialController::class, 'show']);
        Route::post('/materials/{material}/complete', [MaterialController::class, 'complete']);
        
        // Quizzes
        Route::get('/quizzes', [QuizController::class, 'index']);
        Route::get('/quizzes/{quiz}', [QuizController::class, 'show']);
        Route::get('/quizzes/{quiz}/start', [QuizController::class, 'start']);
        Route::post('/quizzes/{quiz}/submit', [QuizController::class, 'submit']);
        
        // Attempts History
        Route::get('/attempts', [QuizController::class, 'attemptsHistory']);
        Route::get('/attempts/{attempt}', [QuizController::class, 'attemptResult']);
    });
});
