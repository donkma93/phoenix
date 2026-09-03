<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
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
            $user = JWTAuth::parseToken()->authenticate();
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