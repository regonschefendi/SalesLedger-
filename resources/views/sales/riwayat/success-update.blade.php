<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pembaruan Sukses</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">
    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative pb-32 shadow-2xl overflow-hidden pt-8">
        
        <div class="flex flex-col items-center pt-4 px-5 flex-grow">
            <div class="relative w-28 h-28 mb-6">
                <div class="absolute inset-0 bg-[#ECFDF5] rounded-full animate-ping opacity-75"></div>
                <div class="relative w-full h-full bg-[#10B981] rounded-full flex items-center justify-center text-white shadow-lg border-8 border-[#ECFDF5]">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="3.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                </div>
            </div>
            
            <h2 class="text-[20px] font-bold text-gray-900 mb-1">Faktur Berhasil Dicatat</h2>
            <p class="text-[13px] text-gray-500 mb-6">Data Faktur Berhasil dicatat.</p>
            
            <div class="w-full border border-gray-100 rounded-lg p-5 bg-white shadow-sm space-y-4">
                <div class="flex items-center space-x-3 mb-4 border-b border-gray-50 pb-4">
                    <div class="w-10 h-10 bg-[#ECFDF5] text-[#10B981] rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-[15px] font-bold text-gray-900">{{ $faktur->toko->nama_toko ?? '-' }}</h3>
                        <div class="mt-1.5 flex">
                        <span class="inline-flex items-center text-[12px] font-semibold text-gray-600 bg-gray-100/80 border border-gray-200/60 px-3 py-1 rounded-md tracking-wide">
                            <svg class="w-3.5 h-3.5 mr-1.5 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"></path></svg>
                            {{ $faktur->nomor_faktur }}
                        </span>
                    </div>
                    </div>
                </div>

                <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                    <span class="text-[13px] font-bold text-gray-800">Total Dibayar</span>
                    <span class="text-[13px] font-bold text-gray-800">Rp{{ number_format($faktur->total_dibayar, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                    <span class="text-[13px] font-bold text-gray-800">Tanggal Pembayaran</span>
                    <span class="text-[13px] text-gray-800">{{ \Carbon\Carbon::parse($faktur->tanggal_pembayaran)->translatedFormat('d M Y') }}</span>
                </div>
                <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                    <span class="text-[13px] font-bold text-gray-800">Metode Pembayaran</span>
                    <span class="text-[13px] text-gray-800">{{ $faktur->metode_bayar }}</span>
                </div>
                <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                    <span class="text-[13px] font-bold text-gray-800">Sisa Tagihan</span>
                    <span class="text-[13px] text-gray-800">Rp{{ number_format(max(0, $faktur->total_tagihan - $faktur->total_dibayar), 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[13px] font-bold text-gray-800">Status</span>
                    <span class="text-[10px] font-bold px-3 py-1 rounded {{ $faktur->status === 'lunas' ? 'bg-[#ECFDF5] text-[#10B981]' : 'bg-[#FFF7ED] text-[#F59E0B]' }}">
                        {{ $faktur->status === 'lunas' ? 'Lunas' : 'Belum Lunas' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="fixed bottom-0 max-w-md mx-auto inset-x-0 bg-white p-5 z-20 space-y-3">
            <button onclick="window.location.href='{{ route('sales.riwayat.index') }}'" class="w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-bold py-3.5 rounded-full shadow-md transition text-[14px]">Selesai</button>
            {{-- <button onclick="window.location.href='{{ route('sales.riwayat.show', $faktur->id) }}'" class="w-full bg-white border-2 border-[#0F47A1] text-[#0F47A1] hover:bg-blue-50 font-bold py-3.5 rounded-full transition text-[14px]">Detail Riwayat</button> --}}
        </div>
    </div>
</body>
</html>