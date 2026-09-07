<?php

use App\Http\Controllers\Auth\PhoneOtpController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [PhoneOtpController::class, 'create'])
        ->name('login');

    Route::post('otp/request', [PhoneOtpController::class, 'requestCode'])
        ->middleware('throttle:6,1')
        ->name('otp.request');

    Route::post('otp/verify', [PhoneOtpController::class, 'verify'])
        ->middleware('throttle:6,1')
        ->name('otp.verify');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', function () {
        auth()->guard('web')->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    })->name('logout');
});
