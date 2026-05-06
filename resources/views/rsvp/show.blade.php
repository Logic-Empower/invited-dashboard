<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center">
            <p class="text-sm font-medium text-indigo-600">{{ $guest->event->name }}</p>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">RSVP Invitation</h1>
            @if($guest->event->event_date)
                <p class="mt-1 text-sm text-gray-500">{{ $guest->event->event_date->format('M d, Y') }}</p>
            @endif
        </div>

        @foreach (['success' => 'green', 'info' => 'blue'] as $type => $color)
            @if(session($type))
                <div class="rounded-md border border-{{ $color }}-200 bg-{{ $color }}-50 px-4 py-3 text-sm text-{{ $color }}-700">
                    {{ session($type) }}
                </div>
            @endif
        @endforeach

        <div class="space-y-3 rounded-md border border-gray-200 bg-gray-50 p-4 text-sm">
            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Name</span>
                <span class="text-right font-medium text-gray-900">{{ $guest->name }}</span>
            </div>
            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Email</span>
                <span class="text-right font-medium text-gray-900">{{ $guest->email ?? '-' }}</span>
            </div>
            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Mobile</span>
                <span class="text-right font-medium text-gray-900">{{ $guest->mobile ?? '-' }}</span>
            </div>
            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Maximum seats</span>
                <span class="text-right font-medium text-gray-900">{{ $guest->max_seats }}</span>
            </div>
        </div>

        @if($guest->responded_at || in_array($guest->status, ['confirmed', 'declined', 'cancelled'], true))
            <div class="rounded-md border border-gray-200 p-4 text-center">
                @if($guest->status === 'confirmed')
                    <p class="text-lg font-semibold text-green-700">Confirmed</p>
                    <p class="mt-1 text-sm text-gray-600">
                        You confirmed {{ $guest->confirmed_seats }} {{ Str::plural('seat', $guest->confirmed_seats) }}.
                    </p>
                @elseif($guest->status === 'declined')
                    <p class="text-lg font-semibold text-red-700">Declined</p>
                    <p class="mt-1 text-sm text-gray-600">You have declined this invitation.</p>
                @else
                    <p class="text-lg font-semibold text-gray-700">Unavailable</p>
                    <p class="mt-1 text-sm text-gray-600">This invitation is no longer available.</p>
                @endif

                @if($guest->responded_at)
                    <p class="mt-3 text-xs text-gray-400">
                        Submitted on {{ $guest->responded_at->format('M d, Y g:i A') }}
                    </p>
                @endif
            </div>
        @else
            <form method="POST" action="{{ route('rsvp.respond', ['code' => $guest->invite_code]) }}" class="space-y-5">
                @csrf

                <div>
                    <label for="confirmed_seats" class="block text-sm font-medium text-gray-700">
                        How many guests will attend?
                    </label>
                    <select
                        id="confirmed_seats"
                        name="confirmed_seats"
                        class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        @for($seat = 1; $seat <= $guest->max_seats; $seat++)
                            <option value="{{ $seat }}" @selected((int) old('confirmed_seats', 1) === $seat)>
                                {{ $seat }} {{ Str::plural('guest', $seat) }}
                            </option>
                        @endfor
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('confirmed_seats')" />
                </div>

                <x-input-error class="mt-2" :messages="$errors->get('response')" />

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <x-primary-button
                        name="response"
                        value="confirmed"
                        class="justify-center"
                    >
                        Confirm
                    </x-primary-button>
                    <x-danger-button
                        name="response"
                        value="declined"
                        class="justify-center"
                    >
                        Decline
                    </x-danger-button>
                </div>
            </form>
        @endif
    </div>
</x-guest-layout>
