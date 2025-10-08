<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicationsController;
use App\Http\Controllers\CitationsController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\ProgressController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');


// Debug route for troubleshooting
Route::get('/debug', function () {
    return view('debug');
})->name('debug');


// Draft
Route::get('/api/drafts', [App\Http\Controllers\DraftController::class, 'apiIndex'])->name('drafts.api');
Route::get('/api/draft/{draft}', [App\Http\Controllers\DraftController::class, 'apiShow'])->name('draft.api');
Route::delete('/drafts/{draft}', [App\Http\Controllers\DraftController::class, 'destroy'])->name('drafts.destroy');

// test
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
Route::post('/privacy/accept', [LoginController::class, 'acceptPrivacy'])->name('privacy.accept');
Route::get('/privacy/status', [LoginController::class, 'getPrivacyStatus'])->name('privacy.status');

// logout with draft session cleanup
Route::post('/logout', [\App\Http\Controllers\Auth\LogoutController::class, 'logout'])->name('logout');

// Registration

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'throttle:120,1'
])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/publications/request', [\App\Http\Controllers\PublicationsController::class, 'create'])->name('publications.request')->middleware('mobile.restrict');
    Route::post('/publications/submit', [\App\Http\Controllers\PublicationsController::class, 'submitPublicationRequest'])->name('publications.submit')->middleware('mobile.restrict');
    Route::get('/citations/request', [\App\Http\Controllers\CitationsController::class, 'create'])->name('citations.request')->middleware('mobile.restrict');
    Route::post('/citations/submit', [\App\Http\Controllers\CitationsController::class, 'submitCitationRequest'])->name('citations.submit')->middleware('mobile.restrict');
    Route::post('/citations/generate', [\App\Http\Controllers\CitationsController::class, 'generateCitationDocx'])->name('citations.generate')->middleware('mobile.restrict');
    Route::get('/citations/success', [\App\Http\Controllers\CitationsController::class, 'success'])->name('citations.success')->middleware('mobile.restrict');
    // publication DOCX generations
    Route::post('/publications/generate', [\App\Http\Controllers\PublicationsController::class, 'generatePublicationDocx'])->name('publications.generate')->middleware('mobile.restrict');
    Route::post('/publications/preload-templates', [\App\Http\Controllers\PublicationsController::class, 'preloadTemplates'])->name('publications.preloadTemplates')->middleware('mobile.restrict');
    Route::post('/citations/preload-templates', [\App\Http\Controllers\CitationsController::class, 'preloadTemplates'])->name('citations.preloadTemplates')->middleware('mobile.restrict');
    // Nudge
    Route::post('/requests/{request}/nudge', [\App\Http\Controllers\DashboardController::class, 'nudge'])->name('requests.nudge');
    // Signing
    Route::get('/signing', [\App\Http\Controllers\SigningController::class, 'index'])->name('signing.index');
    Route::post('/signing/revert-document', [\App\Http\Controllers\SigningController::class, 'revertDocument'])->name('signing.revert-document');
    Route::get('/signing/download-files/{requestId}', [\App\Http\Controllers\SigningController::class, 'downloadRequestFiles'])->name('signing.download-files')->middleware('throttle:20,1');
    Route::post('/signing/upload-signed', [\App\Http\Controllers\SigningController::class, 'uploadSignedDocuments'])->name('signing.upload-signed')->middleware('throttle:10,1');

// file download
Route::middleware(['auth', 'admin', 'throttle:30,1'])->group(function () {
    Route::get('/admin/download/{type}/{filename}', [\App\Http\Controllers\AdminFileController::class, 'download'])->name('admin.download.file');
});
    
    // signature management
    Route::middleware('auth')->group(function () {
        Route::resource('signatures', \App\Http\Controllers\SignatureController::class)->except(['create']);
    });
    

    

    // search signatories
    Route::get('/signatories', [\App\Http\Controllers\SignatoryController::class, 'index'])->name('signatories.index');
    Route::get('/signatories/validate', [\App\Http\Controllers\SignatoryController::class, 'validate'])->name('signatories.validate');
    
    // testing generateDocx
    Route::get('/debug-generate', function() {
        try {
            $templatePath = storage_path('app/templates/Incentive_Application_Form.docx');
            $exists = file_exists($templatePath);
            $service = new \App\Services\TemplateCacheService();
            $processor = $service->getTemplateProcessor($templatePath);
            return response()->json([
                'template_path' => $templatePath,
                'file_exists' => $exists,
                'service_works' => true,
                'processor_created' => $processor ? true : false
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    });
});

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// set privacy session
Route::post('auth/google/privacy-check', [GoogleController::class, 'checkPrivacyBeforeGoogle'])->name('google.privacy.check');

Route::middleware(['auth', 'mobile.restrict', 'throttle:600,1'])->prefix('admin')->group(function () {
    // admin dashboard page
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/requests/manage', [\App\Http\Controllers\AdminRequestController::class, 'index'])->name('admin.requests.manage');
    Route::get('/requests/{request}/data', [\App\Http\Controllers\AdminRequestController::class, 'getRequestData'])->name('admin.requests.data');
    Route::get('/requests/{request}/download-zip', [\App\Http\Controllers\AdminRequestController::class, 'downloadZip'])->name('admin.requests.download-zip');
    // endpointss
    Route::get('/dashboard/data', [\App\Http\Controllers\DashboardController::class, 'getData'])->name('admin.dashboard.data');
    Route::get('/dashboard/stream', [\App\Http\Controllers\DashboardController::class, 'streamUpdates'])->name('admin.dashboard.stream');
    // Settings
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('admin.settings');
    Route::put('/settings', [\App\Http\Controllers\SettingsController::class, 'update'])->name('admin.settings.update');
    Route::post('/settings/create-account', [\App\Http\Controllers\SettingsController::class, 'createAccount'])->name('admin.settings.create-account');
    // notifications endpoints
    Route::get('/notifications', [\App\Http\Controllers\AdminUserController::class, 'listNotifications'])->name('admin.notifications.list');
    Route::post('/notifications/read', [\App\Http\Controllers\AdminUserController::class, 'markNotificationsRead'])->name('admin.notifications.read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\AdminUserController::class, 'markNotificationsRead'])->name('admin.notifications.mark-all-read');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\AdminUserController::class, 'markNotificationAsRead'])->name('admin.notifications.mark-read');
    
    // request management
    Route::patch('/requests/{request}', [\App\Http\Controllers\AdminRequestController::class, 'updateStatus'])->name('admin.requests.update');
    Route::patch('/requests/{request}/status', [\App\Http\Controllers\AdminRequestController::class, 'updateStatus'])->name('admin.requests.status');
    Route::delete('/requests/{id}', [\App\Http\Controllers\PublicationsController::class, 'destroy'])->name('admin.requests.destroy');
    Route::get('/requests/{request}/download', [\App\Http\Controllers\PublicationsController::class, 'adminDownloadFile'])->name('admin.requests.download');
    Route::get('/requests/{request}/debug', [\App\Http\Controllers\PublicationsController::class, 'debugFilePaths'])->name('admin.requests.debug');
    Route::get('/requests/{request}/serve', [\App\Http\Controllers\PublicationsController::class, 'serveFile'])->name('admin.requests.serve');
});

// progress tracking
Route::middleware(['auth', 'throttle:10,1'])->get('/progress/stream', [ProgressController::class, 'streamProgress'])->name('progress.stream');