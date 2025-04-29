<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\AssignedProjectController;
use App\Http\Controllers\API\AssignedProjectRequestController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BiddingController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ClientController;
use App\Http\Controllers\API\FreelancerController;
use App\Http\Controllers\API\LocalJobController;
use App\Http\Controllers\API\PaymentHistoryController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\TalentController;
use App\Http\Controllers\API\WorkController;

use App\Constants\RoutePaths;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\WithdrawRequestController;

// Public Routes

// Registration
Route::post('/register', [AuthController::class, 'register']);
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'emailVerification'])->middleware('signed')->name('verification.verify');

// Login
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Forgot Password
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/password/change', [AuthController::class, 'passwordChange']);

// Reset Password
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// SSLCommerz IPN callback
Route::post('/add-balance/ssl-ipn', [PaymentController::class, 'sslIpn'])
->name('api.addBalance.ssl-ipn');

// SSLCommerz user-redirect callbacks
Route::match(['get','post'], '/add-balance/ssl-success', [PaymentController::class, 'sslSuccess'])
->name('api.addBalance.ssl-success');
Route::post('/add-balance/ssl-fail', [PaymentController::class, 'sslFail'])
->name('api.addBalance.ssl-fail');
Route::post('/add-balance/ssl-cancel', [PaymentController::class, 'sslCancel'])
->name('api.addBalance.ssl-cancel');

// Stripe user-redirect callbacks
Route::get('/add-balance/stripe-success', [PaymentController::class, 'stripeSuccess'])
->name('api.addBalance.stripe-success');
Route::get('/add-balance/stripe-cancel', [PaymentController::class, 'stripeCancel'])
->name('api.addBalance.stripe-cancel');

Route::middleware(['web'])->group(function () {
    // Initiate Google login.
    Route::get('login/google', [AuthController::class, 'redirectToGoogle']);

    // Google OAuth callback route.
    Route::get('login/google/callback', [AuthController::class, 'handleGoogleCallback']);

    // Role selection route (redirect target).
    Route::get('select-role', [AuthController::class, 'showRoleSelection'])->name('role.selection');

    // Complete registration route.
    Route::post('complete-registration', [AuthController::class, 'completeRegistration']);
});


// Freelancer section
Route::get('/freelancers', [FreelancerController::class, 'index']);
Route::get('/freelancers/{id}', [FreelancerController::class, 'show']);

// Client section
Route::get('/clients', [ClientController::class, 'index']);
Route::get('/clients/{id}', [ClientController::class, 'show']);

// Projects section
Route::get('/projects', [ProjectController::class, 'index']);
Route::get(RoutePaths::PROJECT_SHOW, [ProjectController::class, 'show']);

// Local Jobs section
Route::get('/local-jobs', [LocalJobController::class, 'index']);
Route::get(RoutePaths::LOCALJOBS_SHOW, [LocalJobController::class, 'show']);

// Categorirs & Skills
Route::get('/categories', [CategoryController::class, 'index']);
Route::get(RoutePaths::CATEGORIES_SHOW, [CategoryController::class, 'show']);
Route::get('/categories/{id}/skills', [CategoryController::class, 'getCategorySkills']);
Route::get('/categoriesWithMetrics', [CategoryController::class, 'categoriesWithMetrics']);

// Protected Routes
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Assigned Projects routes (visible to all authenticated users)
    Route::get('/assignments', [AssignedProjectController::class, 'index']);
    Route::get(RoutePaths::ASSIGNMENTS_SHOW, [AssignedProjectController::class, 'show']);
    Route::put(RoutePaths::ASSIGNMENTS_SHOW, [AssignedProjectController::class, 'update']);

    // Reviews routes
    Route::get('/reviews', [ReviewController::class, 'index']);
    Route::get(RoutePaths::REVIEW_SHOW, [ReviewController::class, 'show']);

    // Hire Talent
    // GET /talent?category_id=1&skills=Laravel,Vue.js
    Route::get('/talent', [TalentController::class, 'index']);

    // Find Work
    Route::get('/work', [WorkController::class, 'index']);

    // Withdraw Balance
    Route::get('/withdraw-requests', [WithdrawRequestController::class, 'index']);
    Route::get('/withdraw-requests/{id}', [WithdrawRequestController::class, 'show']);
    Route::post('/withdraw-requests', [WithdrawRequestController::class, 'store']);

    // Admin-only routes
    Route::middleware('role:Admin')->group(function () {
        // Withdraw Balance
        Route::post('/withdraw-requests/{id}/approve', [WithdrawRequestController::class, 'approve']);
        Route::post('/withdraw-requests/{id}/reject', [WithdrawRequestController::class, 'reject']);

        // Profile
        Route::get('/admin-dashboard', function () {
            return response()->json(['message' => 'Welcome, Admin']);
        });
        Route::get('/admins', [AdminController::class, 'index']);
        Route::get('/admins/{id}', [AdminController::class, 'show']);

        // Update and Delete admin record
        Route::put('/admins', [AdminController::class, 'storeOrUpdate']);
        Route::delete('/admins/{id}', [AdminController::class, 'destroy']);
        // Update stats for a specific admin
        Route::put('/admins/{id}/update-stats', [AdminController::class, 'updateStats']);

        // Categorirs & Skills
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put(RoutePaths::CATEGORIES_SHOW, [CategoryController::class, 'update']);
        Route::delete(RoutePaths::CATEGORIES_SHOW, [CategoryController::class, 'destroy']);

        // Freelancer and Client Control
        Route::put('/freelancers/{id}/update-stats', [FreelancerController::class, 'updateStats']);
        Route::put('/clients/{id}/update-stats', [ClientController::class, 'updateStats']);

        // Payment History endpoints
        Route::get('/payments', [PaymentHistoryController::class, 'index']);
        Route::get(RoutePaths::PAYMENT_SHOW, [PaymentHistoryController::class, 'show']);
        // Note: We no longer use PaymentHistoryController::store because payment records are auto-created on assignment completion.
        Route::put(RoutePaths::PAYMENT_SHOW, [PaymentHistoryController::class, 'update']);
        Route::delete(RoutePaths::PAYMENT_SHOW, [PaymentHistoryController::class, 'destroy']);
    });

    // Freelancer-only routes
    Route::middleware('role:Freelancer')->group(function () {
        // Profile
        Route::get('/freelancer-dashboard', function () {
            return response()->json(['message' => 'Welcome, Freelancer']);
        });
        Route::put('/freelancers', [FreelancerController::class, 'store']);
        Route::delete('/freelancers/{id}', [FreelancerController::class, 'destroy']);

        // Bidding
        Route::get('/freelancer/biddings', [BiddingController::class, 'myBiddings']);
        Route::post('/projects/{project_id}/biddings', [BiddingController::class, 'store']);
        
        // Fetch reviews by freelancer ID for profile views
        Route::get('/freelancer-reviews', [ReviewController::class, 'getByFreelancer']);

        // Endpoints to get payments for authenticated freelancers
        Route::get('/freelancer-payments', [PaymentHistoryController::class, 'getFreelancerPayments'])->middleware('role:Freelancer');

        // Project Request creation
        Route::get('project-requests', [AssignedProjectRequestController::class, 'index']);
        Route::patch('project-requests/{id}', [AssignedProjectRequestController::class, 'update']);
        Route::get('project-requests/{id}', [AssignedProjectRequestController::class, 'show']);
        Route::get('/freelancer/assignments', [AssignedProjectController::class, 'myAssignments']);
    });

    // Client-only routes
    Route::middleware('role:Client')->group(function () {
        // Profile
        Route::get('/client-dashboard', function () {
            return response()->json(['message' => 'Welcome, Client']);
        });
        Route::put('/clients', [ClientController::class, 'store']);
        Route::delete('/clients/{id}', [ClientController::class, 'destroy']);

        // Projects
        Route::post('/projects', [ProjectController::class, 'store']);
        Route::put(RoutePaths::PROJECT_SHOW, [ProjectController::class, 'update']);
        Route::delete(RoutePaths::PROJECT_SHOW, [ProjectController::class, 'destroy']);
        Route::get('/my-projects', [ProjectController::class, 'myProjects']);

        // Biddings
        Route::get('/projects/{project_id}/biddings', [BiddingController::class, 'index']);

        // Only clients can create, update, or delete assignments (project assignment is done by the client who created the project)
        Route::post('/assignments', [AssignedProjectController::class, 'store']);
        Route::delete(RoutePaths::ASSIGNMENTS_SHOW, [AssignedProjectController::class, 'destroy']);

        // Only clients can submit, update, or delete reviews after a project is completed
        Route::post('/reviews', [ReviewController::class, 'store']);
        Route::put(RoutePaths::REVIEW_SHOW, [ReviewController::class, 'update']);
        Route::delete(RoutePaths::REVIEW_SHOW, [ReviewController::class, 'destroy']);

        // Fetch reviews by client ID for profile views
        Route::get('/client-reviews', [ReviewController::class, 'getByClient']);
    
        // Endpoints to get payments for authenticated clients
        Route::get('/client-payments', [PaymentHistoryController::class, 'getClientPayments'])->middleware('role:Client');

        // Local Jobs section
        Route::post('/local-jobs', [LocalJobController::class, 'store']);
        Route::put(RoutePaths::LOCALJOBS_SHOW, [LocalJobController::class, 'update']);
        Route::delete(RoutePaths::LOCALJOBS_SHOW, [LocalJobController::class, 'destroy']);

        // Project Request creation
        Route::post('project-requests', [AssignedProjectRequestController::class, 'store']);

        
        // Add Balance
        Route::post('/add-balance', [PaymentController::class, 'process'])
            ->name('api.addBalance.process');

        
    });

});

