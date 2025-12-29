<?php

namespace App\Modules\EmailIntegration\Jobs;

use App\Modules\Communications\Models\Communication;
use App\Modules\EmailIntegration\Models\UserEmailAccount;
use App\Modules\EmailIntegration\Models\EmailMessage;
use App\Modules\EmailIntegration\Services\GmailService;
use App\Modules\EmailIntegration\Services\MicrosoftGraphService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public UserEmailAccount $account,
        public array $emailData
    ) {}

    public function handle(GmailService $gmail, MicrosoftGraphService $microsoft): void
    {
        try {
            $service = match ($this->account->provider) {
                'gmail' => $gmail,
                'outlook' => $microsoft,
                default => throw new \Exception("Unknown provider: {$this->account->provider}"),
            };

            $externalId = $service->sendEmail($this->account, $this->emailData);

            // Create email message record
            $emailMessage = EmailMessage::create([
                'user_email_account_id' => $this->account->id,
                'external_id' => $externalId,
                'from_email' => $this->account->email_address,
                'from_name' => $this->account->display_name,
                'to_emails' => [$this->emailData['to']],
                'subject' => $this->emailData['subject'],
                'body_html' => nl2br(htmlspecialchars($this->emailData['body'])),
                'body_text' => $this->emailData['body'],
                'direction' => 'outbound',
                'is_read' => true,
                'email_date' => now(),
                'contact_id' => $this->emailData['contact_id'] ?? null,
                'client_id' => $this->emailData['client_id'] ?? null,
            ]);

            // Create communication record
            $communication = Communication::create([
                'type' => 'email',
                'direction' => 'outbound',
                'subject' => $this->emailData['subject'],
                'content' => $this->emailData['body'],
                'client_id' => $this->emailData['client_id'] ?? null,
                'contact_id' => $this->emailData['contact_id'] ?? null,
                'created_by' => $this->account->user_id,
                'assigned_to' => $this->account->user_id,
                'status' => 'completed',
                'email_message_id' => $emailMessage->id,
                'email_from' => $this->account->email_address,
                'email_to' => [$this->emailData['to']],
                'email_body_html' => nl2br(htmlspecialchars($this->emailData['body'])),
            ]);

            $emailMessage->update(['communication_id' => $communication->id]);

            Log::info("Email sent successfully via {$this->account->provider} to {$this->emailData['to']}");

        } catch (\Exception $e) {
            Log::error("Failed to send email: " . $e->getMessage());
            $this->account->addSyncError("Send failed: " . $e->getMessage());
            throw $e;
        }
    }
}
