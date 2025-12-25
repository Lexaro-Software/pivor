<?php

namespace App\Modules\Contacts\Livewire;

use App\Modules\Contacts\Models\Contact;
use Livewire\Component;
use Livewire\WithPagination;

class ContactList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public ?int $clientId = null;
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function deleteContact(int $id): void
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        session()->flash('message', 'Contact deleted successfully.');
    }

    public function render()
    {
        $contacts = Contact::query()
            ->visibleTo(auth()->user())
            ->with('client')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', '%' . $this->search . '%')
                      ->orWhere('last_name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->when($this->clientId, fn ($query) => $query->where('client_id', $this->clientId))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('contacts::livewire.contact-list', [
            'contacts' => $contacts,
        ])->layout('components.layouts.app', ['title' => 'Contacts']);
    }
}
