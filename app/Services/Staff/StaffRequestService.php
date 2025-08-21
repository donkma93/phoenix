<?php

namespace App\Services\Staff;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\StaffBaseServiceInterface;
use App\Models\User;
use App\Models\Package;
use App\Models\UserRequest;
use App\Models\WarehouseArea;
use App\Models\PackageGroup;
use App\Models\PackageHistory;
use App\Models\PackageDetail;
use App\Models\RequestPackage;
use App\Models\RequestPackageGroup;
use App\Models\RequestPackageImage;
use App\Models\RequestPackageTracking;
use App\Models\RequestTimeHistory;
use App\Models\RequestHistory;
use App\Models\MRequestType;
use App\Models\RequestWorkingTime;
use App\Notifications\UserRequestDone;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;

class StaffRequestService extends StaffBaseService implements StaffBaseServiceInterface
{
    function getRequests($input) {
        $groupNames = [];
        $requestQuery = UserRequest::with('user', 'mRequestType')->has('user')

        // ->whereHas('user', function($users) {
        //     $users->where('role', User::ROLE_USER);
        // })

        ->orderByDesc('updated_at');

        if(isset($input['email'])) {
            $requestQuery = $requestQuery->whereHas('user', function ($query) use ($input) {
                $query->where('email', 'like', '%'.$input['email'].'%');
            });
        }

        if (isset($input['status'])) {
            $requestQuery = $requestQuery->where('status', $input['status']);
        }

        if (isset($input['type'])) {
            $requestQuery = $requestQuery->whereHas('mRequestType', function ($query) use ($input) {
                $query->where('name', $input['type']);
            });
        }

        if (isset($input['barcode'])) {
            $requestQuery = $requestQuery->whereHas('requestPackageGroups', function ($query) use ($input) {
                $query->whereHas('requestPackages', function ($package) use ($input) {
                    $package->where('barcode', $input['barcode']);
                });
            });
        }

        if(isset($input['startDate'])) {
            $requestQuery = $requestQuery->where('created_at', '>=' , date("Y-m-d",strtotime($input['startDate'])));
        }

        if(isset($input['endDate'])) {
            $requestQuery = $requestQuery->where('created_at', '<=' , date("Y-m-d 23:59:59",strtotime($input['endDate'])));
        }

        $requestQuery = $requestQuery->paginate()->withQueryString();



        foreach($requestQuery as $request) {



            $groupName = RequestPackageGroup::where('user_request_id', $request['id'])
            ->leftJoin('package_groups', 'request_package_groups.package_group_id', '=', 'package_groups.id')
            ->select(DB::raw('GROUP_CONCAT(package_groups.name SEPARATOR ", ") AS name'))->groupBy('request_package_groups.user_request_id')->first();

            $groupNames[$request['id']] = $groupName->name;
        }

        $requestTypes = MRequestType::pluck('name')->toArray();

        $users = User::where('role', User::ROLE_USER)->pluck('email')->toArray();



        return [
            'requests' => $requestQuery,
            'requestTypes' => $requestTypes,
            'oldInput' => $input,
            'users' => $users,
            'groupNames' => $groupNames
        ];
    }

    function getRequest($id, $input) {
        $userRequest = UserRequest::with('mRequestType',  'user')->has('user')->find($id);
        $packages = [];
        $unitNeed = 0;
        $packagesList = explode(',', $userRequest->packages) ?? [];
        $packageGroupImages = [];
        $packageGroupIds = [];
        $packageGroups = [];
        $warehouseAreaInUse = [];
        $trackings = [];

        $packagesSaved = Package::whereIn('packages.id', $packagesList)
        ->leftJoin(DB::raw('(select package_details.package_id as id, GROUP_CONCAT(package_groups.name SEPARATOR ", ") AS name
        from package_details left join package_groups on package_details.package_group_id = package_groups.id
        group by package_details.package_id) as package_group_names'), 'packages.id', '=','package_group_names.id')
        ->leftJoin('package_groups', 'packages.package_group_id', '=','package_groups.id')
        ->select('packages.*',
        'package_group_names.name as detail_groups_name',
        'package_groups.name as group_name from packages')
        ->withTrashed()->orderByDesc('created_at')->get();

        $allPackages = [];
        if($userRequest->mRequestType->name == 'add package' && isset($userRequest->is_allow)) {
            $packages = Package::where('user_request_id', $userRequest['id'])->paginate()->withQueryString();
            $allPackages = Package::where('user_request_id', $userRequest['id'])->get();
            $packageGroups = RequestPackageGroup::where('user_request_id', $userRequest['id'])->with('packageGroup')->paginate()->withQueryString();

            foreach($packageGroups as $package) {
                if(!in_array($package['id'], $packageGroupIds)) {
                    array_push($packageGroupIds, $package['id']);
                    $packageImage = RequestPackageImage::where('request_package_group_id', $package['id'])->pluck('image_url')->toArray();
                    $packageGroupImages[$package['id']] = $packageImage;
                }

                $tracking = RequestPackageTracking::where('request_package_group_id', $package['id'])->select(DB::raw('GROUP_CONCAT(tracking_url) AS tracking'), 'request_package_group_id')->groupBy('request_package_group_id')->first();
                if(isset($tracking)) {
                    $trackings[$package['id']] = str_replace(',', '||',$tracking->tracking);
                }
            }
        } else {
            $packages = RequestPackageGroup::where('user_request_id', $userRequest['id'])
            ->join('request_packages', 'request_package_groups.id', '=', 'request_packages.request_package_group_id')
            ->join('package_groups', 'package_groups.id', '=', 'request_package_groups.package_group_id')
            ->select('request_packages.id',
            'request_package_groups.id as request_package_group_id',
            'request_package_groups.package_group_id',
            'request_packages.package_number',
            'request_packages.unit_number',
            'request_packages.received_package_number',
            'request_packages.received_unit_number',
            'request_package_groups.barcode',
            'request_package_groups.file',
            'request_package_groups.is_insurance',
            'request_package_groups.insurance_fee',
            'request_package_groups.ship_mode',
            'request_packages.height',
            'request_packages.length',
            'request_packages.width',
            'request_packages.weight',
            'package_groups.name');

            $packages = $packages->orderByDesc('request_package_groups.created_at')->paginate()->withQueryString();

            foreach($packages as $package) {
                if(!in_array($package['request_package_group_id'], $packageGroupIds)) {
                    array_push($packageGroupIds, $package['request_package_group_id']);
                    $packageImage = RequestPackageImage::where('request_package_group_id', $package['request_package_group_id'])->pluck('image_url')->toArray();
                    $packageGroupImages[$package['request_package_group_id']] = $packageImage;
                }

                $tracking = RequestPackageTracking::where('request_package_group_id', $package['request_package_group_id'])->select(DB::raw('GROUP_CONCAT(tracking_url) AS tracking'), 'request_package_group_id')->groupBy('request_package_group_id')->first();
                if(isset($tracking)) {
                    $trackings[$package['id']] = str_replace(',', '||',$tracking->tracking);
                }
            }
        }

        $warehouseAreas = WarehouseArea::get();

        $warehouses = WarehouseArea::where('is_full', 0)->pluck('name')->toArray();
        $requestHour = RequestTimeHistory::where('user_request_id', $userRequest['id'])->first();

        $workingTime = RequestWorkingTime::where('user_request_id', $userRequest['id'])->whereNull('finish_at')->orderByDesc('created_at')->first();

        return  [
            'allPackages' => $allPackages,
            'userRequest' => $userRequest,
            'packages' => $packages,
            'oldInput' => $input,
            'warehouseAreas' => $warehouseAreas,
            'unitNeed' => $unitNeed,
            'warehouses' => $warehouses,
            'packagesSaved' => $packagesSaved,
            'packageGroupImages' => $packageGroupImages,
            'trackings' => $trackings,
            'requestHour' => $requestHour,
            'lastWorking' => $workingTime,
            'packageGroups' => $packageGroups
        ];
    }

    function updateUserRequest($id, $status, $staffId) {
        DB::beginTransaction();

        try {
            $now = Carbon::now();
            $request = UserRequest::find($id);
            if($request->status == 0) {
                $request->staff_id = $staffId;
                $request->start_at = $now;

                RequestWorkingTime::create([
                    'user_request_id' => $id,
                    'start_at' => $now
                ]);
            } else {
                $request->finish_at = $now;
                $workingTime = RequestWorkingTime::where('user_request_id', $id)->whereNull('finish_at')->orderByDesc('created_at')->first();
                if($workingTime) {
                    $workingTime->finish_at = now();
                    $workingTime->save();

                    $hour = round($this->differenceInHours($workingTime->start_at, now()), 2);

                    $requestHour = RequestTimeHistory::where('user_request_id', $id)->first();
                    if(empty($requestHour)) {
                        RequestTimeHistory::create([
                            'user_request_id' => $id,
                            'hour' => $hour
                        ]);
                    } else {
                        $requestHour->hour = $requestHour->hour + $hour;
                        $requestHour->save();
                    }
                }
            }
            $request->status = $status;
            $request->save();

            // notify when request done
            if ($status == UserRequest::STATUS_DONE) {
                $user = User::find($request->user_id);
                $user->notify(new UserRequestDone([
                    'user_request_id' => $id,
                    'type' => $request->mRequestType->name,
                ]));
            }

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }

        return true;
    }

    function updatePackage($request)
    {
        DB::beginTransaction();

        try {
            foreach ($request['package'] as $index=>$item) {
                $package = Package::with('warehouseArea')->find($item['id']);
                $warehouseInput = $item['warehouse'] ?? '';
                $warehousePackage = $package['warehouseArea']['name'] ?? '';
                if((isset($package['warehouseArea']) != isset($item['warehouse']))
                    || ((isset($package['warehouseArea']) == isset($item['warehouse']) && $warehousePackage != $warehouseInput))
                    || ($package['status'] != $item['status'])
                    || ($package['barcode'] != $item['barcode'])
                    || (isset($item['received_unit_number'])
                        && $package['received_unit_number'] != $item['received_unit_number'])) {

                    if($package['barcode'] != $item['barcode']) {
                        $isExisted = $this->barcodeNumberExists($item['barcode'], []);

                        if($isExisted == true) {
                            DB::rollBack();

                            return $item['barcode'].' is already existed';
                        }
                    }

                    if(isset($item['received_unit_number']) && isset($item['unit_number']) && $item['received_unit_number'] > $item['unit_number']) {
                        DB::rollBack();

                        return 'Out of package limit';
                    }

                    $oldUnit = $package['received_unit_number'];
                    $oldBarcode = $package['barcode'];
                    $oldStatus = $package['status'];

                    $package->status = $item['status'];
                    $package->barcode = $item['barcode'];
                    $warehouseId = $package->warehouse_area_id;

                    if(isset($item['warehouse'])) {
                        $area = WarehouseArea::where('name', $item['warehouse'])->first();
                        if(empty($area)) {
                            DB::rollBack();

                            return 'Warehouse area not existed';
                        }
                        $warehouseId = $area['id'];
                    }

                    $package->received_unit_number = isset($item['received_unit_number']) ? $item['received_unit_number'] : $package->received_unit_number;
                    $package->warehouse_area_id = $warehouseId;

                    $package->save();
                    if(in_array($request['type_name'], ['add package', 'removal', 'return'])) {
                        $packageRequest = RequestPackage::whereHas('requestPackageGroup', function ($query) use ($request, $item) {
                            $query->where('user_request_id', $request['request_id'])->where('package_group_id', $item['package_group_id']);
                        })->first();
                        $packageRequest->received_unit_number = $packageRequest->received_unit_number + ($item['received_unit_number'] - $oldUnit);

                        $packageRequest->save();

                        RequestHistory::create([
                            'request_package_id' => $packageRequest->id,
                            'staff_id' => Auth::id(),
                            'unit_number' => ($item['received_unit_number'] - $oldUnit)
                        ]);
                    }

                    $lastHistory = PackageHistory::where('package_id', $package['id'])->orderByDesc('created_at')->first();

                    PackageHistory::create([
                        "barcode" => $package['barcode'],
                        "previous_barcode" => $oldBarcode,
                        "package_id" => $package['id'],
                        "warehouse_area_id" =>  $warehouseId,
                        "staff_id" => Auth::id(),
                        "previous_status" => $oldStatus,
                        "status" => $package['status'],
                        "unit_number" => isset($item['received_unit_number']) ? $item['received_unit_number'] : $package['received_unit_number'],
                        'weight_staff' => $package['weight'] ?? null,
                        'height_staff' => $package['height'] ?? null,
                        'length_staff' => $package['length'] ?? null,
                        'width_staff' => $package['width'] ?? null,
                        'weight' => $lastHistory->weight ?? null,
                        'height' => $lastHistory->height ?? null,
                        'length' => $lastHistory->length ?? null,
                        'width' => $lastHistory->width ?? null,
                        "previous_created_at" => $lastHistory->created_at,
                        'stage' => 'staff - request detail'
                    ]);
                }
            }

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }

        return null;
    }

    function addPackage($request)
    {
        $userRequest = UserRequest::with('mRequestType', 'requestPackage')->find($request['user_request_id']);
        $packageGroup = PackageGroup::find($request['package_group_id']);
        $totalPackages = Package::where('package_group_id', $request['package_group_id'])->count();
        $now = Carbon::now();

        if(in_array($userRequest['mRequestType']['name'], ['removal', 'return'])) {
            $received =  $request['remain'] ??  0;
            $totalUnit = 0;
            foreach ($request['package'] as $index=>$package) {
                $totalUnit = $totalUnit + (int)$package['received_unit_number'];
            }

            if($totalUnit > (int)$received) {

                return [
                    'message' => 'Out of unit limit'
                ];
            }
        }

        DB::beginTransaction();

        try {
            $ids = '';
            foreach ($request['package'] as $index=>$package) {
                if(($request['type_name'] == 'add package' && isset($package['checked'])) || $request['type_name'] != 'add package')
                {
                    $data = [];
                    if($request['type_name'] == 'add package') {
                        $unitNumber = $request['unit_number'];
                    } else {
                        $unitNumber = $package['received_unit_number'];
                    }
                    if(!isset($package['barcode']) || $package['barcode'] == null || $package['barcode'] == '') {
                        if(isset($packageGroup->barcode)) {
                            $data['barcode'] = $this->generateBarcodeNumber($packageGroup->barcode, $packageGroup['name'], $unitNumber);
                        } else {
                            $data['barcode'] = $this->generateBarcodeNumber(null, $packageGroup['name'], $unitNumber);
                        }
                    }

                    $data['user_id'] = $request['user_id'];
                    $data['package_group_id'] = $request['package_group_id'];
                    $data['created_at'] = $now;
                    $data['updated_at'] = $now;
                    $data['unit_number'] = $unitNumber;

                    $properties = ['status', 'warehouse_area_id', 'barcode', 'received_unit_number'];

                    if($request['type_name'] == 'add package' && isset($package['warehouse_area_name'])) {
                        $area = WarehouseArea::where('name' , $package['warehouse_area_name'])->first();
                        $package['warehouse_area_id'] = $area['id'];
                    }

                    foreach ($properties as $prop) {
                        // allow update null
                        if (array_key_exists($prop, $package)) {
                            $data[$prop] = $package[$prop];
                        }
                    }

                    $id = Package::insertGetId($data);

                    PackageDetail::create([
                        'package_group_id' => $request['package_group_id'],
                        'package_id' => $id,
                        'unit_number' => $package['unit_number'],
                        'received_unit_number' => $package['received_unit_number'],
                    ]);

                    PackageHistory::create([
                        'package_id' => $id,
                        'previous_status' => $package['status'],
                        'status' => $package['status'],
                        'staff_id' => Auth::id(),
                        'unit_number' => $unitNumber,
                        'warehouse_area_id' => $package['warehouse_area_id'] ?? null ,
                        'stage' => 'staff - request detail ' . $request['type_name']
                    ]);

                    $ids = $ids ? $ids.','.$id : $id;
                }
            }

            $userRequest->packages = $userRequest->packages ? $userRequest->packages.','.$ids : $ids;

            $userRequest->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    function getPackage($request)
    {
        $package = Package::with('warehouseArea')
        ->orderByDesc('created_at')
        ->where('user_id', $request['user_id'])
        ->where('unit_number', $request['unit_number'])
        ->whereIn('status', [Package::STATUS_INBOUND, Package::STATUS_STORED]);

        if(isset($request['barcode'])) {
            $package = $package->where('barcode', $request['barcode']);
        }

        $package = $package->whereHas('details', function ($packages) use ($request) {
            $packages->where('package_group_id', $request['package_group_id'])->withTrashed();
        })
        ->orWhereHas('packageGroupWithTrashed', function ($packages) use ($request) {
            $packages->where('id', $request['package_group_id']);
        });

        $package = $package->get();
        if(isset($request['barcode'])) {
            $package = $package->firstWhere('barcode', $request['barcode']);
        }

        return $package;
    }

    function checkPackage($request)
    {
        $package = Package::with('warehouseArea')
        ->orderByDesc('created_at')
        ->where('user_id', $request['user_id'])
        ->whereHas('details', function ($packages) use ($request) {
            $packages->where('package_group_id', $request['package_group_id'])->withTrashed();
        })
        ->orWhereHas('package_group_id', $request['package_group_id'])
        ->whereIn('status', [Package::STATUS_INBOUND, Package::STATUS_STORED]);

        if(isset($request['barcode'])) {
            $package = $package->where('barcode', $request['barcode']);
        }

        $package = $package->first();

        return $package;
    }

    function savePackage($request)
    {
        DB::beginTransaction();

        $now = Carbon::now();
        try {
            $ids = '';
            $barcodeList = [];
            $userRequest = UserRequest::with('mRequestType')->find($request['user_request_id']);
            $numberForAdd = 0;
            $unitForAdd = 0;
            $requestPackage = RequestPackage::find($request['request_package_id']);

            $listUsedId = [];
            $packagedInSave = $userRequest['packages'];
            $listUsedId = explode(",", $packagedInSave);

            if(!in_array($userRequest['mRequestType']['name'], ["add package", "removal", "return"])) {
                if($userRequest['mRequestType']['name'] == 'warehouse labor') {
                    foreach ($request['package'] as $item) {
                        if(isset($item['id'])) {
                            if(!in_array($item['id'], $listUsedId)) {
                                $package = Package::find($item['id']);

                                if(isset($item['delete'])) {
                                    $package->delete();
                                } else {
                                    $package->weight_staff = $item['weight'];
                                    $package->height_staff = $item['height'];
                                    $package->length_staff = $item['length'];
                                    $package->width_staff = $item['width'];
                                    $package->unit_number = $item['unit_number'];
                                    $package->received_unit_number = $item['received_unit_number'];
                                    $package->save();
                                }

                                array_push($listUsedId, $item['id']);

                                $lastHistory = PackageHistory::where('package_id', $item['id'])->orderByDesc('created_at')->first();

                                $historyData = [
                                    "barcode" => $package['barcode'],
                                    "previous_barcode" => $package['barcode'],
                                    "package_id" => $item['id'],
                                    "warehouse_area_id" =>  $package['warehouse_area_id'],
                                    "staff_id" => Auth::id(),
                                    "previous_status" => $package['status'],
                                    "status" => $package['status'],
                                    "unit_number" =>  $item['received_unit_number'],
                                    'weight_staff' => $package['weight'],
                                    'height_staff' => $package['height'],
                                    'length_staff' => $package['length'],
                                    'width_staff' => $package['width'],
                                    'weight' => $lastHistory->weight ?? null,
                                    'height' => $lastHistory->height ?? null,
                                    'length' => $lastHistory->length ?? null,
                                    'width' => $lastHistory->width ?? null,
                                    "previous_created_at" => $lastHistory->created_at,
                                    'stage' => 'staff - request detail ' . $userRequest['mRequestType']['name']
                                ];

                                if(isset($item['delete'])) {
                                    $historyData['type'] = PackageHistory::TYPE_DELETE;
                                }
                                PackageHistory::create($historyData);


                                $ids = $ids ? $ids.','.$item['id'] : $item['id'];

                                $numberForAdd = $numberForAdd + 1;
                            }
                        } else {
                            $packageGroup = PackageGroup::withTrashed()->find($request['package_group_id']);
                            $barcode = $this->generateBarcodeNumber($barcodeList, $packageGroup['name'], $item['unit_number']);
                            array_push($barcodeList, $barcode);

                            $warehouseId = null;
                            if(isset($item['warehouse_area_name'])) {
                                $area = WarehouseArea::where('name', $item['warehouse_area_name'])->first();
                                $warehouseId = $area->id;
                            }

                            $insertData = [
                                'package_group_id' => $request['package_group_id'],
                                'status' => $item['status'],
                                'user_id' => $request['user_id'],
                                'warehouse_area_id' => $warehouseId,
                                'unit_number' => $item['unit_number'],
                                'received_unit_number' => $item['received_unit_number'],
                                'barcode' => $barcode,
                                'weight_staff' => $item['weight'] ?? null,
                                'height_staff' => $item['height'] ?? null,
                                'length_staff' => $item['length'] ?? null,
                                'width_staff' => $item['width'] ?? null,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];

                            $newPackage = Package::create($insertData);

                            PackageDetail::create([
                                'package_group_id' => $request['package_group_id'],
                                'package_id' => $newPackage->id,
                                'unit_number' => $item['unit_number'],
                                'received_unit_number' => $item['received_unit_number'],
                            ]);

                            $id = $newPackage->id;

                            PackageHistory::create([
                                "barcode" => $barcode,
                                "previous_barcode" => $barcode,
                                "package_id" => $id,
                                "warehouse_area_id" =>  $warehouseId,
                                "staff_id" => Auth::id(),
                                "previous_status" => $item['status'],
                                "status" => $item['status'],
                                "unit_number" => $item['received_unit_number'],
                                'weight_staff' => $item['weight'] ?? null,
                                'height_staff' => $item['height'] ?? null,
                                'length_staff' => $item['length'] ?? null,
                                'width_staff' => $item['width'] ?? null,
                                "previous_created_at" => null,
                                'stage' => 'staff - request detail ' . $userRequest['mRequestType']['name']
                            ]);

                            array_push($listUsedId, $id);

                            $ids = $ids ? $ids.','.$id : $id;

                            $numberForAdd = $numberForAdd + 1;
                        }
                    }
                } else {
                    foreach ($request['package'] as $item) {

                        if(!in_array($item['id'], $listUsedId)) {
                            $ids = $ids ? $ids.','.$item['id'] : $item['id'];
                            array_push($listUsedId, $item['id']);

                            $package = Package::find($item['id']);
                            $warehouse = $package->warehouse_area_id;
                            $status = $package->status;

                            if(isset($item['warehouse_area_name']) && $item['warehouse_area_name'] != "null") {
                                $area = WarehouseArea::where('name', $item['warehouse_area_name'])->first();

                                $warehouse = $area->id;
                            }

                            if(isset($item['status']) && $item['warehouse_area_name'] != "null") {
                                $status = $item['status'];
                            }

                            if($userRequest['mRequestType']['name'] == 'outbound') {
                                $status = Package::STATUS_OUTBOUND;
                            }

                            $package->warehouse_area_id = $warehouse;
                            $package->status = $status;
                            $package->save();

                            $lastHistory = PackageHistory::where('package_id', $item['id'])->orderByDesc('created_at')->first();

                            PackageHistory::create([
                                "barcode" => $package['barcode'],
                                "previous_barcode" => $package['barcode'],
                                "package_id" => $item['id'],
                                "warehouse_area_id" =>  $warehouse,
                                "staff_id" => Auth::id(),
                                "previous_status" => $package['status'],
                                "status" => $status,
                                "unit_number" =>  $package['unit_number'],
                                'weight_staff' => $package['weight'],
                                'height_staff' => $package['height'],
                                'length_staff' => $package['length'],
                                'width_staff' => $package['width'],
                                'weight' => $lastHistory->weight ?? null,
                                'height' => $lastHistory->height ?? null,
                                'length' => $lastHistory->length ?? null,
                                'width' => $lastHistory->width ?? null,
                                "previous_created_at" => $lastHistory->created_at,
                                'stage' => 'staff - request detail ' . $userRequest['mRequestType']['name']
                            ]);

                            $ids = $ids ? $ids.','.$item['id'] : $item['id'];

                            $numberForAdd = $numberForAdd + 1;
                        }
                    }
                }
            } else {
                if($userRequest['mRequestType']['name'] == "add package" && isset($userRequest['is_allow'])) {
                    // if($requestPackage->package_number < $requestPackage->received_package_number + count($request['package'])) {
                    //     DB::rollBack();

                    //     throw new Exception("Out of limit");
                    // }

                    foreach ($request['package'] as $item) {
                        if(isset($item['save'])) {
                            $package = Package::find($item['id']);

                            $warehouseId = null;
                            if(isset($item['warehouse'])) {
                                $area = WarehouseArea::where('name', $item['warehouse'])->first();
                                $warehouseId = $area->id;
                            }

                            $package->warehouse_area_id = $warehouseId;
                            $package->received_unit_number = $package->unit_number;
                            $package->status = Package::STATUS_STORED;

                            $package->save();

                            PackageHistory::create([
                                "barcode" => $package['barcode'],
                                "previous_barcode" => $package['barcode'],
                                "package_id" => $item['id'],
                                "warehouse_area_id" =>  $warehouseId,
                                "staff_id" => Auth::id(),
                                "previous_status" => $package['status'],
                                "status" => Package::STATUS_STORED,
                                "unit_number" => $package['received_unit_number'],
                                'weight_staff' => $package['weight'] ?? null,
                                'height_staff' => $package['height'] ?? null,
                                'length_staff' => $package['length'] ?? null,
                                'width_staff' => $package['width'] ?? null,
                                'weight' => $package['weight_user'] ?? null,
                                'height' => $package['height_user'] ?? null,
                                'length' => $package['length_user'] ?? null,
                                'width' => $package['width_user'] ?? null,
                                "previous_created_at" => null,
                                'stage' => 'staff - request detail ' . $userRequest['mRequestType']['name']
                            ]);

                            $unitForAdd = $unitForAdd + (int)$package['received_unit_number'];

                            if(!in_array($request['id'], $listUsedId)) {
                                $ids = $ids ? $ids.','.$request['id'] : $request['id'];
                                array_push($listUsedId, $request['id']);
                            }

                            $historyData = [
                                'package_id' => $request['id'],
                                'staff_id' => Auth::id(),
                                'unit_number' => $package['unit_number'],
                            ];

                            RequestHistory::create($historyData);
                        }
                    }
                } else {

                    if($userRequest['mRequestType']['name'] == "add package") {
                        if($requestPackage->package_number < $requestPackage->received_package_number + count($request['package'])) {
                            DB::rollBack();

                            throw new Exception("Out of limit");
                        }
                    } else {
                        $count = 0;
                        foreach ($request['package'] as $package) {
                            $count = $count + $package['received_unit_number'];
                        }

                        if($requestPackage->unit_number < $requestPackage->received_package_number + $count) {
                            DB::rollBack();

                            throw new Exception("Out of limit");
                        }
                    }

                    foreach ($request['package'] as $package) {
                        if(isset($package['save'])) {

                            $packageGroup = PackageGroup::withTrashed()->find($request['package_group_id']);
                            $numberForAdd = $numberForAdd + 1;

                            $requestPackage = RequestPackage::find($request['request_package_id']);
                            $barcode = $this->generateBarcodeNumber($barcodeList, $packageGroup['name'], $package['unit_number']);
                            array_push($barcodeList, $barcode);

                            $warehouseId = null;
                            if(isset($package['warehouse'])) {
                                $area = WarehouseArea::where('name', $package['warehouse'])->first();
                                $warehouseId = $area->id;
                            }

                            $insertData = [
                                'package_group_id' => $request['package_group_id'],
                                'status' => $package['status'],
                                'user_id' => $request['user_id'],
                                'warehouse_area_id' => $warehouseId,
                                'unit_number' => $package['unit_number'],
                                'received_unit_number' => $package['received_unit_number'],
                                'barcode' => $barcode,
                                'weight_staff' => $package['weight'] ?? null,
                                'height_staff' => $package['height'] ?? null,
                                'length_staff' => $package['length'] ?? null,
                                'width_staff' => $package['width'] ?? null,
                                'weight' => $package['weight_user'] ?? null,
                                'height' => $package['height_user'] ?? null,
                                'length' => $package['length_user'] ?? null,
                                'width' => $package['width_user'] ?? null,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];

                            if(in_array($userRequest['mRequestType']['name'], ["removal", "return"])) {
                                $insertData['unit_barcode'] = $requestPackage['barcode'];
                            }

                            $newPackage = Package::create($insertData);

                            $id = $newPackage->id;

                            PackageDetail::create([
                                'package_group_id' => $request['package_group_id'],
                                'package_id' => $newPackage->id,
                                'unit_number' => $package['unit_number'],
                                'received_unit_number' => $package['received_unit_number'],
                            ]);

                            PackageHistory::create([
                                "barcode" => $barcode,
                                "previous_barcode" => $barcode,
                                "package_id" => $id,
                                "warehouse_area_id" =>  $warehouseId,
                                "staff_id" => Auth::id(),
                                "previous_status" => $package['status'],
                                "status" => $package['status'],
                                "unit_number" => $package['received_unit_number'],
                                'weight_staff' => $package['weight'] ?? null,
                                'height_staff' => $package['height'] ?? null,
                                'length_staff' => $package['length'] ?? null,
                                'width_staff' => $package['width'] ?? null,
                                'weight' => $package['weight_user'] ?? null,
                                'height' => $package['height_user'] ?? null,
                                'length' => $package['length_user'] ?? null,
                                'width' => $package['width_user'] ?? null,
                                "previous_created_at" => null,
                                'stage' => 'staff - request detail ' . $userRequest['mRequestType']['name']
                            ]);

                            $unitForAdd = $unitForAdd + (int)$package['received_unit_number'];

                            if(!in_array($id, $listUsedId)) {
                                $ids = $ids ? $ids.','.$id : $id;
                                array_push($listUsedId, $id);
                            }
                        }
                    }
                }
            }

            if($userRequest['mRequestType']['name'] != "add package" ||
            ($userRequest['mRequestType']['name'] == "add package" && !isset($userRequest['is_allow']))) {
                $historyData = [
                    'request_package_id' => $request['request_package_id'],
                    'staff_id' => Auth::id(),
                    'package_number' => $numberForAdd,
                ];
            }

            $userRequest->packages = $userRequest->packages ? $userRequest->packages.','.$ids : $ids;

            $userRequest->save();

            if($userRequest['mRequestType']['name'] != "add package" ||
            ($userRequest['mRequestType']['name'] == "add package" && !isset($userRequest['is_allow']))) {
                $packageNumber = $requestPackage->received_package_number ?? 0;

                $requestPackage->received_package_number =  $packageNumber + $numberForAdd;

                if(in_array($userRequest['mRequestType']['name'], ["removal", "return", "add package"]))
                {
                    $requestPackage->received_unit_number = $requestPackage->received_unit_number + $unitForAdd;
                    $historyData['unit_number'] = $unitForAdd;
                }

                $requestPackage->save();

                RequestHistory::create($historyData);
            }



            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    function setTimeForRequest($request) {
        DB::beginTransaction();

        try {
            $now = Carbon::now();
            $workingTime = RequestWorkingTime::where('user_request_id', $request['user_request_id'])->whereNull('finish_at')->orderByDesc('created_at')->first();
            if($workingTime) {
                $workingTime->finish_at = now();
                $workingTime->save();
                $hour = round($this->differenceInHours($workingTime->start_at, now()), 2);

                $requestHour = RequestTimeHistory::where('user_request_id', $request['user_request_id'])->first();
                if(empty($requestHour)) {
                    RequestTimeHistory::create([
                        'user_request_id' => $request['user_request_id'],
                        'hour' => $hour
                    ]);
                } else {
                    $requestHour->hour = $requestHour->hour + $hour;
                    $requestHour->save();
                }
            } else {
                RequestWorkingTime::create([
                    'user_request_id' => $request['user_request_id'],
                    'start_at' => $now
                ]);
            }
            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    function differenceInHours($startDate,$endDate){
        $startTimestamp = strtotime($startDate);
        $endTimestamp = strtotime($endDate);
        $difference = abs($endTimestamp - $startTimestamp) / 3600;

        return $difference;
    }
 }
