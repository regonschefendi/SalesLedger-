@use('App\Support\Format')
<!DOCTYPE html>
<html lang="id">
<head>
    <x-head title="Pembukuan - Sales Ledger" />
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    @if(session('error') || $errors->any())
        <div id="flash-toast" class="fixed top-5 left-1/2 -translate-x-1/2 z-[70] w-[calc(100%-2rem)] max-w-sm bg-red-50 border border-red-200 text-red-700 text-[12px] font-semibold px-4 py-3 rounded-xl shadow-lg flex items-start space-x-2">
            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path></svg>
            <span>{{ session('error') ?? $errors->first() }}</span>
        </div>
    @endif

    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative pb-24 shadow-2xl overflow-hidden">
        
        <div class="px-6 pt-12 pb-2 flex justify-between items-center bg-white">
            <div>
                <h1 class="text-[22px] font-bold text-gray-900">Pembukuan</h1>
                <p class="text-[12px] text-gray-500 mt-0.5">Semua laporan nota dari sales</p>
            </div>
            <div class="flex items-center space-x-2">
                <button type="button" onclick="toggleExportModal(true)" class="flex items-center space-x-1.5 bg-[#2563EB] text-white px-5 py-2.5 rounded-full text-[11px] font-bold shadow-sm hover:bg-blue-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>Ekspor Excel</span>
                </button>
                <button type="button" onclick="toggleBottomSheet(true)" class="w-10 h-10 bg-white rounded-full border border-gray-100 flex items-center justify-center text-[#2563EB] shadow-sm hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                </button>
            </div>
        </div>

        <form id="filter-form" action="{{ route('admin.pembukuan.index') }}" method="GET" class="px-5 space-y-3 pt-2">
            <input type="hidden" name="time" id="param-time" value="{{ $filter_time }}">
            <input type="hidden" name="status" id="param-status" value="{{ $filter_status }}">
            <input type="hidden" name="start_date" id="param-start" value="{{ request('start_date') }}">
            <input type="hidden" name="end_date" id="param-end" value="{{ request('end_date') }}">

            <x-search-input name="search" placeholder="Cari nama toko atau no. nota" submit-on-change />

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
                        <p class="text-[15px] font-bold text-gray-900">{{ Format::rupiah($total_tagihan) }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 border-l border-gray-100 pl-4">
                    <div class="w-9 h-9 bg-orange-50 rounded-xl flex items-center justify-center text-orange-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 font-bold">Sisa Tagihan</p>
                        <p class="text-[15px] font-bold text-gray-900">{{ Format::rupiah($sisa_tagihan) }}</p>
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
                            <h3 class="text-[14px] font-bold text-gray-900">{{ $item->toko_name ?? '-' }}</h3>
                            <p class="text-[11px] text-gray-500 font-medium mt-0.5">Sales: {{ $item->sales_name ?? 'Sales' }} &bull; {{ $item->nomor_faktur }}</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">Bayar {{ \Carbon\Carbon::parse($item->tanggal_nota)->format('d M Y') }}</p>
                        </div>
                    </div>
                    <div class="text-right flex items-center space-x-3">
                        <div>
                            <p class="text-[14px] font-bold text-gray-900">{{ Format::rupiah($item->total_tagihan) }}</p>
                            <span class="inline-block text-[9px] font-bold px-2 py-0.5 rounded mt-1 {{ $item->status == 'lunas' ? 'bg-emerald-50 text-emerald-600' : 'bg-orange-50 text-orange-600' }}">
                                {{ $item->status == 'lunas' ? 'Lunas' : 'Belum Lunas' }}
                            </span>
                            @if($item->status !== 'lunas')
                                <p class="text-[10px] text-gray-400 font-medium mt-1">Sisa {{ Format::rupiah(($item->total_tagihan - $item->total_dibayar)) }}</p>
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

        @include('partials.admin-bottom-nav')
    </div>

    <div id="bottom-sheet" class="fixed inset-0 z-50 hidden items-end justify-center bg-black/40 backdrop-blur-xs animate-fadeIn">
        <div class="bg-white w-full max-w-md rounded-t-[30px] p-6 pb-8 space-y-6 transform transition-transform duration-300">
            <div class="w-12 h-1.5 bg-gray-200 rounded-full mx-auto mb-1" onclick="toggleBottomSheet(false)"></div>
            <div class="flex items-center justify-between">
                <h3 class="text-[16px] font-bold text-gray-900">Penyaring Pembukuan</h3>
                <button type="button" onclick="toggleBottomSheet(false)" aria-label="Tutup penyaring"
                        class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 hover:text-gray-700 transition inline-flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
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
                <p class="text-[11px] text-gray-400">Saring berdasarkan tanggal unggah</p>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-2">
                <button type="button" onclick="resetFormFilter()" class="w-full border border-blue-600 text-blue-600 font-bold py-3.5 rounded-xl text-[13px] transition hover:bg-blue-50">Atur Ulang</button>
                <button type="button" onclick="submitFormFilter()" class="w-full bg-[#2563EB] text-white font-bold py-3.5 rounded-xl text-[13px] shadow-md hover:bg-blue-700">Terapkan</button>
            </div>
        </div>
    </div>

    <div id="export-modal" class="fixed inset-0 z-50 hidden items-end justify-center bg-black/40 backdrop-blur-xs">
        <div class="bg-white w-full max-w-md rounded-t-[30px] p-6 pb-8 space-y-6 transform transition-transform duration-300">
            <div class="w-12 h-1.5 bg-gray-200 rounded-full mx-auto mb-1" onclick="toggleExportModal(false)"></div>
            <div>
                <h3 class="text-[16px] font-bold text-gray-900">Ekspor Pembukuan ke Excel</h3>
                <p class="text-[11px] text-gray-400 mt-1">Pilih rentang tanggal faktur yang ingin diekspor.</p>
            </div>

            <form id="export-form" action="{{ route('admin.pembukuan.export') }}" method="GET" class="space-y-4">
                <div>
                    <label class="block text-[11px] text-gray-400 font-bold mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="export-start" required
                           value="{{ old('start_date', request('start_date', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d'))) }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-3 text-[13px] font-medium focus:outline-none focus:ring-1 focus:ring-[#2563EB]">
                </div>
                <div>
                    <label class="block text-[11px] text-gray-400 font-bold mb-1">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="export-end" required
                           value="{{ old('end_date', request('end_date', \Carbon\Carbon::now()->format('Y-m-d'))) }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-3 text-[13px] font-medium focus:outline-none focus:ring-1 focus:ring-[#2563EB]">
                </div>
                <p class="text-[11px] text-gray-400">File akan diunduh dalam format .xlsx</p>

                <div class="grid grid-cols-2 gap-4 pt-2">
                    <button type="button" onclick="toggleExportModal(false)" class="w-full border border-gray-300 text-gray-600 font-bold py-3.5 rounded-xl text-[13px] transition hover:bg-gray-50">Batal</button>
                    <button type="submit" class="w-full bg-[#2563EB] text-white font-bold py-3.5 rounded-xl text-[13px] shadow-md hover:bg-blue-700 flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>Ekspor</span>
                    </button>
                </div>
            </form>
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

        function toggleExportModal(show) {
            const modal = document.getElementById('export-modal');
            if(show) { modal.classList.remove('hidden'); modal.classList.add('flex'); }
            else { modal.classList.add('hidden'); modal.classList.remove('flex'); }
        }

        @if($errors->any())
            toggleExportModal(true);
        @endif

        const flashToast = document.getElementById('flash-toast');
        if (flashToast) {
            setTimeout(() => flashToast.remove(), 5000);
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