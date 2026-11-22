<?php

namespace Modules\QueryMonitor\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\QueryMonitor\Storage\MonitorStore;

class MonitorAuthenticate
{
    public const SESSION_KEY = 'query_monitor_auth_user_id';

    /** @var MonitorStore */
    protected $store;

    public function __construct(MonitorStore $store)
    {
        $this->store = $store;
    }

    public function handle(Request $request, Closure $next, ?string $requiredRole = null)
    {
        $userId = $request->session()->get(self::SESSION_KEY);
        if (!$userId) {
            return redirect()->route('query-monitor.login');
        }

        $user = $this->store->findUserById((int) $userId);
        if (!$user || (int) $user['is_active'] !== 1) {
            $request->session()->forget(self::SESSION_KEY);
            return redirect()->route('query-monitor.login')
                ->withErrors(['email' => 'Phiên đăng nhập không hợp lệ hoặc tài khoản đã bị khóa.']);
        }

        if ($requiredRole !== null && $user['role'] !== $requiredRole) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này (yêu cầu quyền Admin của Query Monitor).');
        }

        $request->attributes->set('monitor_user', $user);
        view()->share('monitorUser', $user);

        return $next($request);
    }
}
