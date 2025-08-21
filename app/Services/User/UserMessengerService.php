<?php

namespace App\Services\User;

use App\Models\ChatBox;
use App\Models\ChatBoxDetail;
use App\Services\UserBaseServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class UserMessengerService extends UserBaseService implements UserBaseServiceInterface
{
    public function getMessage()
    {
        $message = ChatBoxDetail::with('chatBox')->whereHas('chatBox', function ($query) {
            $query->where('user_id', Auth::id());
        })->get();

        return $message;
    }

    public function update($request) 
    {
        try {
            DB::beginTransaction();

            $chatBox = ChatBox::where('user_id', Auth::id())->first();
            
            if(empty($chatBox)) {
                $newChatBox = ChatBox::create([
                    'user_id' => Auth::id()
                ]);

                $id = $newChatBox->id;
            } else {
                $id = $chatBox->id;
            }
            
            $newMessage = [
                'message' => $request['message'],
                'from_user_id' => Auth::id(),
                'chat_box_id' => $id,
                'staff_get' => 0
            ];

            ChatBoxDetail::create($newMessage);

            $message = $this->getNewMessage();

            DB::commit();

            return $message;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function getNewMessage() 
    {
        $total = ChatBoxDetail::with('chatBox')->whereNull('read_at')->whereHas('chatBox', function ($query) {
            $query->where('user_id', Auth::id());
        })->where('from_user_id', '<>', Auth::id())->count();

        $queryBuilder =  ChatBoxDetail::with('chatBox')->whereHas('chatBox', function ($query) {
            $query->where('user_id', Auth::id());
        })->where('user_get', 0);
        
        $newMessage = $queryBuilder->get();

        $queryBuilder->update(['user_get' => 1]);

        return [
            'total' => $total,
            'newMessage' => $newMessage
        ];
    }
}
