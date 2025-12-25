<?php

namespace App\Modules\Contacts\Livewire;

use App\Models\User;
use App\Modules\Clients\Models\Client;
use App\Modules\Contacts\Models\Contact;
use Livewire\Component;

class ContactForm extends Component
{
    public ?Contact $contact = null;

    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public string $mobile = '';
    public string $job_title = '';
    public string $department = '';
    public ?int $client_id = null;
    public bool $is_primary_contact = false;
    public string $address_line_1 = '';
    public string $address_line_2 = '';
    public string $city = '';
    public string $county = '';
    public string $postcode = '';
    public string $country = 'GB';
    public string $linkedin_url = '';
    public string $status = 'active';
    public ?int $assigned_to = null;
    public string $notes = '';

    protected function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'job_title' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'client_id' => 'nullable|exists:clients,id',
            'is_primary_contact' => 'boolean',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'county' => 'nullable|string|max:100',
            'postcode' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:2',
            'linkedin_url' => 'nullable|url|max:255',
            'status' => 'required|in:active,inactive,archived',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:5000',
        ];
    }

    public function mount(?Contact $contact = null): void
    {
        if ($contact && $contact->exists) {
            $this->contact = $contact;
            $this->fill($contact->toArray());
        }

        // Pre-fill client_id from query string
        if (request()->has('client_id')) {
            $this->client_id = (int) request()->get('client_id');
        }
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->contact) {
            $this->contact->update($validated);
            session()->flash('message', 'Contact updated successfully.');
        } else {
            Contact::create($validated);
            session()->flash('message', 'Contact created successfully.');
        }

        $this->redirect(route('contacts.index'), navigate: true);
    }

    public function render()
    {
        $users = User::orderBy('name')->get();
        $clients = Client::orderBy('name')->get();

        return view('contacts::livewire.contact-form', [
            'users' => $users,
            'clients' => $clients,
            'isEditing' => (bool) $this->contact,
        ])->layout('components.layouts.app', [
            'title' => $this->contact ? 'Edit Contact' : 'Create Contact',
        ]);
    }
}
