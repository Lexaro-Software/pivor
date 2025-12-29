<?php

namespace App\Modules\EmailIntegration\Models;

use App\Modules\Clients\Models\Client;
use App\Modules\Communications\Models\Communication;
use App\Modules\Contacts\Models\Contact;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EmailMessage extends Model
{
    protected $fillable = [
        'user_email_account_id',
        'external_id',
        'thread_id',
        'communication_id',
        'from_email',
        'from_name',
        'to_emails',
        'cc_emails',
        'subject',
        'body_html',
        'body_text',
        'attachments',
        'direction',
        'is_read',
        'email_date',
        'contact_id',
        'client_id',
    ];

    protected $casts = [
        'to_emails' => 'array',
        'cc_emails' => 'array',
        'attachments' => 'array',
        'is_read' => 'boolean',
        'email_date' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    // Relationships

    public function userEmailAccount(): BelongsTo
    {
        return $this->belongsTo(UserEmailAccount::class);
    }

    public function communication(): BelongsTo
    {
        return $this->belongsTo(Communication::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // Accessors

    public function getFromDisplayAttribute(): string
    {
        if ($this->from_name) {
            return "{$this->from_name} <{$this->from_email}>";
        }

        return $this->from_email;
    }

    public function getToDisplayAttribute(): string
    {
        return implode(', ', $this->to_emails ?? []);
    }

    public function getBodyPreviewAttribute(): string
    {
        $text = $this->body_text ?: strip_tags($this->body_html);
        return Str::limit($text, 150);
    }
}
