<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Sales Home - Sales Ledger</title>
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#ffffff">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
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
                <h1 class="text-[22px] font-bold text-gray-900">Halo, {{ $nama_sales }}</h1>
                <p class="text-[13px] text-gray-500 mt-1">Semangat bekerja hari ini !</p>
            </div>
            <div class="flex items-center space-x-3">
                <button class="w-10 h-10 bg-white rounded-full border border-gray-200 flex items-center justify-center text-gray-800 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </button>
                <a href="{{ route('sales.profile.index') }}" class="p-2 bg-white rounded-full border border-gray-100 shadow-sm">
                    <svg class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></svg>
                </a>
            </div>
        </div>

        <div class="px-5 space-y-4 flex-grow overflow-y-auto no-scrollbar pb-10">
            
            <div class="border border-gray-100 rounded-2xl p-5 shadow-sm bg-white">
                <h2 class="text-[14px] font-semibold text-gray-800 mb-4">Ringkasan Laporan Saya</h2>

                <div class="flex items-center mb-5">
                    <div class="w-12 h-12 bg-[#F0F5FF] rounded-full flex items-center justify-center mr-4 text-[#0F47A1]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[13px] text-gray-800 font-medium">Total Nota</p>
                        <p class="text-[18px] font-bold text-[#0F47A1]">{{ $total_nota }} Nota</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-5">
                    <div class="border border-gray-100 rounded-2xl p-3 shadow-sm bg-white flex flex-col justify-center">
                        <div class="flex items-center space-x-2 mb-2">
                            <div class="w-7 h-7 bg-[#ECFDF5] rounded-md flex items-center justify-center text-[#10B981]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <p class="text-[12px] text-gray-800 font-medium">Total Tagihan</p>
                        </div>
                        <p class="text-[15px] font-bold text-[#10B981]">Rp{{ number_format($total_tagihan, 0, ',', '.') }}</p>
                    </div>
                    <div class="border border-gray-100 rounded-2xl p-3 shadow-sm bg-white flex flex-col justify-center">
                        <div class="flex items-center space-x-2 mb-2">
                            <div class="w-7 h-7 bg-[#F0F5FF] rounded-md flex items-center justify-center text-[#0F47A1]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <p class="text-[12px] text-gray-800 font-medium">Total Dibayar</p>
                        </div>
                        <p class="text-[15px] font-bold text-[#0F47A1]">Rp{{ number_format($total_dibayar, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="border border-gray-100 rounded-2xl p-5 shadow-sm bg-white mt-4 flex items-center">
                    <div class="w-10 h-10 bg-[#FFF7ED] rounded-full flex items-center justify-center mr-4 text-[#F59E0B]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[13px] text-gray-800 font-medium">Sisa Kredit/ Piutang</p>
                        <p class="text-[16px] font-bold text-[#F59E0B]">Rp{{ number_format($sisa_kredit, 0, ',', '.') }}</p>
                    </div>
                </div>

            <div class="border border-gray-100 rounded-2xl p-5 shadow-sm bg-white mt-4">
                <h2 class="text-[14px] font-semibold text-gray-800 mb-4">Status Pembayaran</h2>
                <div class="grid grid-cols-2">
                    <div class="flex items-center">
                        <div class="w-9 h-9 bg-[#ECFDF5] rounded-full flex items-center justify-center text-[#10B981] mr-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <p class="text-[12px] font-semibold text-gray-800">Lunas</p>
                            <p class="text-[16px] text-gray-800">{{ $lunas }}</p>
                        </div>
                    </div>
                    <div class="flex items-center border-l border-gray-100 pl-4">
                        <div class="w-9 h-9 bg-[#FFF7ED] rounded-full flex items-center justify-center text-[#F59E0B] mr-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[12px] font-semibold text-gray-800">Belum Lunas</p>
                            <p class="text-[16px] text-gray-800">{{ $belum_lunas }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border border-gray-100 rounded-2xl p-5 shadow-sm bg-white mt-4">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-[14px] font-semibold text-gray-800">Riwayat Terbaru</h2>
                    <a href="#" class="text-[12px] font-semibold text-[#0F47A1]">Lihat Semua</a>
                </div>
                
                @if(count($riwayat) > 0)
                    <div class="space-y-4">
                        @foreach($riwayat as $item)
                        <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                            <div>
                                <p class="text-[13px] font-bold text-gray-800">{{ $item->toko->nama_toko ?? '-' }}</p>
                                <p class="text-[11px] text-gray-500">{{ date('d M Y', strtotime($item->created_at)) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[13px] font-bold text-[#10B981]">Rp{{ number_format($item->total_tagihan, 0, ',', '.') }}</p>
                                <p class="text-[10px] font-semibold {{ $item->status == 'lunas' ? 'text-green-500' : 'text-orange-500' }}">{{ strtoupper(str_replace('_', ' ', $item->status)) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-4 mb-2">
                        <svg class="w-16 h-16 text-gray-200 mb-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                        </svg>
                        <p class="text-[14px] font-bold text-gray-800 mb-1">Belum ada laporan nota</p>
                        <p class="text-[12px] text-gray-500 text-center px-4 leading-relaxed">
                            Kamu belum mengupload data tagihan.<br>Upload nota pertama kamu untuk mulai<br>mencatat data.
                        </p>
                    </div>
                @endif
            </div>

        </div>
    </div>

    @include('partials.sales-bottom-nav')

</body>
</html>