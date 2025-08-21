<?php

namespace App\Services\Staff;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\StaffBaseServiceInterface;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserAddress;
use Exception;

class StaffSettingService extends StaffBaseService implements StaffBaseServiceInterface
{
    function changePassword($input) {
        DB::beginTransaction();

        try {
            $userRequest = User::find(Auth::id());
            $userRequest->password = Hash::make($input['password']);
            $userRequest->save();
            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    function getUserInfo() {
        $userInfo = UserProfile::where('user_id', Auth::id())->first();
        $address = UserAddress::where('user_id', Auth::id())->where('in_use', 1)->first();

        return ['userInfo' => $userInfo, 'address' => $address];
    }

    function updateProfile($input) {
        DB::beginTransaction();

        try {
            $userInfo = UserProfile::where('user_id', Auth::id())->first();

            if(isset($input['first_name'])) {
                $userInfo->first_name = $input['first_name'];
            }

            if(isset($input['last_name'])) {
                $userInfo->last_name = $input['last_name'];
            }

            if(isset($input['gender'])) {
                $userInfo->gender = $input['gender'];
            }

            if(isset($input['birthday'])) {
                $userInfo->birthday = $input['birthday'];
            }

            if(isset($input['avatar'])) {
                $imageName = $input['avatar']->move('imgs' . DIRECTORY_SEPARATOR . UserProfile::IMG_FOLDER, cleanName($input['avatar']->getClientOriginalName()));

                $userInfo->avatar = $imageName;
            }

            if(isset($input['phone'])) {
                $userInfo->phone = $input['phone'];
            }

            $userInfo->save();

            $address = UserAddress::where('user_id', Auth::id())->where('in_use', 1)->first();

            if(isset($input['address'])) {
                $address->building = $input['address'];
            }

            if(isset($input['post_code'])) {
                $address->post_code = $input['post_code'];
            }

            $address->save();

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }
}
