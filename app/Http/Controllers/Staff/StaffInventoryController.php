<?php

namespace App\Http\Controllers\Staff;

use App\Http\Requests\Staff\UpdateInventoryRequest;
use App\Http\Requests\Staff\UpdateAvailableInventoryRequest;
use App\Services\Staff\StaffInventoryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StaffInventoryController extends StaffBaseController
{
    protected $inventoryService;

    public function __construct(StaffInventoryService $inventoryService)
    {
        parent::__construct();

        $this->inventoryService = $inventoryService;
    }

    /**
     * View for listing inventory
     *
     * @return \Illuminate\View\View
     */
    public function list(Request $request)
    {
        $input = $request->only('product', 'sku', 'store');
        $info = $this->inventoryService->list($input);

        return view('staff.inventory.list', $info);
    }

    /**
     * Detail inventory
     *
     * @return \Illuminate\View\View
     */
    public function detail($id)
    {
        $info = $this->inventoryService->detail($id);

        return view('staff.inventory.detail', $info);
    }

    /**
     * Update inventory
     *
     * @param App\Http\Requests\Staff\UpdateAvailableInventoryRequest $request
     * @return \Illuminate\Http\Response
     */
    public function updateAvailable(UpdateAvailableInventoryRequest $request)
    {
        try {
            $parameters = $request->all();
            $id = $this->inventoryService->updateAvailable($parameters);

            return redirect()->route('staff.inventory.detail', ['id' => $id])->with('success', "Update inventory successfully!");
        } catch(Exception $e) {
            Log::error($e);

            return redirect()->back()->with('fail', "Update inventory fail!");
        }
    }

    /**
     * Update inventory
     *
     * @param App\Http\Requests\Staff\UpdateInventoryRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateInventoryRequest $request)
    {
        try {
            $parameters = $request->all();
            $this->inventoryService->update($parameters);

            return redirect()->back()->with('success', "Update inventory successfully!");
        } catch(Exception $e) {
            Log::error($e);
            return redirect()->back()->with('fail', "Update inventory fail!");
        }
    }

     /**
     * Update inventory history
     *
     * @param App\Http\Requests\Staff\UpdateInventoryRequest $request
     * @return \Illuminate\Http\Response
     */
    public function updateHistory(UpdateInventoryRequest $request)
    {
        try {
            $parameters = $request->all();
            $this->inventoryService->updateHistory($parameters);

            return redirect()->back()->with('success', "Update inventory successfully!");
        } catch(Exception $e) {
            Log::error($e);
            return redirect()->back()->with('fail', "Update inventory fail!");
        }
    }
}
