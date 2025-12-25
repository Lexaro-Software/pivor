<div>
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('clients.index') }}" wire:navigate class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Clients
        </a>
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $client->display_name }}</h2>
                @if ($client->trading_name && $client->trading_name !== $client->name)
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $client->name }}</p>
                @endif
            </div>
            <div class="flex items-center space-x-2">
                @php
                    $statusColors = [
                        'active' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
                        'prospect' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
                        'inactive' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                        'archived' => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
                    ];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$client->status] ?? $statusColors['inactive'] }} capitalize">
                    {{ $client->status }}
                </span>
                <a href="{{ route('clients.edit', $client) }}" wire:navigate class="btn-primary">
                    Edit Client
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Details Card -->
            <div class="card p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Details</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white capitalize">{{ $client->type }}</dd>
                    </div>
                    @if ($client->registration_number)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Registration Number</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $client->registration_number }}</dd>
                        </div>
                    @endif
                    @if ($client->vat_number)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">VAT Number</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $client->vat_number }}</dd>
                        </div>
                    @endif
                    @if ($client->industry)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Industry</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $client->industry }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- Contacts -->
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Contacts</h3>
                    <a href="{{ route('contacts.create', ['client_id' => $client->id]) }}" wire:navigate class="text-sm text-pivor-600 hover:text-pivor-700 dark:text-pivor-400">
                        Add Contact
                    </a>
                </div>
                @if ($client->contacts->count() > 0)
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($client->contacts as $contact)
                            <li class="py-3 flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ $contact->initials }}</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $contact->full_name }}
                                            @if ($contact->is_primary_contact)
                                                <span class="ml-2 text-xs text-pivor-600 dark:text-pivor-400">Primary</span>
                                            @endif
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $contact->job_title ?? $contact->email }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('contacts.show', $contact) }}" wire:navigate class="text-sm text-pivor-600 hover:text-pivor-700 dark:text-pivor-400">View</a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">No contacts yet.</p>
                @endif
            </div>

            <!-- Recent Communications -->
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Communications</h3>
                    <a href="{{ route('communications.create', ['client_id' => $client->id]) }}" wire:navigate class="text-sm text-pivor-600 hover:text-pivor-700 dark:text-pivor-400">
                        Add Communication
                    </a>
                </div>
                @if ($client->communications->count() > 0)
                    <ul class="space-y-3">
                        @foreach ($client->communications->take(5) as $comm)
                            <li class="flex items-start space-x-3">
                                <div class="flex-shrink-0 mt-0.5">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $comm->subject }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $comm->created_at->diffForHumans() }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">No communications yet.</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Contact Info -->
            <div class="card p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Contact Information</h3>
                <dl class="space-y-3">
                    @if ($client->email)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                            <dd class="mt-1">
                                <a href="mailto:{{ $client->email }}" class="text-sm text-pivor-600 hover:text-pivor-700 dark:text-pivor-400">{{ $client->email }}</a>
                            </dd>
                        </div>
                    @endif
                    @if ($client->phone)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                            <dd class="mt-1">
                                <a href="tel:{{ $client->phone }}" class="text-sm text-pivor-600 hover:text-pivor-700 dark:text-pivor-400">{{ $client->phone }}</a>
                            </dd>
                        </div>
                    @endif
                    @if ($client->website)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Website</dt>
                            <dd class="mt-1">
                                <a href="{{ $client->website }}" target="_blank" class="text-sm text-pivor-600 hover:text-pivor-700 dark:text-pivor-400">{{ $client->website }}</a>
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- Address -->
            @if ($client->full_address)
                <div class="card p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Address</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        {{ $client->address_line_1 }}<br>
                        @if ($client->address_line_2){{ $client->address_line_2 }}<br>@endif
                        {{ $client->city }}@if($client->county), {{ $client->county }}@endif<br>
                        {{ $client->postcode }}<br>
                        {{ $client->country }}
                    </p>
                </div>
            @endif

            <!-- Notes -->
            @if ($client->notes)
                <div class="card p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Notes</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $client->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
