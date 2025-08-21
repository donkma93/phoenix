<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Auth\RegisterService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Exception;

class RegisteredUserController extends Controller
{
    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \App\Http\Requests\Auth\RegisterRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request)
    {
        try {
            $request->flash();
            $user = $this->registerService->register($request);
        } catch(Exception $e) {
            Log::error($e);

            return redirect()->route('register')->with('message', 'Have an error when register!');;
        }

        Auth::login($user);

        return redirect()->route('verification.notice', ['locale' => app()->getLocale()]);
    }
}
