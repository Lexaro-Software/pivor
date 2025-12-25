<div>
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('contacts.index') }}" wire:navigate class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Contacts
        </a>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ $isEditing ? 'Edit Contact' : 'Create Contact' }}
        </h2>
    </div>

    <form wire:submit="save" class="space-y-6">
        <!-- Personal Information -->
        <div class="card p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Personal Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="first_name" class="label">First Name *</label>
                    <input type="text" id="first_name" wire:model="first_name" class="input" required>
                    @error('first_name') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="last_name" class="label">Last Name *</label>
                    <input type="text" id="last_name" wire:model="last_name" class="input" required>
                    @error('last_name') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="email" class="label">Email</label>
                    <input type="email" id="email" wire:model="email" class="input">
                    @error('email') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="phone" class="label">Phone</label>
                    <input type="tel" id="phone" wire:model="phone" class="input">
                    @error('phone') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="mobile" class="label">Mobile</label>
                    <input type="tel" id="mobile" wire:model="mobile" class="input">
                    @error('mobile') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="linkedin_url" class="label">LinkedIn</label>
                    <input type="url" id="linkedin_url" wire:model="linkedin_url" class="input" placeholder="https://linkedin.com/in/...">
                    @error('linkedin_url') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Professional Information -->
        <div class="card p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Professional Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="client_id" class="label">Company</label>
                    <select id="client_id" wire:model="client_id" class="input">
                        <option value="">No company</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->display_name }}</option>
                        @endforeach
                    </select>
                    @error('client_id') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="job_title" class="label">Job Title</label>
                    <input type="text" id="job_title" wire:model="job_title" class="input">
                    @error('job_title') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="department" class="label">Department</label>
                    <input type="text" id="department" wire:model="department" class="input">
                    @error('department') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="status" class="label">Status</label>
                    <select id="status" wire:model="status" class="input">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="archived">Archived</option>
                    </select>
                    @error('status') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="is_primary_contact" class="rounded border-gray-300 text-pivor-600 focus:ring-pivor-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Primary contact for this company</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Address -->
        <div class="card p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Address</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="address_line_1" class="label">Address Line 1</label>
                    <input type="text" id="address_line_1" wire:model="address_line_1" class="input">
                    @error('address_line_1') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="address_line_2" class="label">Address Line 2</label>
                    <input type="text" id="address_line_2" wire:model="address_line_2" class="input">
                    @error('address_line_2') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="city" class="label">City</label>
                    <input type="text" id="city" wire:model="city" class="input">
                    @error('city') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="county" class="label">County</label>
                    <input type="text" id="county" wire:model="county" class="input">
                    @error('county') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="postcode" class="label">Postcode</label>
                    <input type="text" id="postcode" wire:model="postcode" class="input">
                    @error('postcode') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="country" class="label">Country</label>
                    <select id="country" wire:model="country" class="input">
                        <option value="GB">United Kingdom</option>
                        <option value="US">United States</option>
                        <option value="CA">Canada</option>
                        <option value="AU">Australia</option>
                    </select>
                    @error('country') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Internal -->
        <div class="card p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Internal</h3>
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="assigned_to" class="label">Assigned To</label>
                    <select id="assigned_to" wire:model="assigned_to" class="input">
                        <option value="">Not assigned</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="notes" class="label">Notes</label>
                    <textarea id="notes" wire:model="notes" rows="4" class="input"></textarea>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end space-x-4">
            <a href="{{ route('contacts.index') }}" wire:navigate class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">
                {{ $isEditing ? 'Update Contact' : 'Create Contact' }}
            </button>
        </div>
    </form>
</div>
