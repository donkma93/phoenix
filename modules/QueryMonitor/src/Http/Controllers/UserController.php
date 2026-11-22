<?php

namespace Modules\QueryMonitor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\QueryMonitor\Storage\MonitorStore;

class UserController extends Controller
{
    /** @var MonitorStore */
    protected $store;

    public function __construct(MonitorStore $store)
    {
        $this->store = $store;
    }

    public function index()
    {
        $users = $this->store->listUsers();
        foreach ($users as &$u) {
            $u['last_login_at'] = MonitorStore::toDisplayTime($u['last_login_at'] ?? null);
            $u['created_at'] = MonitorStore::toDisplayTime($u['created_at'] ?? null);
        }
        unset($u);

        return view('query-monitor::users.index', [
            'users' => $users,
            'recordingEnabled' => $this->store->isRecordingEnabled(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,viewer',
        ]);

        if ($this->store->findUserByEmail($data['email'])) {
            return back()->withErrors(['email' => 'Email này đã tồn tại trong Query Monitor.']);
        }

        $this->store->createUser($data['name'], $data['email'], $data['password'], $data['role']);

        return back()->with('status', 'Đã tạo tài khoản Query Monitor mới.');
    }

    public function toggleActive(Request $request, int $id)
    {
        $currentUser = $request->attributes->get('monitor_user');
        if ((int) $currentUser['id'] === $id) {
            return back()->withErrors(['user' => 'Không thể tự vô hiệu hóa tài khoản đang đăng nhập.']);
        }

        $target = $this->store->findUserById($id);
        if (!$target) {
            return back()->withErrors(['user' => 'Không tìm thấy tài khoản.']);
        }

        $this->store->setUserActive($id, (int) $target['is_active'] !== 1);
        return back()->with('status', 'Đã cập nhật trạng thái tài khoản.');
    }

    public function resetPassword(Request $request, int $id)
    {
        $data = $request->validate([
            'password' => 'required|string|min:6',
        ]);

        $this->store->updateUserPassword($id, $data['password']);
        return back()->with('status', 'Đã đổi mật khẩu thành công.');
    }

    public function destroy(Request $request, int $id)
    {
        $currentUser = $request->attributes->get('monitor_user');
        if ((int) $currentUser['id'] === $id) {
            return back()->withErrors(['user' => 'Không thể tự xóa tài khoản đang đăng nhập.']);
        }

        $this->store->deleteUser($id);
        return back()->with('status', 'Đã xóa tài khoản khỏi Query Monitor.');
    }
}
