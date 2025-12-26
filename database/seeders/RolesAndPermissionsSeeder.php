<?php

namespace Database\Seeders;

use App\Modules\Core\Models\Permission;
use App\Modules\Core\Models\Role;
use App\Modules\Core\Models\User;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // Clients
            ['name' => 'clients.view', 'display_name' => 'View Clients', 'group' => 'Clients'],
            ['name' => 'clients.create', 'display_name' => 'Create Clients', 'group' => 'Clients'],
            ['name' => 'clients.edit', 'display_name' => 'Edit Clients', 'group' => 'Clients'],
            ['name' => 'clients.delete', 'display_name' => 'Delete Clients', 'group' => 'Clients'],

            // Contacts
            ['name' => 'contacts.view', 'display_name' => 'View Contacts', 'group' => 'Contacts'],
            ['name' => 'contacts.create', 'display_name' => 'Create Contacts', 'group' => 'Contacts'],
            ['name' => 'contacts.edit', 'display_name' => 'Edit Contacts', 'group' => 'Contacts'],
            ['name' => 'contacts.delete', 'display_name' => 'Delete Contacts', 'group' => 'Contacts'],

            // Communications
            ['name' => 'communications.view', 'display_name' => 'View Communications', 'group' => 'Communications'],
            ['name' => 'communications.create', 'display_name' => 'Create Communications', 'group' => 'Communications'],
            ['name' => 'communications.edit', 'display_name' => 'Edit Communications', 'group' => 'Communications'],
            ['name' => 'communications.delete', 'display_name' => 'Delete Communications', 'group' => 'Communications'],

            // Records visibility
            ['name' => 'records.view_all', 'display_name' => 'View All Records', 'description' => 'Can view records assigned to other users', 'group' => 'Records'],
            ['name' => 'records.edit_all', 'display_name' => 'Edit All Records', 'description' => 'Can edit records assigned to other users', 'group' => 'Records'],

            // Administration
            ['name' => 'users.view', 'display_name' => 'View Users', 'group' => 'Administration'],
            ['name' => 'users.manage', 'display_name' => 'Manage Users', 'description' => 'Can create, edit, and delete users', 'group' => 'Administration'],
            ['name' => 'roles.manage', 'display_name' => 'Manage Roles', 'description' => 'Can create, edit, and delete roles', 'group' => 'Administration'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Create roles
        $adminRole = Role::updateOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator',
                'description' => 'Full access to all features and settings',
                'is_system' => true,
            ]
        );

        $managerRole = Role::updateOrCreate(
            ['name' => 'manager'],
            [
                'display_name' => 'Manager',
                'description' => 'Can view and manage all CRM data',
                'is_system' => true,
            ]
        );

        $userRole = Role::updateOrCreate(
            ['name' => 'user'],
            [
                'display_name' => 'User',
                'description' => 'Can view and manage assigned records only',
                'is_system' => true,
            ]
        );

        // Assign permissions to admin (all permissions)
        $adminRole->permissions()->sync(Permission::all()->pluck('id'));

        // Assign permissions to manager
        $managerPermissions = Permission::whereIn('name', [
            'clients.view', 'clients.create', 'clients.edit', 'clients.delete',
            'contacts.view', 'contacts.create', 'contacts.edit', 'contacts.delete',
            'communications.view', 'communications.create', 'communications.edit', 'communications.delete',
            'records.view_all', 'records.edit_all',
        ])->pluck('id');
        $managerRole->permissions()->sync($managerPermissions);

        // Assign permissions to user
        $userPermissions = Permission::whereIn('name', [
            'clients.view', 'clients.create', 'clients.edit',
            'contacts.view', 'contacts.create', 'contacts.edit',
            'communications.view', 'communications.create', 'communications.edit',
        ])->pluck('id');
        $userRole->permissions()->sync($userPermissions);

        // Update existing users with legacy roles to use new role system
        User::whereNull('role_id')->each(function ($user) use ($adminRole, $managerRole, $userRole) {
            $roleMap = [
                'admin' => $adminRole->id,
                'manager' => $managerRole->id,
                'user' => $userRole->id,
            ];

            $user->update([
                'role_id' => $roleMap[$user->role] ?? $userRole->id,
            ]);
        });
    }
}
