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
                    <input type="text" name="full_name" placeholder="full name" required class="w-full bg-[#F3F4F6] border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#0F47A1] focus:outline-none transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1 pl-1">Username</label>
                    <input type="text" name="username" placeholder="username" required class="w-full bg-[#F3F4F6] border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#0F47A1] focus:outline-none transition placeholder-gray-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1 pl-1">Email</label>
                    <input type="email" name="email" placeholder="email" required class="w-full bg-[#F3F4F6] border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#0F47A1] focus:outline-none transition placeholder-gray-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1 pl-1">Password</label>
                    <input type="password" name="password" placeholder="password" required class="w-full bg-[#F3F4F6] border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#0F47A1] focus:outline-none transition placeholder-gray-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1 pl-1">Confirm Password</label>
                    <input type="password" name="confirm_password" placeholder="password" required class="w-full bg-[#F3F4F6] border-0 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#0F47A1] focus:outline-none transition placeholder-gray-400">
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

    <script>
        document.getElementById('registerForm').addEventListener('submit', async function(e) {
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