<?php

namespace App\Modules\Clients\Models;

use App\Models\User;
use App\Modules\Communications\Models\Communication;
use App\Modules\Contacts\Models\Contact;
use App\Traits\ScopedByUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Client extends Model
{
    use HasFactory, SoftDeletes, ScopedByUser;

    protected static function newFactory()
    {
        return \Database\Factories\ClientFactory::new();
    }

    protected $fillable = [
        'name',
        'trading_name',
        'registration_number',
        'vat_number',
        'type',
        'status',
        'email',
        'phone',
        'website',
        'address_line_1',
        'address_line_2',
        'city',
        'county',
        'postcode',
        'country',
        'industry',
        'employee_count',
        'annual_revenue',
        'assigned_to',
        'notes',
    ];

    protected $casts = [
        'annual_revenue' => 'decimal:2',
        'employee_count' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    // Relationships

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function communications(): HasMany
    {
        return $this->hasMany(Communication::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function primaryContact(): ?Contact
    {
        return $this->contacts()->where('is_primary_contact', true)->first();
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeProspects($query)
    {
        return $query->where('status', 'prospect');
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    // Accessors

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

    public function getDisplayNameAttribute(): string
    {
        return $this->trading_name ?: $this->name;
    }
}
