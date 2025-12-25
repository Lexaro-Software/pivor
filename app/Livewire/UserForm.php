<?php

namespace App\Livewire;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UserForm extends Component
{
    public ?User $user = null;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public ?int $role_id = null;

    protected function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user?->id),
            ],
            'role_id' => 'required|exists:roles,id',
        ];

        // Password required only for new users
        if (!$this->user) {
            $rules['password'] = 'required|string|min:8|confirmed';
        } else {
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }

        return $rules;
    }

    public function mount(?User $user = null): void
    {
        // Set default role to 'user' role
        $defaultRole = Role::where('name', 'user')->first();
        $this->role_id = $defaultRole?->id;

        if ($user && $user->exists) {
            $this->user = $user;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role_id = $user->role_id ?? $defaultRole?->id;
        }
    }

    public function save(): void
    {
        $validated = $this->validate();

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
        ];

        // Also update legacy role column for backwards compatibility
        $role = Role::find($validated['role_id']);
        if ($role) {
            $data['role'] = $role->name;
        }

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        if ($this->user) {
            // Prevent demoting yourself from admin
            $adminRole = Role::where('name', 'admin')->first();
            if ($this->user->id === auth()->id() && $this->user->role_id === $adminRole?->id && $validated['role_id'] !== $adminRole?->id) {
                session()->flash('error', 'You cannot demote yourself from admin.');
                return;
            }

            $this->user->update($data);
            session()->flash('message', 'User updated successfully.');
        } else {
            User::create($data);
            session()->flash('message', 'User created successfully.');
        }

        $this->redirect(route('users.index'), navigate: true);
    }

    public function render()
    {
        $roles = Role::orderBy('display_name')->get();

        return view('livewire.user-form', [
            'isEditing' => (bool) $this->user,
            'roles' => $roles,
        ])->layout('components.layouts.app', [
            'title' => $this->user ? 'Edit User' : 'Create User',
        ]);
    }
}
