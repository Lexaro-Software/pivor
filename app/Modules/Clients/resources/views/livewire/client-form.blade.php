<div>
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('clients.index') }}" wire:navigate class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Clients
        </a>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ $isEditing ? 'Edit Client' : 'Create Client' }}
        </h2>
    </div>

    <form wire:submit="save" class="space-y-6">
        <!-- Basic Information -->
        <div class="card p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="label">Company Name *</label>
                    <input type="text" id="name" wire:model="name" class="input" required>
                    @error('name') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="trading_name" class="label">Trading Name</label>
                    <input type="text" id="trading_name" wire:model="trading_name" class="input">
                    @error('trading_name') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="type" class="label">Type *</label>
                    <select id="type" wire:model="type" class="input">
                        <option value="company">Company</option>
                        <option value="individual">Individual</option>
                        <option value="organisation">Organisation</option>
                    </select>
                    @error('type') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="status" class="label">Status *</label>
                    <select id="status" wire:model="status" class="input">
                        <option value="prospect">Prospect</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="archived">Archived</option>
                    </select>
                    @error('status') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="registration_number" class="label">Registration Number</label>
                    <input type="text" id="registration_number" wire:model="registration_number" class="input">
                    @error('registration_number') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="vat_number" class="label">VAT Number</label>
                    <input type="text" id="vat_number" wire:model="vat_number" class="input">
                    @error('vat_number') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Contact Details -->
        <div class="card p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Contact Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                <div class="md:col-span-2">
                    <label for="website" class="label">Website</label>
                    <input type="text" id="website" wire:model="website" class="input" placeholder="www.example.com">
                    @error('website') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
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

        <!-- Business Information -->
        <div class="card p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Business Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="industry" class="label">Industry</label>
                    <input type="text" id="industry" wire:model="industry" class="input">
                    @error('industry') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="employee_count" class="label">Number of Employees</label>
                    <input type="number" id="employee_count" wire:model="employee_count" class="input" min="0">
                    @error('employee_count') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="annual_revenue" class="label">Annual Revenue (GBP)</label>
                    <input type="number" id="annual_revenue" wire:model="annual_revenue" class="input" min="0" step="0.01">
                    @error('annual_revenue') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
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
                    @error('assigned_to') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="notes" class="label">Notes</label>
                    <textarea id="notes" wire:model="notes" rows="4" class="input"></textarea>
                    @error('notes') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end space-x-4">
            <a href="{{ route('clients.index') }}" wire:navigate class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">
                {{ $isEditing ? 'Update Client' : 'Create Client' }}
            </button>
        </div>
    </form>
</div>
