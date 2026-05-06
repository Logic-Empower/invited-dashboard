<x-app-layout>
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex justify-between items-center mb-6 mt-3">
                <!-- Left side -->
                <h2 class="text-2xl font-semibold text-gray-800">My Events</h2>

                <!-- Right side -->
                <div class="flex space-x-3">
                    <x-secondary-button onclick="window.location.href='{{ route('events.index') }}'">
                        All Events
                    </x-secondary-button>
                    <x-add-event-button />
                </div>
            </div>

            <!-- Inside dashboard.blade.php or index.blade.php -->
            <x-events-list :events="$events" />

        </div>
    </div>
</x-app-layout>