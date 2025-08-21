<?php

namespace App\Http\Controllers\User;

use App\Services\User\UserDashboardService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserDashboardController extends UserBaseController
{
    protected $dashboardService;

    public function __construct(UserDashboardService $dashboardService)
    {
        parent::__construct();
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        try {
            $countItems = $this->dashboardService->index();
            //return view('user.dashboard.index', $countItems);
            return view('pages.dashboard', $countItems);
        } catch(Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    public function exportOrderCSV()
    {
        try {
            return $this->dashboardService->exportOrderCsv();
            // return redirect()->route('dashboard');
        } catch(Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    public function exportOrderByCondition(Request $request)
    {
        try {
            $params = $request->all();

            return $this->dashboardService->exportOrderByCondition($params);
        } catch(Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }
}
