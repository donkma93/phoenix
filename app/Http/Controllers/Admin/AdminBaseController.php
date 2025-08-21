<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminBaseService;

class AdminBaseController extends Controller
{
    public function __construct()
    {
        $adminService = new AdminBaseService();

        $this->adminService = $adminService;
    }

    /**
     * Get number of new user request
     *
     * @return int $total
     */
    public function notification()
    {
        $total = $this->adminService->notification();

        return $total;
    }
}
