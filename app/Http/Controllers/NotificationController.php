<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->latest()
            ->paginate(20);

        $unreadCount = Auth::user()->unreadNotifications()->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead(string $id)
    {
        $notification = Auth::user()
            ->notifications()
            ->whereKey($id)
            ->firstOrFail();

        if (! $notification->read_at) {
            $notification->markAsRead();
        }

        $url = $notification->data['url'] ?? null;

        return $url ? redirect()->to($url) : back();
    }

    public function markAllAsRead(Request $request)
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}
