<?php

namespace App\Services\Staff;

use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\StaffBaseServiceInterface;
use App\Models\Package;
use App\Models\PackageDetail;
use App\Models\PackageGroup;
use App\Models\PackageHistory;
use App\Models\PackageGroupHistory;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\WarehouseArea;
use Exception;

class StaffPackageGroupService extends StaffBaseService implements StaffBaseServiceInterface
{
    public function list($request)
    {
        $packageDetails = PackageDetail::pluck('package_id')->toArray();
        $packageGroups = PackageGroup::with('user')->has('user')->withCount(['packageDetails' => function ($packageDetail) {
            $packageDetail->whereHas('package', function ($package) {
                $package->where('status', '<>', Package::STATUS_OUTBOUND)->withTrashed(); 
            });
         }])->withCount(['packages' => function ($package) use($packageDetails) {
            $package->whereNotIn("id", $packageDetails)->where('status', '<>', Package::STATUS_OUTBOUND)->withTrashed();
         }]);

        if(isset($request['email'])) {
            $packageGroups = $packageGroups->whereHas('user', function ($query) use ($request) {
                $query->where('email', 'like', '%'.$request['email'].'%');
            });
        }

        if(isset($request['onlyDeleted'])) {
            if($request['onlyDeleted'] == 1) {
                $packageGroups = $packageGroups->onlyTrashed();
            }
        }
        else {
            $packageGroups = $packageGroups->withTrashed();
        }

        if(isset($request['name'])) {
            $packageGroups = $packageGroups->where('name', 'like', '%'.$request['name'].'%');
        }

        if(isset($request['barcode'])) {
            $packageGroups = $packageGroups->where('barcode', $request['barcode']);
        }

        $packageGroups = $packageGroups->orderByRaw('CASE WHEN (package_details_count + packages_count) <> 0 THEN updated_at END DESC, 
                    CASE WHEN (package_details_count + packages_count) = 0 THEN updated_at END DESC');

        $packageGroups = $packageGroups->paginate()->withQueryString();
 
        $users = User::where('role', User::ROLE_USER)->pluck('email')->toArray();

        return [
            'oldInput' => $request,
            'packageGroups' => $packageGroups,
            'users' => $users,
        ];
    }

    function getDetailForNewPackage($request ,$id)
    {
        $packageGroup = PackageGroup::withTrashed()->find($id);

        $packageDetailIds = PackageDetail::where('package_group_id', $id)->pluck('package_id')->toArray();
        $totalPackagesInDetail = PackageDetail::withTrashed()->where('package_group_id', $id)
        ->whereHas('package', function ($packages) {
            $packages->where('status', '<>', Package::STATUS_OUTBOUND)->withTrashed();
        })->count();

        $totalPackagesNotInDetail = Package::withTrashed()->where('package_group_id', $id)
        ->where('status', '<>', Package::STATUS_OUTBOUND)->whereNotIn("id", $packageDetailIds)->count();
        
        $totalPackages = $totalPackagesInDetail + $totalPackagesNotInDetail;

        $packages = Package::whereHas('packageDetails', function($detail) use ($id) {
            $detail->where('package_group_id', $id)->whereHas('package', function($p){
                $p->where('status', '<>', Package::STATUS_OUTBOUND);
            });
        })->orWhereHas('packageGroupWithTrashed', function ($group) use ($id) {
            $group->where('id', $id);
        })->where('status', '<>', Package::STATUS_OUTBOUND)->groupBy('id')->withTrashed()->orderByDesc('created_at')->paginate()->withQueryString();

        $warehouseAreas = WarehouseArea::where("is_full", 0)->pluck('name')->toArray();

        $areasDetail = WarehouseArea::select('name', 'barcode', 'is_full')->get();

        $product = Product::withTrashed()->where('package_group_id', $id)->first();

        $productType = ProductType::where('id', $product->product_type_id)->first();

        $productId = $product['id'] ?? null;

        return [
            'packageGroup' => $packageGroup,
            'warehouseAreas' => $warehouseAreas,
            'packages' => $packages,
            'request' => $request,
            'totalPackages' => $totalPackages,
            'areasDetail' => $areasDetail,
            'productId' => $productId,
            'productType' => $productType
        ];
    }

    function updatePackageGroup($request)
    {
        $group = PackageGroup::find($request['id']);

        $oldBarcode = $group->barcode;
        if($oldBarcode != $request['barcode']) {
            $isExisted = $this->barcodeNumberExists($request['barcode'], []);
            if($isExisted == true) {

                return $request['barcode'].' is already existed';
            }
        }

        PackageGroupHistory::create([
            'package_group_id' => $group['id'],
            'previous_user_id' => $group['user_id'],
            'user_id' => $group['user_id'],
            'previous_name' => $group['name'],
            'name' => $request['name'],
            'previous_barcode' => $group['barcode'] ?? null,
            'barcode' => $request['barcode'] ?? null,
            'unit_width' => $group['unit_width'],
            'unit_weight' => $group['unit_weight'],
            'unit_length' => $group['unit_length'],
            'unit_height' => $group['unit_height'],
            'previous_unit_weight' => $request['unit_weight'] ?? $group['unit_weight'],
            'previous_unit_height' => $request['unit_height'] ?? $group['unit_height'],
            'previous_unit_length' => $request['unit_length'] ?? $group['unit_length'], 
            'previous_unit_width' => $request['unit_width'] ?? $group['unit_width'],
            'staff_id' => Auth::id(),
            'stage' => 'staff - package group detail',
            'type' => PackageGroupHistory::UPDATE
        ]);

        $product = Product::where("package_group_id", $request['id'])->first();
        if(!empty($product)) {
            $product->name = $request['name'];
            $product->save();
        } 

        $group->name = $request['name'];
        $group->unit_width = $request['unit_width'] ?? null;
        $group->unit_length = $request['unit_length'] ?? null;
        $group->unit_height = $request['unit_height'] ?? null;
        $group->unit_weight = $request['unit_weight'] ?? null;
        $group->barcode = $request['barcode'] ?? null;

        $group->save();

        return null;
    }

    public function new()
    {
        $users = User::where('role', User::ROLE_USER)->pluck('email')->toArray();
        $productTypes = ProductType::pluck('name_attribute', 'id')->toArray();
        
        return [
            'users' => $users,
            'productTypes' => $productTypes
        ];
    }

    public function create($request)
    {
        DB::beginTransaction();

        try {
            $user = User::where('email', $request['email'])->first();

            $properties = ['name', 'barcode', 'unit_width', 'unit_height', 'unit_length', 'unit_weight'];
            $data = [];

            if(isset($request['file'])) {
                $fileName = $request['file']->move('files' . DIRECTORY_SEPARATOR . PackageGroup::FILE_FOLDER, cleanName($request['file']->getClientOriginalName()));
                $data['file'] = $fileName;
            }

            $data['user_id'] = $user['id'];
            foreach ($properties as $prop) {
                // allow update null
                if (array_key_exists($prop, $request)) {
                    $data[$prop] = $request[$prop];
                }
            }

            $newGroup = PackageGroup::create($data);

            $dataProduct['name'] = $newGroup['name'];
            $dataProduct['user_id'] = $newGroup['user_id'];
            $dataProduct['package_group_id'] = $newGroup['id'];
            $dataProduct['status'] = Product::STATUS_ACTIVE;
            $dataProduct['product_type_id'] = $request['product_type'];
            if(isset($request['image'])) {
                $dataProduct['image_url'] = $request['image']->move('imgs' . DIRECTORY_SEPARATOR . Product::IMG_FOLDER, cleanName($request['image']->getClientOriginalName()));
            }

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
                'stage' => 'staff - create package group',
                'type' => PackageGroupHistory::CREATE
            ]);

            $newProduct = Product::create($dataProduct);
            Inventory::create([
                'product_id' => $newProduct->id,
                'sku' => $this->generateSku(),
            ]);

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }

        return $newGroup;
    }

    function insertPackage($request)
    {
        $area = [];
        if(isset($request['warehouse'])) {
            $area = WarehouseArea::where('name', $request['warehouse'])->first();
        }

        DB::transaction(function () use ($request, $area) {
            $barcodeList = [];
            $group = PackageGroup::withTrashed()->find($request['package_group_id']);
            for($i = 0; $i < (int)$request['number']; $i++){
                $barcode = $this->generateBarcodeNumber($barcodeList, $group['name'], $request['unit']);
                array_push($barcodeList, $barcode);
                $package = Package::create([
                    'package_group_id' => $request['package_group_id'],
                    'status' => $request['status'],
                    'user_id' => $request['user_id'],
                    'warehouse_area_id' => isset($request['warehouse']) ? $area['id'] : null,
                    'unit_number' => $request['unit'],
                    'received_unit_number' => $request['unit'],
                    'barcode' => $barcode,
                    'weight_staff' => $request['weight'] ?? null,
                    'height_staff' => $request['height'] ?? null,
                    'length_staff' => $request['length'] ?? null,
                    'width_staff' => $request['width'] ?? null,
                ]);

                PackageDetail::create([
                    'package_group_id' => $request['package_group_id'],
                    'package_id' => $package['id'],
                    'unit_number' => $request['unit'],
                    'received_unit_number' => $request['unit'],
                ]);

                PackageHistory::create([
                    'package_id' => $package['id'],
                    'barcode' => $barcode,
                    'previous_barcode' => $barcode,
                    'previous_status' => $request['status'],
                    'status' => $request['status'],
                    'staff_id' => Auth::id(),
                    'unit_number' => $request['unit'],
                    'weight_staff' => $request['weight'] ?? null,
                    'height_staff' => $request['height'] ?? null,
                    'length_staff' => $request['length'] ?? null,
                    'width_staff' => $request['width'] ?? null,
                    'warehouse_area_id' => isset($request['warehouse']) ? $area['id'] : null,
                    'previous_created_at' => null,
                    'stage' => 'staff - packge group detail'
                ]);
            }
        });
    }

    function createProduct($request)
    {
        DB::beginTransaction();

        try {
            $packageGroup = PackageGroup::withTrashed()->find($request['id']);

            $data = [];
            $data['name'] = $packageGroup['name'];
            $data['user_id'] = $packageGroup['user_id'];
            $data['package_group_id'] = $packageGroup['id'];
            $data['status'] = Product::STATUS_ACTIVE;

            $newProduct = Product::create($data);
            Inventory::create([
                'product_id' => $newProduct->id,
                'sku' => $this->generateSku(),
            ]);

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }

        return $newProduct['id'];
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
