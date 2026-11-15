@props([
    'title',
    'manifest' => false,
    'csrf' => false,
])

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>{{ $title }}</title>

@if($manifest)
    <link rel="manifest" href="/manifest.webmanifest">
@endif

@php($themePreference = auth()->check() ? auth()->user()->theme : null)

<script>
    (function () {
        var storageKey = 'sales-ledger-theme';
        var preference = @json($themePreference);
        var stored = null;

        try {
            stored = window.localStorage.getItem(storageKey);
        } catch (error) {
            stored = null;
        }

        var theme = (preference === 'dark' || preference === 'light')
            ? preference
            : (stored === 'dark' ? 'dark' : 'light');

        document.documentElement.classList.toggle('dark', theme === 'dark');
        document.documentElement.dataset.theme = theme;

        try {
            window.localStorage.setItem(storageKey, theme);
        } catch (error) {}

        window.Theme = {
            storageKey: storageKey,
            current: function () {
                return document.documentElement.classList.contains('dark') ? 'dark' : 'light';
            },
            apply: function (value, persist) {
                var normalized = value === 'dark' ? 'dark' : 'light';

                document.documentElement.classList.toggle('dark', normalized === 'dark');
                document.documentElement.dataset.theme = normalized;

                try {
                    window.localStorage.setItem(storageKey, normalized);
                } catch (error) {}

                document.dispatchEvent(new CustomEvent('themechange', {
                    detail: { theme: normalized }
                }));

                if (persist === false) {
                    return;
                }

                var token = document.querySelector('meta[name="csrf-token"]');

                if (!token) {
                    return;
                }

                fetch('/profile/theme', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token.getAttribute('content')
                    },
                    body: JSON.stringify({ theme: normalized })
                }).catch(function () {});
            }
        };
    })();
</script>

@if($csrf)
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endif

{{ $slot }}

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
<noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"></noscript>
@vite('resources/css/app.css')
<style>
    body { font-family: 'Inter', sans-serif; }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
