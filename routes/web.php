<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\auth\RoleController;
use App\Http\Controllers\sales\OcrController;
use App\Http\Controllers\sales\SalesController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ==========================================
// ROOT/LANDING ROUTE (Smart Redirect)
// ==========================================
Route::get('/', function () {
    if (Auth::check()) {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->email_verified_at) {
            return redirect('/verify-email');
        }
        if ($user->role === 'admin') {
            return redirect('/admin/dashboard');
        }
        if ($user->role === 'sales') {
            return redirect('/sales/home');
        }

        // Kalau login via Google tapi belum milih role
        return redirect('/select-role');
    }

    // Kalau belum login, tampilin halaman Landing (auth/landing.blade.php)
    return view('auth.landing');
})->name('landing');

// ==========================================
// GLOBAL / PWA ROUTES
// ==========================================
Route::view('/offline', 'offline')->name('offline');

// ==========================================
// GUEST ROUTES (Hanya untuk yang belum login)
// ==========================================
Route::middleware('guest')->group(function () {
    // UI Auth
    Route::view('/signup', 'auth.signup')->name('register');
    Route::view('/login', 'auth.login')->name('login');
    Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');

    // API Auth
    Route::post('/api/register', [AuthController::class, 'register']);
    Route::post('/api/login', [AuthController::class, 'login']);

    // API Reset Password
    Route::post('/api/forgot-password/send-otp', [AuthController::class, 'sendResetOtp']);
    Route::post('/api/forgot-password/reset', [AuthController::class, 'resetPassword']);
});

// ==========================================
// AUTHENTICATED ROUTES (Wajib Login)
// ==========================================
Route::middleware('auth')->group(function () {

    // Logout (KEMBALIKAN KE POST DEMI KEAMANAN CSRF!)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    // Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    // Halaman & Proses Verifikasi Email
    Route::view('/verify-email', 'auth.verify-email')->name('verification.notice');
    Route::post('/api/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/api/resend-otp', [AuthController::class, 'resendOtp']);

    // ==========================================
    // VERIFIED ROUTES (Wajib Login & Email Terverifikasi)
    // ==========================================
    Route::middleware('verified')->group(function () {

        // Halaman & Proses Pilih Role
        Route::view('/select-role', 'auth.select-role')->name('select-role');
        Route::post('/api/set-admin', [RoleController::class, 'setAdmin']);
        Route::post('/api/set-sales', [RoleController::class, 'setSales']);

        // ------------------------------------------
        // ROLE: SALES AREA
        // ------------------------------------------
        Route::middleware('role:sales')->prefix('sales')->name('sales.')->group(function () {
            Route::get('/home', [SalesController::class, 'index'])->name('home');
            
            Route::get('/profile', [SalesController::class, 'profileIndex'])->name('profile.index');
            Route::get('/profile/edit', [SalesController::class, 'profileEdit'])->name('profile.edit');
            Route::post('/profile/update', [SalesController::class, 'profileUpdate'])->name('profile.update');

            // OCR & Faktur
            Route::get('/input', [OcrController::class, 'index'])->name('input');
            Route::post('/api/scan', [OcrController::class, 'processApi'])->name('scan');
            Route::post('/faktur/simpan', [OcrController::class, 'createFaktur'])->name('addFaktur');
        });

        // ------------------------------------------
        // ROLE: ADMIN AREA
        // ------------------------------------------
        Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
            Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
            Route::get('/pembukuan', [AdminController::class, 'pembukuanIndex'])->name('pembukuan.index');
            Route::get('/pembukuan/{id}', [AdminController::class, 'pembukuanShow'])->name('pembukuan.show');

            Route::get('/profile', [AdminController::class, 'profileIndex'])->name('profile.index');
            Route::get('/profile/edit', [AdminController::class, 'profileEdit'])->name('profile.edit');
            Route::post('/profile/update', [AdminController::class, 'profileUpdate'])->name('profile.update');
            Route::get('/profile/custom-code', [AdminController::class, 'customCodeIndex'])->name('profile.custom_code');
            Route::post('/profile/custom-code/update', [AdminController::class, 'customCodeUpdate'])->name('profile.custom-code.update');
        });
    });
});
