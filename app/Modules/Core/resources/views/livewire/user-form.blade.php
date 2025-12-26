<div>
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('users.index') }}" wire:navigate class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Users
        </a>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ $isEditing ? 'Edit User' : 'Create User' }}
        </h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ $isEditing ? 'Update user account details' : 'Add a new user to the system' }}
        </p>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-800 rounded-lg text-red-700 dark:text-red-300">
            {{ session('error') }}
        </div>
    @endif

    <!-- Form -->
    <div class="card p-6">
        <form wire:submit="save" class="space-y-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" wire:model="name" class="input @error('name') border-red-500 @enderror" placeholder="John Doe">
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" id="email" wire:model="email" class="input @error('email') border-red-500 @enderror" placeholder="john@example.com">
                @error('email')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role -->
            <div>
                <label for="role_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Role <span class="text-red-500">*</span>
                </label>
                <select id="role_id" wire:model="role_id" class="input @error('role_id') border-red-500 @enderror">
                    <option value="">Select a role...</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                    @endforeach
                </select>
                @error('role_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                @php
                    $selectedRole = $roles->firstWhere('id', $role_id);
                @endphp
                @if($selectedRole && $selectedRole->description)
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $selectedRole->description }}</p>
                @endif
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Password @if(!$isEditing)<span class="text-red-500">*</span>@endif
                </label>
                <input type="password" id="password" wire:model="password" class="input @error('password') border-red-500 @enderror" placeholder="{{ $isEditing ? 'Leave blank to keep current' : 'Enter password' }}">
                @error('password')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                @if($isEditing)
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave blank to keep the current password</p>
                @endif
            </div>

            <!-- Password Confirmation -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Confirm Password @if(!$isEditing)<span class="text-red-500">*</span>@endif
                </label>
                <input type="password" id="password_confirmation" wire:model="password_confirmation" class="input" placeholder="Confirm password">
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6">
                <a href="{{ route('users.index') }}" wire:navigate class="btn-secondary">
                    Cancel
                </a>
                <button type="submit" class="btn-primary">
                    <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ $isEditing ? 'Update User' : 'Create User' }}
                </button>
            </div>
        </form>
    </div>
</div>
