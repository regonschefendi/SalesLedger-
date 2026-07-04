<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Detail Sales - Sales Ledger</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>body { font-family: 'Inter', sans-serif; } .no-scrollbar::-webkit-scrollbar { display: none; }</style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative pb-24 shadow-2xl overflow-hidden">
        
        <div class="px-6 pt-12 pb-4 flex items-center bg-white z-10 relative">
            <button onclick="window.history.back()" class="text-[#0F47A1] p-2 -ml-2 hover:text-blue-900 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <h1 class="text-[18px] font-bold text-gray-900 flex-grow text-center pr-8">Detail Sales</h1>
        </div>

        <div class="px-5 pt-2 flex-grow overflow-y-auto no-scrollbar pb-10">
            
            <div class="bg-gradient-to-br from-[#EBF1FF] to-white border border-blue-50 rounded-2xl p-5 shadow-sm relative overflow-hidden mb-6">
                <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-blue-100 rounded-full opacity-50"></div>
                
                <div class="flex items-center space-x-4 relative z-10">
                    <div class="w-16 h-16 bg-[#0F47A1] rounded-full shadow-sm flex items-center justify-center text-white flex-shrink-0">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div class="space-y-1">
                        <h2 class="text-[20px] font-bold text-gray-900 leading-tight">{{ $sales->full_name }}</h2>
                        <p class="text-[12px] font-medium text-gray-500">Sales</p>
                        @if($sales->no_telp)
                        <div class="flex items-center space-x-1.5 text-[12px] font-semibold text-[#0F47A1] pt-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.72l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.72.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span>{{ $sales->no_telp }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-6">
                <div class="border border-gray-100 rounded-2xl p-4 bg-white shadow-sm flex flex-col justify-center space-y-2">
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 bg-blue-50 text-[#2563EB] rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4v2h16V4zm1 10v-2l-1-5H4l-1 5v2h1v6h10v-6h4v6h2v-6h1zm-9 4H6v-4h6v4z"></path></svg>
                        </div>
                        <p class="text-[11px] font-bold text-gray-500">Toko Pegangan</p>
                    </div>
                    <p class="text-[14px] font-bold text-gray-900">{{ $stats['total_toko'] }} Toko</p>
                </div>
                
                <div class="border border-gray-100 rounded-2xl p-4 bg-white shadow-sm flex flex-col justify-center space-y-2">
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 bg-blue-50 text-[#2563EB] rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"></path></svg>
                        </div>
                        <p class="text-[11px] font-bold text-gray-500">Nota Masuk</p>
                    </div>
                    <p class="text-[14px] font-bold text-gray-900">{{ $stats['total_nota'] }} Nota</p>
                </div>

                <div class="border border-gray-100 rounded-2xl p-4 bg-white shadow-sm flex flex-col justify-center space-y-2">
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 bg-[#ECFDF5] text-[#10B981] rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        </div>
                        <p class="text-[11px] font-bold text-gray-500">Total Tagihan</p>
                    </div>
                    <p class="text-[14px] font-bold text-gray-900">Rp{{ number_format($stats['total_tagihan'], 0, ',', '.') }}</p>
                </div>

                <div class="border border-gray-100 rounded-2xl p-4 bg-white shadow-sm flex flex-col justify-center space-y-2">
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 bg-[#FFF7ED] text-[#F59E0B] rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="text-[11px] font-bold text-gray-500">Sisa Tagihan</p>
                    </div>
                    <p class="text-[14px] font-bold text-gray-900">Rp{{ number_format($stats['sisa_tagihan'], 0, ',', '.') }}</p>
                </div>
            </div>

            <h3 class="text-[16px] font-bold text-gray-900 mb-3">Status Pembayaran</h3>
            <div class="grid grid-cols-2 gap-3 mb-6">
                <div class="bg-[#ECFDF5] border border-[#10B981]/20 rounded-2xl p-4 shadow-sm flex items-center space-x-3">
                    <div class="w-10 h-10 bg-[#10B981] text-white rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-gray-600 leading-tight">Lunas</p>
                        <p class="text-[15px] font-bold text-gray-900">{{ $stats['lunas_count'] }} Nota</p>
                    </div>
                </div>
                <div class="bg-[#FFF7ED] border border-[#F59E0B]/20 rounded-2xl p-4 shadow-sm flex items-center space-x-3">
                    <div class="w-10 h-10 bg-[#F59E0B] text-white rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-[16px] font-bold">!</span>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-gray-600 leading-tight">Belum Lunas</p>
                        <p class="text-[15px] font-bold text-gray-900">{{ $stats['belum_lunas_count'] }} Nota</p>
                    </div>
                </div>
            </div>

            <h3 class="text-[16px] font-bold text-gray-900 mb-3">Toko Pegangan</h3>
            <div class="space-y-3">
                @forelse($tokoPegangan as $toko)
                <a href="{{ $toko->id ? route('admin.toko.show', $toko->id) : '#' }}" class="flex items-center border border-gray-100 rounded-2xl p-4 bg-white shadow-sm hover:shadow-md transition">
                    <div class="w-11 h-11 bg-blue-50 text-[#2563EB] rounded-xl flex items-center justify-center flex-shrink-0 mr-4">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M21 7V5c0-1.103-.897-2-2-2H5c-1.103 0-2 .897-2 2v2a3 3 0 0 0 1.488 2.585V19c0 1.103.897 2 2 2h11c1.103 0 2-.897 2-2V9.585A3 3 0 0 0 21 7zM5 5h14v2a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V5zm12.5 14H6.5V9.038A2.969 2.969 0 0 0 8 9.5a2.969 2.969 0 0 0 2-1.5 2.969 2.969 0 0 0 2 1.5 2.969 2.969 0 0 0 2-1.5 2.969 2.969 0 0 0 2 1.5c.571 0 1.102-.178 1.5-.462V19z"></path></svg>
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-[14px] font-bold text-gray-900">{{ $toko->nama_toko }}</h3>
                        <p class="text-[11px] text-gray-500 font-medium mt-0.5">{{ $toko->total_nota }} nota diproses</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $toko->status == 'lunas' ? 'text-[#10B981] bg-[#ECFDF5]' : 'text-[#F59E0B] bg-[#FFF7ED]' }}">
                            {{ $toko->status == 'lunas' ? 'Lunas' : 'Belum Lunas' }}
                        </span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </a>
                @empty
                <div class="text-center py-6 text-gray-400 text-[12px] font-medium border border-dashed border-gray-200 rounded-xl">Belum ada toko yang dipegang.</div>
                @endforelse
            </div>

        </div>

        <div class="fixed bottom-0 max-w-md mx-auto inset-x-0 bg-white border-t border-gray-100 p-5 z-20 shadow-[0_-4px_15px_rgba(0,0,0,0.03)]">
            <button onclick="window.history.back()" class="w-full bg-[#2563EB] hover:bg-blue-800 text-white font-bold py-3.5 rounded-xl shadow-md transition duration-200 text-[14px]">Kembali</button>
        </div>
    </div>
</body>
</html>