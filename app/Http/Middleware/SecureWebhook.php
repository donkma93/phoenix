<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecureWebhook
{
    /**
     * Require a shared secret for inbound webhooks.
     * Accepts (in order): header Secure-Token, header X-Webhook-Token, query ?token=
     * Secret from config('jwt.secure_token') or env WEBHOOK_SECURE_TOKEN.
     */
    public function handle(Request $request, Closure $next)
    {
        $expected = config('jwt.secure_token') ?: env('WEBHOOK_SECURE_TOKEN');
        if (!$expected) {
            // Misconfigured server — fail closed
            return response()->json(['status' => 'error', 'message' => 'Webhook secret not configured'], 503);
        }

        $provided = $request->header('Secure-Token')
            ?? $request->header('X-Webhook-Token')
            ?? $request->query('token');

        if (!$provided || !hash_equals((string) $expected, (string) $provided)) {
            return response()->json(['status' => 'error', 'message' => 'Webhook token is invalid'], 403);
        }

        return $next($request);
    }
}
