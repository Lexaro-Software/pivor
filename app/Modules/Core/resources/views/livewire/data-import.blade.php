<div>
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Import Data</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Import your data from CSV files</p>
    </div>

    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            @foreach ([1 => 'Select Type', 2 => 'Upload File', 3 => 'Map Fields', 4 => 'Preview', 5 => 'Complete'] as $num => $label)
                <div class="flex items-center {{ $num < 5 ? 'flex-1' : '' }}">
                    <button
                        wire:click="goToStep({{ $num }})"
                        @class([
                            'flex items-center justify-center w-10 h-10 rounded-full text-sm font-medium transition-colors',
                            'bg-pivor-600 text-white' => $step === $num,
                            'bg-pivor-100 text-pivor-600 dark:bg-pivor-900/50 dark:text-pivor-400' => $step > $num,
                            'bg-gray-200 text-gray-500 dark:bg-gray-700 dark:text-gray-400' => $step < $num,
                            'cursor-pointer hover:bg-pivor-200 dark:hover:bg-pivor-800' => $step > $num,
                            'cursor-default' => $step <= $num,
                        ])
                        @disabled($step <= $num)
                    >
                        @if ($step > $num)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @else
                            {{ $num }}
                        @endif
                    </button>
                    <span class="ml-2 text-sm font-medium {{ $step >= $num ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400' }} hidden sm:inline">
                        {{ $label }}
                    </span>
                    @if ($num < 5)
                        <div class="flex-1 mx-4 h-0.5 {{ $step > $num ? 'bg-pivor-600' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-800 rounded-lg text-red-700 dark:text-red-300">
            {{ session('error') }}
        </div>
    @endif

    <!-- Step 1: Select Type -->
    @if ($step === 1)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <button wire:click="selectType('clients')" class="card p-6 text-left hover:shadow-lg transition-shadow hover:border-pivor-500 dark:hover:border-pivor-400">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-pivor-100 dark:bg-pivor-900/50 rounded-lg">
                        <svg class="w-6 h-6 text-pivor-600 dark:text-pivor-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="ml-4 text-lg font-medium text-gray-900 dark:text-white">Import Clients</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Import companies, organizations, or individuals into your CRM.
                </p>
            </button>

            <button wire:click="selectType('contacts')" class="card p-6 text-left hover:shadow-lg transition-shadow hover:border-pivor-500 dark:hover:border-pivor-400">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="ml-4 text-lg font-medium text-gray-900 dark:text-white">Import Contacts</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Import people and their contact information, optionally linked to clients.
                </p>
            </button>

            <button wire:click="selectType('communications')" class="card p-6 text-left hover:shadow-lg transition-shadow hover:border-pivor-500 dark:hover:border-pivor-400">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-green-100 dark:bg-green-900/50 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h3 class="ml-4 text-lg font-medium text-gray-900 dark:text-white">Import Communications</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Import emails, calls, meetings, notes, and tasks.
                </p>
            </button>
        </div>
    @endif

    <!-- Step 2: Upload File -->
    @if ($step === 2)
        <div class="card p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                Upload CSV File for {{ ucfirst($importType) }}
            </h3>

            <div class="mb-6">
                <label class="block">
                    <div class="flex items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:border-pivor-500 dark:hover:border-pivor-400 transition-colors {{ $csvFile ? 'bg-green-50 dark:bg-green-900/20 border-green-500 dark:border-green-400' : '' }}">
                        <div class="text-center">
                            @if ($csvFile)
                                <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="mt-2 text-sm text-green-600 dark:text-green-400">{{ $csvFile->getClientOriginalName() }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Click to change file</p>
                            @else
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium text-pivor-600 dark:text-pivor-400">Click to upload</span> or drag and drop
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">CSV file up to 10MB</p>
                            @endif
                        </div>
                    </div>
                    <input type="file" wire:model="csvFile" accept=".csv,.txt" class="hidden">
                </label>
                @error('csvFile') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror

                <div wire:loading wire:target="csvFile" class="mt-4 flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing file...
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                <button wire:click="goToStep(1)" class="btn-secondary">
                    Back
                </button>
            </div>
        </div>
    @endif

    <!-- Step 3: Map Fields -->
    @if ($step === 3)
        <div class="card p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Map CSV Columns to Fields</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Match your CSV columns to the corresponding database fields. Required fields are marked with *</p>

            <div class="space-y-4 mb-6">
                @foreach ($csvHeaders as $header)
                    <div class="flex items-center gap-4">
                        <div class="w-1/3">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded block truncate" title="{{ $header }}">
                                {{ $header }}
                            </span>
                        </div>
                        <div class="flex items-center text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </div>
                        <div class="w-1/2">
                            <select wire:model="fieldMapping.{{ $header }}" class="input">
                                <option value="">-- Skip this column --</option>
                                @foreach ($this->availableFields as $field => $config)
                                    <option value="{{ $field }}">
                                        {{ $config['label'] }}{{ $config['required'] ? ' *' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Preview -->
            @if (count($csvPreview) > 0)
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preview (first {{ count($csvPreview) }} rows)</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    @foreach ($csvHeaders as $header)
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ Str::limit($header, 15) }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($csvPreview as $row)
                                    <tr>
                                        @foreach ($csvHeaders as $header)
                                            <td class="px-3 py-2 text-gray-600 dark:text-gray-400 truncate max-w-32">{{ $row[$header] ?? '' }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                <button wire:click="goToStep(2)" class="btn-secondary">
                    Back
                </button>
                <button wire:click="proceedToPreview" class="btn-primary">
                    Continue
                </button>
            </div>
        </div>
    @endif

    <!-- Step 4: Preview & Import -->
    @if ($step === 4)
        <div class="card p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Ready to Import</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Review your settings and click Import to proceed.</p>

            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Import Type</dt>
                        <dd class="text-sm text-gray-900 dark:text-white capitalize">{{ $importType }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">File</dt>
                        <dd class="text-sm text-gray-900 dark:text-white">{{ $csvFile->getClientOriginalName() }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Mapped Fields</dt>
                        <dd class="text-sm text-gray-900 dark:text-white">
                            @foreach (array_filter($fieldMapping) as $csv => $field)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-pivor-100 text-pivor-800 dark:bg-pivor-900/50 dark:text-pivor-300 mr-1 mb-1">
                                    {{ $csv }} → {{ $this->availableFields[$field]['label'] ?? $field }}
                                </span>
                            @endforeach
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg mb-6">
                <div class="flex">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <p class="ml-3 text-sm text-yellow-700 dark:text-yellow-300">
                        This action will create new records. Make sure your data is correct before proceeding.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                <button wire:click="goToStep(3)" class="btn-secondary" wire:loading.attr="disabled">
                    Back
                </button>
                <button wire:click="executeImport" class="btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="executeImport">Import Data</span>
                    <span wire:loading wire:target="executeImport" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Importing...
                    </span>
                </button>
            </div>
        </div>
    @endif

    <!-- Step 5: Complete -->
    @if ($step === 5)
        <div class="card p-6 text-center">
            @if ($importResults['success'] > 0)
                <div class="mx-auto w-16 h-16 bg-green-100 dark:bg-green-900/50 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-2">Import Complete!</h3>
            @else
                <div class="mx-auto w-16 h-16 bg-red-100 dark:bg-red-900/50 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-2">Import Failed</h3>
            @endif

            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-6 inline-block">
                <div class="flex items-center gap-6 text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Total rows:</span>
                        <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $importResults['total'] }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Imported:</span>
                        <span class="font-medium text-green-600 dark:text-green-400 ml-1">{{ $importResults['success'] }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Errors:</span>
                        <span class="font-medium text-red-600 dark:text-red-400 ml-1">{{ count($importResults['errors']) }}</span>
                    </div>
                </div>
            </div>

            @if (count($importResults['errors']) > 0)
                <div class="text-left mb-6 max-h-48 overflow-y-auto bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-red-800 dark:text-red-300 mb-2">Errors:</h4>
                    <ul class="text-sm text-red-700 dark:text-red-400 space-y-1">
                        @foreach ($importResults['errors'] as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex items-center justify-center gap-4">
                <button wire:click="resetImport" class="btn-secondary">
                    Import More Data
                </button>
                <a href="{{ route($importType . '.index') }}" wire:navigate class="btn-primary">
                    View {{ ucfirst($importType) }}
                </a>
            </div>
        </div>
    @endif
</div>
