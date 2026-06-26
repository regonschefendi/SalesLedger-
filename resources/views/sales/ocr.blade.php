<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Upload Nota - Sales Ledger</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative shadow-2xl overflow-hidden pb-10">
        
        <div id="main-header" class="px-6 pt-12 pb-4 flex items-center bg-white border-b border-gray-100 relative z-10">
            <button onclick="window.history.back()" class="text-[#0F47A1] hover:text-blue-900 transition p-2 -ml-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <h1 class="text-[18px] font-bold text-gray-900 flex-grow text-center pr-8">Upload Nota</h1>
        </div>

        <div class="px-5 pt-5 overflow-y-auto flex-grow relative">

            <div id="view-review" class="space-y-4 pb-10 block">
                <div class="flex items-center space-x-3 bg-[#ECFDF5] border border-[#10B981]/30 p-3.5 rounded-xl">
                    <div class="w-7 h-7 bg-[#10B981] rounded-full flex items-center justify-center text-white flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <p class="text-[12px] font-bold text-[#10B981]">Scan nota berhasil dibaca</p>
                        <p class="text-[10px] text-gray-500">Data toko bisa di edit sebelum disimpan.</p>
                    </div>
                </div>

                <div class="flex justify-center my-2">
                    <span id="badge-status" class="bg-[#FFF7ED] text-[#F59E0B] border border-[#F59E0B]/20 text-[13px] font-bold px-4 py-1.5 rounded-md">Belum Lunas</span>
                </div>

                <div class="border border-gray-100 rounded-2xl p-5 shadow-sm bg-white space-y-4">
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg><span class="text-[13px] font-bold">Nama Toko</span></div>
                        <span id="txt-nama" class="text-[13px] font-bold text-gray-900 text-right">Loading...</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg><span class="text-[13px] font-bold">Nomor Faktur</span></div>
                        <span id="txt-nofaktur" class="text-[13px] font-medium text-gray-900 text-right">Loading...</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg><span class="text-[13px] font-bold">Tanggal Faktur</span></div>
                        <span id="txt-tanggal" class="text-[13px] font-medium text-gray-900 text-right">Loading...</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5 text-[#0F47A1]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg><span class="text-[13px] font-bold">Total Tagihan</span></div>
                        <span id="txt-tagihan" class="text-[14px] font-bold text-[#0F47A1] text-right">Rp0</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5 text-[#10B981]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span class="text-[13px] font-bold">Sudah Dibayar</span></div>
                        <span id="txt-dibayar" class="text-[14px] font-bold text-[#10B981] text-right">Rp0</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5 text-[#F59E0B]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg><span class="text-[13px] font-bold">Sisa Tagihan</span></div>
                        <span id="txt-sisa" class="text-[14px] font-bold text-[#F59E0B] text-right">Rp0</span>
                    </div>
                </div>

                <div id="alert-metode" class="flex items-center justify-center space-x-2 text-[#2563EB] text-[12px] font-medium pt-1">
                    <div class="w-4 h-4 border border-[#2563EB] rounded-full flex items-center justify-center font-bold text-[10px]">!</div>
                    <span>Pilih Metode Pembayaran</span>
                </div>

                <div class="border border-gray-100 rounded-2xl p-4 bg-white shadow-sm">
                    <p class="text-[12px] font-bold text-gray-900 mb-3 flex items-center"><svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 022 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>Metode Pembayaran</p>
                    <div class="flex justify-center space-x-8">
                        <label class="flex items-center space-x-2 cursor-pointer select-none">
                            <input type="radio" name="review_metode" value="Cash" onchange="setMetodePembayaran(this.value)" class="w-4 h-4 text-[#0F47A1] focus:ring-[#0F47A1]">
                            <span class="text-[13px] font-semibold text-gray-800 flex items-center">💵 Cash</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer select-none">
                            <input type="radio" name="review_metode" value="Transfer" onchange="setMetodePembayaran(this.value)" class="w-4 h-4 text-[#0F47A1] focus:ring-[#0F47A1]">
                            <span class="text-[13px] font-semibold text-gray-800 flex items-center">💳 Transfer</span>
                        </label>
                    </div>
                </div>

                <div class="space-y-3 pt-2">
                    <button onclick="switchView('edit')" class="w-full bg-white border-2 border-[#0F47A1] text-[#0F47A1] hover:bg-gray-50 font-semibold py-3.5 rounded-full transition duration-200 text-[14px]">Edit Faktur</button>
                    <button id="btn-save-database" disabled class="w-full bg-slate-400 text-white font-semibold py-3.5 rounded-full shadow-md transition duration-200 text-[14px] cursor-not-allowed">Simpan Data</button>
                </div>

                <p class="text-center text-[10px] text-gray-400 mt-2"><span class="text-[#10B981]">✓</span> Periksa data terlebih dahulu sebelum menyimpan.</p>
            </div>

            <div id="view-edit" class="hidden space-y-4 pb-10">
                <div class="flex items-center space-x-3 bg-blue-50 border border-blue-200 p-3.5 rounded-xl mb-4">
                    <div class="w-6 h-6 border-2 border-[#0F47A1] text-[#0F47A1] rounded-full flex items-center justify-center font-bold text-[12px] flex-shrink-0">!</div>
                    <p class="text-[12px] font-bold text-[#0F47A1]">Perbaiki data faktur sebelum disimpan.</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-[12px] font-bold text-gray-900 mb-1.5">Nama Toko</label>
                        <div class="relative"><svg class="w-4 h-4 absolute left-3.5 top-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <input type="text" id="input-nama" class="w-full border border-gray-200 rounded-2xl pl-10 pr-4 py-3 text-[13px] font-medium focus:ring-2 focus:ring-[#0F47A1] focus:outline-none"></div>
                    </div>
                    <div>
                        <label class="block text-[12px] font-bold text-gray-900 mb-1.5">Nomor Faktur</label>
                        <div class="relative"><svg class="w-4 h-4 absolute left-3.5 top-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <input type="text" id="input-nofaktur" class="w-full border border-gray-200 rounded-2xl pl-10 pr-4 py-3 text-[13px] font-medium focus:ring-2 focus:ring-[#0F47A1] focus:outline-none"></div>
                    </div>
                    <div>
                        <label class="block text-[12px] font-bold text-gray-900 mb-1.5">Tanggal Faktur</label>
                        <div class="relative"><svg class="w-4 h-4 absolute left-3.5 top-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <input type="text" id="input-tanggal" class="w-full border border-gray-200 rounded-2xl pl-10 pr-4 py-3 text-[13px] font-medium focus:ring-2 focus:ring-[#0F47A1] focus:outline-none"></div>
                    </div>
                    <div>
                        <label class="block text-[12px] font-bold text-gray-900 mb-1.5">Total Tagihan (Angka)</label>
                        <div class="relative"><svg class="w-4 h-4 absolute left-3.5 top-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        <input type="number" id="input-tagihan" oninput="calcSisaEdit()" class="w-full border border-gray-200 rounded-2xl pl-10 pr-4 py-3 text-[13px] font-medium focus:ring-2 focus:ring-[#0F47A1] focus:outline-none"></div>
                    </div>
                    <div>
                        <label class="block text-[12px] font-bold text-gray-900 mb-1.5">Pembayaran Awal (Angka)</label>
                        <div class="relative"><svg class="w-4 h-4 absolute left-3.5 top-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <input type="number" id="input-dibayar" oninput="calcSisaEdit()" class="w-full border border-gray-200 rounded-2xl pl-10 pr-4 py-3 text-[13px] font-medium focus:ring-2 focus:ring-[#0F47A1] focus:outline-none"></div>
                    </div>
                    <div>
                        <label class="block text-[12px] font-bold text-gray-900 mb-1.5">Sisa Tagihan</label>
                        <div class="relative"><svg class="w-4 h-4 absolute left-3.5 top-3.5 text-[#F59E0B]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 022 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <input type="text" id="input-sisa" disabled class="w-full border border-gray-200 bg-gray-50 text-[#F59E0B] rounded-2xl pl-10 pr-4 py-3 text-[13px] font-bold"></div>
                    </div>
                    
                    <div class="border border-gray-100 rounded-2xl p-4 bg-white shadow-sm">
                        <label class="block text-[12px] font-bold text-gray-900 mb-2">Metode Pembayaran</label>
                        <div class="flex space-x-6">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" id="edit-metode-cash" name="edit_metode" value="Cash" class="w-4 h-4 text-[#0F47A1]">
                                <span class="text-[13px] font-medium text-gray-800">💵 Cash</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" id="edit-metode-transfer" name="edit_metode" value="Transfer" class="w-4 h-4 text-[#0F47A1]">
                                <span class="text-[13px] font-medium text-gray-800">💳 Transfer</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="space-y-3 pt-2">
                    <button onclick="saveEdit()" class="w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-semibold py-3.5 rounded-full shadow-md transition duration-200 text-[14px]">Simpan Perubahan</button>
                    <button onclick="switchView('review')" class="w-full bg-white border-2 border-[#0F47A1] text-[#0F47A1] hover:bg-gray-50 font-semibold py-3.5 rounded-full transition duration-200 text-[14px]">Batalkan</button>
                </div>
            </div>

            <div id="view-success" class="hidden flex-col items-center pt-8 pb-10 h-full">
                <div class="relative w-28 h-28 mb-6">
                    <div class="absolute inset-0 bg-[#ECFDF5] rounded-full animate-ping opacity-75"></div>
                    <div class="relative w-full h-full bg-[#10B981] rounded-full flex items-center justify-center text-white shadow-lg border-8 border-[#ECFDF5]">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </div>

                <h2 class="text-[20px] font-bold text-gray-900 mb-1">Faktur Berhasil Disimpan</h2>
                <p class="text-[13px] text-gray-500 mb-6">Data Faktur Berhasil disimpan.</p>

                <div class="w-full border border-gray-100 rounded-2xl p-5 shadow-sm bg-white space-y-4 mb-8">
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg><span class="text-[13px] font-bold">Nama Toko</span></div>
                        <span id="success-nama" class="text-[13px] font-bold text-gray-900 text-right">-</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg><span class="text-[13px] font-bold">Nomor Faktur</span></div>
                        <span id="success-nofaktur" class="text-[13px] font-medium text-gray-900 text-right">-</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg><span class="text-[13px] font-bold">Tanggal Faktur</span></div>
                        <span id="success-tanggal" class="text-[13px] font-medium text-gray-900 text-right">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg><span class="text-[13px] font-bold">Metode Pembayaran</span></div>
                        <span id="success-metode" class="text-[13px] font-medium text-gray-900 text-right">-</span>
                    </div>

                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5 text-[#0F47A1]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg><span class="text-[13px] font-bold">Total Tagihan</span></div>
                        <span id="success-tagihan" class="text-[14px] font-bold text-[#0F47A1] text-right">Rp0</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5 text-[#10B981]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span class="text-[13px] font-bold">Sudah Dibayar</span></div>
                        <span id="success-dibayar" class="text-[14px] font-bold text-[#10B981] text-right">Rp0</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5 text-[#F59E0B]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 022 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg><span class="text-[13px] font-bold">Sisa Tagihan</span></div>
                        <span id="success-sisa" class="text-[14px] font-bold text-[#F59E0B] text-right">Rp0</span>
                    </div>
                    <div class="flex justify-between items-center pt-1">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5 text-[#F59E0B]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span class="text-[13px] font-bold">Status</span></div>
                        <span id="success-status-badge" class="text-[11px] font-bold px-3 py-1 rounded">Belum Lunas</span>
                    </div>
                </div>

                <div class="w-full mt-auto space-y-3">
                    <button onclick="togglePhotoModal(true)" class="w-full bg-white border border-[#0F47A1] text-[#0F47A1] hover:bg-blue-50 font-semibold py-4 rounded-full shadow-sm transition duration-200 text-[14px]">Lihat Foto Faktur</button>
                    <button onclick="finishProcess()" class="w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-semibold py-4 rounded-full shadow-md transition duration-200 text-[14px]">Selesai</button>
                </div>
            </div>

        </div>
    </div>

    <div id="modal-photo" class="fixed inset-0 z-[60] hidden justify-center bg-black/50 backdrop-blur-sm">
        <div class="w-full max-w-md bg-white flex flex-col h-full shadow-2xl relative">
            
            <div class="px-6 pt-12 pb-4 flex items-center border-b border-gray-100 bg-white">
                <button onclick="togglePhotoModal(false)" class="text-[#0F47A1] hover:text-blue-900 transition p-2 -ml-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <h1 class="text-[18px] font-bold text-gray-900 flex-grow text-center pr-8">Foto Nota</h1>
            </div>
            
            <div class="flex-grow p-5 bg-gray-100 flex items-center justify-center overflow-hidden relative">
                
                <div id="loading-image" class="absolute text-[13px] font-bold text-gray-400 flex flex-col items-center">
                    <svg class="w-8 h-8 mb-2 animate-spin text-[#0F47A1]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Memuat Foto...
                </div>

                <img id="preview-image" src="" alt="Faktur" 
                     class="max-w-full max-h-full rounded-xl shadow-md object-contain relative z-10 hidden"
                     onload="document.getElementById('loading-image').classList.add('hidden'); this.classList.remove('hidden');"
                     onerror="this.classList.add('hidden'); document.getElementById('loading-image').innerHTML = '⚠️ Gagal memuat gambar.<br><span class=\'text-[10px] font-normal\'>URL tidak valid atau file terhapus.</span>';">
            </div>
            
            <div class="p-6 bg-white space-y-3 pb-8">
                <button onclick="togglePhotoModal(false)" class="w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-semibold py-3.5 rounded-full shadow-md transition duration-200 text-[14px]">Kembali</button>
            </div>
            
        </div>
    </div>

    @include('partials.loading')

    <script>
        let currentData = {
            nama_toko: '',
            nomor_faktur: '',
            tanggal_nota: '',
            total_tagihan: 0,
            total_dibayar: 0,
            sisa_tagihan: 0,
            metode_bayar: ''
        };
        let savedFotoUrl = '';

        const formatRp = (angka) => 'Rp' + parseInt(angka || 0).toLocaleString('id-ID');

        // INIT PAGE & BACA SESSION (Pencegah "Loading..." abadi)
        window.addEventListener('DOMContentLoaded', () => {
            const ocrDataString = sessionStorage.getItem('ocrData');

            // Jika data kosong, beri tahu user daripada macet
            if (!ocrDataString) {
                alert("Waduh, data hasil scan tidak ditemukan di memori! Silakan kembali dan scan ulang fotonya.");
                return;
            }

            try {
                const parsed = JSON.parse(ocrDataString);
                currentData.nama_toko = parsed.nama_toko || '-';
                currentData.nomor_faktur = parsed.nomor_faktur || '-';
                currentData.tanggal_nota = parsed.tanggal_nota || '-';
                currentData.total_tagihan = parseInt(parsed.total_tagihan) || 0;
                currentData.total_dibayar = 0;

                updateCalculations();
                renderReview(); // Fungsi ini yang mengubah teks "Loading..." jadi data asli
            } catch (e) {
                console.error("Gagal membaca data JSON:", e);
                alert("Data scan rusak. Harap ulangi proses scan.");
            }
        });

        // FUNGSI KONVERSI BASE64 KE BLOB
        function base64ToBlob(base64Data, contentType = 'image/png') {
            const base64Clean = base64Data.indexOf('base64,') !== -1
                ? base64Data.split('base64,')[1]
                : base64Data;
            const byteCharacters = atob(base64Clean);
            const byteArrays = [];
            for (let offset = 0; offset < byteCharacters.length; offset += 512) {
                const slice = byteCharacters.slice(offset, offset + 512);
                const byteNumbers = new Array(slice.length);
                for (let i = 0; i < slice.length; i++) {
                    byteNumbers[i] = slice.charCodeAt(i);
                }
                byteArrays.push(new Uint8Array(byteNumbers));
            }
            return new Blob(byteArrays, {type: contentType});
        }

        // FUNGSI PREVIEW MODAL
        function togglePhotoModal(show = true) {
            const modal = document.getElementById('modal-photo');
            const imgEl = document.getElementById('preview-image');
            const loaderEl = document.getElementById('loading-image');

            if (!modal) return;

            if (show) {
                if (savedFotoUrl) {
                    imgEl.classList.add('hidden');
                    if (loaderEl) loaderEl.classList.remove('hidden');

                    imgEl.src = savedFotoUrl;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                } else {
                    alert("Foto faktur sedang diproses atau tidak tersedia.");
                }
            } else {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                setTimeout(() => { imgEl.src = ""; }, 300);
            }
        }

        // --- FUNGSI-FUNGSI LOGIKA UI LAINNYA ---
        function updateCalculations() {
            currentData.sisa_tagihan = Math.max(0, currentData.total_tagihan - currentData.total_dibayar);
        }

        function setMetodePembayaran(value) {
            currentData.metode_bayar = value;
            document.getElementById('alert-metode').classList.add('hidden');
            const btnSave = document.getElementById('btn-save-database');
            btnSave.disabled = false;
            btnSave.className = "w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-semibold py-3.5 rounded-full shadow-md transition duration-200 text-[14px] cursor-pointer";
        }

        function renderReview() {
            document.getElementById('txt-nama').innerText = currentData.nama_toko;
            document.getElementById('txt-nofaktur').innerText = currentData.nomor_faktur;
            document.getElementById('txt-tanggal').innerText = currentData.tanggal_nota;
            document.getElementById('txt-tagihan').innerText = formatRp(currentData.total_tagihan);
            document.getElementById('txt-dibayar').innerText = formatRp(currentData.total_dibayar);
            document.getElementById('txt-sisa').innerText = formatRp(currentData.sisa_tagihan);

            if (currentData.metode_bayar) {
                const radios = document.getElementsByName('review_metode');
                radios.forEach(r => { if(r.value === currentData.metode_bayar) r.checked = true; });
                setMetodePembayaran(currentData.metode_bayar);
            }

            const badge = document.getElementById('badge-status');
            if (currentData.sisa_tagihan === 0 && currentData.total_tagihan > 0) {
                badge.className = "bg-[#ECFDF5] text-[#10B981] border border-[#10B981]/20 text-[13px] font-bold px-4 py-1.5 rounded-md";
                badge.innerText = "Lunas";
            } else {
                badge.className = "bg-[#FFF7ED] text-[#F59E0B] border border-[#F59E0B]/20 text-[13px] font-bold px-4 py-1.5 rounded-md";
                badge.innerText = "Belum Lunas";
            }
        }

        function switchView(view) {
            document.getElementById('view-review').classList.add('hidden');
            document.getElementById('view-edit').classList.add('hidden');
            document.getElementById('view-success').classList.add('hidden');
            document.getElementById('main-header').classList.remove('hidden');

            if (view === 'review') {
                document.getElementById('view-review').classList.remove('hidden');
            } else if (view === 'edit') {
                document.getElementById('input-nama').value = currentData.nama_toko;
                document.getElementById('input-nofaktur').value = currentData.nomor_faktur;
                document.getElementById('input-tanggal').value = currentData.tanggal_nota;
                document.getElementById('input-tagihan').value = currentData.total_tagihan;
                document.getElementById('input-dibayar').value = currentData.total_dibayar;
                calcSisaEdit();
                document.getElementById('view-edit').classList.remove('hidden');
            } else if (view === 'success') {
                document.getElementById('main-header').classList.add('hidden');
                document.getElementById('success-nama').innerText = currentData.nama_toko;
                document.getElementById('success-nofaktur').innerText = currentData.nomor_faktur;
                document.getElementById('success-tanggal').innerText = currentData.tanggal_nota;
                document.getElementById('success-metode').innerText = currentData.metode_bayar;
                document.getElementById('success-tagihan').innerText = formatRp(currentData.total_tagihan);
                document.getElementById('success-dibayar').innerText = formatRp(currentData.total_dibayar);
                document.getElementById('success-sisa').innerText = formatRp(currentData.sisa_tagihan);

                const badge = document.getElementById('success-status-badge');
                if (currentData.sisa_tagihan === 0 && currentData.total_tagihan > 0) {
                    badge.className = "text-[#10B981] bg-[#ECFDF5] text-[11px] font-bold px-3 py-1 rounded";
                    badge.innerText = "Lunas";
                } else {
                    badge.className = "text-[#F59E0B] bg-[#FFF7ED] text-[11px] font-bold px-3 py-1 rounded";
                    badge.innerText = "Belum Lunas";
                }
                document.getElementById('view-success').classList.remove('hidden');
                document.getElementById('view-success').classList.add('flex');
            }
        }

        function calcSisaEdit() {
            const tagihan = parseInt(document.getElementById('input-tagihan').value || 0);
            const dibayar = parseInt(document.getElementById('input-dibayar').value || 0);
            document.getElementById('input-sisa').value = formatRp(Math.max(0, tagihan - dibayar));
        }

        function saveEdit() {
            currentData.nama_toko = document.getElementById('input-nama').value;
            currentData.nomor_faktur = document.getElementById('input-nofaktur').value;
            currentData.tanggal_nota = document.getElementById('input-tanggal').value;
            currentData.total_tagihan = parseInt(document.getElementById('input-tagihan').value || 0);
            currentData.total_dibayar = parseInt(document.getElementById('input-dibayar').value || 0);
            const selectedMetode = document.querySelector('input[name="edit_metode"]:checked');
            if(selectedMetode) currentData.metode_bayar = selectedMetode.value;

            updateCalculations();
            renderReview();
            switchView('review');
        }

        // SUBMIT DATA & AUTO COMPRESS IMAGE
        document.getElementById('btn-save-database').addEventListener('click', async function() {
            if (typeof Loading !== 'undefined') Loading.show('Memproses dan mengunggah foto...');
            const btn = this;
            btn.disabled = true;

            const formData = new FormData();
            formData.append('nama_toko', currentData.nama_toko);
            formData.append('nomor_faktur', currentData.nomor_faktur);
            formData.append('tanggal_nota', currentData.tanggal_nota);
            formData.append('total_tagihan', currentData.total_tagihan);
            formData.append('total_dibayar', currentData.total_dibayar);
            formData.append('metode_bayar', currentData.metode_bayar);

            const ocrImage = sessionStorage.getItem('ocrImage');
            
            // Pengecekan ketat memori foto
            if (!ocrImage || ocrImage.length < 100) {
                alert("Foto tidak ditemukan di memori! Silakan kembali dan ulangi proses scan.");
                if (typeof Loading !== 'undefined') Loading.hide();
                btn.disabled = false;
                return;
            }

            try {
                // MESIN KOMPRESOR GAMBAR HTML5 CANVAS
                await new Promise((resolve, reject) => {
                    const img = new Image();
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        const MAX_WIDTH = 1200; // Resolusi aman baca teks
                        let width = img.width;
                        let height = img.height;
                        
                        // Perkecil dimensi jika terlalu besar
                        if (width > MAX_WIDTH) {
                            height = Math.round((height * MAX_WIDTH) / width);
                            width = MAX_WIDTH;
                        }
                        canvas.width = width;
                        canvas.height = height;
                        
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);
                        
                        // Ubah jadi JPEG dan kompres size-nya (Kualitas 70%)
                        canvas.toBlob((blob) => {
                            if (blob) {
                                formData.append('foto_file', blob, 'bukti_faktur.jpg');
                                resolve();
                            } else {
                                reject(new Error("Gagal konversi Canvas ke Blob"));
                            }
                        }, 'image/jpeg', 0.7); 
                    };
                    img.onerror = () => reject(new Error("Gagal meload gambar dari memori"));
                    
                    // Antisipasi jika murni Base64 tanpa header data URL
                    img.src = ocrImage.indexOf('base64,') === -1 ? 'data:image/jpeg;base64,' + ocrImage : ocrImage;
                });

                // KIRIM KE LARAVEL
                const response = await fetch("{{ route('sales.addFaktur') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();
                
                if (response.ok && result.success) {
                    savedFotoUrl = result.foto_url;
                    switchView('success');
                } else {
                    alert(result.message); // Akan memunculkan peringatan Jebakan Batman jika gagal
                }
            } catch (err) {
                console.error("Critical Error:", err);
                alert("Terjadi kesalahan sistem: " + err.message);
            } finally {
                if (typeof Loading !== 'undefined') Loading.hide();
                btn.disabled = false;
            }
        });

        function finishProcess() {
            sessionStorage.removeItem('ocrData');
            sessionStorage.removeItem('ocrImage');
            window.location.href = "{{ route('sales.home') }}";
        }
    </script>
</body>
</html>