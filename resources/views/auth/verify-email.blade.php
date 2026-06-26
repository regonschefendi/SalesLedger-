<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Verify Email - Sales Ledger</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 antialiased">

    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative shadow-2xl overflow-hidden px-8 pt-16 pb-8 justify-between">
        
        <div class="space-y-6 text-center">
            <div class="flex justify-center">
                <div class="w-16 h-16 bg-[#F0F5FF] rounded-full flex items-center justify-center text-[#0F47A1]">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 19v-8.93a2 2 0 01.89-1.664l8-5.333a2 2 0 012.22 0l8 5.333A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-2.25 1.5a2 2 0 01-2.22 0l-2.25-1.5"></path></svg>
                </div>
            </div>

            <div class="space-y-2">
                <h2 class="text-[24px] font-bold text-gray-900">Verify Your Email</h2>
                <p class="text-xs text-gray-500 px-4 leading-relaxed">
                    Masukkan 6 digit kode verifikasi rahasia yang dikirim ke email akun anda.
                </p>
            </div>

            <form id="otpForm" class="space-y-6 pt-4">
                <div>
                    <input type="text" id="otp-input" name="otp" maxlength="6" placeholder="000000" required 
                           class="w-full bg-[#F3F4F6] border-0 rounded-2xl px-4 py-4 text-center text-2xl font-bold tracking-[10px] focus:ring-2 focus:ring-[#0F47A1] focus:outline-none transition placeholder-gray-300">
                </div>

                <div id="error-box" class="hidden text-xs bg-red-50 border border-red-200 text-red-600 px-4 py-2.5 rounded-xl font-medium"></div>

                <div class="text-xs text-gray-500">
                    <span id="timer-text">Kode berlaku selama <strong id="countdown" class="text-red-500">02:00</strong></span>
                    <button type="button" id="btn-resend" onclick="resendOtp()" disabled 
                            class="hidden font-bold text-[#0F47A1] hover:underline disabled:opacity-50 disabled:no-underline">
                        Kirim Ulang Kode
                    </button>
                </div>

                <button type="submit" id="btn-submit" class="w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-semibold py-4 rounded-xl shadow-md transition duration-200 text-sm">
                    <span id="btn-text">Verify & Continue</span>
                </button>
            </form>
        </div>

        <div class="text-center text-xs text-gray-400">
            Sales Ledger Security System
        </div>
    </div>

    @include('partials.loading')

    <script>
        let timeLeft = 120; // 2 Menit dalam hitungan detik
        let timerInterval;

        function startTimer() {
            clearInterval(timerInterval);
            timeLeft = 120;
            document.getElementById('timer-text').classList.remove('hidden');
            document.getElementById('btn-resend').classList.add('hidden');
            document.getElementById('btn-resend').disabled = true;
            document.getElementById('otp-input').disabled = false;

            timerInterval = setInterval(() => {
                let minutes = Math.floor(timeLeft / 60);
                let seconds = timeLeft % 60;

                minutes = minutes < 10 ? '0' + minutes : minutes;
                seconds = seconds < 10 ? '0' + seconds : seconds;

                document.getElementById('countdown').innerText = `${minutes}:${seconds}`;

                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    document.getElementById('timer-text').classList.add('hidden');
                    document.getElementById('btn-resend').classList.remove('hidden');
                    document.getElementById('btn-resend').disabled = false;
                    document.getElementById('otp-input').disabled = true; // Kunci input jika expired
                }
                timeLeft--;
            }, 1000);
        }

        // Jalankan timer pas halaman kelar di-load
        window.onload = startTimer;

        // AJAX: Submit OTP
        document.getElementById('otpForm').addEventListener('submit', async function(e) {
            Loading.show('Memverifikasi OTP...');
            e.preventDefault();
            const formData = new FormData(e.target);
            const btnText = document.getElementById('btn-text');
            const btnSubmit = document.getElementById('btn-submit');
            const errorBox = document.getElementById('error-box');

            btnText.innerText = "Verifying...";
            btnSubmit.disabled = true;
            errorBox.classList.add('hidden');

            try {
                const response = await fetch('/api/verify-otp', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    window.location.href = result.redirect; // Lanjut ke halaman Select Role
                } else {
                    errorBox.innerText = result.message || "Kode OTP salah.";
                    errorBox.classList.remove('hidden');
                }
            } catch (error) {
                errorBox.innerText = "Terjadi kesalahan sistem jaringan.";
                errorBox.classList.remove('hidden');
            } finally {
                btnText.innerText = "Verify & Continue";
                btnSubmit.disabled = false;
                Loading.hide();
            }
        });

        // AJAX: Resend OTP
        async function resendOtp() {
            const errorBox = document.getElementById('error-box');
            errorBox.classList.add('hidden');

            try {
                const response = await fetch('/api/resend-otp', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    alert('Kode baru berhasil dikirim ke email anda!');
                    startTimer(); // Reset countdown biar mulai dari 2 menit lagi
                } else {
                    alert(result.message || 'Gagal mengirim ulang kode.');
                }
            } catch (error) {
                alert('Gagal terhubung ke server.');
            }
        }
    </script>
</body>
</html>