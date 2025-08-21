<?php

namespace App\Services\Staff;

use App\Models\ChatBox;
use App\Models\ChatBoxDetail;
use App\Services\StaffBaseServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffMessengerService extends StaffBaseService implements StaffBaseServiceInterface
{
    public function getChatBox($request)
    {
        $listChat = ChatBoxDetail::select("*")->with(['chatBox' => function($chatBox) {
            $chatBox->with(['user' => function($user) {
                $user->with('profile');
            }]);
        }, 'user'])->whereHas('user', function($user) use ($request) {
            if(isset($request['email'])) $user->where('email', 'like', '%'.$request['email'].'%');
        })->orderByDesc('created_at')->get();
    
        return $listChat->unique('chat_box_id')->values()->all();
    }

    public function update($request) 
    {
        try {
            DB::beginTransaction();

            $chatBox = ChatBox::find($request['chat_box_id']);
            
            if(empty($chatBox)) {
                DB::rollback();

                return 'No conversation';
            }
            
            $newMessage = [
                'message' => $request['message'],
                'from_user_id' => Auth::id(),
                'chat_box_id' => $request['chat_box_id'],
                'user_get' => 0,
                'staff_get' => 1
            ];

            ChatBoxDetail::create($newMessage);

            $queryBuilder =  ChatBoxDetail::with(['chatBox' => function ($query) {
                $query->where('user_id', Auth::id());
            }])->where('staff_get', 1);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function getDetail($request)
    {
        $queryBuilder = ChatBoxDetail::with(['user', 'chatBox'])->where('chat_box_id', $request['chat_box_id']);

        $queryBuilder->update(['staff_get' => 1]);

        $listChat = $queryBuilder->get();

        $chatBox = ChatBox::with(['user' => function($user) {
            $user->with('profile');
        }])->find($request['chat_box_id']);

        return [
            'listChat' => $listChat,
            'chatBox' => $chatBox
        ];
    }

    public function updateReadAt($request) 
    {
        $now = Carbon::now();

        $queryBuilder =  ChatBoxDetail::with(['chatBox' => function ($query) use ($request) {
            $query->where('user_id', $request['user_id']);
        }])->whereNull('read_at')->where('from_user_id', '<>', $request['user_id'])->update(['read_at' => $now]);
    }
}
