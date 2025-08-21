<?php

namespace App\Http\Controllers\Staff;

use App\Services\Staff\StaffWarehouseAreaService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Exception;

class StaffWarehouseAreaController extends StaffBaseController
{
    protected $warehouseAreaService;

    public function __construct(StaffWarehouseAreaService $warehouseAreaService)
    {
        parent::__construct();
        $this->warehouseAreaService = $warehouseAreaService;
    }
    /**
     * Display list warehouse area
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function list(Request $request)
    {
        try {
            $input = $request->only('name', 'warehouse');
            $warehouseAreas = $this->warehouseAreaService->list($input);

            return view('staff.warehouseArea.list', $warehouseAreas);
        } catch(Exception $e) {
            Log::error($e);
            
            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * Display warehouse area detail
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function detail(Request $request, $id)
    {
        try {
            $input = $request->only('id');
            $warehouseArea = $this->warehouseAreaService->detail($id);
            
            return view('staff.warehouseArea.detail', $warehouseArea);
        } catch(Exception $e) {
            Log::error($e);
            
            //TODO redirect to error page
            abort(500);
        }
    }

        /**
     *  Update warehouse area
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function setNull(Request $request)
    {
        try {
            $input = $request->only('id');
            $info = $this->warehouseAreaService->updateArea($input);
        } catch(Exception $e) {
            Log::error($e);
        }
    }

    /**
     *  Update warehouse area
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function updateArea(Request $request)
    {
        try {
            $input = $request->only('id', 'fromList');
            $info = $this->warehouseAreaService->updateArea($input);

            if(!$input['fromList']) {
                return redirect()->back()->with('success', "Update area successfully!");
            }
        } catch(Exception $e) {
            Log::error($e);

            if(!$input['fromList']) {
                return redirect()->back()->with('error', "Update area successfully!");
            }
        }
    }
}
