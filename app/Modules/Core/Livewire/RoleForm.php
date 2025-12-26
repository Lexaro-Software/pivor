<?php

namespace App\Modules\Core\Livewire;

use App\Modules\Core\Models\Permission;
use App\Modules\Core\Models\Role;
use Illuminate\Validation\Rule;
use Livewire\Component;

class RoleForm extends Component
{
    public ?Role $role = null;

    public string $name = '';
    public string $display_name = '';
    public string $description = '';
    public array $selectedPermissions = [];

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z_]+$/',
                Rule::unique('roles', 'name')->ignore($this->role?->id),
            ],
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'selectedPermissions' => 'array',
        ];
    }

    protected $messages = [
        'name.regex' => 'The name must contain only lowercase letters and underscores.',
    ];

    public function mount(?Role $role = null): void
    {
        if ($role && $role->exists) {
            $this->role = $role;
            $this->name = $role->name;
            $this->display_name = $role->display_name;
            $this->description = $role->description ?? '';
            $this->selectedPermissions = $role->permissions->pluck('id')->map(fn($id) => (string) $id)->toArray();
        }
    }

    public function save(): void
    {
        $validated = $this->validate();

        // Prevent editing system role names
        if ($this->role && $this->role->is_system && $this->name !== $this->role->name) {
            session()->flash('error', 'Cannot change the name of a system role.');
            return;
        }

        $data = [
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'],
        ];

        if ($this->role) {
            $this->role->update($data);
            $this->role->permissions()->sync($this->selectedPermissions);
            session()->flash('message', 'Role updated successfully.');
        } else {
            $role = Role::create($data);
            $role->permissions()->sync($this->selectedPermissions);
            session()->flash('message', 'Role created successfully.');
        }

        $this->redirect(route('roles.index'), navigate: true);
    }

    public function togglePermission(int $permissionId): void
    {
        $permissionId = (string) $permissionId;

        if (in_array($permissionId, $this->selectedPermissions)) {
            $this->selectedPermissions = array_values(array_diff($this->selectedPermissions, [$permissionId]));
        } else {
            $this->selectedPermissions[] = $permissionId;
        }
    }

    public function selectAllInGroup(string $group): void
    {
        $permissionIds = Permission::where('group', $group)->pluck('id')->map(fn($id) => (string) $id)->toArray();

        foreach ($permissionIds as $id) {
            if (!in_array($id, $this->selectedPermissions)) {
                $this->selectedPermissions[] = $id;
            }
        }
    }

    public function deselectAllInGroup(string $group): void
    {
        $permissionIds = Permission::where('group', $group)->pluck('id')->map(fn($id) => (string) $id)->toArray();

        $this->selectedPermissions = array_values(array_diff($this->selectedPermissions, $permissionIds));
    }

    public function render()
    {
        $permissionsByGroup = Permission::query()
            ->orderBy('group')
            ->orderBy('display_name')
            ->get()
            ->groupBy('group');

        return view('core::livewire.role-form', [
            'isEditing' => (bool) $this->role,
            'isSystemRole' => $this->role?->is_system ?? false,
            'permissionsByGroup' => $permissionsByGroup,
        ])->layout('components.layouts.app', [
            'title' => $this->role ? 'Edit Role' : 'Create Role',
        ]);
    }
}
