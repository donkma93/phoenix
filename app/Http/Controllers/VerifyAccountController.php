<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class VerifyAccountController extends Controller
{
    public function verifyAcc(Request $request)
    {
        $user_id = $request->input('id');
        $user = DB::table('users')->find($user_id);

        if (!$user) {
            return redirect()->route('register', ['locale' => app()->getLocale()]);
        }

        if ($user->email_verified_at) {
            return redirect()->route('dashboard')->with('success', 'The account has been successfully verified!');
        }

        $token = $request->input('token');

        if ($token === md5(date('YmdHis', strtotime($user->created_at)) . $user->email . config('app.str_secret'))) {
            DB::table('users')
                ->where('id', $user_id)
                ->update([
                    'email_verified_at' => date('Y-m-d H:i:s', time()),
                ]);
        }

        return redirect()->route('dashboard')->with('success', 'Successful account registration!');
    }
}
