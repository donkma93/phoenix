<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use App\Models\Tote;
use App\Models\ToteHistory;
use App\Models\WarehouseArea;
use App\Services\AdminBaseServiceInterface;
use Exception;

class AdminToteService extends AdminBaseService implements AdminBaseServiceInterface
{
    function list($request)
    {
        $totes = Tote::with(['warehouseArea' => function ($area) {
            $area->withTrashed();
         }])->has('warehouseArea');

        if(isset($request['name'])) {
            $totes->where('name', 'like', '%'.$request['name'].'%');
        }

        if(isset($request['status'])) {
            $totes->where('status', $request['status']);
        }

        if(isset($request['barcode'])) {
            $totes->where('barcode', $request['barcode']);
        }
        
        if(isset($request['warehouse'])) {
            $packages->whereHas('warehouseArea', function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request['warehouse'].'%');
            });
        }

        if(isset($request['onlyDeleted'])) {
            if($request['onlyDeleted'] == 1) {
                $totes = $totes->onlyTrashed();
            }
        } else {
            $totes = $totes->withTrashed();
        }

        $totes = $totes->orderByDesc('updated_at');

        $totes = $totes->paginate()->withQueryString();

        $warehouseAreas = WarehouseArea::pluck('name')->toArray();

        $areas = WarehouseArea::select('name', 'barcode')->get()->toArray();

        return [
            'oldInput' => $request,
            'totes' => $totes,
            'warehouses' => $warehouseAreas,
            'areas' => $areas,
        ];
    }

    function detail($id)
    {
        $tote = Tote::withTrashed()->find($id);

        $warehouseAreas = WarehouseArea::pluck('name')->toArray();
        
        $areas = WarehouseArea::select('name', 'barcode')->get()->toArray();
        
        return [
            'tote' => $tote,
            'warehouses' => $warehouseAreas,
            'areas' => $areas,
        ];
    }

    function new()
    {
        $warehouseAreas = WarehouseArea::pluck('name')->toArray();
        
        $areas = WarehouseArea::select('name', 'barcode')->get()->toArray();


        return [
            'warehouses' => $warehouseAreas,
            'areas' => $areas,
        ];
    }

    function create($request)
    {
        DB::beginTransaction();

        try {
            $warehouse = WarehouseArea::where('name', $request['warehouse'])->first();

            $tote = Tote::create([
                'name' => $request['name'],
                'barcode' => $this->generateBarcodeNumber([], $request['name'], null),
                'warehouse_area_id' => $warehouse->id,
                'status' => Tote::STATUS_IN_USE
            ]);
            DB::commit();

            return $tote['id'];

        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    function update($request)
    {
        $area = WarehouseArea::where('name' , $request['warehouse'])->first();

        $tote = Tote::find($request['id']);

        $tote->status = $request['status'];
        $tote->warehouse_area_id = $area->id;

        $tote->save();
    }

    function delete($request)
    {
        $tote = Tote::find($request['id']);

        
        if(isset($tote['deleted_at'])) {
            $tote->restore();
        } else {
            $tote->delete();
        }
    }
}
