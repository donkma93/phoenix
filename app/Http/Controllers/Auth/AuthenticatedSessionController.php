<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


class AuthenticatedSessionController extends Controller
{

    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('client.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();


        $attributes = request()->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        





        $request->session()->regenerate();

        if (!$request->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice', ['locale' => app()->getLocale()]);
        }


        //-- call api get token
        $requestAPI = Request::create('/api/auth/login', 'POST', [
            'email' =>  $attributes['email'],
            'password' =>  $attributes['password']
        ]);
        $response = Route::dispatch($requestAPI);

        $role = $request->user()->role;

        return redirect(route(User::$home[$role]))->with('api-auth', json_decode($response->content())->access_token);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
