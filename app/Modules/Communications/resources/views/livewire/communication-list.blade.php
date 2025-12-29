<div>
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Communications</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Track all interactions and follow-ups</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-2">
            <!-- Import/Export Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" type="button" class="btn-secondary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Import / Export
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-10">
                    <button wire:click="downloadTemplate" @click="open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Template
                    </button>
                    <button wire:click="openImportModal" @click="open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Import CSV
                    </button>
                    <button wire:click="exportCommunications" @click="open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Export CSV
                    </button>
                </div>
            </div>

            <a href="{{ route('communications.create') }}" wire:navigate class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Communication
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="sm:col-span-2">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search communications..." class="input">
            </div>
            <div>
                <select wire:model.live="type" class="input">
                    <option value="">All Types</option>
                    <option value="email">Email</option>
                    <option value="phone">Phone</option>
                    <option value="meeting">Meeting</option>
                    <option value="note">Note</option>
                    <option value="task">Task</option>
                </select>
            </div>
            <div>
                <select wire:model.live="status" class="input">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-800 rounded-lg text-green-700 dark:text-green-300">
            {{ session('message') }}
        </div>
    @endif

    <!-- Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:text-gray-700 dark:hover:text-gray-200" wire:click="sortBy('subject')">Subject</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Related To</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:text-gray-700 dark:hover:text-gray-200" wire:click="sortBy('created_at')">Date</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($communications as $comm)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $typeIcons = [
                                        'email' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>',
                                        'phone' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>',
                                        'meeting' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>',
                                        'note' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>',
                                        'task' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>',
                                    ];
                                @endphp
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        {!! $typeIcons[$comm->type] ?? $typeIcons['note'] !!}
                                    </svg>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $comm->subject }}</p>
                                @if ($comm->is_overdue)
                                    <span class="text-xs text-red-600 dark:text-red-400">Overdue</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                @if ($comm->client)
                                    <a href="{{ route('clients.show', $comm->client) }}" wire:navigate class="hover:text-pivor-600 dark:hover:text-pivor-400">
                                        {{ $comm->client->display_name }}
                                    </a>
                                @elseif ($comm->contact)
                                    <a href="{{ route('contacts.show', $comm->contact) }}" wire:navigate class="hover:text-pivor-600 dark:hover:text-pivor-400">
                                        {{ $comm->contact->full_name }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300',
                                        'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
                                        'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
                                        'cancelled' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$comm->status] ?? $statusColors['pending'] }} capitalize">
                                    {{ str_replace('_', ' ', $comm->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $comm->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    @if ($comm->type === 'email' && $comm->email_body_html)
                                        <button wire:click="viewCommunication({{ $comm->id }})" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                                            View
                                        </button>
                                    @endif
                                    @if ($comm->is_task && $comm->status !== 'completed')
                                        <button wire:click="markComplete({{ $comm->id }})" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                            Complete
                                        </button>
                                    @endif
                                    <a href="{{ route('communications.edit', $comm) }}" wire:navigate class="text-pivor-600 hover:text-pivor-900 dark:text-pivor-400 dark:hover:text-pivor-300">
                                        Edit
                                    </a>
                                    <button wire:click="deleteCommunication({{ $comm->id }})" wire:confirm="Are you sure?" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-white">No communications found</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Start tracking your interactions.</p>
                                    <a href="{{ route('communications.create') }}" wire:navigate class="mt-4 btn-primary">
                                        Add Communication
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($communications->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $communications->links() }}
            </div>
        @endif
    </div>

    <!-- Import Modal -->
    @include('core::components.import-modal', ['title' => 'Import Communications', 'fields' => $importFields])

    <!-- View Email Modal -->
    @if($showViewModal && $viewingCommunication)
        <div class="fixed inset-0 z-40" style="background-color: rgba(0,0,0,0.5);" wire:click="closeViewModal"></div>
        <div class="fixed inset-0 z-50 flex items-center justify-center p-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-h-[80vh] flex flex-col" style="width: 700px;">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 flex-shrink-0" style="padding: 20px 24px;">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $viewingCommunication->subject }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            @if($viewingCommunication->direction === 'inbound')
                                From: {{ $viewingCommunication->email_from }}
                            @else
                                To: {{ is_array($viewingCommunication->email_to) ? implode(', ', $viewingCommunication->email_to) : $viewingCommunication->email_to }}
                            @endif
                            <span class="mx-2">•</span>
                            {{ $viewingCommunication->created_at->format('M d, Y \a\t g:i A') }}
                        </p>
                    </div>
                    <button type="button" wire:click="closeViewModal" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="overflow-y-auto flex-1 text-gray-900 dark:text-white" style="padding: 24px; --tw-text-opacity: 1;">
                    <div class="text-sm" style="color: inherit !important;">
                        <style>.email-body-content, .email-body-content * { color: inherit !important; }</style>
                        <div class="email-body-content">{!! $viewingCommunication->email_body_html !!}</div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="border-t border-gray-200 dark:border-gray-700 flex-shrink-0" style="padding: 16px 24px;">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            @if($viewingCommunication->contact)
                                <a href="{{ route('contacts.show', $viewingCommunication->contact) }}" wire:navigate class="text-pivor-600 hover:text-pivor-700 dark:text-pivor-400">
                                    {{ $viewingCommunication->contact->full_name }}
                                </a>
                            @endif
                        </div>
                        <button type="button" wire:click="closeViewModal" class="btn-secondary">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
