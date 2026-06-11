<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In - Sales Ledger</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 antialiased">

    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-gradient-to-b from-[#70B5FF] to-[#C2E0FF] relative shadow-2xl overflow-hidden">
        
        <div class="h-44 flex items-center justify-center"></div>

        <div class="bg-white rounded-t-[40px] px-8 pt-10 pb-8 flex-grow flex flex-col justify-between shadow-[0_-10px_25px_rgba(0,0,0,0.05)]">
            
            <div class="space-y-6">
                <div class="text-center mb-8">
                    <h2 class="text-[28px] font-bold text-gray-900">Welcome Back</h2>
                </div>

                <form id="loginForm" class="space-y-4">
                    {{-- @csrf --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 pl-1">Email / Username</label>
                        <input type="text" name="login_id" placeholder="email/username" required class="w-full bg-[#F3F4F6] border-0 rounded-xl px-4 py-3.5 text-sm focus:ring-2 focus:ring-[#0F47A1] focus:outline-none transition placeholder-gray-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5 pl-1">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" placeholder="password" required class="w-full bg-[#F3F4F6] border-0 rounded-xl px-4 py-3.5 text-sm focus:ring-2 focus:ring-[#0F47A1] focus:outline-none transition placeholder-gray-400 pr-10">
                            <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>
                        </div>
                    </div>

                    <div class="text-center pt-1">
                        <button type="submit" class="w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-semibold py-4 rounded-xl shadow-md transition duration-200 text-sm flex items-center justify-center">
                            <span id="btn-text">Sign In</span>
                        </button>
                    </div>
                </form>

                <div class="text-center">
                    <a href="/forgot-password" class="text-xs font-semibold text-[#0F47A1] hover:underline">Forgot Password?</a>
                </div>

                <div class="flex items-center my-6">
                    <div class="flex-grow border-t border-gray-200"></div>
                    <span class="mx-4 text-xs text-gray-400 font-medium">Or</span>
                    <div class="flex-grow border-t border-gray-200"></div>
                </div>
            </div>

            <div class="text-center text-xs text-gray-500 pt-6">
                Don't have an account? <a href="/signup" class="font-bold text-[#0F47A1] hover:underline">Sign Up</a>
            </div>
        </div>
    </div>

    <script>
        // Fungsi Toggle Mata Password
        function togglePasswordVisibility() {
            const pwd = document.getElementById('password');
            pwd.type = pwd.type === 'password' ? 'text' : 'password';
        }

        // Fungsi AJAX Login
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = e.target;
            const formData = new FormData(form);
            const btnText = document.getElementById('btn-text');
            const btn = form.querySelector('button[type="submit"]');
            
            // Hapus pesan error lama jika ada
            const oldError = document.getElementById('error-message');
            if(oldError) oldError.remove();

            // Ubah tombol jadi mode loading
            btnText.innerText = "Authenticating...";
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');

            try {
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    // Redirect sesuai peran/status dari backend
                    window.location.href = result.redirect;
                } else {
                    // Tampilkan pesan error di atas form
                    const errorDiv = document.createElement('div');
                    errorDiv.id = 'error-message';
                    errorDiv.className = 'text-xs bg-red-50 border border-red-200 text-red-600 px-4 py-2.5 rounded-xl font-medium mb-4 text-center animate-pulse';
                    errorDiv.innerText = result.message || "Gagal melakukan login.";
                    form.insertBefore(errorDiv, form.firstChild);
                }
            } catch (error) {
                alert("Terjadi kesalahan jaringan.");
            } finally {
                // Kembalikan tombol ke semula
                btnText.innerText = "Sign In";
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        });
    </script>
</body>
</html>