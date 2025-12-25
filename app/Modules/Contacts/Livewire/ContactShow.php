<?php

namespace App\Modules\Contacts\Livewire;

use App\Modules\Contacts\Models\Contact;
use Livewire\Component;

class ContactShow extends Component
{
    public Contact $contact;

    public function mount(Contact $contact): void
    {
        $this->contact = $contact->load(['client', 'communications', 'assignedUser']);
    }

    public function render()
    {
        return view('contacts::livewire.contact-show')
            ->layout('components.layouts.app', ['title' => $this->contact->full_name]);
    }
}
