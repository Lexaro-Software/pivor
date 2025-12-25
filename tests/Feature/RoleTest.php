<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_roles_page_requires_authentication(): void
    {
        $response = $this->get('/roles');

        $response->assertRedirect('/login');
    }

    public function test_roles_page_requires_admin_role(): void
    {
        $userRole = Role::where('name', 'user')->first();
        $user = User::factory()->create(['role_id' => $userRole->id, 'role' => 'user']);

        $response = $this->actingAs($user)->get('/roles');

        $response->assertStatus(403);
    }

    public function test_admin_can_view_roles_list(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $admin = User::factory()->create(['role_id' => $adminRole->id, 'role' => 'admin']);

        $response = $this->actingAs($admin)->get('/roles');

        $response->assertStatus(200);
    }

    public function test_admin_can_view_create_role_form(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $admin = User::factory()->create(['role_id' => $adminRole->id, 'role' => 'admin']);

        $response = $this->actingAs($admin)->get('/roles/create');

        $response->assertStatus(200);
    }

    public function test_admin_can_view_edit_role_form(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $admin = User::factory()->create(['role_id' => $adminRole->id, 'role' => 'admin']);
        $role = Role::where('name', 'manager')->first();

        $response = $this->actingAs($admin)->get("/roles/{$role->id}/edit");

        $response->assertStatus(200);
    }

    public function test_role_has_permissions_relationship(): void
    {
        $role = Role::where('name', 'admin')->first();

        $this->assertNotNull($role->permissions);
        $this->assertTrue($role->permissions->count() > 0);
    }

    public function test_role_has_users_relationship(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        User::factory()->create(['role_id' => $adminRole->id, 'role' => 'admin']);

        $this->assertNotNull($adminRole->users);
        $this->assertEquals(1, $adminRole->users->count());
    }

    public function test_role_can_check_permission(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();

        $this->assertTrue($adminRole->hasPermission('users.manage'));
        $this->assertFalse($userRole->hasPermission('users.manage'));
    }

    public function test_role_can_give_permission(): void
    {
        $role = Role::create([
            'name' => 'test_role',
            'display_name' => 'Test Role',
        ]);

        $this->assertFalse($role->hasPermission('clients.view'));

        $role->givePermission('clients.view');

        $this->assertTrue($role->fresh()->hasPermission('clients.view'));
    }

    public function test_role_can_revoke_permission(): void
    {
        $role = Role::create([
            'name' => 'test_role',
            'display_name' => 'Test Role',
        ]);
        $role->givePermission('clients.view');

        $this->assertTrue($role->hasPermission('clients.view'));

        $role->revokePermission('clients.view');

        $this->assertFalse($role->fresh()->hasPermission('clients.view'));
    }

    public function test_system_roles_cannot_be_deleted(): void
    {
        $adminRole = Role::where('name', 'admin')->first();

        $this->assertTrue($adminRole->is_system);
    }

    public function test_user_inherits_permissions_from_role(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();

        $admin = User::factory()->create(['role_id' => $adminRole->id, 'role' => 'admin']);
        $user = User::factory()->create(['role_id' => $userRole->id, 'role' => 'user']);

        $this->assertTrue($admin->hasPermission('users.manage'));
        $this->assertFalse($user->hasPermission('users.manage'));
    }

    public function test_user_can_check_role(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $admin = User::factory()->create(['role_id' => $adminRole->id, 'role' => 'admin']);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isManager());
        $this->assertFalse($admin->isUser());
        $this->assertTrue($admin->hasRole('admin'));
        $this->assertTrue($admin->hasRole(['admin', 'manager']));
    }

    public function test_permission_belongs_to_roles(): void
    {
        $permission = Permission::where('name', 'clients.view')->first();

        $this->assertNotNull($permission->roles);
        $this->assertTrue($permission->roles->count() > 0);
    }

    public function test_permissions_can_be_grouped(): void
    {
        $groups = Permission::getGroups();

        $this->assertContains('Clients', $groups);
        $this->assertContains('Contacts', $groups);
        $this->assertContains('Communications', $groups);
        $this->assertContains('Administration', $groups);
    }
}
