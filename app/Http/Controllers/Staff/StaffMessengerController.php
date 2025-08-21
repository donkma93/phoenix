<?php

namespace App\Http\Controllers\Staff;

use App\Services\Staff\StaffMessengerService;
use Illuminate\Http\Request;

class StaffMessengerController extends StaffBaseController
{
    protected $messageService;

    public function __construct(StaffMessengerService $messageService)
    {
        parent::__construct();

        $this->messageService = $messageService;
    }

    /**
     * Display staff dashboard
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('staff.messenger.index');
    }

    public function detail(Request $request) 
    {
        try {
            $param = $request->only('chat_box_id');
            $message = $this->messageService->getDetail($param);

            return $message;
        } catch(Exception $e) {
            Log::error($e);
            
            //TODO redirect to error page
            abort(500);
        }
    }

    public function sendMessage(Request $request) 
    {
        try {
            $param = $request->only('chat_box_id', 'message');
            $this->messageService->update($param);
        } catch(Exception $e) {
            Log::error($e);
            
            //TODO redirect to error page
            abort(500);
        }
    }

    public function getChatBox(Request $request) 
    {
        try {
            $param = $request->only('email');
            $chatList = $this->messageService->getChatBox($param);

            return $chatList;
        } catch(Exception $e) {
            Log::error($e);
            
            //TODO redirect to error page
            abort(500);
        }
    }
}
