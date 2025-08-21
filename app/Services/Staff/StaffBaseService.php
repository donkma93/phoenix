<?php

namespace App\Services\Staff;

use App\Services\StaffBaseServiceInterface;
use App\Models\WarehouseArea;
use App\Models\Package;
use App\Models\PackageGroup;
use App\Models\ChatBoxDetail;
use App\Models\UserRequest;
use App\Models\User;
use App\Models\Tote;
use Carbon\Carbon;

class StaffBaseService implements StaffBaseServiceInterface
{
    function notification() {
        $totalUsersRequest = UserRequest::whereHas('user', function($users) {
            $users->where('role', config('auth.role.user'));
        })->where('status', '=', UserRequest::STATUS_NEW)->count();

        $listChat = ChatBoxDetail::select("*")->where('staff_get', 0)->orderByDesc('created_at')->get();
        $totalMessage = count($listChat->unique('chat_box_id')->values()->all());

        return [ 
            'request' => $totalUsersRequest,
            'message' => $totalMessage,
        ];
    }

    function getAllWarehouseArea() {
        $warehouseAreas = WarehouseArea::all();

        return $warehouseAreas;
    }

    function getAllUser() {
        $users = User::where('role', config('auth.role.user'))->get();
        
        return $users;
    }

    function generateBarcodeNumber($barcodeList, $name, $number)
    {
        $barcode = null;    
        $strArr = explode(" ",$name);
        if(count($strArr) <= 2) {
            $barcode = $this->generateName($strArr, 3);
        } else {
            $barcode = $this->generateName($strArr, 1);
        }
        
        if($number > 0) {
            $barcode = $barcode . '-' . $number;
        }
        
        if ($this->barcodeNumberExists($barcode, $barcodeList)) {
            $number = $number + 1 ?? 1;
            return $this->generateBarcodeNumber($barcodeList, $name, $number);
        }

        return $barcode;
    }

    function barcodeNumberExists($barcode, $barcodeList)
    {
        return PackageGroup::where('barcode', $barcode)->exists() || 
                Package::where('barcode', $barcode)->exists() || 
                WarehouseArea::where('barcode', $barcode)->exists() || 
                Tote::where('barcode', $barcode)->exists() || 
                in_array($barcode, $barcodeList);
    }

    function generateName($arr, $numberOfText) 
    {
        $now = Carbon::now();

        $result = "";
        for($i = 0; $i < count($arr); $i++) {
            $str = mb_strtoupper(mb_substr($arr[$i], 0, $numberOfText, 'UTF-8'));
            if($result == "") 
            {
                $result = $str;
            } else {
                $result = $result . $str;
            }
        }

        if(strlen($result) > 10) {
            $result = mb_substr($result, 0, 10, 'UTF-8');
        }
        
        return $result . $now->format('ymdu');
    }
}
