<?php

namespace App\Http\Controllers\Staff;

use App\Services\Staff\StaffUserService;
use App\Http\Requests\Staff\CheckUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class StaffUserController extends StaffBaseController
{
    protected $userService;

    public function __construct(StaffUserService $userService)
    {
        parent::__construct();
        $this->userService = $userService;
    }

    /**
     * Display list user
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function list(Request $request)
    {
        try {
            $input = $request->only('email', 'isMembership');
            $users = $this->userService->list($input);

            return view('staff.user.list', $users);
        } catch(Exception $e) {
            Log::error($e);

            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * Show user profile
     *
     * @param  int  $userId
     * @return \Illuminate\Contracts\View\View
     */
    public function profile(Request $request, $id)
    {
        $userInfo = $this->userService->getUserInfo($id);

        return view('staff.user.profile', $userInfo);
    }

    /**
     * Update membership
     *
     * @param App\Http\Requests\Staff\CheckUserRequest $request
     * @return \Illuminate\Contracts\View\View
     */
    public function update(CheckUserRequest $request)
    {
        try {
            $input = $request->only('id', 'verify-submit', 'membership-submit');
            if(isset($input['membership-submit'])) {
                $this->userService->setMembership($input);
            } else {
                $this->userService->setVerify($input);
            }

            return redirect()->back()->with('success', "Update successfully!");
        } catch(Exception $e) {
            Log::error($e);

            return redirect()->back()->with('fail', "Update fail!");
        }
    }
}
