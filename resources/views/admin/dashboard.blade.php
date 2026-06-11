<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Admin Dashboard - Sales Ledger</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>
        body { font-family: 'Inter', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative pb-28 shadow-2xl overflow-hidden">
        
        <div class="px-6 pt-12 pb-4 flex justify-between items-center bg-white">
            <div>
                <h1 class="text-[22px] font-bold text-gray-900">Halo, {{ $nama_admin }}</h1>
                <p class="text-[13px] text-gray-500 mt-1">Pantau Laporan hari ini</p>
            </div>
            <div class="flex items-center space-x-3">
                <button class="w-10 h-10 bg-white rounded-full border border-gray-200 flex items-center justify-center text-gray-800 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </button>
                
                <a href="{{ route('admin.profile.index') }}" class="w-10 h-10 bg-white rounded-full border border-gray-200 flex items-center justify-center text-gray-800 shadow-sm hover:bg-gray-50 transition">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></svg>
                </a>
            </div>
        </div>

        <div class="px-6 flex space-x-2 mb-2">
            <a href="?filter=hari_ini" class="px-6 py-2 rounded-full text-[12px] font-bold transition {{ $filter_aktif == 'hari_ini' ? 'bg-[#2563EB] text-white shadow-sm' : 'bg-[#F3F4F6] text-gray-500 hover:bg-gray-200' }}">Hari Ini</a>
            <a href="?filter=bulan_ini" class="px-6 py-2 rounded-full text-[12px] font-bold transition {{ $filter_aktif == 'bulan_ini' ? 'bg-[#2563EB] text-white shadow-sm' : 'bg-[#F3F4F6] text-gray-500 hover:bg-gray-200' }}">Bulan Ini</a>
        </div>

        <div class="px-5 space-y-4 flex-grow overflow-y-auto no-scrollbar pb-10">
            
            <div class="pt-2">
                <h2 class="text-[14px] font-bold text-gray-900 mb-3 pl-1">Ringkasan</h2>

                <div class="border border-gray-100 rounded-2xl p-5 shadow-sm bg-[#F8FAFC] mb-3">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mr-4 text-[#2563EB] shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[13px] text-gray-700 font-semibold">Total Nota Masuk</p>
                            <p class="text-[20px] font-bold text-[#2563EB]">{{ $total_nota }} Nota</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div class="border border-gray-100 rounded-2xl p-4 shadow-sm bg-white">
                        <div class="flex items-center space-x-2 mb-2">
                            <div class="w-7 h-7 bg-[#ECFDF5] rounded-md flex items-center justify-center text-[#10B981]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <p class="text-[12px] text-gray-800 font-semibold">Total Tagihan</p>
                        </div>
                        <p class="text-[16px] font-bold text-gray-900">Rp{{ number_format($total_tagihan, 0, ',', '.') }}</p>
                    </div>
                    <div class="border border-gray-100 rounded-2xl p-4 shadow-sm bg-white">
                        <div class="flex items-center space-x-2 mb-2">
                            <div class="w-7 h-7 bg-[#EFF6FF] rounded-md flex items-center justify-center text-[#2563EB]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <p class="text-[12px] text-gray-800 font-semibold">Total Dibayar</p>
                        </div>
                        <p class="text-[16px] font-bold text-gray-900">Rp{{ number_format($total_dibayar, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="border border-gray-100 rounded-2xl p-4 shadow-sm bg-white flex items-center">
                    <div class="w-10 h-10 bg-[#FFF7ED] rounded-full flex items-center justify-center mr-4 text-[#F59E0B]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[12px] text-gray-800 font-semibold mb-0.5">Sisa Kredit/ Piutang</p>
                        <p class="text-[16px] font-bold text-gray-900">Rp{{ number_format($sisa_kredit, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="pt-2">
                <h2 class="text-[14px] font-bold text-gray-900 mb-3 pl-1">Status Pembayaran</h2>
                <div class="border border-gray-100 rounded-2xl p-4 shadow-sm bg-white">
                    <div class="grid grid-cols-2">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-[#ECFDF5] rounded-full flex items-center justify-center text-[#10B981] mr-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <p class="text-[12px] font-bold text-gray-700">Lunas</p>
                                <p class="text-[16px] font-bold text-gray-900">{{ $lunas }}</p>
                            </div>
                        </div>
                        <div class="flex items-center border-l border-gray-100 pl-4">
                            <div class="w-10 h-10 bg-[#FFF7ED] rounded-full flex items-center justify-center text-[#F59E0B] mr-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[12px] font-bold text-gray-700">Belum Lunas</p>
                                <p class="text-[16px] font-bold text-gray-900">{{ $belum_lunas }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border border-gray-100 rounded-2xl p-5 shadow-sm bg-white mt-4">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-[14px] font-bold text-gray-900">Riwayat Terbaru</h2>
                    <a href="#" class="text-[12px] font-bold text-[#2563EB]">Lihat Semua</a>
                </div>
                
                @if(count($riwayat) > 0)
                    <div class="space-y-4 pt-2">
                        @foreach($riwayat as $item)
                        <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                            <div>
                                <p class="text-[13px] font-bold text-gray-900">{{ $item->nama_toko }}</p>
                                <div class="flex items-center space-x-1 mt-0.5">
                                    <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></svg>
                                    <p class="text-[10px] text-gray-500 font-medium">{{ $item->user->full_name ?? 'Sales' }} • {{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[13px] font-bold text-[#10B981]">Rp{{ number_format($item->total_tagihan, 0, ',', '.') }}</p>
                                <p class="text-[10px] font-bold {{ $item->status == 'lunas' ? 'text-[#10B981]' : 'text-[#F59E0B]' }} mt-0.5">{{ strtoupper(str_replace('_', ' ', $item->status)) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center py-4 mb-2">
                        <svg class="w-16 h-16 text-gray-200 mb-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                        </svg>
                        <p class="text-[14px] font-bold text-gray-900 mb-1">Belum ada laporan nota</p>
                        <p class="text-[12px] text-gray-500 text-center px-4 leading-relaxed">
                            Kamu belum mengupload data tagihan.<br>Upload nota pertama kamu untuk mulai<br>mencatat data.
                        </p>
                    </div>
                @endif
            </div>

        </div>

        <div class="fixed bottom-0 max-w-md mx-auto inset-x-0 w-full bg-white border-t border-gray-100 h-[72px] flex justify-around items-center px-6 z-40 rounded-t-[24px] shadow-[0_-4px_20px_rgba(0,0,0,0.04)]">
            <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center text-[#2563EB] w-16">
                <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"></path></svg>
                <span class="text-[10px] font-bold">Home</span>
            </a>
            
            <a href="{{ route('admin.pembukuan.index') }}" class="flex flex-col items-center text-gray-400 hover:text-gray-700 w-16 transition">
                <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"></path></svg>
                <span class="text-[10px] font-bold">Pembukuan</span>
            </a>
            
            <a href="#" class="flex flex-col items-center text-gray-400 hover:text-gray-700 w-16 transition">
                <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"></path></svg>
                <span class="text-[10px] font-bold">Sales</span>
            </a>
            
            <a href="#" class="flex flex-col items-center text-gray-400 hover:text-gray-700 w-16 transition">
                <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4v2h16V4zm1 10v-2l-1-5H4l-1 5v2h1v6h10v-6h4v6h2v-6h1zm-9 4H6v-4h6v4z"></path></svg>
                <span class="text-[10px] font-bold">Toko</span>
            </a>
        </div>
    </div>

</body>
</html>