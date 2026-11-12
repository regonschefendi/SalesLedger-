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

@if($csrf)
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endif

{{ $slot }}

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@vite('resources/css/app.css')
<style>
    body { font-family: 'Inter', sans-serif; }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
