<?php

namespace App\Services\Admin;

use App\Models\PriceList;
use App\Services\AdminBaseServiceInterface;
use App\Models\User;
use App\Models\Partner;
use App\Models\UserProfile;
use App\Models\Package;
use App\Models\UserRequest;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserService extends AdminBaseService implements AdminBaseServiceInterface
{
    public function list($request)
    {
        if(isset($request['role'])) {
            $users = User::where('role', $request['role']);
        } else {
            $users = User::where('role', '<>', User::ROLE_ADMIN);
        }

        if(isset($request['email'])) {
            $users = $users->where('email', 'like', '%'.$request['email'].'%');
        }

        if(isset($request['isVerify'])) {
            if($request['isVerify'] == 1) {
                $users = $users->whereNotNull('email_verified_at');
            } else {
                $users = $users->whereNull('email_verified_at');
            }
        }

        if(isset($request['onlyDeleted'])) {
            if($request['onlyDeleted'] == 1) {
                $users = $users->onlyTrashed();
            } else {
                $users = $users->withTrashed();
            }
        }

        $users = $users->paginate()->withQueryString();

        $userList = User::where('role', User::ROLE_USER)->withTrashed()->pluck('email')->toArray();

        return [
            'oldInput' => $request,
            'users' => $users,
            'userList' => $userList
        ];
    }

    public function create($request){
        DB::beginTransaction();

        $partner = Partner::where('partner_code', $request['partner_code'])->first();
        $user_create_id = auth()->user()->id;
        $partnerId = null;
        $partnerCode = null;

        if (isset($partner)) {
            /*$partnerId = $partner->id;
            $partnerCode = $partner->partner_code;*/
            return [
                'status'=>'error',
                'message'=>'Partner code already exists!'
            ];
            //return redirect()->back()->with('error', 'Partner code already exists!');
        } else {
            $rs = Partner::create([
                'partner_code'=>$request['partner_code'],
                'partner_name'=>$request['email'],
                'created_by'=>$user_create_id
            ]);
            $partnerId = $rs->id;
            $partnerCode = $request['partner_code'];
        }

        try {
            $userInfo = User::create([
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
                'role' => $request['role'],
                'partner_id' => $partnerId,
                'partner_code' => $partnerCode,
                'email_verified_at' => date('Y-m-d H:i:s')
            ]);

            $userInfo->profile()->create();
            $userInfo->addresses()->create();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }

        return $userInfo;
    }

    public function getUserInfo($id)
    {
        $workInfo = [];
        $userInfo = User::withTrashed()->with(['profile' => function ($profile) {
            $profile->withTrashed();
         }])->find($id);

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


            $priceInfo = DB::select('select `pnx_price_table`.`name`, `pnx_price_table`.`id` from `users` inner join `partners` on `partners`.`partner_code` COLLATE utf8mb4_general_ci = `users`.`partner_code` COLLATE utf8mb4_general_ci inner join `pnx_price_table` on `pnx_price_table`.`id` = `partners`.`id_price_table` where `users`.`id` = ' . $id);
            $listPriceTable = DB::table('pnx_price_table')->where('status', PriceList::TABLE_ACTIVE)->select('id', 'name')->get()->toArray();
        } else {
            $requestTotal = UserRequest::where('staff_id', $id)->count();
            $requestCount = UserRequest::select('status', DB::raw('count(*) as total'))
                ->where('staff_id', $id)
                ->groupBy('status')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item['status'] => $item['total']];
                })
                ->all();

            $workInfo = [
                'requestTotal' => $requestTotal,
                'requestCount' => $requestCount,
            ];
        }

        return [
            'userInfo' => $userInfo,
            'priceInfo' => $priceInfo[0] ?? [],
            'listPriceTable' => $listPriceTable ?? [],
            'workInfo' => $workInfo
        ];
    }

    public function deleteUser($request)
    {
        $userInfo = User::withTrashed()->find($request['id']);

        if(isset($userInfo['deleted_at'])) {
            $userInfo->restore();
        } else {
            $userInfo->delete();
        }
    }

    public function setOrRemoveMembership($request)
    {
        $userInfo = UserProfile::withTrashed()->where('user_id',$request['id'])->first();

        if(isset($userInfo->membership_at)) {
            $userInfo->membership_at = null;
        } else {
            $userInfo->membership_at = date('Y-m-d');
        }

        $userInfo->save();
    }

    public function setOrRemoveVerify($request)
    {
        $userInfo = User::withTrashed()->find($request['id']);

        if(isset($userInfo->email_verified_at)) {
            $userInfo->email_verified_at = null;
        } else {
            $userInfo->email_verified_at = date('Y-m-d');
        }

        $userInfo->save();
    }
}
