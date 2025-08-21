<?php

namespace App\Http\Controllers\Staff;

use App\Services\Staff\StaffStoreFulfillService;
use App\Http\Requests\Staff\AddUnitPriceRequest;
use App\Http\Requests\Staff\CreateStoreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class StaffStoreFulfillController extends StaffBaseController
{
    protected $storeService;

    public function __construct(StaffStoreFulfillService $storeService)
    {
        parent::__construct();
        $this->storeService = $storeService;
    }

    /**
     * Display list store
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function list(Request $request)
    {
        try {
            $input = $request->only('name', 'code', 'onlyDeleted');
            $stores = $this->storeService->list($input);

            return view('staff.store-fulfill.list', $stores);
        } catch(Exception $e) {
            Log::error($e);

            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * Display store detail
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function detail(Request $request, $id)
    {
        try {
            $store = $this->storeService->detail($id);

            return view('staff.store-fulfill.detail', $store);
        } catch(Exception $e) {
            Log::error($e);

            //TODO redirect to error page
            abort(500);
        }
    }
}
