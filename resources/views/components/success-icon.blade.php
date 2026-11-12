@props([
    'size' => 'w-28 h-28',
    'iconSize' => 'w-12 h-12',
    'color' => 'bg-[#10B981]',
    'border' => 'border-8',
])

<div class="relative {{ $size }}">
    <div class="absolute inset-0 bg-[#ECFDF5] rounded-full animate-ping opacity-75"></div>
    <div class="relative w-full h-full {{ $color }} rounded-full flex items-center justify-center text-white shadow-lg {{ $border }} border-[#ECFDF5]">
        <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
        </svg>
    </div>
</div>
