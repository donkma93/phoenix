<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\UpdateIncomingInventoryRequest;
use App\Http\Requests\User\UpdateRemindRequest;
use App\Http\Requests\User\UpdateListRemindRequest;
use App\Services\User\UserInventoryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserInventoryController extends UserBaseController
{
    protected $inventoryService;

    public function __construct(UserInventoryService $inventoryService)
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
        try {
            $input = $request->only('product');
            $info = $this->inventoryService->list($input);

            return view('user.inventory.list', $info);
        } catch(Exception $e) {
            Log::error($e);
            abort(500);
        }
    }

    public function inventoryPrint(Request $request)
    {       
        $input = $request->input('label_list');

        if (!$input) {
            return back();
        }

        return view('user.inventory.print', [
            'sku_list' => $input
        ]);
    }

    /**
     * Detail inventory
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        try {
            $info = $this->inventoryService->detail($id);

            return view('user.inventory.detail', $info);
        } catch(Exception $e) {
            Log::error($e);
            abort(500);
        }
    }

    /**
     * Remind inventory
     *
     * @return \Illuminate\View\View
     */
    public function remind(Request $request)
    {
        $info = $this->inventoryService->remind($request);

        return view('user.inventory.remind', $info);
    }

    /**
     * Update inventory
     *
     * @param App\Http\Requests\User\UpdateIncomingInventoryRequest $request
     * @return \Illuminate\Http\Response
     */
    public function updateIncomming(UpdateIncomingInventoryRequest $request)
    {
        try {
            $parameters = $request->all();
            $this->inventoryService->updateIncomming($parameters);

            return redirect()->route('inventories.show', ['id' => $parameters['id']])->with('success', "Update inventory successfully!");
        } catch(Exception $e) {
            Log::error($e);

            return redirect()->back()->with('fail', "Update inventory fail!");
        }
    }

    /**
     * Update list remind
     *
     * @param App\Http\Requests\User\UpdateListRemindRequest $request
     * @return \Illuminate\Http\Response
     */
    public function updateListRemind(UpdateListRemindRequest $request)
    {
        try {
            $parameters = $request->all();
            $this->inventoryService->updateReminds($parameters);

            return redirect()->back()->with('success', "Update remind successfully!");
        } catch(Exception $e) {
            Log::error($e);

            return redirect()->back()->with('fail', "Update inventory fail!");
        }
    }

    /**
     * Update remind
     *
     * @param App\Http\Requests\User\UpdateRemindRequest $request
     * @return \Illuminate\Http\Response
     */
    public function updateRemind(UpdateRemindRequest $request)
    {
        try {
            $parameters = $request->all();
            $this->inventoryService->updateRemind($parameters);

            return redirect()->back()->with('success', "Update remind successfully!");
        } catch(Exception $e) {
            Log::error($e);

            return redirect()->back()->with('fail', "Update inventory fail!");
        }
    }
}
