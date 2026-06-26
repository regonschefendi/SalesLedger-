<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Select Role - Sales Ledger</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 antialiased">

    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative shadow-2xl overflow-hidden px-8 pt-14 pb-8 justify-between">
        
        <div class="space-y-6">
            <div class="space-y-2 text-center">
                <h2 class="text-[26px] font-bold text-gray-900">Pilih Role Anda</h2>
                <p class="text-xs text-gray-500 px-2 leading-relaxed">
                    Tentukan peran anda di aplikasi ini. Peran yang dipilih tidak dapat diubah kembali demi keamanan data.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-2">
                <div id="card-admin" onclick="toggleRole('admin')" 
                     class="border-2 border-[#0F47A1] bg-[#F0F5FF] rounded-2xl p-5 text-center cursor-pointer transition transform active:scale-95 flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#0F47A1] mb-3 shadow-sm">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    </div>
                    <span class="text-sm font-bold text-[#0F47A1]">Owner / Admin</span>
                </div>

                <div id="card-sales" onclick="toggleRole('sales')" 
                     class="border border-gray-200 bg-white rounded-2xl p-5 text-center cursor-pointer transition transform active:scale-95 flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-500 mb-3 border border-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                    <span class="text-sm font-bold text-gray-700">Tim Sales</span>
                </div>
            </div>

            <form id="roleForm" class="space-y-4 pt-4">
                <div>
                    <label id="input-label" class="block text-xs font-semibold text-gray-700 mb-1.5 pl-1">Bikin Kode Khusus Admin</label>
                    <input type="text" id="code-input" name="admin_code" placeholder="Contoh: ADM-COMPANY" required 
                           class="w-full bg-[#F3F4F6] border-0 rounded-xl px-4 py-3.5 text-sm focus:ring-2 focus:ring-[#0F47A1] focus:outline-none transition placeholder-gray-400">
                    <p id="input-desc" class="text-[11px] text-gray-400 mt-1.5 pl-1 leading-relaxed">
                        Kode unik ini nantinya wajib dibagikan ke tim sales anda agar mereka dapat terhubung langsung.
                    </p>
                </div>

                <div id="error-box" class="hidden text-xs bg-red-50 border border-red-200 text-red-600 px-4 py-2.5 rounded-xl font-medium"></div>

                <div class="text-center pt-4">
                    <button type="submit" id="btn-submit" class="w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-semibold py-4 rounded-xl shadow-md transition duration-200 text-sm">
                        <span id="btn-text">Confirm as Admin</span>
                    </button>
                </div>
            </form>
        </div>

        <div class="text-center text-xs text-gray-400">
            Sales Ledger System Relation
        </div>
    </div>

    @include('partials.loading')

    <script>
        let currentRole = 'admin'; // Default

        function toggleRole(role) {
            currentRole = role;
            const cardAdmin = document.getElementById('card-admin');
            const cardSales = document.getElementById('card-sales');
            const label = document.getElementById('input-label');
            const input = document.getElementById('code-input');
            const desc = document.getElementById('input-desc');
            const btnText = document.getElementById('btn-text');

            if (role === 'admin') {
                // Style Active Admin
                cardAdmin.className = "border-2 border-[#0F47A1] bg-[#F0F5FF] rounded-2xl p-5 text-center cursor-pointer transition transform active:scale-95 flex flex-col items-center";
                cardSales.className = "border border-gray-200 bg-white rounded-2xl p-5 text-center cursor-pointer transition transform active:scale-95 flex flex-col items-center";
                
                // Content Switch
                label.innerText = "Bikin Kode Khusus Admin";
                input.placeholder = "Contoh: ADM-COMPANY";
                desc.innerText = "Kode unik ini nantinya wajib dibagikan ke tim sales anda agar mereka dapat terhubung langsung.";
                btnText.innerText = "Confirm as Admin";
            } else {
                // Style Active Sales
                cardSales.className = "border-2 border-[#0F47A1] bg-[#F0F5FF] rounded-2xl p-5 text-center cursor-pointer transition transform active:scale-95 flex flex-col items-center";
                cardAdmin.className = "border border-gray-200 bg-white rounded-2xl p-5 text-center cursor-pointer transition transform active:scale-95 flex flex-col items-center";
                
                // Content Switch
                label.innerText = "Masukkan Kode Referral Admin";
                input.placeholder = "Masukkan kode bos/admin anda...";
                desc.innerText = "Minta kode khusus ke atasan/admin anda untuk mensinkronisasi data laporan.";
                btnText.innerText = "Connect to Admin";
            }
        }

        // AJAX: Submit Data Peran
        document.getElementById('roleForm').addEventListener('submit', async function(e) {
            Loading.show('Menyimpan peran...');
            e.preventDefault();
            const formData = new FormData(e.target);
            const btnText = document.getElementById('btn-text');
            const btnSubmit = document.getElementById('btn-submit');
            const errorBox = document.getElementById('error-box');

            btnSubmit.disabled = true;
            errorBox.classList.add('hidden');

            // Deteksi endpoint berdasarkan role aktif
            const endpoint = currentRole === 'admin' ? '/api/set-admin' : '/api/set-sales';

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    // Masuk ke dashboard masing-masing sesuai respons backend
                    window.location.href = result.redirect;
                } else {
                    errorBox.innerText = result.message || "Gagal memproses peran database.";
                    errorBox.classList.remove('hidden');
                }
            } catch (error) {
                errorBox.innerText = "Gagal menghubungi server.";
                errorBox.classList.remove('hidden');
            } finally {
                btnSubmit.disabled = false;
                toggleRole(currentRole); // Reset tombol text state
                Loading.hide();
            }
        });
    </script>
</body>
</html>