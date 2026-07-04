<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Create Account - Sales Ledger</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 antialiased">

    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative shadow-2xl overflow-hidden px-8 pt-12 pb-8 justify-between">
        
        <div class="space-y-6">
            <div class="flex items-center space-x-4">
                <a href="/login" class="text-gray-800 hover:text-black">
                    <svg class="w-6 " fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h2 class="text-[26px] font-bold text-gray-900">Create Account</h2>
            </div>

            <form id="registerForm" class="space-y-4 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1 pl-1">Full Name</label>
                    <input type="text" name="full_name" placeholder="Full Name" required class="w-full bg-[#F3F4F6] border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#0F47A1] focus:outline-none transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1 pl-1">Username</label>
                    <input type="text" name="username" placeholder="Username" required class="w-full bg-[#F3F4F6] border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#0F47A1] focus:outline-none transition placeholder-gray-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1 pl-1">Email</label>
                    <input type="email" name="email" placeholder="email" required class="w-full bg-[#F3F4F6] border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#0F47A1] focus:outline-none transition placeholder-gray-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1 pl-1">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" placeholder="Password" required class="w-full bg-[#F3F4F6] border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#0F47A1] focus:outline-none transition placeholder-gray-400">
                        <button type="button" onclick="togglePasswordVisibility('password')" class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </button>
                    </div>
                    
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1 pl-1">Confirm Password</label>
                    <div class="relative">
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Password" required class="w-full bg-[#F3F4F6] border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#0F47A1] focus:outline-none transition placeholder-gray-400">
                        <button type="button" onclick="togglePasswordVisibility('confirm_password')" class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </button>
                    </div>
                </div>

                <div id="error-box" class="hidden text-xs bg-red-50 border border-red-200 text-red-600 px-4 py-2.5 rounded-xl font-medium"></div>

                <div class="text-center pt-4">
                    <button type="submit" id="btn-submit" class="w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-semibold py-4 rounded-xl shadow-md transition duration-200 text-sm flex items-center justify-center">
                        <span id="btn-text">Create Account</span>
                    </button>
                </div>
            </form>
        </div>

        <div class="text-center text-xs text-gray-500 pt-6">
            Don't have an account? <a href="/login" class="font-bold text-[#0F47A1] hover:underline">Sign Up</a>
        </div>
    </div>

    @include('partials.loading')

    <script>
        // Fungsi Toggle Mata Password
        function togglePasswordVisibility(inputId) {
            const pwd = document.getElementById(inputId);
            if (pwd) {
                pwd.type = pwd.type === 'password' ? 'text' : 'password';
            }
        }

        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            Loading.show('Membuat akun...');
            e.preventDefault();
            
            const form = e.target;
            const formData = new FormData(form);
            const btnText = document.getElementById('btn-text');
            const btnSubmit = document.getElementById('btn-submit');
            const errorBox = document.getElementById('error-box');

            // Set Loading state
            btnText.innerText = "Processing...";
            btnSubmit.disabled = true;
            errorBox.classList.add('hidden');

            try {
                const response = await fetch('/api/register', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    // Redirect otomatis ke halaman verifikasi email OTP bawaan Controller
                    window.location.href = result.redirect;
                } else {
                    // Handle validasi error dari Laravel (Password ga sama, email duplikat, dll)
                    let errorMsg = result.message || "Terjadi kesalahan registrasi.";
                    if(result.errors) {
                        errorMsg = Object.values(result.errors).flat().join('<br>');
                    }
                    errorBox.innerHTML = errorMsg;
                    errorBox.classList.remove('hidden');
                }
            } catch (error) {
                errorBox.innerHTML = "Gagal terhubung ke server. Periksa koneksi internet.";
                errorBox.classList.remove('hidden');
            } finally {
                btnText.innerText = "Create Account";
                btnSubmit.disabled = false;
            }
        });
    </script>
</body>
</html>