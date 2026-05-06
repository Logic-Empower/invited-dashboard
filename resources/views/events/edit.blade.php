<x-app-layout>
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6 mt-3">
                <!-- Left side -->
                <h2 class="text-2xl font-semibold text-gray-800">Edit Event: {{ $event->name }}</h2>

                <!-- Right side -->
                <div class="flex space-x-3">
                    <x-secondary-button onclick="window.location.href='{{ route('guests.index', $event) }}'">
                        Manage Guests
                    </x-secondary-button>
                    <x-secondary-button onclick="window.location.href='{{ route('events.index') }}'">
                        Back to My Events
                    </x-secondary-button>
                </div>
            </div>

            <form method="post" action="{{ route('events.update', $event) }}" class="mt-6 space-y-6">
                @csrf
                @method('PATCH')

                <div>
                    <x-input-label for="name" :value="__('Event Name *')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" 
                        :value="old('name', $event->name)" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="description" :value="__('Description')" />
                    <x-textarea id="description" name="description" class="mt-1 block w-full">
                        {{ old('description', $event->description) }}
                    </x-textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('description')" />
                </div>

                <div>
                    <x-input-label for="capacity" :value="__('Max Capacity')" />
                    <x-text-input id="capacity" name="capacity" type="number" class="mt-1 block w-full" 
                        :value="old('capacity', $event->capacity)" />
                    <x-input-error class="mt-2" :messages="$errors->get('capacity')" />
                </div>

                <div>
                    <x-input-label for="event_date" :value="__('Event Date *')" />
                    <x-text-input id="event_date" name="event_date" type="datetime-local" class="mt-1 block w-full" 
                        :value="old('event_date', optional($event->event_date)->format('Y-m-d\TH:i'))" required />
                    <x-input-error class="mt-2" :messages="$errors->get('event_date')" />
                </div>

                <div>
                    <x-input-label for="expires_at" :value="__('Invitations Expire At *')" />
                    <x-text-input id="expires_at" name="expires_at" type="datetime-local" class="mt-1 block w-full" 
                        :value="old('expires_at', optional($event->expires_at)->format('Y-m-d\TH:i'))" required />
                    <x-input-error class="mt-2" :messages="$errors->get('expires_at')" />
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>{{ __('Update Event') }}</x-primary-button>
                    
                    @if (session('status') === 'event-updated')
                        <p
                            x-data="{ show: true }"
                            x-show="show"
                            x-transition
                            x-init="setTimeout(() => show = false, 2000)"
                            class="text-sm text-gray-600"
                        >{{ __('Saved.') }}</p>
                    @endif
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
