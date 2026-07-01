<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Riwayat - Sales Ledger</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>body { font-family: 'Inter', sans-serif; } .no-scrollbar::-webkit-scrollbar { display: none; }</style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative pb-28 shadow-2xl overflow-hidden">
        
        <div class="px-6 pt-12 pb-4 text-center bg-white z-10">
            <h1 class="text-[20px] font-bold text-gray-900">Riwayat</h1>
        </div>

        <div class="px-5 space-y-4 flex-grow overflow-y-auto no-scrollbar pb-10">
            <form action="{{ route('sales.riwayat.index') }}" method="GET">
                <input type="hidden" name="status" value="{{ request('status', 'semua') }}">
                <div class="relative flex items-center">
                    <svg class="w-5 h-5 absolute left-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama toko atau no. faktur" class="w-full border border-gray-200 rounded-full pl-12 pr-4 py-3.5 text-[13px] focus:ring-2 focus:ring-[#0F47A1] focus:outline-none">
                </div>
            </form>

            <div class="flex space-x-2 pt-1 overflow-x-auto no-scrollbar">
                <a href="{{ route('sales.riwayat.index', ['status' => 'semua', 'q' => request('q')]) }}" class="{{ request('status', 'semua') == 'semua' ? 'bg-[#0F47A1] text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }} px-5 py-2 rounded-full text-[12px] font-semibold whitespace-nowrap transition">Semua</a>
                
                <a href="{{ route('sales.riwayat.index', ['status' => 'lunas', 'q' => request('q')]) }}" class="{{ request('status') == 'lunas' ? 'bg-[#0F47A1] text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }} px-5 py-2 rounded-full text-[12px] font-semibold whitespace-nowrap transition">Lunas</a>
                
                <a href="{{ route('sales.riwayat.index', ['status' => 'belum_lunas', 'q' => request('q')]) }}" class="{{ request('status') == 'belum_lunas' ? 'bg-[#0F47A1] text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }} px-5 py-2 rounded-full text-[12px] font-semibold whitespace-nowrap transition">Belum Lunas</a>
            </div>

            <div class="space-y-6 pt-2">
                @forelse($groupedFakturs as $date => $fakturs)
                    <div class="space-y-3">
                        <h2 class="text-[13px] font-bold text-gray-500">{{ $date }}</h2>
                        @foreach($fakturs as $faktur)
                            <a href="{{ route('sales.riwayat.show', $faktur->id) }}" class="flex items-center border border-gray-100 rounded-2xl p-4 bg-white shadow-sm hover:shadow-md transition">
                                <div class="w-11 h-11 {{ $faktur->status == 'lunas' ? 'bg-[#ECFDF5] text-[#10B981]' : 'bg-[#FFF7ED] text-[#F59E0B]' }} rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"></path></svg>
                                </div>
                                <div class="flex-grow border-r border-gray-100 pr-3">
                                    <h3 class="text-[14px] font-bold text-gray-900 truncate">{{ $faktur->toko->nama_toko ?? '-' }}</h3>
                                    <p class="text-[11px] text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($faktur->tanggal_nota)->translatedFormat('d M Y') }}</p>
                                </div>
                                <div class="pl-3 text-right">
                                    <p class="text-[10px] text-gray-500 font-medium mb-0.5">Total Tagihan</p>
                                    <p class="text-[13px] font-bold text-gray-900 mb-1">Rp{{ number_format($faktur->total_tagihan, 0, ',', '.') }}</p>
                                    <span class="text-[9px] font-bold px-2 py-0.5 rounded {{ $faktur->status == 'lunas' ? 'bg-[#ECFDF5] text-[#10B981]' : 'bg-[#FFF7ED] text-[#F59E0B]' }}">
                                        {{ $faktur->status == 'lunas' ? 'Lunas' : 'Belum Lunas' }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-400 text-sm">Tidak ada riwayat nota ditemukan.</div>
                @endforelse
            </div>

        </div>

        @include('partials.sales-bottom-nav')
    </div>
</body>
</html>