<x-app-layout>
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex justify-between items-center mb-6 mt-3">
                <h2 class="text-2xl font-semibold text-gray-800">New Event</h2>
                <x-secondary-button onclick="window.location.href='{{ route('events.index') }}'">
                    My Events
                </x-secondary-button>
            </div>

            <form method="post" action="{{ route('events.store') }}" class="mt-6 space-y-6">
                @csrf

                <div>
                    <x-input-label for="name" :value="__('Event Name *')" />
                    <x-text-input 
                    id="name" 
                    name="name" 
                    type="text" 
                    class="mt-1 block w-full" 
                    value="{{ old('name') }}"
                    required 
                    autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div> 
                <div>
                    <x-input-label for="description" :value="__('Description')" />
                    <x-textarea 
                    id="description" 
                    name="description" 
                    class="mt-1 block w-full" 
                    autofocus>
                        {{ old('description') }}
                    </x-textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('description')" />
                </div> 

                <div>
                    <x-input-label for="capacity" :value="__('Max Capicity')" />
                    <x-text-input 
                    id="capacity" 
                    name="capacity" 
                    type="number" 
                    class="mt-1 block w-full" 
                    value="{{ old('capacity') }}"
                    autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('capacity')" />
                </div> 

                <div>
                    <x-input-label for="event_date" :value="__('Event Date *')" />
                    <x-text-input 
                    id="event_date" 
                    name="event_date" 
                    type="datetime-local" 
                    class="mt-1 block w-full" 
                    value="{{ old('event_date') }}"
                    required 
                    autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('event_date')" />
                </div> 

                <div>
                    <x-input-label for="expires_at" :value="__('Invitations Expire At *')" />
                    <x-text-input 
                    id="expires_at" 
                    name="expires_at" 
                    type="datetime-local" 
                    class="mt-1 block w-full"
                    value="{{ old('expires_at') }}" 
                    required 
                    autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('expires_at')" />
                </div> 

                <x-primary-button>{{ __('Save') }}</x-primary-button>

            </form>
        </div>
    </div>
</x-app-layout>