<?php

namespace App\Modules\Communications\Livewire;

use App\Modules\Core\Models\User;
use App\Modules\Clients\Models\Client;
use App\Modules\Communications\Models\Communication;
use App\Modules\Contacts\Models\Contact;
use Livewire\Component;

class CommunicationForm extends Component
{
    public ?Communication $communication = null;

    public string $type = 'note';
    public string $direction = 'internal';
    public string $subject = '';
    public string $content = '';
    public ?int $client_id = null;
    public ?int $contact_id = null;
    public ?string $due_at = null;
    public string $priority = 'normal';
    public ?int $assigned_to = null;
    public string $status = 'completed';

    protected function rules(): array
    {
        return [
            'type' => 'required|in:email,phone,meeting,note,task',
            'direction' => 'required|in:inbound,outbound,internal',
            'subject' => 'required|string|max:255',
            'content' => 'nullable|string|max:10000',
            'client_id' => 'nullable|exists:clients,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'due_at' => 'nullable|date',
            'priority' => 'required|in:low,normal,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ];
    }

    public function mount(?Communication $communication = null): void
    {
        if ($communication && $communication->exists) {
            $this->communication = $communication;
            $this->fill([
                ...$communication->toArray(),
                'due_at' => $communication->due_at?->format('Y-m-d\TH:i'),
            ]);
        }

        // Pre-fill from query string
        if (request()->has('client_id')) {
            $this->client_id = (int) request()->get('client_id');
        }
        if (request()->has('contact_id')) {
            $this->contact_id = (int) request()->get('contact_id');
        }
    }

    public function updatedType(): void
    {
        // Set default status based on type
        if ($this->type === 'task') {
            $this->status = 'pending';
        } else {
            $this->status = 'completed';
        }
    }

    public function save(): void
    {
        $validated = $this->validate();

        // Convert due_at to proper datetime
        if (!empty($validated['due_at'])) {
            $validated['due_at'] = \Carbon\Carbon::parse($validated['due_at']);
        }

        if ($this->communication) {
            $this->communication->update($validated);
            session()->flash('message', 'Communication updated successfully.');
        } else {
            Communication::create($validated);
            session()->flash('message', 'Communication created successfully.');
        }

        $this->redirect(route('communications.index'), navigate: true);
    }

    public function render()
    {
        $users = User::orderBy('name')->get();
        $clients = Client::orderBy('name')->get();
        $contacts = Contact::orderBy('first_name')->get();

        return view('communications::livewire.communication-form', [
            'users' => $users,
            'clients' => $clients,
            'contacts' => $contacts,
            'isEditing' => (bool) $this->communication,
        ])->layout('components.layouts.app', [
            'title' => $this->communication ? 'Edit Communication' : 'Add Communication',
        ]);
    }
}
