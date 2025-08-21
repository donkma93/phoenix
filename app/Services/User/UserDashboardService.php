<?php

namespace App\Services\User;

use App\Exports\User\OrdersExport;
use App\Models\Order;
use App\Models\Package;
use App\Models\PackageHistory;
use App\Models\UserRequest;
use App\Models\Inventory;
use App\Services\UserBaseServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;

class UserDashboardService extends UserBaseService implements UserBaseServiceInterface
{
    const STATES = [
        "AL",
        "AK",
        "AZ",
        "AR",
        "CA",
        "CO",
        "CT",
        "DE",
        "FL",
        "GA",
        "HI",
        "ID",
        "IL",
        "IN",
        "IA",
        "KS",
        "KY",
        "LA",
        "ME",
        "MD",
        "MA",
        "MI",
        "MN",
        "MS",
        "MO",
        "MT",
        "NE",
        "NV",
        "NH",
        "NJ",
        "NM",
        "NY",
        "NC",
        "ND",
        "OH",
        "OK",
        "OR",
        "PA",
        "RI",
        "SC",
        "SD",
        "TN",
        "TX",
        "UT",
        "VT",
        "VA",
        "WA",
        "WV",
        "WI",
        "WY",
    ];

    public function index()
    {
        $now = Carbon::now();
        $thisMonth = $now->month;
        $thisYear = $now->year;

        $userId = Auth::id();

        $packageStoredCount = Package::where('user_id', Auth::id())
            ->where('status', Package::STATUS_STORED)
            ->count();

        $packageStoredCurrentMonthCount = Package::where('user_id', Auth::id())
            ->where('status', Package::STATUS_STORED)
            ->whereHas('histories', function ($query) use ($thisYear, $thisMonth) {
                $query->where('status', Package::STATUS_STORED)
                    ->whereYear('updated_at', '=', $thisYear)
                    ->whereMonth('updated_at', '=', $thisMonth);
            })
            ->count();

        $packageHistoryCount = PackageHistory::select('status', DB::raw('count(*) as total'))
            ->whereHas('package', function ($query) {
                $query->where('user_id',  Auth::id());
            })
            ->whereIn('status', [Package::STATUS_INBOUND, Package::STATUS_OUTBOUND])
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item['status'] => $item['total']];
            })
            ->all();

        $packageHistoryCurrentMonthCount = PackageHistory::select('status', DB::raw('count(*) as total'))
            ->whereHas('package', function ($query) {
                $query->where('user_id',  Auth::id());
            })
            ->whereIn('status', [Package::STATUS_INBOUND, Package::STATUS_OUTBOUND])
            ->whereYear('updated_at', '=', $thisYear)
            ->whereMonth('updated_at', '=', $thisMonth)
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item['status'] => $item['total']];
            })
            ->all();

        $requestCount = UserRequest::select('status', DB::raw('count(*) as total'))
            ->where('user_id', Auth::id())
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item['status'] => $item['total']];
            })
            ->all();

        $requestThisMonthCount = UserRequest::select('status', DB::raw('count(*) as total'))
            ->where('user_id', Auth::id())
            ->whereYear('created_at', '=', $thisYear)
            ->whereMonth('created_at', '=', $thisMonth)
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item['status'] => $item['total']];
            })
            ->all();

        $orders = self::getCompletedOrders($userId, ['addressTo']);
        $states = self::convertState($orders);

        uasort($states, function($first, $second){
            return $first < $second;
        });

        $remind = Inventory::whereHas('product', function ($query) {
            $query->where('user_id', Auth::id());
        })->whereRaw('(available + incoming) <= min')->where('is_remind', true)->paginate()->withQueryString();
        
        return [
            'packageStoredCount' => $packageStoredCount,
            'packageStoredCurrentMonthCount' => $packageStoredCurrentMonthCount,
            'packageHistoryCount' => $packageHistoryCount,
            'packageHistoryCurrentMonthCount' => $packageHistoryCurrentMonthCount,
            'requestItems' => $requestCount,
            'requestCurrentItems' => $requestThisMonthCount,

            'orders' => $orders,
            'states' => $states,
            'remind' => $remind,
        ];
    }

    public function convertState($orders) {
        $states = [];

        foreach ($orders as $order) {
            if (isset($order->addressTo) && self::isUs($order->addressTo->country)) {
                $state = strtoupper($order->addressTo->state);
                if (isset($states[$state])) {
                    $states[$state] += 1;
                } else {
                    $states[$state] = 1;
                }

                continue;
            }

            $states['OTHER'] = isset($states['OTHER']) ? $states['OTHER'] + 1 : 1;
        }

        return $states;
    }

    public function isUs($name) {
        return !strcasecmp($name, "US") || !strcasecmp($name, "United States");
    }

    public function getCompletedOrders($userId, $preload) {
        return Order::with($preload)
            ->where('user_id', $userId)
            ->where('status', Order::STATUS_DONE)
            ->get();
    }

    public function exportOrderCsv() {
        $userId = Auth::id();
        $orders = self::getCompletedOrders($userId, ['orderProducts.product', 'addressTo']);

        $export = new OrdersExport($orders);
        return Excel::download($export, 'orders.csv');
    }

    public function exportOrderByCondition($params) {
        $userId = Auth::id();
        $preload = ['orderProducts.product', 'addressTo'];

        $builder = Order::with($preload)
            ->where('user_id', $userId)
            ->whereDate('created_at', '>=', $params['startDate'])
            ->whereDate('created_at', '<=', $params['toDate']);

        if (isset($params['status'])) {
            $builder = $builder->where('status', $params['status']);
        }

        if (isset($params['payment'])) {
            $builder = $builder->where('payment', $params['payment']);
        }

        if (isset($params['fulfillment'])) {
            $builder = $builder->where('fulfillment', $params['fulfillment']);
        }

        $orders = $builder->orderBy('updated_at', 'DESC')
            ->get();

        $export = new OrdersExport($orders);
        return Excel::download($export, 'orders.csv');
    }
}
