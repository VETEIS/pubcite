<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicationsController;

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
    Route::get('/publications/request', [\App\Http\Controllers\PublicationsController::class, 'create'])->name('publications.request');
    Route::post('/publications/submit', [\App\Http\Controllers\PublicationsController::class, 'submitPublicationRequest'])->name('publications.submit');
    Route::patch('/admin/requests/{request}', [\App\Http\Controllers\PublicationsController::class, 'adminUpdate'])->name('admin.requests.update');
    Route::post('/publications/incentive-application/generate', [\App\Http\Controllers\PublicationsController::class, 'generateIncentiveDocx'])->name('publications.incentive.generate');
    Route::delete('/admin/requests/{id}', [\App\Http\Controllers\PublicationsController::class, 'destroy'])->name('admin.requests.destroy');
    Route::get('/admin/requests/{request}/download', [\App\Http\Controllers\PublicationsController::class, 'adminDownloadFile'])->name('admin.requests.download');
    Route::get('/admin/requests/{request}/download-zip', [\App\Http\Controllers\PublicationsController::class, 'adminDownloadZip'])->name('admin.requests.download-zip');
    Route::get('/admin/requests/{request}/debug', [\App\Http\Controllers\PublicationsController::class, 'debugFilePaths'])->name('admin.requests.debug');
    Route::get('/admin/requests/{request}/serve', [\App\Http\Controllers\PublicationsController::class, 'serveFile'])->name('admin.requests.serve');
});

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);