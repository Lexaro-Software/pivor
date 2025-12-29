<div>
    <style>
        .toggle-switch {
            position: relative;
            width: 44px;
            height: 24px;
            background-color: #d1d5db;
            border-radius: 9999px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .toggle-switch.active {
            background-color: #0284c7;
        }
        .toggle-switch .toggle-knob {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 20px;
            height: 20px;
            background-color: white;
            border-radius: 9999px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
            transition: transform 0.2s;
        }
        .toggle-switch.active .toggle-knob {
            transform: translateX(20px);
        }
        .dark .toggle-switch {
            background-color: #4b5563;
        }
        .dark .toggle-switch.active {
            background-color: #0284c7;
        }
    </style>

    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Notification Settings</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Configure how and when you receive notifications</p>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-800 rounded-lg text-green-700 dark:text-green-300">
            {{ session('message') }}
        </div>
    @endif

    <!-- Form -->
    <div class="card p-6">
        <form wire:submit="save" class="space-y-6">
            <!-- Task Reminders Section -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Task Reminders</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Get email reminders for tasks that are due soon.</p>

                <!-- Enable/Disable Toggle -->
                <div class="flex items-center justify-between py-4 border-b border-gray-100 dark:border-gray-700">
                    <div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">Enable task reminders</span>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Receive email notifications about upcoming tasks</p>
                    </div>
                    <button type="button" wire:click="$toggle('taskRemindersEnabled')"
                        class="toggle-switch {{ $taskRemindersEnabled ? 'active' : '' }}"
                        role="switch">
                        <span class="toggle-knob"></span>
                    </button>
                </div>

                @if($taskRemindersEnabled)
                    <div class="mt-6 space-y-4 ml-4 pl-4 border-l-2 border-pivor-500">
                        <!-- Remind Day Before Toggle -->
                        <div class="flex items-center justify-between py-3">
                            <div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Remind 1 day before</span>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Get notified the day before a task is due</p>
                            </div>
                            <button type="button" wire:click="$toggle('remindDayBefore')"
                                class="toggle-switch {{ $remindDayBefore ? 'active' : '' }}"
                                role="switch">
                                <span class="toggle-knob"></span>
                            </button>
                        </div>

                        <!-- Remind Same Day Toggle -->
                        <div class="flex items-center justify-between py-3">
                            <div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Remind on due date</span>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Get notified on the day a task is due</p>
                            </div>
                            <button type="button" wire:click="$toggle('remindSameDay')"
                                class="toggle-switch {{ $remindSameDay ? 'active' : '' }}"
                                role="switch">
                                <span class="toggle-knob"></span>
                            </button>
                        </div>

                        <!-- Reminder Time -->
                        <div class="py-3">
                            <label for="reminderTime" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Send reminders at
                            </label>
                            <input type="time" id="reminderTime" wire:model="reminderTime"
                                class="block w-40 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-pivor-500 focus:ring-pivor-500 sm:text-sm @error('reminderTime') border-red-500 @enderror">
                            @error('reminderTime')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endif
            </div>

            <!-- SMTP Notice -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-blue-500 dark:text-blue-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        Email reminders require SMTP to be configured. Contact your administrator if you're not receiving emails.
                    </p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end pt-8 mt-8 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="btn-primary">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
