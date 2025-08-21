<?php

namespace App\Services\User;

use App\Services\UserBaseServiceInterface;
use App\Models\PricingRequest;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Str;

class UserPricingService extends UserBaseService implements UserBaseServiceInterface
{
    public function index() {
        $lastRequest = PricingRequest::where('user_id', Auth::id())->orderBy('created_at', 'DESC')->first();

        return [
            'lastRequest' => $lastRequest
        ];
    }

    public function create($request)
    {
        $note = null;
        if(isset($request['note'])) {
            $note = $request['note'];
        }
        
        PricingRequest::create([
            'user_id' => Auth::id(),
            'note' => $note,
            'is_done' => 0
        ]);
    }
}
