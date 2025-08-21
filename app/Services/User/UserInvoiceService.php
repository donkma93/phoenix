<?php

namespace App\Services\User;

use App\Services\UserBaseServiceInterface;
use App\Models\User;
use App\Models\UserInvoice;
use Illuminate\Support\Facades\Auth;

class UserInvoiceService extends UserBaseService implements UserBaseServiceInterface
{
    function list($request)
    {
        $invoices = UserInvoice::with('user')->has('user')->where('user_id', Auth::id());

        if(isset($request['month'])) {
            $invoices->where('month', $request['month']);
        }

        if(isset($request['year'])) {
            $invoices->where('year', $request['year']);
        }

        $invoices = $invoices->orderByDesc('updated_at');

        $invoices = $invoices->paginate()->withQueryString();

        $users = User::where('role', User::ROLE_USER)->pluck('email')->toArray();



        return [
            'oldInput' => $request,
            'invoices' => $invoices,
            'users' => $users,
        ];
    }

    public function detail($id)
    {
        $invoice = UserInvoice::with('user')->where('user_id', Auth::id())->findOrFail($id);

        return [
            'invoice' => $invoice
        ];
    }
}
