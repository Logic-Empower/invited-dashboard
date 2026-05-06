<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $event->name }}
            </h2>
            <div class="flex space-x-3">
                <x-secondary-button onclick="window.location.href='{{ route('guests.index', $event) }}'">
                    Manage Guests
                </x-secondary-button>                
                <x-secondary-button onclick="window.location.href='{{ route('events.edit', $event) }}'">
                    Edit Event
                </x-secondary-button>
                <form method="POST" action="{{ route('events.destroy', $event) }}" onsubmit="return confirm('Are you sure?')">
                    @csrf
                    @method('DELETE')
                    <x-danger-button type="submit">
                        Delete Event
                    </x-danger-button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                
                <!-- Status Header -->
                <div class="mb-6 flex items-center space-x-4">
                    <span class="px-3 py-1 rounded-full text-sm font-semibold {{ optional($event->expires_at)->isPast() ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                        {{ optional($event->expires_at)->isPast() ? 'Expired' : 'Active' }}
                    </span>
                    <span class="text-gray-500 text-sm">
                        Created on {{ $event->created_at->format('l, M d, Y @ H:i') }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Left Column: Details -->
                    <div class="md:col-span-2 space-y-6">
                        <div>
                            <h4 class="text-sm font-bold text-gray-400 uppercase tracking-widest">Description</h4>
                            <p class="mt-2 text-gray-700 leading-relaxed whitespace-pre-line">
                                {{ $event->description ?? 'No description provided.' }}
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="border p-4 rounded-lg">
                                <span class="block text-xs text-gray-500 uppercase">Event Date</span>
                                <span class="text-gray-800 font-medium">{{ optional($event->event_date)->format('l, M d, Y @ H:i') }}</span>
                            </div>
                            <div class="border p-4 rounded-lg">
                                <span class="block text-xs text-gray-500 uppercase">Registration Deadline</span>
                                <span class="text-gray-800 font-medium">{{ optional($event->expires_at)->format('l, M d, Y @ H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Stats -->
                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                        <h4 class="text-sm font-bold text-gray-800 mb-4">Attendance Overview</h4>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Capacity</span>
                                {{-- <span class="font-bold">{{ $event->capacity ?? 'Unlimited' }}</span> --}}
                                <span class="font-bold">
                                    @if(is_null($event->remaining_seats))
                                        Unlimited
                                    @else
                                        {{ $event->remaining_seats }} / {{ $event->capacity }}
                                    @endif                                    
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Total Guests</span>
                                <span class="font-bold">{{ $event->guests_count ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Confirmed</span>
                                <span class="font-bold text-green-600">{{ $event->confirmed_guests_count ?? 0 }}</span>
                            </div>
                            
                            <!-- Simple Progress Bar -->
                            @if($event->capacity)
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="flex justify-between text-xs mb-1">
                                        <span>Filling Rate</span>
                                        <span>{{ number_format(($event->confirmed_guests_count / $event->capacity) * 100, 0) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ ($event->confirmed_guests_count / $event->capacity) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Back Link -->
                <div class="mt-8 pt-6 border-t">
                    <a href="{{ route('events.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Back to All Events
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
