<?php

namespace App\Livewire;

use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;

class RoleList extends Component
{
    use WithPagination;

    public string $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function deleteRole(int $id): void
    {
        $role = Role::findOrFail($id);

        if ($role->is_system) {
            session()->flash('error', 'System roles cannot be deleted.');
            return;
        }

        if ($role->users()->count() > 0) {
            session()->flash('error', 'Cannot delete role with assigned users. Please reassign users first.');
            return;
        }

        $role->delete();
        session()->flash('message', 'Role deleted successfully.');
    }

    public function render()
    {
        $roles = Role::query()
            ->withCount(['users', 'permissions'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('display_name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('is_system', 'desc')
            ->orderBy('display_name')
            ->paginate(10);

        return view('livewire.role-list', [
            'roles' => $roles,
        ])->layout('components.layouts.app', ['title' => 'Roles']);
    }
}
