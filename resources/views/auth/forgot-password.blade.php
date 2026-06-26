<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Forgot Password - Sales Ledger</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 antialiased">

    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative shadow-2xl overflow-hidden px-8 pt-12 pb-8 justify-between">
        
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center space-x-4">
                <a href="/login" class="text-gray-800 hover:text-black">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h2 class="text-[26px] font-bold text-gray-900">Reset Password</h2>
            </div>

            <p id="instruction-text" class="text-xs text-gray-500 leading-relaxed pl-1">
                Masukkan email akun anda. Kami akan mengirimkan 6 digit kode OTP untuk mengatur ulang kata sandi.
            </p>

            <!-- Error/Success Notif -->
            <div id="notif-box" class="hidden text-xs px-4 py-2.5 rounded-xl font-medium text-center"></div>

            <!-- Form Request OTP -->
            <form id="requestOtpForm" class="space-y-4 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1 pl-1">Email Terdaftar</label>
                    <input type="email" id="email-input" name="email" placeholder="contoh@gmail.com" required 
                           class="w-full bg-[#F3F4F6] border-0 rounded-xl px-4 py-3.5 text-sm focus:ring-2 focus:ring-[#0F47A1] focus:outline-none transition placeholder-gray-400">
                </div>

                <div class="text-center pt-2">
                    <button type="submit" id="btn-request" class="w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-semibold py-4 rounded-xl shadow-md transition duration-200 text-sm">
                        Kirim Kode OTP
                    </button>
                </div>
            </form>

            <!-- Form Reset Password -->
            <form id="resetPasswordForm" class="space-y-4 pt-2 hidden">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1 pl-1 text-center">Kode OTP (Cek Email)</label>
                    <input type="text" name="otp" maxlength="6" placeholder="000000" required 
                           class="w-full bg-[#F3F4F6] border-0 rounded-2xl px-4 py-4 text-center text-2xl font-bold tracking-[10px] focus:ring-2 focus:ring-[#0F47A1] focus:outline-none transition placeholder-gray-300">
                </div>

                <div class="pt-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1 pl-1">Password Baru</label>
                    <input type="password" name="password" placeholder="Minimal 8 karakter" required 
                           class="w-full bg-[#F3F4F6] border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#0F47A1] focus:outline-none transition placeholder-gray-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1 pl-1">Konfirmasi Password Baru</label>
                    <input type="password" name="confirm_password" placeholder="Ketik ulang password" required 
                           class="w-full bg-[#F3F4F6] border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#0F47A1] focus:outline-none transition placeholder-gray-400">
                </div>

                <div class="text-center pt-4">
                    <button type="submit" id="btn-reset" class="w-full bg-[#10B981] hover:bg-green-600 text-white font-semibold py-4 rounded-xl shadow-md transition duration-200 text-sm">
                        Simpan Password Baru
                    </button>
                </div>
            </form>

        </div>
    </div>

    @include('partials.loading')

    <!-- AJAX LOGIC -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const notifBox = document.getElementById('notif-box');
        
        function showNotif(message, isError = true) {
            notifBox.innerHTML = message;
            notifBox.className = `text-xs px-4 py-2.5 rounded-xl font-medium text-center ${isError ? 'bg-red-50 border border-red-200 text-red-600' : 'bg-green-50 border border-green-200 text-green-600'}`;
            notifBox.classList.remove('hidden');
        }

        // Request OTP
        document.getElementById('requestOtpForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-request');
            const email = document.getElementById('email-input').value;
            
            btn.innerText = "Mengirim...";
            btn.disabled = true;
            notifBox.classList.add('hidden');

            try {
                const response = await fetch('/api/forgot-password/send-otp', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ email: email })
                });
                
                const result = await response.json();

                if (response.ok && result.success) {
                    showNotif(result.message, false);
                    document.getElementById('instruction-text').innerText = "Masukkan kode yang baru saja kami kirimkan ke email anda, beserta password baru.";
                    
                    // Transisi UI (Sembunyikan form minta email, munculkan form input OTP)
                    document.getElementById('requestOtpForm').classList.add('hidden');
                    document.getElementById('resetPasswordForm').classList.remove('hidden');
                } else {
                    showNotif(result.message || "Email tidak ditemukan di sistem kami.");
                    btn.disabled = false;
                    btn.innerText = "Kirim Kode OTP";
                }
            } catch (error) {
                showNotif("Terjadi kesalahan jaringan.");
                btn.disabled = false;
                btn.innerText = "Kirim Kode OTP";
            }
        });

        // Submit OTP & Sandi Baru
        document.getElementById('resetPasswordForm').addEventListener('submit', async function(e) {
            Loading.show('Menyimpan password baru...');
            e.preventDefault();
            const btn = document.getElementById('btn-reset');
            const email = document.getElementById('email-input').value; // Ambil email
            const formData = new FormData(e.target);
            formData.append('email', email); // Gabungkan email ke request

            btn.innerText = "Menyimpan...";
            btn.disabled = true;
            notifBox.classList.add('hidden');

            try {
                const response = await fetch('/api/forgot-password/reset', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    Loading.hide();
                    alert(result.message); // Native alert biar jelas
                    window.location.href = result.redirect; // Lempar ke halaman Login
                } else {
                    let errorMsg = result.message || "Gagal mereset sandi.";
                    if(result.errors) errorMsg = Object.values(result.errors).flat().join('<br>');
                    showNotif(errorMsg, true);
                    btn.disabled = false;
                    btn.innerText = "Simpan Password Baru";
                }
            } catch (error) {
                showNotif("Terjadi kesalahan jaringan.");
                btn.disabled = false;
                btn.innerText = "Simpan Password Baru";
            }
        });
    </script>
</body>
</html>