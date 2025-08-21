<?php

namespace App\Services\User;

use App\Models\Inventory;
use App\Models\PackageGroup;
use App\Models\PackageDetail;
use App\Models\PackageGroupHistory;
use App\Models\Package;
use App\Models\KitComponent;
use App\Models\Product;
use App\Services\UserBaseServiceInterface;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserPackageGroupService extends UserBaseService implements UserBaseServiceInterface
{
    public function store($params)
    {
        try {
            DB::beginTransaction();

            $params['user_id'] = Auth::id();
            $packageGroup = PackageGroup::create($params);

            PackageGroupHistory::create([
                'package_group_id' => $packageGroup['id'],
                'previous_user_id' => $packageGroup['user_id'],
                'user_id' => $packageGroup['user_id'],
                'previous_name' => $packageGroup['name'],
                'name' => $packageGroup['name'],
                'previous_barcode' => $packageGroup['barcode'],
                'barcode' => $packageGroup['barcode'],
                'unit_width' => $packageGroup['unit_width'],
                'unit_weight' => $packageGroup['unit_weight'],
                'unit_length' => $packageGroup['unit_length'],
                'unit_height' => $packageGroup['unit_height'],
                'previous_unit_weight' => $packageGroup['unit_weight'],
                'previous_unit_height' => $packageGroup['unit_height'],
                'previous_unit_length' => $packageGroup['unit_length'],
                'previous_unit_width' => $packageGroup['unit_width'],
                'staff_id' => Auth::id(),
                'stage' => 'user - create package group',
                'type' => PackageGroupHistory::CREATE
            ]);

            $newProduct = Product::create([
                'name' => $packageGroup->name,
                'status' => Product::STATUS_ACTIVE,
                'package_group_id' => $packageGroup->id,
                'user_id' => Auth::id(),
            ]);

            $sku = $this->generateSku();
            Inventory::create([
                'product_id' => $newProduct->id,
                'sku' => $sku,
            ]);
            DB::commit();

            //DB::commit();

            return $sku;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function index($request)
    {
        $packageDetails = PackageDetail::pluck('package_id')->toArray();

        $packageGroups = PackageGroup::withCount(['packageDetails' => function ($packageDetail) {
            $packageDetail->whereHas('package', function ($package) {
                $package->where('user_id', Auth::id())->whereIn('status', [Package::STATUS_INBOUND, Package::STATUS_STORED]);
            });
        }])->withCount(['packages' => function ($package) use ($packageDetails) {
            $package->where('user_id', Auth::id())->whereNotIn("id", $packageDetails)->whereIn('status', [Package::STATUS_INBOUND, Package::STATUS_STORED]);
        }])->withSum(['packages' => function ($query) use ($packageDetails) {
            $query->where('user_id', Auth::id())->whereNotIn("id", $packageDetails)
                ->whereIn('status', [Package::STATUS_INBOUND, Package::STATUS_STORED]);
        }], 'unit_number')->withSum(['packageDetails' => function ($packageDetail) {
            $packageDetail->whereHas('package', function ($query) {
                $query->where('user_id', Auth::id())
                    ->whereIn('status', [Package::STATUS_INBOUND, Package::STATUS_STORED]);
            });
        }], 'unit_number')->where('user_id', Auth::id());

        if (isset($request['name'])) {
            $packageGroups = $packageGroups->where('name', $request['name']);
        }

        if (isset($request['barcode'])) {
            $packageGroups = $packageGroups->where('barcode', $request['barcode']);
        }

        $packageGroups = $packageGroups->orderByRaw('CASE WHEN (package_details_count + packages_count) <> 0 THEN updated_at END DESC,
                    CASE WHEN (package_details_count + packages_count) = 0 THEN updated_at END DESC');

        $packageGroups = $packageGroups->paginate(20)->withQueryString();

        $remind = Inventory::whereHas('product', function ($query) {
            $query->where('user_id', Auth::id());
        })->whereRaw('(available + incoming) <= min')->where('is_remind', true)->get();

        return [
            'oldInput' => $request,
            'packageGroups' => $packageGroups,
            'remind' => $remind,
        ];
    }

    public function show($packageId)
    {
        $packageGroup = PackageGroup::with(['packages' => function ($packages) {
            $packages->has('warehouseArea');
        }])->with('product')->where('user_id', Auth::id())->find($packageId);

        $totalPackage = Package::where('package_group_id', $packageId)
            ->where('user_id', Auth::id())
            ->whereIn('status', [Package::STATUS_INBOUND, Package::STATUS_STORED])->count();

        $totalUnit = Package::where('package_group_id', $packageId)
            ->where('user_id', Auth::id())
            ->whereIn('status', [Package::STATUS_INBOUND, Package::STATUS_STORED])->sum("unit_number");

        $packages = Package::with('packageGroup')->where('package_group_id', $packageId)->paginate()->withQueryString();

        $product = Product::where('package_group_id', $packageId)->first();

        $components = [];
        $componentKit = [];
        if (isset($product)) {
            $components = KitComponent::where("product_id", $product->id)->with('component')->get();

            $componentKit = Product::where('user_id', Auth::id())->pluck('name')->toArray();
        }


        return [
            'packageGroup' => $packageGroup,
            'packages' => $packages,
            'totalPackages' => $totalPackage,
            'totalUnit' => $totalUnit,
            'components' => $components,
            'componentKit' => $componentKit,
        ];
    }

    public function uploadImage($request)
    {
        if (isset($request['image'])) {
            $product = Product::where('package_group_id', $request['id'])->first();
            if ($product) {
                $image = $request['image']->move('imgs' . DIRECTORY_SEPARATOR . Product::IMG_FOLDER, cleanName($request['image']->getClientOriginalName()));

                $product->image_url = $image;
                $product->save();
            } else {
                $dataProduct['name'] = $request['name'];
                $dataProduct['user_id'] = $request['user_id'];
                $dataProduct['package_group_id'] = $request['id'];
                $dataProduct['status'] = Product::STATUS_ACTIVE;

                $dataProduct['image_url'] = $request['image']->move('imgs' . DIRECTORY_SEPARATOR . Product::IMG_FOLDER, cleanName($request['image']->getClientOriginalName()));

                $newProduct = Product::create($dataProduct);

                Inventory::create([
                    'product_id' => $newProduct->id,
                    'sku' => $this->generateSku(),
                ]);
            }
        }
    }

    function createKitComponent($request)
    {
        DB::beginTransaction();

        try {
            $componentId = 0;

            $component = Product::where('name', $request['name'])->first();
            if ($component) {
                $componentId = $component['id'];
            } else {
                $newGroup = PackageGroup::create([
                    'user_id' => Auth::id(),
                    'name' => $request['name'],
                ]);

                $newProduct = Product::create([
                    'status' => Product::STATUS_ACTIVE,
                    'package_group_id' => $newGroup['id'],
                    'user_id' => Auth::id(),
                    'name' => $request['name'],
                ]);

                $componentId = $newProduct['id'];
            }

            KitComponent::create([
                'product_id' => $request['id'],
                'component_id' => $componentId,
                'quantity' => $request['quantity']
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    function deleteKitComponent($request)
    {
        $component = KitComponent::find($request['id']);
        $component->delete();
    }

    function updateKitComponent($request)
    {
        $component = KitComponent::find($request['id']);
        $component->quantity = $request['quantity'];
        $component->save();
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
