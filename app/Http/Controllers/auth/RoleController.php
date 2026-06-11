<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    // Proses jika user pilih Admin
    public function setAdmin(Request $request)
    {
        $request->validate([
            'admin_code' => 'required|string|unique:users,admin_code' // Wajib unik se-database
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->role = 'admin';
        $user->admin_code = $request->admin_code;
        $user->save();

        return response()->json(['success' => true, 'redirect' => '/admin/dashboard']);
    }

    // Proses jika user pilih Sales
    public function setSales(Request $request)
    {
        $request->validate([
            'admin_code' => 'required|string|exists:users,admin_code'
        ], [
            'admin_code.exists' => 'Kode Referal tidak ditemukan! Pastikan lu minta kode yang benar ke Admin lu.'
        ]);

        // Cek apakah kode admin tersebut ada di database?
        $admin = User::where('admin_code', $request->admin_code)->first();

        if (!$admin) {
            return response()->json(['success' => false, 'message' => 'Kode Admin tidak ditemukan!'], 404);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->role = 'sales';
        $user->parent_admin_id = $admin->id; // Hubungkan Sales ke Admin
        $user->save();

        return response()->json(['success' => true, 'redirect' => '/sales/home']);
    }
}
