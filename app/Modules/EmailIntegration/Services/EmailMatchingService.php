<?php

namespace App\Modules\EmailIntegration\Services;

use App\Modules\Communications\Models\Communication;
use App\Modules\Contacts\Models\Contact;
use App\Modules\Core\Models\User;
use App\Modules\EmailIntegration\Models\EmailMessage;

class EmailMatchingService
{
    public function getMatchableEmails(User $user): array
    {
        return Contact::query()
            ->visibleTo($user)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->pluck('email')
            ->unique()
            ->values()
            ->toArray();
    }

    public function findContactByEmail(string $email, User $user): ?Contact
    {
        return Contact::query()
            ->visibleTo($user)
            ->where('email', 'like', $email)
            ->first();
    }

    public function createCommunicationFromEmail(EmailMessage $email, User $user): Communication
    {
        // Check if communication already exists for this email
        if ($email->communication_id) {
            return $email->communication;
        }

        $communication = Communication::create([
            'type' => 'email',
            'direction' => $email->direction,
            'subject' => $email->subject ?? 'No Subject',
            'content' => $email->body_text ?? strip_tags($email->body_html ?? ''),
            'client_id' => $email->client_id,
            'contact_id' => $email->contact_id,
            'created_by' => $user->id,
            'assigned_to' => $user->id,
            'status' => 'completed',
            'email_message_id' => $email->id,
            'email_from' => $email->from_email,
            'email_to' => $email->to_emails,
            'email_cc' => $email->cc_emails,
            'email_body_html' => $email->body_html,
        ]);

        // Link back to email message
        $email->update(['communication_id' => $communication->id]);

        return $communication;
    }
}
