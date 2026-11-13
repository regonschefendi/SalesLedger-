<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    // Tampilkan halaman pilih role, atau langsung lempar user yang sudah punya role.
    public function showSelectRole()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect('/admin/dashboard');
        }

        if ($user->role === 'sales') {
            return redirect('/sales/home');
        }

        return view('auth.select-role');
    }

    // Proses jika user pilih Admin
    public function setAdmin(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Role sudah dipilih dan tidak dapat diubah.',
            ], 409);
        }

        $request->merge(['admin_code' => strtoupper(trim((string) $request->admin_code))]);

        $request->validate([
            'admin_code' => 'required|string|max:50|alpha_dash|unique:users,admin_code', // Wajib unik se-database
        ]);

        $user->update([
            'role' => 'admin',
            'admin_code' => $request->admin_code,
            'parent_admin_id' => null,
        ]);

        return response()->json(['success' => true, 'redirect' => '/admin/dashboard']);
    }

    // Proses jika user pilih Sales
    public function setSales(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Role sudah dipilih dan tidak dapat diubah.',
            ], 409);
        }

        $request->merge(['admin_code' => strtoupper(trim((string) $request->admin_code))]);

        $request->validate([
            'admin_code' => 'required|string|max:50',
        ], [
            'admin_code.required' => 'Kode Referal wajib diisi.',
        ]);

        // Hanya boleh terhubung ke user yang benar-benar berperan sebagai admin.
        $admin = User::where('admin_code', $request->admin_code)
            ->where('role', 'admin')
            ->where('id', '!=', $user->id)
            ->first();

        if (! $admin) {
            return response()->json([
                'success' => false,
                'message' => 'Kode Referal tidak ditemukan! Pastikan kode benar.',
            ], 404);
        }

        $user->update([
            'role' => 'sales',
            'parent_admin_id' => $admin->id, // Hubungkan Sales ke Admin
            'admin_code' => null,
        ]);

        return response()->json(['success' => true, 'redirect' => '/sales/home']);
    }
}
