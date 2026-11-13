@props([
    'name' => 'q',
    'value' => null,
    'placeholder' => 'Cari...',
    'submitOnChange' => false,
    'accent' => '#2563EB',
])

@php
    $currentValue = $value ?? request($name);
    $currentValue = is_scalar($currentValue) ? (string) $currentValue : '';
@endphp

<div class="relative">
    <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>

    <input
        type="text"
        name="{{ $name }}"
        value="{{ $currentValue }}"
        placeholder="{{ $placeholder }}"
        aria-label="{{ $placeholder }}"
        enterkeyhint="search"
        autocomplete="off"
        data-search-input
        oninput="toggleSearchClear(this)"
        @if($submitOnChange) onchange="this.form.requestSubmit ? this.form.requestSubmit() : this.form.submit()" @endif
        style="--tw-ring-color: {{ $accent }}"
        {{ $attributes->merge(['class' => 'w-full bg-white border border-gray-200 rounded-full pl-12 pr-11 py-3.5 text-[13px] focus:ring-2 focus:outline-none transition']) }}
    >

    <button
        type="button"
        data-search-clear
        onclick="clearSearchField(this)"
        onmousedown="event.preventDefault()"
        aria-label="Hapus pencarian"
        style="display: {{ filled($currentValue) ? 'flex' : 'none' }}"
        class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 items-center justify-center text-gray-400 hover:text-gray-600 transition"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>
</div>

@once
<script>
    function toggleSearchClear(input) {
        const button = input.parentElement.querySelector('[data-search-clear]');
        if (button) button.style.display = input.value.trim() === '' ? 'none' : 'flex';
    }

    function clearSearchField(button) {
        const input = button.parentElement.querySelector('[data-search-input]');
        if (!input) return;

        input.value = '';
        toggleSearchClear(input);

        if (input.form) {
            input.form.requestSubmit ? input.form.requestSubmit() : input.form.submit();
        } else {
            input.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }
</script>
@endonce
