<?php

namespace Modules\QueryMonitor\Console;

use Illuminate\Console\Command;
use Modules\QueryMonitor\Storage\MonitorStore;

class CreateUserCommand extends Command
{
    protected $signature = 'query-monitor:user
        {--email= : Email đăng nhập của tài khoản Query Monitor}
        {--name= : Tên hiển thị}
        {--password= : Mật khẩu (tối thiểu 6 ký tự)}
        {--role=admin : Quyền (admin hoặc viewer)}';

    protected $description = 'Tạo mới hoặc đặt lại mật khẩu tài khoản cho module Query Monitor';

    public function handle(MonitorStore $store): int
    {
        $email = $this->option('email') ?: $this->ask('Email');
        $password = $this->option('password') ?: $this->secret('Mật khẩu (tối thiểu 6 ký tự)');

        if (strlen((string) $password) < 6) {
            $this->error('Mật khẩu phải có ít nhất 6 ký tự.');
            return 1;
        }

        $existing = $store->findUserByEmail($email);
        if ($existing) {
            $store->updateUserPassword((int) $existing['id'], $password);
            $store->setUserActive((int) $existing['id'], true);
            $this->info("Đã đặt lại mật khẩu và kích hoạt tài khoản: {$email}");
            return 0;
        }

        $name = $this->option('name') ?: $this->ask('Tên hiển thị', 'Monitor Admin');
        $role = $this->option('role') ?: 'admin';

        $store->createUser($name, $email, $password, $role);
        $this->info("Đã tạo tài khoản Query Monitor [{$role}]: {$email}");
        return 0;
    }
}
