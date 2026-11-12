@php
    $nomorInvoice = $faktur->nomor_faktur ?: 'INV-' . $faktur->id;
    $isLunas = $faktur->status === 'lunas';
    $sisaTagihan = max(0, (float) $faktur->total_tagihan - (float) $faktur->total_dibayar);
    $tanggalNota = $faktur->tanggal_nota
        ? \Carbon\Carbon::parse($faktur->tanggal_nota)->translatedFormat('d F Y')
        : '-';
    $tanggalSetor = $faktur->tanggal_pembayaran
        ? \Carbon\Carbon::parse($faktur->tanggal_pembayaran)->translatedFormat('d F Y')
        : '-';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Invoice {{ $nomorInvoice }} - Sales Ledger</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 text-gray-800 antialiased">

    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-gray-50 relative shadow-2xl">
        <div class="px-6 pt-12 pb-4 flex items-center bg-white border-b border-gray-100">
            <a href="{{ $backUrl }}" class="text-[#0F47A1] p-2 -ml-2 hover:text-blue-900 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <h1 class="text-[18px] font-bold text-gray-900 flex-grow text-center pr-8">Invoice</h1>
        </div>

        <div class="p-5 space-y-4 flex-grow">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-[#0F47A1] px-6 py-6 text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-[11px] font-bold tracking-[0.2em] text-blue-100 uppercase">Sales Ledger</p>
                            <h2 class="text-[28px] font-extrabold tracking-tight mt-1">INVOICE</h2>
                        </div>
                        <div class="w-11 h-11 rounded-full bg-white/15 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-5 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] text-blue-100 font-medium">Nomor Invoice</p>
                            <p class="text-[15px] font-bold mt-0.5">{{ $nomorInvoice }}</p>
                        </div>
                        <span class="text-[10px] font-bold px-3 py-1.5 rounded-full {{ $isLunas ? 'bg-emerald-400/20 text-emerald-50' : 'bg-orange-400/20 text-orange-50' }}">
                            {{ $isLunas ? 'LUNAS' : 'BELUM LUNAS' }}
                        </span>
                    </div>
                </div>

                <div class="p-5 space-y-5">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Tanggal Nota</p>
                            <p class="text-[13px] font-bold text-gray-900 mt-1">{{ $tanggalNota }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Metode Bayar</p>
                            <p class="text-[13px] font-bold text-gray-900 mt-1">{{ $faktur->metode_bayar ?: '-' }}</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide mb-2">Ditagihkan Kepada</p>
                        <p class="text-[15px] font-extrabold text-gray-900">{{ $faktur->toko->nama_toko ?? '-' }}</p>
                        @if($faktur->toko)
                            <p class="text-[12px] text-gray-500 leading-relaxed mt-1">{{ $faktur->toko->alamat ?: '-' }}</p>
                            <p class="text-[12px] text-gray-500 leading-relaxed">
                                {{ collect([$faktur->toko->kota, $faktur->toko->provinsi, $faktur->toko->kode_pos])->filter()->join(', ') ?: '-' }}
                            </p>
                            <p class="text-[12px] text-gray-500 mt-1">Telp: {{ $faktur->toko->no_telp ?: '-' }}</p>
                        @endif
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <div class="flex items-center justify-between text-[10px] font-bold text-gray-400 uppercase tracking-wide pb-2 border-b border-gray-100">
                            <span>Deskripsi</span>
                            <span>Total</span>
                        </div>
                        <div class="flex items-start justify-between py-3 border-b border-gray-50">
                            <div>
                                <p class="text-[13px] font-bold text-gray-900">Tagihan Faktur</p>
                                <p class="text-[11px] text-gray-500 mt-0.5">No. {{ $nomorInvoice }}</p>
                            </div>
                            <p class="text-[13px] font-bold text-gray-900">Rp{{ number_format($faktur->total_tagihan, 0, ',', '.') }}</p>
                        </div>

                        <div class="space-y-2 pt-3">
                            <div class="flex justify-between text-[12px]">
                                <span class="text-gray-500 font-medium">Total Tagihan</span>
                                <span class="font-bold text-gray-900">Rp{{ number_format($faktur->total_tagihan, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-[12px]">
                                <span class="text-gray-500 font-medium">Sudah Dibayar</span>
                                <span class="font-bold text-emerald-600">Rp{{ number_format($faktur->total_dibayar, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                                <span class="text-[12px] font-bold text-gray-900">Sisa Tagihan</span>
                                <span class="text-[17px] font-extrabold {{ $sisaTagihan > 0 ? 'text-orange-500' : 'text-emerald-600' }}">
                                    Rp{{ number_format($sisaTagihan, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-4 grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Sales</p>
                            <p class="text-[12px] font-bold text-gray-900 mt-1">{{ $faktur->user->full_name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Tanggal Setor</p>
                            <p class="text-[12px] font-bold text-gray-900 mt-1">{{ $tanggalSetor }}</p>
                        </div>
                    </div>
                </div>

                <div class="px-5 pb-5">
                    <p class="text-[10px] text-gray-400 leading-relaxed">
                        Invoice ini dibuat otomatis oleh Sales Ledger pada {{ now()->translatedFormat('d F Y, H:i') }}.
                    </p>
                </div>
            </div>

            <div class="space-y-3 pb-6">
                <a href="{{ $downloadUrl }}" class="w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-bold py-3.5 rounded-full shadow-md transition text-[14px] flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>Download Invoice PDF</span>
                </a>
                <a href="{{ $backUrl }}" class="w-full bg-white border border-gray-200 text-gray-700 font-bold py-3.5 rounded-full transition text-[14px] flex items-center justify-center">
                    Kembali
                </a>
            </div>
        </div>
    </div>
</body>
</html>
