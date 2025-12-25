<div>
    <!-- Welcome -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Welcome back!</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Here's what's happening with your CRM today.</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="card p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 p-3 bg-pivor-100 dark:bg-pivor-900/50 rounded-lg">
                    <svg class="w-6 h-6 text-pivor-600 dark:text-pivor-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Clients</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_clients'] }}</p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 p-3 bg-green-100 dark:bg-green-900/50 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Clients</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['active_clients'] }}</p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 p-3 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Contacts</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_contacts'] }}</p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 p-3 {{ $stats['overdue_tasks'] > 0 ? 'bg-red-100 dark:bg-red-900/50' : 'bg-yellow-100 dark:bg-yellow-900/50' }} rounded-lg">
                    <svg class="w-6 h-6 {{ $stats['overdue_tasks'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-yellow-600 dark:text-yellow-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Tasks</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                        {{ $stats['pending_tasks'] }}
                        @if ($stats['overdue_tasks'] > 0)
                            <span class="text-sm text-red-600 dark:text-red-400">({{ $stats['overdue_tasks'] }} overdue)</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Clients -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Clients</h3>
                <a href="{{ route('clients.index') }}" wire:navigate class="text-sm text-pivor-600 hover:text-pivor-700 dark:text-pivor-400">View all</a>
            </div>
            @if ($recentClients->count() > 0)
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($recentClients as $client)
                        <li class="py-3">
                            <a href="{{ route('clients.show', $client) }}" wire:navigate class="flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 -mx-3 px-3 py-2 rounded-lg transition-colors">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $client->display_name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $client->city ?? 'No location' }}</p>
                                </div>
                                @php
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
                                        'prospect' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
                                        'inactive' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                        'archived' => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusColors[$client->status] ?? $statusColors['inactive'] }} capitalize">
                                    {{ $client->status }}
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No clients yet. <a href="{{ route('clients.create') }}" wire:navigate class="text-pivor-600 hover:text-pivor-700 dark:text-pivor-400">Add your first client</a>.</p>
            @endif
        </div>

        <!-- Upcoming Tasks -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Upcoming Tasks</h3>
                <a href="{{ route('communications.index', ['type' => 'task']) }}" wire:navigate class="text-sm text-pivor-600 hover:text-pivor-700 dark:text-pivor-400">View all</a>
            </div>
            @if ($upcomingTasks->count() > 0)
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($upcomingTasks as $task)
                        <li class="py-3">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $task->subject }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Due {{ $task->due_at->diffForHumans() }}
                                        @if ($task->client)
                                            &middot; {{ $task->client->display_name }}
                                        @endif
                                    </p>
                                </div>
                                @if ($task->is_overdue)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">
                                        Overdue
                                    </span>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No upcoming tasks.</p>
            @endif
        </div>

        <!-- Recent Communications -->
        <div class="card p-6 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Activity</h3>
                <a href="{{ route('communications.index') }}" wire:navigate class="text-sm text-pivor-600 hover:text-pivor-700 dark:text-pivor-400">View all</a>
            </div>
            @if ($recentCommunications->count() > 0)
                <ul class="space-y-3">
                    @foreach ($recentCommunications as $comm)
                        <li class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-0.5">
                                @php
                                    $typeColors = [
                                        'email' => 'bg-blue-100 dark:bg-blue-900/50',
                                        'phone' => 'bg-green-100 dark:bg-green-900/50',
                                        'meeting' => 'bg-purple-100 dark:bg-purple-900/50',
                                        'note' => 'bg-gray-100 dark:bg-gray-700',
                                        'task' => 'bg-yellow-100 dark:bg-yellow-900/50',
                                    ];
                                @endphp
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $typeColors[$comm->type] ?? $typeColors['note'] }}">
                                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                </span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $comm->subject }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ ucfirst($comm->type) }} &middot; {{ $comm->created_at->diffForHumans() }}
                                    @if ($comm->client)
                                        &middot; {{ $comm->client->display_name }}
                                    @elseif ($comm->contact)
                                        &middot; {{ $comm->contact->full_name }}
                                    @endif
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No recent activity.</p>
            @endif
        </div>
    </div>
</div>
