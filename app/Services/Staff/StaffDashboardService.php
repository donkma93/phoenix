<?php

namespace App\Services\Staff;

use Illuminate\Support\Facades\DB;
use App\Services\StaffBaseServiceInterface;
use App\Models\UserRequest;
use App\Models\OrderProduct;
use App\Models\Order;
use App\Models\ToteHistory;
use Carbon\Carbon;

class StaffDashboardService extends StaffBaseService implements StaffBaseServiceInterface
{
    function getAllUserRequest() {
        $usersRequest = UserRequest::select('m_request_type_id', DB::raw('count(*) as total'))
            ->whereHas('user', function($users) {
                $users->where('role', config('auth.role.user'));
            })
            ->whereIn('m_request_type_id', [1, 3, 4])
            ->groupBy('m_request_type_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item['m_request_type_id'] => $item['total']];
            })
        // Explicit INNER JOIN instead of correlated whereHas EXISTS
        $usersRequest = DB::table('user_requests as ur')
            ->join('users as u', 'u.id', '=', 'ur.user_id')
            ->select('ur.m_request_type_id', DB::raw('count(*) as total'))
            ->whereNull('ur.deleted_at')
            ->whereNull('u.deleted_at')
            ->where('u.role', config('auth.role.user'))
            ->whereIn('ur.m_request_type_id', [1, 3, 4])
            ->groupBy('ur.m_request_type_id')
            ->pluck('total', 'm_request_type_id')
            ->all();

        $orders = Order::select('fulfillment', DB::raw('count(*) as total'))
        // Covering index-only GROUP BY using idx_orders_deleted_fulfillment
        $orders = DB::table('orders')
            ->select('fulfillment', DB::raw('count(*) as total'))
            ->whereNull('deleted_at')
            ->groupBy('fulfillment')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item['fulfillment'] => $item['total']];
            })
            ->pluck('total', 'fulfillment')
            ->all();

        $itemsPickToday = OrderProduct::whereHas('order', function($orders) {
            $orders->where('fulfillment', Order::PENDING);
        })->sum('quantity');
        // Drive from indexed orders (deleted_at, fulfillment) instead of full-scanning 132k order_product rows
        $itemsPickToday = (int) DB::table('orders as o')
            ->join('order_product as op', function ($join) {
                $join->on('op.order_id', '=', 'o.id')
                    ->whereNull('op.deleted_at');
            })
            ->whereNull('o.deleted_at')
            ->where('o.fulfillment', Order::PENDING)
            ->sum('op.quantity');

        $today = Carbon::now();

        $itemPicked = ToteHistory::whereDate('created_at', $today)->sum('quantity');
        // Index-friendly date range instead of DATE(created_at) = ?
        $itemPicked = (int) ToteHistory::where('created_at', '>=', $today->copy()->startOfDay())
            ->where('created_at', '<=', $today->copy()->endOfDay())
            ->sum('quantity');

        $itemsDueToday = $itemsPickToday - $itemPicked;

        $requests = [
            [
                'm_request_type_id' => 1,
                'm_request_type_name' => 'relabel',
                'statusName' => 'Relabel',
                'total' => 0,
                'icon' => 'nc-icon nc-tag-content',
                'color' => 'text-success'
            ],
            [
                'm_request_type_id' => 3,
                'm_request_type_name' => 'outbound',
                'statusName' => 'Outbound',
                'total' => 0,
                'icon' => 'nc-icon nc-cart-simple',
                'color' => 'text-danger'
            ],
            [
                'm_request_type_id' => 4,
                'm_request_type_name' => 'add package',
                'statusName' => 'Add package',
                'total' =>  0,
                'icon' => 'nc-icon nc-send',
                'color' => 'text-info'
            ]
        ];

        $orderElements = [
            [
                'fulfillment' => Order::READY_TO_SHIP,
                'fulfillmentName' => Order::$fulfillName[Order::READY_TO_SHIP],
                'total' => 0,
                'icon' => 'nc-icon nc-app',
                'color' => 'text-warning'
            ],
            [
                'fulfillment' => Order::DUE_TODAY,
                'fulfillmentName' => Order::$fulfillName[Order::DUE_TODAY],
                'total' => 0,
                'icon' => 'nc-icon nc-bag-16',
                'color' => 'text-primary'
            ],
            [
                'fulfillment' => Order::SHIP_TODAY,
                'fulfillmentName' => Order::$fulfillName[Order::SHIP_TODAY],
                'total' => 0,
                'icon' => 'nc-icon nc-delivery-fast',
                'color' => 'text-danger'
            ]
        ];

        $requestTotal = [];
        foreach($requests as $request) {
            $request['total'] = $usersRequest[$request['m_request_type_id']] ?? 0;
            array_push($requestTotal, $request);
        }

        $orderTotal = [];
        foreach($orderElements as $order) {
            $order['total'] = $orders[$order['fulfillment']] ?? 0;
            array_push($orderTotal, $order);
        }

        return [
            'itemsDueToday' => $itemsDueToday,
            'itemsPickToday' => $itemsPickToday,
            'requests' => $requestTotal,
            'orders' => $orderTotal
        ];
    }
}
