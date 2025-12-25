<div>
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('communications.index') }}" wire:navigate class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Communications
        </a>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ $isEditing ? 'Edit Communication' : 'Add Communication' }}
        </h2>
    </div>

    <form wire:submit="save" class="space-y-6">
        <!-- Type and Direction -->
        <div class="card p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Type</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="type" class="label">Communication Type *</label>
                    <select id="type" wire:model.live="type" class="input">
                        <option value="note">Note</option>
                        <option value="email">Email</option>
                        <option value="phone">Phone Call</option>
                        <option value="meeting">Meeting</option>
                        <option value="task">Task</option>
                    </select>
                    @error('type') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="direction" class="label">Direction *</label>
                    <select id="direction" wire:model="direction" class="input">
                        <option value="internal">Internal</option>
                        <option value="inbound">Inbound</option>
                        <option value="outbound">Outbound</option>
                    </select>
                    @error('direction') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="status" class="label">Status *</label>
                    <select id="status" wire:model="status" class="input">
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    @error('status') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="card p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Details</h3>
            <div class="space-y-6">
                <div>
                    <label for="subject" class="label">Subject *</label>
                    <input type="text" id="subject" wire:model="subject" class="input" required>
                    @error('subject') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="content" class="label">Content</label>
                    <textarea id="content" wire:model="content" rows="5" class="input"></textarea>
                    @error('content') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Relationships -->
        <div class="card p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Related To</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="client_id" class="label">Client</label>
                    <select id="client_id" wire:model="client_id" class="input">
                        <option value="">No client</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->display_name }}</option>
                        @endforeach
                    </select>
                    @error('client_id') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="contact_id" class="label">Contact</label>
                    <select id="contact_id" wire:model="contact_id" class="input">
                        <option value="">No contact</option>
                        @foreach ($contacts as $contact)
                            <option value="{{ $contact->id }}">{{ $contact->full_name }}</option>
                        @endforeach
                    </select>
                    @error('contact_id') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Task Settings (shown for tasks) -->
        @if ($type === 'task')
            <div class="card p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Task Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="due_at" class="label">Due Date</label>
                        <input type="datetime-local" id="due_at" wire:model="due_at" class="input">
                        @error('due_at') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="priority" class="label">Priority</label>
                        <select id="priority" wire:model="priority" class="input">
                            <option value="low">Low</option>
                            <option value="normal">Normal</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                        @error('priority') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="assigned_to" class="label">Assigned To</label>
                        <select id="assigned_to" wire:model="assigned_to" class="input">
                            <option value="">Not assigned</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('assigned_to') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="flex items-center justify-end space-x-4">
            <a href="{{ route('communications.index') }}" wire:navigate class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">
                {{ $isEditing ? 'Update' : 'Save' }} Communication
            </button>
        </div>
    </form>
</div>
