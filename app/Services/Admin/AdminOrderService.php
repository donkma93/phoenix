<?php

namespace App\Services\Admin;

use App\Models\Order;
use App\Models\User;
use App\Services\AdminBaseServiceInterface;

class AdminOrderService extends AdminBaseService implements AdminBaseServiceInterface
{
    public function list($request)
    {
        $orders = Order::with([
            'orderProducts.product', 'orderPackage',
            'orderTransaction', 'addressFrom', 'addressTo', 'orderRates'
        ])->has('user');

        if(isset($request['email'])) {
            $orders->whereHas('user', function ($query) use ($request) {
                $query->where('email', 'like', '%'.$request['email'].'%');
            });
        }

        if (isset($request['status'])) {
            $orders = $orders->where('status', $request['status']);
        }

        if (isset($request['state'])) {
            $orders = $orders->where('state', $request['state']);
        }

        if (isset($request['payment'])) {
            $orders = $orders->where('payment', $request['payment']);
        }

        if (isset($request['picking_status'])) {
            $orders = $orders->where('picking_status', $request['picking_status']);
        }
        
        if (isset($request['fulfillment'])) {
            $orders = $orders->where('fulfillment', $request['fulfillment']);
        }

        $orders = $orders->orderByDesc('updated_at')
            ->paginate();

        $emails = User::where('role', User::ROLE_USER)->pluck('email')->toArray();
        $users = User::where('role', User::ROLE_USER)->get();

        return [
            'orders' => $orders,
            'oldInput' => $request,
            'emails' => $emails,
            'users' => $users
        ];
    }

    public function detail($id)
    {
        $order = Order::with([
            'orderProducts.product.category', 'orderPackage', 'user',
            'addressFrom', 'addressTo', 'orderTransaction.orderRate'
        ])->has('user')
            ->findOrFail($id);

        return [
            'order' => $order
        ];
    }
}
