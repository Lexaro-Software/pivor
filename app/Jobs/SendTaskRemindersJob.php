<?php

namespace App\Jobs;

use App\Mail\TaskReminderMail;
use App\Modules\Communications\Models\Communication;
use App\Modules\Core\Models\User;
use App\Modules\Core\Models\UserNotificationSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTaskRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $currentHour = now()->format('H');

        // Get all users with notification settings matching the current hour
        $settings = UserNotificationSetting::where('task_reminders_enabled', true)
            ->whereRaw("strftime('%H', reminder_time) = ?", [$currentHour])
            ->with('user')
            ->get();

        foreach ($settings as $setting) {
            $user = $setting->user;

            if (!$user || !$user->email) {
                continue;
            }

            $this->sendRemindersForUser($user, $setting);
        }
    }

    protected function sendRemindersForUser(User $user, UserNotificationSetting $setting): void
    {
        // Day before reminders
        if ($setting->remind_day_before) {
            $tomorrow = now()->addDay()->startOfDay();
            $tomorrowEnd = now()->addDay()->endOfDay();

            $tasksDueTomorrow = Communication::where('assigned_to', $user->id)
                ->where('type', 'task')
                ->whereIn('status', ['pending', 'in_progress'])
                ->whereBetween('due_at', [$tomorrow, $tomorrowEnd])
                ->whereNull('reminder_sent_at')
                ->with(['client', 'contact'])
                ->get();

            foreach ($tasksDueTomorrow as $task) {
                $this->sendReminder($user, $task, 'tomorrow');
            }
        }

        // Same day reminders
        if ($setting->remind_same_day) {
            $today = now()->startOfDay();
            $todayEnd = now()->endOfDay();

            $tasksDueToday = Communication::where('assigned_to', $user->id)
                ->where('type', 'task')
                ->whereIn('status', ['pending', 'in_progress'])
                ->whereBetween('due_at', [$today, $todayEnd])
                ->whereNull('reminder_sent_at')
                ->with(['client', 'contact'])
                ->get();

            foreach ($tasksDueToday as $task) {
                $this->sendReminder($user, $task, 'today');
            }
        }
    }

    protected function sendReminder(User $user, Communication $task, string $timing): void
    {
        try {
            Mail::to($user->email)->send(new TaskReminderMail($task, $timing));

            $task->update(['reminder_sent_at' => now()]);

            Log::info("Task reminder sent", [
                'user_id' => $user->id,
                'task_id' => $task->id,
                'timing' => $timing,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send task reminder", [
                'user_id' => $user->id,
                'task_id' => $task->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
