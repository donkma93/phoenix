<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Exception;
use Illuminate\Support\Facades\Cache;
use JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    /**
     * Authenticate JWT for API routes. Always returns JSON with proper HTTP status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();
            $userId = $payload->get('sub');
            // Short 60s cache + explicit columns avoids hitting `users` table on every API request
            $user = $userId
                ? Cache::remember("jwt_user:{$userId}", 60, function () use ($userId) {
                    return User::select(
                        'id',
                        'email',
                        'email_verified_at',
                        'role',
                        'partner_id',
                        'partner_code',
                        'created_at',
                        'updated_at'
                    )->find($userId);
                })
                : null;

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated',
                ], 401);
            }
            // Ensure Auth::user() / Auth::check() work in controllers & role middleware
            auth('api')->setUser($user);
            auth()->shouldUse('api');
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['status' => 'error', 'message' => 'Token is Invalid'], 401);
            }
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['status' => 'error', 'message' => 'Token is Expired'], 401);
            }
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenBlacklistedException) {
                return response()->json(['status' => 'error', 'message' => 'Token is Blacklisted'], 401);
            }
            return response()->json(['status' => 'error', 'message' => 'Authorization Token not found'], 401);
        }

        return $next($request);
    }
}