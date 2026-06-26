<div id="global-loading" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/40 backdrop-blur-sm transition-all duration-150">
    <div class="bg-white p-6 rounded-2xl shadow-xl flex flex-col items-center space-y-4 max-w-[200px] mx-auto text-center">
        <div class="relative w-12 h-12">
            <div class="w-12 h-12 border-4 border-slate-100 rounded-full"></div>
            <div class="w-12 h-12 border-4 border-[#0F47A1] border-t-transparent rounded-full animate-spin absolute top-0 left-0"></div>
        </div>
        <div>
            <p id="global-loading-text" class="text-[14px] font-bold text-gray-900">Memproses...</p>
            <p class="text-[11px] text-gray-400 mt-0.5">Mohon tunggu sebentar</p>
        </div>
    </div>
</div>

<script>
    window.Loading = {
        show: function(message = 'Memproses...') {
            const overlay = document.getElementById('global-loading');
            const text = document.getElementById('global-loading-text');
            if (overlay && text) {
                text.innerText = message;
                overlay.classList.remove('hidden');
                overlay.classList.add('flex');
            }
        },
        hide: function() {
            const overlay = document.getElementById('global-loading');
            if (overlay) {
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
            }
        }
    };
</script>