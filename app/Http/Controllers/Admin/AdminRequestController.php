<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\AdminRequestService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminRequestController extends AdminBaseController
{
    protected $requestService;

    public function __construct(AdminRequestService $requestService)
    {
        parent::__construct();

        $this->requestService = $requestService;
    }

    /**
     * Display a listing of current user request.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function list(Request $request)
    {
        try {
            $input = $request->only('type', 'status', 'email');
            $requests = $this->requestService->list($input);

            return view('admin.request.list', $requests);
        } catch(Exception $e) {
            Log::error($e);
            
            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * Display a listing of current user request.
     *
     * @param  int  $userRequestId
     * @return \Illuminate\Contracts\View\View
     */
    public function detail(Request $request, $userRequestId)
    {
        try {
            $input = $request->only('warehouse', 'status', 'barcode');
            $info = $this->requestService->detail($userRequestId, $input);

            return view('admin.request.detail', $info);
        } catch(Exception $e) {
            Log::error($e);

            //TODO redirect to error page
            abort(500);
        }
    }
}
