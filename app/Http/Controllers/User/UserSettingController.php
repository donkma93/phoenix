<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\UpdateUserPasswordRequest;
use App\Http\Requests\User\UpdateUserProfileRequest;
use App\Services\User\UserSettingService;
use Exception;
use Illuminate\Support\Facades\Log;

class UserSettingController extends UserBaseController
{
    protected $userSettingService;

    public function __construct(UserSettingService $userSettingService)
    {
        parent::__construct();
        $this->userSettingService = $userSettingService;
    }

    /**
     * Show the form to edit user profile.
     *
     * @return \Illuminate\View\View
     */
    public function showProfile()
    {
        try {
            $profile =  $this->userSettingService->showProfile();
            return view('user.setting.profile', $profile);
        } catch(Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * Update user profile.
     *
     * @param  App\Http\Requests\User\UpdateUserProfileRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(UpdateUserProfileRequest $request)
    {
        try {
            $input = $request->all();
            $this->userSettingService->updateProfile($input);
            return redirect()->route('setting.profile.index')->with('success', "User profile updated successfully.");
        } catch(Exception $e) {
            Log::error($e);
            return redirect()->route('setting.profile.index')->with('fail', "User profile update failed.");
        }
    }

    /**
     * Show the form to edit user password.
     *
     * @return \Illuminate\View\View
     */
    public function editPassword()
    {
        return view('user.setting.password');
    }

    /**
     * Update user password.
     *
     * @param  App\Http\Requests\User\UpdateUserPasswordRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(UpdateUserPasswordRequest $request)
    {
        try {
            $newPassword = $request->input('password');
            $this->userSettingService->updatePassword($newPassword);
            return redirect()->route('setting.profile.index')->with('success', "Password updated successfully");
        } catch(Exception $e) {
            Log::error($e);
            return redirect()->route('setting.profile.index')->with('fail', "Password update failed");
        }
    }
}
