<?php

namespace App\Http\Controllers\User;

use App\Services\User\UserMessengerService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class UserMessengerController extends UserBaseController
{
    protected $chattingService;

    public function __construct(UserMessengerService $chattingService)
    {
        parent::__construct();
        $this->chattingService = $chattingService;
    }

    public function index(Request $request)
    {
        try {
            return view('user.chat.index');
        } catch(Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    public function getChatBox(Request $request)
    {
        try {
            $message = $this->chattingService->getMessage();

            return $message;
        } catch(Exception $e) {
            Log::error($e);
            
            //TODO redirect to error page
            abort(500);
        }
    }

    public function update(Request $request)
    {
        try {
            $parameter = $request->only('message');
            $message = $this->chattingService->update($parameter);

            return $message;
        } catch(Exception $e) {
            Log::error($e);
            
            //TODO redirect to error page
            abort(500);
        }
    }

    public function getNewMessage(Request $request) 
    {
        try {
            $message = $this->chattingService->getNewMessage();

            return $message;
        } catch(Exception $e) {
            Log::error($e);
            
            //TODO redirect to error page
            abort(500);
        }
    }
}
