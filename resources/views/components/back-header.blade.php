@props([
    'title',
    'backUrl' => null,
    'backAction' => null,
    'titleId' => null,
    'color' => 'text-[#0F47A1]',
    'borderClass' => '',
    'padding' => 'pt-12 pb-4',
])

<div class="px-6 {{ $padding }} flex items-center bg-white {{ $borderClass }} z-10 relative">
    @if($backUrl)
        <a href="{{ $backUrl }}" class="{{ $color }} p-2 -ml-2 hover:text-blue-900 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
    @elseif($backAction)
        <button type="button" onclick="{{ $backAction }}" class="{{ $color }} p-2 -ml-2 hover:text-blue-900 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
    @else
        <span class="w-8 -ml-2"></span>
    @endif

    <h1 @if($titleId) id="{{ $titleId }}" @endif class="text-[18px] font-bold text-gray-900 flex-grow text-center pr-8">
        {{ $title }}
    </h1>

    @if(trim($slot) !== '')
        <div class="flex items-center space-x-4">
            {{ $slot }}
        </div>
    @endif
</div>
