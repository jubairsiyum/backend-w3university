<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ProfileController;
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

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // User Profile Routes
    Route::prefix('profile')->group(function () {
        // Get full profile
        Route::get('/', [ProfileController::class, 'show']);
        
        // Update profile information
        Route::put('/basic-info', [ProfileController::class, 'updateBasicInfo']);
        Route::put('/details', [ProfileController::class, 'updateProfile']);
        Route::post('/avatar', [ProfileController::class, 'uploadAvatar']);
        Route::post('/change-password', [ProfileController::class, 'changePassword']);
        
        // Favorites management
        Route::get('/favorites', [ProfileController::class, 'getFavorites']);
        Route::post('/favorites', [ProfileController::class, 'addFavorite']);
        Route::put('/favorites/{id}', [ProfileController::class, 'updateFavorite']);
        Route::delete('/favorites/{id}', [ProfileController::class, 'deleteFavorite']);
        
        // Activity tracking
        Route::post('/activity', [ProfileController::class, 'trackActivity']);
        Route::get('/activity/history', [ProfileController::class, 'getActivityHistory']);
        
        // Performance stats
        Route::get('/performance', [ProfileController::class, 'getPerformance']);
        Route::post('/badge', [ProfileController::class, 'awardBadge']);
    });
    
    // Public profile view
    Route::get('/profiles/{userId}', [ProfileController::class, 'getPublicProfile']);
    
    // Protected blog routes (for admin/authors)
    Route::post('/blogs', [BlogController::class, 'store']);
    Route::put('/blogs/{slug}', [BlogController::class, 'update']);
    Route::delete('/blogs/{slug}', [BlogController::class, 'destroy']);
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
