<?php

namespace App\Services\Auth;

use App\Models\Partner;
use App\Services\AuthBaseServiceInterface;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Mail\PhoenixMail;

class RegisterService extends AuthBaseService implements AuthBaseServiceInterface
{

    /**
     * @param $user
     *
     * @return @void
     */
    public function register($user) {
        DB::beginTransaction();

        try {
            $rs = Partner::create([
                'partner_code'=>$user->partner_code,
                'partner_name'=>$user->email
            ]);

            $partnerId = $rs->id;


            $userInfo = User::create([
                'email' => $user->email,
                'password' => Hash::make($user->password),
                'role' => 2,
                'partner_id' => $partnerId,
                'partner_code' => $user->partner_code
            ]);

            // event(new Registered($userInfo));
            $userInfo->profile()->create();
            $userInfo->addresses()->create();

            DB::commit();

            // Gửi mail kích hoạt tài khoản
            $token_verify = md5(date('YmdHis', strtotime($userInfo->created_at)) . $userInfo->email . config('app.str_secret'));
            $url_verify = route('verify.account', ['id' => $userInfo->id, 'token' => $token_verify, 'locale' => app()->getLocale()]);
            $mail_data = [
                'url' => $url_verify,
                'template' => 'mail-templates.registration-successful'
            ];

            Mail::to($userInfo->email)->send(new PhoenixMail($mail_data));

        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }

        return $userInfo;
    }
}
