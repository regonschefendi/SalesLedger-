<div class="border border-gray-100 rounded-2xl bg-white shadow-sm p-4 mb-3.5">
    <div class="flex items-center space-x-4 mb-3">
        <div class="w-9 h-9 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </div>
        <div>
            <span class="text-[14px] font-bold text-gray-800">Tema Tampilan</span>
            <p class="text-[11px] text-gray-400 mt-0.5">Pilih mode terang atau gelap.</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-2 bg-gray-100 rounded-xl p-1">
        <button type="button" data-theme-option="light" class="rounded-lg py-2 text-[12px] font-bold text-gray-500 transition">
            Terang
        </button>
        <button type="button" data-theme-option="dark" class="rounded-lg py-2 text-[12px] font-bold text-gray-500 transition">
            Gelap
        </button>
    </div>
</div>

@once
<script>
    (function () {
        var buttons = document.querySelectorAll('[data-theme-option]');

        function render(theme) {
            buttons.forEach(function (button) {
                var active = button.dataset.themeOption === theme;

                button.classList.toggle('bg-white', active);
                button.classList.toggle('shadow-sm', active);
                button.classList.toggle('text-gray-900', active);
                button.classList.toggle('text-gray-500', !active);
            });
        }

        if (window.Theme) {
            render(window.Theme.current());

            document.addEventListener('themechange', function (event) {
                render(event.detail.theme);
            });
        }

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                if (window.Theme) {
                    window.Theme.apply(button.dataset.themeOption);
                }
            });
        });
    })();
</script>
@endonce
