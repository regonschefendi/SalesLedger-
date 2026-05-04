<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Sales Ledger - Scan Faktur</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50 text-gray-800 pb-10">

    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white shadow-xl relative">
        <!-- Header -->
        <div class="bg-blue-600 text-white p-5 rounded-b-3xl shadow-md z-10">
            <h1 class="text-2xl font-bold text-center">Data Entry Faktur</h1>
            <p class="text-center text-blue-100 text-sm mt-1">Scan otomatis dengan AI</p>
        </div>

        <div class="p-6 flex-grow space-y-8">
            
            <!-- BAGIAN 1: INPUT GAMBAR -->
            <div class="space-y-3">
                <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wider">1. Upload Faktur</h2>
                <div class="grid grid-cols-2 gap-4">
                    <label class="cursor-pointer border-2 border-dashed border-blue-300 bg-blue-50 hover:bg-blue-100 rounded-xl p-4 flex flex-col items-center justify-center text-center transition">
                        <input type="file" id="cameraInput" accept="image/*" capture="environment" class="hidden" onchange="processImage(this)">
                        <svg class="h-8 w-8 text-blue-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                        <span class="text-xs font-semibold text-blue-700">Kamera</span>
                    </label>

                    <label class="cursor-pointer border-2 border-dashed border-gray-300 bg-gray-50 hover:bg-gray-100 rounded-xl p-4 flex flex-col items-center justify-center text-center transition">
                        <input type="file" id="galleryInput" accept="image/*" class="hidden" onchange="processImage(this)">
                        <svg class="h-8 w-8 text-gray-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="text-xs font-semibold text-gray-700">Galeri</span>
                    </label>
                </div>
            </div>

            <!-- ANIMASI LOADING -->
            <div id="loadingIndicator" class="hidden flex-col items-center justify-center py-4">
                <svg class="animate-spin h-8 w-8 text-blue-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <p class="text-sm text-gray-500 font-medium animate-pulse">AI sedang membaca faktur...</p>
            </div>

            <!-- BAGIAN 2: FORM DATA (Auto-fill & Manual) -->
            <div id="formSection" class="space-y-4 opacity-50 pointer-events-none transition-opacity duration-300">
                <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wider flex items-center justify-between">
                    2. Verifikasi Data 
                    <span id="aiBadge" class="hidden bg-green-100 text-green-700 text-[10px] px-2 py-1 rounded-full">Auto-filled by AI</span>
                </h2>

                <form action="#" method="POST" class="space-y-4">
                    <!-- Kolom Hasil AI (Bisa diedit manual) -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Toko / Penerima</label>
                        <input type="text" id="input_nama_toko" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Nota</label>
                            <input type="text" id="input_tanggal_nota" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Total Tagihan (Rp)</label>
                            <input type="number" id="input_total_tagihan" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                        </div>
                    </div>

                    <!-- Kolom Tambahan (Wajib isi manual) -->
                    <div class="pt-4 border-t border-gray-200">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Catatan Tambahan (Manual)</label>
                        <textarea id="input_catatan" rows="2" placeholder="Tulis info tambahan jika ada..." class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition"></textarea>
                    </div>

                    <button type="button" class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg transition duration-200">
                        Simpan ke Database
                    </button>
                </form>
            </div>

        </div>
    </div>

    <!-- Script AJAX untuk lempar foto ke Backend tanpa reload -->
    <script>
        async function processImage(inputElement) {
            if (!inputElement.files || inputElement.files.length === 0) return;

            const file = inputElement.files[0];
            const formData = new FormData();
            formData.append('image', file);

            // Tampilkan loading, sembunyikan form sementara
            document.getElementById('loadingIndicator').classList.remove('hidden');
            document.getElementById('loadingIndicator').classList.add('flex');
            document.getElementById('formSection').classList.add('opacity-50', 'pointer-events-none');
            document.getElementById('aiBadge').classList.add('hidden');

            try {
                const response = await fetch('/api/scan', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // Isi form dengan data dari AI
                    document.getElementById('input_nama_toko').value = result.data.nama_toko;
                    document.getElementById('input_tanggal_nota').value = result.data.tanggal_nota;
                    document.getElementById('input_total_tagihan').value = result.data.total_tagihan;

                    // Aktifkan form biar bisa diedit user
                    document.getElementById('formSection').classList.remove('opacity-50', 'pointer-events-none');
                    document.getElementById('aiBadge').classList.remove('hidden');
                } else {
                    alert('Gagal: ' + result.message);
                }
            } catch (error) {
                alert('Terjadi kesalahan jaringan/sistem.');
                console.error(error);
            } finally {
                // Sembunyikan loading
                document.getElementById('loadingIndicator').classList.add('hidden');
                document.getElementById('loadingIndicator').classList.remove('flex');
                // Reset input file biar bisa upload foto yang sama lagi kalo gagal
                inputElement.value = ''; 
            }
        }
    </script>
</body>
</html>