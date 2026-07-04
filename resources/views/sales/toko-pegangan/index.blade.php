<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Toko Pegangan - Sales Ledger</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>body { font-family: 'Inter', sans-serif; } .no-scrollbar::-webkit-scrollbar { display: none; }</style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative pb-28 shadow-2xl overflow-hidden">
        
        <div class="px-6 pt-12 pb-4 flex items-center bg-white z-10 relative border-b border-gray-50">
            <button onclick="goBack()" class="text-[#0F47A1] p-2 -ml-2 hover:text-blue-900 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <h1 id="header-title" class="text-[18px] font-bold text-gray-900 flex-grow text-center pr-8">Toko Pegangan</h1>
        </div>

        <div class="px-5 pt-4 flex-grow overflow-y-auto no-scrollbar pb-10">

            <div id="view-assigned" class="space-y-3 block">
                @forelse($assignedTokos as $toko)
                    <div onclick="openDetail({{ $toko->id }}, 'assigned')" class="flex items-center border border-gray-100 rounded-xl p-4 bg-white shadow-sm cursor-pointer hover:bg-gray-50 transition">
                        <div class="w-10 h-10 bg-blue-50 text-[#0F47A1] rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M21 7V5c0-1.103-.897-2-2-2H5c-1.103 0-2 .897-2 2v2a3 3 0 0 0 1.488 2.585V19c0 1.103.897 2 2 2h11c1.103 0 2-.897 2-2V9.585A3 3 0 0 0 21 7zM5 5h14v2a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V5zm12.5 14H6.5V9.038A2.969 2.969 0 0 0 8 9.5a2.969 2.969 0 0 0 2-1.5 2.969 2.969 0 0 0 2 1.5 2.969 2.969 0 0 0 2-1.5 2.969 2.969 0 0 0 2 1.5c.571 0 1.102-.178 1.5-.462V19z"></path></svg>
                        </div>
                        <h3 class="text-[14px] font-bold text-gray-900">{{ $toko->nama_toko }}</h3>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-400 text-[13px]">Belum ada toko yang dipilih.</div>
                @endforelse
            </div>

            <form id="view-select" action="{{ route('sales.toko-pegangan.sync') }}" method="POST" class="space-y-3 hidden">
                @csrf
                <p class="text-[14px] font-bold text-gray-900 mb-4">Daftar Toko</p>
                
                @foreach($allTokos as $toko)
                    <div class="flex items-center justify-between border border-gray-100 rounded-xl p-3 bg-white shadow-sm hover:bg-gray-50 transition">
                        
                        <div class="flex-grow flex items-center cursor-pointer pr-4" onclick="openDetail({{ $toko->id }}, 'select')">
                            <div class="w-10 h-10 bg-[#EBF1FF] text-[#0F47A1] rounded-xl flex items-center justify-center flex-shrink-0 mr-4">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M21 7V5c0-1.103-.897-2-2-2H5c-1.103 0-2 .897-2 2v2a3 3 0 0 0 1.488 2.585V19c0 1.103.897 2 2 2h11c1.103 0 2-.897 2-2V9.585A3 3 0 0 0 21 7zM5 5h14v2a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V5zm12.5 14H6.5V9.038A2.969 2.969 0 0 0 8 9.5a2.969 2.969 0 0 0 2-1.5 2.969 2.969 0 0 0 2 1.5 2.969 2.969 0 0 0 2-1.5 2.969 2.969 0 0 0 2 1.5c.571 0 1.102-.178 1.5-.462V19z"></path></svg>
                            </div>
                            <div class="flex flex-col">
                                <h3 class="text-[14px] font-bold text-gray-900 select-none">{{ $toko->nama_toko }}</h3>
                                <p class="text-[10px] text-gray-500 truncate w-40 mt-0.5">{{ $toko->alamat ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="pl-4 border-l border-gray-100 flex items-center h-full">
                            <input type="checkbox" name="toko_ids[]" value="{{ $toko->id }}" id="chk-{{ $toko->id }}" 
                                class="w-5 h-5 text-[#0F47A1] border-gray-300 rounded focus:ring-[#0F47A1] cursor-pointer"
                                {{ in_array($toko->id, $assignedTokoIds) ? 'checked' : '' }}>
                        </div>
                    </div>
                @endforeach
            </form>

            <div id="view-detail" class="hidden space-y-4">
                <h2 id="dt-nama-judul" class="text-[22px] font-bold text-gray-900 text-center mb-6">-</h2>
                
                <div class="border border-gray-100 rounded-xl p-5 bg-white shadow-sm space-y-4">
                    <div class="flex items-center space-x-3 mb-4 border-b border-gray-50 pb-4">
                        <div class="w-10 h-10 bg-[#ECFDF5] text-[#10B981] rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-medium text-gray-500">Nama Toko</p>
                            <h3 id="dt-nama" class="text-[14px] font-bold text-gray-900">-</h3>
                        </div>
                    </div>

                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"></path></svg></div>
                        <span id="dt-telp" class="text-[13px] font-bold text-gray-900 text-right">-</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"></path></svg></div>
                        <span id="dt-alamat" class="text-[13px] font-bold text-gray-900 text-right w-2/3 truncate">-</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-[#F59E0B]"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="7"></circle></svg></div>
                        <span id="dt-provinsi" class="text-[13px] font-bold text-gray-900 text-right">-</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-[#EAB308]"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"></path></svg></div>
                        <span id="dt-kota" class="text-[13px] font-bold text-gray-900 text-right">-</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-3 text-gray-500"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"></path></svg></div>
                        <span id="dt-kodepos" class="text-[13px] font-bold text-gray-900 text-right">-</span>
                    </div>
                </div>
            </div>

        </div>

        <div class="fixed bottom-0 max-w-md mx-auto inset-x-0 bg-white p-5 z-20 space-y-3 shadow-[0_-4px_15px_rgba(0,0,0,0.03)]">
            
            <button id="btn-assigned" onclick="showView('view-select')" class="w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-bold py-3.5 rounded-full shadow-md transition text-[14px]">Pilih Toko Baru</button>
            
            <button id="btn-select" onclick="document.getElementById('view-select').submit()" class="hidden w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-bold py-3.5 rounded-full shadow-md transition text-[14px]">Simpan Perubahan</button>
            
            <button id="btn-detail-kembali" onclick="goBack()" class="hidden w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-bold py-3.5 rounded-full shadow-md transition text-[14px]">Kembali</button>
            <button id="btn-detail-pilih" onclick="selectTokoAndBack()" class="hidden w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-bold py-3.5 rounded-full shadow-md transition text-[14px]">Pilih & Kembali</button>
        </div>
    </div>

    <script>
        // Tarik data toko dari Laravel ke JS
        const allTokos = @json($allTokos);
        
        let currentState = 'assigned'; 
        let currentDetailId = null;

        function showView(viewId) {
            // Sembunyiin semua dulu
            document.getElementById('view-assigned').classList.add('hidden');
            document.getElementById('view-select').classList.add('hidden');
            document.getElementById('view-detail').classList.add('hidden');
            
            document.getElementById('btn-assigned').classList.add('hidden');
            document.getElementById('btn-select').classList.add('hidden');
            document.getElementById('btn-detail-kembali').classList.add('hidden');
            document.getElementById('btn-detail-pilih').classList.add('hidden');

            // Munculin yang diminta
            document.getElementById(viewId).classList.remove('hidden');

            // Set Header & Button berdasarkan View
            if (viewId === 'view-assigned') {
                currentState = 'assigned';
                document.getElementById('header-title').innerText = 'Toko Pegangan';
                document.getElementById('btn-assigned').classList.remove('hidden');
            } else if (viewId === 'view-select') {
                currentState = 'select';
                document.getElementById('header-title').innerText = 'Pilih Toko';
                document.getElementById('btn-select').classList.remove('hidden');
            } else if (viewId === 'view-detail') {
                document.getElementById('header-title').innerText = 'Detail Toko';
                
                // Cek darimana kita buka detail ini
                if(currentState === 'assigned') {
                    document.getElementById('btn-detail-kembali').classList.remove('hidden');
                } else {
                    document.getElementById('btn-detail-pilih').classList.remove('hidden');
                }
            }
        }

        // Fungsi Buka Detail
        function openDetail(id, sourceState) {
            const toko = allTokos.find(t => t.id === id);
            if(!toko) return;
            
            currentDetailId = id;
            document.getElementById('dt-nama-judul').innerText = toko.nama_toko;
            document.getElementById('dt-nama').innerText = toko.nama_toko;
            document.getElementById('dt-telp').innerText = toko.no_telp || '-';
            document.getElementById('dt-alamat').innerText = toko.alamat || '-';
            document.getElementById('dt-provinsi').innerText = toko.provinsi || '-';
            document.getElementById('dt-kota').innerText = toko.kota || '-';
            document.getElementById('dt-kodepos').innerText = toko.kode_pos || '-';

            showView('view-detail');
        }

        // Fungsi Centang via Tombol 'Pilih' di dalam Detail
        function selectTokoAndBack() {
            const chk = document.getElementById('chk-' + currentDetailId);
            if(chk) chk.checked = true; // Langsung dicentang
            showView('view-select'); // Balik ke halaman centangan
        }

        // Fungsi Navigasi Back
        function goBack() {
            // Kalau lagi di List Pegangan, berarti balik ke halaman Profile
            if (currentState === 'assigned' && document.getElementById('view-detail').classList.contains('hidden')) {
                window.location.href = "{{ route('sales.profile.index') }}";
            } 
            // Kalau lagi buka Detail, berarti tutup detail dan balik ke state asalnya
            else if (!document.getElementById('view-detail').classList.contains('hidden')) {
                showView('view-' + currentState); 
            } 
            // Kalau lagi di form Pilih Toko, batalin dan balik ke List Pegangan
            else if (currentState === 'select') {
                showView('view-assigned');
            }
        }
    </script>
</body>
</html>