<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\AdminDashboardService;
use App\Exports\User\OrderStaffExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;
use Excel;

class AdminDashboardController extends AdminBaseController
{
    protected $dashboardService;

    public function __construct(AdminDashboardService $dashboardService)
    {
        parent::__construct();
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display admin dashboard
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        try {
            $countItems = $this->dashboardService->index();
            return view('admin.dashboard.index', $countItems);
        } catch(Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * Display order overview
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function board(Request $request)
    {
        try {
            $info = $this->dashboardService->board();

            return view('admin.dashboard.board', $info);
        } catch(Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * Display order overview
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function liveShipping(Request $request)
    {
        try {
            $info = $this->dashboardService->liveShipping($request);

            return view('admin.dashboard.liveShipping', $info);
        } catch(Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    public function exportExcel($type) {
        $data = $this->dashboardService->export($type);

        $export = new OrderStaffExport($data);
        $typeName = $type == 0 ? 'Picker' : 'Packer';
        $fileName = date('YmdHis', strtotime(\Carbon\Carbon::now())) .'-' . $typeName . '.csv';

        return Excel::download($export, $fileName);
    }
}
