<?php

namespace App\Modules\EmailIntegration\Livewire;

use App\Modules\EmailIntegration\Models\UserEmailAccount;
use App\Modules\EmailIntegration\Services\GmailService;
use App\Modules\EmailIntegration\Services\MicrosoftGraphService;
use App\Modules\EmailIntegration\Jobs\SyncInboundEmailsJob;
use Livewire\Component;

class EmailSettings extends Component
{
    public ?UserEmailAccount $gmailAccount = null;
    public ?UserEmailAccount $outlookAccount = null;

    public function mount(): void
    {
        $this->loadAccounts();
    }

    protected function loadAccounts(): void
    {
        $this->gmailAccount = auth()->user()->emailAccounts()
            ->where('provider', 'gmail')
            ->first();
        $this->outlookAccount = auth()->user()->emailAccounts()
            ->where('provider', 'outlook')
            ->first();
    }

    public function connectGmail()
    {
        $url = app(GmailService::class)->getAuthUrl();
        return redirect($url);
    }

    public function connectOutlook()
    {
        $url = app(MicrosoftGraphService::class)->getAuthUrl();
        return redirect($url);
    }

    public function disconnect(string $provider): void
    {
        auth()->user()->emailAccounts()
            ->where('provider', $provider)
            ->delete();

        $this->loadAccounts();
        session()->flash('message', ucfirst($provider) . ' account disconnected.');
    }

    public function syncNow(string $provider): void
    {
        $account = auth()->user()->emailAccounts()
            ->where('provider', $provider)
            ->firstOrFail();

        SyncInboundEmailsJob::dispatch($account);
        session()->flash('message', 'Sync started. New emails will appear shortly.');
    }

    public function render()
    {
        return view('email-integration::livewire.email-settings')
            ->layout('components.layouts.app', ['title' => 'Email Settings']);
    }
}
