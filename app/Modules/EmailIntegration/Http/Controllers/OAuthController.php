<?php

namespace App\Modules\EmailIntegration\Http\Controllers;

use App\Modules\EmailIntegration\Services\GmailService;
use App\Modules\EmailIntegration\Services\MicrosoftGraphService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class OAuthController extends Controller
{
    public function googleCallback(Request $request, GmailService $service)
    {
        try {
            if ($request->has('error')) {
                throw new \Exception($request->get('error_description', 'Authorization denied'));
            }

            $code = $request->get('code');
            if (!$code) {
                throw new \Exception('No authorization code received');
            }

            $account = $service->handleCallback($code);

            return redirect()->route('settings.email')
                ->with('message', 'Gmail connected successfully! Your emails will sync shortly.');

        } catch (\Exception $e) {
            Log::error('Gmail OAuth error: ' . $e->getMessage());

            return redirect()->route('settings.email')
                ->with('error', 'Failed to connect Gmail: ' . $e->getMessage());
        }
    }

    public function microsoftCallback(Request $request, MicrosoftGraphService $service)
    {
        try {
            if ($request->has('error')) {
                throw new \Exception($request->get('error_description', 'Authorization denied'));
            }

            $code = $request->get('code');
            if (!$code) {
                throw new \Exception('No authorization code received');
            }

            $account = $service->handleCallback($code);

            return redirect()->route('settings.email')
                ->with('message', 'Outlook connected successfully! Your emails will sync shortly.');

        } catch (\Exception $e) {
            Log::error('Microsoft OAuth error: ' . $e->getMessage());

            return redirect()->route('settings.email')
                ->with('error', 'Failed to connect Outlook: ' . $e->getMessage());
        }
    }
}
