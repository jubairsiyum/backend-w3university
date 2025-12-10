<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\ExerciseController as AdminExerciseController;

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
Route::middleware('auth:api')->group(function () {
    Route::get('/user', [ProfileController::class, 'show']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile/basic-info', [ProfileController::class, 'updateBasicInfo']);
    Route::put('/profile/details', [ProfileController::class, 'updateDetails']);
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword']);
    
    // Favorites routes
    Route::get('/profile/favorites', [ProfileController::class, 'getFavorites']);
    Route::post('/profile/favorites', [ProfileController::class, 'addFavorite']);
    Route::put('/profile/favorites/{id}', [ProfileController::class, 'updateFavorite']);
    Route::delete('/profile/favorites/{id}', [ProfileController::class, 'deleteFavorite']);
    
    // Activity routes
    Route::post('/profile/activity', [ProfileController::class, 'trackActivity']);
    Route::get('/profile/activity/history', [ProfileController::class, 'getActivityHistory']);
    
    // Performance routes
    Route::get('/profile/performance', [ProfileController::class, 'getPerformance']);
    Route::post('/profile/badge', [ProfileController::class, 'awardBadge']);
    
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
Route::prefix('admin')->middleware(['auth:api', 'admin'])->group(function () {
    // Blog management
    Route::get('/blogs', [AdminBlogController::class, 'index']);
    Route::get('/blogs/stats', [AdminBlogController::class, 'stats']);
    Route::get('/blogs/{id}', [AdminBlogController::class, 'show']);
    Route::post('/blogs', [AdminBlogController::class, 'store']);
    Route::put('/blogs/{id}', [AdminBlogController::class, 'update']);
    Route::delete('/blogs/{id}', [AdminBlogController::class, 'destroy']);
    Route::post('/blogs/bulk-delete', [AdminBlogController::class, 'bulkDelete']);
    Route::post('/blogs/bulk-update-status', [AdminBlogController::class, 'bulkUpdateStatus']);
    
    // Exercise management
    Route::get('/exercises', [AdminExerciseController::class, 'index']);
    Route::get('/exercises/stats', [AdminExerciseController::class, 'stats']);
    Route::get('/exercises/{id}', [AdminExerciseController::class, 'show']);
    Route::post('/exercises', [AdminExerciseController::class, 'store']);
    Route::put('/exercises/{id}', [AdminExerciseController::class, 'update']);
    Route::delete('/exercises/{id}', [AdminExerciseController::class, 'destroy']);
    Route::post('/exercises/bulk-delete', [AdminExerciseController::class, 'bulkDelete']);
    Route::post('/exercises/bulk-update-status', [AdminExerciseController::class, 'bulkUpdateStatus']);
});