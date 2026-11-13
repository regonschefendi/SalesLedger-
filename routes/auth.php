<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\auth\RoleController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::view('/signup', 'auth.signup')->name('register');
    Route::view('/login', 'auth.login')->name('login');
    Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');

    Route::post('/api/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
    Route::post('/api/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::post('/api/forgot-password/send-otp', [AuthController::class, 'sendResetOtp'])->middleware('throttle:5,1');
    Route::post('/api/forgot-password/reset', [AuthController::class, 'resetPassword'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/verify-email', [AuthController::class, 'showVerifyEmail'])->name('verification.notice');
    Route::post('/api/verify-otp', [AuthController::class, 'verifyOtp'])->middleware('throttle:15,1');
    Route::post('/api/resend-otp', [AuthController::class, 'resendOtp'])->middleware('throttle:5,1');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/select-role', [RoleController::class, 'showSelectRole'])->name('select-role');
    Route::post('/api/set-admin', [RoleController::class, 'setAdmin'])->middleware('throttle:10,1');
    Route::post('/api/set-sales', [RoleController::class, 'setSales'])->middleware('throttle:10,1');
});
