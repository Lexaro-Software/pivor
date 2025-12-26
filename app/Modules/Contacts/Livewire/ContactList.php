<?php

namespace App\Modules\Contacts\Livewire;

use App\Modules\Clients\Models\Client;
use App\Modules\Contacts\Models\Contact;
use App\Modules\Core\Traits\WithCsvImport;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContactList extends Component
{
    use WithPagination, WithCsvImport;

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

    // Import implementation
    protected function getImportFields(): array
    {
        return [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'mobile' => 'Mobile',
            'job_title' => 'Job Title',
            'department' => 'Department',
            'client_name' => 'Client Name',
            'is_primary' => 'Is Primary',
            'status' => 'Status',
            'notes' => 'Notes',
        ];
    }

    protected function getImportRules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'status' => 'nullable|in:active,inactive,archived',
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

        // Handle is_primary conversion
        $data['is_primary_contact'] = in_array(strtolower($data['is_primary'] ?? ''), ['yes', 'true', '1', 'oui']);
        unset($data['is_primary']);

        $data['client_id'] = $clientId;
        $data['status'] = $data['status'] ?? 'active';
        $data['assigned_to'] = auth()->id();
        Contact::create($data);
    }

    public function exportContacts(): StreamedResponse
    {
        $filename = 'contacts_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['first_name', 'last_name', 'email', 'phone', 'mobile', 'job_title', 'department', 'client_name', 'is_primary', 'status', 'notes']);

            Contact::visibleTo(auth()->user())->with('client')->chunk(100, function ($contacts) use ($handle) {
                foreach ($contacts as $contact) {
                    fputcsv($handle, [
                        $contact->first_name,
                        $contact->last_name,
                        $contact->email,
                        $contact->phone,
                        $contact->mobile,
                        $contact->job_title,
                        $contact->department,
                        $contact->client?->name,
                        $contact->is_primary ? 'yes' : 'no',
                        $contact->status,
                        $contact->notes,
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
            fputcsv($handle, ['first_name', 'last_name', 'email', 'phone', 'mobile', 'job_title', 'department', 'client_name', 'is_primary', 'status', 'notes']);
            fputcsv($handle, ['John', 'Doe', 'john@example.com', '+1234567890', '+0987654321', 'CEO', 'Executive', 'Acme Corp', 'yes', 'active', 'Primary contact']);
            fclose($handle);
        }, 'contacts_template.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
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
            'importFields' => $this->getImportFields(),
        ])->layout('components.layouts.app', ['title' => 'Contacts']);
    }
}
