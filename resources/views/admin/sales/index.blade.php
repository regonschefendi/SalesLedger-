<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Daftar Sales - Sales Ledger</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>body { font-family: 'Inter', sans-serif; } .no-scrollbar::-webkit-scrollbar { display: none; }</style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative pb-28 shadow-2xl overflow-hidden">
        
        <div class="px-5 pt-12 pb-4 bg-white z-10">
            <h1 class="text-[22px] font-bold text-gray-900">Sales</h1>
            <p class="text-[13px] text-gray-500 mt-0.5">Pantau data dan aktivitas sales</p>
        </div>

        <div class="px-5 space-y-4 flex-grow overflow-y-auto no-scrollbar pb-10">
            
            <form action="{{ route('admin.sales.index') }}" method="GET" class="relative flex items-center">
                <svg class="w-5 h-5 absolute left-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama sales..." class="w-full border border-gray-200 rounded-full pl-12 pr-4 py-3.5 text-[13px] focus:ring-2 focus:ring-[#2563EB] focus:outline-none">
            </form>

            <div class="border border-gray-100 rounded-2xl p-4 bg-white shadow-sm flex items-center space-x-3 mb-2">
                <div class="w-11 h-11 bg-blue-50 text-[#2563EB] rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"></path></svg>
                </div>
                <div>
                    <p class="text-[12px] font-bold text-gray-500 leading-tight">Total Sales</p>
                    <p class="text-[18px] font-bold text-gray-900 mt-0.5">{{ $totalSales }} Sales</p>
                </div>
            </div>

            <div class="space-y-3">
                @forelse($salesUsers as $s)
                <a href="{{ route('admin.sales.show', $s->id) }}" class="flex items-center border border-gray-100 rounded-2xl p-4 bg-white shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-[#EBF1FF] text-[#2563EB] rounded-full flex items-center justify-center flex-shrink-0 mr-4 border border-blue-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    
                    <div class="flex-grow">
                        <h3 class="text-[15px] font-bold text-gray-900">{{ $s->full_name }}</h3>
                        <div class="flex items-center mt-1 space-x-2">
                            <span class="text-[11px] text-gray-500 font-medium">{{ $s->total_toko }} toko pegangan</span>
                            <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded text-[#F59E0B] bg-[#FFF7ED]">{{ $s->belum_lunas_count }} Belum Lunas</span>
                        </div>
                    </div>
                    
                    <div class="pl-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </a>
                @empty
                <div class="text-center py-10 text-gray-400 text-[13px] font-medium">Belum ada data sales.</div>
                @endforelse
            </div>

        </div>

        @include('partials.admin-bottom-nav')
    </div>
</body>
</html>