<?php

namespace App\Services\Admin;

use App\Services\AdminBaseServiceInterface;
use App\Models\Package;
use App\Models\PackageGroup;
use App\Models\PackageHistory;
use App\Models\PackageDetail;
use App\Models\User;
use App\Models\UserRequest;
use App\Models\WarehouseArea;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class AdminPackageService extends AdminBaseService implements AdminBaseServiceInterface
{
    public function list($request)
    {
        $packages = Package::with(['warehouseArea' => function ($warehouseArea) {
            $warehouseArea->withTrashed();
         }, 'user' => function ($user) {
            $user->withTrashed();
         }, 'packageGroup' => function ($group) {
            $group->withTrashed();
         }]);

        if(isset($request['onlyDeleted'])) {
            if($request['onlyDeleted'] == 1) {
                $packages = $packages->onlyTrashed();
            }
        } else {
            $packages = $packages->withTrashed();
        }

        $users = User::where('role', User::ROLE_USER)->withTrashed()->pluck('email')->toArray();

        $warehouseAreas = WarehouseArea::withTrashed()->pluck('name')->toArray();

        if(isset($request['status'])) {
            $packages = $packages->where('status', $request['status']);
        }

        if(isset($request['email'])) {
            $packages = $packages->whereHas('user', function ($query) use ($request) {
                $query->where('email', 'like', '%'.$request['email'].'%');
            });
        }

        if(isset($request['warehouse'])) {
            $packages = $packages->whereHas('warehouseArea', function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request['warehouse'].'%');
            });
        }

        if(isset($request['startDate'])) {
            $packages = $packages->where('created_at', '>=' , date("Y-m-d",strtotime($request['startDate'])));
        }

        if(isset($request['endDate'])) {
            $packages = $packages->where('created_at', '<=' , date("Y-m-d  23:59:59",strtotime($request['endDate'])));
        }

        if(isset($request['barcode'])) {
            $packages = $packages->where('barcode', $request['barcode']);
        }
        
        $packages = $packages->orderByDesc('updated_at');

        $packages = $packages->paginate()->withQueryString();

        $areas = WarehouseArea::withTrashed()->select('name', 'barcode')->get()->toArray();
        
        return [
            'oldInput' => $request,
            'packages' => $packages,
            'users' => $users,
            'warehouseAreas' => $warehouseAreas,
            'areas' => $areas
        ];
    }

    public function history($request) 
    {
        $packages = PackageHistory::withTrashed()->with(['package' => function ($package) {
            $package->withTrashed();
         }, 'staff' => function ($staff) {
            $staff->withTrashed();
         }, 'warehouseArea' => function ($area) {
            $area->withTrashed();
         }]);
        
        if(isset($request['status'])) {
            $packages = $packages->where('status', $request['status']);
        }
        
        if(isset($request['previous_status'])) {
            $packages = $packages->where('previous_status', $request['previous_status']);
        }

        if(isset($request['name'])) {
            $packages = $packages->where('name', $request['name']);
        }

        if(isset($request['type'])) {
            $packages = $packages->where('type', $request['type']);
        }

        if(isset($request['barcode'])) {
            $packages = $packages->where('barcode', $request['barcode'])->orWhere('previous_barcode', $request['barcode']);
        }

        if(isset($request['warehouse'])) {
            $packages = $packages->whereHas('warehouseArea', function($package) use ($request) {
                $package->where('name', $request['warehouse']);
            });
        }

        if(isset($request['startDate'])) {
            $packages = $packages->where('created_at', '>=' , date("Y-m-d",strtotime($request['startDate'])));
        }

        if(isset($request['endDate'])) {
            $packages = $packages->where('created_at', '<=' , date("Y-m-d 23:59:59",strtotime($request['endDate'])));
        }

        $packages = $packages->orderByDesc('created_at');
        
        $packages = $packages->paginate()->withQueryString();

        $areas = WarehouseArea::withTrashed()->select('name', 'barcode')->get()->toArray();

        $warehouseAreas = WarehouseArea::withTrashed()->pluck('name')->toArray();

        return [
            'oldInput' => $request,
            'packages' => $packages,
            'warehouseAreas' => $warehouseAreas,
            'areas' => $areas
        ];
    }

    public function detail($id) 
    {
        $package = Package::withTrashed()->with(['user' => function ($user) {
            $user->withTrashed();
         }, 'warehouseArea' => function ($area) {
            $area->withTrashed();
         }, 'packageGroup' => function ($group) {
            $group->withTrashed();
         }])->find($id);

        $groupByDetail = PackageDetail::where('package_id', $package->id)->pluck('package_group_id')->toArray();

        $groups = PackageGroup::withTrashed()->whereIn('id', $groupByDetail)->orWhere('id', $package->package_group_id)->paginate()->withQueryString();
        
        return [
            'package' => $package,
            'groups' => $groups
        ];
    }

    public function historyDetail($id) 
    {
        $history = PackageHistory::withTrashed()->with(['staff' => function ($user) {
            $user->withTrashed();
         }, 'warehouseArea' => function ($area) {
            $area->withTrashed();
         }])->find($id);
        
        $package = Package::find($history['package_id']);
        $groupDetail = PackageGroup::find($package['package_group_id']);
        
        return [
            'history' => $history,
            'groupDetail' => $groupDetail
        ];
    }

    public function delete($request) 
    {
        $package = Package::withTrashed()->find($request['id']);
        DB::beginTransaction();
        
        try {
            if(isset($package->deleted_at)) {
                $package->restore();

                $lastHistory = PackageHistory::where('package_id', $request['id'])->orderByDesc('created_at')->first(); 

                PackageHistory::create([
                    "barcode" => $package['barcode'],
                    "previous_barcode" => $package['barcode'],
                    "package_id" => $package['id'],
                    "warehouse_area_id" => $package['warehouse_area_id'] ?? null,
                    "staff_id" => Auth::id(),
                    "previous_status" => $package['status'],
                    "status" => $package['status'],
                    "unit_number" => $package['received_unit_number'] ?? 0,
                    'weight' => $package['weight'] ?? null,
                    'height' => $package['height'] ?? null,
                    'length' => $package['length'] ?? null,
                    'width' => $package['width'] ?? null,
                    'weight_staff' => $package['weight_staff'] ?? null,
                    'height_staff' => $package['height_staff'] ?? null,
                    'length_staff' => $package['length_staff'] ?? null,
                    'width_staff' => $package['width_staff'] ?? null,
                    "previous_created_at" => $lastHistory->created_at,
                    'type' => PackageHistory::TYPE_RESTORE,
                    'stage' => 'admin - packge detail'
                ]);
            } else {
                $getRequests = UserRequest::where('user_id', $request['user_id'])->whereIn('status', [UserRequest::STATUS_INPROGRESS, UserRequest::STATUS_NEW])->get();
                $isExisted = false;
            
                foreach($getRequests as $userRequest) {
                    if(isset($userRequest->packages) && $userRequest->packages != '') {
                        $packageList = explode(',', $userRequest->packages) ?? [];
                        if(in_array($request['id'], $packageList)) {
                            $isExisted = true;

                            break;
                        }
                    }
                }
                
                if($isExisted) {
                    return false;
                }

                $package->delete();

                $lastHistory = PackageHistory::where('package_id', $request['id'])->orderByDesc('created_at')->first(); 

                PackageHistory::create([
                    "barcode" => $package['barcode'],
                    "previous_barcode" => $package['barcode'],
                    "package_id" => $package['id'],
                    "warehouse_area_id" => $package['warehouse_area_id'] ?? null,
                    "staff_id" => Auth::id(),
                    "previous_status" => $package['status'],
                    "status" => $package['status'],
                    "unit_number" => $package['received_unit_number'] ?? 0,
                    'weight' => $package['weight'] ?? null,
                    'height' => $package['height'] ?? null,
                    'length' => $package['length'] ?? null,
                    'width' => $package['width'] ?? null,
                    'weight_staff' => $package['weight_staff'] ?? null,
                    'height_staff' => $package['height_staff'] ?? null,
                    'length_staff' => $package['length_staff'] ?? null,
                    'width_staff' => $package['width_staff'] ?? null,
                    "previous_created_at" => $lastHistory->created_at,
                    'type' => PackageHistory::TYPE_DELETE,
                    'stage' => 'admin - packge detail'
                ]);
            }
            DB::commit();

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            
            throw new Exception($e->getMessage());
        }
    }
}