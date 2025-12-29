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
            <div class="flex items-center space-x-2">
                @if($contact->email)
                    <button
                        type="button"
                        x-data
                        x-on:click="$dispatch('open-compose-email', { contactId: {{ $contact->id }} })"
                        class="btn-secondary"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Send Email
                    </button>
                @endif
                <a href="{{ route('contacts.edit', $contact) }}" wire:navigate class="btn-primary">
                    Edit Contact
                </a>
            </div>
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
                            <li class="flex items-start space-x-3 {{ $comm->type === 'email' && $comm->email_body_html ? 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 -mx-2 px-2 py-1 rounded-lg' : '' }}"
                                @if($comm->type === 'email' && $comm->email_body_html) wire:click="viewEmail({{ $comm->id }})" @endif>
                                <div class="flex-shrink-0 mt-0.5">
                                    @php
                                        $icon = match($comm->type) {
                                            'email' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                                            'phone' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
                                            default => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $comm->subject }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $comm->created_at->diffForHumans() }}
                                        @if($comm->type === 'email')
                                            <span class="mx-1">•</span>
                                            <span class="text-xs {{ $comm->direction === 'inbound' ? 'text-green-600 dark:text-green-400' : 'text-blue-600 dark:text-blue-400' }}">
                                                {{ $comm->direction === 'inbound' ? 'Received' : 'Sent' }}
                                            </span>
                                        @endif
                                    </p>
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

    <!-- Compose Email Modal -->
    @livewire('email-integration.compose-email')

    <!-- View Email Modal -->
    @if($showEmailModal && $viewingEmail)
        <div class="fixed inset-0 z-40" style="background-color: rgba(0,0,0,0.5);" wire:click="closeEmailModal"></div>
        <div class="fixed inset-0 z-50 flex items-center justify-center p-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-h-[80vh] flex flex-col" style="width: 700px;">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 flex-shrink-0" style="padding: 20px 24px;">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $viewingEmail->subject }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            @if($viewingEmail->direction === 'inbound')
                                From: {{ $viewingEmail->email_from }}
                            @else
                                To: {{ is_array($viewingEmail->email_to) ? implode(', ', $viewingEmail->email_to) : $viewingEmail->email_to }}
                            @endif
                            <span class="mx-2">•</span>
                            {{ $viewingEmail->created_at->format('M d, Y \a\t g:i A') }}
                        </p>
                    </div>
                    <button type="button" wire:click="closeEmailModal" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="overflow-y-auto flex-1 text-gray-900 dark:text-white" style="padding: 24px; --tw-text-opacity: 1;">
                    <div class="text-sm" style="color: inherit !important;">
                        <style>.email-body-content, .email-body-content * { color: inherit !important; }</style>
                        <div class="email-body-content">{!! $viewingEmail->email_body_html !!}</div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="border-t border-gray-200 dark:border-gray-700 flex-shrink-0" style="padding: 16px 24px;">
                    <div class="flex justify-end">
                        <button type="button" wire:click="closeEmailModal" class="btn-secondary">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
