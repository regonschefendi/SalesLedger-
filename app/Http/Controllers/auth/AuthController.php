<?php

namespace App\Http\Controllers\auth;

use App\Auth\CachedUserProvider;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\BrevoMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class AuthController extends Controller
{
    private const OTP_TTL_MINUTES = 2;

    private const OTP_MAX_ATTEMPTS = 5;

    private const OTP_RESEND_COOLDOWN_SECONDS = 60;

    private const RESET_OTP_TTL_MINUTES = 5;

    private const RESET_OTP_MAX_ATTEMPTS = 5;

    private const RESET_OTP_RESEND_COOLDOWN_SECONDS = 60;

    public function __construct(private readonly BrevoMailService $mailer) {}

    private function normalizeEmail(?string $email): string
    {
        return mb_strtolower(trim((string) $email));
    }

    // ==========================================
    // PROSES SIGNUP & VERIFIKASI EMAIL
    // ==========================================
    public function register(Request $request)
    {
        $request->merge([
            'full_name' => trim((string) $request->full_name),
            'username' => trim((string) $request->username),
            'email' => $this->normalizeEmail($request->email),
        ]);

        $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                function (string $attribute, $value, \Closure $fail) {
                    // PostgreSQL membedakan huruf besar/kecil pada unique index,
                    // jadi cek manual agar "Adminapp" dan "adminapp" dianggap sama.
                    if (User::whereRaw('LOWER(username) = ?', [mb_strtolower(trim((string) $value))])->exists()) {
                        $fail('Nama pengguna sudah digunakan.');
                    }
                },
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                function (string $attribute, $value, \Closure $fail) {
                    // Cek tanpa membedakan huruf besar/kecil agar tidak ada
                    // dua akun dengan email yang sama beda kapitalisasi.
                    if (User::whereRaw('LOWER(email) = ?', [$value])->exists()) {
                        $fail('Email sudah terdaftar.');
                    }
                },
            ],
            'password' => 'required|string|min:8|max:255|same:confirm_password',
        ]);

        $user = User::create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Login secara otomatis, tapi statusnya belum verified
        Auth::login($user);
        $request->session()->regenerate();

        // Kirim OTP. Kalau email gagal terkirim, akun tetap dibuat agar user
        // bisa memakai tombol "Kirim Ulang Kode" di halaman verifikasi.
        try {
            $this->sendOtp($user->email);
        } catch (Throwable $e) {
            Log::error('Registrasi berhasil tetapi OTP gagal dikirim.', [
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);

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
        $email = $this->normalizeEmail($email);

        if (blank(config('services.brevo.key'))
            && config('mail.default') === 'smtp'
            && (blank(config('mail.mailers.smtp.username')) || blank(config('mail.mailers.smtp.password')))) {
            throw new RuntimeException('Konfigurasi email belum lengkap. Isi BREVO_API_KEY (disarankan untuk hosting) atau MAIL_USERNAME/MAIL_PASSWORD.');
        }

        $otp = random_int(100000, 999999);

        // SECURITY BEST PRACTICE: Simpan OTP di Cache, otomatis hancur dalam 2 menit
        Cache::put('otp_'.$email, $otp, now()->addMinutes(self::OTP_TTL_MINUTES));
        Cache::put('otp_attempts_'.$email, 0, now()->addMinutes(self::OTP_TTL_MINUTES));
        Cache::put('otp_cooldown_'.$email, true, now()->addSeconds(self::OTP_RESEND_COOLDOWN_SECONDS));

        try {
            $this->mailer->sendVerificationCode($email, $otp);
        } catch (Throwable $e) {
            Cache::forget('otp_'.$email);
            Cache::forget('otp_attempts_'.$email);
            Cache::forget('otp_cooldown_'.$email);
            Log::error('Gagal mengirim OTP verifikasi.', ['email' => $email, 'error' => $e->getMessage()]);

            throw new RuntimeException('Gagal mengirim kode OTP.', 0, $e);
        }
    }

    // 3. Proses Resend OTP
    public function resendOtp()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->email_verified_at) {
            return response()->json(['success' => false, 'message' => 'Email sudah diverifikasi.'], 400);
        }

        $email = $this->normalizeEmail($user->email);

        if (Cache::has('otp_cooldown_'.$email)) {
            return response()->json([
                'success' => false,
                'message' => 'Tunggu sebentar sebelum meminta kode baru.',
            ], 429);
        }

        try {
            $this->sendOtp($email);
        } catch (Throwable $e) {
            Log::error('Gagal mengirim ulang OTP.', ['email' => $email, 'error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim kode OTP. Silakan coba lagi nanti.',
            ], 500);
        }

        return response()->json(['success' => true, 'message' => 'Kode baru telah dikirim!']);
    }

    // 4. Verifikasi Inputan OTP User
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        /** @var User $user */
        $user = Auth::user();

        if ($user->email_verified_at) {
            return response()->json(['success' => false, 'message' => 'Email sudah diverifikasi.'], 400);
        }

        $email = $this->normalizeEmail($user->email);
        $otpKey = 'otp_'.$email;
        $attemptsKey = 'otp_attempts_'.$email;

        $attempts = (int) Cache::get($attemptsKey, 0);

        if ($attempts >= self::OTP_MAX_ATTEMPTS) {
            Cache::forget($otpKey);

            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak percobaan. Minta kode OTP baru.',
            ], 429);
        }

        $cachedOtp = Cache::get($otpKey);

        if (! $cachedOtp) {
            return response()->json(['success' => false, 'message' => 'Kode OTP sudah kadaluarsa (lebih dari 2 menit). Silakan minta kode baru.'], 400);
        }

        if (! hash_equals((string) $cachedOtp, (string) $request->otp)) {
            $attempts++;
            Cache::put($attemptsKey, $attempts, now()->addMinutes(self::OTP_TTL_MINUTES));

            if ($attempts >= self::OTP_MAX_ATTEMPTS) {
                Cache::forget($otpKey);

                return response()->json([
                    'success' => false,
                    'message' => 'Terlalu banyak percobaan. Minta kode OTP baru.',
                ], 429);
            }

            return response()->json(['success' => false, 'message' => 'Kode OTP salah!'], 400);
        }

        // Jika benar, hapus cache dan tandai user sebagai verified
        Cache::forget($otpKey);
        Cache::forget($attemptsKey);

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
            'login_id' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        $loginId = trim((string) $request->login_id);
        $remember = $request->boolean('remember');

        // Deteksi Apakah inputnya Email atau Username
        $loginType = filter_var($loginId, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Susun array kredensial untuk Laravel Auth
        $credentials = [
            $loginType => $loginId,
            'password' => $request->password,
        ];

        $authenticated = Auth::attempt($credentials, $remember);

        // Login email tidak membedakan huruf besar/kecil. Ini juga menangani
        // akun lama yang emailnya tersimpan dengan huruf campuran.
        if (! $authenticated && $loginType === 'email') {
            $user = User::whereRaw('LOWER(email) = ?', [$this->normalizeEmail($loginId)])->first();

            if ($user && Hash::check($request->password, $user->password)) {
                Auth::login($user, $remember);
                $authenticated = true;
            }
        }

        if (! $authenticated) {
            return response()->json([
                'success' => false,
                'message' => 'Email/Nama Pengguna atau Kata Sandi tidak cocok.',
            ], 401);
        }

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

    public function showVerifyEmail()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->email_verified_at) {
            return redirect('/');
        }

        return view('auth.verify-email');
    }

    // ==========================================
    // LUPA KATA SANDI (KIRIM OTP)
    // ==========================================
    public function sendResetOtp(Request $request)
    {
        $request->merge(['email' => $this->normalizeEmail($request->email)]);

        $request->validate([
            'email' => 'required|string|email|max:255',
        ]);

        $email = (string) $request->email;

        if (Cache::has('reset_otp_cooldown_'.$email)) {
            return response()->json([
                'success' => false,
                'message' => 'Tunggu sebentar sebelum meminta kode baru.',
            ], 429);
        }

        Cache::put('reset_otp_cooldown_'.$email, true, now()->addSeconds(self::RESET_OTP_RESEND_COOLDOWN_SECONDS));

        // Balasan seragam agar tidak membocorkan apakah sebuah email terdaftar.
        $genericResponse = response()->json([
            'success' => true,
            'message' => 'Jika email terdaftar, kode OTP pemulihan telah dikirim.',
        ]);

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (! $user) {
            return $genericResponse;
        }

        $otp = random_int(100000, 999999);

        // Simpan OTP di Cache selama 5 Menit
        Cache::put('reset_otp_'.$email, $otp, now()->addMinutes(self::RESET_OTP_TTL_MINUTES));
        Cache::put('reset_otp_attempts_'.$email, 0, now()->addMinutes(self::RESET_OTP_TTL_MINUTES));

        // Kirim OTP via Email
        try {
            $this->mailer->sendResetCode($email, $otp);
        } catch (Throwable $e) {
            Cache::forget('reset_otp_'.$email);
            Cache::forget('reset_otp_attempts_'.$email);
            Log::error('Gagal mengirim OTP atur ulang kata sandi.', ['email' => $email, 'error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim kode OTP ke email. Silakan coba lagi.',
            ], 500);
        }

        return $genericResponse;
    }

    // ==========================================
    // ATUR ULANG KATA SANDI BARU
    // ==========================================
    public function resetPassword(Request $request)
    {
        $request->merge(['email' => $this->normalizeEmail($request->email)]);

        $request->validate([
            'email' => 'required|string|email|max:255',
            'otp' => 'required|digits:6',
            'password' => 'required|string|min:8|max:255|same:confirm_password',
        ]);

        $email = (string) $request->email;
        $otpKey = 'reset_otp_'.$email;
        $attemptsKey = 'reset_otp_attempts_'.$email;

        $invalidResponse = response()->json([
            'success' => false,
            'message' => 'Kode OTP salah atau sudah kadaluarsa.',
        ], 400);

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (! $user) {
            return $invalidResponse;
        }

        $attempts = (int) Cache::get($attemptsKey, 0);

        if ($attempts >= self::RESET_OTP_MAX_ATTEMPTS) {
            Cache::forget($otpKey);

            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak percobaan. Minta kode OTP baru.',
            ], 429);
        }

        $cachedOtp = Cache::get($otpKey);

        if (! $cachedOtp) {
            return $invalidResponse;
        }

        if (! hash_equals((string) $cachedOtp, (string) $request->otp)) {
            $attempts++;
            Cache::put($attemptsKey, $attempts, now()->addMinutes(self::RESET_OTP_TTL_MINUTES));

            if ($attempts >= self::RESET_OTP_MAX_ATTEMPTS) {
                Cache::forget($otpKey);

                return response()->json([
                    'success' => false,
                    'message' => 'Terlalu banyak percobaan. Minta kode OTP baru.',
                ], 429);
            }

            return $invalidResponse;
        }

        // OTP Benar -> Perbarui Kata Sandi di Database
        $user->password = Hash::make($request->password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        // Segera hapus OTP dari cache setelah berhasil digunakan
        Cache::forget($otpKey);
        Cache::forget($attemptsKey);
        Cache::forget('reset_otp_cooldown_'.$email);

        return response()->json([
            'success' => true,
            'redirect' => '/login',
            'message' => 'Kata sandi berhasil diubah! Silakan masuk kembali.',
        ]);
    }

    // ==========================================
    // PROSES LOGOUT
    // ==========================================
    public function logout(Request $request)
    {
        /** @var User|null $user */
        $user = Auth::user();

        Auth::logout();

        // Hapus semua jejak sesi
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Bersihkan cache user agar tidak tersisa setelah logout.
        if ($user) {
            Cache::forget(CachedUserProvider::cacheKey($user->getKey()));
        }

        return redirect('/login');
    }
}
