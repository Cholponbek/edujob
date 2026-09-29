<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CandidateResumeController;
use App\Http\Controllers\Institution\ApplicationController as InstitutionApplicationController;
use App\Http\Controllers\Institution\StaffRequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/applications', [ApplicationController::class, 'store'])->name('applications.store');
    Route::post('/applications/{application}/answers', [ApplicationController::class, 'submitAnswers'])->name('applications.answers');

    Route::post('/candidate/resume/upload', [CandidateResumeController::class, 'uploadResume'])->name('candidate.resume.upload');
    Route::post('/candidate/resume/generate', [CandidateResumeController::class, 'generateResume'])->name('candidate.resume.generate');
    Route::post('/candidate/recommendations/refresh', [CandidateResumeController::class, 'refreshRecommendations'])->name('candidate.recommendations.refresh');

    Route::post('/institution/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');

    Route::get('/institution/staff-requests', [StaffRequestController::class, 'index'])->name('institution.staff-requests.index');
    Route::post('/institution/staff-requests', [StaffRequestController::class, 'store'])->name('institution.staff-requests.store');
    Route::patch('/institution/staff-requests/{staffRequest}', [StaffRequestController::class, 'update'])->name('institution.staff-requests.update');
    Route::post('/institution/staff-requests/{staffRequest}/publish', [StaffRequestController::class, 'publish'])->name('institution.staff-requests.publish');
    Route::post('/institution/staff-requests/{staffRequest}/close', [StaffRequestController::class, 'close'])->name('institution.staff-requests.close');

    Route::get('/institution/staff-requests/{staffRequest}/applications', [InstitutionApplicationController::class, 'index'])->name('institution.applications.index');
    Route::patch('/institution/applications/{application}', [InstitutionApplicationController::class, 'updateStatus'])->name('institution.applications.update');
});

require __DIR__.'/auth.php';
