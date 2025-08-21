<?php

namespace App\Services\Admin;

use App\Services\AdminBaseServiceInterface;
use App\Models\Package;
use App\Models\UserRequest;
use App\Models\User;
use App\Models\Order;
use App\Models\ToteHistory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardService extends AdminBaseService implements AdminBaseServiceInterface
{
    public function index()
    {
        $packageTotal = Package::count();
        $packageCount = Package::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item['status'] => $item['total']];
            })
            ->all();

        $requestTotal = UserRequest::count();
        $requestCount = UserRequest::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item['status'] => $item['total']];
            })
            ->all();

        $userTotal = User::where('role', '<>' , User::ROLE_ADMIN)->count();
        $userCount = User::select('role', DB::raw('count(*) as total'))
        ->where('role', '<>' , User::ROLE_ADMIN)
        ->groupBy('role')
        ->get()
        ->mapWithKeys(function ($item) {
            return [$item['role'] => $item['total']];
        })
        ->all();

        $readyToShipTotal = Order::select('picking_status', DB::raw('count(*) as total'))
        ->where('fulfillment' , Order::READY_TO_SHIP)
        ->groupBy('picking_status')
        ->get()
        ->mapWithKeys(function ($item) {
            return [$item['picking_status'] => $item['total']];
        })
        ->all();

        $dueTodayTotal = Order::select('picking_status', DB::raw('count(*) as total'))
        ->where('fulfillment' , Order::DUE_TODAY)
        ->groupBy('picking_status')
        ->get()
        ->mapWithKeys(function ($item) {
            return [$item['picking_status'] => $item['total']];
        })
        ->all();

        return [
            'packageItems' => $packageCount,
            'packageTotal' => $packageTotal,
            'requestItems' => $requestCount,
            'requestTotal' => $requestTotal,
            'userTotal' => $userTotal,
            'userCount' => $userCount,
            'readyToShipTotal' => $readyToShipTotal,
            'dueTodayTotal' => $dueTodayTotal,
        ];
    }

    public function board()
    {
        $readyToShipTotal = Order::select('picking_status', DB::raw('count(*) as total'))
        ->where('fulfillment' , Order::READY_TO_SHIP)
        ->groupBy('picking_status')
        ->get()
        ->mapWithKeys(function ($item) {
            return [$item['picking_status'] => $item['total']];
        })
        ->all();

        $dueTodayTotal = Order::select('picking_status', DB::raw('count(*) as total'))
        ->where('fulfillment' , Order::DUE_TODAY)
        ->groupBy('picking_status')
        ->get()
        ->mapWithKeys(function ($item) {
            return [$item['picking_status'] => $item['total']];
        })
        ->all();

        $pickerOverview = $this->getOrderHandlerStaff(0);

        $packerOverview = $this->getOrderHandlerStaff(1);

        return [
            'readyToShipTotal' => $readyToShipTotal,
            'dueTodayTotal' => $dueTodayTotal,
            'pickerOverview' => $pickerOverview,
            'packerOverview' => $packerOverview,
        ];
    }

    public function liveShipping($request) {
        $today = Carbon::now();
        $pickerData =  ToteHistory::select(DB::raw('picker_id, users.email,HOUR(pick_at) as hour, sum(quantity) as count'))
            ->leftJoin('users', 'users.id', '=', 'tote_histories.picker_id')
            ->leftJoin('orders', 'tote_histories.order_id', '=', 'orders.id')
            ->whereNotNull('picker_id')
            ->whereNull('packer_id')
            ->whereDate('tote_histories.created_at', $today)
            ->groupBy('picker_id')
            ->groupBy('hour')
            ->orderBy('picker_id')
            ->orderBy('hour')
            ->get();
        $pickerEmails = ToteHistory::whereNotNull('picker_id')->has('picker')
        ->whereNull('packer_id')
        ->whereDate('created_at', $today)
        ->groupBy('picker_id')
        ->orderBy('picker_id')
        ->get();

        return [ 
            'pickerData' => $pickerData,
            'pickerEmails' => $pickerEmails
        ];
    }

    public function export($type) {
        return $this->getOrderHandlerStaff($type);
    }

    function getOrderHandlerStaff($type) {
        if($type == 0) {
            return ToteHistory::whereNull('tote_histories.packer_id')
            ->leftJoin(DB::raw('(select tote_histories.picker_id, count(orders.picking_status) as number_order from tote_histories 
                left join orders on tote_histories.order_id = orders.id
                where tote_histories.packer_id is NULL 
                    AND orders.picking_status = '. Order::PICKING_PENDING .'
                    AND orders.fulfillment = '. Order::READY_TO_SHIP .'
                group by picker_id) as a'), 'a.picker_id', '=', 'tote_histories.picker_id')
            ->leftJoin(DB::raw('(select tote_histories.picker_id, count(orders.picking_status) as number_order from tote_histories 
                left join orders on tote_histories.order_id = orders.id
                where tote_histories.packer_id is NULL 
                    AND orders.picking_status = '. Order::PICKING_INTOTE .'
                    AND orders.fulfillment = '. Order::READY_TO_SHIP .'
                group by picker_id) as b'), 'b.picker_id', '=', 'tote_histories.picker_id')
            ->leftJoin(DB::raw('(select tote_histories.picker_id, count(orders.picking_status) as number_order from tote_histories 
                left join orders on tote_histories.order_id = orders.id
                where tote_histories.packer_id is NULL 
                    AND orders.picking_status = '. Order::PICKING_FULFILLED .'
                    AND orders.fulfillment = '. Order::READY_TO_SHIP .'
                group by picker_id) as c'), 'c.picker_id', '=', 'tote_histories.picker_id')
            ->leftJoin(DB::raw('(select tote_histories.picker_id, count(orders.picking_status) as number_order from tote_histories 
                left join orders on tote_histories.order_id = orders.id
                where tote_histories.packer_id is NULL 
                    AND orders.picking_status = '. Order::PICKING_PENDING .'
                    AND orders.fulfillment = '. Order::DUE_TODAY .'
                group by picker_id) as d'), 'd.picker_id', '=', 'tote_histories.picker_id')
            ->leftJoin(DB::raw('(select tote_histories.picker_id, count(orders.picking_status) as number_order from tote_histories 
                left join orders on tote_histories.order_id = orders.id
                where tote_histories.packer_id is NULL 
                    AND orders.picking_status = '. Order::PICKING_INTOTE .'
                    AND orders.fulfillment = '. Order::DUE_TODAY .'
                group by picker_id) as e'), 'e.picker_id', '=', 'tote_histories.picker_id')
            ->leftJoin(DB::raw('(select tote_histories.picker_id, count(orders.picking_status) as number_order from tote_histories 
                left join orders on tote_histories.order_id = orders.id
                where tote_histories.packer_id is NULL 
                    AND orders.picking_status = '. Order::PICKING_FULFILLED .'
                    AND orders.fulfillment = '. Order::DUE_TODAY .'
                group by picker_id) as f'), 'f.picker_id', '=', 'tote_histories.picker_id')
            ->leftJoin('orders', 'tote_histories.order_id', '=', 'orders.id')
            ->leftJoin('users', 'users.id', '=', 'tote_histories.picker_id')
            ->having('a.number_order', '<>', 'NULL')
            ->having('b.number_order', '<>', 'NULL')
            ->having('c.number_order', '<>', 'NULL')
            ->having('d.number_order', '<>', 'NULL')
            ->having('e.number_order', '<>', 'NULL')
            ->having('f.number_order', '<>', 'NULL')
            ->select('a.number_order as rtsPending', 
            'b.number_order as rtsInTote', 
            'c.number_order as rtsFulfill',
            'd.number_order as dtPending', 
            'e.number_order as dtInTote', 
            'f.number_order as dtFulfill',
            'users.email')->distinct()->withTrashed()->get();
        } else {
            return ToteHistory::whereNotNull('tote_histories.packer_id')
            ->leftJoin(DB::raw('(select tote_histories.picker_id, count(orders.picking_status) as number_order from tote_histories 
                left join orders on tote_histories.order_id = orders.id
                where tote_histories.packer_id is NOT NULL 
                    AND orders.picking_status = '. Order::PICKING_PENDING .'
                    AND orders.fulfillment = '. Order::READY_TO_SHIP .'
                group by picker_id) as a'), 'a.picker_id', '=', 'tote_histories.picker_id')
            ->leftJoin(DB::raw('(select tote_histories.picker_id, count(orders.picking_status) as number_order from tote_histories 
                left join orders on tote_histories.order_id = orders.id
                where tote_histories.packer_id is NOT NULL 
                    AND orders.picking_status = '. Order::PICKING_INTOTE .'
                    AND orders.fulfillment = '. Order::READY_TO_SHIP .'
                group by picker_id) as b'), 'b.picker_id', '=', 'tote_histories.picker_id')
            ->leftJoin(DB::raw('(select tote_histories.picker_id, count(orders.picking_status) as number_order from tote_histories 
                left join orders on tote_histories.order_id = orders.id
                where tote_histories.packer_id is NOT NULL 
                    AND orders.picking_status = '. Order::PICKING_FULFILLED .'
                    AND orders.fulfillment = '. Order::READY_TO_SHIP .'
                group by picker_id) as c'), 'c.picker_id', '=', 'tote_histories.picker_id')
            ->leftJoin(DB::raw('(select tote_histories.picker_id, count(orders.picking_status) as number_order from tote_histories 
                left join orders on tote_histories.order_id = orders.id
                where tote_histories.packer_id is NOT NULL 
                    AND orders.picking_status = '. Order::PICKING_PENDING .'
                    AND orders.fulfillment = '. Order::DUE_TODAY .'
                group by picker_id) as d'), 'd.picker_id', '=', 'tote_histories.picker_id')
            ->leftJoin(DB::raw('(select tote_histories.picker_id, count(orders.picking_status) as number_order from tote_histories 
                left join orders on tote_histories.order_id = orders.id
                where tote_histories.packer_id is NOT NULL 
                    AND orders.picking_status = '. Order::PICKING_INTOTE .'
                    AND orders.fulfillment = '. Order::DUE_TODAY .'
                group by picker_id) as e'), 'e.picker_id', '=', 'tote_histories.picker_id')
            ->leftJoin(DB::raw('(select tote_histories.picker_id, count(orders.picking_status) as number_order from tote_histories 
                left join orders on tote_histories.order_id = orders.id
                where tote_histories.packer_id is NOT NULL 
                    AND orders.picking_status = '. Order::PICKING_FULFILLED .'
                    AND orders.fulfillment = '. Order::DUE_TODAY .'
                group by picker_id) as f'), 'f.picker_id', '=', 'tote_histories.picker_id')
            ->leftJoin('orders', 'tote_histories.order_id', '=', 'orders.id')
            ->leftJoin('users', 'users.id', '=', 'tote_histories.picker_id')
            ->having('a.number_order', '<>', 'NULL')
            ->having('b.number_order', '<>', 'NULL')
            ->having('c.number_order', '<>', 'NULL')
            ->having('d.number_order', '<>', 'NULL')
            ->having('e.number_order', '<>', 'NULL')
            ->having('f.number_order', '<>', 'NULL')
            ->select('a.number_order as rtsPending', 
            'b.number_order as rtsInTote', 
            'c.number_order as rtsFulfill',
            'd.number_order as dtPending', 
            'e.number_order as dtInTote', 
            'f.number_order as dtFulfill',
            'users.email')->distinct()->withTrashed()->get();
        }
    }
}
