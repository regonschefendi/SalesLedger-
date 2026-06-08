<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Cek apakah role user di database cocok dengan role yang diminta di web.php
        if (Auth::user()->role !== $role) {
            // Jika Sales mencoba buka link Admin (atau sebaliknya), tolak dan kembalikan!
            abort(403, 'Akses Ditolak! Lu gak punya izin buat masuk ke halaman ini.');
        }

        return $next($request);
    }
}
