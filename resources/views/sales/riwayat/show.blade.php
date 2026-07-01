<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Detail Faktur - Sales Ledger</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative pb-24 shadow-2xl overflow-hidden">
        
        <div class="px-5 pt-8 flex-grow overflow-y-auto no-scrollbar pb-10">
            
            <div class="flex flex-col items-center pt-4">
                @php 
                    $isLunas = $faktur->status === 'lunas'; 
                    $colorClass = $isLunas ? 'emerald' : 'emerald'; // Kita pakai ikon checklist sukses hijau untuk keduanya sesuai gambar lu
                @endphp
                <div class="relative w-28 h-28 mb-6">
                    <div class="absolute inset-0 bg-[#ECFDF5] rounded-full animate-ping opacity-75"></div>
                    <div class="relative w-full h-full bg-[#10B981] rounded-full flex items-center justify-center text-white shadow-lg border-8 border-[#ECFDF5]">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="3.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </div>
                
                <h2 class="text-[20px] font-bold text-gray-900 mb-1">Detail Faktur</h2>
                <p class="text-[13px] text-gray-500 mb-6">Data tagihan yang terekam di sistem.</p>
                
                <div class="w-full border border-gray-100 rounded-2xl p-5 bg-white shadow-sm space-y-4">
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5 text-[#10B981]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 4H4v2h16V4zm1 10v-2l-1-5H4l-1 5v2h1v6h10v-6h4v6h2v-6h1zm-9 4H6v-4h6v4z"></path></svg><span class="text-[13px] font-medium">Nama Toko</span></div>
                        <span class="text-[13px] font-bold text-gray-900">{{ $faktur->toko->nama_toko ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"></path></svg><span class="text-[13px] font-medium">Nomor Faktur</span></div>
                        <span class="text-[13px] font-semibold text-gray-900">{{ $faktur->nomor_faktur }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg><span class="text-[13px] font-medium">Tanggal Faktur</span></div>
                        <span class="text-[13px] font-semibold text-gray-900">{{ \Carbon\Carbon::parse($faktur->tanggal_nota)->translatedFormat('d M Y') }}</span>
                    </div>
                    @if(!($faktur->status === 'lunas' && is_null($faktur->tanggal_pembayaran)))
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg><span class="text-[13px] font-medium">Tanggal Pembayaran</span></div>
                        <span class="text-[13px] font-semibold text-gray-900">
                            {{ $faktur->tanggal_pembayaran ? \Carbon\Carbon::parse($faktur->tanggal_pembayaran)->translatedFormat('d M Y') : '-' }}
                        </span>
                    </div>
                    @endif
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg><span class="text-[13px] font-medium">Metode Pembayaran</span></div>
                        <span class="text-[13px] font-semibold text-gray-900">{{ $faktur->metode_bayar }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5 text-[#0F47A1]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg><span class="text-[13px] font-medium">Total Tagihan</span></div>
                        <span class="text-[14px] font-bold text-[#0F47A1]">Rp{{ number_format($faktur->total_tagihan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5 text-[#10B981]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span class="text-[13px] font-medium">Sudah Dibayar</span></div>
                        <span class="text-[14px] font-bold text-[#10B981]">Rp{{ number_format($faktur->total_dibayar, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5 text-[#F59E0B]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 022 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg><span class="text-[13px] font-medium">Sisa Tagihan</span></div>
                        <span class="text-[14px] font-bold text-[#F59E0B]">Rp{{ number_format(max(0, $faktur->total_tagihan - $faktur->total_dibayar), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span class="text-[13px] font-medium">Status</span></div>
                        <span class="text-[11px] font-bold px-3 py-1 rounded {{ $isLunas ? 'bg-[#ECFDF5] text-[#10B981]' : 'bg-[#FFF7ED] text-[#F59E0B]' }}">
                            {{ $isLunas ? 'Lunas' : 'Belum Lunas' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex justify-center mt-6">
                @if($faktur->foto_url)
                    <button onclick="togglePhotoModal(true)" class="border border-[#0F47A1] text-[#0F47A1] hover:bg-blue-50 font-semibold px-6 py-2.5 rounded-full flex items-center space-x-2 transition text-[13px]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>Lihat Foto Faktur</span>
                    </button>
                @endif
            </div>
        </div>

        <div class="fixed bottom-0 max-w-md mx-auto inset-x-0 bg-white border-t border-gray-100 p-5 z-20 space-y-3">
            <button onclick="window.location.href='{{ route('sales.riwayat.index') }}'" class="w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-bold py-3.5 rounded-full shadow-md transition text-[14px]">Kembali</button>
            @if($faktur->status === 'belum_lunas')
                <button onclick="window.location.href='{{ route('sales.riwayat.edit', $faktur->id) }}'" class="w-full bg-white border-2 border-[#0F47A1] text-[#0F47A1] hover:bg-blue-50 font-bold py-3.5 rounded-full transition text-[14px]">Catat Pembayaran</button>
            @endif
        </div>
    </div>

    @if($faktur->foto_url)
    <div id="modal-photo" class="fixed inset-0 z-[60] hidden justify-center bg-black/70 backdrop-blur-sm transition-opacity">
        <div class="w-full max-w-md bg-white flex flex-col h-full shadow-2xl relative">
            <div class="px-6 pt-12 pb-4 flex items-center border-b border-gray-100 bg-white shadow-sm z-10">
                <button onclick="togglePhotoModal(false)" class="text-[#0F47A1] hover:text-blue-900 transition p-2 -ml-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <h1 class="text-[18px] font-bold text-gray-900 flex-grow text-center pr-8">Foto Nota</h1>
            </div>
            <div class="flex-grow bg-[#E5E7EB] flex items-center justify-center overflow-hidden relative p-4">
                <img id="preview-image" src="" alt="Faktur Full" class="max-w-full max-h-full object-contain rounded-lg shadow-lg relative z-10 hidden transition-opacity duration-300" onload="this.classList.remove('hidden');" onerror="if(this.src && this.src !== window.location.href) { this.classList.add('hidden'); }">
            </div>
        </div>
    </div>

    <script>
        function togglePhotoModal(show) {
            const modal = document.getElementById('modal-photo');
            const imgEl = document.getElementById('preview-image');
            if(!modal) return;
            if(show) { imgEl.src = "{{ $faktur->foto_url }}"; modal.classList.remove('hidden'); modal.classList.add('flex'); } 
            else { modal.classList.add('hidden'); modal.classList.remove('flex'); setTimeout(() => { imgEl.src = ""; }, 300); }
        }
    </script>
    @endif
</body>
</html>