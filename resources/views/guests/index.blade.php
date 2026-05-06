<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex flex-col">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Guests for: {{ $event->name }}
                </h2>
                <div class="text-sm text-gray-500 mt-1">
                    Event Date: {{ optional($event->event_date)->format('M d, Y') }}
                </div>
            </div>
	            <div class="flex space-x-3">
	                <x-secondary-button onclick="window.location.href='{{ route('guests.export', $event) }}'">
	                    Export Guests
	                </x-secondary-button>
	                <x-secondary-button onclick="window.location.href='{{ route('guests.import', $event) }}'">
	                    Import Guests
                </x-secondary-button>
                <x-primary-button onclick="window.location.href='{{ route('guests.create', $event) }}'">
                    Add New Guest
                </x-primary-button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Event Capacity Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-indigo-500">
                    <div class="text-xs font-bold text-gray-400 uppercase">Max Capacity</div>
                    <div class="text-xl font-bold text-gray-800">{{ $event->capacity ?? 'Unlimited' }}</div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-blue-500">
                    <div class="text-xs font-bold text-gray-400 uppercase">Total Guests</div>
                    <div class="text-xl font-bold text-gray-800">{{ $guests->count() }}</div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-green-500">
                    <div class="text-xs font-bold text-gray-400 uppercase">Confirmed Seats</div>
                    <div class="text-xl font-bold text-green-600">{{ $guests->sum('confirmed_seats') }}</div>
                </div>
                @if($event->capacity)
                <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 {{ ($event->capacity - $guests->sum('confirmed_seats')) <= 0 ? 'border-red-500' : 'border-yellow-500' }}">
                    <div class="text-xs font-bold text-gray-400 uppercase">Remaining Room</div>
                    <div class="text-xl font-bold text-gray-800">
                        @if(is_null($event->remaining_seats))
                            Unlimited
                        @else
                            {{ $event->remaining_seats }}
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Guest Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 text-gray-400 text-xs uppercase">
                                <th class="pb-3 px-2">Guest Name</th>
                                <th class="pb-3 px-2">Contact</th>
                                <th class="pb-3 px-2 text-center">Seats</th>
	                                <th class="pb-3 px-2">Status</th>
	                                <th class="pb-3 px-2">Invite Code</th>
	                                <th class="pb-3 px-2 text-center">QR Code</th>
	                                <th class="pb-3 px-2 text-right">Actions</th>
	                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($guests as $guest)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-4 px-2 font-medium text-gray-800">{{ $guest->name }}</td>
                                <td class="py-4 px-2 text-sm text-gray-600">{{ $guest->email ?? $guest->mobile ?? '-' }}</td>
                                <td class="py-4 px-2 text-center text-sm">
                                    <span class="font-bold">{{ $guest->confirmed_seats }}</span>
                                    <span class="text-gray-400">/ {{ $guest->max_seats }}</span>
                                </td>
                                <td class="py-4 px-2">
                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                        {{ $guest->status === 'confirmed' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $guest->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                        {{ $guest->status === 'declined' || $guest->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}
                                        {{ $guest->status === 'viewed' ? 'bg-blue-100 text-blue-700' : '' }}
                                    ">
                                        {{ $guest->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-2 text-xs font-mono text-gray-400">
                                    <x-copyable-text :value="$guest->invite_code" label="Copy invite code" />
                                </td>
                                <td class="py-4 px-2 text-center">
                                    <x-qr-link
                                        :url="route('rsvp.show', ['code' => $guest->invite_code])"
                                        label="Open RSVP link"
                                        :download-name="'rsvp-'.$guest->invite_code.'.svg'"
                                    />
                                </td>
                                <td class="py-4 px-2 text-right">
                                    <div class="flex justify-end space-x-2">
                                        <a href="{{ route('guests.edit', [$event, $guest]) }}" class="text-indigo-600 hover:text-indigo-900">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('guests.destroy', [$event, $guest]) }}" onsubmit="return confirm('Remove this guest?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                    <td colspan="7" class="py-10 text-center text-gray-400 italic">
                                    No guests found for this event.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Back Link -->
            <a href="{{ route('events.edit', $event) }}" class="text-sm text-gray-500 hover:text-gray-800 flex items-center transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Back to {{ $event->name }} Event
            </a>
        </div>
    </div>
</x-app-layout>
