<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Tambah Toko - Sales Ledger</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative shadow-2xl overflow-hidden pb-24">
        
        <div class="px-6 pt-12 pb-4 flex items-center bg-white border-b border-gray-50 z-10 relative">
            <button onclick="handleBack()" class="text-[#0F47A1] p-2 -ml-2 hover:text-blue-900 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <h1 id="header-title" class="text-[18px] font-bold text-gray-900 flex-grow text-center pr-8">Tambah Toko</h1>
        </div>

        <div class="px-5 pt-4 flex-grow overflow-y-auto no-scrollbar">
            
            <div id="view-form" class="space-y-4 block">
                <div>
                    <h2 class="text-[20px] font-bold text-gray-900 mb-1">Tambah Toko</h2>
                    <p class="text-[13px] text-gray-500 mb-6">Menambah Daftar Toko</p>
                </div>

                <form id="toko-form" class="space-y-4">
                    <div><label class="block text-[13px] font-bold text-gray-900 mb-1.5">Nama Toko</label><input type="text" id="in-nama" required placeholder="Masukkan nama toko" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-[13px] focus:ring-2 focus:ring-[#0F47A1] focus:outline-none"></div>
                    <div><label class="block text-[13px] font-bold text-gray-900 mb-1.5">Nomor Telepon</label><input type="tel" id="in-telp" placeholder="Masukkan nomor telepon" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-[13px] focus:ring-2 focus:ring-[#0F47A1] focus:outline-none"></div>
                    <div><label class="block text-[13px] font-bold text-gray-900 mb-1.5">Alamat</label><input type="text" id="in-alamat" placeholder="Masukkan alamat toko" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-[13px] focus:ring-2 focus:ring-[#0F47A1] focus:outline-none"></div>
                    <div><label class="block text-[13px] font-bold text-gray-900 mb-1.5">Provinsi</label><input type="text" id="in-provinsi" placeholder="Masukkan nama provinsi" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-[13px] focus:ring-2 focus:ring-[#0F47A1] focus:outline-none"></div>
                    <div><label class="block text-[13px] font-bold text-gray-900 mb-1.5">Kabupaten / Kota</label><input type="text" id="in-kota" placeholder="Masukkan nama kabupaten / kota" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-[13px] focus:ring-2 focus:ring-[#0F47A1] focus:outline-none"></div>
                    <div><label class="block text-[13px] font-bold text-gray-900 mb-1.5">Kode Pos</label><input type="number" id="in-kodepos" placeholder="Masukkan kode pos" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-[13px] focus:ring-2 focus:ring-[#0F47A1] focus:outline-none"></div>
                </form>

                <div class="flex items-center space-x-3 bg-blue-50 border border-blue-100 p-4 rounded-xl mt-6">
                    <div class="w-6 h-6 border-2 border-[#0F47A1] text-[#0F47A1] rounded-full flex items-center justify-center font-bold text-[12px] flex-shrink-0">!</div>
                    <p class="text-[12px] font-bold text-[#0F47A1]">Lengkapi Data toko dengan benar sebelum disimpan.</p>
                </div>
            </div>

            <div id="view-verify" class="hidden space-y-4">
                <h2 id="v-title-nama" class="text-[20px] font-bold text-gray-900 text-center mb-6">Toko Maju Jaya</h2>
                
                <div class="border border-gray-100 rounded-2xl p-5 bg-white shadow-sm space-y-4">
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5 text-[#10B981]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16h16M4 20h16M4 8h16M4 12h16"></path></svg><span class="text-[13px] font-medium">Nama Toko</span></div>
                        <span id="v-nama" class="text-[13px] font-bold text-gray-900">-</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.72l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.72.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg><span class="text-[13px] font-medium">Nomor Telepon</span></div>
                        <span id="v-telp" class="text-[13px] font-semibold text-gray-900">-</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"></path></svg><span class="text-[13px] font-medium">Alamat</span></div>
                        <span id="v-alamat" class="text-[13px] font-semibold text-gray-900 truncate max-w-[150px]">-</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5 text-[#F59E0B]" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8"></circle></svg><span class="text-[13px] font-medium">Provinsi</span></div>
                        <span id="v-provinsi" class="text-[13px] font-semibold text-gray-900">-</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5 text-[#EAB308]" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8"></circle></svg><span class="text-[13px] font-medium">Kabupaten / Kota</span></div>
                        <span id="v-kota" class="text-[13px] font-semibold text-gray-900">-</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-3 text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg><span class="text-[13px] font-medium">Kode Pos</span></div>
                        <span id="v-kodepos" class="text-[13px] font-semibold text-gray-900">-</span>
                    </div>
                </div>
            </div>

            <div id="view-success" class="hidden flex-col items-center pt-4">
                <div class="relative w-24 h-24 mb-4">
                    <div class="absolute inset-0 bg-[#ECFDF5] rounded-full animate-ping opacity-75"></div>
                    <div class="relative w-full h-full bg-[#10B981] rounded-full flex items-center justify-center text-white shadow-lg border-[6px] border-[#ECFDF5]">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="3.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </div>
                <h2 class="text-[20px] font-bold text-gray-900 mb-1">Toko Berhasil Disimpan</h2>
                <p class="text-[13px] text-gray-500 mb-6">Data Toko Berhasil disimpan.</p>
                
                <div class="w-full border border-gray-100 rounded-2xl p-5 bg-white shadow-sm space-y-4" id="success-summary">
                    </div>
            </div>

        </div>

        <div class="fixed bottom-0 max-w-md mx-auto inset-x-0 bg-white border-t border-gray-100 p-5 z-20">
            <button id="btn-action" onclick="handleNext()" class="w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-bold py-4 rounded-xl shadow-md transition duration-200 text-[14px]">Simpan</button>
        </div>
    </div>

    @include('partials.loading')

    <script>
        let currentState = 'form';
        let storeData = {};

        function handleNext() {
            if (currentState === 'form') {
                const inputNama = document.getElementById('in-nama').value;
                if (!inputNama) return alert('Nama Toko wajib diisi!');
                
                storeData = {
                    nama_toko: inputNama,
                    no_telp: document.getElementById('in-telp').value || '-',
                    alamat: document.getElementById('in-alamat').value || '-',
                    provinsi: document.getElementById('in-provinsi').value || '-',
                    kota: document.getElementById('in-kota').value || '-',
                    kode_pos: document.getElementById('in-kodepos').value || '-',
                };

                // Isi data verify
                document.getElementById('v-title-nama').innerText = storeData.nama_toko;
                document.getElementById('v-nama').innerText = storeData.nama_toko;
                document.getElementById('v-telp').innerText = storeData.no_telp;
                document.getElementById('v-alamat').innerText = storeData.alamat;
                document.getElementById('v-provinsi').innerText = storeData.provinsi;
                document.getElementById('v-kota').innerText = storeData.kota;
                document.getElementById('v-kodepos').innerText = storeData.kode_pos;

                switchView('verify');
            } 
            else if (currentState === 'verify') {
                submitData();
            }
            else if (currentState === 'success') {
                window.location.href = "/admin/toko"; // Kembali ke list toko
            }
        }

        function handleBack() {
            if (currentState === 'form') { window.history.back(); }
            else if (currentState === 'verify') { switchView('form'); }
            else if (currentState === 'success') { window.location.href = "/admin/toko"; }
        }

        function switchView(state) {
            currentState = state;
            document.getElementById('view-form').classList.add('hidden');
            document.getElementById('view-verify').classList.add('hidden');
            document.getElementById('view-success').classList.add('hidden');
            
            const btn = document.getElementById('btn-action');
            const title = document.getElementById('header-title');

            if (state === 'form') {
                document.getElementById('view-form').classList.remove('hidden');
                btn.innerText = 'Simpan';
                title.innerText = 'Tambah Toko';
            } else if (state === 'verify') {
                document.getElementById('view-verify').classList.remove('hidden');
                btn.innerText = 'Simpan';
                title.innerText = 'Detail Toko';
            } else if (state === 'success') {
                document.getElementById('view-success').classList.remove('hidden');
                document.getElementById('success-summary').innerHTML = document.querySelector('#view-verify .bg-white').innerHTML;
                btn.innerText = 'Selesai';
                title.classList.add('hidden'); // Sembunyikan title di header pas sukses
            }
        }

        async function submitData() {
            if(typeof Loading !== 'undefined') Loading.show('Menyimpan data toko...');
            document.getElementById('btn-action').disabled = true;

            try {
                // Hapus dash (-) yang tadi dipakai untuk visual empty state sebelum di POST
                const postData = {};
                for (const key in storeData) {
                    postData[key] = storeData[key] === '-' ? '' : storeData[key];
                }

                const response = await fetch('/admin/toko/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(postData)
                });
                
                const result = await response.json();
                if (response.ok && result.success) {
                    switchView('success');
                } else {
                    alert('Gagal menyimpan: ' + (result.message || 'Kesalahan sistem'));
                }
            } catch (err) {
                alert('Terjadi kesalahan jaringan.');
            } finally {
                if(typeof Loading !== 'undefined') Loading.hide();
                document.getElementById('btn-action').disabled = false;
            }
        }
    </script>
</body>
</html>