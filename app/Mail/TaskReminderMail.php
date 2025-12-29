<?php

namespace App\Mail;

use App\Modules\Communications\Models\Communication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Communication $task,
        public string $timing // 'tomorrow' or 'today'
    ) {}

    public function envelope(): Envelope
    {
        $when = $this->timing === 'tomorrow' ? 'due tomorrow' : 'due today';

        return new Envelope(
            subject: "Reminder: {$this->task->subject} is {$when}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.task-reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
