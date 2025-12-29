<?php

namespace App\Modules\EmailIntegration\Services;

use App\Modules\EmailIntegration\Models\UserEmailAccount;
use App\Modules\EmailIntegration\Models\EmailMessage;
use Google\Client;
use Google\Service\Gmail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class GmailService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect_uri'));
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
        $this->client->addScope(Gmail::GMAIL_READONLY);
        $this->client->addScope(Gmail::GMAIL_SEND);
        $this->client->addScope(Gmail::GMAIL_MODIFY);
        $this->client->addScope('email');
        $this->client->addScope('profile');
    }

    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }

    protected function setAccountToken(UserEmailAccount $account): void
    {
        // Only set access_token - don't include refresh_token as Google tries to use
        // UserRefreshCredentials which expects a JSON key file format
        $this->client->setAccessToken([
            'access_token' => $account->access_token,
            'expires_in' => $account->token_expires_at
                ? max(0, now()->diffInSeconds($account->token_expires_at, false))
                : 3600,
            'created' => $account->updated_at?->timestamp ?? time(),
        ]);
    }

    public function handleCallback(string $code): UserEmailAccount
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            throw new \Exception('Failed to get access token: ' . $token['error_description']);
        }

        $this->client->setAccessToken($token);

        // Get user info
        $gmail = new Gmail($this->client);
        $profile = $gmail->users->getProfile('me');

        // Create or update account
        $account = UserEmailAccount::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'provider' => 'gmail',
            ],
            [
                'email_address' => $profile->getEmailAddress(),
                'access_token' => $token['access_token'],
                'refresh_token' => $token['refresh_token'] ?? null,
                'token_expires_at' => isset($token['expires_in'])
                    ? now()->addSeconds($token['expires_in'])
                    : null,
                'is_active' => true,
                'sync_errors' => null,
            ]
        );

        return $account;
    }

    public function refreshToken(UserEmailAccount $account): void
    {
        if (!$account->refresh_token) {
            throw new \Exception('No refresh token available');
        }

        // fetchAccessTokenWithRefreshToken uses the client credentials set in constructor
        $token = $this->client->fetchAccessTokenWithRefreshToken($account->refresh_token);

        if (isset($token['error'])) {
            throw new \Exception('Failed to refresh token: ' . $token['error_description']);
        }

        $account->update([
            'access_token' => $token['access_token'],
            'refresh_token' => $token['refresh_token'] ?? $account->refresh_token,
            'token_expires_at' => isset($token['expires_in'])
                ? now()->addSeconds($token['expires_in'])
                : null,
        ]);
    }

    public function fetchEmails(UserEmailAccount $account, array $contactEmails): Collection
    {
        if ($account->isTokenExpiringSoon()) {
            $this->refreshToken($account);
            $account->refresh();
        }

        $this->setAccountToken($account);

        $gmail = new Gmail($this->client);
        $emails = collect();

        if (empty($contactEmails)) {
            return $emails;
        }

        // Build query for emails from/to contacts
        $query = implode(' OR ', array_map(fn($email) => "from:{$email} OR to:{$email}", $contactEmails));

        // Only fetch emails from last 7 days for initial sync, or since last sync
        $after = $account->last_sync_at
            ? $account->last_sync_at->timestamp
            : now()->subDays(7)->timestamp;

        $query .= " after:{$after}";

        try {
            $results = $gmail->users_messages->listUsersMessages('me', [
                'q' => $query,
                'maxResults' => 50,
            ]);

            foreach ($results->getMessages() ?? [] as $message) {
                // Check if already synced
                if (EmailMessage::where('user_email_account_id', $account->id)
                    ->where('external_id', $message->getId())
                    ->exists()) {
                    continue;
                }

                $fullMessage = $gmail->users_messages->get('me', $message->getId(), ['format' => 'full']);
                $emailData = $this->parseMessage($fullMessage, $account);

                if ($emailData) {
                    $emails->push(EmailMessage::create($emailData));
                }
            }
        } catch (\Exception $e) {
            Log::error('Gmail sync error: ' . $e->getMessage());
            $account->addSyncError($e->getMessage());
        }

        return $emails;
    }

    protected function parseMessage(Gmail\Message $message, UserEmailAccount $account): ?array
    {
        $headers = collect($message->getPayload()->getHeaders());

        $from = $this->getHeader($headers, 'From');
        $to = $this->getHeader($headers, 'To');
        $subject = $this->getHeader($headers, 'Subject');
        $date = $this->getHeader($headers, 'Date');

        // Parse from email
        preg_match('/<(.+?)>/', $from, $matches);
        $fromEmail = $matches[1] ?? $from;
        $fromName = trim(str_replace("<{$fromEmail}>", '', $from));

        // Parse to emails
        $toEmails = array_map(function ($email) {
            preg_match('/<(.+?)>/', $email, $matches);
            return trim($matches[1] ?? $email);
        }, explode(',', $to));

        // Determine direction
        $direction = strtolower($fromEmail) === strtolower($account->email_address)
            ? 'outbound'
            : 'inbound';

        // Get body
        $body = $this->getBody($message->getPayload());

        return [
            'user_email_account_id' => $account->id,
            'external_id' => $message->getId(),
            'thread_id' => $message->getThreadId(),
            'from_email' => $fromEmail,
            'from_name' => $fromName ?: null,
            'to_emails' => $toEmails,
            'subject' => $subject,
            'body_html' => $body['html'] ?? null,
            'body_text' => $body['text'] ?? null,
            'direction' => $direction,
            'is_read' => !in_array('UNREAD', $message->getLabelIds() ?? []),
            'email_date' => $date ? \Carbon\Carbon::parse($date) : now(),
        ];
    }

    protected function getHeader(Collection $headers, string $name): ?string
    {
        $header = $headers->first(fn($h) => strtolower($h->getName()) === strtolower($name));
        return $header?->getValue();
    }

    protected function getBody($payload): array
    {
        $body = ['html' => null, 'text' => null];

        if ($payload->getBody()->getData()) {
            $data = base64_decode(strtr($payload->getBody()->getData(), '-_', '+/'));
            if ($payload->getMimeType() === 'text/html') {
                $body['html'] = $data;
            } else {
                $body['text'] = $data;
            }
        }

        foreach ($payload->getParts() ?? [] as $part) {
            if ($part->getMimeType() === 'text/html' && $part->getBody()->getData()) {
                $body['html'] = base64_decode(strtr($part->getBody()->getData(), '-_', '+/'));
            } elseif ($part->getMimeType() === 'text/plain' && $part->getBody()->getData()) {
                $body['text'] = base64_decode(strtr($part->getBody()->getData(), '-_', '+/'));
            }

            // Check nested parts
            if ($part->getParts()) {
                $nested = $this->getBody($part);
                $body['html'] = $body['html'] ?? $nested['html'];
                $body['text'] = $body['text'] ?? $nested['text'];
            }
        }

        return $body;
    }

    public function sendEmail(UserEmailAccount $account, array $data): string
    {
        if ($account->isTokenExpiringSoon()) {
            $this->refreshToken($account);
            $account->refresh();
        }

        $this->setAccountToken($account);

        $gmail = new Gmail($this->client);

        $rawMessage = $this->createRawMessage(
            $account->email_address,
            $account->display_name ?? '',
            $data['to'],
            $data['to_name'] ?? '',
            $data['subject'],
            $data['body']
        );

        $message = new Gmail\Message();
        $message->setRaw($rawMessage);

        $sent = $gmail->users_messages->send('me', $message);

        return $sent->getId();
    }

    protected function createRawMessage(
        string $fromEmail,
        string $fromName,
        string $toEmail,
        string $toName,
        string $subject,
        string $body
    ): string {
        $from = $fromName ? "{$fromName} <{$fromEmail}>" : $fromEmail;
        $to = $toName ? "{$toName} <{$toEmail}>" : $toEmail;

        $message = "From: {$from}\r\n";
        $message .= "To: {$to}\r\n";
        $message .= "Subject: {$subject}\r\n";
        $message .= "MIME-Version: 1.0\r\n";
        $message .= "Content-Type: text/html; charset=utf-8\r\n\r\n";
        $message .= nl2br(htmlspecialchars($body));

        return rtrim(strtr(base64_encode($message), '+/', '-_'), '=');
    }
}
