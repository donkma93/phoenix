<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\Staff\StaffBaseService;
use Illuminate\Http\Request;

class StaffBaseController extends Controller
{
    protected $staffService;

    /** @var \Illuminate\Support\Collection|null Lazy-loaded, not on every request */
    protected $warehouseAreas;

    /** @var \Illuminate\Support\Collection|null Lazy-loaded, not on every request */
    protected $users;

    public function __construct()
    {
        $this->staffService = new StaffBaseService();
        // Intentionally do NOT load all users / warehouse areas here.
        // That previously ran on every staff page and ballooned memory.
    }

    /**
     * Lazy-load warehouse areas only when a controller needs them.
     */
    protected function getWarehouseAreas()
    {
        if ($this->warehouseAreas === null) {
            $this->warehouseAreas = $this->staffService->getAllWarehouseArea();
        }

        return $this->warehouseAreas;
    }

    /**
     * Lazy-load users only when a controller needs them.
     */
    protected function getUsers()
    {
        if ($this->users === null) {
            $this->users = $this->staffService->getAllUser();
        }

        return $this->users;
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
