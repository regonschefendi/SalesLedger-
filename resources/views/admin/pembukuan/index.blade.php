<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pembukuan - Sales Ledger</title>
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
        
        <div class="px-6 pt-12 pb-2 flex justify-between items-center bg-white">
            <div>
                <h1 class="text-[22px] font-bold text-gray-900">Pembukuan</h1>
                <p class="text-[12px] text-gray-500 mt-0.5">Semua laporan nota dari sales</p>
            </div>
            <button onclick="toggleBottomSheet(true)" class="w-10 h-10 bg-white rounded-full border border-gray-100 flex items-center justify-center text-[#2563EB] shadow-sm hover:bg-gray-50 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            </button>
        </div>

        <form id="filter-form" action="{{ route('admin.pembukuan.index') }}" method="GET" class="px-5 space-y-3 pt-2">
            <input type="hidden" name="time" id="param-time" value="{{ $filter_time }}">
            <input type="hidden" name="status" id="param-status" value="{{ $filter_status }}">
            <input type="hidden" name="start_date" id="param-start" value="{{ request('start_date') }}">
            <input type="hidden" name="end_date" id="param-end" value="{{ request('end_date') }}">

            <div class="relative">
                <svg class="w-5 h-5 absolute left-4 top-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" name="search" value="{{ request('search') }}" onchange="this.form.submit()" placeholder="Cari nama toko atau no. nota" 
                       class="w-full bg-white border border-gray-200 rounded-full pl-12 pr-4 py-3.5 text-[13px] focus:ring-2 focus:ring-[#2563EB] focus:outline-none transition">
            </div>

            <div class="flex space-x-2 overflow-x-auto no-scrollbar py-1">
                <button type="button" onclick="setChip('time', 'semua')" class="px-5 py-1.5 rounded-full text-[12px] font-medium whitespace-nowrap transition {{ $filter_time == 'semua' && $filter_status == 'semua' ? 'bg-[#2563EB] text-white shadow-sm font-bold' : 'bg-gray-100 text-gray-500' }}">Semua</button>
                <button type="button" onclick="setChip('time', 'hari_ini')" class="px-5 py-1.5 rounded-full text-[12px] font-medium whitespace-nowrap transition {{ $filter_time == 'hari_ini' ? 'bg-[#2563EB] text-white shadow-sm font-bold' : 'bg-gray-100 text-gray-500' }}">Hari Ini</button>
                <button type="button" onclick="setChip('time', 'minggu_ini')" class="px-5 py-1.5 rounded-full text-[12px] font-medium whitespace-nowrap transition {{ $filter_time == 'minggu_ini' ? 'bg-[#2563EB] text-white shadow-sm font-bold' : 'bg-gray-100 text-gray-500' }}">Minggu Ini</button>
                <button type="button" onclick="setChip('status', 'lunas')" class="px-5 py-1.5 rounded-full text-[12px] font-medium whitespace-nowrap transition {{ $filter_status == 'lunas' ? 'bg-[#2563EB] text-white shadow-sm font-bold' : 'bg-gray-100 text-gray-500' }}">Lunas</button>
                <button type="button" onclick="setChip('status', 'belum_lunas')" class="px-5 py-1.5 rounded-full text-[12px] font-medium whitespace-nowrap transition {{ $filter_status == 'belum_lunas' ? 'bg-[#2563EB] text-white shadow-sm font-bold' : 'bg-gray-100 text-gray-500' }}">Belum Lunas</button>
            </div>
        </form>

        <div class="px-5 space-y-4 flex-grow overflow-y-auto no-scrollbar pb-10 pt-2">
            
            <div class="border border-gray-100 rounded-2xl p-4 shadow-sm bg-white grid grid-cols-2 gap-4">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 font-bold">Total Tagihan</p>
                        <p class="text-[15px] font-bold text-gray-900">Rp{{ number_format($total_tagihan, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 border-l border-gray-100 pl-4">
                    <div class="w-9 h-9 bg-orange-50 rounded-xl flex items-center justify-center text-orange-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 font-bold">Sisa Tagihan</p>
                        <p class="text-[15px] font-bold text-gray-900">Rp{{ number_format($sisa_tagihan, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                @forelse($fakturs as $item)
                <a href="{{ route('admin.pembukuan.show', $item->id) }}" class="border border-gray-100 rounded-2xl p-4 shadow-sm bg-white flex justify-between items-center hover:bg-gray-50 transition duration-150">
                    <div class="flex items-center space-x-4">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 {{ $item->status == 'lunas' ? 'bg-emerald-50 text-emerald-500' : 'bg-blue-50 text-blue-500' }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4v2h16V4zm1 10v-2l-1-5H4l-1 5v2h1v6h10v-6h4v6h2v-6h1zm-9 4H6v-4h6v4z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-[14px] font-bold text-gray-900">{{ $item->nama_toko }}</h3>
                            <p class="text-[11px] text-gray-500 font-medium mt-0.5">Sales: {{ $item->user->full_name ?? 'Sales' }} • {{ $item->nomor_faktur }}</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">Bayar {{ \Carbon\Carbon::parse($item->tanggal_nota)->format('d M Y') }}</p>
                        </div>
                    </div>
                    <div class="text-right flex items-center space-x-3">
                        <div>
                            <p class="text-[14px] font-bold text-gray-900">Rp{{ number_format($item->total_tagihan, 0, ',', '.') }}</p>
                            <span class="inline-block text-[9px] font-bold px-2 py-0.5 rounded mt-1 {{ $item->status == 'lunas' ? 'bg-emerald-50 text-emerald-600' : 'bg-orange-50 text-orange-600' }}">
                                {{ $item->status == 'lunas' ? 'Lunas' : 'Belum Lunas' }}
                            </span>
                            @if($item->status !== 'lunas')
                                <p class="text-[10px] text-gray-400 font-medium mt-1">Sisa Rp{{ number_format(($item->total_tagihan - $item->total_dibayar), 0, ',', '.') }}</p>
                            @endif
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </a>
                @empty
                    <div class="text-center py-12 text-gray-400 text-xs">Belum ada data transaksi pembukuan.</div>
                @endforelse
            </div>

        </div>

        <div class="fixed bottom-0 max-w-md mx-auto inset-x-0 w-full bg-white border-t border-gray-100 h-[72px] flex justify-around items-center px-6 z-30 rounded-t-[24px] shadow-[0_-4px_20px_rgba(0,0,0,0.04)]">
            <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center text-gray-400 w-16">
                <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"></path></svg><span class="text-[10px] font-bold">Home</span>
            </a>
            <a href="{{ route('admin.pembukuan.index') }}" class="flex flex-col items-center text-[#2563EB] w-16">
                <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"></path></svg><span class="text-[10px] font-bold">Pembukuan</span>
            </a>
            <a href="#" class="flex flex-col items-center text-gray-400 w-16"><svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"></path></svg><span class="text-[10px] font-bold">Sales</span></a>
            <a href="#" class="flex flex-col items-center text-gray-400 w-16"><svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4v2h16V4zm1 10v-2l-1-5H4l-1 5v2h1v6h10v-6h4v6h2v-6h1zm-9 4H6v-4h6v4z"></path></svg><span class="text-[10px] font-bold">Toko</span></a>
        </div>
    </div>

    <div id="bottom-sheet" class="fixed inset-0 z-50 hidden items-end justify-center bg-black/40 backdrop-blur-xs animate-fadeIn">
        <div class="bg-white w-full max-w-md rounded-t-[30px] p-6 pb-8 space-y-6 transform transition-transform duration-300">
            <div class="w-12 h-1.5 bg-gray-200 rounded-full mx-auto mb-1" onclick="toggleBottomSheet(false)"></div>
            <h3 class="text-[16px] font-bold text-gray-900">Filter Pembukuan</h3>
            
            <div class="space-y-4">
                <p class="text-[13px] font-bold text-gray-800">Rentang Tanggal</p>
                <div class="space-y-3">
                    <div>
                        <label class="block text-[11px] text-gray-400 font-bold mb-1">Tanggal Mulai</label>
                        <input type="date" id="sheet-start" value="{{ request('start_date') }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-[13px] font-medium focus:outline-none focus:ring-1 focus:ring-[#2563EB]">
                    </div>
                    <div>
                        <label class="block text-[11px] text-gray-400 font-bold mb-1">Tanggal Akhir</label>
                        <input type="date" id="sheet-end" value="{{ request('end_date') }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-[13px] font-medium focus:outline-none focus:ring-1 focus:ring-[#2563EB]">
                    </div>
                </div>
                <p class="text-[11px] text-gray-400">Filter berdasarkan tanggal upload</p>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-2">
                <button type="button" onclick="resetFormFilter()" class="w-full border border-blue-600 text-blue-600 font-bold py-3.5 rounded-xl text-[13px] transition hover:bg-blue-50">Reset</button>
                <button type="button" onclick="submitFormFilter()" class="w-full bg-[#2563EB] text-white font-bold py-3.5 rounded-xl text-[13px] shadow-md hover:bg-blue-700">Terapkan</button>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('filter-form');

        function setChip(type, value) {
            if(type === 'time') {
                document.getElementById('param-time').value = value;
                document.getElementById('param-status').value = 'semua'; // reset crossover
            }
            if(type === 'status') {
                document.getElementById('param-status').value = value;
                document.getElementById('param-time').value = 'semua'; // reset crossover
            }
            form.submit();
        }

        function toggleBottomSheet(show) {
            const sheet = document.getElementById('bottom-sheet');
            if(show) { sheet.classList.remove('hidden'); sheet.classList.add('flex'); }
            else { sheet.classList.add('hidden'); sheet.classList.remove('flex'); }
        }

        function submitFormFilter() {
            document.getElementById('param-start').value = document.getElementById('sheet-start').value;
            document.getElementById('param-end').value = document.getElementById('sheet-end').value;
            form.submit();
        }

        function resetFormFilter() {
            document.getElementById('param-start').value = '';
            document.getElementById('param-end').value = '';
            document.getElementById('param-time').value = 'semua';
            document.getElementById('param-status').value = 'semua';
            form.submit();
        }
    </script>
</body>
</html>