<?php

namespace App\Modules\EmailIntegration\Models;

use App\Modules\Core\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class UserEmailAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'provider',
        'email_address',
        'display_name',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'is_active',
        'last_sync_at',
        'sync_token',
        'sync_errors',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'last_sync_at' => 'datetime',
        'is_active' => 'boolean',
        'sync_errors' => 'array',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    // Encrypt/Decrypt tokens

    public function setAccessTokenAttribute($value): void
    {
        $this->attributes['access_token'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getAccessTokenAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function setRefreshTokenAttribute($value): void
    {
        $this->attributes['refresh_token'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getRefreshTokenAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function emailMessages(): HasMany
    {
        return $this->hasMany(EmailMessage::class);
    }

    // Helpers

    public function isTokenExpired(): bool
    {
        if (!$this->token_expires_at) {
            return false;
        }

        return $this->token_expires_at->isPast();
    }

    public function isTokenExpiringSoon(int $minutes = 5): bool
    {
        if (!$this->token_expires_at) {
            return false;
        }

        return $this->token_expires_at->subMinutes($minutes)->isPast();
    }

    public function addSyncError(string $error): void
    {
        $errors = $this->sync_errors ?? [];
        $errors[] = [
            'error' => $error,
            'at' => now()->toIso8601String(),
        ];

        // Keep only last 10 errors
        $this->sync_errors = array_slice($errors, -10);
        $this->save();
    }
}
