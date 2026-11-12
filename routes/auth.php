<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\auth\RoleController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::view('/signup', 'auth.signup')->name('register');
    Route::view('/login', 'auth.login')->name('login');
    Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');

    Route::post('/api/register', [AuthController::class, 'register']);
    Route::post('/api/login', [AuthController::class, 'login']);

    Route::post('/api/forgot-password/send-otp', [AuthController::class, 'sendResetOtp']);
    Route::post('/api/forgot-password/reset', [AuthController::class, 'resetPassword']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::view('/verify-email', 'auth.verify-email')->name('verification.notice');
    Route::post('/api/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/api/resend-otp', [AuthController::class, 'resendOtp']);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/select-role', 'auth.select-role')->name('select-role');
    Route::post('/api/set-admin', [RoleController::class, 'setAdmin']);
    Route::post('/api/set-sales', [RoleController::class, 'setSales']);
});
