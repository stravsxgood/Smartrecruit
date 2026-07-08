<?php

use App\Http\Controllers\ApplicantProfileController;
use App\Http\Controllers\ProfileAvatarController;
use App\Http\Controllers\CvAnalysisController;
use App\Http\Controllers\JobPositionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\NewsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::get('/jobapply', [JobController::class, 'index'])->name('jobapply.index');
Route::get('/jobapply/{job}', [JobController::class, 'show'])->name('jobapply.show');

Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');

Route::middleware(['auth', 'verified'])->group(function () {
   Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/jobapply/{jobPosition}/apply', [JobController::class, 'apply'])
        ->name('jobapply.apply')
        ->middleware(['auth', 'verified']);

    Route::post('/profile/avatar', [ProfileAvatarController::class, 'store'])
        ->name('profile.avatar.store');

    Route::post('/cv-analysis/analyze', [CvAnalysisController::class, 'analyze'])
        ->name('cv.analyze');

    Route::get('/cv-analysis/result', [CvAnalysisController::class, 'show'])
        ->name('cv.result');

    Route::resource('job-positions', JobPositionController::class)
        ->except(['create', 'edit']);

    Route::get('/my-profile', [ApplicantProfileController::class, 'show'])
        ->name('applicant-profiles.show');

    Route::post('/my-profile', [ApplicantProfileController::class, 'store'])
        ->name('applicant-profiles.store');

    Route::put('/my-profile/{applicantProfile}', [ApplicantProfileController::class, 'update'])
        ->name('applicant-profiles.update');

    Route::post('/my-profile/documents', [ApplicantProfileController::class, 'uploadDocument'])
        ->name('applicant-profiles.documents.upload');

    Route::delete('/my-profile/documents/{document}', [ApplicantProfileController::class, 'deleteDocument'])
        ->name('applicant-profiles.documents.delete');
});

require __DIR__ . '/settings.php';
