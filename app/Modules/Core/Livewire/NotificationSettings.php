<?php

namespace App\Modules\Core\Livewire;

use App\Modules\Core\Models\UserNotificationSetting;
use Livewire\Component;

class NotificationSettings extends Component
{
    public bool $taskRemindersEnabled = true;
    public bool $remindDayBefore = true;
    public bool $remindSameDay = false;
    public string $reminderTime = '09:00';

    public function mount(): void
    {
        $settings = auth()->user()->getNotificationSettings();

        $this->taskRemindersEnabled = $settings->task_reminders_enabled;
        $this->remindDayBefore = $settings->remind_day_before;
        $this->remindSameDay = $settings->remind_same_day;
        $this->reminderTime = $settings->reminder_time instanceof \DateTime
            ? $settings->reminder_time->format('H:i')
            : $settings->reminder_time;
    }

    public function save(): void
    {
        $this->validate([
            'reminderTime' => 'required|date_format:H:i',
        ]);

        $settings = auth()->user()->getNotificationSettings();

        $settings->update([
            'task_reminders_enabled' => $this->taskRemindersEnabled,
            'remind_day_before' => $this->remindDayBefore,
            'remind_same_day' => $this->remindSameDay,
            'reminder_time' => $this->reminderTime,
        ]);

        session()->flash('message', 'Notification settings saved successfully.');
    }

    public function render()
    {
        return view('core::livewire.notification-settings')
            ->layout('components.layouts.app', ['title' => 'Notification Settings']);
    }
}
