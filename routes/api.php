<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public blog routes
Route::get('/blogs', [BlogController::class, 'index']);
Route::get('/blogs/categories', [BlogController::class, 'categories']);
Route::get('/blogs/popular', [BlogController::class, 'popular']);
Route::get('/blogs/recent', [BlogController::class, 'recent']);
Route::get('/blogs/{slug}', [BlogController::class, 'show']);

// Public exercise routes
Route::get('/exercises', [ExerciseController::class, 'index']);
Route::get('/exercises/categories', [ExerciseController::class, 'categories']);
Route::get('/exercises/popular', [ExerciseController::class, 'popular']);
Route::get('/exercises/recent', [ExerciseController::class, 'recent']);
Route::get('/exercises/difficulty/{difficulty}', [ExerciseController::class, 'byDifficulty']);
Route::get('/exercises/{slug}', [ExerciseController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Protected blog routes (for admin/authors)
    Route::post('/blogs', [BlogController::class, 'store']);
    Route::put('/blogs/{slug}', [BlogController::class, 'update']);
    Route::delete('/blogs/{slug}', [BlogController::class, 'destroy']);
    
    // Protected exercise routes (for admin/authors)
    Route::post('/exercises', [ExerciseController::class, 'store']);
    Route::put('/exercises/{slug}', [ExerciseController::class, 'update']);
    Route::delete('/exercises/{slug}', [ExerciseController::class, 'destroy']);
    Route::post('/exercises/{slug}/complete', [ExerciseController::class, 'complete']);
});

// Admin routes
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    // Blog management
    Route::get('/blogs', [AdminBlogController::class, 'index']);
    Route::get('/blogs/stats', [AdminBlogController::class, 'stats']);
    Route::get('/blogs/{id}', [AdminBlogController::class, 'show']);
    Route::post('/blogs', [AdminBlogController::class, 'store']);
    Route::put('/blogs/{id}', [AdminBlogController::class, 'update']);
    Route::delete('/blogs/{id}', [AdminBlogController::class, 'destroy']);
    Route::post('/blogs/bulk-delete', [AdminBlogController::class, 'bulkDelete']);
    Route::post('/blogs/bulk-update-status', [AdminBlogController::class, 'bulkUpdateStatus']);
});
