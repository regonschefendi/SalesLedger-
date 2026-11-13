<!DOCTYPE html>
<html lang="id">
<head>
    <x-head title="Ubah Kata Sandi" />
</head>
<body class="bg-gray-50 text-gray-800 antialiased">
    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative pb-10 shadow-2xl overflow-hidden">
        
        <!-- HEADER -->
        @php
            $profileUrl = Auth::user()->role === 'admin'
                ? route('admin.profile.index')
                : route('sales.profile.index');
        @endphp
        <x-back-header title="Ubah Kata Sandi" :back-url="$profileUrl" />

        <div class="px-5 pt-6 flex-grow overflow-y-auto">
            
            <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label class="block text-[13px] font-bold text-gray-900 mb-2">Masukkan Kata Sandi Lama</label>
                    <div class="relative flex items-center">
                        <input type="password" id="current_password" name="current_password" required placeholder="Masukkan Kata Sandi Awal"
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
                    <label class="block text-[13px] font-bold text-gray-900 mb-2">Masukkan Kata Sandi Baru</label>
                    <div class="relative flex items-center">
                        <input type="password" id="password" name="password" required placeholder="Kata Sandi Baru"
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
                    <label class="block text-[13px] font-bold text-gray-900 mb-2">Konfirmasi Kata Sandi</label>
                    <div class="relative flex items-center">
                        <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Konfirmasi Kata Sandi Baru"
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