<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\AdminUnitPriceService;
use App\Http\Requests\Admin\UpdateUnitPriceRequest;
use App\Http\Requests\Admin\CreateUnitPriceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class AdminUnitPriceController extends AdminBaseController
{
    protected $unitPriceService;

    public function __construct(AdminUnitPriceService $unitPriceService)
    {
        parent::__construct();
        $this->unitPriceService = $unitPriceService;
    }

    /**
     * Display list unit price
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function list(Request $request)
    {
        try {
            $unitPrice = $this->unitPriceService->list();

            return view('admin.unit-price.list', $unitPrice);
        } catch(Exception $e) {
            Log::error($e);

            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * Display list unit price
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function detail(Request $request, $id)
    {
        try {
            $priceDetail = $this->unitPriceService->detail($id);

            return view('admin.unit-price.detail', $priceDetail);
        } catch(Exception $e) {
            Log::error($e);

            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * Update unit price
     *
     * @param App\Http\Requests\Admin\UpdateUnitPriceRequest $request
     * @return \Illuminate\Contracts\View\View
     */
    public function update(UpdateUnitPriceRequest $request)
    {
        try {
            $input = $request->all();
            $info = $this->unitPriceService->update($input);

            return redirect()->back()->with('success', "Update unit price successfully!");
        } catch(Exception $e) {
            Log::error($e);

            return redirect()->back()->with('fail', "Update unit price fail!");
        }
    }

    /**
     * Update unit price
     *
     * @param App\Http\Requests\Admin\CreateUnitPriceRequest $request
     * @return \Illuminate\Contracts\View\View
     */
    public function create(CreateUnitPriceRequest $request)
    {
        try {
            $input = $request->all();
            $info = $this->unitPriceService->create($input);

            return redirect()->back()->with('success', "Create unit price successfully!");
        } catch(Exception $e) {
            Log::error($e);

            return redirect()->back()->with('fail', "Create unit price fail!");
        }
    }
}
