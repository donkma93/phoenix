<?php

namespace App\Services\Admin;

use App\Models\Inventory;
use App\Services\AdminBaseServiceInterface;
use App\Models\Package;
use App\Models\PackageGroup;
use App\Models\PackageDetail;
use App\Models\PackageHistory;
use App\Models\PackageGroupHistory;
use App\Models\User;
use App\Models\UserRequest;
use App\Models\WarehouseArea;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class AdminPackageGroupService extends AdminBaseService implements AdminBaseServiceInterface
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

        if(isset($request['onlyDeleted'])) {
            if($request['onlyDeleted'] == 1) {
                $packageGroups = $packageGroups->onlyTrashed();
            }
        }
        else {
            $packageGroups = $packageGroups->withTrashed();
        }

        if(isset($request['email'])) {
            $packageGroups = $packageGroups->whereHas('user', function ($query) use ($request) {
                $query->where('email', 'like', '%'.$request['email'].'%');
            });
        }

        if(isset($request['name'])) {
            $packageGroups = $packageGroups->where('name', 'like', '%'.$request['name'].'%');
        }

        if(isset($request['barcode'])) {
            $packageGroups = $packageGroups->where('barcode', $request['barcode']);
        }

        if(isset($request['startDate'])) {
            $packageGroups = $packageGroups->where('created_at', '>=' , date("Y-m-d 00:00:00",strtotime($request['startDate'])));
        }

        if(isset($request['endDate'])) {
            $packageGroups = $packageGroups->where('created_at', '<=' , date("Y-m-d 23:59:59",strtotime($request['endDate'])));
        }

        $packageGroups = $packageGroups->orderByRaw('CASE WHEN (package_details_count + packages_count) <> 0 THEN updated_at END DESC, 
        CASE WHEN (package_details_count + packages_count) = 0 THEN updated_at END DESC');

        $packageGroups = $packageGroups->paginate()->withQueryString();

        $users = User::where('role', User::ROLE_USER)->withTrashed()->pluck('email')->toArray();

        $groups = PackageGroup::withTrashed()->pluck('name')->toArray();

        $needCompare = false;

        $checkProduct = Product::withTrashed()->has('user')->count();
        $checkGroup = PackageGroup::withTrashed()->has('user')->count();

        if($checkProduct < $checkGroup) {
            $needCompare = true;
        }

        return [
            'oldInput' => $request,
            'packages' => $packageGroups,
            'users' => $users,
            'groups' => $groups,
            'needCompare' => $needCompare
        ];
    }

    public function detail($id)
    {
        $packageGroup = PackageGroup::with('packages')->withCount('packages')->withTrashed()->find($id);

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

        $emails = User::where('role', User::ROLE_USER)->withTrashed()->pluck('email')->toArray();

        $product = Product::withTrashed()->where('package_group_id', $id)->first();
        $productId = $product['id'] ?? null;

        return [
            'packageGroup' => $packageGroup,
            'packages' => $packages,
            'totalPackage' => $totalPackages,
            'emails' => $emails,
            'productId' => $productId
        ];
    }

    public function update($request)
    {
        $userRequest = UserRequest::with('requestPackageGroups')
            ->join('request_package_groups', 'request_package_groups.user_request_id', '=', 'user_requests.id')
            ->where('request_package_groups.package_group_id', $request['id'])
            ->whereIn('status', [UserRequest::STATUS_INPROGRESS, UserRequest::STATUS_NEW])->get();

         if(count($userRequest) > 0) {
             return false;
         }

        $user = User::where('email', $request['email'])->first();

        DB::beginTransaction();

        try {

            $group = PackageGroup::find($request['id']);
            $group->user_id = $user->id;
            $group->save();

            PackageGroupHistory::create([
                'package_group_id' => $group['id'],
                'previous_user_id' => $group['user_id'],
                'user_id' => $user->id,
                'previous_name' => $group['name'],
                'name' => $group['name'],
                'previous_barcode' => $group['barcode'],
                'barcode' => $group['barcode'],
                'unit_width' => $group['unit_width'],
                'unit_weight' => $group['unit_weight'],
                'unit_length' => $group['unit_length'],
                'unit_height' => $group['unit_height'],
                'previous_unit_weight' => $group['unit_weight'],
                'previous_unit_height' => $group['unit_height'],
                'previous_unit_length' => $group['unit_length'], 
                'previous_unit_width' => $group['unit_width'],
                'staff_id' => Auth::id(),
                'stage' => 'admin - package group detail',
                'type' => PackageGroupHistory::UPDATE
            ]);

            Package::where('package_group_id', $request['id'])->update(['user_id' => $user->id]);

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    function delete($request)
    {
        $group = PackageGroup::withTrashed()->find($request['id']);

        DB::beginTransaction();

        try {
            if(isset($group->deleted_at)) {
                $group->restore();

                PackageGroupHistory::create([
                    'package_group_id' => $group['id'],
                    'previous_user_id' => $group['user_id'],
                    'user_id' => $group['user_id'],
                    'previous_name' => $group['name'],
                    'name' => $group['name'],
                    'previous_barcode' => $group['barcode'],
                    'barcode' => $group['barcode'],
                    'unit_width' => $group['unit_width'],
                    'unit_weight' => $group['unit_weight'],
                    'unit_length' => $group['unit_length'],
                    'unit_height' => $group['unit_height'],
                    'previous_unit_weight' => $group['unit_weight'],
                    'previous_unit_height' => $group['unit_height'],
                    'previous_unit_length' => $group['unit_length'], 
                    'previous_unit_width' => $group['unit_width'],
                    'staff_id' => Auth::id(),
                    'stage' => 'admin - package group detail',
                    'type' => PackageGroupHistory::RESTORE
                ]);

                $packages = Package::where('package_group_id', $request['id'])->withTrashed()->get();

                foreach($packages as $package) {
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
                        'stage' => 'admin - packge group detail'
                    ]);
                }

                $product = Product::where('package_group_id', $request['id'])->withTrashed()->first();

                if($product) {
                    $product->restore();
                }
            } else {
                $userRequest = UserRequest::with('requestPackageGroups')
                ->join('request_package_groups', 'request_package_groups.user_request_id', '=', 'user_requests.id')
                ->where('request_package_groups.package_group_id', $request['id'])
                ->whereIn('status', [UserRequest::STATUS_INPROGRESS, UserRequest::STATUS_NEW])->get();

                if(count($userRequest) > 0) {
                    DB::rollBack();

                    return false;
                }

                $group->delete();

                PackageGroupHistory::create([
                    'package_group_id' => $group['id'],
                    'previous_user_id' => $group['user_id'],
                    'user_id' => $group['user_id'],
                    'previous_name' => $group['name'],
                    'name' => $group['name'],
                    'previous_barcode' => $group['barcode'],
                    'barcode' => $group['barcode'],
                    'unit_width' => $group['unit_width'],
                    'unit_weight' => $group['unit_weight'],
                    'unit_length' => $group['unit_length'],
                    'unit_height' => $group['unit_height'],
                    'previous_unit_weight' => $group['unit_weight'],
                    'previous_unit_height' => $group['unit_height'],
                    'previous_unit_length' => $group['unit_length'], 
                    'previous_unit_width' => $group['unit_width'],
                    'staff_id' => Auth::id(),
                    'stage' => 'admin - package group detail',
                    'type' => PackageGroupHistory::DELETE
                ]);

                $packages = Package::where('package_group_id', $request['id'])->withTrashed()->get();

                foreach($packages as $package) {
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
                        'stage' => 'admin - packge group detail'
                    ]);
                }

                $product = Product::where('package_group_id', $request['id'])->first();

                if($product) {
                    $order = Order::with('orderProducts')
                    ->join('order_product', 'order_product.order_id', '=', 'orders.id')
                    ->where('order_product.product_id', $product['id'])
                    ->whereIn('status', [ORDER::STATUS_INPROGRESS, ORDER::STATUS_NEW])
                    ->get();

                    if(count($order) > 0) {
                        DB::rollBack();

                        return false;
                    }

                    $product->delete();
                }
            }

            DB::commit();

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            
            throw new Exception($e->getMessage());
        }
    }

    function history($request) {
        $histories = PackageGroupHistory::has('user');

        if(isset($request['email'])) {
            $histories = $histories->whereHas('user', function ($query) use ($request) {
                $query->where('email', 'like', '%'.$request['email'].'%');
            });
        }

        if(isset($request['previous_email'])) {
            $histories = $histories->whereHas('user', function ($query) use ($request) {
                $query->where('email', 'like', '%'.$request['previous_email'].'%');
            });
        }

        if(isset($request['staff'])) {
            $histories = $histories->whereHas('staff', function ($query) use ($request) {
                $query->where('email', 'like', '%'.$request['staff'].'%');
            });
        }

        if(isset($request['type'])) {
            $histories = $histories->where('type', $request['type']);
        }

        if(isset($request['name'])) {
            $histories = $histories->where('name', 'like', '%'.$request['name'].'%');
        }

        if(isset($request['previous_name'])) {
            $histories = $histories->where('previous_name', 'like', '%'.$request['previous_name'].'%');
        }

        if(isset($request['barcode'])) {
            $histories = $histories->where('barcode', $request['barcode']);
        }

        if(isset($request['previous_barcode'])) {
            $histories = $histories->where('previous_barcode', $request['previous_barcode']);
        }

        if(isset($request['startDate'])) {
            $histories = $histories->where('created_at', '>=' , date("Y-m-d 00:00:00",strtotime($request['startDate'])));
        }

        if(isset($request['endDate'])) {
            $histories = $histories->where('created_at', '<=' , date("Y-m-d 23:59:59",strtotime($request['endDate'])));
        }

        $histories = $histories->orderByDesc('updated_at');

        $histories = $histories->paginate()->withQueryString();

        $users = User::where('role', User::ROLE_USER)->withTrashed()->pluck('email')->toArray();

        $staffs = User::withTrashed()->pluck('email')->toArray();

        $groups = PackageGroup::withTrashed()->pluck('name')->toArray();

        return [
            'oldInput' => $request,
            'users' => $users,
            'histories' => $histories,
            'groups' => $groups,
            'staffs' => $staffs
        ];
    }

    public function historyDetail($id)
    {
        $history = PackageGroupHistory::withTrashed()->find($id);

        return [
            'history' => $history
        ];
    }

    function compare()
    {
        DB::beginTransaction();

        try {
            $productIds = Product::withTrashed()->has('user')->pluck('id')->toArray();

            $groups = PackageGroup::withTrashed()->has('user')->get();

            foreach($groups as $group) {
                if(!in_array($group['id'], $productIds)) {
                    $dataProduct['name'] = $group['name'];
                    $dataProduct['user_id'] = $group['user_id'];
                    $dataProduct['package_group_id'] = $group['id'];
                    $dataProduct['status'] = Product::STATUS_ACTIVE;

                    $newProduct = Product::create($dataProduct);
                    Inventory::create([
                        'product_id' => $newProduct->id,
                        'sku' => $this->generateSku(),
                    ]);
                }
            }

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
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
