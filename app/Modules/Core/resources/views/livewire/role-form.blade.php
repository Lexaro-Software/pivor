<div>
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('roles.index') }}" wire:navigate class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Roles
        </a>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ $isEditing ? 'Edit Role' : 'Create Role' }}
        </h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ $isEditing ? 'Update role details and permissions' : 'Create a new role with specific permissions' }}
        </p>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-800 rounded-lg text-red-700 dark:text-red-300">
            {{ session('error') }}
        </div>
    @endif

    <!-- Form -->
    <form wire:submit="save" class="space-y-6">
        <div class="card p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Role Details</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" wire:model="name" class="input @error('name') border-red-500 @enderror" placeholder="e.g., sales_rep" @if($isSystemRole) disabled @endif>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Lowercase letters and underscores only</p>
                </div>

                <!-- Display Name -->
                <div>
                    <label for="display_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Display Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="display_name" wire:model="display_name" class="input @error('display_name') border-red-500 @enderror" placeholder="e.g., Sales Representative">
                    @error('display_name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Description
                    </label>
                    <textarea id="description" wire:model="description" rows="2" class="input @error('description') border-red-500 @enderror" placeholder="Brief description of this role's purpose"></textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Permissions -->
        <div class="card p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Permissions</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Select the permissions this role should have.</p>

            @if($isEditing && $role->name === 'admin')
                <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/50 border border-blue-200 dark:border-blue-800 rounded-lg text-blue-700 dark:text-blue-300">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        The Administrator role has all permissions by default.
                    </div>
                </div>
            @endif

            <div class="space-y-6">
                @foreach ($permissionsByGroup as $group => $permissions)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                        <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 flex items-center justify-between">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $group }}</h4>
                            <div class="flex items-center space-x-2">
                                <button type="button" wire:click="selectAllInGroup('{{ $group }}')" class="text-xs text-pivor-600 hover:text-pivor-700 dark:text-pivor-400 dark:hover:text-pivor-300">
                                    Select All
                                </button>
                                <span class="text-gray-300 dark:text-gray-600">|</span>
                                <button type="button" wire:click="deselectAllInGroup('{{ $group }}')" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                    Deselect All
                                </button>
                            </div>
                        </div>
                        <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($permissions as $permission)
                                <label class="flex items-start space-x-3 cursor-pointer group">
                                    <input
                                        type="checkbox"
                                        wire:click="togglePermission({{ $permission->id }})"
                                        @checked(in_array((string) $permission->id, $selectedPermissions))
                                        class="mt-0.5 h-4 w-4 text-pivor-600 border-gray-300 dark:border-gray-600 rounded focus:ring-pivor-500 dark:bg-gray-700"
                                    >
                                    <div class="flex-1">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">
                                            {{ $permission->display_name }}
                                        </span>
                                        @if($permission->description)
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $permission->description }}</p>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('roles.index') }}" wire:navigate class="btn-secondary">
                Cancel
            </a>
            <button type="submit" class="btn-primary">
                <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ $isEditing ? 'Update Role' : 'Create Role' }}
            </button>
        </div>
    </form>
</div>
