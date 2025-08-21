<?php

namespace App\Http\Controllers\Staff;

use App\Services\Staff\StaffSettingService;
use App\Http\Requests\Staff\ResetPasswordRequest;
use App\Http\Requests\Staff\UpdateProfileRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StaffSettingController extends StaffBaseController
{
    protected $settingService;

    public function __construct(StaffSettingService $settingService)
    {
        parent::__construct();

        $this->settingService = $settingService;
    }

    /**
     * Display staff change password
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function password()
    {
        return view('staff.setting.password');
    }

    /**
     * Display staff profile
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function profile()
    {
        $userInfo = $this->settingService->getUserInfo();

        return view('staff.setting.profile', $userInfo);
    }

    /**
     * Update staff password.
     *
     * @param  App\Http\Requests\Staff\ResetPasswordRequest;  $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(ResetPasswordRequest $request) {
        try {
            $input = $request->only('password');

            $this->settingService->changePassword($input);

            return redirect()->back()->with('success', "Change password successfully!");
        } catch(Exception $e) {
            Log::error($e);

            return redirect()->back()->with('fail', "Change password fail!");
        }
    }

    /**
     * Update staff profile.
     *
     * @param  App\Http\Requests\Staff\UpdateProfileRequest;  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            $input = $request->only('first_name', 'last_name', 'gender', 'birthday', 'avatar', 'phone', 'address', 'post_code');
            $this->settingService->updateProfile($input);

            return redirect()->back()->with('success', "Update profile successfully!");
        } catch(Exception $e) {
            Log::error($e);
            
            return redirect()->back()->with('fail', "Update profile fail!");
        }
    }
}
