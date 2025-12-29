<?php

namespace App\Modules\EmailIntegration\Services;

use App\Modules\EmailIntegration\Models\UserEmailAccount;
use App\Modules\EmailIntegration\Models\EmailMessage;
use TheNetworg\OAuth2\Client\Provider\Azure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MicrosoftGraphService
{
    protected Azure $provider;
    protected string $graphBaseUrl = 'https://graph.microsoft.com/v1.0';

    public function __construct()
    {
        $this->provider = new Azure([
            'clientId' => config('services.microsoft.client_id'),
            'clientSecret' => config('services.microsoft.client_secret'),
            'redirectUri' => config('services.microsoft.redirect_uri'),
            'tenant' => config('services.microsoft.tenant_id', 'common'),
            'defaultEndPointVersion' => '2.0',
        ]);
    }

    public function getAuthUrl(): string
    {
        return $this->provider->getAuthorizationUrl([
            'scope' => 'openid profile email offline_access https://graph.microsoft.com/User.Read https://graph.microsoft.com/Mail.Read https://graph.microsoft.com/Mail.Send',
        ]);
    }

    public function handleCallback(string $code): UserEmailAccount
    {
        $token = $this->provider->getAccessToken('authorization_code', [
            'code' => $code,
        ]);

        // Get user info via HTTP
        $response = Http::withToken($token->getToken())
            ->get("{$this->graphBaseUrl}/me");

        if (!$response->successful()) {
            Log::error('Microsoft Graph /me error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Failed to get user info from Microsoft Graph: ' . $response->body());
        }

        $user = $response->json();

        // Create or update account
        $account = UserEmailAccount::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'provider' => 'outlook',
            ],
            [
                'email_address' => $user['mail'] ?? $user['userPrincipalName'],
                'display_name' => $user['displayName'] ?? null,
                'access_token' => $token->getToken(),
                'refresh_token' => $token->getRefreshToken(),
                'token_expires_at' => $token->getExpires()
                    ? \Carbon\Carbon::createFromTimestamp($token->getExpires())
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

        $token = $this->provider->getAccessToken('refresh_token', [
            'refresh_token' => $account->refresh_token,
        ]);

        $account->update([
            'access_token' => $token->getToken(),
            'refresh_token' => $token->getRefreshToken() ?? $account->refresh_token,
            'token_expires_at' => $token->getExpires()
                ? \Carbon\Carbon::createFromTimestamp($token->getExpires())
                : null,
        ]);
    }

    public function fetchEmails(UserEmailAccount $account, array $contactEmails): Collection
    {
        if ($account->isTokenExpiringSoon()) {
            $this->refreshToken($account);
        }

        $emails = collect();

        if (empty($contactEmails)) {
            return $emails;
        }

        try {
            $response = Http::withToken($account->access_token)
                ->withHeaders(['Prefer' => 'outlook.body-content-type="html"'])
                ->get("{$this->graphBaseUrl}/me/messages", [
                    '$top' => 50,
                    '$orderby' => 'receivedDateTime desc',
                ]);

            if (!$response->successful()) {
                throw new \Exception('Failed to fetch emails: ' . $response->body());
            }

            $messages = $response->json()['value'] ?? [];

            foreach ($messages as $message) {
                // Check if sender or recipient matches contacts
                $fromEmail = $message['from']['emailAddress']['address'] ?? '';
                $toEmails = collect($message['toRecipients'] ?? [])
                    ->map(fn($r) => $r['emailAddress']['address'] ?? null)
                    ->filter()
                    ->toArray();

                $matchesContact = in_array(strtolower($fromEmail), array_map('strtolower', $contactEmails))
                    || count(array_intersect(array_map('strtolower', $toEmails), array_map('strtolower', $contactEmails))) > 0;

                if (!$matchesContact) {
                    continue;
                }

                // Check if already synced
                if (EmailMessage::where('user_email_account_id', $account->id)
                    ->where('external_id', $message['id'])
                    ->exists()) {
                    continue;
                }

                $emailData = $this->parseMessage($message, $account);

                if ($emailData) {
                    $emails->push(EmailMessage::create($emailData));
                }
            }
        } catch (\Exception $e) {
            Log::error('Microsoft Graph sync error: ' . $e->getMessage());
            $account->addSyncError($e->getMessage());
        }

        return $emails;
    }

    protected function parseMessage(array $message, UserEmailAccount $account): array
    {
        $fromEmail = $message['from']['emailAddress']['address'] ?? '';
        $fromName = $message['from']['emailAddress']['name'] ?? null;

        $toEmails = collect($message['toRecipients'] ?? [])
            ->map(fn($r) => $r['emailAddress']['address'] ?? null)
            ->filter()
            ->values()
            ->toArray();

        $ccEmails = collect($message['ccRecipients'] ?? [])
            ->map(fn($r) => $r['emailAddress']['address'] ?? null)
            ->filter()
            ->values()
            ->toArray();

        $direction = strtolower($fromEmail) === strtolower($account->email_address)
            ? 'outbound'
            : 'inbound';

        return [
            'user_email_account_id' => $account->id,
            'external_id' => $message['id'],
            'thread_id' => $message['conversationId'] ?? null,
            'from_email' => $fromEmail,
            'from_name' => $fromName,
            'to_emails' => $toEmails,
            'cc_emails' => $ccEmails ?: null,
            'subject' => $message['subject'] ?? '',
            'body_html' => $message['body']['content'] ?? null,
            'body_text' => strip_tags($message['body']['content'] ?? ''),
            'direction' => $direction,
            'is_read' => $message['isRead'] ?? false,
            'email_date' => isset($message['receivedDateTime'])
                ? \Carbon\Carbon::parse($message['receivedDateTime'])
                : now(),
        ];
    }

    public function sendEmail(UserEmailAccount $account, array $data): string
    {
        if ($account->isTokenExpiringSoon()) {
            $this->refreshToken($account);
        }

        $message = [
            'message' => [
                'subject' => $data['subject'],
                'body' => [
                    'contentType' => 'HTML',
                    'content' => nl2br(htmlspecialchars($data['body'])),
                ],
                'toRecipients' => [
                    [
                        'emailAddress' => [
                            'address' => $data['to'],
                            'name' => $data['to_name'] ?? null,
                        ],
                    ],
                ],
            ],
            'saveToSentItems' => true,
        ];

        $response = Http::withToken($account->access_token)
            ->post("{$this->graphBaseUrl}/me/sendMail", $message);

        if (!$response->successful()) {
            throw new \Exception('Failed to send email: ' . $response->body());
        }

        // Microsoft doesn't return message ID on send, generate a temp one
        return 'sent_' . time() . '_' . uniqid();
    }
}
