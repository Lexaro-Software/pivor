<?php

namespace App\Modules\EmailIntegration\Jobs;

use App\Modules\EmailIntegration\Models\UserEmailAccount;
use App\Modules\EmailIntegration\Services\EmailSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncInboundEmailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public ?UserEmailAccount $account = null
    ) {}

    public function handle(EmailSyncService $service): void
    {
        // If specific account provided, sync only that one
        $accounts = $this->account
            ? collect([$this->account])
            : UserEmailAccount::where('is_active', true)->get();

        foreach ($accounts as $account) {
            try {
                $service->syncAccount($account);
            } catch (\Exception $e) {
                Log::error("Email sync failed for account {$account->id}: " . $e->getMessage());
                $account->addSyncError($e->getMessage());
            }
        }
    }
}
