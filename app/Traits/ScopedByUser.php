<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ScopedByUser
{
    public function scopeVisibleTo(Builder $query, $user = null): Builder
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return $query->whereRaw('1 = 0'); // Return nothing if no user
        }

        // Admin and Manager can see all records
        if ($user->canViewAllRecords()) {
            return $query;
        }

        // Regular users can only see their assigned records
        return $query->where('assigned_to', $user->id);
    }
}
