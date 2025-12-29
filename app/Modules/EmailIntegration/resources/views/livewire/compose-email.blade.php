<div x-data x-on:open-compose-email.window="$wire.openComposeEmail($event.detail.contactId)">
    @if($showModal)
        <!-- Backdrop -->
        <div class="fixed inset-0 z-40" style="background-color: rgba(0,0,0,0.5);" wire:click="closeModal"></div>

        <!-- Modal -->
        <div class="fixed inset-0 z-50 flex items-center justify-center p-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl" style="width: 600px;">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700" style="padding: 20px 24px;">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Send Email</h3>
                    <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div style="padding: 24px;">
                    @if($accounts->isEmpty())
                        <div class="text-center py-6">
                            <p class="font-medium text-gray-900 dark:text-white">No email account connected</p>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Connect Gmail or Outlook first.</p>
                            <a href="{{ route('settings.email') }}" wire:navigate class="mt-4 inline-block text-sm text-pivor-600 hover:text-pivor-700">
                                Go to Email Settings →
                            </a>
                        </div>
                    @else
                        <form wire:submit="send" style="display: flex; flex-direction: column; gap: 24px;">
                            <!-- To -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">To</label>
                                <div class="rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-3 text-sm text-gray-900 dark:text-white">
                                    {{ $contact?->full_name }} &lt;{{ $contact?->email }}&gt;
                                </div>
                            </div>

                            <!-- Subject -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject</label>
                                <input type="text" wire:model="subject" class="input" placeholder="Enter subject...">
                                @error('subject') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Body -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Message</label>
                                <textarea wire:model="body" rows="6" class="input" placeholder="Write your message..."></textarea>
                                @error('body') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-end gap-3 border-t border-gray-200 dark:border-gray-700" style="padding-top: 20px; margin-top: 10px;">
                                <button type="button" wire:click="closeModal" class="btn-secondary">Cancel</button>
                                <button type="submit" wire:loading.attr="disabled" class="btn-primary">
                                    <span wire:loading.remove wire:target="send">Send</span>
                                    <span wire:loading wire:target="send">Sending...</span>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
