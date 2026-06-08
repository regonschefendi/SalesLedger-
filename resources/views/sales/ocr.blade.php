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

            <div id="view-review" class="space-y-4 pb-10 block animate-fadeIn">
                <div class="flex items-center space-x-3 bg-[#ECFDF5] border border-[#10B981]/30 p-3.5 rounded-xl">
                    <div class="w-7 h-7 bg-[#10B981] rounded-full flex items-center justify-center text-white flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <p class="text-[12px] font-bold text-[#10B981]">Scan nota berhasil dibaca</p>
                        <p class="text-[10px] text-gray-500">Data toko bisa di edit sebelum disimpan.</p>
                    </div>
                </div>

                <div class="flex justify-center my-4">
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

                <div class="space-y-3 pt-4">
                    <button id="btn-save-database" class="w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-semibold py-3.5 rounded-full shadow-md transition duration-200 text-[14px]">Simpan Data</button>
                    <button onclick="switchView('edit')" class="w-full bg-white border-2 border-[#0F47A1] text-[#0F47A1] hover:bg-gray-50 font-semibold py-3.5 rounded-full transition duration-200 text-[14px]">Edit Faktur</button>
                </div>

                <div class="flex justify-center pt-4">
                    <button onclick="togglePhotoModal()" class="flex items-center space-x-2 border border-[#0F47A1] text-[#0F47A1] px-5 py-2.5 rounded-full text-[13px] font-bold hover:bg-blue-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>Lihat Foto Faktur</span>
                    </button>
                </div>
                <p class="text-center text-[10px] text-gray-400 mt-4"><span class="text-[#10B981]">✓</span> Periksa data terlebih dahulu sebelum menyimpan.</p>
            </div>

            <div id="view-edit" class="hidden space-y-4 pb-10 animate-fadeIn">
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
                        <div class="relative"><svg class="w-4 h-4 absolute left-3.5 top-3.5 text-[#F59E0B]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <input type="text" id="input-sisa" disabled class="w-full border border-gray-200 bg-gray-50 text-[#F59E0B] rounded-2xl pl-10 pr-4 py-3 text-[13px] font-bold"></div>
                    </div>
                </div>

                <div class="flex justify-center py-2">
                    <button onclick="togglePhotoModal()" class="flex items-center space-x-2 border border-[#0F47A1] text-[#0F47A1] px-5 py-2.5 rounded-full text-[13px] font-bold hover:bg-blue-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>Lihat Foto Faktur</span>
                    </button>
                </div>

                <div class="space-y-3 pt-2">
                    <button onclick="saveEdit()" class="w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-semibold py-3.5 rounded-full shadow-md transition duration-200 text-[14px]">Simpan</button>
                    <button onclick="switchView('review')" class="w-full bg-white border-2 border-[#0F47A1] text-[#0F47A1] hover:bg-gray-50 font-semibold py-3.5 rounded-full transition duration-200 text-[14px]">Batalkan</button>
                </div>
            </div>

            <div id="view-success" class="hidden flex-col items-center pt-8 pb-10 animate-fadeIn h-full">
                <div class="relative w-28 h-28 mb-6">
                    <div class="absolute inset-0 bg-[#ECFDF5] rounded-full animate-ping opacity-75"></div>
                    <div class="relative w-full h-full bg-[#10B981] rounded-full flex items-center justify-center text-white shadow-lg border-8 border-[#ECFDF5]">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </div>

                <h2 class="text-[20px] font-bold text-gray-900 mb-1">Faktur Berhasil Disimpan</h2>
                <p class="text-[13px] text-gray-500 mb-8">Data Faktur Berhasil disimpan.</p>

                <div class="w-full border border-gray-100 rounded-2xl p-5 shadow-sm bg-white space-y-4 mb-8" id="success-card-content">
                    </div>

                <div class="w-full mt-auto">
                    <button onclick="finishProcess()" class="w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-semibold py-4 rounded-full shadow-md transition duration-200 text-[14px]">Selesai</button>
                </div>
            </div>

        </div>
    </div>

    <div id="modal-photo" class="fixed inset-0 z-50 hidden bg-white flex-col">
        <div class="px-6 pt-12 pb-4 flex items-center border-b border-gray-100">
            <button onclick="togglePhotoModal()" class="text-[#0F47A1] hover:text-blue-900 transition p-2 -ml-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <h1 class="text-[18px] font-bold text-gray-900 flex-grow text-center pr-8">Foto Nota</h1>
        </div>
        <div class="flex-grow p-5 bg-gray-100 flex items-center justify-center">
            <img id="preview-image" src="" alt="Faktur" class="max-w-full max-h-full rounded-xl shadow-md object-contain">
        </div>
        <div class="p-6 bg-white space-y-3">
            <button onclick="togglePhotoModal()" class="w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-semibold py-3.5 rounded-full shadow-md transition duration-200 text-[14px]">Kembali</button>
        </div>
    </div>

    <script>
        // State Management Data
        let currentData = {
            nama_toko: '',
            nomor_faktur: '',
            tanggal_nota: '',
            total_tagihan: 0,
            total_dibayar: 0,
            sisa_tagihan: 0
        };

        // Format Uang
        const formatRp = (angka) => 'Rp' + parseInt(angka || 0).toLocaleString('id-ID');

        window.addEventListener('DOMContentLoaded', () => {
            const ocrDataString = sessionStorage.getItem('ocrData');
            const ocrImage = sessionStorage.getItem('ocrImage');
            
            if (ocrImage) document.getElementById('preview-image').src = ocrImage;

            if (ocrDataString) {
                try {
                    const parsed = JSON.parse(ocrDataString);
                    currentData.nama_toko = parsed.nama_toko || '';
                    currentData.nomor_faktur = parsed.nomor_faktur || '';
                    currentData.tanggal_nota = parsed.tanggal_nota || '';
                    currentData.total_tagihan = parseInt(parsed.total_tagihan) || 0;
                    currentData.total_dibayar = 0; // Default bayar awal 0
                    updateCalculations();
                    renderReview();
                } catch (e) {
                    console.error("Gagal parse data");
                }
            }
        });

        function updateCalculations() {
            currentData.sisa_tagihan = currentData.total_tagihan - currentData.total_dibayar;
            if(currentData.sisa_tagihan < 0) currentData.sisa_tagihan = 0;
        }

        // Render Data ke Tampilan Review (State 1)
        function renderReview() {
            document.getElementById('txt-nama').innerText = currentData.nama_toko;
            document.getElementById('txt-nofaktur').innerText = currentData.nomor_faktur;
            document.getElementById('txt-tanggal').innerText = currentData.tanggal_nota;
            document.getElementById('txt-tagihan').innerText = formatRp(currentData.total_tagihan);
            document.getElementById('txt-dibayar').innerText = formatRp(currentData.total_dibayar);
            document.getElementById('txt-sisa').innerText = formatRp(currentData.sisa_tagihan);

            const badge = document.getElementById('badge-status');
            if (currentData.sisa_tagihan === 0 && currentData.total_tagihan > 0) {
                badge.className = "bg-[#ECFDF5] text-[#10B981] border border-[#10B981]/20 text-[13px] font-bold px-4 py-1.5 rounded-md";
                badge.innerText = "Lunas";
            } else {
                badge.className = "bg-[#FFF7ED] text-[#F59E0B] border border-[#F59E0B]/20 text-[13px] font-bold px-4 py-1.5 rounded-md";
                badge.innerText = "Belum Lunas";
            }
        }

        // Siapkan input pas masuk form Edit (State 2)
        function prepEditForm() {
            document.getElementById('input-nama').value = currentData.nama_toko;
            document.getElementById('input-nofaktur').value = currentData.nomor_faktur;
            document.getElementById('input-tanggal').value = currentData.tanggal_nota;
            document.getElementById('input-tagihan').value = currentData.total_tagihan;
            document.getElementById('input-dibayar').value = currentData.total_dibayar;
            calcSisaEdit();
        }

        // Realtime hitung sisa pas ngetik di Edit form
        function calcSisaEdit() {
            const tagihan = parseInt(document.getElementById('input-tagihan').value || 0);
            const dibayar = parseInt(document.getElementById('input-dibayar').value || 0);
            let sisa = tagihan - dibayar;
            if(sisa < 0) sisa = 0;
            document.getElementById('input-sisa').value = formatRp(sisa);
        }

        // Simpan Hasil Edit manual ke State
        function saveEdit() {
            currentData.nama_toko = document.getElementById('input-nama').value;
            currentData.nomor_faktur = document.getElementById('input-nofaktur').value;
            currentData.tanggal_nota = document.getElementById('input-tanggal').value;
            currentData.total_tagihan = parseInt(document.getElementById('input-tagihan').value || 0);
            currentData.total_dibayar = parseInt(document.getElementById('input-dibayar').value || 0);
            
            updateCalculations();
            renderReview();
            switchView('review');
        }

        // Switcher Antar Layar
        function switchView(view) {
            document.getElementById('view-review').classList.add('hidden');
            document.getElementById('view-edit').classList.add('hidden');
            document.getElementById('view-success').classList.add('hidden');
            document.getElementById('main-header').classList.remove('hidden');

            if (view === 'review') {
                document.getElementById('view-review').classList.remove('hidden');
            } else if (view === 'edit') {
                prepEditForm();
                document.getElementById('view-edit').classList.remove('hidden');
            } else if (view === 'success') {
                document.getElementById('main-header').classList.add('hidden'); // Sembunyikan header atas
                // Copy isi card review ke layar success
                const cardHtml = document.querySelector('#view-review > .border-gray-100.p-5').innerHTML;
                document.getElementById('success-card-content').innerHTML = cardHtml;
                // Copy badge status juga
                const badgeHtml = document.querySelector('#badge-status').outerHTML;
                document.getElementById('success-card-content').innerHTML += `<div class="flex justify-between items-center pt-3 border-t border-gray-50"><div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5 text-[#F59E0B]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span class="text-[13px] font-bold">Status</span></div>${badgeHtml}</div>`;
                
                document.getElementById('view-success').classList.remove('hidden');
                document.getElementById('view-success').classList.add('flex');
            }
        }

        // Toggle Foto Fullscreen
        function togglePhotoModal() {
            const modal = document.getElementById('modal-photo');
            if(modal.classList.contains('hidden')){
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } else {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        // Submit to Database via AJAX
        document.getElementById('btn-save-database').addEventListener('click', async function() {
            const btn = this;
            const originalText = btn.innerText;
            btn.innerText = "Menyimpan...";
            btn.disabled = true;

            const formData = new FormData();
            formData.append('nama_toko', currentData.nama_toko);
            formData.append('nomor_faktur', currentData.nomor_faktur);
            formData.append('tanggal_nota', currentData.tanggal_nota);
            formData.append('total_tagihan', currentData.total_tagihan);
            formData.append('total_dibayar', currentData.total_dibayar);

            try {
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
                    switchView('success'); // Sukses, tampilkan layar Hasil Nota
                } else {
                    alert("Gagal menyimpan data: " + result.message);
                }
            } catch (err) {
                alert("Kesalahan jaringan.");
            } finally {
                btn.innerText = originalText;
                btn.disabled = false;
            }
        });

        // Tombol Selesai dari layar Success
        function finishProcess() {
            sessionStorage.removeItem('ocrData');
            sessionStorage.removeItem('ocrImage');
            window.location.href = "{{ route('sales.home') }}";
        }
    </script>
</body>
</html>