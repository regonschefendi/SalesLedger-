@php
    $backUrl = auth()->user()->role === 'admin'
        ? route('admin.dashboard')
        : route('sales.home');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Notifikasi - Sales Ledger</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="manifest" href="/manifest.webmanifest">
    @vite('resources/css/app.css')
    <style>
        body { font-family: 'Inter', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    <div class="min-h-screen flex flex-col max-w-md mx-auto bg-white relative shadow-2xl overflow-hidden pb-10">

        <div class="px-6 pt-12 pb-4 flex items-center bg-white border-b border-gray-50">
            <a href="{{ $backUrl }}" class="text-[#0F47A1] hover:text-blue-900 transition p-2 -ml-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <h1 class="text-[18px] font-bold text-gray-900 flex-grow text-center pr-8">Notifikasi</h1>
        </div>

        <div class="px-5 pt-5 flex-grow overflow-y-auto no-scrollbar">
            @if(session('success'))
                <div class="mb-4 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-[12px] font-medium text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex items-center justify-between mb-4">
                <p class="text-[12px] font-medium text-gray-500">
                    {{ $unreadCount > 0 ? $unreadCount . ' notifikasi belum dibaca' : 'Semua notifikasi sudah dibaca' }}
                </p>

                @if($unreadCount > 0)
                    <form action="{{ route('notifications.read-all') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-[12px] font-bold text-[#0F47A1] hover:text-blue-900 transition">
                            Tandai semua dibaca
                        </button>
                    </form>
                @endif
            </div>

            <div class="space-y-3">
                @forelse($notifications as $notification)
                    @php
                        $data = $notification->data;
                        $type = $data['type'] ?? 'info';
                        $isUnread = is_null($notification->read_at);

                        $iconClass = match ($type) {
                            'faktur' => 'bg-emerald-50 text-emerald-600',
                            'pembayaran' => 'bg-orange-50 text-orange-500',
                            'toko' => 'bg-blue-50 text-[#0F47A1]',
                            default => 'bg-gray-100 text-gray-500',
                        };
                    @endphp

                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left border rounded-2xl p-4 shadow-sm transition flex items-start space-x-3 {{ $isUnread ? 'bg-[#F0F5FF]/60 border-blue-100 hover:bg-[#F0F5FF]' : 'bg-white border-gray-100 hover:bg-gray-50' }}">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $iconClass }}">
                                @if($type === 'faktur')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                @elseif($type === 'pembayaran')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @elseif($type === 'toko')
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4v2h16V4zm1 10v-2l-1-5H4l-1 5v2h1v6h10v-6h4v6h2v-6h1zm-9 4H6v-4h6v4z"></path></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @endif
                            </div>

                            <div class="flex-grow min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="text-[13px] font-bold text-gray-900 leading-snug">{{ $data['title'] ?? 'Notifikasi' }}</h3>
                                    @if($isUnread)
                                        <span class="w-2 h-2 rounded-full bg-[#2563EB] flex-shrink-0 mt-1.5"></span>
                                    @endif
                                </div>
                                <p class="text-[12px] text-gray-600 leading-relaxed mt-1">{{ $data['message'] ?? '' }}</p>
                                <p class="text-[10px] font-medium text-gray-400 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                        </button>
                    </form>
                @empty
                    <div class="flex flex-col items-center justify-center py-16">
                        <div class="w-16 h-16 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        </div>
                        <p class="text-[14px] font-bold text-gray-800 mb-1">Belum ada notifikasi</p>
                        <p class="text-[12px] text-gray-500 text-center px-6 leading-relaxed">
                            Notifikasi terbaru seputar nota, pembayaran, dan toko akan muncul di sini.
                        </p>
                    </div>
                @endforelse
            </div>

            @if($notifications->hasPages())
                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</body>
</html>
