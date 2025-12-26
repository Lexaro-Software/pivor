<div>
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Export Data</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Download your CRM data as CSV files</p>
    </div>

    <!-- Export Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Clients -->
        <div class="card p-6">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-pivor-100 dark:bg-pivor-900/50 rounded-lg">
                    <svg class="w-6 h-6 text-pivor-600 dark:text-pivor-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Clients</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $counts['clients'] }} records</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Export all client data including company details, contact information, and addresses.
            </p>
            <button wire:click="exportClients" class="btn-primary w-full" @if($counts['clients'] === 0) disabled @endif>
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Download CSV
            </button>
        </div>

        <!-- Contacts -->
        <div class="card p-6">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Contacts</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $counts['contacts'] }} records</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Export all contacts with their personal details, job information, and client associations.
            </p>
            <button wire:click="exportContacts" class="btn-primary w-full" @if($counts['contacts'] === 0) disabled @endif>
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Download CSV
            </button>
        </div>

        <!-- Communications -->
        <div class="card p-6">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-green-100 dark:bg-green-900/50 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Communications</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $counts['communications'] }} records</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Export all communications including emails, calls, meetings, notes, and tasks.
            </p>
            <button wire:click="exportCommunications" class="btn-primary w-full" @if($counts['communications'] === 0) disabled @endif>
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Download CSV
            </button>
        </div>
    </div>

    <!-- Info -->
    <div class="mt-8 card p-4 bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800">
        <div class="flex">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="ml-3">
                <p class="text-sm text-blue-700 dark:text-blue-300">
                    CSV files are UTF-8 encoded and compatible with Excel, Google Sheets, and other spreadsheet applications.
                </p>
            </div>
        </div>
    </div>
</div>
