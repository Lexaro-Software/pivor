<?php

namespace App\Modules\Contacts\Livewire;

use App\Modules\Contacts\Models\Contact;
use App\Modules\Communications\Models\Communication;
use Livewire\Component;

class ContactShow extends Component
{
    public Contact $contact;
    public bool $showEmailModal = false;
    public ?Communication $viewingEmail = null;

    public function mount(Contact $contact): void
    {
        $this->contact = $contact->load(['client', 'assignedUser', 'communications']);
    }

    public function viewEmail(int $id): void
    {
        $this->viewingEmail = Communication::findOrFail($id);
        $this->showEmailModal = true;
    }

    public function closeEmailModal(): void
    {
        $this->showEmailModal = false;
        $this->viewingEmail = null;
    }

    public function render()
    {
        return view('contacts::livewire.contact-show')
            ->layout('components.layouts.app', ['title' => $this->contact->full_name]);
    }
}
