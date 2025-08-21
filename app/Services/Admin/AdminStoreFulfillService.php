<?php

namespace App\Services\Admin;

use App\Services\AdminBaseServiceInterface;
use App\Models\WarehouseArea;
use App\Models\Package;
use App\Models\PackageGroup;
use App\Models\StoreFulfill;

class AdminStoreFulfillService extends AdminBaseService implements AdminBaseServiceInterface
{
    public function list($input)
    {
        $stores = StoreFulfill::orderByDesc('created_at');

        if(isset($input['onlyDeleted'])) {
            if($input['onlyDeleted'] == 1) {
                $stores = StoreFulfill::onlyTrashed();
            } else {
                $stores = StoreFulfill::withTrashed();
            }
        }

        if(isset($input['name'])) {
            $stores = $stores->where('name', 'like', '%'.$input['name'].'%');
        }

        if(isset($input['code'])) {
            $stores = $stores->where('code', 'like', '%'.$input['code'].'%');
        }

        $stores = $stores->paginate()->withQueryString();

        $storeNames = StoreFulfill::withTrashed()->pluck('name')->toArray();

        return [
            'stores' => $stores,
            'oldInput' => $input,
            'storeNames' => $storeNames
        ];
    }

    public function detail($id)
    {
        $store = StoreFulfill::withTrashed()->find($id);

        return [
            'store' => $store,
        ];
    }

    public function create($input)
    {
        StoreFulfill::create([
            'name' => $input['name'],
            'code' => $this->generateBarcodeNumber()
        ]);
    }

    public function delete($input)
    {
        $store = StoreFulfill::withTrashed()->find($input['id']);

        if(isset($store['deleted_at'])) {
            $store->restore();
        } else {
            $store->delete();
        }
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
