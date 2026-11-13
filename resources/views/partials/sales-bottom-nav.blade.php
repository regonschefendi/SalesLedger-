<div class="fixed bottom-0 max-w-md mx-auto inset-x-0 w-full bg-white border-t border-gray-100 h-[72px] flex justify-between items-center px-10 z-40 rounded-t-[20px] shadow-[0_-4px_15px_rgba(0,0,0,0.03)]">
    
    <a href="{{ route('sales.home') }}" class="flex flex-col items-center w-16 transition {{ request()->routeIs('sales.home') ? 'text-[#0F47A1]' : 'text-gray-400 hover:text-gray-700' }}">
        <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"></path></svg>
        <span class="text-[10px] font-bold">Beranda</span>
    </a>

    <div class="relative flex flex-col items-center w-24">
        <button onclick="toggleUploadModal()" class="absolute -top-[45px] bg-[#0F47A1] text-white rounded-full p-[14px] border-[6px] border-white hover:bg-blue-800 transition transform hover:scale-105 shadow-md z-50">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><circle cx="12" cy="13" r="3"></circle></svg>
        </button>
        <span class="text-[10px] font-bold text-[#0F47A1] absolute top-[20px] whitespace-nowrap">Unggah Nota Baru</span>
    </div>

    <a href="{{ route('sales.riwayat.index') }}" class="flex flex-col items-center w-16 transition {{ request()->routeIs('sales.riwayat.*') ? 'text-[#0F47A1]' : 'text-gray-400 hover:text-gray-700' }}">
        <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"></path></svg>
        <span class="text-[10px] font-bold">Riwayat</span>
    </a>

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
    <svg class="animate-spin h-10 w-10 text-[#0F47A1] mb-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
    <p class="text-sm text-gray-800 font-bold animate-pulse">Faktur sedang diproses, mohon tunggu...</p>
</div>

<div id="ocr-fail-modal" class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/50 backdrop-blur-sm px-4">
    <div class="bg-white w-full max-w-sm rounded-[24px] p-6 text-center shadow-2xl transform transition-all animate-slideUp">
        <div class="w-12 h-12 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 border border-red-100">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <h3 class="text-[16px] font-bold text-gray-900 mb-1">Gagal Membaca Faktur</h3>
        <p class="text-[12px] text-gray-500 mb-6 leading-relaxed">AI sedang sibuk atau foto kurang jelas. Anda tetap bisa melanjutkan proses dengan mengisi data faktur secara manual.</p>
        <div class="flex space-x-3">
            <button onclick="closeOcrFailModal()" class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 rounded-xl text-[13px] transition">Kembali</button>
            <button id="btn-lanjut-manual" class="w-1/2 bg-[#0F47A1] hover:bg-blue-900 text-white font-semibold py-3 rounded-xl text-[13px] shadow-md transition">Lanjut Manual</button>
        </div>
    </div>
</div>

<script>
    const OCR_MAX_DIMENSION = 1600;
    const OCR_TARGET_BYTES = 1.5 * 1024 * 1024;
    const OCR_MIN_QUALITY = 0.45;

    function toggleUploadModal() {
        const modal = document.getElementById('upload-modal');
        if(modal.classList.contains('hidden')){ modal.classList.remove('hidden'); modal.classList.add('flex'); } 
        else { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    }

    function showLoadingOverlay(show) {
        const loader = document.getElementById('loading-overlay');
        if (!loader) return;
        loader.classList.toggle('hidden', !show);
        loader.classList.toggle('flex', show);
    }

    function safeSessionSet(key, value) {
        try {
            sessionStorage.setItem(key, value);
            return true;
        } catch (e) {
            console.error('sessionStorage tidak tersedia/penuh:', e);
            return false;
        }
    }

    function showSessionError() {
        alert('Penyimpanan sementara browser penuh atau diblokir. Tutup tab lain atau matikan mode privat, lalu coba lagi.');
    }

    function dataUrlToBlob(dataUrl) {
        const parts = dataUrl.split(',');
        const mime = (parts[0].match(/:(.*?);/) || [])[1] || 'image/jpeg';
        const binary = atob(parts[1]);
        const bytes = new Uint8Array(binary.length);

        for (let i = 0; i < binary.length; i++) {
            bytes[i] = binary.charCodeAt(i);
        }

        return new Blob([bytes], { type: mime });
    }

    function canvasToBlob(canvas, type, quality) {
        return new Promise((resolve, reject) => {
            if (canvas.toBlob) {
                canvas.toBlob((blob) => {
                    blob ? resolve(blob) : reject(new Error('Gagal mengompres foto.'));
                }, type, quality);
                return;
            }

            // Fallback untuk browser lama yang belum punya canvas.toBlob.
            try {
                resolve(dataUrlToBlob(canvas.toDataURL(type, quality)));
            } catch (e) {
                reject(new Error('Browser tidak mendukung pemrosesan gambar.'));
            }
        });
    }

    function blobToDataUrl(blob) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => resolve(reader.result);
            reader.onerror = () => reject(new Error('Gagal membaca foto.'));
            reader.readAsDataURL(blob);
        });
    }

    /**
     * Kompres foto dari kamera/galeri agar aman untuk semua device:
     * - Membatasi sisi terpanjang ke 1600px (teks nota tetap terbaca).
     * - Menurunkan kualitas bertahap sampai ukuran file <= 2MB.
     * - Hasilnya berupa Blob (dikirim ke server) + base64 (untuk sessionStorage).
     */
    function compressImage(file) {
        return new Promise((resolve, reject) => {
            // Sebagian browser HP tidak mengisi file.type (mis. HEIC), jadi
            // biarkan gambar tetap dicoba dibaca selama bukan tipe non-gambar.
            if (!file || (file.type && !file.type.startsWith('image/'))) {
                reject(new Error('File yang dipilih bukan gambar.'));
                return;
            }

            const objectUrl = URL.createObjectURL(file);
            const img = new Image();
            const cleanup = () => URL.revokeObjectURL(objectUrl);

            img.onload = async () => {
                try {
                    let width = img.naturalWidth || img.width;
                    let height = img.naturalHeight || img.height;

                    if (!width || !height) {
                        throw new Error('Foto tidak dapat dibaca. Coba ambil ulang.');
                    }

                    const scale = Math.min(1, OCR_MAX_DIMENSION / Math.max(width, height));
                    width = Math.max(1, Math.round(width * scale));
                    height = Math.max(1, Math.round(height * scale));

                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;

                    const ctx = canvas.getContext('2d');
                    if (!ctx) {
                        throw new Error('Browser tidak mendukung pemrosesan gambar.');
                    }

                    // Latar putih agar PNG transparan tidak menjadi hitam saat dijadikan JPEG.
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, width, height);
                    ctx.imageSmoothingEnabled = true;
                    ctx.imageSmoothingQuality = 'high';
                    ctx.drawImage(img, 0, 0, width, height);

                    let quality = 0.85;
                    let blob = await canvasToBlob(canvas, 'image/jpeg', quality);

                    while (blob.size > OCR_TARGET_BYTES && quality > OCR_MIN_QUALITY) {
                        quality = Math.max(OCR_MIN_QUALITY, quality - 0.1);
                        blob = await canvasToBlob(canvas, 'image/jpeg', quality);
                    }

                    const dataUrl = await blobToDataUrl(blob);
                    resolve({ blob, dataUrl });
                } catch (e) {
                    reject(e);
                } finally {
                    cleanup();
                }
            };

            img.onerror = () => {
                cleanup();
                reject(new Error('Format foto tidak didukung. Gunakan JPG/PNG atau ambil ulang dari kamera.'));
            };

            img.src = objectUrl;
        });
    }

    async function handleUpload(input) {
        const file = input.files && input.files[0];
        input.value = '';

        if (!file) return;

        toggleUploadModal();
        showLoadingOverlay(true);

        try {
            const { blob, dataUrl } = await compressImage(file);
            await sendToOcrApi(blob, dataUrl);
        } catch (e) {
            console.error(e);
            showLoadingOverlay(false);
            alert(e.message || 'Gagal memproses gambar. Coba ambil ulang foto.');
        }
    }

    function closeOcrFailModal() {
        const modal = document.getElementById('ocr-fail-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function triggerManualFallback(compressedBase64) {
        showLoadingOverlay(false);

        const modal = document.getElementById('ocr-fail-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        const btnManual = document.getElementById('btn-lanjut-manual');
        btnManual.onclick = function() {
            // Buat data kosong sebagai fallback, pasang flag is_manual
            const now = new Date();
            const tanggalHariIni = String(now.getDate()).padStart(2, '0') + '/' + String(now.getMonth() + 1).padStart(2, '0') + '/' + now.getFullYear();
            const fallbackData = {
                nama_toko: '',
                nomor_faktur: 'INV-' + Math.floor(10000 + Math.random() * 90000), // Random default INV
                tanggal_nota: tanggalHariIni, // Hari ini (dd/mm/yyyy)
                total_tagihan: 0,
                is_manual: true 
            };
            
            // PENTING: Foto tetap disimpen ke sessionStorage biar ga hilang!
            const dataSaved = safeSessionSet('ocrData', JSON.stringify(fallbackData));
            const imageSaved = safeSessionSet('ocrImage', compressedBase64);

            if (!dataSaved || !imageSaved) {
                showSessionError();
                return;
            }
            
            window.location.href = "{{ route('sales.input') }}";
        };
    }

    async function sendToOcrApi(imageBlob, compressedBase64) {
        const formData = new FormData(); 
        // Kirim hasil kompresi (bukan foto asli) agar tidak kena limit upload
        // server dan lebih cepat di jaringan mobile.
        formData.append('image', imageBlob, 'nota.jpg');

        const controller = typeof AbortController !== 'undefined' ? new AbortController() : null;
        const timeoutId = controller ? setTimeout(() => controller.abort(), 60000) : null;

        try {
            const response = await fetch("{{ route('sales.scan') }}", {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 
                    'Accept': 'application/json' 
                },
                body: formData,
                signal: controller ? controller.signal : undefined
            });

            const raw = await response.text();
            let result = null;

            try {
                result = JSON.parse(raw);
            } catch (e) {
                result = null;
            }

            if (response.ok && result && result.success) {
                const dataSaved = safeSessionSet('ocrData', JSON.stringify(result.data));
                const imageSaved = safeSessionSet('ocrImage', compressedBase64);

                if (!dataSaved || !imageSaved) {
                    showLoadingOverlay(false);
                    showSessionError();
                    return;
                }

                window.location.href = "{{ route('sales.input') }}";
            } else {
                // JIKA API GEMINI GAGAL/LIMIT, LEMPAR KE MODAL MANUAL
                console.warn('OCR Gagal:', result && result.message ? result.message : raw);
                triggerManualFallback(compressedBase64);
            }
        } catch (e) {
            // JIKA ERROR JARINGAN / TIMEOUT / IPHONE MEMORY LIMIT, LEMPAR KE MODAL MANUAL
            console.error('Network/System Error:', e);
            triggerManualFallback(compressedBase64);
        } finally {
            clearTimeout(timeoutId);
        }
    }
</script>