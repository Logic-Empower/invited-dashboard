<x-app-layout>
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6 mt-3">
                <div class="flex flex-col">
                    <h2 class="text-2xl font-semibold text-gray-800">Import Guests</h2>
                    <p class="text-sm text-gray-500">Event: {{ $event->name }}</p>
                </div>
                <x-secondary-button onclick="window.location.href='{{ route('guests.index', $event->id) }}'">
                    Back to Guest List
                </x-secondary-button>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                <form method="POST" action="{{ route('guests.import.store', $event) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- File Upload Area -->
                    <div x-data="{ fileName: '' }" class="space-y-4">
                        <x-input-label for="csv_file" :value="__('Select CSV File')" />
                        
                        <div class="relative flex items-center justify-center w-full">
                            <label for="csv_file" class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500 font-semibold" x-text="fileName ? fileName : 'Click to upload or drag and drop'"></p>
                                    <p class="text-xs text-gray-400">CSV files only (Max: 5MB)</p>
                                </div>
                                <input id="csv_file" name="csv_file" type="file" accept=".csv" class="hidden" @change="fileName = $event.target.files[0].name" required />
                            </label>
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('csv_file')" />
                    </div>

                    <!-- Instructions Card -->
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">CSV Formatting Requirements</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>The first row must be a header: <strong>name, email, mobile, max_seats</strong>.</li>
                                        <li>At least the <strong>name</strong> column is required.</li>
                                        <li><strong>max_seats</strong> is optional and defaults to 1.</li>
                                        <li>Invite codes are generated automatically during import.</li>
                                    </ul>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('guests.import.sample', $event) }}" class="text-sm font-bold text-blue-800 hover:underline">Download CSV Sample</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-4 border-t">
                        <x-primary-button>{{ __('Start Import') }}</x-primary-button>
                        <x-secondary-button type="button" onclick="window.location.href='{{ route('guests.index', $event->id) }}'">
                            {{ __('Cancel') }}
                        </x-secondary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
