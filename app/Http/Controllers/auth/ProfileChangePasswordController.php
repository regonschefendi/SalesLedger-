<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileChangePasswordController extends Controller
{
    public function edit()
    {
        return view('global-profile.password-change');
    }

    /**
     * Memproses update password
     */
    public function update(Request $request)
    {
        // Validasi Best Practice Laravel
        $request->validate([
            // 'current_password' otomatis mengecek kecocokan dengan password lama di database
            'current_password' => ['required', 'current_password'], 
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'Password lama yang Anda masukkan salah.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.'
        ]);

        // Update password yang sudah di-Hash demi keamanan (Good Security)
        $request->user()->update([
            'password' => Hash::make($request->password)
        ]);

        return view('global-profile.change-password-success');
    }
}
