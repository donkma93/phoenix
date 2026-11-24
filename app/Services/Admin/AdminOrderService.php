<?php

namespace App\Services\Admin;

use App\Models\Order;
use App\Models\User;
use App\Services\AdminBaseServiceInterface;

class AdminOrderService extends AdminBaseService implements AdminBaseServiceInterface
{
    public function list($request)
    {
        // Keep list lean: skip orderRates (heavy JSON table) and select only needed order columns
        $orders = Order::with([
            'orderProducts.product', 'orderPackage',
            'orderTransaction', 'addressFrom', 'addressTo',
        ])
            ->select([
                'id', 'order_code', 'order_number', 'status', 'payment', 'fulfillment',
                'picking_status', 'state', 'user_id', 'partner_code',
                'order_address_from_id', 'order_address_to_id',
                'created_at', 'updated_at',
            ])
            ->has('user');

        if (!empty($request['email'])) {
            $matchingUserIds = User::where('email', 'like', '%' . $request['email'] . '%')->pluck('id');
            $orders->whereIn('user_id', $matchingUserIds);
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

        // Single query on users (derive emails in PHP instead of querying users twice)
        $users = User::select('id', 'email', 'partner_code', 'role')
            ->where('role', User::ROLE_USER)
            ->get();
        $emails = $users->pluck('email')->toArray();

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
