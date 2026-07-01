<div class="fixed bottom-0 max-w-md mx-auto inset-x-0 w-full bg-white border-t border-gray-100 h-[72px] flex justify-between items-center px-10 z-40 rounded-t-[20px] shadow-[0_-4px_15px_rgba(0,0,0,0.03)]">
    
    <a href="{{ route('sales.home') }}" class="flex flex-col items-center w-16 transition {{ request()->routeIs('sales.home') ? 'text-[#0F47A1]' : 'text-gray-400 hover:text-gray-700' }}">
        <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"></path></svg>
        <span class="text-[10px] font-bold">Home</span>
    </a>

    <div class="relative flex flex-col items-center w-24">
        <button onclick="toggleUploadModal()" class="absolute -top-[45px] bg-[#0F47A1] text-white rounded-full p-[14px] border-[6px] border-white hover:bg-blue-800 transition transform hover:scale-105 shadow-md z-50">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><circle cx="12" cy="13" r="3"></circle></svg>
        </button>
        <span class="text-[10px] font-bold text-[#0F47A1] absolute top-[20px] whitespace-nowrap">Upload Nota Baru</span>
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

<script>
    function toggleUploadModal() {
        const modal = document.getElementById('upload-modal');
        if(modal.classList.contains('hidden')){ modal.classList.remove('hidden'); modal.classList.add('flex'); } 
        else { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    }

    function compressImageToBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = (e) => {
                const img = new Image(); img.src = e.target.result;
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    const MAX_WIDTH = 1000; let w = img.width, h = img.height;
                    if (w > MAX_WIDTH) { 
                        h = Math.round((h * MAX_WIDTH) / w); 
                        w = MAX_WIDTH; }
                    canvas.width = w; canvas.height = h;
                    canvas.getContext('2d').drawImage(img, 0, 0, w, h);
                    resolve(canvas.toDataURL('image/jpeg', 0.6));
                };
                img.onerror = reject;
            };
            reader.onerror = reject;
        });
    }

    // function compressImageToBase64(file) {
    //         return new Promise((resolve, reject) => {
    //             const reader = new FileReader();
    //             reader.readAsDataURL(file);
    //             reader.onload = (event) => {
    //                 const img = new Image();
    //                 img.src = event.target.result;
    //                 img.onload = () => {
    //                     const canvas = document.createElement('canvas');
    //                     const MAX_WIDTH = 1000; // Diperkecil agar ringan di session storage
    //                     let width = img.width;
    //                     let height = img.height;
    //                     if (width > MAX_WIDTH) {
    //                         height = Math.round((height * MAX_WIDTH) / width);
    //                         width = MAX_WIDTH;
    //                     }
    //                     canvas.width = width;
    //                     canvas.height = height;
    //                     const ctx = canvas.getContext('2d');
    //                     ctx.drawImage(img, 0, 0, width, height);

    //                     // Ubah ke JPEG kualitas 60% (Sangat kecil & aman buat session storage)
    //                     const base64String = canvas.toDataURL('image/jpeg', 0.6);
    //                     resolve(base64String);
    //                 };
    //                 img.onerror = (err) => reject(err);
    //             };
    //             reader.onerror = (err) => reject(err);
    //         });
    //     }

    async function handleUpload(input) {
        if (input.files && input.files[0]) {
            toggleUploadModal();
            const file = input.files[0];
            const loader = document.getElementById('loading-overlay');
            loader.classList.remove('hidden'); loader.classList.add('flex');
            try {
                const compressedBase64 = await compressImageToBase64(file);
                await sendToOcrApi(file, compressedBase64);
            } catch (e) {
                alert("Gagal memproses gambar."); loader.classList.add('hidden'); loader.classList.remove('flex');
            }
        }
        input.value = '';
    }

    async function sendToOcrApi(imageFile, compressedBase64) {
        const formData = new FormData(); formData.append('image', imageFile);
        try {
            const response = await fetch("{{ route('sales.scan') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: formData
            });
            const result = await response.json();
            if (response.ok && result.success) {
                sessionStorage.setItem('ocrData', JSON.stringify(result.data));
                sessionStorage.setItem('ocrImage', compressedBase64);
                window.location.href = "{{ route('sales.input') }}";
            } else {
                alert('Gagal: ' + result.message);
                document.getElementById('loading-overlay').classList.add('hidden'); document.getElementById('loading-overlay').classList.remove('flex');
            }
        } catch (e) {
            alert('Terjadi kesalahan jaringan/sistem.');
            document.getElementById('loading-overlay').classList.add('hidden'); document.getElementById('loading-overlay').classList.remove('flex');
        }
    }
</script>