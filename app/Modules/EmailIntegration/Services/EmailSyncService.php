<?php

namespace App\Modules\EmailIntegration\Services;

use App\Modules\EmailIntegration\Models\UserEmailAccount;
use Illuminate\Support\Facades\Log;

class EmailSyncService
{
    public function __construct(
        protected GmailService $gmail,
        protected MicrosoftGraphService $microsoft,
        protected EmailMatchingService $matcher
    ) {}

    public function syncAccount(UserEmailAccount $account): int
    {
        $user = $account->user;
        $contactEmails = $this->matcher->getMatchableEmails($user);

        if (empty($contactEmails)) {
            return 0;
        }

        $service = match ($account->provider) {
            'gmail' => $this->gmail,
            'outlook' => $this->microsoft,
            default => throw new \Exception("Unknown provider: {$account->provider}"),
        };

        $emails = $service->fetchEmails($account, $contactEmails);
        $matched = 0;

        foreach ($emails as $email) {
            // Try to match by from_email first (for inbound), then to_emails (for outbound)
            $matchEmail = $email->direction === 'inbound'
                ? $email->from_email
                : collect($email->to_emails)->first();

            $contact = $this->matcher->findContactByEmail($matchEmail, $user);

            if ($contact) {
                $email->update([
                    'contact_id' => $contact->id,
                    'client_id' => $contact->client_id,
                ]);

                $this->matcher->createCommunicationFromEmail($email, $user);
                $matched++;
            }
        }

        $account->update(['last_sync_at' => now()]);

        Log::info("Synced {$matched} emails for account {$account->email_address}");

        return $matched;
    }
}
