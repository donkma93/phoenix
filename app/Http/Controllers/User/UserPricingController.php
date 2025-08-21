<?php

namespace App\Http\Controllers\User;

use App\Services\User\UserPricingService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserPricingController extends UserBaseController
{
    protected $requestService;

    public function __construct(UserPricingService $pricingService)
    {
        parent::__construct();
        $this->pricingService = $pricingService;
    }

    /**
     * Display a listing of current user request.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $items = $this->pricingService->index();

        return view('user.pricing.index', $items);
    }

    /**
     * Send request
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function create(Request $request)
    {
        try {
            $this->pricingService->create($request);

            return redirect()->back()->with('success', "Pricing request successed");
        } catch(Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            return redirect()->back()->with('fail', "Pricing request fail");
        }
    }
}
