<div>
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('contacts.index') }}" wire:navigate class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Contacts
        </a>
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center">
                    <span class="text-xl font-medium text-gray-600 dark:text-gray-300">{{ $contact->initials }}</span>
                </div>
                <div class="ml-4">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $contact->full_name }}</h2>
                    @if ($contact->job_title || $contact->client)
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $contact->job_title }}
                            @if ($contact->job_title && $contact->client) at @endif
                            @if ($contact->client)
                                <a href="{{ route('clients.show', $contact->client) }}" wire:navigate class="text-pivor-600 hover:text-pivor-700 dark:text-pivor-400">
                                    {{ $contact->client->display_name }}
                                </a>
                            @endif
                        </p>
                    @endif
                </div>
            </div>
            <a href="{{ route('contacts.edit', $contact) }}" wire:navigate class="btn-primary">
                Edit Contact
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Recent Communications -->
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Communications</h3>
                    <a href="{{ route('communications.create', ['contact_id' => $contact->id]) }}" wire:navigate class="text-sm text-pivor-600 hover:text-pivor-700 dark:text-pivor-400">
                        Add Communication
                    </a>
                </div>
                @if ($contact->communications->count() > 0)
                    <ul class="space-y-3">
                        @foreach ($contact->communications->take(10) as $comm)
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
            <!-- Contact Details -->
            <div class="card p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Contact Details</h3>
                <dl class="space-y-3">
                    @if ($contact->email)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                            <dd class="mt-1">
                                <a href="mailto:{{ $contact->email }}" class="text-sm text-pivor-600 hover:text-pivor-700 dark:text-pivor-400">{{ $contact->email }}</a>
                            </dd>
                        </div>
                    @endif
                    @if ($contact->phone)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                            <dd class="mt-1">
                                <a href="tel:{{ $contact->phone }}" class="text-sm text-pivor-600 hover:text-pivor-700 dark:text-pivor-400">{{ $contact->phone }}</a>
                            </dd>
                        </div>
                    @endif
                    @if ($contact->mobile)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Mobile</dt>
                            <dd class="mt-1">
                                <a href="tel:{{ $contact->mobile }}" class="text-sm text-pivor-600 hover:text-pivor-700 dark:text-pivor-400">{{ $contact->mobile }}</a>
                            </dd>
                        </div>
                    @endif
                    @if ($contact->linkedin_url)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">LinkedIn</dt>
                            <dd class="mt-1">
                                <a href="{{ $contact->linkedin_url }}" target="_blank" class="text-sm text-pivor-600 hover:text-pivor-700 dark:text-pivor-400">View Profile</a>
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- Notes -->
            @if ($contact->notes)
                <div class="card p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Notes</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $contact->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
