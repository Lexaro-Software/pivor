<?php

namespace App\Modules\EmailIntegration\Livewire;

use App\Modules\Contacts\Models\Contact;
use App\Modules\EmailIntegration\Models\UserEmailAccount;
use App\Modules\EmailIntegration\Jobs\SendEmailJob;
use Livewire\Attributes\On;
use Livewire\Component;

class ComposeEmail extends Component
{
    public ?Contact $contact = null;
    public string $subject = '';
    public string $body = '';
    public bool $showModal = false;
    public ?int $selectedAccountId = null;

    #[On('openComposeEmail')]
    public function openComposeEmail($contactId): void
    {
        $this->contact = Contact::findOrFail($contactId);
        $this->showModal = true;
        $this->subject = '';
        $this->body = '';

        // Select first available account
        $firstAccount = auth()->user()->emailAccounts()->first();
        $this->selectedAccountId = $firstAccount?->id;
    }

    public function send(): void
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'selectedAccountId' => 'required|exists:user_email_accounts,id',
        ]);

        $account = UserEmailAccount::findOrFail($this->selectedAccountId);

        if ($account->user_id !== auth()->id()) {
            session()->flash('error', 'Invalid email account.');
            return;
        }

        if (!$this->contact->email) {
            session()->flash('error', 'Contact has no email address.');
            return;
        }

        SendEmailJob::dispatch($account, [
            'to' => $this->contact->email,
            'to_name' => $this->contact->full_name,
            'subject' => $this->subject,
            'body' => $this->body,
            'contact_id' => $this->contact->id,
            'client_id' => $this->contact->client_id,
        ]);

        $this->showModal = false;
        $this->reset(['subject', 'body']);
        session()->flash('message', 'Email sent successfully!');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['subject', 'body', 'contact']);
    }

    public function render()
    {
        $accounts = auth()->user()->emailAccounts ?? collect();

        return view('email-integration::livewire.compose-email', [
            'accounts' => $accounts,
        ]);
    }
}
