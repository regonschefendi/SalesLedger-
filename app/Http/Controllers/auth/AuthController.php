<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\BrevoMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class AuthController extends Controller
{
    public function __construct(private readonly BrevoMailService $mailer) {}

    // ==========================================
    // PROSES SIGNUP & VERIFIKASI EMAIL
    // ==========================================
    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|same:confirm_password',
        ]);

        $user = User::create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Login secara otomatis, tapi statusnya belum verified
        Auth::login($user);

        // Kirim OTP. Kalau email gagal terkirim, akun tetap dibuat agar user
        // bisa memakai tombol "Kirim Ulang Kode" di halaman verifikasi.
        try {
            $this->sendOtp($user->email);
        } catch (Throwable) {
            return response()->json([
                'success' => true,
                'redirect' => '/verify-email?mail_failed=1',
            ]);
        }

        return response()->json(['success' => true, 'redirect' => '/verify-email']);
    }

    // 2. Fungsi Generate & Kirim OTP (Reusable)
    private function sendOtp(string $email): void
    {
        if (blank(config('services.brevo.key'))
            && config('mail.default') === 'smtp'
            && (blank(config('mail.mailers.smtp.username')) || blank(config('mail.mailers.smtp.password')))) {
            throw new RuntimeException('Konfigurasi email belum lengkap. Isi BREVO_API_KEY (disarankan untuk hosting) atau MAIL_USERNAME/MAIL_PASSWORD.');
        }

        $otp = random_int(100000, 999999);

        // SECURITY BEST PRACTICE: Simpan OTP di Cache, otomatis hancur dalam 2 menit
        Cache::put('otp_'.$email, $otp, now()->addMinutes(2));

        try {
            $this->mailer->sendVerificationCode($email, $otp);
        } catch (Throwable $e) {
            Cache::forget('otp_'.$email);
            Log::error('Gagal mengirim OTP verifikasi.', ['email' => $email, 'error' => $e->getMessage()]);

            throw new RuntimeException('Gagal mengirim kode OTP: '.$e->getMessage(), 0, $e);
        }
    }

    // 3. Proses Resend OTP
    public function resendOtp()
    {
        $user = Auth::user();
        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email sudah diverifikasi'], 400);
        }

        try {
            $this->sendOtp($user->email);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json(['success' => true, 'message' => 'Kode baru telah dikirim!']);
    }

    // 4. Verifikasi Inputan OTP User
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|numeric']);

        /** @var User $user */
        $user = Auth::user();

        $cachedOtp = Cache::get('otp_'.$user->email);

        if (! $cachedOtp) {
            return response()->json(['success' => false, 'message' => 'Kode OTP sudah kadaluarsa (lebih dari 2 menit). Silakan minta kode baru.'], 400);
        }

        if ($cachedOtp != $request->otp) {
            return response()->json(['success' => false, 'message' => 'Kode OTP salah!'], 400);
        }

        // Jika benar, hapus cache dan tandai user sebagai verified
        Cache::forget('otp_'.$user->email);

        $user->email_verified_at = now();
        $user->save();

        return response()->json(['success' => true, 'redirect' => '/select-role']);
    }

    // ==========================================
    // PROSES LOGIN
    // ==========================================
    public function login(Request $request)
    {
        $request->validate([
            'login_id' => 'required|string',
            'password' => 'required|string',
        ]);

        // Deteksi Apakah inputnya Email atau Username
        $loginType = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Susun array kredensial untuk Laravel Auth
        $credentials = [
            $loginType => $request->login_id,
            'password' => $request->password,
        ];

        // Eksekusi pengecekan ke database
        if (Auth::attempt($credentials)) {
            // Regenerasi ID Session agar kebal dari Session Fixation
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            // 4. Routing Berdasarkan Status & Role
            if (! $user->email_verified_at) {
                // Kalau belum verifikasi OTP, lempar ke halaman verifikasi
                return response()->json(['success' => true, 'redirect' => '/verify-email']);
            }

            if ($user->role === 'admin') {
                return response()->json(['success' => true, 'redirect' => '/admin/dashboard']);
            } elseif ($user->role === 'sales') {
                return response()->json(['success' => true, 'redirect' => '/sales/home']);
            } else {
                // Kalau udah login tapi aplikasinya sempet ketutup sebelum dia milih role
                return response()->json(['success' => true, 'redirect' => '/select-role']);
            }
        }

        // Kalau password/email salah
        return response()->json([
            'success' => false,
            'message' => 'Email/Username atau Password tidak cocok.',
        ], 401);
    }

    // ==========================================
    // LUPA PASSWORD (KIRIM OTP)
    // ==========================================
    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email', // Pastikan email terdaftar
        ]);

        $otp = random_int(100000, 999999);

        // Simpan OTP di Cache selama 5 Menit
        Cache::put('reset_otp_'.$request->email, $otp, now()->addMinutes(5));

        // Kirim OTP via Email
        try {
            $this->mailer->sendResetCode($request->email, $otp);
        } catch (Throwable $e) {
            Cache::forget('reset_otp_'.$request->email);
            Log::error('Gagal mengirim OTP reset password.', ['email' => $request->email, 'error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim kode OTP ke email. Silakan coba lagi.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP pemulihan telah dikirim ke email.',
        ]);
    }

    // ==========================================
    // RESET PASSWORD BARU
    // ==========================================
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|numeric',
            'password' => 'required|min:8|same:confirm_password',
        ]);

        $cachedOtp = Cache::get('reset_otp_'.$request->email);

        // Validasi OTP
        if (! $cachedOtp) {
            return response()->json(['success' => false, 'message' => 'Kode OTP sudah kadaluarsa (lebih dari 5 menit).'], 400);
        }

        if ($cachedOtp != $request->otp) {
            return response()->json(['success' => false, 'message' => 'Kode OTP salah!'], 400);
        }

        // OTP Benar -> Update Password di Database
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Segera hapus OTP dari cache setelah berhasil digunakan
        Cache::forget('reset_otp_'.$request->email);

        return response()->json([
            'success' => true,
            'redirect' => '/login',
            'message' => 'Password berhasil diubah! Silakan login kembali.',
        ]);
    }

    // ==========================================
    // PROSES LOGOUT
    // ==========================================
    public function logout(Request $request)
    {
        Auth::logout();

        // Hapus semua jejak sesi
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
