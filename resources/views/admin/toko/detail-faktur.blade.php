<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Detail Nota - Sales Ledger</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative pb-24 shadow-2xl overflow-hidden">
        
        <div class="px-6 pt-12 pb-4 flex items-center bg-white z-10 relative">
            <button onclick="window.history.back()" class="text-[#0F47A1] p-2 -ml-2 hover:text-blue-900 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <h1 class="text-[18px] font-bold text-gray-900 flex-grow text-center pr-8">Detail Nota</h1>
        </div>

        <div class="px-5 pt-2 flex-grow overflow-y-auto no-scrollbar pb-10 space-y-4">
            
            @php
                $sisaTagihan = $faktur->total_tagihan - $faktur->total_dibayar;
                $isLunas = $faktur->status === 'lunas';
                // Warna Dinamis
                $colorTheme = $isLunas ? 'emerald' : 'orange';
                $hexColor = $isLunas ? '#10B981' : '#F59E0B';
                $bgLight = $isLunas ? '#ECFDF5' : '#FFF7ED';
            @endphp

            <div class="border border-{{ $colorTheme }}-100 rounded-xl bg-{{ $colorTheme }}-50/50 p-4 flex items-center shadow-sm">
                <div class="flex-1 flex items-center justify-center space-x-2 border-r border-{{ $colorTheme }}-200/60 pr-4">
                    <div class="w-6 h-6 rounded-full bg-[{{ $hexColor }}] flex items-center justify-center text-white flex-shrink-0">
                        @if($isLunas)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        @endif
                    </div>
                    <span class="text-[14px] font-bold text-[{{ $hexColor }}]">{{ $isLunas ? 'Lunas' : 'Belum Lunas' }}</span>
                </div>
                <div class="flex-1 flex flex-col justify-center pl-4 text-center">
                    <span class="text-[11px] font-bold text-gray-800 leading-tight">Sisa Tagihan:</span>
                    <span class="text-[15px] font-bold text-[{{ $hexColor }}]">Rp{{ number_format(max(0, $sisaTagihan), 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="border border-gray-100 rounded-2xl p-5 bg-white shadow-sm space-y-4">
                <div class="flex items-center space-x-2 mb-2">
                    <div class="w-6 h-6 bg-blue-50 text-[#0F47A1] rounded flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-[14px] font-bold text-gray-900">Ringkasan</h3>
                </div>
                
                <div class="space-y-3.5">
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <span class="text-[13px] text-gray-600 font-medium">Nama Toko</span>
                        <span class="text-[13px] font-medium text-gray-900">{{ $faktur->toko->nama_toko ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <span class="text-[13px] text-gray-600 font-medium">No. Nota</span>
                        <span class="text-[13px] font-medium text-gray-900">{{ $faktur->nomor_faktur }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <span class="text-[13px] text-gray-600 font-medium">Tanggal Nota</span>
                        <span class="text-[13px] font-medium text-gray-900">{{ \Carbon\Carbon::parse($faktur->tanggal_nota)->translatedFormat('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[13px] text-gray-600 font-medium">Total Tagihan</span>
                        <span class="text-[14px] font-bold text-gray-900">Rp{{ number_format($faktur->total_tagihan, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="border border-gray-100 rounded-2xl p-5 bg-white shadow-sm space-y-4">
                <div class="flex items-center space-x-2 mb-2">
                    <div class="w-6 h-6 bg-blue-50 text-[#0F47A1] rounded flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 022 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h3 class="text-[14px] font-bold text-gray-900">Ringkasan Pembayaran</h3>
                </div>
                
                <div class="space-y-3.5">
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <span class="text-[13px] text-gray-600 font-medium">Metode bayar</span>
                        <span class="text-[13px] font-medium text-gray-900">{{ $faktur->metode_bayar ?? 'Transfer' }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <span class="text-[13px] text-gray-600 font-medium">Sudah Dibayar</span>
                        <span class="text-[13px] font-medium text-gray-900">Rp{{ number_format($faktur->total_dibayar, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <span class="text-[13px] text-gray-600 font-medium">Sisa Tagihan</span>
                        <span class="text-[13px] font-bold text-[#F59E0B]">Rp{{ number_format(max(0, $sisaTagihan), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <span class="text-[13px] text-gray-600 font-medium">Tanggal Pembayaran</span>
                        <span class="text-[13px] font-medium text-gray-900">{{ \Carbon\Carbon::parse($faktur->created_at)->translatedFormat('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[13px] text-gray-600 font-medium">Tanggal Setor</span>
                        <span class="text-[13px] font-medium text-gray-900">{{ \Carbon\Carbon::parse($faktur->created_at)->translatedFormat('d M Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="border border-gray-100 rounded-2xl p-5 bg-white shadow-sm space-y-4">
                <div class="flex items-center space-x-2 mb-2">
                    <div class="w-6 h-6 bg-blue-50 text-[#0F47A1] rounded flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                    </div>
                    <h3 class="text-[14px] font-bold text-gray-900">Foto Nota</h3>
                </div>
                
                <div class="w-full h-48 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-center overflow-hidden relative">
                    @if($faktur->foto_url)
                        <img src="{{ $faktur->foto_url }}" alt="Thumbnail Faktur" class="w-full h-full object-cover cursor-pointer hover:opacity-90 transition" onclick="toggleAdminPhotoModal(true)">
                    @else
                        <span class="text-[12px] font-medium text-gray-400">Foto tidak tersedia</span>
                    @endif
                </div>
            </div>

        </div>

        <div class="fixed bottom-0 max-w-md mx-auto inset-x-0 bg-white border-t border-gray-100 p-5 z-20 shadow-[0_-4px_15px_rgba(0,0,0,0.03)]">
            <button onclick="window.history.back()" class="w-full bg-[#2563EB] hover:bg-blue-800 text-white font-semibold py-3.5 rounded-xl shadow-md transition duration-200 text-[14px]">Kembali</button>
        </div>
    </div>

    @if($faktur->foto_url)
    <div id="modal-photo-admin" class="fixed inset-0 z-[60] hidden justify-center bg-black/70 backdrop-blur-sm transition-opacity">
        <div class="w-full max-w-md bg-white flex flex-col h-full shadow-2xl relative">
            <div class="px-6 pt-12 pb-4 flex items-center border-b border-gray-100 bg-white shadow-sm z-10">
                <button onclick="toggleAdminPhotoModal(false)" class="text-[#0F47A1] hover:text-blue-900 transition p-2 -ml-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <h1 class="text-[18px] font-bold text-gray-900 flex-grow text-center pr-8">Bukti Nota Autentik</h1>
            </div>
            
            <div class="flex-grow bg-[#E5E7EB] flex items-center justify-center overflow-hidden relative p-4">
                <div id="loading-admin-image" class="absolute flex flex-col items-center text-gray-500 font-medium text-[12px]">
                    <svg class="w-8 h-8 mb-2 animate-spin text-[#0F47A1]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Memuat Dokumen...
                </div>
                <img id="admin-preview-image" src="" alt="Faktur Full" 
                     class="max-w-full max-h-full object-contain rounded-lg shadow-lg relative z-10 hidden transition-opacity duration-300"
                     onload="document.getElementById('loading-admin-image').classList.add('hidden'); this.classList.remove('hidden');"
                     onerror="this.classList.add('hidden'); document.getElementById('loading-admin-image').innerHTML = '⚠️ Foto tidak dapat dimuat.';">
            </div>
            
            <div class="p-5 bg-white pb-8 shadow-[0_-4px_15px_rgba(0,0,0,0.05)] z-10">
                <button onclick="toggleAdminPhotoModal(false)" class="w-full bg-[#1D4ED8] hover:bg-blue-800 text-white font-semibold py-3.5 rounded-xl shadow-md transition duration-200 text-[14px]">Tutup Dokumen</button>
            </div>
        </div>
    </div>

    <script>
        function toggleAdminPhotoModal(show) {
            const modal = document.getElementById('modal-photo-admin');
            const imgEl = document.getElementById('admin-preview-image');
            if(!modal) return;
            if(show) {
                imgEl.src = "{{ $faktur->foto_url }}";
                modal.classList.remove('hidden'); 
                modal.classList.add('flex'); 
            } else { 
                modal.classList.add('hidden'); 
                modal.classList.remove('flex'); 
                setTimeout(() => { imgEl.src = ""; }, 300);
            }
        }
    </script>
    @endif
</body>
</html>