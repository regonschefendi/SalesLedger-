<!DOCTYPE html>
<html lang="en">
<head>
    <x-head title="Password Berhasil Diganti" />
</head>
<body class="bg-gray-50 text-gray-800 antialiased">
    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative pb-10 shadow-2xl overflow-hidden pt-8">
        
        <!-- HEADER -->
        <div class="px-6 pb-4 flex items-center bg-white z-10 relative">
            <h1 class="text-[18px] font-bold text-gray-900 flex-grow text-center">Ubah Password</h1>
        </div>

        <div class="flex flex-col items-center justify-center flex-grow px-5 pb-20">
            <div class="mb-4">
                <x-success-icon
                    size="w-24 h-24"
                    icon-size="w-10 h-10"
                    color="bg-[#0F47A1]"
                    border="border-[6px]"
                />
            </div>

            <h2 class="text-[18px] font-bold text-gray-900 mb-1">Password Berhasil Diganti</h2>
            <p class="text-[13px] text-gray-500 text-center">Password anda berhasil diganti</p>
        </div>

        <div class="px-5 w-full mt-auto mb-10">
            @php
                // Logika Pintar: Ngecek role user buat nentuin tombol Selesai balik ke mana
                $redirectUrl = Auth::user()->role === 'admin' ? route('admin.dashboard') : route('sales.profile.index');
            @endphp
            <button onclick="window.location.href='{{ $redirectUrl }}'" class="w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-bold py-4 rounded-full shadow-md transition duration-200 text-[14px]">Selesai</button>
        </div>

    </div>
</body>
</html>