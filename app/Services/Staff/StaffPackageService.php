<?php

namespace App\Services\Staff;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\StaffBaseServiceInterface;
use App\Models\User;
use App\Models\Package;
use App\Models\PackageDetail;
use App\Models\PackageGroup;
use App\Models\PackageGroupHistory;
use App\Models\PackageOutbound;
use App\Models\PackageHistory;
use App\Models\WarehouseArea;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class StaffPackageService extends StaffBaseService implements StaffBaseServiceInterface
{
    function list($request)
    {
        $packages = Package::with(['packageGroup' => function ($group) {
            $group->withTrashed();
         }, 'user', 'warehouseArea' => function ($area) {
            $area->withTrashed();
         }])->has('user');

        if(isset($request['email'])) {
            $packages->whereHas('user', function ($query) use ($request) {
                $query->where('email', 'like', '%'.$request['email'].'%');
            });
        }

        if(isset($request['warehouse'])) {
            $packages->whereHas('warehouseArea', function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request['warehouse'].'%');
            });
        }

        if(isset($request['group'])) {
            $packages->whereHas('packageGroup', function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request['group'].'%');
            })->orWhereHas('details', function($query) use ($request) {
                $query->whereHas('packageGroup',  function ($detail) use ($request) {
                    $detail->where('name', 'like', '%'.$request['group'].'%');
                });
            });
        }

        if(isset($request['unit'])) {
            $packages = $packages->where('unit_number', $request['unit']);
        }
        
        if(isset($request['barcode'])) {
            $packages = $packages->where('barcode', $request['barcode']);
        }

        if(isset($request['status'])) {
            $packages = $packages->where('status', $request['status']);
        }

        if(isset($request['onlyDeleted'])) {
            if($request['onlyDeleted'] == 1) {
                $packages = $packages->onlyTrashed();
            }
        } else {
            $packages = $packages->withTrashed();
        }

        $packages = $packages->orderByDesc('updated_at');
        
        $packages = $packages->paginate()->withQueryString();

        $users = User::where('role', User::ROLE_USER)->pluck('email')->toArray();

        $warehouses = WarehouseArea::pluck('name')->toArray();

        $groups = PackageGroup::pluck('name')->toArray();

        $areas = WarehouseArea::select('name', 'barcode')->get()->toArray();

        return [
            'oldInput' => $request,
            'packages' => $packages,
            'users' => $users,
            'warehouses' => $warehouses,
            'groups' => $groups,
            'areas' => $areas,
        ];
    }

    public function detail($id) 
    {
        $package = Package::with(['packageGroup' => function ($group) {
            $group->withTrashed();
         }, 'user', 'warehouseArea' => function ($area) {
            $area->withTrashed();
         }])->has('user')->withTrashed()->find($id);
        
        $warehouses = WarehouseArea::where('is_full', 0)->pluck('name')->toArray();

        $areas = WarehouseArea::select('id', 'name', 'barcode', 'is_full')->get()->toArray();
        
        $groupByDetail = PackageDetail::where('package_id', $package->id)->pluck('package_group_id')->toArray();

        $groups = PackageGroup::withTrashed()->whereIn('id', $groupByDetail)->orWhere('id', $package->package_group_id)->paginate()->withQueryString();

        return [
            'package' => $package,
            'warehouses' => $warehouses,
            'areas' => $areas,
            'groups' => $groups
        ];
    }

    public function new()
    {
        $users = User::where('role', User::ROLE_USER)->pluck('email')->toArray();

        $warehouses = WarehouseArea::where('is_full', 0)->pluck('name')->toArray();

        $areas = WarehouseArea::select('id', 'name', 'barcode', 'is_full')->get()->toArray();

        return [
            'users' => $users,
            'warehouses' => $warehouses,
            'areas' => $areas,
        ];
    }

    function getPackagesByUser($request)
    {
        $packages = Package::with('user', 'warehouseArea', 'packageGroup')->has('user')->has('packageGroup')
        // ->has('packageDetail')
        ->where('status', '<>', Package::STATUS_OUTBOUND);

        if(isset($request['email']) && $request['email'] != null) {
            $packages->whereHas('user', function ($query) use ($request) {
                $query->where('email', 'like', '%'.$request['email'].'%');
            });
        }

        if(isset($request['warehouse']) && $request['warehouse'] != null) {
            $packages->whereHas('warehouseArea', function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request['warehouse'].'%');
            });
        }

        if(isset($request['group']) && $request['group'] != null) {
            $packages->whereHas('packageGroup', function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request['group'].'%');
            })->orWhereHas('details', function($query) use ($request) {
                $query->whereHas('packageGroup',  function ($detail) use ($request) {
                    $detail->where('name', 'like', '%'.$request['group'].'%');
                });
            });
        }

        if(isset($request['showSelectedOnly']) && $request['showSelectedOnly'] != null) {
            if(!empty($request->session()->get('packageIds')) && count($request->session()->get('packageIds')) > 0) {
                $ids = $request->session()->get('packageIds');

                $packages = $packages->whereIn('id', $ids);
            } else {
                $packages = $packages->where('id', -1);
            }
        }

        if(isset($request['barcode']) && $request['barcode'] != null) {
            $packages = $packages->where('barcode', $request['barcode']);
        }

        $packages = $packages->orderByDesc('created_at')->paginate()->withQueryString();

        $users = User::where('role', User::ROLE_USER)->pluck('email')->toArray();

        $warehouses = WarehouseArea::pluck('name')->toArray();

        $groups = PackageGroup::pluck('name')->toArray();

        $areas = WarehouseArea::select('name', 'barcode', 'is_full')->get()->toArray();

        return [
            'packages' => $packages,
            'oldInput' => $request,
            'users' => $users,
            'warehouses' => $warehouses,
            'groups' => $groups,
            'areas' => $areas,
        ];
    }

    function getGroup($request)
    {
        $groups = PackageGroup::whereHas('user', function ($query) use ($request) {
            $query->where('email', $request['email']);
        })->with('user');
        
        if(isset($request['name'])) {
            $groups = $groups->where('name', 'like', '%'.$request['name'].'%');
        }
        $groups = $groups->get();

        return $groups;
    }

    function setOutboundPackage($packageIds, $addresses)
    {
        DB::transaction(function () use ($packageIds, $addresses) {
            for($i=0; $i<count($packageIds); $i++) {
                $package = Package::find($packageIds[$i]);
                $oldStatus = $package['status'];

                $package->status = Package::STATUS_OUTBOUND;
                $package->save();

                $lastHistory = PackageHistory::where('package_id', $packageIds[$i])->orderByDesc('created_at')->first(); 

                PackageHistory::create([
                    "barcode" => $package['barcode'],
                    "previous_barcode" => $package['barcode'],
                    "package_id" => $packageIds[$i],
                    "warehouse_area_id" => $package['warehouse_area_id'] ?? null,
                    "staff_id" => Auth::id(),
                    "previous_status" => $oldStatus,
                    "status" => Package::STATUS_OUTBOUND,
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
                    'stage' => 'staff - outbound'
                ]);

                PackageOutbound::create([
                    'package_id' => $packageIds[$i],
                    'address' => $addresses[$i],
                ]);
                
                if(isset($package['warehouse_area_id'])) {
                    $area = WarehouseArea::find($package['warehouse_area_id']);

                    if($area['is_full']) {
                        $area['is_full'] = false;
                        
                        $area->save();
                    }
                }
            }
        });
    }

    function create($request)
    {
        $now = Carbon::now();

        DB::transaction(function () use ($request) {
            $groupId = null;
            $userId = null;
            $groupName = null;
            if($request['group_type'] == 'new') {
                $user = User::where('email', $request['email'])->first();

                $properties = ['name', 'barcode', 'unit_weight', 'unit_height', 'unit_length', 'unit_width'];
                $data = [];
    
                if(isset($request['file'])) {
                    $fileName = $request['file']->move('files' . DIRECTORY_SEPARATOR . PackageGroup::FILE_FOLDER, cleanName($request['file']->getClientOriginalName()));
                    $data['file'] = $fileName;
                }
                
                $data['user_id'] = $user['id'];
                $userId = $user['id'];
                foreach ($properties as $prop) {
                    // allow update null
                    if (array_key_exists($prop, $request)) {
                        $data[$prop] = $request[$prop];
                    }
                }
    
                $newGroup = PackageGroup::create($data);

                PackageGroupHistory::create([
                    'package_group_id' => $newGroup['id'],
                    'previous_user_id' => $newGroup['user_id'],
                    'user_id' => $newGroup['user_id'],
                    'previous_name' => $newGroup['name'],
                    'name' => $newGroup['name'],
                    'previous_barcode' => $newGroup['barcode'],
                    'barcode' => $newGroup['barcode'],
                    'unit_width' => $newGroup['unit_width'],
                    'unit_weight' => $newGroup['unit_weight'],
                    'unit_length' => $newGroup['unit_length'],
                    'unit_height' => $newGroup['unit_height'],
                    'previous_unit_weight' => $newGroup['unit_weight'],
                    'previous_unit_height' => $newGroup['unit_height'],
                    'previous_unit_length' => $newGroup['unit_length'], 
                    'previous_unit_width' => $newGroup['unit_width'],
                    'staff_id' => Auth::id(),
                    'stage' => 'staff - create package',
                    'type' => PackageGroupHistory::CREATE
                ]);

                $groupId = $newGroup['id'];
                $groupName = $data['name'];
            } else if($request['group_type'] == 'exited') {
                $groupId = $request['group_id'];
                $userId = $request['user_id'];
                $group = PackageGroup::withTrashed()->find($groupId);
                $groupName = $group['name'];
            }

            $barcodeList = [];
            foreach ($request['package'] as $index=>$package) {
                $barcode = $this->generateBarcodeNumber($barcodeList, $groupName, $package['unit_number']);
                array_push($barcodeList, $barcode);
                $data = [];
                $data['user_id'] = $userId;
                $data['package_group_id'] = $groupId;
                $data['warehouse_area_id'] = $package['warehouse_area_id'] ?? null;
                $data['barcode'] = $barcode;

                $properties = ['status', 'unit_number','received_unit_number', 'warehouse_area_id', 'weight_staff', 'height_staff', 'length_staff', 'width_staff'];

                foreach ($properties as $prop) {
                    // allow update null
                    if (array_key_exists($prop, $package)) {
                        $data[$prop] = $package[$prop];
                    }
                }
                
                $package = Package::create($data);

                PackageDetail::create([
                    'package_group_id' => $groupId,
                    'package_id' => $package['id'],
                    'unit_number' => $package['unit_number'],
                    'received_unit_number' => $package['unit_number'],
                ]);

                PackageHistory::create([
                    'package_id' => $package['id'],
                    'barcode' => $barcode,
                    'previous_barcode' => $barcode,
                    'previous_status' => $package['status'],
                    'status' => $package['status'],
                    'staff_id' => Auth::id(),
                    'unit_number' => $package['unit_number'],
                    'weight_staff' => $package['weight_staff'] ?? null,
                    'length_staff' => $package['length_staff'] ?? null,
                    'width_staff' => $package['width_staff'] ?? null,
                    'height_staff' => $package['height_staff'] ?? null,
                    'warehouse_area_id' => $package['warehouse_area_id'] ?? null ,
                    'previous_created_at' => null,
                    'stage' => 'staff - new packge'
                ]);
            }
        });
    }

    function update($request) 
    {
        DB::beginTransaction();
        
        try {
            
            $area = WarehouseArea::where('name' , $request['warehouse'])->first();
            
            $package = Package::find($request['id']);
            $oldStatus = $package['status'];
            $oldBarcode = $package['barcode'];
            if($oldBarcode != $request['barcode']) {
               $isExisted = $this->barcodeNumberExists($request['barcode'], []);

               if($isExisted == true) {
                    DB::rollBack();

                    return $request['barcode'].' is already existed';
               }
            }
            $package->warehouse_area_id = isset($request['warehouse']) ? $area['id'] : null;
            $package->status = $request['status'];
            $package->weight_staff = $request['weight_staff'] ?? null;
            $package->height_staff = $request['height_staff'] ?? null;
            $package->length_staff = $request['length_staff'] ?? null;
            $package->width_staff = $request['width_staff'] ?? null;
            $package->barcode = $request['barcode'];

            $package->save();

            $lastHistory = PackageHistory::where('package_id', $request['id'])->orderByDesc('created_at')->first(); 
            
            PackageHistory::create([
                'package_id' => $package['id'],
                'barcode' => $request['barcode'],
                'previous_barcode' => $package['barcode'],
                'previous_status' => $oldStatus,
                'status' => $request['status'],
                'staff_id' => Auth::id(),
                'unit_number' => $package['unit_number'],
                'weight_staff' => $request['weight_staff'] ?? null,
                'height_staff' => $request['height_staff'] ?? null,
                'length_staff' => $request['length_staff'] ?? null,
                'width_staff' => $request['width_staff'] ?? null,
                'weight' => $lastHistory['weight'] ?? null,
                'height' => $lastHistory['height'] ?? null,
                'length' => $lastHistory['length'] ?? null,
                'width' => $lastHistory['width'] ?? null,
                'warehouse_area_id' => isset($request['warehouse']) ? $area['id'] : null ,
                'previous_created_at' => $lastHistory->created_at,
                'stage' => 'staff - packge detail'
            ]);
            
            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
              
            throw new Exception($e->getMessage());
        }

        return null;
    }
}
