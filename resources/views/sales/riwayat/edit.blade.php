<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Catat Pembayaran</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">
    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative pb-32 shadow-2xl overflow-hidden">
        
        <div class="px-6 pt-12 pb-4 flex items-center bg-white z-10 border-b border-gray-50">
            <button onclick="window.history.back()" class="text-[#0F47A1] p-2 -ml-2 hover:text-blue-900 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <h1 class="text-[18px] font-bold text-gray-900 flex-grow text-center pr-8">Upload Nota</h1>
        </div>

        <div class="px-5 pt-5 flex-grow overflow-y-auto">
            <div class="border border-gray-100 rounded-xl p-4 bg-white shadow-sm flex items-start space-x-4 mb-6">
                <div class="w-10 h-10 bg-orange-50 text-[#F59E0B] rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div class="flex-grow">
                    <h3 class="text-[14px] font-bold text-gray-900">{{ $faktur->toko->nama_toko ?? '-' }}</h3>
                    <p class="text-[11px] font-medium text-gray-400 bg-gray-100 px-2 py-0.5 rounded w-max mt-1">{{ $faktur->nomor_faktur }}</p>
                    <p class="text-[11px] text-gray-500 mt-2">Tanggal Faktur<br><span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($faktur->tanggal_nota)->translatedFormat('d M Y') }}</span></p>
                </div>
                <div class="text-right border-l border-gray-100 pl-4">
                    <p class="text-[10px] font-medium text-gray-500">Sisa Tagihan</p>
                    <p class="text-[14px] font-bold text-[#F59E0B] mt-0.5">Rp{{ number_format(max(0, $faktur->total_tagihan - $faktur->total_dibayar), 0, ',', '.') }}</p>
                </div>
            </div>

            <form action="{{ route('sales.riwayat.update', $faktur->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[13px] font-bold text-gray-900 mb-1.5">Tanggal Pembayaran</label>
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3.5 top-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <input type="date" name="tanggal_pembayaran" required value="{{ date('Y-m-d') }}" class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-3 text-[13px] font-medium focus:ring-2 focus:ring-[#0F47A1] focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-[13px] font-bold text-gray-900 mb-1.5">Nominal Pembayaran</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3.5 text-[13px] font-bold text-gray-500">Rp</span>
                        <input type="number" name="nominal_pembayaran" required value="{{ max(0, $faktur->total_tagihan - $faktur->total_dibayar) }}" class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-3 text-[13px] font-medium focus:ring-2 focus:ring-[#0F47A1] focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-[13px] font-bold text-gray-900 mb-1.5">Metode Pembayaran</label>
                    <select name="metode_pembayaran" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-[13px] font-medium focus:ring-2 focus:ring-[#0F47A1] focus:outline-none bg-white">
                        <option value="Transfer" {{ $faktur->metode_bayar == 'Transfer' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="Cash" {{ $faktur->metode_bayar == 'Cash' ? 'selected' : '' }}>Cash</option>
                    </select>
                </div>

                <div class="fixed bottom-0 max-w-md mx-auto inset-x-0 bg-white p-5 z-20 space-y-3">
                    <button type="submit" class="w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-bold py-3.5 rounded-full shadow-md transition text-[14px]">Simpan Pembayaran</button>
                    <button type="button" onclick="window.history.back()" class="w-full bg-white border-2 border-[#0F47A1] text-[#0F47A1] hover:bg-blue-50 font-bold py-3.5 rounded-full transition text-[14px]">Batalkan</button>
                </div>
            </form>

        </div>
    </div>
</body>
</html>