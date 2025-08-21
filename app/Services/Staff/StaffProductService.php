<?php

namespace App\Services\Staff;

use Illuminate\Support\Facades\DB;
use App\Models\PackageGroup;
use App\Models\PackageGroupHistory;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\KitComponent;
use App\Models\ProductAttribute;
use App\Models\ProductType;
use App\Models\ProductTypeAttr;
use App\Services\StaffBaseServiceInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffProductService extends StaffBaseService implements StaffBaseServiceInterface
{
    function list($request)
    {
        $products = Product::with(['user' => function ($user) {
            $user->withTrashed();
         }, 'category' => function ($category) {
            $category->withTrashed();
         }])->has('user');

        if(isset($request['email'])) {
            $products->whereHas('user', function ($query) use ($request) {
                $query->where('email', 'like', '%'.$request['email'].'%');
            });
        }

        if(isset($request['category'])) {
            $products->whereHas('category', function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request['category'].'%');
            });
        }

        if(isset($request['name'])) {
            $products->where('name', 'like', '%'.$request['name'].'%');
        }

        if(isset($request['status'])) {
            $products->where('status', $request['status']);
        }

        if(isset($request['onlyDeleted'])) {
            if($request['onlyDeleted'] == 1) {
                $products = $products->onlyTrashed();
            }
        } else {
            $products = $products->withTrashed();
        }

        $products = $products->orderByDesc('updated_at');


        $products = $products->paginate()->withQueryString();

        foreach ($products as $product) {
            $productTypeAttr = ProductAttribute::where('product_id', $product->id)->pluck('value_attribute', 'name_attribute')->toArray();
            $product['productTypeAttr'] = $productTypeAttr;
        }

        $users = User::where('role', User::ROLE_USER)->pluck('email')->toArray();

        $categories = Category::pluck('name')->toArray();

        return [
            'oldInput' => $request,
            'products' => $products,
            'users' => $users,
            'categories' => $categories
        ];
    }

    function detail($id)
    {
        $product = Product::with(['user' => function ($user) {
            $user->withTrashed();
        }, 'category' => function ($category) {
            $category->withTrashed();
        }])->has('user')->withTrashed()->find($id);

        $users = User::where('role', User::ROLE_USER)->pluck('email')->toArray();

        $categories = Category::pluck('name')->toArray();

        $components = KitComponent::where("product_id", $id)->with('component')->get();

        $componentKit = Product::where('user_id', $product['user_id'])->pluck('name')->toArray();

        $productType = ProductType::find($product->product_type_id);

        $productTypeAttr = ProductTypeAttr::where('product_type_id', $product->product_type_id)->pluck('name_attribute')->toArray();

        $productAttr = ProductAttribute::where('product_id', $product->id)->pluck('value_attribute', 'name_attribute')->toArray();

        $key = array_search($product['name'], $componentKit);
        if ($key !== false) {
            array_splice($componentKit, $key, 1);
        }
        
        return [
            'product' => $product,
            'users' => $users,
            'categories' => $categories,
            'components' => $components,
            'componentKit' => $componentKit,
            'productType' => $productType,
            'productTypeAttr' => $productTypeAttr,
            'productAttr' => $productAttr
        ];
    }

    function new()
    {
        $users = User::where('role', User::ROLE_USER)->pluck('email')->toArray();

        $categories = Category::pluck('name')->toArray();

        return [
            'users' => $users,
            'categories' => $categories
        ];
    }

    function create($request)
    {
        DB::beginTransaction();

        try {
            $user = User::where('role', User::ROLE_USER)->where('email', $request['email'])->first();

            $packageGroupData = [];
            $packageGroupData['user_id'] = $user['id'];
            $packageGroupData['name'] = $request['name'];
            $newGroup = PackageGroup::create($packageGroupData);

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
                'stage' => 'staff - create product',
                'type' => PackageGroupHistory::UPDATE
            ]);

            $data = [];
            $data['status'] = $request['status'];
            $data['name'] = $request['name'];
            $data['user_id'] = $user['id'];
            $data['package_group_id'] = $newGroup['id'];

            if(isset($request['category'])) {
                $category = Category::where('name', $request['category'])->first();
                $data['category_id'] = $category['id'];
            }

            if(isset($request['fulfillment_fee'])) {
                $data['fulfillment_fee'] = $request['fulfillment_fee'];
            }

            if(isset($request['extra_pick_fee'])) {
                $data['extra_pick_fee'] = $request['extra_pick_fee'];
            }

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

    function update($request)
    {
        $product = Product::find($request['id']);

        $product->status = $request['status'];

        if(isset($request['category'])) {
            $category = Category::where('name', $request['category'])->first();
            $product->category_id = $category['id'];
        } else {
            $product->category_id = null;
        }

        if(isset($request['fulfillment_fee'])) {
            $product->fulfillment_fee = $request['fulfillment_fee'];
        }

        if(isset($request['extra_pick_fee'])) {
            $product->extra_pick_fee = $request['extra_pick_fee'];
        }

        if (count($request['product_attribute_values']) > 0) {
            foreach ($request['product_attribute_values'] as $index => $attrValue) {
                $productAttr = ProductAttribute::where('product_id', $request['id'])->where('name_attribute', $request['product_attribute_names'][$index])->first();
                if (!isset($productAttr)) {
                    ProductAttribute::create([
                        'product_id' => $request['id'],
                        'name_attribute' => $request['product_attribute_names'][$index],
                        'value_attribute' => $attrValue,
                        'user_id' => Auth::id()
                    ]);
                } else {
                    ProductAttribute::where('id', $productAttr->id)->update([
                        'value_attribute' => $attrValue,
                    ]);
                }
            }
        }

        $product->save();
    }

    function createKitComponent($request) {
        DB::beginTransaction();

        try {
            $componentId = 0;

            $component = Product::where('name', $request['name'])->first();
            if($component) {
                $componentId = $component['id']; 
            } else {
                $newGroup = PackageGroup::create([
                    'user_id' => $request['user_id'],
                    'name' => $request['name'],
                ]);

                $newProduct = Product::create([
                    'status' => Product::STATUS_ACTIVE,
                    'package_group_id' => $newGroup['id'],
                    'user_id' => $request['user_id'],
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

    function deleteKitComponent($request) {
        $component = KitComponent::find($request['id']);
        $component->delete();
    }

    function updateKitComponent($request) {
        $component = KitComponent::find($request['id']);
        $component->quantity = $request['quantity'];
        $component->save();
    }

    function createSKU($request) {
        Inventory::create([
            'product_id' => $request['id'],
            'sku' => $this->generateSku(),
        ]);

        return true;
    }

    public function createAttr($product_id)
    {
        $product = Product::where('id', $product_id)->first();
        $productTypes = ProductType::where('id', $product->product_type_id)->first();
        return [
            'product_id'=> $product_id,
            'productTypes' => $productTypes
        ];
    }

    public function getProductAttributeBySKU($sku)
    {
        $inventory = Inventory::where('sku', $sku)->first();
        if (!isset($inventory)) {
            return [
                'MESSAGE_CODE' => "INVENTORY_NOT_FOUND",
                'MESSAGE_TEXT' => "Inventory not found",
                'data' => null
            ];
        }
        $product = Product::where('id', $inventory->product_id)->first();
        if (!isset($product)) {
            return [
                'MESSAGE_CODE' => "PRODUCT_NOT_FOUND",
                'MESSAGE_TEXT' => "Product not found",
                'data' => null
            ];
        }
        $productAttr = ProductAttribute::where('product_id', $inventory->product_id)->pluck('value_attribute', 'name_attribute')->toArray();
        return [
            'MESSAGE_CODE' => "SUCCESS",
            'MESSAGE_TEXT' => "SUCCESS",
            'data' => [
                'product_name' => $product->name,
                'sku' => $sku,
                'product_id'=> $inventory->product_id,
                'product_attributes' => $productAttr
            ]
        ];
    }

    public function storeAttribute(Request $request)
    {
        foreach ($request['product_attribute_names'] as $index => $name) {
            $productAttr = [
                'product_id' => $request['product_id'],
                'name_attribute' => $name,
                'value_attribute' => $request['product_attribute_values'][$index],
                'user_id' => Auth::id()
            ];

            ProductAttribute::create($productAttr);
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
