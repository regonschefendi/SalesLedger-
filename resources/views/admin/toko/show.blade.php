<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Detail Toko - Sales Ledger</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>
        body { font-family: 'Inter', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative pb-24 shadow-2xl overflow-hidden">
        
        <div class="px-6 pt-12 pb-4 flex items-center bg-white z-10 relative">
            <button onclick="window.history.back()" class="text-[#0F47A1] p-2 -ml-2 hover:text-blue-900 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <h1 class="text-[18px] font-bold text-gray-900 flex-grow text-center pr-8">Detail Toko</h1>
        </div>

        <div class="px-5 pt-2 flex-grow overflow-y-auto no-scrollbar pb-10">
            
            <div class="bg-gradient-to-br from-[#EBF1FF] to-white border border-blue-50 rounded-2xl p-5 shadow-sm relative overflow-hidden mb-6">
                <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-blue-100 rounded-full opacity-50"></div>
                
                <div class="flex items-center space-x-4 relative z-10">
                    <div class="w-[70px] h-[70px] bg-white rounded-xl shadow-sm border border-gray-100 flex items-center justify-center text-[#2563EB] flex-shrink-0">
                        <svg class="w-9 h-9" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4v2h16V4zm1 10v-2l-1-5H4l-1 5v2h1v6h10v-6h4v6h2v-6h1zm-9 4H6v-4h6v4z"></path></svg>
                    </div>
                    <div class="space-y-1.5">
                        <h2 class="text-[18px] font-bold text-gray-900 leading-tight">{{ $toko->nama_toko }}</h2>
                        <div class="flex items-center space-x-2 text-[12px] font-medium text-gray-700">
                            <svg class="w-3.5 h-3.5 text-[#2563EB]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.72l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.72.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span>{{ $toko->no_telp ?? '-' }}</span>
                        </div>
                        <div class="flex items-start space-x-2 text-[12px] font-medium text-gray-700">
                            <svg class="w-3.5 h-3.5 text-[#2563EB] mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"></path></svg>
                            <span class="leading-tight">{{ $toko->alamat ?? '-' }}</span>
                        </div>
                        <div class="flex items-center space-x-2 text-[12px] font-medium text-gray-700 pt-0.5">
                            <svg class="w-3.5 h-3.5 text-[#2563EB]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></svg>
                            <span>Sales: <span class="font-bold">{{ $toko->sales_name ?? '-' }}</span></span>
                        </div>
                    </div>
                </div>
            </div>

            <h3 class="text-[16px] font-bold text-gray-900 mb-3">Ringkasan Tagihan</h3>
            <div class="grid grid-cols-2 gap-3 mb-6">
                <div class="border border-gray-100 rounded-2xl p-4 bg-white shadow-sm flex flex-col justify-center space-y-2">
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 bg-blue-50 text-[#2563EB] rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"></path></svg>
                        </div>
                        <p class="text-[11px] font-bold text-gray-500">Total Nota</p>
                    </div>
                    <p class="text-[14px] font-bold text-gray-900">{{ $stats['total_nota'] ?? 0 }} Nota</p>
                </div>
                
                <div class="border border-gray-100 rounded-2xl p-4 bg-white shadow-sm flex flex-col justify-center space-y-2">
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 bg-blue-50 text-[#2563EB] rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M21 18v1c0 1.1-.9 2-2 2H5c-1.11 0-2-.9-2-2V5c0-1.1.89-2 2-2h14c1.1 0 2 .9 2 2v1h-9c-1.11 0-2 .9-2 2v8c0 1.1.89 2 2 2h9zm-9-2h10V8H12v8zm4-2.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"></path></svg>
                        </div>
                        <p class="text-[11px] font-bold text-gray-500">Total Tagihan</p>
                    </div>
                    <p class="text-[14px] font-bold text-gray-900">Rp{{ number_format($stats['total_tagihan'] ?? 0, 0, ',', '.') }}</p>
                </div>
                
                <div class="border border-gray-100 rounded-2xl p-4 bg-white shadow-sm flex flex-col justify-center space-y-2">
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 bg-[#ECFDF5] text-[#10B981] rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M21 18v1c0 1.1-.9 2-2 2H5c-1.11 0-2-.9-2-2V5c0-1.1.89-2 2-2h14c1.1 0 2 .9 2 2v1h-9c-1.11 0-2 .9-2 2v8c0 1.1.89 2 2 2h9zm-9-2h10V8H12v8zm4-2.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"></path></svg>
                        </div>
                        <p class="text-[11px] font-bold text-gray-500">Sudah Dibayar</p>
                    </div>
                    <p class="text-[14px] font-bold text-gray-900">Rp{{ number_format($stats['sudah_dibayar'] ?? 0, 0, ',', '.') }}</p>
                </div>
                
                <div class="border border-gray-100 rounded-2xl p-4 bg-white shadow-sm flex flex-col justify-center space-y-2">
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 bg-[#FFF7ED] text-[#F59E0B] rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="text-[11px] font-bold text-gray-500">Sisa Tagihan</p>
                    </div>
                    <p class="text-[14px] font-bold text-gray-900">Rp{{ number_format($stats['sisa_tagihan'] ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>

            <h3 class="text-[16px] font-bold text-gray-900 mb-3">Riwayat Nota</h3>
            <div class="space-y-3">
                @forelse($toko->fakturs as $faktur)
                <a href="{{ route('admin.nota.show', $faktur->id) }}" class="flex items-center border border-gray-100 rounded-2xl p-4 bg-white shadow-sm hover:shadow-md transition">
                    <div class="w-10 h-10 {{ $faktur->status == 'lunas' ? 'bg-[#ECFDF5] text-[#10B981]' : 'bg-[#EBF1FF] text-[#2563EB]' }} rounded-lg flex items-center justify-center flex-shrink-0 mr-4">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"></path></svg>
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-[14px] font-bold text-gray-900">{{ $faktur->nomor_faktur }}</h3>
                        <p class="text-[11px] text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($faktur->tanggal_nota)->translatedFormat('d M Y') }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-md {{ $faktur->status == 'lunas' ? 'bg-[#ECFDF5] text-[#10B981]' : 'bg-[#FFF7ED] text-[#F59E0B]' }}">
                            {{ $faktur->status == 'lunas' ? 'Lunas' : 'Belum Lunas' }}
                        </span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </a>
                @empty
                <div class="text-center py-6 text-gray-400 text-[12px]">Belum ada riwayat nota.</div>
                @endforelse
            </div>

        </div>

        <div class="fixed bottom-0 max-w-md mx-auto inset-x-0 bg-white border-t border-gray-100 p-5 z-20 shadow-[0_-4px_15px_rgba(0,0,0,0.03)]">
            <button onclick="window.history.back()" class="w-full bg-[#2563EB] hover:bg-blue-800 text-white font-bold py-3.5 rounded-xl shadow-md transition duration-200 text-[14px]">Kembali</button>
        </div>
    </div>
</body>
</html>