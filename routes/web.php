<?php

use App\Http\Controllers\auth\ProfileChangePasswordController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\ThemeController;
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
    Route::post('/profile/theme', [ThemeController::class, 'update'])->name('profile.theme');

    Route::get('/profile/change-password', [ProfileChangePasswordController::class, 'edit'])->name('profile.password.edit');
    Route::post('/profile/change-password', [ProfileChangePasswordController::class, 'update'])->name('profile.password.update');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    Route::get('/push/public-key', [PushSubscriptionController::class, 'publicKey'])->name('push.public-key');
    Route::post('/push/subscribe', [PushSubscriptionController::class, 'store'])->name('push.subscribe');
    Route::delete('/push/unsubscribe', [PushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');

    Route::middleware('role:sales')
        ->prefix('sales')
        ->name('sales.')
        ->group(base_path('routes/sales.php'));

    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(base_path('routes/admin.php'));
});
