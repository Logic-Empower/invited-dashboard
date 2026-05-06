<x-app-layout>
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6 mt-3">
                <div class="flex flex-col">
                    <h2 class="text-2xl font-semibold text-gray-800">Edit Guest</h2>
                    <p class="text-sm text-gray-500">Event: {{ $event->name }}</p>
                </div>
                <x-secondary-button onclick="window.location.href='{{ route('guests.index', $event) }}'">
                    Back to Guest List
                </x-secondary-button>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                <form method="post" action="{{ route('guests.update', [$event, $guest]) }}" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label for="name" :value="__('Guest Full Name *')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $guest->name)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="email" :value="__('Email Address')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $guest->email)" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div>
                            <x-input-label for="mobile" :value="__('Mobile Number')" />
                            <x-text-input id="mobile" name="mobile" type="text" class="mt-1 block w-full" :value="old('mobile', $guest->mobile)" placeholder="+961..." />
                            <x-input-error class="mt-2" :messages="$errors->get('mobile')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-gray-50 rounded-lg">
                        <div>
                            <x-input-label for="max_seats" :value="__('Allowed Seats (Max)')" />
                            <x-text-input id="max_seats" name="max_seats" type="number" min="1" class="mt-1 block w-full" :value="old('max_seats', $guest->max_seats)" required />
                            <p class="mt-1 text-xs text-gray-500">Total people this guest can bring, including themselves.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('max_seats')" />
                        </div>

                        <div>
                            <x-input-label for="confirmed_seats" :value="__('Confirmed Seats')" />
                            <x-text-input id="confirmed_seats" name="confirmed_seats" type="number" min="0" class="mt-1 block w-full" :value="old('confirmed_seats', $guest->confirmed_seats)" />
                            <p class="mt-1 text-xs text-gray-500">Must be less than or equal to allowed seats.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('confirmed_seats')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach(['pending', 'viewed', 'confirmed', 'declined', 'cancelled'] as $status)
                                    <option value="{{ $status }}" @selected(old('status', $guest->status) === $status)>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>

                        <div>
                            <x-input-label :value="__('Invite Code')" />
                            <div class="mt-1 rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-mono text-gray-500">
                                {{ $guest->invite_code }}
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Invite codes are generated automatically and are not edited here.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-4 border-t">
                        <x-primary-button>{{ __('Update Guest') }}</x-primary-button>
                        <x-secondary-button type="button" onclick="window.location.href='{{ route('guests.index', $event) }}'">
                            {{ __('Cancel') }}
                        </x-secondary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
