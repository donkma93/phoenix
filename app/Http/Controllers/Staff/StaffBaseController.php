<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\Staff\StaffBaseService;
use Illuminate\Http\Request;

class StaffBaseController extends Controller
{
    protected $staffService;

    protected $warehouseAreas;

    protected $users;

    public function __construct()
    {
        $staffService = new StaffBaseService();

        $this->staffService = $staffService;

        $this->warehouseAreas = $this->staffService->getAllWarehouseArea();

        $this->users = $this->staffService->getAllUser();
    }

    /**
     * Get number of new user request
     *
     * @return int $totalPackage
     */
    public function notification()
    {
        $totalPackage = $this->staffService->notification();

        return $totalPackage;
    }
}
