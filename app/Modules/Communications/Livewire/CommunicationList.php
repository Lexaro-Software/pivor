<?php

namespace App\Modules\Communications\Livewire;

use App\Modules\Clients\Models\Client;
use App\Modules\Communications\Models\Communication;
use App\Modules\Contacts\Models\Contact;
use App\Modules\Core\Traits\WithCsvImport;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CommunicationList extends Component
{
    use WithPagination, WithCsvImport;

    public string $search = '';
    public string $type = '';
    public string $status = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'type' => ['except' => ''],
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

    public function deleteCommunication(int $id): void
    {
        $communication = Communication::findOrFail($id);
        $communication->delete();

        session()->flash('message', 'Communication deleted successfully.');
    }

    public function markComplete(int $id): void
    {
        $communication = Communication::findOrFail($id);
        $communication->markAsCompleted();

        session()->flash('message', 'Task marked as completed.');
    }

    // Import implementation
    protected function getImportFields(): array
    {
        return [
            'type' => 'Type',
            'subject' => 'Subject',
            'description' => 'Description',
            'client_name' => 'Client Name',
            'contact_name' => 'Contact Name',
            'status' => 'Status',
            'priority' => 'Priority',
            'due_at' => 'Due Date',
        ];
    }

    protected function getImportRules(): array
    {
        return [
            'type' => 'required|in:email,phone,meeting,note,task',
            'subject' => 'required|string|max:255',
            'status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'priority' => 'nullable|in:low,medium,high,urgent',
        ];
    }

    protected function createFromImport(array $data): void
    {
        // Handle client lookup by name
        $clientId = null;
        if (!empty($data['client_name'])) {
            $client = Client::where('name', $data['client_name'])
                ->orWhere('trading_name', $data['client_name'])
                ->first();
            $clientId = $client?->id;
        }
        unset($data['client_name']);

        // Handle contact lookup by name
        $contactId = null;
        if (!empty($data['contact_name'])) {
            $nameParts = explode(' ', $data['contact_name'], 2);
            $firstName = $nameParts[0];
            $lastName = $nameParts[1] ?? '';
            $contact = Contact::where('first_name', $firstName)
                ->when($lastName, fn ($q) => $q->where('last_name', $lastName))
                ->first();
            $contactId = $contact?->id;
        }
        unset($data['contact_name']);

        // Handle due_at date parsing
        if (!empty($data['due_at'])) {
            $data['due_at'] = \Carbon\Carbon::parse($data['due_at']);
        }

        $data['client_id'] = $clientId;
        $data['contact_id'] = $contactId;
        $data['status'] = $data['status'] ?? 'pending';
        $data['priority'] = $data['priority'] ?? 'medium';
        $data['created_by'] = auth()->id();
        $data['assigned_to'] = auth()->id();
        Communication::create($data);
    }

    public function exportCommunications(): StreamedResponse
    {
        $filename = 'communications_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['type', 'subject', 'description', 'client_name', 'contact_name', 'status', 'priority', 'due_at', 'completed_at']);

            Communication::visibleTo(auth()->user())->with(['client', 'contact'])->chunk(100, function ($communications) use ($handle) {
                foreach ($communications as $comm) {
                    fputcsv($handle, [
                        $comm->type,
                        $comm->subject,
                        $comm->description,
                        $comm->client?->name,
                        $comm->contact ? $comm->contact->first_name . ' ' . $comm->contact->last_name : '',
                        $comm->status,
                        $comm->priority,
                        $comm->due_at?->format('Y-m-d H:i'),
                        $comm->completed_at?->format('Y-m-d H:i'),
                    ]);
                }
            });
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function downloadTemplate(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['type', 'subject', 'description', 'client_name', 'contact_name', 'status', 'priority', 'due_at']);
            fputcsv($handle, ['email', 'Follow up on proposal', 'Discussed pricing options', 'Acme Corp', 'John Doe', 'completed', 'medium', '2025-01-15 10:00']);
            fclose($handle);
        }, 'communications_template.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function render()
    {
        $communications = Communication::query()
            ->visibleTo(auth()->user())
            ->with(['client', 'contact', 'createdBy'])
            ->when($this->search, function ($query) {
                $query->where('subject', 'like', '%' . $this->search . '%');
            })
            ->when($this->type, fn ($query) => $query->where('type', $this->type))
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('communications::livewire.communication-list', [
            'communications' => $communications,
            'importFields' => $this->getImportFields(),
        ])->layout('components.layouts.app', ['title' => 'Communications']);
    }
}
