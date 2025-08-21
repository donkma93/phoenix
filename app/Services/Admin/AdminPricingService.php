<?php

namespace App\Services\Admin;

use App\Services\AdminBaseServiceInterface;
use App\Models\PricingRequest;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminPricingService extends AdminBaseService implements AdminBaseServiceInterface
{
    public function list($request)
    {
        $pRequests = PricingRequest::orderByDesc('created_at');

        if(isset($request['email'])) {
            $pRequests = $pRequests->whereHas('user', function ($query) use ($request) {
                $query->where('email', 'like', '%'.$request['email'].'%');
            });
        }

        if(isset($request['isDone'])) {
            $pRequests = $pRequests->where('is_done', $request['isDone']);
        }

        $pRequests = $pRequests->paginate()->withQueryString();

        $users = User::where('role', User::ROLE_USER)->withTrashed()->pluck('email')->toArray();

        return [
            'oldInput' => $request,
            'users' => $users,
            'pRequests' => $pRequests
        ];
    }
    
    public function update($request) {
        $pricing = PricingRequest::find($request['id']);

        $pricing->is_done = 1;
        $pricing->save();
    }
}
