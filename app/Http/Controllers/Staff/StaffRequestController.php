<?php

namespace App\Http\Controllers\Staff;

use App\Services\Staff\StaffRequestService;
use App\Http\Requests\Staff\AddPackageRequest;
use App\Http\Requests\Staff\UpdatePackageSavedRequest;
use App\Http\Requests\Staff\SearchByDateRequest;
use App\Http\Requests\Staff\UpdateRequestHour;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StaffRequestController extends StaffBaseController
{
    protected $requestService;

    public function __construct(StaffRequestService $requestService)
    {
        parent::__construct();

        $this->requestService = $requestService;
    }

    /**
     * Display a listing of current user request.
     *
     * @param App\Http\Requests\Staff\SearchByDateRequest $request
     * @return \Illuminate\Contracts\View\View
     */
    public function listRequest(SearchByDateRequest $request)
    {
        try {
            $input = $request->only('type', 'status', 'email', 'barcode', 'endDate', 'startDate');
            $requests = $this->requestService->getRequests($input);

            //dd($requests);
            //return view('staff.request.list', $requests);
            return view('request.list', $requests);
        } catch(Exception $e) {
            Log::error($e);

            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * Display a listing of current user request.
     *
     * @param  int  $userRequestId
     * @return \Illuminate\Contracts\View\View
     */
    public function requestDetail(Request $request, $userRequestId)
    {
        try {
            $input = $request->only('warehouse', 'status', 'barcode');
            $requestDetail = $this->requestService->getRequest($userRequestId, $input);

            return view('request.detail', $requestDetail);
        } catch(Exception $e) {
            Log::error($e);

            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * Update Package status, warehouse
     *
     * @param App\Http\Requests\Staff\UpdatePackageSavedRequest $request
     * @return \Illuminate\Http\Response
     */
    public function updatePackage(UpdatePackageSavedRequest $request)
    {
        try {
            $parameters = $request->all();

            $message = $this->requestService->updatePackage($parameters);
            if($message != null) {
                return redirect()->back()->with('fail', $message);
            }
            return redirect()->back()->with('success', "Update package successfully!");
        } catch(Exception $e) {
            Log::error($e);

            //TODO redirect to error page
            return redirect()->back()->with('fail', "Update package fail");
        }
    }

    /**
     * Update request status
     *
     * @return \Illuminate\Http\Response
     */
    public function updateRequest(Request $request)
    {
        try {
            $parameters = $request->all();
            $this->requestService->updateUserRequest($parameters['id'], $parameters['status'], Auth::id());

        } catch(Exception $e) {
            Log::error($e);
        }
    }

    /**
     * Update request status
     *
     * @param App\Http\Requests\Staff\AddPackageRequest $request
     * @return \Illuminate\Http\Response
     */
    public function addPackage(AddPackageRequest $request)
    {
        try {
            $parameters = $request->all();
            $result = $this->requestService->addPackage($parameters);

            if(isset($result['message'])) {
                return redirect()->back()->with('fail', $result['message']);
            }

            return redirect()->back()->with('success', "Add package successfully!");
        } catch(Exception $e) {
            Log::error($e);

            //TODO redirect to error page
            return redirect()->back()->with('fail', "Add package fail");
        }
    }

    /**
     * Get package
     *
     * @return \Illuminate\Http\Response
     */
    public function getPackage(Request $request)
    {
        try {
            $parameters = $request->only('user_id', 'unit_number', 'barcode', 'package_group_id');
            $result = $this->requestService->getPackage($parameters);

            return $result;
        } catch(Exception $e) {
            Log::error($e);

            return $e;
        }
    }

    /**
     * Check package status
     *
     * @return \Illuminate\Http\Response
     */
    public function checkPackage(Request $request)
    {
        try {
            $parameters = $request->only('user_id', 'barcode', 'package_group_id');
            $result = $this->requestService->checkPackage($parameters);

            return $result;
        } catch(Exception $e) {
            Log::error($e);

            return $e;
        }
    }

    /**
     * Add package to request
     *
     * @return \Illuminate\Http\Response
     */
    public function savePackage(Request $request)
    {
        try {
            $parameters = $request->all();
            $this->requestService->savePackage($parameters);

            return redirect()->back()->with('success', "Save package successfully!");
        } catch(Exception $e) {
            Log::error($e);

            return redirect()->back()->with('fail', "Save package fail");
        }
    }

    /**
     * Set total request time
     *
     * @param App\Http\Requests\Staff\UpdateRequestHour $request
     * @return \Illuminate\Http\Response
     */
    public function updateTime(UpdateRequestHour $request)
    {
        try {
            $parameters = $request->all('user_request_id');
            $this->requestService->setTimeForRequest($parameters);

            return redirect()->back()->with('success', "Update time successfully!");
        } catch(Exception $e) {
            Log::error($e);

            return redirect()->back()->with('fail', "Update time fail");
        }
    }
}
