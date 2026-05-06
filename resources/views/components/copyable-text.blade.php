@props([
    'value',
    'label' => 'Copy to clipboard',
])

<button
    type="button"
    x-data="{ copied: false, value: @js($value) }"
    @click="navigator.clipboard.writeText(value); copied = true; setTimeout(() => copied = false, 1500)"
    {{ $attributes->merge(['class' => 'relative inline-flex items-center gap-1 rounded px-1 py-0.5 hover:bg-gray-100 hover:text-gray-700 transition']) }}
    title="{{ $label }}"
    aria-label="{{ $label }}"
>
    <span
        x-show="copied"
        x-cloak
        x-transition
        class="absolute bottom-full left-1/2 mb-1 -translate-x-1/2 rounded bg-gray-900 px-2 py-1 text-[10px] font-sans font-medium text-white shadow"
    >
        Copied
    </span>
    <span>{{ $slot->isEmpty() ? $value : $slot }}</span>
    <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
    </svg>
    <svg x-show="copied" x-cloak class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
    </svg>
</button>
