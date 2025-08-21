<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $email = $request->email;
        $token = $request->token;
        $user = DB::table('users')->where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request', ['locale' => app()->getLocale()])->with('error', 'The email does not exist in the system, please check again.');
        }

        $token_verify = md5(date('YmdHis', strtotime($user->updated_at)) . $user->email . config('app.str_secret'));

        if ($token !== $token_verify) {
            return redirect()->route('password.request', ['locale' => app()->getLocale()])->with('error', 'Invalid token!');
        }

        return view('auth.reset-password', ['email' => $email, 'token' => $token]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @param  App\Http\Requests\Auth\ResetPasswordRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(ResetPasswordRequest $request)
    {
        $token = $request->token;
        $email = $request->email;
        $password = $request->password;

        $user = DB::table('users')->where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request', ['locale' => app()->getLocale()])->with('error', 'The email does not exist in the system, please check again.');
        }

        $token_verify = md5(date('YmdHis', strtotime($user->updated_at)) . $user->email . config('app.str_secret'));

        if ($token !== $token_verify) {
            return redirect()->route('password.request', ['locale' => app()->getLocale()])->with('error', 'Invalid token!');
        }

        // Update password
        DB::table('users')->where('email', $user->email)
            ->update([
                'password' => Hash::make($password),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        return redirect()->route('login', ['locale' => app()->getLocale()]);

        /*
        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login', ['locale' => app()->getLocale()])->with('status', __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
        */
    }
}
