<?php

namespace App\Services\Staff;

use App\Models\Order;
use App\Models\OrderJourney;
use Illuminate\Support\Facades\DB;
use App\Models\Partner;
use App\Models\Warehouse;
use App\Services\StaffBaseServiceInterface;
use Exception;

class StaffOrderTrackingService extends StaffBaseService implements StaffBaseServiceInterface
{
    function list($request)
    {
        $orders = OrderJourney::whereNotNull('id');

        if(isset($request['partner_code'])) {
            $orders->where('partner_code', 'like', '%'.$request['partner_code'].'%');
        }

        if(isset($request['phone'])) {
            $orders->where('phone', $request['phone']);
        }

        if(isset($request['partner_name'])) {
            $orders->where('partner_name', $request['partner_name']);
        }

        $ordersFull = Order::with([ 'orderPackage', 'orderTransaction', 'addressFrom', 'addressTo', 'orderRates'])
        ->has('user')->whereIn('id', [4902,4900])->orderByDesc('updated_at')->paginate();

        return [
            'oldInput' => $request,
            'orders' => $ordersFull
        ];
    }

    function detail($id)
    {
        $partner = Partner::withTrashed()->find($id);

        $warehouses = Warehouse::pluck('name')->toArray();
        
        return [
            'partner' => $partner,
            'warehouses' => $warehouses
        ];
    }

    function new()
    {
        $warehouses = Warehouse::pluck('name')->toArray();
        
        return [
            'warehouses' => $warehouses,
        ];
    }

    function create($request)
    {
        DB::beginTransaction();

        try {

            $partner = Partner::create([
                'partner_code' => $request['partner_code'],
                'partner_name' => $request['partner_name'],
                'address' => $request['address'],
                'phone' => $request['phone'],
            ]);
            DB::commit();

            return $partner['id'];

        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    function update($request)
    {
        $partner = Partner::find($request['id']);
        $partner->partner_name = $request['partner_name'];
        $partner->address = $request['address'];
        $partner->phone = $request['phone'];
        $partner->save();
    }

    function delete($request)
    {
        $partner = Partner::find($request['id']);

        
        if(isset($partner['deleted_at'])) {
            $partner->restore();
        } else {
            $partner->delete();
        }
    }
}
