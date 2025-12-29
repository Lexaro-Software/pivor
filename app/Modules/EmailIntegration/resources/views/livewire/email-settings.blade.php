<div>
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Email Integration</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Connect your email to sync communications with your contacts</p>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-800 rounded-lg text-green-700 dark:text-green-300">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-800 rounded-lg text-red-700 dark:text-red-300">
            {{ session('error') }}
        </div>
    @endif

    <!-- Email Accounts Table -->
    <div class="card overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Provider</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Last Sync</th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <!-- Gmail Row -->
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-10 flex items-center justify-center">
                                <img src="{{ asset('images/providers/gmail.png') }}" alt="Gmail" class="h-8 w-auto">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">Gmail</div>
                                @if($gmailAccount)
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $gmailAccount->email_address }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($gmailAccount)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">
                                Connected
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                Not connected
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $gmailAccount?->last_sync_at?->diffForHumans() ?? '—' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-3">
                            @if($gmailAccount)
                                <button wire:click="syncNow('gmail')" wire:loading.attr="disabled" class="text-pivor-600 hover:text-pivor-900 dark:text-pivor-400 dark:hover:text-pivor-300">
                                    <span wire:loading.remove wire:target="syncNow('gmail')">Sync</span>
                                    <span wire:loading wire:target="syncNow('gmail')">Syncing...</span>
                                </button>
                                <button wire:click="disconnect('gmail')" wire:confirm="Are you sure you want to disconnect Gmail?" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                    Disconnect
                                </button>
                            @else
                                <button wire:click="connectGmail" class="text-pivor-600 hover:text-pivor-900 dark:text-pivor-400 dark:hover:text-pivor-300">
                                    Connect
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>

                <!-- Outlook Row -->
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-10 flex items-center justify-center">
                                <img src="{{ asset('images/providers/outlook.png') }}" alt="Outlook" class="h-8 w-auto">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">Outlook</div>
                                @if($outlookAccount)
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $outlookAccount->email_address }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($outlookAccount)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">
                                Connected
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                Not connected
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $outlookAccount?->last_sync_at?->diffForHumans() ?? '—' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-3">
                            @if($outlookAccount)
                                <button wire:click="syncNow('outlook')" wire:loading.attr="disabled" class="text-pivor-600 hover:text-pivor-900 dark:text-pivor-400 dark:hover:text-pivor-300">
                                    <span wire:loading.remove wire:target="syncNow('outlook')">Sync</span>
                                    <span wire:loading wire:target="syncNow('outlook')">Syncing...</span>
                                </button>
                                <button wire:click="disconnect('outlook')" wire:confirm="Are you sure you want to disconnect Outlook?" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                    Disconnect
                                </button>
                            @else
                                <button wire:click="connectOutlook" class="text-pivor-600 hover:text-pivor-900 dark:text-pivor-400 dark:hover:text-pivor-300">
                                    Connect
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Info -->
    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
        Emails from your CRM contacts sync automatically every 5 minutes.
    </p>
</div>
