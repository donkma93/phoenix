<?php

namespace Modules\QueryMonitor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\QueryMonitor\Http\Middleware\MonitorAuthenticate;
use Modules\QueryMonitor\Storage\MonitorStore;

class AuthController extends Controller
{
    /** @var MonitorStore */
    protected $store;

    public function __construct(MonitorStore $store)
    {
        $this->store = $store;
    }

    public function showLogin(Request $request)
    {
        if ($request->session()->has(MonitorAuthenticate::SESSION_KEY)) {
            $u = $this->store->findUserById((int) $request->session()->get(MonitorAuthenticate::SESSION_KEY));
            if ($u && (int) $u['is_active'] === 1) {
                return redirect()->route('query-monitor.dashboard');
            }
        }

        $needsSetup = $this->store->countUsers() === 0;

        return view('query-monitor::auth.login', [
            'needsSetup' => $needsSetup,
        ]);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = $this->store->findUserByEmail($data['email']);
        if (!$user || !password_verify($data['password'], $user['password_hash'])) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email hoặc mật khẩu không đúng.']);
        }

        if ((int) $user['is_active'] !== 1) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Tài khoản này đang bị vô hiệu hóa.']);
        }

        $request->session()->put(MonitorAuthenticate::SESSION_KEY, (int) $user['id']);
        $this->store->touchUserLogin((int) $user['id']);

        return redirect()->route('query-monitor.dashboard');
    }

    public function setup(Request $request)
    {
        if ($this->store->countUsers() > 0) {
            return redirect()->route('query-monitor.login');
        }

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $id = $this->store->createUser($data['name'], $data['email'], $data['password'], 'admin');
        $request->session()->put(MonitorAuthenticate::SESSION_KEY, $id);
        $this->store->touchUserLogin($id);

        return redirect()->route('query-monitor.dashboard')
            ->with('status', 'Đã tạo tài khoản Admin đầu tiên cho Query Monitor.');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(MonitorAuthenticate::SESSION_KEY);
        return redirect()->route('query-monitor.login')->with('status', 'Đã đăng xuất khỏi Query Monitor.');
    }
}
