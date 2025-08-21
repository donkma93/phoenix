<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login', ['locale' => app()->getLocale()]);
        }

        if (!Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice', ['locale' => app()->getLocale()]);
        }

        $role = Auth::user()->role;
        if (in_array(User::$roleName[$role], $roles)) {
            return $next($request);
        }
        
        return redirect()->route(User::$home[$role]);
    }
}
