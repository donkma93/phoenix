<?php

namespace App\Services\Admin;

use App\Services\AdminBaseServiceInterface;
use App\Models\Warehouse;
use App\Models\WarehouseArea;
use App\Models\Package;
use App\Models\PackageGroup;
use App\Models\StoreFulfill;

class AdminWarehouseAreaService extends AdminBaseService implements AdminBaseServiceInterface
{
    public function list($input)
    {
        $warehouseAreas = WarehouseArea::orderByDesc('created_at');

        if(isset($input['onlyDeleted'])) {
            if($input['onlyDeleted'] == 1) {
                $warehouseAreas = WarehouseArea::onlyTrashed();
            } else {
                $warehouseAreas = WarehouseArea::withTrashed();
            }
        }

        if(isset($input['name'])) {
            $warehouseAreas = $warehouseAreas->where('name', 'like', '%'.$input['name'].'%');
        }

        if(isset($input['warehouse'])) {
            $warehouseAreas = $warehouseAreas->whereHas('warehouse', function($warehouse) use ($input) {
                $warehouse->where('name', 'like' , '%'.$input['warehouse'].'%');
            });
        }

        $warehouseAreas = $warehouseAreas->paginate()->withQueryString();

        $warehouses = Warehouse::withTrashed()->pluck('name')->toArray();

        $areas = WarehouseArea::withTrashed()->select('name', 'barcode')->get()->toArray();

        $areaList = WarehouseArea::withTrashed()->pluck('name')->toArray();

        return [
            'warehouseAreas' => $warehouseAreas,
            'warehouses' => $warehouses,
            'areas' => $areas,
            'areaList' => $areaList,
            'oldInput' => $input,
        ];
    }

    public function detail($id)
    {
        $areaInfo = WarehouseArea::withTrashed()->with(['warehouse' => function ($warehouse) {
            $warehouse->withTrashed();
         }, 'packages' => function ($packages) {
            $packages->withTrashed();
         }])->withCount('packages')->find($id);

        return [
            'areaInfo' => $areaInfo
        ];
    }

    public function delete($input)
    {
        $area = WarehouseArea::withTrashed()->find($input['id']);

        if(isset($area['deleted_at'])) {
            $area->restore();
        } else {
            $area->delete();
        }
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

    public function create($input)
    {   
        $warehouse = Warehouse::where('name', $input['warehouse'])->first();
        
        WarehouseArea::create([
            'name' => $input['name'],
            'warehouse_id' => $warehouse['id'],
            'barcode' => $input['barcode']
        ]);
    }

    function generateBarcodeNumber()
    {
        $barcode = uniqid();
        
        if ($this->barcodeNumberExists($barcode)) {
            return $this->generateBarcodeNumber();
        }

        return $barcode;
    }

    function barcodeNumberExists($barcode)
    {
        return WarehouseArea::where('barcode', $barcode)->exists() || 
            Package::where('barcode', $barcode)->exists() || 
            PackageGroup::where('barcode', $barcode)->exists() ||
            StoreFulfill::where('code', $barcode)->exists();
    }
}
