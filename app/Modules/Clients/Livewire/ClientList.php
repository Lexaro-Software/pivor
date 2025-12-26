<?php

namespace App\Modules\Clients\Livewire;

use App\Modules\Clients\Models\Client;
use App\Modules\Core\Traits\WithCsvImport;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientList extends Component
{
    use WithPagination, WithCsvImport;

    public string $search = '';
    public string $status = '';
    public string $type = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'type' => ['except' => ''],
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

    public function deleteClient(int $id): void
    {
        $client = Client::findOrFail($id);
        $client->delete();
        session()->flash('message', 'Client deleted successfully.');
    }

    // Import implementation
    protected function getImportFields(): array
    {
        return [
            'name' => 'Company Name',
            'trading_name' => 'Trading Name',
            'type' => 'Type',
            'status' => 'Status',
            'email' => 'Email',
            'phone' => 'Phone',
            'website' => 'Website',
            'address_line_1' => 'Address Line 1',
            'address_line_2' => 'Address Line 2',
            'city' => 'City',
            'state' => 'State',
            'postal_code' => 'Postal Code',
            'country' => 'Country',
            'notes' => 'Notes',
        ];
    }

    protected function getImportRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'nullable|in:company,individual,organisation',
            'status' => 'nullable|in:active,inactive,prospect,archived',
            'email' => 'nullable|email|max:255',
        ];
    }

    protected function createFromImport(array $data): void
    {
        $data['type'] = $data['type'] ?? 'company';
        $data['status'] = $data['status'] ?? 'prospect';
        $data['assigned_to'] = auth()->id();
        Client::create($data);
    }

    public function exportClients(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, array_keys($this->getImportFields()));

            Client::visibleTo(auth()->user())->chunk(100, function ($clients) use ($handle) {
                foreach ($clients as $client) {
                    fputcsv($handle, [
                        $client->name, $client->trading_name, $client->type, $client->status,
                        $client->email, $client->phone, $client->website,
                        $client->address_line_1, $client->address_line_2,
                        $client->city, $client->state, $client->postal_code, $client->country,
                        $client->notes,
                    ]);
                }
            });
            fclose($handle);
        }, 'clients_' . date('Y-m-d_His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function downloadTemplate(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, array_keys($this->getImportFields()));
            fputcsv($handle, ['Acme Corp', 'Acme', 'company', 'active', 'contact@acme.com', '+1234567890', 'https://acme.com', '123 Main St', 'Suite 100', 'New York', 'NY', '10001', 'USA', 'Important client']);
            fclose($handle);
        }, 'clients_template.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function render()
    {
        $clients = Client::query()
            ->visibleTo(auth()->user())
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('trading_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->when($this->type, fn ($query) => $query->where('type', $this->type))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('clients::livewire.client-list', [
            'clients' => $clients,
            'importFields' => $this->getImportFields(),
        ])->layout('components.layouts.app', ['title' => 'Clients']);
    }
}
