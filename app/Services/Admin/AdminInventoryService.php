<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Models\StoreFulfill;
use App\Models\Product;
use App\Services\AdminBaseServiceInterface;
use Exception;

class AdminInventoryService extends AdminBaseService implements AdminBaseServiceInterface
{
    function list($request)
    {
        $inventories = Inventory::with(['storeFulfill' => function ($store) {
            $store->withTrashed();
         }, 'product' => function ($product) {
            $product->withTrashed();
         }])->withTrashed();

        if(isset($request['sku'])) {
            $inventories->where('sku', $request['sku']);
        }

        if(isset($request['product'])) {
            $inventories->whereHas('product', function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request['product'].'%');
            });
        }

        if(isset($request['store'])) {
            $inventories->whereHas('storeFulfill', function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request['store'].'%');
            });
        }

        $inventories = $inventories->orderByDesc('updated_at');
        
        $inventories = $inventories->paginate()->withQueryString();
        
        $products = Product::pluck('name')->toArray();

        $stores = StoreFulfill::pluck('name')->toArray();

        return [
            'oldInput' => $request,
            'inventories' => $inventories,
            'products' => $products,
            'stores' => $stores,
        ];
    }

    function detail($id) {
        $inventory = Inventory::with(['storeFulfill' => function ($store) {
            $store->withTrashed();
         }, 'product' => function ($product) {
            $product->withTrashed();
         }])->find($id);

         $histories = InventoryHistory::where('inventory_id', $inventory->id)->get();

         return [
             'inventory' => $inventory,
             'histories' => $histories
         ];
    }
}