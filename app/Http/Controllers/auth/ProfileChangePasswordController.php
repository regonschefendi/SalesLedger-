<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileChangePasswordController extends Controller
{
    public function edit()
    {
        return view('global-profile.password-change');
    }

    /**
     * Memproses pembaruan kata sandi
     */
    public function update(Request $request)
    {
        // Validasi sesuai praktik terbaik Laravel
        $request->validate([
            // 'current_password' otomatis mengecek kecocokan dengan kata sandi lama di database
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'Kata sandi lama yang Anda masukkan salah.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        // Perbarui kata sandi yang sudah di-hash demi keamanan
        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return view('global-profile.change-password-success');
    }
}
