<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Password Berhasil Diganti</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">
    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative pb-10 shadow-2xl overflow-hidden pt-8">
        
        <!-- HEADER -->
        <div class="px-6 pb-4 flex items-center bg-white z-10 relative">
            <h1 class="text-[18px] font-bold text-gray-900 flex-grow text-center">Ubah Password</h1>
        </div>

        {{-- <div class="flex flex-col items-center justify-center flex-grow px-5 pb-20">
            <div class="relative w-24 h-24 mb-4 flex-shrink-0">
                <div class="absolute inset-0 bg-[#ECFDF5] rounded-full animate-ping opacity-75"></div>
                <div class="relative w-full h-full bg-[#0F47A1] rounded-full flex items-center justify-center text-white shadow-lg border-[6px] border-[#ECFDF5]">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="3.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                </div>
            </div>
            
            <h2 class="text-[18px] font-bold text-gray-900 mb-1">Password Berhasil Diganti</h2>
            <p class="text-[13px] text-gray-500 text-center">Password anda berhasil diganti</p>
        </div> --}}

        <div class="flex flex-col items-center justify-center flex-grow px-5 pb-20">
            
            <div class="relative mb-4 aspect-square shrink-0" style="width: 96px; height: 96px; min-width: 96px; min-height: 96px;">
                <div class="absolute inset-0 bg-[#ECFDF5] rounded-full animate-ping opacity-75"></div>
                <div class="relative w-full h-full bg-[#0F47A1] rounded-full flex items-center justify-center text-white shadow-lg border-[6px] border-[#ECFDF5] aspect-square">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="3.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                </div>
            </div>
            
            <h2 class="text-[18px] font-bold text-gray-900 mb-1">Password Berhasil Diganti</h2>
            <p class="text-[13px] text-gray-500 text-center">Password anda berhasil diganti</p>
        </div>

        <div class="px-5 w-full mt-auto mb-10">
            @php
                // Logika Pintar: Ngecek role user buat nentuin tombol Selesai balik ke mana
                $redirectUrl = Auth::user()->role === 'admin' ? route('admin.home') : route('sales.profile.index');
            @endphp
            <button onclick="window.location.href='{{ $redirectUrl }}'" class="w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-bold py-4 rounded-full shadow-md transition duration-200 text-[14px]">Selesai</button>
        </div>

    </div>
</body>
</html>