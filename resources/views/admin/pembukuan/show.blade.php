<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Detail Pembukuan - Sales Ledger</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative pb-20 shadow-2xl overflow-hidden">
        
        <div class="px-6 pt-12 pb-4 flex items-center bg-white border-b border-gray-50">
            <button onclick="window.history.back()" class="text-blue-600 p-2 -ml-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <h1 class="text-[18px] font-bold text-gray-900 flex-grow text-center pr-8">Detail Pembukuan</h1>
        </div>

        <div class="px-5 pt-4 space-y-4 flex-grow overflow-y-auto no-scrollbar pb-10">
            
            <div class="p-3.5 rounded-xl border {{ $faktur->status == 'lunas' ? 'bg-emerald-50 border-emerald-200 text-emerald-600' : 'bg-orange-50 border-orange-200 text-orange-600' }} text-[12px] font-bold flex items-center space-x-2">
                <div class="w-2 h-2 rounded-full {{ $faktur->status == 'lunas' ? 'bg-emerald-500' : 'bg-orange-500' }}"></div>
                <span>{{ $faktur->status == 'lunas' ? 'Lunas' : 'Belum Lunas' }}</span>
            </div>

            <div class="border border-gray-100 rounded-2xl p-4 shadow-sm bg-white space-y-4">
                <div class="flex items-center space-x-2 text-blue-600 border-b border-gray-50 pb-2">
                    <svg class="w-4 h-4 text-[#2563EB]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <span class="text-[13px] font-bold text-gray-900">Informasi Utama</span>
                </div>
                <div class="space-y-3 text-[13px]">
                    <div class="flex justify-between"><span class="text-gray-400 font-medium">Nama Toko</span><span class="font-bold text-gray-900">{{ $faktur->nama_toko }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400 font-medium">No. Nota</span><span class="font-semibold text-gray-900">{{ $faktur->nomor_faktur }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400 font-medium">Sales</span><span class="font-semibold text-gray-900">{{ $faktur->user->full_name ?? '-' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400 font-medium">Tanggal Upload</span><span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($faktur->created_at)->format('d M Y, H:i') }}</span></div>
                </div>
            </div>

            <div class="border border-gray-100 rounded-2xl p-4 shadow-sm bg-white space-y-4">
                <div class="flex items-center space-x-2 text-blue-600 border-b border-gray-50 pb-2">
                    <svg class="w-4 h-4 text-[#2563EB]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    <span class="text-[13px] font-bold text-gray-900">Ringkasan Pembayaran</span>
                </div>
                <div class="space-y-3 text-[13px]">
                    <div class="flex justify-between"><span class="text-gray-400 font-medium">Metode bayar</span><span class="font-semibold text-gray-900">{{ $faktur->metode_bayar ?? 'Transfer' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400 font-medium">Total Tagihan</span><span class="font-bold text-gray-900">Rp{{ number_format($faktur->total_tagihan, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400 font-medium">Sudah Dibayar</span><span class="font-bold text-gray-900">Rp{{ number_format($faktur->total_dibayar, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between border-t border-gray-50 pt-2"><span class="text-gray-400 font-bold">Sisa Tagihan</span><span class="font-bold text-orange-500">Rp{{ number_format(($faktur->total_tagihan - $faktur->total_dibayar), 0, ',', '.') }}</span></div>
                </div>
            </div>

            <div class="border border-gray-100 rounded-2xl p-4 shadow-sm bg-white space-y-4">
                <div class="flex items-center space-x-2 text-blue-600 border-b border-gray-50 pb-2">
                    <svg class="w-4 h-4 text-[#2563EB]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span class="text-[13px] font-bold text-gray-900">Tanggal</span>
                </div>
                <div class="space-y-3 text-[13px]">
                    <div class="flex justify-between"><span class="text-gray-400 font-medium">Tanggal Nota</span><span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($faktur->tanggal_nota)->format('d M Y') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400 font-medium">Tanggal Setor</span><span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($faktur->tanggal_nota)->format('d M Y') }}</span></div>
                </div>
            </div>

            <div class="border border-gray-100 rounded-2xl p-4 shadow-sm bg-white space-y-4">
                <div class="flex items-center space-x-2 text-blue-600 border-b border-gray-50 pb-2">
                    <svg class="w-4 h-4 text-[#2563EB]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span class="text-[13px] font-bold text-gray-900">Foto Nota</span>
                </div>
                <div class="w-full h-44 bg-slate-50 border border-dashed border-slate-200 rounded-2xl flex items-center justify-center overflow-hidden">
                    <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375 0 11-.75 0 .375 0 01.75 0z"></path></svg>
                </div>
                <button class="w-full border border-[#2563EB] text-[#2563EB] text-[13px] font-bold py-3 rounded-full hover:bg-blue-50 transition flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    <span>Lihat Foto</span>
                </button>
            </div>

        </div>

        <div class="p-4 bg-white border-t border-gray-100 fixed bottom-0 inset-x-0 max-w-md mx-auto z-40">
            <button onclick="window.history.back()" class="w-full bg-[#2563EB] hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl text-[14px] shadow-md transition duration-150">Kembali</button>
        </div>
    </div>

</body>
</html>