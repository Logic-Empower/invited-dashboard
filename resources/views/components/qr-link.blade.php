@props([
    'url',
    'label' => 'Open QR code link',
    'size' => '56',
    'downloadName' => 'qr-code.svg',
])

@php
    $qrCode = (new \chillerlan\QRCode\QRCode())->render($url);
@endphp

<div class="inline-flex items-center justify-center gap-2">
    <a
        href="{{ $url }}"
        title="{{ $label }}"
        aria-label="{{ $label }}"
        target="_blank"
        class="inline-flex rounded border border-gray-200 bg-white p-1 transition hover:border-indigo-300 hover:shadow-sm"
    >
        <img
            src="{{ $qrCode }}"
            alt="{{ $label }}"
            width="{{ $size }}"
            height="{{ $size }}"
            class="block"
        >
    </a>

    <a
        href="{{ $qrCode }}"
        download="{{ $downloadName }}"
        title="Download QR code"
        aria-label="Download QR code"
        class="inline-flex h-8 w-8 items-center justify-center rounded text-gray-400 transition hover:bg-gray-100 hover:text-indigo-600"
    >
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 4v12m0 0l-4-4m4 4l4-4" />
        </svg>
    </a>
</div>
