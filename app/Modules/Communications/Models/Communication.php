<?php

namespace App\Modules\Communications\Models;

use App\Models\User;
use App\Modules\Clients\Models\Client;
use App\Modules\Contacts\Models\Contact;
use App\Traits\ScopedByUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Communication extends Model
{
    use HasFactory, SoftDeletes, ScopedByUser;

    protected static function newFactory()
    {
        return \Database\Factories\CommunicationFactory::new();
    }

    protected $fillable = [
        'type',
        'direction',
        'subject',
        'content',
        'client_id',
        'contact_id',
        'due_at',
        'completed_at',
        'priority',
        'created_by',
        'assigned_to',
        'status',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();

            if (auth()->check() && !$model->created_by) {
                $model->created_by = auth()->id();
            }
        });
    }

    // Relationships

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Scopes

    public function scopeTasks($query)
    {
        return $query->where('type', 'task');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_at', '<', now())
            ->whereNull('completed_at');
    }

    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeForContact($query, $contactId)
    {
        return $query->where('contact_id', $contactId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Methods

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    // Accessors

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_at
            && $this->due_at->isPast()
            && !$this->completed_at;
    }

    public function getIsTaskAttribute(): bool
    {
        return $this->type === 'task';
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'email' => 'envelope',
            'phone' => 'phone',
            'meeting' => 'calendar',
            'task' => 'check-circle',
            default => 'chat-bubble-left',
        };
    }
}
