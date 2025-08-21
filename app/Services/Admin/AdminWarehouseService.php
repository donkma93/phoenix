<?php

namespace App\Services\Admin;

use App\Services\AdminBaseServiceInterface;
use App\Models\Warehouse;
use App\Models\WarehouseArea;
use App\Models\WarehouseUnitPrice;
use App\Models\MUnit;

class AdminWarehouseService extends AdminBaseService implements AdminBaseServiceInterface
{
    public function list($input)
    {
        $warehouses = Warehouse::orderByDesc('created_at');

        if(isset($input['onlyDeleted'])) {
            if($input['onlyDeleted'] == 1) {
                $warehouses = Warehouse::onlyTrashed();
            } else {
                $warehouses = Warehouse::withTrashed();
            }
        }

        if(isset($input['name'])) {
            $warehouses = $warehouses->where('name', 'like', '%'.$input['name'].'%');
        }

        $warehouses = $warehouses->paginate()->withQueryString();

        $warehouseList = Warehouse::withTrashed()->pluck('name')->toArray();

        return [
            'warehouses' => $warehouses,
            'oldInput' => $input,
            'warehouseList' => $warehouseList
        ];
    }

    public function detail($id)
    {
        $warehouse = Warehouse::withTrashed()->find($id);
        
        $totalArea = WarehouseArea::where('warehouse_id', $id)->count();

        return [
            'warehouse' => $warehouse,
            'totalArea' => $totalArea,
        ];
    }

    public function create($input)
    {
        Warehouse::create([
            'name' => $input['name'],
            'address' => $input['address']
        ]);
    }

    public function delete($input)
    {
        $warehouse = Warehouse::withTrashed()->find($input['id']);

        if(isset($warehouse['deleted_at'])) {
            $warehouse->restore();
        } else {
            $warehouse->delete();
        }
    }

    public function addUnitPrice($input)
    {
        WarehouseUnitPrice::create([
            'name' => $input['name'],
            'price' => doubleval($input['price']),
            'm_unit_id' => $input['unit_id'],
            'warehouse_id' => $input['warehouse_id'],
        ]);
    }

    public function deleteUnitPrice($input)
    {
        WarehouseUnitPrice::withTrashed()->find($input['id'])->delete();
    }
}
