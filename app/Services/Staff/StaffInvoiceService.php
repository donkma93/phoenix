<?php

namespace App\Services\Staff;

use App\Services\StaffBaseServiceInterface;
use App\Models\User;
use App\Models\UserInvoice;

class StaffInvoiceService extends StaffBaseService implements StaffBaseServiceInterface
{
    function list($request)
    {
        $invoices = UserInvoice::with('user')->has('user');

        if(isset($request['email'])) {
            $invoices->whereHas('user', function ($query) use ($request) {
                $query->where('email', 'like', '%'.$request['email'].'%');
            });
        }

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
        $invoice = UserInvoice::with('user')->findOrFail($id);

        return [
            'invoice' => $invoice
        ];
    }
}
