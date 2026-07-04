<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Ubah Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">
    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative pb-10 shadow-2xl overflow-hidden">
        
        <!-- HEADER -->
        <div class="px-6 pt-12 pb-4 flex items-center bg-white z-10 relative">
            <button onclick="window.history.back()" class="text-[#0F47A1] p-2 -ml-2 hover:text-blue-900 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <h1 class="text-[18px] font-bold text-gray-900 flex-grow text-center pr-8">Ubah Password</h1>
        </div>

        <div class="px-5 pt-6 flex-grow overflow-y-auto">
            
            <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label class="block text-[13px] font-bold text-gray-900 mb-2">Masukkan Password Lama</label>
                    <div class="relative flex items-center">
                        <input type="password" id="current_password" name="current_password" required placeholder="Masukkan Password Awal" 
                            class="w-full border {{ $errors->has('current_password') ? 'border-red-400' : 'border-gray-200' }} rounded-full px-4 py-3.5 text-[13px] font-medium focus:ring-2 focus:ring-[#0F47A1] focus:outline-none placeholder-gray-400">
                        <button type="button" onclick="togglePasswordVisibility('current_password')" class="absolute right-4 text-gray-400 hover:text-gray-600 focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="text-red-500 text-[11px] font-medium mt-1.5 ml-4">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-[13px] font-bold text-gray-900 mb-2">Masukkan Password Baru</label>
                    <div class="relative flex items-center">
                        <input type="password" id="password" name="password" required placeholder="Password Baru" 
                            class="w-full border {{ $errors->has('password') ? 'border-red-400' : 'border-gray-200' }} rounded-full px-4 py-3.5 text-[13px] font-medium focus:ring-2 focus:ring-[#0F47A1] focus:outline-none placeholder-gray-400">
                        <button type="button" onclick="togglePasswordVisibility('password')" class="absolute right-4 text-gray-400 hover:text-gray-600 focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-[11px] font-medium mt-1.5 ml-4">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-[13px] font-bold text-gray-900 mb-2">Konfirmasi Password</label>
                    <div class="relative flex items-center">
                        <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Konfirmasi Password Baru" 
                            class="w-full border border-gray-200 rounded-full px-4 py-3.5 text-[13px] font-medium focus:ring-2 focus:ring-[#0F47A1] focus:outline-none placeholder-gray-400">
                        <button type="button" onclick="togglePasswordVisibility('password_confirmation')" class="absolute right-4 text-gray-400 hover:text-gray-600 focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </button>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-bold py-4 rounded-full shadow-md transition duration-200 text-[14px]">Kirim</button>
                </div>
            </form>

        </div>
    </div>

    <script>
        function togglePasswordVisibility(inputId) {
            const input = document.getElementById(inputId);
            if (input) {
                input.type = input.type === 'password' ? 'text' : 'password';
            }
        }
    </script>
</body>
</html>