<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'group',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public static function getGroups(): array
    {
        return static::query()
            ->select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group')
            ->toArray();
    }

    public static function getByGroup(): array
    {
        return static::query()
            ->orderBy('group')
            ->orderBy('display_name')
            ->get()
            ->groupBy('group')
            ->toArray();
    }
}
