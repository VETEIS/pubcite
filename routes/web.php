<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicationsController;

Route::get('/', function () {
    return view('welcome');
});

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
    Route::get('/publications/request', [\App\Http\Controllers\PublicationsController::class, 'create'])->name('publications.request');
    Route::post('/publications/submit', [\App\Http\Controllers\PublicationsController::class, 'submitPublicationRequest'])->name('publications.submit');
    Route::patch('/admin/requests/{request}', [\App\Http\Controllers\PublicationsController::class, 'adminUpdate'])->name('admin.requests.update');
    Route::post('/publications/incentive-application/generate', [\App\Http\Controllers\PublicationsController::class, 'generateIncentiveDocx'])->name('publications.incentive.generate');
    Route::delete('/admin/requests/{id}', [\App\Http\Controllers\PublicationsController::class, 'destroy'])->name('admin.requests.destroy');
});

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);