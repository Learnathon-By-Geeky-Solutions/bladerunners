<?php

use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ClientController;
use App\Http\Controllers\API\FreelancerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Routes

// Authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Freelancer section
Route::get('/freelancers', [FreelancerController::class, 'index']);
Route::get('/freelancers/{id}', [FreelancerController::class, 'show']);

// Client section
Route::get('/clients', [ClientController::class, 'index']);
Route::get('/clients/{id}', [ClientController::class, 'show']);


// Protected Routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin-only routes
    Route::middleware('role:Admin')->group(function () {
        Route::get('/admin-dashboard', function () {
            return response()->json(['message' => 'Welcome, Admin']);
        });

        Route::get('/admins', [AdminController::class, 'index']);
        Route::get('/admins/{id}', [AdminController::class, 'show']);

        // Create or update the admin record
        Route::put('/admins', [AdminController::class, 'storeOrUpdate']);

        // Delete an admin record
        Route::delete('/admins/{id}', [AdminController::class, 'destroy']);

        // Update stats for a specific admin
        Route::put('/admins/{id}/update-stats', [AdminController::class, 'updateStats']);

        // Freelancer and Client Control
        Route::put('/freelancers/{id}/update-stats', [FreelancerController::class, 'updateStats']);
        Route::put('/clients/{id}/update-stats', [ClientController::class, 'updateStats']);
    });

    // Freelancer-only routes
    Route::middleware('role:Freelancer')->group(function () {
        Route::get('/freelancer-dashboard', function () {
            return response()->json(['message' => 'Welcome, Freelancer']);
        });

        Route::put('/freelancers', [FreelancerController::class, 'store']);
        Route::delete('/freelancers/{id}', [FreelancerController::class, 'destroy']);
    });

    // Client-only routes
    Route::middleware('role:Client')->group(function () {
        Route::get('/client-dashboard', function () {
            return response()->json(['message' => 'Welcome, Client']);
        });

        Route::put('/clients', [ClientController::class, 'store']);
        Route::delete('/clients/{id}', [ClientController::class, 'destroy']);
    });
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
