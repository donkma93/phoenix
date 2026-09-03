<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * JSON-friendly role gate for JWT API routes (does not redirect like web Role middleware).
 */
class JwtRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::guard('api')->user() ?? Auth::user();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated',
            ], 401);
        }

        $roleName = User::$roleName[$user->role] ?? null;
        if ($roleName && in_array($roleName, $roles, true)) {
            return $next($request);
        }

        // Also allow numeric role constants passed as "0","1","2"
        if (in_array((string) $user->role, $roles, true)) {
            return $next($request);
        }

        return response()->json([
            'status' => 'error',
            'message_code' => 'FORBIDDEN',
            'message' => 'You do not have permission to access this resource.',
        ], 403);
    }
}
