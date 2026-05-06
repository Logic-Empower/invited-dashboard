<x-app-layout>
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6 mt-3">
                <div class="flex flex-col">
                    <h2 class="text-2xl font-semibold text-gray-800">Add Guest</h2>
                    <p class="text-sm text-gray-500">Event: {{ $event->name }}</p>
                </div>
                <x-secondary-button onclick="window.location.href='{{ route('guests.index', $event) }}'">
                    Back to Guest List
                </x-secondary-button>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                <form method="post" action="{{ route('guests.store', $event) }}" class="space-y-6">
                    @csrf

                    <!-- Guest Name -->
                    <div>
                        <x-input-label for="name" :value="__('Guest Full Name *')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Email -->
                        <div>
                            <x-input-label for="email" :value="__('Email Address')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <!-- Mobile -->
                        <div>
                            <x-input-label for="mobile" :value="__('Mobile Number')" />
                            <x-text-input id="mobile" name="mobile" type="text" class="mt-1 block w-full" :value="old('mobile')" placeholder="+961..." />
                            <x-input-error class="mt-2" :messages="$errors->get('mobile')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-gray-50 rounded-lg">
                        <!-- Max Seats -->
                        <div>
                            <x-input-label for="max_seats" :value="__('Allowed Seats (Max)')" />
                            <x-text-input id="max_seats" name="max_seats" type="number" min="1" class="mt-1 block w-full" :value="old('max_seats', 1)" required max="{{ $event->remaining_seats }}" />
                            <p class="mt-1 text-xs text-gray-500">Total people this guest can bring (including themselves).</p>
                            <x-input-error class="mt-2" :messages="$errors->get('max_seats')" />
                        </div>

                        <!-- Confirmed Seats (Default 0 or 1) -->
                        <div>
                            <x-input-label for="confirmed_seats" :value="__('Initially Confirmed Seats')" />
                            <x-text-input id="confirmed_seats" name="confirmed_seats" type="number" min="0" class="mt-1 block w-full" :value="old('confirmed_seats', 0)" />
                            <p class="mt-1 text-xs text-gray-500">Number of seats already confirmed by you.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('confirmed_seats')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="status" :value="__('Initial Status')" />
                        <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('status')" />
                    </div>

                    <div class="flex items-center gap-4 pt-4 border-t">
                        <x-primary-button>{{ __('Save Guest') }}</x-primary-button>
                        <x-secondary-button type="button" onclick="window.location.href='{{ route('guests.index', $event) }}'">
                            {{ __('Cancel') }}
                        </x-secondary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>