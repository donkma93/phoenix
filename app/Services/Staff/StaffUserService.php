<?php

namespace App\Services\Staff;

use App\Services\StaffBaseServiceInterface;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Package;
use App\Models\UserRequest;
use Illuminate\Support\Facades\DB;

class StaffUserService extends StaffBaseService implements StaffBaseServiceInterface
{
    public function list($request)
    {
        $users = User::where('role', User::ROLE_USER)->with('profile');

        if(isset($request['email'])) {
            $users = $users->where('email', 'like', '%'.$request['email'].'%');
        }

        if(isset($request['isMembership'])) {
            if($request['isMembership'] == 1) {
                $users = $users->whereHas('profile', function ($query) {
                    $query->whereNotNull('membership_at');
                });
            } else {
                $users = $users->whereHas('profile', function ($query) {
                    $query->whereNull('membership_at');
                });
            }
        }

        $users = $users->orderByDesc('created_at');
        $users = $users->paginate()->withQueryString();

        $email = User::where('role', User::ROLE_USER)->pluck('email')->toArray();

        return [
            'oldInput' => $request,
            'users' => $users,
            'email' => $email,
        ];
    }

    public function getUserInfo($id)
    {
        $userInfo = User::with('profile')->find($id);
        $workInfo = [];
        
        if($userInfo->role == User::ROLE_USER) {
            $packageTotal = Package::where('user_id', $id)->count();
            $packageCount = Package::select('status', DB::raw('count(*) as total'))
                ->where('user_id', $id)
                ->groupBy('status')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item['status'] => $item['total']];
                })
                ->all();

            $requestTotal = UserRequest::where('user_id', $id)->count();
            $requestCount = UserRequest::select('status', DB::raw('count(*) as total'))
                ->where('user_id', $id)
                ->groupBy('status')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item['status'] => $item['total']];
                })
                ->all();

            $workInfo = [
                'packageTotal' => $packageTotal,
                'packageCount' => $packageCount,
                'requestTotal' => $requestTotal,
                'requestCount' => $requestCount,
            ];
        }

        return [
            'userInfo' => $userInfo,
            'workInfo' => $workInfo
        ];
    }

    public function setMembership($request)
    {
        $userInfo = UserProfile::where('user_id', $request['id'])->first();
        $userInfo->membership_at = date('Y-m-d H:i:s');

        $userInfo->save();
    }

    public function setVerify($request)
    {
        $userInfo = User::find($request['id']);
        $userInfo->email_verified_at = date('Y-m-d H:i:s');

        $userInfo->save();
    }
}
