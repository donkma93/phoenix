<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\AdminWarehouseService;
use App\Http\Requests\Admin\AddUnitPriceRequest;
use App\Http\Requests\Admin\CreateWarehouseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class AdminWarehouseController extends AdminBaseController
{
    protected $warehouseService;

    public function __construct(AdminWarehouseService $warehouseService)
    {
        parent::__construct();
        $this->warehouseService = $warehouseService;
    }

    /**
     * Display list warehouse
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function list(Request $request)
    {
        try {
            $input = $request->only('name', 'onlyDeleted');
            $warehouses = $this->warehouseService->list($input);

            return view('admin.warehouse.list', $warehouses);
        } catch(Exception $e) {
            Log::error($e);

            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * Display warehouse detail
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function detail(Request $request, $id)
    {
        try {
            $warehouse = $this->warehouseService->detail($id);

            return view('admin.warehouse.detail', $warehouse);
        } catch(Exception $e) {
            Log::error($e);

            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     *  New warehouse
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function new(Request $request)
    {
        return view('admin.warehouse.new');
    }

    /**
     * Create new warehouse
     *
     * @param App\Http\Requests\Admin\CreateWarehouseRequest $request
     * @return \Illuminate\Contracts\View\View
     */
    public function create(CreateWarehouseRequest $request)
    {
        try {
            $input = $request->only('name', 'address');
            $info = $this->warehouseService->create($input);

            return redirect()->back()->with('success', "Create warehouse successfully!");
        } catch(Exception $e) {
            Log::error($e);
            
            return redirect()->back()->with('fail', "Create warehouse fail!");
        }
    }

    /**
     * Delete warehouse
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function delete(Request $request)
    {
        try {
            $input = $request->only('id');
            $info = $this->warehouseService->delete($input);

            return redirect()->back()->with('success', "Update warehouse successfully!");
        } catch(Exception $e) {
            Log::error($e);

            return redirect()->back()->with('fail', "Update warehouse fail!");
        }
    }

    /**
     * Add warehouse price
     *
     * @param App\Http\Requests\Admin\AddUnitPriceRequest $request
     * @return \Illuminate\Contracts\View\View
     */
    public function addUnitPrice(AddUnitPriceRequest $request)
    {
        try {
            $input = $request->only('name', 'price', 'unit_id', 'warehouse_id');
            $info = $this->warehouseService->addUnitPrice($input);

            return redirect()->back()->with('updateUnitSuccess', "Create warehouse unit price successfully!");
        } catch(Exception $e) {
            Log::error($e);
            
            return redirect()->back()->with('updateUnitFail', "Create unit price fail!");
        }
    }

    /**
     * Delete warehouse unit price
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function deleteUnitPrice(Request $request)
    {
        try {
            $input = $request->only('id');
            $info = $this->warehouseService->deleteUnitPrice($input);

            return redirect()->back()->with('updateUnitSuccess', "Delete unit price successfully!");
        } catch(Exception $e) {
            Log::error($e);
         
            return redirect()->back()->with('updateUnitFail', "Delete unit price fail!");
        }
    }
}
