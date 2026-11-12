<?php

use App\Http\Controllers\auth\ProfileChangePasswordController;
use App\Http\Controllers\NotificationController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->email_verified_at) {
            return redirect('/verify-email');
        }
        if ($user->role === 'admin') {
            return redirect('/admin/dashboard');
        }
        if ($user->role === 'sales') {
            return redirect('/sales/home');
        }

        return redirect('/select-role');
    }

    return view('auth.landing');
})->name('landing');

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile/change-password', [ProfileChangePasswordController::class, 'edit'])->name('profile.password.edit');
    Route::post('/profile/change-password', [ProfileChangePasswordController::class, 'update'])->name('profile.password.update');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    Route::middleware('role:sales')
        ->prefix('sales')
        ->name('sales.')
        ->group(base_path('routes/sales.php'));

    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(base_path('routes/admin.php'));
});
