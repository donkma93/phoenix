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
        // Latest message id per chat box (avoids loading entire chat history into memory)
        $latestIds = ChatBoxDetail::query()
            ->selectRaw('MAX(id) as id')
            ->when(isset($request['email']), function ($q) use ($request) {
                $q->whereHas('user', function ($user) use ($request) {
                    $user->where('email', 'like', '%' . $request['email'] . '%');
                });
            })
            ->groupBy('chat_box_id')
            ->pluck('id');

        if ($latestIds->isEmpty()) {
            return [];
        }

        return ChatBoxDetail::with([
            'chatBox.user.profile',
            'user',
        ])
            ->whereIn('id', $latestIds)
            ->orderByDesc('created_at')
            ->get()
            ->all();
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
