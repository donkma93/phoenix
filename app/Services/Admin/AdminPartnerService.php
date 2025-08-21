<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use App\Models\Partner;
use App\Models\Warehouse;
use App\Services\AdminBaseServiceInterface;
use Exception;

class AdminPartnerService extends AdminBaseService implements AdminBaseServiceInterface
{
    function list($request)
    {
        $partners = Partner::whereNotNull('id');

        if(isset($request['partner_code'])) {
            
            $partners->where('partner_code', 'like', '%'.$request['partner_code'].'%');
        }

        if(isset($request['phone'])) {
            $partners->where('phone', $request['phone']);
        }

        if(isset($request['partner_name'])) {
            $partners->where('partner_name', $request['partner_name']);
        }

        $partners = $partners->orderByDesc('updated_at');
        $partners = $partners->paginate()->withQueryString();

        $warehouses = Warehouse::pluck('name')->toArray();

        return [
            'oldInput' => $request,
            'partners' => $partners,
            'warehouses' => $warehouses,
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
