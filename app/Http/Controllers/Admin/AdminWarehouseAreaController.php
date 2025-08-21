<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\AdminWarehouseAreaService;
use App\Http\Requests\Admin\CheckWarehouseIdRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Exception;

class AdminWarehouseAreaController extends AdminBaseController
{
    protected $warehouseAreaService;

    public function __construct(AdminWarehouseAreaService $warehouseAreaService)
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
            $input = $request->only('name', 'warehouse', 'onlyDeleted');
            $warehouseAreas = $this->warehouseAreaService->list($input);

            return view('admin.warehouseArea.list', $warehouseAreas);
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
            
            return view('admin.warehouseArea.detail', $warehouseArea);
        } catch(Exception $e) {
            Log::error($e);
            
            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     *  New warehouse area
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function new(Request $request)
    {
        try {
            $warehouses = $this->warehouseAreaService->getWarehouses();
            $barcode = $this->warehouseAreaService->generateBarcodeNumber();
            
            return view('admin.warehouseArea.new', [ 'warehouses' => $warehouses, 'barcode' => $barcode]);
        } catch(Exception $e) {
            Log::error($e);
        
            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * Create new warehouse area
     * @param App\Http\Requests\Admin\CheckWarehouseIdRequest $request
     * @return \Illuminate\Contracts\View\View
     */
    public function create(CheckWarehouseIdRequest $request)
    {
        try {
            $input = $request->only('name', 'warehouse', 'barcode');
            $this->warehouseAreaService->create($input);
            
            return redirect()->back()->with('success', "Create warehouse area successfully!");
        } catch(Exception $e) {
            Log::error($e);
            
            return redirect()->back()->with('fail', "Create warehouse area fail!");
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
            $input = $request->only('id', 'delete', 'isFull');
            if(isset($input['delete'])) {
                $info = $this->warehouseAreaService->delete($input);
            } else {
                $info = $this->warehouseAreaService->updateArea($input);
            }
            
            return redirect()->back()->with('success', "Update warehouse area successfully!");
        } catch(Exception $e) {
            Log::error($e);
           
            return redirect()->back()->with('fail', "Update warehouse area fail!");
        }
    }
}
