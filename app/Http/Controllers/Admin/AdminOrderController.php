<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\AdminOrderService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminOrderController extends AdminBaseController
{
    protected $orderService;

    public function __construct(AdminOrderService $orderService)
    {
        parent::__construct();
        $this->orderService = $orderService;
    }

    public function list(Request $request)
    {
        try {
            $data = $this->orderService->list($request->all());

            return view('admin.order.list', $data);
        } catch(Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    public function detail($id)
    {
        try {
            $item = $this->orderService->detail($id);

            return view('admin.order.detail', $item);
        } catch(Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }
}
