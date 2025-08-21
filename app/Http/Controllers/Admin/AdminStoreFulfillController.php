<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\AdminStoreFulfillService;
use App\Http\Requests\Admin\AddUnitPriceRequest;
use App\Http\Requests\Admin\CreateStoreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class AdminStoreFulfillController extends AdminBaseController
{
    protected $storeService;

    public function __construct(AdminStoreFulfillService $storeService)
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

            return view('admin.store-fulfill.list', $stores);
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

            return view('admin.store-fulfill.detail', $store);
        } catch(Exception $e) {
            Log::error($e);

            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     *  New store
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function new(Request $request)
    {
        return view('admin.store-fulfill.new');
    }

    /**
     * Create new store
     *
     * @param App\Http\Requests\Admin\CreateStoreRequest $request
     * @return \Illuminate\Contracts\View\View
     */
    public function create(CreateStoreRequest $request)
    {
        try {
            $input = $request->only('name', 'code');
            $info = $this->storeService->create($input);

            return redirect()->back()->with('success', "Create store successfully!");
        } catch(Exception $e) {
            Log::error($e);
            return redirect()->back()->with('fail', "Create store fail!");
        }
    }

    /**
     * Delete store
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function delete(Request $request)
    {
        try {
            $input = $request->only('id');
            $info = $this->storeService->delete($input);

            return redirect()->back()->with('success', "Update store successfully!");
        } catch(Exception $e) {
            Log::error($e);

            return redirect()->back()->with('fail', "Update store fail!");
        }
    }
}
