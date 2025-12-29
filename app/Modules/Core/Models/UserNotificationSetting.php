<?php

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationSetting extends Model
{
    protected $fillable = [
        'user_id',
        'task_reminders_enabled',
        'remind_day_before',
        'remind_same_day',
        'reminder_time',
    ];

    protected $casts = [
        'task_reminders_enabled' => 'boolean',
        'remind_day_before' => 'boolean',
        'remind_same_day' => 'boolean',
        'reminder_time' => 'datetime:H:i',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getOrCreateForUser(User $user): self
    {
        return self::firstOrCreate(
            ['user_id' => $user->id],
            [
                'task_reminders_enabled' => true,
                'remind_day_before' => true,
                'remind_same_day' => false,
                'reminder_time' => '09:00',
            ]
        );
    }
}
