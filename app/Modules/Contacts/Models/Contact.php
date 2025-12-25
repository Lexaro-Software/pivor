<?php

namespace App\Modules\Contacts\Models;

use App\Models\User;
use App\Modules\Clients\Models\Client;
use App\Modules\Communications\Models\Communication;
use App\Traits\ScopedByUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Contact extends Model
{
    use HasFactory, SoftDeletes, ScopedByUser;

    protected static function newFactory()
    {
        return \Database\Factories\ContactFactory::new();
    }

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'mobile',
        'job_title',
        'department',
        'client_id',
        'is_primary_contact',
        'address_line_1',
        'address_line_2',
        'city',
        'county',
        'postcode',
        'country',
        'linkedin_url',
        'status',
        'assigned_to',
        'notes',
    ];

    protected $casts = [
        'is_primary_contact' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });

        // Ensure only one primary contact per client
        static::saving(function ($model) {
            if ($model->is_primary_contact && $model->client_id) {
                static::where('client_id', $model->client_id)
                    ->where('id', '!=', $model->id ?? 0)
                    ->update(['is_primary_contact' => false]);
            }
        });
    }

    // Relationships

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function communications(): HasMany
    {
        return $this->hasMany(Communication::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary_contact', true);
    }

    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    // Accessors

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getInitialsAttribute(): string
    {
        $first = $this->first_name ? strtoupper($this->first_name[0]) : '';
        $last = $this->last_name ? strtoupper($this->last_name[0]) : '';

        return $first . $last;
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->county,
            $this->postcode,
            $this->country,
        ]);

        return implode(', ', $parts);
    }
}
