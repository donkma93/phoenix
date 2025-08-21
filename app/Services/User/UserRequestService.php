<?php

namespace App\Services\User;

use App\Models\Inventory;
use App\Models\MRequestType;
use App\Models\OrderAddress;
use App\Models\Package;
use App\Models\PackageDetail;
use App\Models\PackageGroup;
use App\Models\Product;
use App\Models\RequestPackage;
use App\Models\RequestPackageGroup;
use App\Models\RequestPackageImage;
use App\Models\RequestPackageTracking;
use App\Models\User;
use App\Models\UserRequest;
use App\Notifications\UserRequestDone;
use App\Services\UserBaseServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;

class UserRequestService extends UserBaseService implements UserBaseServiceInterface
{
    public function index($input)
    {
        $userRequestBuilder = UserRequest::where('user_id', Auth::id())
            ->with(['mRequestType', 'requestPackageGroups.packageGroupWithTrashed', 'requestPackageGroups.requestPackages', 'requestPackageGroups.requestPackageTrackings', 'requestPackageGroups.requestPackageImages', 'requestPackages']);
        if (isset($input['status'])) {
            $userRequestBuilder = $userRequestBuilder->where('status', $input['status']);
        }

        if (isset($input['type'])) {
            $userRequestBuilder = $userRequestBuilder->whereHas('mRequestType', function ($query) use ($input) {
                $query->where('name', $input['type']);
            });
        }

        $userRequest = $userRequestBuilder->with(['mRequestType', 'staff.profile'])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
        $requestTypes = MRequestType::pluck('name')->toArray();

      
        
        return [
            'userRequests' => $userRequest,
            'requestTypes' => $requestTypes,
            'oldInput' => $input
        ];
    }

    public function show($userRequestId)
    {
        $userRequest = UserRequest::where('user_id', Auth::id())
            ->with(['mRequestType', 'requestPackageGroups.packageGroupWithTrashed', 'requestPackageGroups.requestPackages', 'requestPackageGroups.requestPackageTrackings', 'requestPackageGroups.requestPackageImages'])
            ->findOrFail($userRequestId);

        $requestType =  $userRequest->mRequestType->name;

        $packages = Package::with(['packageDetails'])
            ->where('user_request_id', $userRequestId)
            ->get();

        return [
            'userRequest' => $userRequest,
            'type' => $requestType,
            'packages' => $packages,
        ];
    }

    public function create()
    {
        $requestTypes = MRequestType::all()->mapWithKeys(function ($request) {
            return [$request['id'] => $request['name']];
        });

        $packageGroups = PackageGroup::where('user_id', Auth::id())
            ->with(['packages' => function ($q) {
                $q->where('user_id', Auth::id())
                    ->where('unit_number', '>', 0)
                    ->whereIn('status', [Package::STATUS_INBOUND, Package::STATUS_STORED])
                    ->orderBy('unit_number');
            }])->orderBy('name')->get()->map(function ($item) {
                $count = $item->packages->pluck('unit_number')->countBy()->toArray();
                $mapItem = $item->toArray();
                $mapItem['packages'] = $count;
                return $mapItem;
            })->toArray();

        $unitPackageGroups = [];
        foreach ($packageGroups as $packageGroup) {
            if (count($packageGroup['packages'])) {
                // map packages
                $packages = [];
                foreach($packageGroup['packages'] as $unitNumber => $total) {
                    $packages[] = [
                        'unit_number' => $unitNumber,
                        'total' => $total
                    ];
                }
                $packageGroup['packages'] = $packages;

                $unitPackageGroups[$packageGroup['id']] = $packageGroup;
            }
        }

        return [
            'requestTypes' => $requestTypes,
            'packageGroups' => $packageGroups,
            'unitPackageGroups' => $unitPackageGroups
        ];
    }

    public function storeAddPackage($params)
    {
        try {
            DB::beginTransaction();

            $request = MRequestType::where('name', "add package")->firstOrFail();

            $params['sender']['user_id'] = Auth::id();
            $params['recipient']['user_id'] = Auth::id();

            $addressFrom = OrderAddress::create($params['sender']);
            $addressTo = OrderAddress::create($params['recipient']);

            $userRequest = [
                'user_id' => Auth::id(),
                'm_request_type_id' => $request->id,
                'status' => UserRequest::STATUS_NEW,

                'address_from_id' => $addressFrom->id,
                'address_to_id' => $addressTo->id,

                'packing_type' => $params['packing_type'],
                'prep_type' => $params['prep_type'],
                'label_by_type' => $params['label_by_type'],
                'store_type' => $params['store_type'],
                'ship_coming' => $params['ship_coming'],
                'ship_mode' => $params['ship_mode'],

                'is_allow' => false,
                'note' => $params['note']
            ];

            $res = UserRequest::create($userRequest);

            $now = Carbon::now();
            $newRequestPackages = [];
            $newTrackingUrls = [];
            $newImageUrls = [];

            $totalPackage = $params['package']['package_number'];
            $totalUnit = 0;
            $packageDetailMap = [];

            if (isset($params['package_group'])) {
                foreach ($params['package_group'] as $packageGroup) {
                    $rpg = RequestPackageGroup::create([
                        'user_request_id' => $res->id,
                        'package_group_id' => $packageGroup['id'],
                    ]);

                    foreach ($packageGroup['info'] as $info) {
                        if ($params['size_type'] == UserRequest::SIZE_CM) {
                            if (isset($info['package_width'])) {
                                $info['package_width'] = cm2inch($info['package_width']);
                            }

                            if (isset($info['package_height'])) {
                                $info['package_height'] = cm2inch($info['package_height']);
                            }

                            if (isset($info['package_length'])) {
                                $info['package_length'] = cm2inch($info['package_length']);
                            }
                        }

                        if ($params['weight_type'] == UserRequest::WEIGHT_KG && isset($info['package_weight'])) {
                            $info['package_weight'] = kg2pound($info['package_weight']);
                        }

                        // $totalPackage += $info['package_number'];

                        // $newRequestPackages[] = [
                        //     'request_package_group_id' => $rpg->id,
                        //     'package_number' => $info['package_number'],
                        //     'unit_number' => $info['unit_number'],
                        //     'width' => $info['package_width'] ?? null,
                        //     'weight' => $info['package_weight'] ?? null,
                        //     'height' => $info['package_height'] ?? null,
                        //     'length' => $info['package_length'] ?? null,
                        //     'created_at' => $now,
                        //     'updated_at' => $now
                        // ];

                        // Unit number
                        if (isset($packageDetailMap[$packageGroup['id']])) {
                            $packageDetailMap[$packageGroup['id']] += $info['unit_number'];
                        } else {
                            $packageDetailMap[$packageGroup['id']] = $info['unit_number'];
                        }

                        $totalUnit += $info['unit_number'];
                    }

                    if (isset($packageGroup['tracking_url'])) {
                        foreach ($packageGroup['tracking_url'] as $trackingUrl) {
                            $newTrackingUrls[] = [
                                'request_package_group_id' => $rpg->id,
                                'tracking_url' => $trackingUrl,
                                'created_at' => $now,
                                'updated_at' => $now
                            ];
                        }
                    }

                    if (isset($packageGroup['file_unit'])) {
                        foreach ($packageGroup['file_unit'] as $fileUnit) {
                            $imageUrl = $fileUnit->move('imgs' . DIRECTORY_SEPARATOR . RequestPackageImage::IMG_FOLDER, cleanName($fileUnit->getClientOriginalName()));

                            $newImageUrls[] = [
                                'request_package_group_id' => $rpg->id,
                                'image_url' => $imageUrl,
                                'created_at' => $now,
                                'updated_at' => $now
                            ];
                        }
                    }
                }
            }

            if (isset($params['new_package_group'])) {
                foreach ($params['new_package_group'] as $packageGroup) {
                    $packageGroup['user_id'] = Auth::id();
                    $packageGroup['barcode'] = $packageGroup['barcode'] ?? null;

                    if (isset($packageGroup['file_barcode'])) {
                        $packageGroup['file'] = $packageGroup['file_barcode']->move('imgs' . DIRECTORY_SEPARATOR . PackageGroup::FILE_FOLDER, cleanName($packageGroup['file_barcode']->getClientOriginalName()));
                    } else if ($packageGroup['barcode'] == null) {
                        $packageGroup['barcode'] = $this->generateBarcodeNumber();
                    }

                    if ($params['size_type'] == UserRequest::SIZE_CM) {
                        if (isset($packageGroup['unit_width'])) {
                            $packageGroup['unit_width'] = cm2inch($packageGroup['unit_width']);
                        }

                        if (isset($packageGroup['unit_height'])) {
                            $packageGroup['unit_height'] = cm2inch($packageGroup['unit_height']);
                        }

                        if (isset($packageGroup['unit_length'])) {
                            $packageGroup['unit_length'] = cm2inch($packageGroup['unit_length']);
                        }
                    }

                    if ($params['weight_type'] == UserRequest::WEIGHT_KG && isset($packageGroup['unit_weight'])) {
                        $packageGroup['unit_weight'] = kg2pound($packageGroup['unit_weight']);
                    }

                    // create new package group
                    $newPackageGroup = PackageGroup::create($packageGroup);



                    $rpg = RequestPackageGroup::create([
                        'user_request_id' => $res->id,
                        'package_group_id' => $newPackageGroup->id,
                    ]);

                    foreach ($packageGroup['info'] as $info) {
                        if ($params['size_type'] == UserRequest::SIZE_CM) {
                            if (isset($info['package_width'])) {
                                $info['package_width'] = cm2inch($info['package_width']);
                            }

                            if (isset($info['package_height'])) {
                                $info['package_height'] = cm2inch($info['package_height']);
                            }

                            if (isset($info['package_length'])) {
                                $info['package_length'] = cm2inch($info['package_length']);
                            }
                        }

                        if ($params['weight_type'] == UserRequest::WEIGHT_KG && isset($info['package_weight'])) {
                            $info['package_weight'] = kg2pound($info['package_weight']);
                        }

                        // $totalPackage += $info['package_number'];
                    
                        // $newRequestPackages[] = [
                        //     'request_package_group_id' => $rpg->id,
                        //     'package_number' => $info['package_number'],
                        //     'unit_number' => $info['unit_number'],
                        //     'width' => $info['package_width'] ?? null,
                        //     'weight' => $info['package_weight'] ?? null,
                        //     'height' => $info['package_height'] ?? null,
                        //     'length' => $info['package_length'] ?? null,
                        //     'created_at' => $now,
                        //     'updated_at' => $now
                        // ];

                        if (isset($packageDetailMap[$newPackageGroup->id])) {
                            $packageDetailMap[$newPackageGroup->id] += $info['unit_number'];
                        } else {
                            $packageDetailMap[$newPackageGroup->id] = $info['unit_number'];
                        }
                    }

                    if (isset($packageGroup['tracking_url'])) {
                        foreach ($packageGroup['tracking_url'] as $trackingUrl) {
                            $newTrackingUrls[] = [
                                'request_package_group_id' => $rpg->id,
                                'tracking_url' => $trackingUrl,
                                'created_at' => $now,
                                'updated_at' => $now
                            ];
                        }
                    }

                    $product = [
                        'name' => $newPackageGroup->name,
                        'status' => Product::STATUS_ACTIVE,
                        'package_group_id' => $newPackageGroup->id,
                        'user_id' => Auth::id(),
                        // image url
                    ];

                    if (isset($packageGroup['file_unit'])) {
                        foreach ($packageGroup['file_unit'] as $fileUnit) {
                            $imageUrl = $fileUnit->move('imgs' . DIRECTORY_SEPARATOR . RequestPackageImage::IMG_FOLDER, cleanName($fileUnit->getClientOriginalName()));
                            $product['image_url'] = $imageUrl;

                            $newImageUrls[] = [
                                'request_package_group_id' => $rpg->id,
                                'image_url' => $imageUrl,
                                'created_at' => $now,
                                'updated_at' => $now
                            ];
                        }
                    }

                    $newProduct = Product::create($product);

                    Inventory::create([
                        'product_id' => $newProduct->id,
                        'sku' => $this->generateSku(),
                    ]);
                }
            }

            $newPackageDetails = [];
            $targetPackageGroupId = array_key_first($packageDetailMap);

            $packagegroupDetail = [];

            foreach ($packageDetailMap as $pgId => $pgUnitNumber) {
                $pgDetail = PackageGroup::findOrFail($pgId);
                $packagegroupDetail[$pgId] = [
                    'packageGroupId' => $pgId,
                    'name' => $pgDetail->name,
                    'unit_number' => $pgUnitNumber,
                ];
            }

            $codes = [];
            $pgInfo = [];

            // Create Package
            for ($i = 0; $i < $totalPackage; $i++) {
                $packageBarcode = $this->generateSku();
                $codes[] = $packageBarcode;

                $newPackage = Package::create([
                    'user_request_id' => $res->id,
                    'user_id' => Auth::id(),
                    'package_group_id' => $targetPackageGroupId,
                    'unit_number' => $totalUnit,
                    'barcode' => $packageBarcode,
                    'weight' => $params['package']['package_weight'] ?? null,
                    'width' => $params['package']['package_width'] ?? null,
                    'height' => $params['package']['package_height'] ?? null,
                    'length' => $params['package']['package_length'] ?? null,
                ]);

                foreach ($packageDetailMap as $pgId => $pgUnitNumber) {
                    $newPackageDetails[] = [
                        'package_id' => $newPackage->id,
                        'package_group_id' => $pgId,
                        'unit_number' => $pgUnitNumber,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                }
            }

            if (count($newPackageDetails)) {
                PackageDetail::insert($newPackageDetails);
            }

            if (count($newRequestPackages)) {
                RequestPackage::insert($newRequestPackages);
            }

            if (count($newTrackingUrls)) {
                RequestPackageTracking::insert($newTrackingUrls);
            }

            if (count($newImageUrls)) {
                RequestPackageImage::insert($newImageUrls);
            }

            DB::commit();

            return [
                'default' => 15,
                'box' => [
                    'totalPackage' => $totalPackage,
                    'price' => 2,
                ],
                'codes' => $codes,
                'sender' => $params['sender'],
                'recipient' => $params['recipient'],
                'date' => $now->format('Y-m-d'),
                'packagegroupDetail' => $packagegroupDetail,
            ];
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function createSkuAddPackage($number)
    {
        $codes = [];

        for ($i = 0; $i < $number; $i++) {
            do {
                $code = $this->generateBarcodeNumber();
            } while (in_array($code, $codes));

            $codes[] = $code;
        }

        return $codes;
    }

    public function outbound()
    {
        $packageGroups = PackageGroup::where('user_id', Auth::id())
            ->whereNotNull('unit_width')
            ->whereNotNull('unit_weight')
            ->whereNotNull('unit_height')
            ->whereNotNull('unit_length')
            ->with(['packages' => function ($q) {
                $q->where('user_id', Auth::id())
                    ->where('unit_number', '>', 0)
                    ->whereIn('status', [Package::STATUS_INBOUND, Package::STATUS_STORED])
                    ->orderBy('unit_number');
            }])->orderBy('name')
            ->get()->map(function ($item) {
                $count = $item->packages->pluck('unit_number')->countBy()->toArray();
                $mapItem = $item->toArray();
                $mapItem['packages'] = $count;
                return $mapItem;
            })->toArray();

        $unitPackageGroups = [];
        foreach ($packageGroups as $packageGroup) {
            if (count($packageGroup['packages'])) {
                // map packages
                $packages = [];
                foreach($packageGroup['packages'] as $unitNumber => $total) {
                    $packages[] = [
                        'unit_number' => $unitNumber,
                        'total' => $total
                    ];
                }
                $packageGroup['packages'] = $packages;

                $unitPackageGroups[$packageGroup['id']] = $packageGroup;
            }
        }

        // TODO: number pallet 40x48x67

        return [
            'packageGroups' => $packageGroups,
            'unitPackageGroups' => $unitPackageGroups
        ];
    }

    public function storeOutbound($params)
    {
        try {
            DB::beginTransaction();
            $request = MRequestType::where('name', "outbound")->firstOrFail();

            $userRequest = [
                'user_id' => Auth::id(),
                'm_request_type_id' => $request->id,
                'status' => UserRequest::STATUS_NEW,
                'is_allow' => false,
            ];
            $res = UserRequest::create($userRequest);

            // so luong package dung file tinh phi
            $totalPackage = 0;

            foreach ($params['group'] as $key => $group) {
                $barcode = $group['barcode'] ?? null;
                $file = null;

                if (isset($group['file'])) {
                    $file = $group['file']->move('imgs' . DIRECTORY_SEPARATOR . UserRequest::IMG_FOLDER, cleanName($group['file']->getClientOriginalName()));
                } else if ($barcode == null) {
                    $barcode = $this->generateBarcodeNumber();
                }

                $rpg = [
                    'user_request_id' => $res->id,
                    'package_group_id' => $group['id'],
                    'quantity' => $group['pallet'] ?? null,
                    'barcode' => $barcode,
                    'file' => $file,
                    'ship_mode' => $group['ship_mode'],
                ];

                if ($group['ship_mode'] == UserRequest::SMALL_PARCEL) {
                    $hasInsurance = isset($group['is_insurance']) ? 1 : 0;
                    $rpg['is_insurance'] = $hasInsurance;
                    $rpg['insurance_fee'] = $hasInsurance ? $group['insurance_fee'] : null;

                    // if ($rpg['file'] == null) {
                    //     throw new Exception(Auth::id() . 'create ship mode = SMALL_PARCEL required file');
                    // }
                }

                $newRequestPackageGroup = RequestPackageGroup::create($rpg);

                RequestPackage::create([
                    'request_package_group_id' => $newRequestPackageGroup->id,
                    'package_number' => $group['package_number'],
                    'unit_number' => $group['unit_number'],
                ]);

                if ($rpg['file'] != null) {
                    $totalPackage += $group['package_number'];
                }
            }

            DB::commit();

            $count = count($params['group']);


            return [
                'default' => 15,
                'sku' => [
                    'count' => $count,
                    'fee' => $count > 1 ? $count * 5 : 0,
                ],
                'pallet' => null, //  4$ mặc định label
                'pdf' => [
                    'count' => $totalPackage,
                    'fee' => $totalPackage * 1 // 1$ 1 label
                ]
            ];
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function store($params)
    {
        try {
            DB::beginTransaction();

            $request =  MRequestType::where('id', $params['m_request_type_id'])->firstOrFail();
            $userRequest = [
                'user_id' => Auth::id(),
                'm_request_type_id' => $params['m_request_type_id'],
                'status' => UserRequest::STATUS_NEW,
                'note' => $params['note']
            ];

            if ($request->name == "warehouse labor") {
                $userRequest['option'] = $params['option'];
            }

            $res = UserRequest::create($userRequest);

            $now = Carbon::now();
            $newRequestPackages = [];
            $newTrackingUrls = [];
            $newImageUrls = [];

            if (in_array($request->name, ["relabel", "repack", "outbound", "warehouse labor"])) {
                if (isset($params['unit_group'])) {
                    foreach ($params['unit_group'] as $packageGroup) {
                        $barcode = null;
                        $file = null;

                        if ($request->name != "repack") {
                            $barcode = $packageGroup['barcode'] ?? null;

                            if (isset($packageGroup['file_barcode'])) {
                                $file = $packageGroup['file_barcode']->move('imgs' . DIRECTORY_SEPARATOR . UserRequest::IMG_FOLDER, cleanName($packageGroup['file_barcode']->getClientOriginalName()));
                            } else if ($barcode == null) {
                                $barcode = $this->generateBarcodeNumber();
                            }
                        }

                        $rpg = RequestPackageGroup::create([
                            'user_request_id' => $res->id,
                            'package_group_id' => $packageGroup['id'],
                            'barcode' => $barcode,
                            'file' => $file
                        ]);

                        foreach($packageGroup['info'] as $info) {
                            $newRequestPackages[] = [
                                'request_package_group_id' => $rpg->id,
                                'package_number' => $info['package_number'],
                                'unit_number' => $info['unit_number'],
                                'created_at' => $now,
                                'updated_at' => $now
                            ];
                        }
                    }
                }
            }

            if ($request->name == "add package") {
                if (isset($params['package_group'])) {
                    foreach ($params['package_group'] as $packageGroup) {
                        $rpg = RequestPackageGroup::create([
                            'user_request_id' => $res->id,
                            'package_group_id' => $packageGroup['id'],
                        ]);

                        foreach ($packageGroup['info'] as $info) {
                            if ($params['size_type'] == UserRequest::SIZE_CM) {
                                if (isset($info['package_width'])) {
                                    $info['package_width'] = cm2inch($info['package_width']);
                                }

                                if (isset($info['package_height'])) {
                                    $info['package_height'] = cm2inch($info['package_height']);
                                }

                                if (isset($info['package_length'])) {
                                    $info['package_length'] = cm2inch($info['package_length']);
                                }
                            }

                            if ($params['weight_type'] == UserRequest::WEIGHT_KG && isset($info['package_weight'])) {
                                $info['package_weight'] = kg2pound($info['package_weight']);
                            }

                            $newRequestPackages[] = [
                                'request_package_group_id' => $rpg->id,
                                'package_number' => $info['package_number'],
                                'unit_number' => $info['unit_number'],
                                'width' => $info['package_width'] ?? null,
                                'weight' => $info['package_weight'] ?? null,
                                'height' => $info['package_height'] ?? null,
                                'length' => $info['package_length'] ?? null,
                                'created_at' => $now,
                                'updated_at' => $now
                            ];
                        }

                        if (isset($packageGroup['tracking_url'])) {
                            foreach ($packageGroup['tracking_url'] as $trackingUrl) {
                                $newTrackingUrls[] = [
                                    'request_package_group_id' => $rpg->id,
                                    'tracking_url' => $trackingUrl,
                                    'created_at' => $now,
                                    'updated_at' => $now
                                ];
                            }
                        }

                        if (isset($packageGroup['file_unit'])) {
                            foreach ($packageGroup['file_unit'] as $fileUnit) {
                                $imageUrl = $fileUnit->move('imgs' . DIRECTORY_SEPARATOR . RequestPackageImage::IMG_FOLDER, cleanName($fileUnit->getClientOriginalName()));

                                $newImageUrls[] = [
                                    'request_package_group_id' => $rpg->id,
                                    'image_url' => $imageUrl,
                                    'created_at' => $now,
                                    'updated_at' => $now
                                ];
                            }
                        }
                    }
                }

                if (isset($params['new_package_group'])) {
                    foreach ($params['new_package_group'] as $packageGroup) {
                        $packageGroup['user_id'] = Auth::id();
                        $packageGroup['barcode'] = $packageGroup['barcode'] ?? null;

                        if (isset($packageGroup['file_barcode'])) {
                            $packageGroup['file'] = $packageGroup['file_barcode']->move('imgs' . DIRECTORY_SEPARATOR . PackageGroup::FILE_FOLDER, cleanName($packageGroup['file_barcode']->getClientOriginalName()));
                        } else if ($packageGroup['barcode'] == null) {
                            $packageGroup['barcode'] = $this->generateBarcodeNumber();
                        }

                        if ($params['size_type'] == UserRequest::SIZE_CM) {
                            if (isset($packageGroup['unit_width'])) {
                                $packageGroup['unit_width'] = cm2inch($packageGroup['unit_width']);
                            }

                            if (isset($packageGroup['unit_height'])) {
                                $packageGroup['unit_height'] = cm2inch($packageGroup['unit_height']);
                            }

                            if (isset($packageGroup['unit_length'])) {
                                $packageGroup['unit_length'] = cm2inch($packageGroup['unit_length']);
                            }
                        }

                        if ($params['weight_type'] == UserRequest::WEIGHT_KG && isset($packageGroup['unit_weight'])) {
                            $packageGroup['unit_weight'] = kg2pound($packageGroup['unit_weight']);
                        }

                        $newPackageGroup = PackageGroup::create($packageGroup);

                        $newProduct = Product::create([
                            'name' => $newPackageGroup->name,
                            'status' => Product::STATUS_ACTIVE,
                            'package_group_id' => $newPackageGroup->id,
                            'user_id' => Auth::id(),
                        ]);

                        Inventory::create([
                            'product_id' => $newProduct->id,
                            'sku' => $this->generateSku(),
                        ]);

                        $rpg = RequestPackageGroup::create([
                            'user_request_id' => $res->id,
                            'package_group_id' => $newPackageGroup->id,
                        ]);

                        foreach ($packageGroup['info'] as $info) {
                            if ($params['size_type'] == UserRequest::SIZE_CM) {
                                if (isset($info['package_width'])) {
                                    $info['package_width'] = cm2inch($info['package_width']);
                                }

                                if (isset($info['package_height'])) {
                                    $info['package_height'] = cm2inch($info['package_height']);
                                }

                                if (isset($info['package_length'])) {
                                    $info['package_length'] = cm2inch($info['package_length']);
                                }
                            }

                            if ($params['weight_type'] == UserRequest::WEIGHT_KG && isset($info['package_weight'])) {
                                $info['package_weight'] = kg2pound($info['package_weight']);
                            }

                            $newRequestPackages[] = [
                                'request_package_group_id' => $rpg->id,
                                'package_number' => $info['package_number'],
                                'unit_number' => $info['unit_number'],
                                'width' => $info['package_width'] ?? null,
                                'weight' => $info['package_weight'] ?? null,
                                'height' => $info['package_height'] ?? null,
                                'length' => $info['package_length'] ?? null,
                                'created_at' => $now,
                                'updated_at' => $now
                            ];
                        }

                        if (isset($packageGroup['tracking_url'])) {
                            foreach ($packageGroup['tracking_url'] as $trackingUrl) {
                                $newTrackingUrls[] = [
                                    'request_package_group_id' => $rpg->id,
                                    'tracking_url' => $trackingUrl,
                                    'created_at' => $now,
                                    'updated_at' => $now
                                ];
                            }
                        }

                        if (isset($packageGroup['file_unit'])) {
                            foreach ($packageGroup['file_unit'] as $fileUnit) {
                                $imageUrl = $fileUnit->move('imgs' . DIRECTORY_SEPARATOR . RequestPackageImage::IMG_FOLDER, cleanName($fileUnit->getClientOriginalName()));

                                $newImageUrls[] = [
                                    'request_package_group_id' => $rpg->id,
                                    'image_url' => $imageUrl,
                                    'created_at' => $now,
                                    'updated_at' => $now
                                ];
                            }
                        }
                    }
                }
            }

            if ($request->name == "removal") {
                if (isset($params['removal_group'])) {
                    foreach ($params['removal_group'] as $packageGroup) {
                        $packageGroup['unit_barcode'] = $packageGroup['unit_barcode'] ?? null;
                        $file = null;

                        if (isset($packageGroup['file_unit_barcode'])) {
                            $file = $packageGroup['file_unit_barcode']->move('imgs' . DIRECTORY_SEPARATOR . UserRequest::IMG_FOLDER, cleanName($packageGroup['file_unit_barcode']->getClientOriginalName()));
                        } else if ($packageGroup['unit_barcode'] == null) {
                            $packageGroup['unit_barcode'] = $this->generateBarcodeNumber();
                        }

                        $rpg = RequestPackageGroup::create([
                            'user_request_id' => $res->id,
                            'package_group_id' => $packageGroup['id'],
                            'barcode' => $packageGroup['unit_barcode'],
                            'file' => $file
                        ]);

                        $newRequestPackages[] = [
                            'request_package_group_id' => $rpg->id,
                            'unit_number' => $packageGroup['unit_number'],
                            'created_at' => $now,
                            'updated_at' => $now
                        ];

                        // if (isset($packageGroup['tracking_url'])) {
                        //     foreach ($packageGroup['tracking_url'] as $trackingUrl) {
                        //         $newTrackingUrls[] = [
                        //             'request_package_group_id' => $rpg->id,
                        //             'tracking_url' => $trackingUrl,
                        //             'created_at' => $now,
                        //             'updated_at' => $now
                        //         ];
                        //     }
                        // }

                        if (isset($packageGroup['file_unit'])) {
                            foreach ($packageGroup['file_unit'] as $fileUnit) {
                                $imageUrl = $fileUnit->move('imgs' . DIRECTORY_SEPARATOR . RequestPackageImage::IMG_FOLDER, cleanName($fileUnit->getClientOriginalName()));

                                $newImageUrls[] = [
                                    'request_package_group_id' => $rpg->id,
                                    'image_url' => $imageUrl,
                                    'created_at' => $now,
                                    'updated_at' => $now
                                ];
                            }
                        }
                    }
                }

                if (isset($params['removal_new_group'])) {
                    foreach ($params['removal_new_group'] as $packageGroup) {
                        $packageGroup['user_id'] = Auth::id();
                        $packageGroup['barcode'] = $packageGroup['barcode'] ?? null;

                        if (isset($packageGroup['file_barcode'])) {
                            $packageGroup['file'] = $packageGroup['file_barcode']->move('imgs' . DIRECTORY_SEPARATOR . PackageGroup::FILE_FOLDER, cleanName($packageGroup['file_barcode']->getClientOriginalName()));
                        } else if ($packageGroup['barcode'] == null) {
                            $packageGroup['barcode'] = $this->generateBarcodeNumber();
                        }

                        if ($params['size_type'] == UserRequest::SIZE_CM) {
                            if (isset($packageGroup['unit_width'])) {
                                $packageGroup['unit_width'] = cm2inch($packageGroup['unit_width']);
                            }

                            if (isset($packageGroup['unit_height'])) {
                                $packageGroup['unit_height'] = cm2inch($packageGroup['unit_height']);
                            }

                            if (isset($packageGroup['unit_length'])) {
                                $packageGroup['unit_length'] = cm2inch($packageGroup['unit_length']);
                            }
                        }

                        if ($params['weight_type'] == UserRequest::WEIGHT_KG && isset($packageGroup['unit_weight'])) {
                            $packageGroup['unit_weight'] = kg2pound($packageGroup['unit_weight']);
                        }

                        $newPackageGroup = PackageGroup::create($packageGroup);

                        $newProduct = Product::create([
                            'name' => $newPackageGroup->name,
                            'status' => Product::STATUS_ACTIVE,
                            'package_group_id' => $newPackageGroup->id,
                            'user_id' => Auth::id(),
                        ]);

                        Inventory::create([
                            'product_id' => $newProduct->id,
                            'sku' => $this->generateSku(),
                        ]);

                        // Create RequestPackageGroup
                        $packageGroup['unit_barcode'] = $packageGroup['unit_barcode'] ?? null;
                        $file = null;

                        if (isset($packageGroup['file_unit_barcode'])) {
                            $file = $packageGroup['file_unit_barcode']->move('imgs' . DIRECTORY_SEPARATOR . UserRequest::IMG_FOLDER, cleanName($packageGroup['file_unit_barcode']->getClientOriginalName()));
                        } else if ($packageGroup['unit_barcode'] == null) {
                            $packageGroup['unit_barcode'] = $this->generateBarcodeNumber();
                        }

                        $rpg = RequestPackageGroup::create([
                            'user_request_id' => $res->id,
                            'package_group_id' => $newPackageGroup->id,
                            'barcode' => $packageGroup['unit_barcode'],
                            'file' => $file
                        ]);

                        $newRequestPackages[] = [
                            'request_package_group_id' => $rpg->id,
                            'unit_number' => $packageGroup['unit_number'],
                            'created_at' => $now,
                            'updated_at' => $now
                        ];

                        // if (isset($packageGroup['tracking_url'])) {
                        //     foreach ($packageGroup['tracking_url'] as $trackingUrl) {
                        //         $newTrackingUrls[] = [
                        //             'request_package_group_id' => $rpg->id,
                        //             'tracking_url' => $trackingUrl,
                        //             'created_at' => $now,
                        //             'updated_at' => $now
                        //         ];
                        //     }
                        // }

                        if (isset($packageGroup['file_unit'])) {
                            foreach ($packageGroup['file_unit'] as $fileUnit) {
                                $imageUrl = $fileUnit->move('imgs' . DIRECTORY_SEPARATOR . RequestPackageImage::IMG_FOLDER, cleanName($fileUnit->getClientOriginalName()));

                                $newImageUrls[] = [
                                    'request_package_group_id' => $rpg->id,
                                    'image_url' => $imageUrl,
                                    'created_at' => $now,
                                    'updated_at' => $now
                                ];
                            }
                        }
                    }
                }
            }

            if ($request->name == "return") {
                if (isset($params['return_group'])) {
                    foreach ($params['return_group'] as $packageGroup) {
                        $packageGroup['unit_barcode'] = $packageGroup['unit_barcode'] ?? null;
                        $file = null;

                        if (isset($packageGroup['file_unit_barcode'])) {
                            $file = $packageGroup['file_unit_barcode']->move('imgs' . DIRECTORY_SEPARATOR . UserRequest::IMG_FOLDER, cleanName($packageGroup['file_unit_barcode']->getClientOriginalName()));
                        } else if ($packageGroup['unit_barcode'] == null) {
                            $packageGroup['unit_barcode'] = $this->generateBarcodeNumber();
                        }

                        $rpg = RequestPackageGroup::create([
                            'user_request_id' => $res->id,
                            'package_group_id' => $packageGroup['id'],
                            'barcode' => $packageGroup['unit_barcode'],
                            'file' => $file
                        ]);

                        $newRequestPackages[] = [
                            'request_package_group_id' => $rpg->id,
                            'unit_number' => $packageGroup['unit_number'],
                            'created_at' => $now,
                            'updated_at' => $now
                        ];

                        if (isset($packageGroup['tracking_url'])) {
                            foreach ($packageGroup['tracking_url'] as $trackingUrl) {
                                $newTrackingUrls[] = [
                                    'request_package_group_id' => $rpg->id,
                                    'tracking_url' => $trackingUrl,
                                    'created_at' => $now,
                                    'updated_at' => $now
                                ];
                            }
                        }

                        if (isset($packageGroup['file_unit'])) {
                            foreach ($packageGroup['file_unit'] as $fileUnit) {
                                $imageUrl = $fileUnit->move('imgs' . DIRECTORY_SEPARATOR . RequestPackageImage::IMG_FOLDER, cleanName($fileUnit->getClientOriginalName()));

                                $newImageUrls[] = [
                                    'request_package_group_id' => $rpg->id,
                                    'image_url' => $imageUrl,
                                    'created_at' => $now,
                                    'updated_at' => $now
                                ];
                            }
                        }
                    }
                }

                if (isset($params['return_new_group'])) {
                    foreach ($params['return_new_group'] as $packageGroup) {
                        $packageGroup['user_id'] = Auth::id();
                        $packageGroup['barcode'] = $packageGroup['barcode'] ?? null;

                        if (isset($packageGroup['file_barcode'])) {
                            $packageGroup['file'] = $packageGroup['file_barcode']->move('imgs' . DIRECTORY_SEPARATOR . PackageGroup::FILE_FOLDER, cleanName($packageGroup['file_barcode']->getClientOriginalName()));
                        } else if ($packageGroup['barcode'] == null) {
                            $packageGroup['barcode'] = $this->generateBarcodeNumber();
                        }

                        if ($params['size_type'] == UserRequest::SIZE_CM) {
                            if (isset($packageGroup['unit_width'])) {
                                $packageGroup['unit_width'] = cm2inch($packageGroup['unit_width']);
                            }

                            if (isset($packageGroup['unit_height'])) {
                                $packageGroup['unit_height'] = cm2inch($packageGroup['unit_height']);
                            }

                            if (isset($packageGroup['unit_length'])) {
                                $packageGroup['unit_length'] = cm2inch($packageGroup['unit_length']);
                            }
                        }

                        if ($params['weight_type'] == UserRequest::WEIGHT_KG && isset($packageGroup['unit_weight'])) {
                            $packageGroup['unit_weight'] = kg2pound($packageGroup['unit_weight']);
                        }

                        $newPackageGroup = PackageGroup::create($packageGroup);

                        $newProduct = Product::create([
                            'name' => $newPackageGroup->name,
                            'status' => Product::STATUS_ACTIVE,
                            'package_group_id' => $newPackageGroup->id,
                            'user_id' => Auth::id(),
                        ]);

                        Inventory::create([
                            'product_id' => $newProduct->id,
                            'sku' => $this->generateSku(),
                        ]);

                        $packageGroup['unit_barcode'] = $packageGroup['unit_barcode'] ?? null;
                        $file = null;

                        if (isset($packageGroup['file_unit_barcode'])) {
                            $file = $packageGroup['file_unit_barcode']->move('imgs' . DIRECTORY_SEPARATOR . UserRequest::IMG_FOLDER, cleanName($packageGroup['file_unit_barcode']->getClientOriginalName()));
                        } else if ($packageGroup['unit_barcode'] == null) {
                            $packageGroup['unit_barcode'] = $this->generateBarcodeNumber();
                        }

                        $rpg = RequestPackageGroup::create([
                            'user_request_id' => $res->id,
                            'package_group_id' => $newPackageGroup->id,
                            'barcode' => $packageGroup['unit_barcode'],
                            'file' => $file
                        ]);

                        $newRequestPackages[] = [
                            'request_package_group_id' => $rpg->id,
                            'unit_number' => $packageGroup['unit_number'],
                            'created_at' => $now,
                            'updated_at' => $now
                        ];

                        if (isset($packageGroup['tracking_url'])) {
                            foreach ($packageGroup['tracking_url'] as $trackingUrl) {
                                $newTrackingUrls[] = [
                                    'request_package_group_id' => $rpg->id,
                                    'tracking_url' => $trackingUrl,
                                    'created_at' => $now,
                                    'updated_at' => $now
                                ];
                            }
                        }

                        if (isset($packageGroup['file_unit'])) {
                            foreach ($packageGroup['file_unit'] as $fileUnit) {
                                $imageUrl = $fileUnit->move('imgs' . DIRECTORY_SEPARATOR . RequestPackageImage::IMG_FOLDER, cleanName($fileUnit->getClientOriginalName()));

                                $newImageUrls[] = [
                                    'request_package_group_id' => $rpg->id,
                                    'image_url' => $imageUrl,
                                    'created_at' => $now,
                                    'updated_at' => $now
                                ];
                            }
                        }
                    }
                }
            }

            if (count($newRequestPackages)) {
                RequestPackage::insert($newRequestPackages);
            }

            if (count($newTrackingUrls)) {
                RequestPackageTracking::insert($newTrackingUrls);
            }

            if (count($newImageUrls)) {
                RequestPackageImage::insert($newImageUrls);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function edit($userRequestId)
    {
        $userRequest = UserRequest::where('user_id', Auth::id())
            ->whereHas('mRequestType', function ($query) {
                $query->where('name', "add package");
            })
            ->where('status', UserRequest::STATUS_NEW)
            ->with(['mRequestType', 'requestPackageGroups.packageGroup', 'requestPackageGroups.requestPackages', 'requestPackageGroups.requestPackageTrackings', 'requestPackageGroups.requestPackageImages'])
            ->findOrFail($userRequestId);

        // $userRequestType =  $userRequest->mRequestType->name;

        // $requestTypes = MRequestType::all()->mapWithKeys(function ($request) {
        //     return [$request['id'] => $request['name']];
        // });

        // $packageGroups = PackageGroup::where('user_id', Auth::id())
        //     ->with(['packages' => function ($q) {
        //         $q->where('user_id', Auth::id())
        //             ->where('unit_number', '>', 0)
        //             ->whereIn('status', [Package::STATUS_INBOUND, Package::STATUS_STORED])
        //             ->orderBy('unit_number');
        //     }])->get()->map(function ($item) {
        //         $count = $item->packages->pluck('unit_number')->countBy()->toArray();
        //         $mapItem = $item->toArray();
        //         $mapItem['packages'] = $count;
        //         return $mapItem;
        //     })->toArray();

        // $unitPackageGroups = [];
        // foreach ($packageGroups as $packageGroup) {
        //     if (count($packageGroup['packages'])) {
        //         // map packages
        //         $packages = [];
        //         foreach($packageGroup['packages'] as $unitNumber => $total) {
        //             $packages[] = [
        //                 'unit_number' => $unitNumber,
        //                 'total' => $total
        //             ];
        //         }
        //         $packageGroup['packages'] = $packages;

        //         $unitPackageGroups[$packageGroup['id']] = $packageGroup;
        //     }
        // }

        return [
            'userRequestId' => $userRequestId,
            'userRequest' => $userRequest,
            // 'requestTypes' => $requestTypes,
            // 'packageGroups' => $packageGroups,
            // 'unitPackageGroups' => $unitPackageGroups,
            // 'type' => $userRequestType
        ];
    }

    public function update($request)
    {
        try {
            DB::beginTransaction();

            $userRequest = UserRequest::where('user_id', Auth::id())
                ->whereHas('mRequestType', function ($query) {
                    $query->where('name', "add package");
                })
                ->where('status', UserRequest::STATUS_NEW)
                ->with(['requestPackageGroups.packageGroup', 'requestPackageGroups.requestPackages'])
                ->findOrFail($request['user_request_id']);

            $requestGroup = [];
            foreach ($request['package_group'] as $packageGroup) {
                foreach ($packageGroup['info'] as $info) {
                    $requestGroup[$info['rpId']] = [
                        "width" => $info["package_width"],
                        "weight" => $info["package_weight"],
                        "height" => $info["package_height"],
                        "length" => $info["package_length"],
                        "unit_number" => $info["unit_number"],
                        "package_number" => $info["package_number"],
                    ];
                }
            }

            $requestPackageGroups = $userRequest->requestPackageGroups;
            foreach ($requestPackageGroups as $requestPackageGroup) {
                foreach ($requestPackageGroup->requestPackages as $requestPackage) {
                    if (isset($requestGroup[$requestPackage->id])) {
                        RequestPackage::where('id', $requestPackage->id)
                            ->update($requestGroup[$requestPackage->id]);
                    }
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function notify($userRequestId)
    {
        $user = User::find(Auth::id());
        $notification = $user->notifications()->where('id', $userRequestId)->first();
        return $notification;
    }

    public function notifyList()
    {
        $user = User::find(Auth::id());
        $notifications = $user->unreadNotifications()
            ->where('type', UserRequestDone::class)
            ->get();
        return $notifications;
    }

    public function cancel($params)
    {
        $userRequest = UserRequest::find($params['id']);
        $userRequest->status = UserRequest::STATUS_CANCEL;
        $userRequest->save();
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
        return Package::where('barcode', $barcode)->orWhere('unit_barcode', $barcode)->exists() || PackageGroup::where('barcode', $barcode)->exists()
            || RequestPackage::where('barcode', $barcode)->exists() || RequestPackageGroup::where('barcode', $barcode)->exists();
    }

    function generateSku()
    {
        $sku = uniqid();

        if ($this->isSkuExist($sku)) {
            return $this->generateSku();
        }

        return $sku;
    }

    function isSkuExist($sku)
    {
        return Inventory::where('sku', $sku)->exists();
    }
}
