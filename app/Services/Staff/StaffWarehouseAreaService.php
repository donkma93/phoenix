<?php

namespace App\Services\Staff;

use App\Services\StaffBaseServiceInterface;
use App\Models\Warehouse;
use App\Models\WarehouseArea;

class StaffWarehouseAreaService extends StaffBaseService implements StaffBaseServiceInterface
{
    public function list($input)
    {
        $warehouseAreas = WarehouseArea::orderByDesc('created_at');

        if(isset($input['name'])) {
            $warehouseAreas = $warehouseAreas->where('name', 'like', '%'.$input['name'].'%');
        }

        if(isset($input['warehouse'])) {
            $warehouseAreas = $warehouseAreas->whereHas('warehouse', function($warehouse) use ($input) {
                $warehouse->where('name', 'like' , '%'.$input['warehouse'].'%');
            });
        }

        $warehouseAreas = $warehouseAreas->paginate()->withQueryString();

        $warehouses = Warehouse::pluck('name')->toArray();

        $areaList = WarehouseArea::pluck('name')->toArray();

        $areas = WarehouseArea::select('name', 'barcode')->get()->toArray();

        return [
            'warehouseAreas' => $warehouseAreas,
            'areas' => $areas,
            'warehouses' => $warehouses,
            'areaList' => $areaList,
            'oldInput' => $input,
        ];
    }

    public function detail($id)
    {
        $areaInfo = WarehouseArea::with(['warehouse', 'packages' => function ($packages) {
            $packages->withTrashed();
         }])->has('warehouse')->withCount('packages')->find($id);

        return [
            'areaInfo' => $areaInfo
        ];
    }

    public function getWarehouses()
    {
        $warehouses = Warehouse::pluck('name')->toArray();;

        return [
            'warehouses' => $warehouses
        ];
    }

    public function updateArea($input) 
    {   
        $area = WarehouseArea::find($input['id']);
        
        $area['is_full'] = !$area['is_full'];
        
        $area->save();
    }
}
