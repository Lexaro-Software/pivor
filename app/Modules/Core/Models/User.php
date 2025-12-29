<?php

namespace App\Modules\Core\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships

    public function roleModel(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function emailAccounts(): HasMany
    {
        return $this->hasMany(\App\Modules\EmailIntegration\Models\UserEmailAccount::class);
    }

    // Role helpers - now check both legacy role column and new role relationship

    public function getRoleName(): string
    {
        // Prefer new role system, fall back to legacy
        return $this->roleModel?->name ?? $this->role ?? 'user';
    }

    public function isAdmin(): bool
    {
        return $this->getRoleName() === 'admin';
    }

    public function isManager(): bool
    {
        return $this->getRoleName() === 'manager';
    }

    public function isUser(): bool
    {
        return $this->getRoleName() === 'user';
    }

    public function hasRole(string|array $roles): bool
    {
        $currentRole = $this->getRoleName();

        if (is_string($roles)) {
            return $currentRole === $roles;
        }

        return in_array($currentRole, $roles);
    }

    public function canManageUsers(): bool
    {
        return $this->hasPermission('users.manage') || $this->isAdmin();
    }

    public function canViewAllRecords(): bool
    {
        return $this->hasPermission('records.view_all') || $this->hasRole(['admin', 'manager']);
    }

    // Permission helpers

    public function hasPermission(string $permission): bool
    {
        $role = $this->roleModel;

        if (!$role) {
            return false;
        }

        // Admin role has all permissions
        if ($role->name === 'admin') {
            return true;
        }

        return $role->hasPermission($permission);
    }

    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    public function getPermissions(): array
    {
        $role = $this->roleModel;

        if (!$role) {
            return [];
        }

        return $role->permissions->pluck('name')->toArray();
    }
}
