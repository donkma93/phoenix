<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\AdminPricingService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class AdminPricingController extends AdminBaseController
{
    protected $pricingService;

    public function __construct(AdminPricingService $pricingService)
    {
        parent::__construct();
        $this->pricingService = $pricingService;
    }

    /**
     * Display list pricing request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function list(Request $request)
    {
        try {
            $paramter = $request->all();
            $pRequest = $this->pricingService->list($paramter);

            return view('admin.pricing.list', $pRequest);
        } catch(Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * Update user
     *
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function update(Request $request)
    {
        try {
            $paramter = $request->all();
            $pRequest = $this->pricingService->update($paramter);

            return redirect()->back()->with('success', "Update request successfully!");
        } catch(Exception $e) {
            Log::error($e);

            return redirect()->back()->with('fail', "Update request fail!");
        }
    }
}
