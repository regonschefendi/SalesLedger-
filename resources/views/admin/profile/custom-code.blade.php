<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Custom Code - Sales Ledger</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    <form action="{{ route('admin.profile.custom-code.update') }}" method="POST" class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative shadow-2xl overflow-hidden pb-10">
        @csrf
        
        <div class="px-6 pt-12 pb-4 flex items-center bg-white border-b border-gray-100">
            <button type="button" onclick="window.history.back()" class="text-[#0F47A1] hover:text-blue-900 transition p-2 -ml-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <h1 class="text-[18px] font-bold text-gray-900 flex-grow text-center">Custom Code</h1>
            <button type="submit" class="text-[#0F47A1] hover:text-blue-900 transition p-2 -mr-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            </button>
        </div>

        <div class="px-5 pt-6 space-y-6 flex-grow overflow-y-auto">
            
            @if ($errors->any())
                <div class="p-3.5 bg-red-50 border border-red-200 rounded-2xl text-red-500 text-xs font-medium">
                    {{ $errors->first('admin_code') }}
                </div>
            @endif

            <div class="border border-gray-100 rounded-2xl p-5 shadow-sm bg-slate-50 text-center space-y-3">
                <p class="text-[12px] text-gray-400 font-bold tracking-wide uppercase">Kode Referal Aktif Lu</p>
                <div class="flex items-center justify-center space-x-3">
                    <span id="ref-code-text" class="text-[24px] font-extrabold text-[#0F47A1] tracking-wider">{{ $user->admin_code }}</span>
                    <button type="button" onclick="copyToClipboard()" class="p-2 bg-white border border-gray-200 rounded-xl text-gray-600 hover:text-blue-600 shadow-xs transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                    </button>
                </div>
                <p id="copy-toast" class="text-[11px] text-[#10B981] font-bold hidden animate-fadeIn">✓ Kode berhasil disalin ke clipboard</p>
            </div>

            <div>
                <label class="block text-[12px] font-bold text-gray-900 mb-1.5 pl-1">Ubah / Buat Kode Baru</label>
                <input type="text" name="admin_code" value="{{ old('admin_code', $user->admin_code) }}" placeholder="Contoh: TEAMMAJU"
                       class="w-full border border-gray-200 rounded-xl px-4 py-3.5 text-[14px] font-extrabold tracking-wider text-[#0F47A1] focus:ring-2 focus:ring-[#0F47A1] focus:outline-none transition uppercase">
                <p class="text-[11px] text-gray-400 mt-2 leading-relaxed pl-1">Salurkan kode kustom unik ini kepada seluruh tim Sales lu agar mereka terikat langsung dengan laporan keuangan dashboard lu.</p>
            </div>

        </div>
    </form>

    <script>
        function copyToClipboard() {
            const code = document.getElementById('ref-code-text').innerText;
            navigator.clipboard.writeText(code).then(() => {
                const toast = document.getElementById('copy-toast');
                toast.classList.remove('hidden');
                setTimeout(() => toast.classList.add('hidden'), 2500);
            });
        }
    </script>
</body>
</html>