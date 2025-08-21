<?php

namespace App\Services\User;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserAddress;
use App\Services\UserBaseServiceInterface;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSettingService extends UserBaseService implements UserBaseServiceInterface
{
    public function showProfile()
    {
        $address = UserAddress::where('user_id', Auth::id())->where('in_use', 1)->first();
        $profile = UserProfile::where('user_id', Auth::id())->first();

        return [
            'profile' =>  $profile,
            'address' =>  $address,
        ];
    }

    public function updateProfile($input)
    {
        DB::beginTransaction();

        try {
            $properties = ['first_name', 'last_name', 'phone', 'birthday', 'gender'];
            $data = [];

            foreach ($properties as $prop) {
                // allow update null
                if (array_key_exists($prop, $input)) {
                    $data[$prop] = $input[$prop];
                }
            }

            if (isset($input['avatar'])) {
                $data['avatar'] = $input['avatar']->move('imgs' . DIRECTORY_SEPARATOR . UserProfile::IMG_FOLDER, cleanName($input['avatar']->getClientOriginalName()));
            }

            UserProfile::updateOrCreate([
                'user_id' => Auth::id()
            ], $data);


            $dataAddress = [];

            if(isset($input['address'])) {
                $dataAddress['building'] = $input['address'];
            }

            if(isset($input['post_code'])) {
                $dataAddress['post_code'] = $input['post_code'];
            }

            UserAddress::updateOrCreate([
                'user_id' => Auth::id(),
                'in_use' => 1,
            ], $dataAddress);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    public function updatePassword($newPassword)
    {
        $user = User::findOrFail(Auth::id());
        $user->password = Hash::make($newPassword);
        $user->save();
    }
}
