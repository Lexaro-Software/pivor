<div x-show="$wire.showImportModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="$wire.showImportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$wire.closeImportModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="$wire.showImportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" @click.stop class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">

            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $title }}</h3>
                    <button @click="$wire.closeImportModal()" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Steps indicator -->
                <div class="mt-4 flex items-center space-x-2">
                    @foreach(['Upload', 'Map Fields', 'Results'] as $i => $stepName)
                        <div class="flex items-center">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full text-xs font-medium {{ $this->importStep > $i + 1 ? 'bg-green-500 text-white' : ($this->importStep == $i + 1 ? 'bg-pivor-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500') }}">
                                @if($this->importStep > $i + 1)
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                @else
                                    {{ $i + 1 }}
                                @endif
                            </span>
                            <span class="ml-2 text-sm {{ $this->importStep == $i + 1 ? 'text-gray-900 dark:text-white font-medium' : 'text-gray-500' }}">{{ $stepName }}</span>
                        </div>
                        @if($i < 2)
                            <div class="flex-1 h-px bg-gray-200 dark:bg-gray-700 mx-2"></div>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="px-6 py-4">
                <!-- Step 1: Upload -->
                @if($this->importStep === 1)
                    <div class="space-y-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Upload a CSV file to import. You can download a template first.</p>

                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center">
                            <input type="file" wire:model="csvFile" accept=".csv,.txt" class="hidden" id="csv-upload-{{ $title }}">
                            <label for="csv-upload-{{ $title }}" class="cursor-pointer">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Click to upload or drag and drop</p>
                                <p class="text-xs text-gray-500">CSV file up to 10MB</p>
                            </label>
                        </div>

                        <div wire:loading wire:target="csvFile" class="text-center text-sm text-gray-500">
                            Processing file...
                        </div>

                        @error('csvFile')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <!-- Step 2: Map Fields -->
                @if($this->importStep === 2)
                    <div class="space-y-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Map your CSV columns to the correct fields.</p>

                        <div class="max-h-64 overflow-y-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">CSV Column</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Maps To</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Preview</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($this->csvHeaders as $header)
                                        <tr>
                                            <td class="px-3 py-2 text-sm font-medium text-gray-900 dark:text-white">{{ $header }}</td>
                                            <td class="px-3 py-2">
                                                <select wire:model="fieldMapping.{{ $header }}" class="input text-sm py-1">
                                                    <option value="">-- Skip --</option>
                                                    @foreach($fields as $field => $label)
                                                        <option value="{{ $field }}">{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-3 py-2 text-sm text-gray-500 truncate max-w-32">
                                                {{ $this->csvPreview[0][$header] ?? '' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Step 3: Results -->
                @if($this->importStep === 3)
                    <div class="space-y-4">
                        <div class="flex items-center justify-center space-x-8">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-green-600">{{ $this->importResults['success'] ?? 0 }}</div>
                                <div class="text-sm text-gray-500">Imported</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-red-600">{{ count($this->importResults['errors'] ?? []) }}</div>
                                <div class="text-sm text-gray-500">Errors</div>
                            </div>
                        </div>

                        @if(!empty($this->importResults['errors']))
                            <div class="mt-4 max-h-32 overflow-y-auto bg-red-50 dark:bg-red-900/20 rounded-lg p-3">
                                @foreach($this->importResults['errors'] as $error)
                                    <p class="text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex justify-between">
                <button wire:click="closeImportModal" class="btn-secondary">
                    {{ $this->importStep === 3 ? 'Close' : 'Cancel' }}
                </button>

                @if($this->importStep === 2)
                    <button wire:click="executeImport" class="btn-primary">
                        Import Data
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
