<?php

namespace App\Modules\Clients\Livewire;

use App\Modules\Clients\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;

class ClientList extends Component
{
    use WithPagination;

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
        ])->layout('components.layouts.app', ['title' => 'Clients']);
    }
}
