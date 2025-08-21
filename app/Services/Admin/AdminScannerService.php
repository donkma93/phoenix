<?php

namespace App\Services\Admin;

use App\Services\AdminBaseServiceInterface;
use App\Models\Package;
use App\Models\PackageGroup;
use App\Models\WarehouseArea;
use App\Models\StoreFulfill;
use App\Models\Tote;

class AdminScannerService implements AdminBaseServiceInterface
{
    function scanner($request) {
        $checkGroup = PackageGroup::where('barcode', $request['barcode'])->first();

        if($checkGroup != null) {
            return [
                'status' => 'success',
                'type' => 'group',
                'id' => $checkGroup->id
            ];
        } 

        $checkPackage = Package::where('barcode', $request['barcode'])->first();

        if($checkPackage != null) {
            return [
                'status' => 'success',
                'type' => 'package',
                'id' => $checkPackage->id
            ];
        } 

        $checkArea = WarehouseArea::where('barcode', $request['barcode'])->first();

        if($checkArea != null) {
            return [
                'status' => 'success',
                'type' => 'warehouse',
                'id' => $checkArea->id
            ];
        }

        $checkStore = StoreFulfill::where('code', $request['barcode'])->first();

        if($checkStore != null) {
            return [
                'status' => 'success',
                'type' => 'store',
                'id' => $checkStore->id
            ];
        }
        
        $checkTote = Tote::where('barcode', $request['barcode'])->first();

        if($checkStore != null) {
            return [
                'status' => 'success',
                'type' => 'tote',
                'id' => $checkTote->id
            ];
        }

        return [
            'status' => 'fail',
        ];
    }
}
