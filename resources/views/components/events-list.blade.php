@props(['events'])
@if($events->isEmpty())
    <div class="bg-white p-6 rounded-lg shadow text-center text-gray-500">
        No events found.
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
        @foreach($events as $event)
            <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">

                <div class="flex justify-between items-start mb-3">
                    <h3 class="text-lg font-bold text-gray-800">
                        {{ $event->name }}
                    </h3>

                    <span class="text-xs px-2 py-1 rounded 
                        {{ optional($event->expires_at)->isPast() ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                        {{ optional($event->expires_at)->isPast() ? 'Expired' : 'Active' }}
                    </span>
                </div>

                <div class="text-sm text-gray-600 space-y-1 mb-4">
                    <div><strong>Date:</strong> {{ optional($event->event_date)->format('M d, Y H:i') }}</div>
                    <div><strong>Expires:</strong> {{ optional($event->expires_at)->format('M d, Y H:i') }}</div>
                </div>

                <div class="grid grid-cols-3 gap-3 text-center text-sm mb-4">

                    <div class="bg-gray-50 p-3 rounded">
                        <div class="font-semibold text-gray-800">
                            @if(is_null($event->remaining_seats))
                                Unlimited
                            @else
                                {{ $event->remaining_seats }} / {{ $event->capacity }}
                            @endif
                        </div>
                        <div class="text-gray-500 text-xs">Remaining Seats</div>
                    </div>

                    <div class="bg-gray-50 p-3 rounded">
                        <div class="font-semibold text-gray-800">{{ $event->guests_count ?? 0 }}</div>
                        <div class="text-gray-500 text-xs">Guests</div>
                    </div>

                    <div class="bg-gray-50 p-3 rounded">
                        <div class="font-semibold text-green-600">{{ $event->confirmed_guests_count ?? 0 }}</div>
                        <div class="text-gray-500 text-xs">Confirmed Seats</div>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <x-secondary-button onclick="window.location.href='{{ route('events.show', $event) }}'">
                        Details
                    </x-secondary-button>

                    <div class="flex space-x-3">
                        <x-secondary-button onclick="window.location.href='{{ route('guests.index', $event) }}'">
                            Manage Guests
                        </x-secondary-button>                        
                        <x-secondary-button onclick="window.location.href='{{ route('events.edit', $event) }}'">
                            Edit
                        </x-secondary-button>
                        &nbsp;
                        <form method="POST" action="{{ route('events.destroy', $event) }}" onsubmit="return confirm('Are you sure you want to delete this event? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')                                        
                            <x-danger-button>
                                Delete
                            </x-danger-button>
                        </form>
                    </div>
                </div>

            </div>
        @endforeach
    </div>
@endif