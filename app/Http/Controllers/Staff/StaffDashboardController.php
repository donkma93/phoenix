<?php

namespace App\Http\Controllers\Staff;

use App\Services\Staff\StaffDashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class StaffDashboardController extends StaffBaseController
{
    protected $dashboardService;

    public function __construct(StaffDashboardService $dashboardService)
    {
        parent::__construct();

        $this->dashboardService = $dashboardService;
    }

    /**
     * Display staff dashboard
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $result = $this->dashboardService->getAllUserRequest();
        //dd($result);

        //return view('staff.dashboard.index', $result);
        return view('pages.dashboard', $result);
    }
}
