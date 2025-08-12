<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicationsController;
use App\Http\Controllers\CitationsController;
use App\Http\Controllers\AdminUserController;

Route::get('/', function () {
    return view('welcome');
});

// Debug route for troubleshooting
Route::get('/debug', function () {
    return view('debug');
})->name('debug');

// Simple test route
Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Laravel is working!',
        'timestamp' => now(),
        'environment' => app()->environment(),
        'app_name' => config('app.name'),
        'build_exists' => file_exists(public_path('build')),
        'build_files' => file_exists(public_path('build')) ? scandir(public_path('build')) : []
    ]);
})->name('test');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/dashboard/data', [\App\Http\Controllers\DashboardController::class, 'getData'])->name('admin.dashboard.data');
    Route::get('/admin/dashboard/stream', [\App\Http\Controllers\DashboardController::class, 'streamUpdates'])->name('admin.dashboard.stream');
    Route::get('/publications/request', [\App\Http\Controllers\PublicationsController::class, 'create'])->name('publications.request');
    Route::post('/publications/submit', [\App\Http\Controllers\PublicationsController::class, 'submitPublicationRequest'])->name('publications.submit');
    Route::get('/citations/request', [\App\Http\Controllers\CitationsController::class, 'create'])->name('citations.request');
    Route::post('/citations/submit', [\App\Http\Controllers\CitationsController::class, 'submitCitationRequest'])->name('citations.submit');
    Route::post('/citations/generate', [\App\Http\Controllers\CitationsController::class, 'generateCitationDocx'])->name('citations.generate');
    Route::get('/citations/success', [\App\Http\Controllers\CitationsController::class, 'success'])->name('citations.success');
    Route::patch('/admin/requests/{request}', [\App\Http\Controllers\PublicationsController::class, 'adminUpdate'])->name('admin.requests.update');
    Route::patch('/admin/requests/{request}/status', [\App\Http\Controllers\PublicationsController::class, 'adminUpdate'])->name('admin.requests.status');
    // Add a single endpoint for all publication DOCX generations
    Route::post('/publications/generate-docx', [\App\Http\Controllers\PublicationsController::class, 'generateDocx'])->name('publications.generateDocx');
    // Optionally, comment out the old incentive-application/generate route
    // Route::post('/publications/incentive-application/generate', ...);
    Route::delete('/admin/requests/{id}', [\App\Http\Controllers\PublicationsController::class, 'destroy'])->name('admin.requests.destroy');
    Route::get('/admin/requests/{request}/download', [\App\Http\Controllers\PublicationsController::class, 'adminDownloadFile'])->name('admin.requests.download');
    Route::get('/admin/requests/{request}/download-zip', [\App\Http\Controllers\PublicationsController::class, 'adminDownloadZip'])->name('admin.requests.download-zip');
    Route::get('/admin/requests/{request}/debug', [\App\Http\Controllers\PublicationsController::class, 'debugFilePaths'])->name('admin.requests.debug');
    Route::get('/admin/requests/{request}/serve', [\App\Http\Controllers\PublicationsController::class, 'serveFile'])->name('admin.requests.serve');
    // Nudge a pending request (user action)
    Route::post('/requests/{request}/nudge', [\App\Http\Controllers\DashboardController::class, 'nudge'])->name('requests.nudge');
    // Admin notifications endpoints
    Route::get('/admin/notifications', [\App\Http\Controllers\AdminUserController::class, 'listNotifications'])->name('admin.notifications.list');
    Route::post('/admin/notifications/read', [\App\Http\Controllers\AdminUserController::class, 'markNotificationsRead'])->name('admin.notifications.read');
});

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/requests/manage', [\App\Http\Controllers\AdminRequestController::class, 'index'])->name('admin.requests.manage');
    Route::get('/requests/{request}/data', [\App\Http\Controllers\AdminRequestController::class, 'getRequestData'])->name('admin.requests.data');
    Route::get('/requests/{request}/download-zip', [\App\Http\Controllers\AdminRequestController::class, 'downloadZip'])->name('admin.requests.download-zip');
});