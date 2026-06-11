<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Profile Sales - Sales Ledger</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative shadow-2xl overflow-hidden pb-10">
        
        <div class="px-6 pt-12 pb-4 flex items-center bg-white border-b border-gray-50">
            <button onclick="window.location.href='{{ route('sales.home') }}'" class="text-[#0F47A1] hover:text-blue-900 transition p-2 -ml-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <h1 class="text-[18px] font-bold text-gray-900 flex-grow text-center pr-8">Profile</h1>
        </div>

        <div class="px-5 pt-6 space-y-6 flex-grow overflow-y-auto no-scrollbar">
            
            <div class="border border-gray-100 rounded-2xl p-5 shadow-sm bg-[#EBF3FF]/60 flex items-center justify-between relative">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-white border border-blue-100 rounded-full flex items-center justify-center text-[#0F47A1] shadow-inner flex-shrink-0">
                        <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-[18px] font-bold text-gray-900 leading-tight">{{ $user->full_name }}</h2>
                        <p class="text-[13px] text-gray-400 font-medium capitalize mt-0.5">{{ $user->role }}</p>
                        <div class="flex items-center space-x-1 mt-1 text-gray-600">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.72l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.72.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span class="text-[12px] font-medium">{{ $user->phone_number ?? 'Belum ada nomor' }}</span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('sales.profile.edit') }}" class="absolute top-4 right-4 bg-white border border-gray-200 rounded-lg px-2.5 py-1 text-[11px] font-bold text-[#0F47A1] shadow-xs hover:bg-gray-50 transition">
                    Edit Profil
                </a>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="border border-gray-50 rounded-2xl p-4 bg-white shadow-sm">
                    <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-10V4m-5 10v.01M15 7h1m-1 4h1m-5 10v.01M11 17v.01M13 17v.01"></path></svg>
                    </div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Toko Pegangan</p>
                    <p class="text-[16px] font-bold text-gray-900 mt-0.5">{{ $stats['total_toko'] }} Toko</p>
                </div>
                <div class="border border-gray-50 rounded-2xl p-4 bg-white shadow-sm">
                    <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Laporan Terkirim</p>
                    <p class="text-[16px] font-bold text-gray-900 mt-0.5">{{ $stats['total_nota'] }} Nota</p>
                </div>
                <div class="border border-gray-50 rounded-2xl p-4 bg-white shadow-sm">
                    <div class="w-8 h-8 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Total Tagihan</p>
                    <p class="text-[16px] font-bold text-emerald-600 mt-0.5">Rp{{ number_format($stats['total_tagihan'], 0, ',', '.') }}</p>
                </div>
                <div class="border border-gray-50 rounded-2xl p-4 bg-white shadow-sm">
                    <div class="w-8 h-8 bg-orange-50 text-orange-600 rounded-lg flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase">Belum Lunas</p>
                    <p class="text-[16px] font-bold text-orange-500 mt-0.5">{{ $stats['belum_lunas'] }} Nota</p>
                </div>
            </div>

            <div class="space-y-3 pt-2">
                <div class="border border-gray-100 rounded-2xl bg-white shadow-sm overflow-hidden divide-y divide-gray-50">
                    <a href="#" class="flex justify-between items-center p-4 hover:bg-slate-50 transition">
                        <div class="flex items-center space-x-4">
                            <div class="w-9 h-9 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4v2h16V4zm1 10v-2l-1-5H4l-1 5v2h1v6h10v-6h4v6h2v-6h1zm-9 4H6v-4h6v4z"></path></svg>
                            </div>
                            <span class="text-[14px] font-bold text-gray-800">Toko Pegangan</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                    <a href="#" class="flex justify-between items-center p-4 hover:bg-slate-50 transition">
                        <div class="flex items-center space-x-4">
                            <div class="w-9 h-9 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <span class="text-[14px] font-bold text-gray-800">Ubah Password</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="w-full border border-gray-100 rounded-2xl p-4 shadow-sm bg-white hover:bg-red-50/40 transition text-left flex items-center space-x-4">
                    <div class="w-9 h-9 bg-red-50 text-red-500 rounded-xl flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </div>
                    <span class="text-[14px] font-bold text-red-500">Keluar</span>
                </button>
            </div>

        </div>
    </div>

</body>
</html>