@php
    // Logic: Is the current count less than the allowed max?
    $canAdd = auth()->user()->events->count() < auth()->user()->max_events;
@endphp

<div class="inline-block" x-data="{ tooltip: false }">
    @if($canAdd)
        <x-primary-button onclick="window.location.href='{{ route('events.create') }}'">
            {{ __('Add Event') }}
        </x-primary-button>
    @else
        <div class="relative flex items-center" @mouseenter="tooltip = true" @mouseleave="tooltip = false">
            <x-secondary-button class="opacity-50 cursor-not-allowed" disabled>
                {{ __('Add Event') }}
            </x-secondary-button>
            
            <!-- Simple Tooltip -->
            <div x-show="tooltip" 
                 class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-800 text-white text-xs rounded shadow-lg whitespace-nowrap">
                Limit of {{ auth()->user()->max_events }} events reached
            </div>
        </div>
    @endif
</div>
