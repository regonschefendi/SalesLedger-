<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Sales Home - Sales Ledger</title>
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#ffffff">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>
        body { font-family: 'Inter', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative pb-28 shadow-2xl overflow-hidden">
        
        <div class="px-6 pt-12 pb-4 flex justify-between items-center bg-white">
            <div>
                <h1 class="text-[22px] font-bold text-gray-900">Halo, {{ $nama_sales }}</h1>
                <p class="text-[13px] text-gray-500 mt-1">Semangat bekerja hari ini !</p>
            </div>
            <div class="flex items-center space-x-3">
                <button class="w-10 h-10 bg-white rounded-full border border-gray-200 flex items-center justify-center text-gray-800 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </button>
                <a href="{{ route('sales.profile.index') }}" class="p-2 bg-white rounded-full border border-gray-100 shadow-sm">
                    <svg class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></svg>
                </a>
            </div>
        </div>

        <div class="px-5 space-y-4 flex-grow overflow-y-auto no-scrollbar pb-10">
            
            <div class="border border-gray-100 rounded-2xl p-5 shadow-sm bg-white">
                <h2 class="text-[14px] font-semibold text-gray-800 mb-4">Ringkasan Laporan Saya</h2>

                <div class="flex items-center mb-5">
                    <div class="w-12 h-12 bg-[#F0F5FF] rounded-full flex items-center justify-center mr-4 text-[#0F47A1]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[13px] text-gray-800 font-medium">Total Nota</p>
                        <p class="text-[18px] font-bold text-[#0F47A1]">{{ $total_nota }} Nota</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-5">
                    <div class="border border-gray-100 rounded-2xl p-3 shadow-sm bg-white flex flex-col justify-center">
                        <div class="flex items-center space-x-2 mb-2">
                            <div class="w-7 h-7 bg-[#ECFDF5] rounded-md flex items-center justify-center text-[#10B981]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <p class="text-[12px] text-gray-800 font-medium">Total Tagihan</p>
                        </div>
                        <p class="text-[15px] font-bold text-[#10B981]">Rp{{ number_format($total_tagihan, 0, ',', '.') }}</p>
                    </div>
                    <div class="border border-gray-100 rounded-2xl p-3 shadow-sm bg-white flex flex-col justify-center">
                        <div class="flex items-center space-x-2 mb-2">
                            <div class="w-7 h-7 bg-[#F0F5FF] rounded-md flex items-center justify-center text-[#0F47A1]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <p class="text-[12px] text-gray-800 font-medium">Total Dibayar</p>
                        </div>
                        <p class="text-[15px] font-bold text-[#0F47A1]">Rp{{ number_format($total_dibayar, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="border border-gray-100 rounded-2xl p-5 shadow-sm bg-white mt-4 flex items-center">
                    <div class="w-10 h-10 bg-[#FFF7ED] rounded-full flex items-center justify-center mr-4 text-[#F59E0B]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[13px] text-gray-800 font-medium">Sisa Kredit/ Piutang</p>
                        <p class="text-[16px] font-bold text-[#F59E0B]">Rp{{ number_format($sisa_kredit, 0, ',', '.') }}</p>
                    </div>
                </div>

            <div class="border border-gray-100 rounded-2xl p-5 shadow-sm bg-white mt-4">
                <h2 class="text-[14px] font-semibold text-gray-800 mb-4">Status Pembayaran</h2>
                <div class="grid grid-cols-2">
                    <div class="flex items-center">
                        <div class="w-9 h-9 bg-[#ECFDF5] rounded-full flex items-center justify-center text-[#10B981] mr-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <p class="text-[12px] font-semibold text-gray-800">Lunas</p>
                            <p class="text-[16px] text-gray-800">{{ $lunas }}</p>
                        </div>
                    </div>
                    <div class="flex items-center border-l border-gray-100 pl-4">
                        <div class="w-9 h-9 bg-[#FFF7ED] rounded-full flex items-center justify-center text-[#F59E0B] mr-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[12px] font-semibold text-gray-800">Belum Lunas</p>
                            <p class="text-[16px] text-gray-800">{{ $belum_lunas }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border border-gray-100 rounded-2xl p-5 shadow-sm bg-white mt-4">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-[14px] font-semibold text-gray-800">Riwayat Terbaru</h2>
                    <a href="#" class="text-[12px] font-semibold text-[#0F47A1]">Lihat Semua</a>
                </div>
                
                @if(count($riwayat) > 0)
                    <div class="space-y-4">
                        @foreach($riwayat as $item)
                        <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                            <div>
                                <p class="text-[13px] font-bold text-gray-800">{{ $item->nama_toko }}</p>
                                <p class="text-[11px] text-gray-500">{{ date('d M Y', strtotime($item->created_at)) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[13px] font-bold text-[#10B981]">Rp{{ number_format($item->total_tagihan, 0, ',', '.') }}</p>
                                <p class="text-[10px] font-semibold {{ $item->status == 'lunas' ? 'text-green-500' : 'text-orange-500' }}">{{ strtoupper(str_replace('_', ' ', $item->status)) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-4 mb-2">
                        <svg class="w-16 h-16 text-gray-200 mb-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                        </svg>
                        <p class="text-[14px] font-bold text-gray-800 mb-1">Belum ada laporan nota</p>
                        <p class="text-[12px] text-gray-500 text-center px-4 leading-relaxed">
                            Kamu belum mengupload data tagihan.<br>Upload nota pertama kamu untuk mulai<br>mencatat data.
                        </p>
                    </div>
                @endif
            </div>

        </div>

        <div class="fixed bottom-0 max-w-md mx-auto inset-x-0 w-full bg-white border-t border-gray-100 h-[72px] flex justify-between items-center px-10 z-50 rounded-t-[20px] shadow-[0_-4px_15px_rgba(0,0,0,0.03)]">
            
            <a href="{{ route('sales.home') }}" class="flex flex-col items-center text-[#0F47A1] w-16">
                <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"></path></svg>
                <span class="text-[10px] font-bold">Home</span>
            </a>

            <div class="relative flex flex-col items-center w-24">
                <button onclick="toggleUploadModal()" class="absolute -top-[45px] bg-[#0F47A1] text-white rounded-full p-[14px] border-[6px] border-white hover:bg-blue-800 transition transform hover:scale-105">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><circle cx="12" cy="13" r="3"></circle></svg>
                </button>
                <span class="text-[10px] font-bold text-[#0F47A1] absolute top-[20px] whitespace-nowrap">Upload Nota</span>
            </div>

            <a href="#" class="flex flex-col items-center text-gray-500 hover:text-gray-700 w-16">
                <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"></path></svg>
                <span class="text-[10px] font-medium">Riwayat</span>
            </a>

        </div>
    </div>

    <div id="upload-modal" class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity">
        <div class="bg-white w-full max-w-md rounded-t-[30px] sm:rounded-[30px] p-6 pb-10 transform transition-transform animate-slideUp">
            
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-gray-900">Pilih Metode Input</h3>
                <button onclick="toggleUploadModal()" class="text-gray-400 hover:text-gray-600 p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <label class="cursor-pointer bg-[#F0F5FF] border border-[#0F47A1]/20 rounded-2xl p-6 flex flex-col items-center justify-center text-center transition hover:bg-blue-50 active:scale-95">
                    <input type="file" accept="image/*" capture="environment" class="hidden" onchange="handleUpload(this)">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-[#0F47A1] mb-3 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                    </div>
                    <span class="text-[13px] font-bold text-[#0F47A1]">Buka Kamera</span>
                </label>

                <label class="cursor-pointer bg-white border border-gray-200 rounded-2xl p-6 flex flex-col items-center justify-center text-center transition hover:bg-gray-50 active:scale-95 shadow-sm">
                    <input type="file" accept="image/*" class="hidden" onchange="handleUpload(this)">
                    <div class="w-12 h-12 bg-[#ECFDF5] rounded-full flex items-center justify-center text-[#10B981] mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <span class="text-[13px] font-bold text-gray-700">Pilih Galeri</span>
                </label>
            </div>
        </div>
    </div>

    <div id="loading-overlay" class="fixed inset-0 bg-white/90 backdrop-blur-sm z-[60] hidden flex-col items-center justify-center">
        <svg class="animate-spin h-10 w-10 text-[#0F47A1] mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        <p class="text-sm text-gray-800 font-bold animate-pulse">Faktur sedang di proses, mohon tunggu...</p>
    </div>

    <script>
        // Buka / Tutup Modal
        function toggleUploadModal() {
            const modal = document.getElementById('upload-modal');
            if(modal.classList.contains('hidden')){
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } else {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        // --- ALAT KOMPRES GAMBAR & KONVERSI BASE64 ---
        // Ini memastikan memori browser (sessionStorage) tidak jebol dan foto tetap terbaca
        function compressImageToBase64(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = (event) => {
                    const img = new Image();
                    img.src = event.target.result;
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        const MAX_WIDTH = 1000; // Diperkecil agar ringan di session storage
                        let width = img.width;
                        let height = img.height;

                        if (width > MAX_WIDTH) {
                            height = Math.round((height * MAX_WIDTH) / width);
                            width = MAX_WIDTH;
                        }

                        canvas.width = width;
                        canvas.height = height;

                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        // Ubah ke JPEG kualitas 60% (Sangat kecil & aman buat session storage)
                        const base64String = canvas.toDataURL('image/jpeg', 0.6);
                        resolve(base64String);
                    };
                    img.onerror = (err) => reject(err);
                };
                reader.onerror = (err) => reject(err);
            });
        }

        // Handle File yang dipilih dari Kamera atau Galeri
        async function handleUpload(input) {
            if (input.files && input.files[0]) {
                toggleUploadModal(); // Tutup modal biar rapi
                
                const file = input.files[0];
                const loadingOverlay = document.getElementById('loading-overlay');
                loadingOverlay.classList.remove('hidden');
                loadingOverlay.classList.add('flex');

                try {
                    // Kompres gambar secara lokal dulu untuk disimpan ke Session Browser
                    const compressedBase64 = await compressImageToBase64(file);
                    
                    // Kirim file asli ke AI OCR agar akurasi bacanya maksimal
                    await sendToOcrApi(file, compressedBase64);
                } catch (e) {
                    alert("Gagal memproses gambar sebelum dikirim.");
                    console.error(e);
                    loadingOverlay.classList.add('hidden');
                    loadingOverlay.classList.remove('flex');
                }
            }
            input.value = ''; // Reset input value biar bisa milih file yang sama lagi kalo gagal
        }

        // Tembak API OCR
        async function sendToOcrApi(imageFile, compressedBase64) {
            const formData = new FormData();
            formData.append('image', imageFile); // File asli dikirim ke Laravel AI

            try {
                const response = await fetch("{{ route('sales.scan') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const result = await response.json();

                if (response.ok && result.success) {
                    // Simpan data AI ke Session Storage
                    sessionStorage.setItem('ocrData', JSON.stringify(result.data));
                    
                    // SIMPAN BASE64 KOMPRES KE SESSION (BUKAN blob: URL)
                    // Karena ini Base64 asli, panjangnya bakal ribuan karakter (lolos jebakan >100 karakter)
                    sessionStorage.setItem('ocrImage', compressedBase64);
                    
                    // Pindah ke halaman edit
                    window.location.href = "{{ route('sales.input') }}"; 
                } else {
                    alert('Gagal membaca nota: ' + result.message);
                    document.getElementById('loading-overlay').classList.add('hidden');
                    document.getElementById('loading-overlay').classList.remove('flex');
                }
            } catch (error) {
                alert('Terjadi kesalahan jaringan/sistem.');
                console.error(error);
                document.getElementById('loading-overlay').classList.add('hidden');
                document.getElementById('loading-overlay').classList.remove('flex');
            }
        }
    </script>

</body>
</html>