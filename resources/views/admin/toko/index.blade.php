<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Daftar Toko - Sales Ledger</title>
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
        
        <div class="px-5 pt-12 pb-4 flex justify-between items-center bg-white z-10">
            <div>
                <h1 class="text-[22px] font-bold text-gray-900">Toko</h1>
                <p class="text-[13px] text-gray-500 mt-0.5">Daftar toko pelanggan</p>
            </div>
            <a href="{{ route('admin.toko.create') }}" class="w-11 h-11 bg-white rounded-full border border-gray-100 flex items-center justify-center text-gray-900 shadow-sm hover:bg-gray-50 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
            </a>
        </div>

        <div class="px-5 space-y-4 flex-grow overflow-y-auto no-scrollbar pb-10">
            
            <div class="relative flex items-center">
                <svg class="w-5 h-5 absolute left-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" placeholder="Cari nama toko atau no. nota" class="w-full border border-gray-200 rounded-full pl-12 pr-4 py-3.5 text-[13px] focus:ring-2 focus:ring-[#2563EB] focus:outline-none">
            </div>

            <div class="flex space-x-2 pt-1">
                <button class="bg-[#2563EB] text-white px-5 py-2 rounded-full text-[12px] font-semibold">Semua</button>
                <button class="bg-white border border-gray-200 text-gray-600 px-5 py-2 rounded-full text-[12px] font-medium hover:bg-gray-50">Lunas</button>
                <button class="bg-white border border-gray-200 text-gray-600 px-5 py-2 rounded-full text-[12px] font-medium hover:bg-gray-50">Belum Lunas</button>
            </div>

            <div class="grid grid-cols-2 gap-3 py-2">
                <div class="border border-gray-100 rounded-2xl p-4 bg-white shadow-sm flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-50 text-[#2563EB] rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4v2h16V4zm1 10v-2l-1-5H4l-1 5v2h1v6h10v-6h4v6h2v-6h1zm-9 4H6v-4h6v4z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-gray-900 leading-tight">Total Toko</p>
                        <p class="text-[16px] font-bold text-gray-900 mt-0.5">{{ $stats['total'] ?? 0 }} Toko</p>
                    </div>
                </div>
                <div class="border border-gray-100 rounded-2xl p-4 bg-white shadow-sm flex items-center space-x-3">
                    <div class="w-10 h-10 bg-orange-50 text-[#F59E0B] rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-gray-900 leading-tight">Belum Lunas</p>
                        <p class="text-[16px] font-bold text-gray-900 mt-0.5">{{ $stats['belum_lunas'] ?? 0 }} Toko</p>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                @forelse($tokos as $toko)
                <a href="{{ route('admin.toko.show', $toko->id) }}" class="flex items-center border border-gray-100 rounded-2xl p-4 bg-white shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 {{ $toko->status_pembayaran == 'lunas' ? 'bg-[#ECFDF5] text-[#10B981]' : 'bg-[#EBF1FF] text-[#2563EB]' }} rounded-xl flex items-center justify-center flex-shrink-0 mr-4">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4v2h16V4zm1 10v-2l-1-5H4l-1 5v2h1v6h10v-6h4v6h2v-6h1zm-9 4H6v-4h6v4z"></path></svg>
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-[14px] font-bold text-gray-900">{{ $toko->nama_toko }}</h3>
                        <p class="text-[11px] text-gray-500 mt-0.5">Sales: {{ $toko->sales_name ?? 'Belum ada' }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-md {{ $toko->status_pembayaran == 'lunas' ? 'bg-[#ECFDF5] text-[#10B981]' : 'bg-[#FFF7ED] text-[#F59E0B]' }}">
                            {{ $toko->status_pembayaran == 'lunas' ? 'Lunas' : 'Belum Lunas' }}
                        </span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </a>
                @empty
                <div class="text-center py-10 text-gray-400 text-sm">Belum ada data toko.</div>
                @endforelse
            </div>

        </div>

        @include('partials.admin-bottom-nav')
    </div>
</body>
</html>