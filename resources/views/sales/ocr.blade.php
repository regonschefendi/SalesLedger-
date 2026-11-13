<!DOCTYPE html>
<html lang="id">
<head>
    <x-head title="Unggah Nota - Sales Ledger" :csrf="true">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">
    </x-head>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative shadow-2xl overflow-hidden pb-10">
        
        <div id="main-header">
            <x-back-header
                title="Unggah Nota"
                :back-url="route('sales.home')"
                border-class="border-b border-gray-100"
            />
        </div>

        <div class="px-5 pt-5 overflow-y-auto flex-grow relative">

            <div id="view-review" class="space-y-4 pb-10 block">
                <div class="flex items-center space-x-3 bg-[#ECFDF5] border border-[#10B981]/30 p-3.5 rounded-xl">
                    <div class="w-7 h-7 bg-[#10B981] rounded-full flex items-center justify-center text-white flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <p class="text-[12px] font-bold text-[#10B981]">Nota berhasil dipindai</p>
                        <p class="text-[10px] text-gray-500">Data toko bisa diubah sebelum disimpan.</p>
                    </div>
                </div>

                <div class="relative z-50">
                    <label class="block text-[12px] font-bold text-gray-900 mb-1.5">Pilih Toko Pelanggan</label>
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3.5 top-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        
                        <input type="text" id="input-nama" onkeyup="searchTokoProcess(this.value)" autocomplete="off" placeholder="Ketik nama toko..." class="w-full border-2 border-blue-100 bg-blue-50/30 rounded-2xl pl-10 pr-10 py-3 text-[13px] font-bold text-gray-900 focus:ring-0 focus:border-[#0F47A1] transition outline-none">
                        
                        <button id="btn-clear-toko" type="button" onclick="clearTokoSelection()" class="hidden absolute right-3.5 top-3.5 text-red-500 hover:text-red-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <div id="dropdown-toko" class="hidden absolute w-full mt-1 bg-white border border-gray-100 rounded-xl shadow-lg max-h-48 overflow-y-auto z-50">
                        <ul id="list-toko" class="divide-y divide-gray-50"></ul>
                    </div>
                </div>

                <div class="flex justify-center my-2">
                    <span id="badge-status" class="bg-[#FFF7ED] text-[#F59E0B] border border-[#F59E0B]/20 text-[13px] font-bold px-4 py-1.5 rounded-md">Belum Lunas</span>
                </div>

                <div class="border border-gray-100 rounded-2xl p-5 shadow-sm bg-white space-y-4">
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg><span class="text-[13px] font-bold">Nama Toko</span></div>
                        <span id="txt-nama" class="text-[13px] font-bold text-gray-900 text-right">Memuat...</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg><span class="text-[13px] font-bold">Nomor Faktur</span></div>
                        <span id="txt-nofaktur" class="text-[13px] font-medium text-gray-900 text-right">Memuat...</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg><span class="text-[13px] font-bold">Tanggal Faktur</span></div>
                        <span id="txt-tanggal" class="text-[13px] font-medium text-gray-900 text-right">Memuat...</span>
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
                
                <div id="alert-toko-review" class="hidden flex items-center justify-center space-x-2 text-red-500 text-[12px] font-medium pt-1">
                    <div class="w-4 h-4 border border-red-500 rounded-full flex items-center justify-center font-bold text-[10px]">!</div>
                    <span id="alert-toko-msg">Toko tidak ditemukan. Klik Ubah Faktur.</span>
                </div>

                <div class="border border-gray-100 rounded-2xl p-4 bg-white shadow-sm">
                    <p class="text-[12px] font-bold text-gray-900 mb-3 flex items-center"><svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 022 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>Metode Pembayaran</p>
                    <div class="flex justify-center space-x-8">
                        <label class="flex items-center space-x-2 cursor-pointer select-none">
                            <input type="radio" name="review_metode" value="Cash" onchange="setMetodePembayaran(this.value)" class="w-4 h-4 text-[#0F47A1] focus:ring-[#0F47A1]">
                            <span class="text-[13px] font-semibold text-gray-800 flex items-center">💵 Tunai</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer select-none">
                            <input type="radio" name="review_metode" value="Transfer" onchange="setMetodePembayaran(this.value)" class="w-4 h-4 text-[#0F47A1] focus:ring-[#0F47A1]">
                            <span class="text-[13px] font-semibold text-gray-800 flex items-center">💳 Transfer</span>
                        </label>
                    </div>
                </div>

                <div class="space-y-3 pt-2">
                    <button onclick="switchView('edit')" class="w-full bg-white border-2 border-[#0F47A1] text-[#0F47A1] hover:bg-gray-50 font-semibold py-3.5 rounded-full transition duration-200 text-[14px]">Ubah Faktur</button>
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
                        <label class="block text-[12px] font-bold text-gray-900 mb-1.5">Nomor Faktur</label>
                        <div class="relative">
                            <svg class="w-4 h-4 absolute left-3.5 top-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <input type="text" id="input-nofaktur" class="w-full border border-gray-200 rounded-2xl pl-10 pr-4 py-3 text-[13px] font-medium focus:ring-2 focus:ring-[#0F47A1] focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[12px] font-bold text-gray-900 mb-1.5">Tanggal Faktur</label>
                        <div class="relative">
                            <!-- Icon Kalender -->
                            <div>
                                <svg class="w-4 h-4 absolute left-3.5 top-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <!-- Input Field Flatpickr -->
                            <input type="text" id="tanggal_faktur" name="tanggal_faktur" required
                                class="w-full border border-gray-200 rounded-2xl pl-10 pr-4 py-3 text-[13px] font-medium focus:ring-2 focus:ring-[#0F47A1] focus:outline-none bg-white"
                                placeholder="Pilih tanggal (dd/mm/yyyy)">
                        </div>
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
                </div>

                <div class="space-y-3 pt-2">
                    <button onclick="saveEdit()" class="w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-semibold py-3.5 rounded-full shadow-md transition duration-200 text-[14px]">Simpan Perubahan</button>
                    <button onclick="switchView('review')" class="w-full bg-white border-2 border-[#0F47A1] text-[#0F47A1] hover:bg-gray-50 font-semibold py-3.5 rounded-full transition duration-200 text-[14px]">Batal</button>
                </div>
            </div>

            <div id="view-success" class="hidden flex-col items-center pt-8 pb-10 h-full">
                <div class="mb-6">
                    <x-success-icon />
                </div>

                <h2 class="text-[20px] font-bold text-gray-900 mb-1">Faktur Berhasil Disimpan</h2>
                <p class="text-[13px] text-gray-500 mb-6">Data faktur berhasil disimpan.</p>

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
                     onerror="this.classList.add('hidden'); document.getElementById('loading-image').innerHTML = '⚠️ Gagal memuat gambar.<br><span class=\'text-[10px] font-normal\'>Tautan tidak valid atau file terhapus.</span>';">
            </div>
            
            <div class="p-6 bg-white space-y-3 pb-8">
                <button onclick="togglePhotoModal(false)" class="w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-semibold py-3.5 rounded-full shadow-md transition duration-200 text-[14px]">Kembali</button>
            </div>
            
        </div>
    </div>

    @include('partials.loading')

    <script>
        let currentData = {
            toko_id: null,  
            nama_toko: '',
            nomor_faktur: '',
            tanggal_nota: '',
            total_tagihan: 0,
            total_dibayar: 0,
            sisa_tagihan: 0,
            metode_bayar: ''
        };
        let savedFotoUrl = '';
        let tanggalPicker = null;

        const formatRp = (angka) => 'Rp' + parseInt(angka || 0, 10).toLocaleString('id-ID');

        function safeSessionGet(key) {
            try {
                return sessionStorage.getItem(key);
            } catch (e) {
                console.error('sessionStorage tidak dapat diakses:', e);
                return null;
            }
        }

        function formatTanggalHariIni() {
            const now = new Date();
            return String(now.getDate()).padStart(2, '0') + '/' + String(now.getMonth() + 1).padStart(2, '0') + '/' + now.getFullYear();
        }

        function isValidDateDmy(value) {
            if (!/^\d{2}\/\d{2}\/\d{4}$/.test(value || '')) return false;

            const [day, month, year] = value.split('/').map(Number);
            const date = new Date(year, month - 1, day);

            return date.getFullYear() === year
                && date.getMonth() === month - 1
                && date.getDate() === day;
        }

        // INIT PAGE & BACA SESSION (Pencegah "Memuat..." abadi)
        window.addEventListener('DOMContentLoaded', () => {
            const ocrDataString = safeSessionGet('ocrData');

            // Jika data kosong, beri tahu user daripada macet
            if (!ocrDataString) {
                alert("Data hasil pindai tidak ditemukan di memori! Silakan kembali dan pindai ulang fotonya.");
                return;
            }

            try {
                const parsed = JSON.parse(ocrDataString);
                currentData.nama_toko = String(parsed.nama_toko || '').trim();
                currentData.toko_id = null;
                currentData.nomor_faktur = parsed.nomor_faktur || '-';
                currentData.tanggal_nota = parsed.tanggal_nota || formatTanggalHariIni();
                currentData.total_tagihan = parseInt(parsed.total_tagihan, 10) || 0;
                currentData.total_dibayar = 0;

                updateCalculations();
                renderReview(); // Fungsi ini yang mengubah teks "Memuat..." jadi data asli

                if (parsed.is_manual === true) {
                    // Ganti teks banner biru di halaman edit biar relevan
                    const editBanner = document.querySelector('#view-edit .bg-blue-50 p');
                    if(editBanner) {
                        editBanner.innerText = "Mode Input Manual: Silakan isi nomor faktur dan total tagihan dari foto.";
                    }
                    
                    // Langsung otomatis buka form edit tanpa harus klik "Ubah Faktur"
                    switchView('edit');
                    
                    // Kasih info ringan ke user
                    setTimeout(() => {
                        alert("Silakan ketik data faktur, foto bukti nota tetap aman terlampir.");
                    }, 300);
                } else if (currentData.nama_toko !== '') {
                    // Nama toko hasil AI dicocokkan otomatis ke daftar toko admin.
                    document.getElementById('input-nama').value = currentData.nama_toko;
                    autoMatchToko(currentData.nama_toko);
                } else {
                    showTokoAlert('Nama toko tidak terbaca. Pilih toko secara manual di kolom pencarian.');
                }
            } catch (e) {
                console.error("Gagal membaca data JSON:", e);
                alert("Data hasil pindai tidak dapat dibaca. Silakan masukkan data faktur secara manual.");
                
                // Paksa render UI kosong dan buka form edit
                currentData.tanggal_nota = currentData.tanggal_nota || formatTanggalHariIni();
                renderReview();
                switchView('edit');
            }
        });

        // FUNGSI AUTO-MATCH TOKO DARI DATABASE
        async function autoMatchToko(keyword) {
            const query = String(keyword || '').trim();

            if (query.length < 2) {
                showTokoAlert('Nama toko tidak terbaca. Pilih toko secara manual di kolom pencarian.');
                return;
            }

            try {
                const response = await fetch(`{{ route('sales.toko.search') }}?q=${encodeURIComponent(query)}`, {
                    headers: { 'Accept': 'application/json' }
                });

                if (!response.ok) {
                    throw new Error('Pencarian toko gagal (' + response.status + ')');
                }

                const tokos = await response.json();

                if (Array.isArray(tokos) && tokos.length > 0) {
                    // Ambil toko urutan pertama yang paling mirip
                    selectToko(tokos[0].id, tokos[0].nama_toko || '');
                } else {
                    // Kalau AI ngawur dan tokonya nggak ada di DB
                    currentData.toko_id = null;
                    showTokoAlert(`Toko "${query}" tidak terdaftar. Pilih toko secara manual di kolom pencarian.`);
                }
            } catch (e) {
                console.error("Auto match gagal", e);
                showTokoAlert('Gagal mencocokkan toko. Pilih toko secara manual di kolom pencarian.');
            }
        }

        function showTokoAlert(msg) {
            document.getElementById('alert-toko-msg').innerText = msg;
            document.getElementById('alert-toko-review').classList.remove('hidden');
            document.getElementById('btn-save-database').disabled = true;
            document.getElementById('btn-save-database').className = "w-full bg-slate-400 text-white font-semibold py-3.5 rounded-full shadow-md transition duration-200 text-[14px] cursor-not-allowed";
        }

        function checkSaveButton() {
            const btnSave = document.getElementById('btn-save-database');
            if (currentData.toko_id && currentData.metode_bayar) {
                btnSave.disabled = false;
                btnSave.className = "w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-semibold py-3.5 rounded-full shadow-md transition duration-200 text-[14px] cursor-pointer";
            } else {
                btnSave.disabled = true;
                btnSave.className = "w-full bg-slate-400 text-white font-semibold py-3.5 rounded-full shadow-md transition duration-200 text-[14px] cursor-not-allowed";
            }
        }

        // LOGIKA KLIK HASIL PENCARIAN
        function selectToko(id, nama) {
            currentData.toko_id = id;
            currentData.nama_toko = nama || '';
            document.getElementById('input-nama').value = currentData.nama_toko;
            document.getElementById('txt-nama').innerText = currentData.nama_toko || '-';
            document.getElementById('dropdown-toko').classList.add('hidden');
            document.getElementById('alert-toko-review').classList.add('hidden');
            document.getElementById('btn-clear-toko').classList.remove('hidden');
            checkSaveButton();
        }

        function clearTokoSelection() {
            currentData.toko_id = null;
            currentData.nama_toko = '';
            document.getElementById('input-nama').value = '';
            document.getElementById('txt-nama').innerText = '-';
            document.getElementById('btn-clear-toko').classList.add('hidden');
            document.getElementById('input-nama').focus();
            checkSaveButton();
        }

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

        // METODE PEMBAYARAN DIKLIK DARI REVIEW PAGE
        function setMetodePembayaran(value) {
            currentData.metode_bayar = value;
            document.getElementById('alert-metode').classList.add('hidden');
            checkSaveButton();
        }

        // DROPDOWN PENCARIAN TOKO MANUAL (Di Halaman Edit)
        let searchTimeout;
        let searchRequestId = 0;

        async function searchTokoProcess(keyword) {
            const dropdown = document.getElementById('dropdown-toko');
            const listToko = document.getElementById('list-toko');
            const query = String(keyword || '').trim();
            
            currentData.toko_id = null;
            document.getElementById('btn-clear-toko').classList.add('hidden');

            if (query.length < 2) { dropdown.classList.add('hidden'); return; }

            clearTimeout(searchTimeout);
            const requestId = ++searchRequestId;

            searchTimeout = setTimeout(async () => {
                try {
                    const response = await fetch(`{{ route('sales.toko.search') }}?q=${encodeURIComponent(query)}`, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Pencarian toko gagal (' + response.status + ')');
                    }

                    const tokos = await response.json();

                    // Abaikan respons lama jika user sudah mengetik query baru.
                    if (requestId !== searchRequestId) return;

                    listToko.innerHTML = '';

                    if (Array.isArray(tokos) && tokos.length > 0) {
                        tokos.forEach(toko => {
                            const item = document.createElement('li');
                            item.className = 'p-3 hover:bg-blue-50 cursor-pointer transition';

                            const nama = document.createElement('p');
                            nama.className = 'text-[13px] font-bold text-gray-900';
                            nama.textContent = toko.nama_toko || '-';

                            const alamat = document.createElement('p');
                            alamat.className = 'text-[10px] text-gray-400 truncate';
                            alamat.textContent = toko.alamat || 'Alamat belum diisi';

                            item.appendChild(nama);
                            item.appendChild(alamat);
                            item.addEventListener('click', () => selectToko(toko.id, toko.nama_toko || ''));

                            listToko.appendChild(item);
                        });
                        dropdown.classList.remove('hidden');
                    } else {
                        listToko.innerHTML = '<li class="p-3 text-[12px] text-gray-500 text-center">Toko tidak ditemukan. Hubungi Admin.</li>';
                        dropdown.classList.remove('hidden');
                    }
                } catch (e) {
                    console.error(e);

                    if (requestId === searchRequestId) {
                        listToko.innerHTML = '<li class="p-3 text-[12px] text-red-500 text-center">Gagal memuat daftar toko. Coba lagi.</li>';
                        dropdown.classList.remove('hidden');
                    }
                }
            }, 300);
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
                document.getElementById('input-nofaktur').value = currentData.nomor_faktur === '-' ? '' : currentData.nomor_faktur;

                if (tanggalPicker) {
                    if (isValidDateDmy(currentData.tanggal_nota)) {
                        tanggalPicker.setDate(currentData.tanggal_nota, false, 'd/m/Y');
                    } else {
                        tanggalPicker.clear();
                    }
                } else {
                    document.getElementById('tanggal_faktur').value = currentData.tanggal_nota;
                }

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
            const tagihan = parseInt(document.getElementById('input-tagihan').value || 0, 10);
            const dibayar = parseInt(document.getElementById('input-dibayar').value || 0, 10);
            document.getElementById('input-sisa').value = formatRp(Math.max(0, tagihan - dibayar));
        }

        function saveEdit() {
            const nomorFaktur = document.getElementById('input-nofaktur').value.trim();
            const tanggal = document.getElementById('tanggal_faktur').value.trim();
            const tagihan = parseInt(document.getElementById('input-tagihan').value || 0, 10) || 0;
            const dibayar = parseInt(document.getElementById('input-dibayar').value || 0, 10) || 0;

            if (nomorFaktur === '') {
                alert('Nomor faktur wajib diisi.');
                return;
            }

            if (!isValidDateDmy(tanggal)) {
                alert('Tanggal faktur tidak valid. Gunakan format dd/mm/yyyy.');
                return;
            }

            if (tagihan < 0 || dibayar < 0) {
                alert('Nominal tagihan/pembayaran tidak boleh negatif.');
                return;
            }

            currentData.nomor_faktur = nomorFaktur;
            currentData.tanggal_nota = tanggal;
            currentData.total_tagihan = tagihan;
            currentData.total_dibayar = Math.min(dibayar, tagihan);

            updateCalculations();
            renderReview();
            checkSaveButton();
            switchView('review');
        }

        function extractErrorMessage(result, fallback) {
            if (!result) return fallback;
            if (result.message) return result.message;

            if (result.errors) {
                const first = Object.values(result.errors)[0];
                if (Array.isArray(first) && first.length > 0) return first[0];
                if (typeof first === 'string') return first;
            }

            return fallback;
        }

        // SUBMIT DATA
        document.getElementById('btn-save-database').addEventListener('click', async function() {
            const btn = this;

            if (btn.disabled) return;

            if (!currentData.toko_id) {
                showTokoAlert('Pilih toko pelanggan terlebih dahulu sebelum menyimpan.');
                return;
            }

            if (!currentData.metode_bayar) {
                alert('Pilih metode pembayaran terlebih dahulu.');
                return;
            }

            if (!isValidDateDmy(currentData.tanggal_nota)) {
                alert('Tanggal faktur tidak valid. Perbaiki lewat tombol Ubah Faktur.');
                switchView('edit');
                return;
            }

            if (typeof Loading !== 'undefined') Loading.show('Memproses dan mengunggah foto...');
            btn.disabled = true;

            try {
                const ocrImage = safeSessionGet('ocrImage');

                // Pengecekan ketat memori foto
                if (!ocrImage || ocrImage.length < 100) {
                    alert("Foto tidak ditemukan di memori! Silakan kembali dan ulangi proses pindai.");
                    return;
                }

                let fotoBlob;

                try {
                    // Foto sudah dikompres saat proses pindai, jadi cukup
                    // dikonversi ke Blob tanpa dikompres ulang.
                    fotoBlob = base64ToBlob(ocrImage, 'image/jpeg');
                } catch (e) {
                    throw new Error('Foto di memori rusak. Silakan ulangi proses pindai.');
                }

                if (!fotoBlob || fotoBlob.size < 100) {
                    throw new Error('Foto di memori rusak. Silakan ulangi proses pindai.');
                }

                const formData = new FormData();
                formData.append('toko_id', currentData.toko_id);
                formData.append('nomor_faktur', currentData.nomor_faktur);
                formData.append('tanggal_nota', currentData.tanggal_nota);
                formData.append('total_tagihan', currentData.total_tagihan);
                formData.append('total_dibayar', currentData.total_dibayar);
                formData.append('metode_bayar', currentData.metode_bayar);
                formData.append('foto_file', fotoBlob, 'bukti_faktur.jpg');

                const controller = typeof AbortController !== 'undefined' ? new AbortController() : null;
                const timeoutId = controller ? setTimeout(() => controller.abort(), 90000) : null;

                let response;

                try {
                    // KIRIM KE LARAVEL
                    response = await fetch("{{ route('sales.addFaktur') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData,
                        signal: controller ? controller.signal : undefined
                    });
                } finally {
                    clearTimeout(timeoutId);
                }

                const raw = await response.text();
                let result = null;

                try {
                    result = JSON.parse(raw);
                } catch (e) {
                    result = null;
                }

                if (response.ok && result && result.success) {
                    savedFotoUrl = result.foto_url;
                    switchView('success');
                } else {
                    alert(extractErrorMessage(result, 'Gagal menyimpan faktur. Silakan coba lagi.'));
                }
            } catch (err) {
                console.error("Critical Error:", err);

                if (err.name === 'AbortError') {
                    alert('Koneksi terlalu lama. Periksa internet lalu coba lagi.');
                } else {
                    alert(err.message || 'Terjadi kesalahan sistem.');
                }
            } finally {
                if (typeof Loading !== 'undefined') Loading.hide();
                btn.disabled = false;
            }
        });

        function finishProcess() {
            try {
                sessionStorage.removeItem('ocrData');
                sessionStorage.removeItem('ocrImage');
            } catch (e) {
                console.error(e);
            }

            window.location.href = "{{ route('sales.home') }}";
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- Locale Bahasa Indonesia -->
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof flatpickr !== 'undefined') {
                tanggalPicker = flatpickr("#tanggal_faktur", {
                    dateFormat: "d/m/Y", // Memaksa format dd/MM/yyyy
                    locale: "id",        // Pakai bahasa Indonesia
                    allowInput: true,    // Izinkan ketik manual tapi difilter
                    disableMobile: true  // Memaksa pakai UI Kalender Flatpickr, bukan bawaan browser HP
                });
            }
        });

        // Tutup dropdown toko saat user klik di luar area pencarian.
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('dropdown-toko');
            const input = document.getElementById('input-nama');

            if (!dropdown || !input) return;

            if (!dropdown.contains(event.target) && !input.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>