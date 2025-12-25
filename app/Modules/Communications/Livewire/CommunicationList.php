<?php

namespace App\Modules\Communications\Livewire;

use App\Modules\Communications\Models\Communication;
use Livewire\Component;
use Livewire\WithPagination;

class CommunicationList extends Component
{
    use WithPagination;

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

    public function render()
    {
        $communications = Communication::query()
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
        ])->layout('components.layouts.app', ['title' => 'Communications']);
    }
}
