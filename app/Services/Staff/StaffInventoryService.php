<?php

namespace App\Services\Staff;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Models\StoreFulfill;
use App\Models\Product;
use App\Services\StaffBaseServiceInterface;
use Exception;

class StaffInventoryService extends StaffBaseService implements StaffBaseServiceInterface
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

         $stores = StoreFulfill::pluck('name')->toArray();

         return [
             'inventory' => $inventory,
             'stores' => $stores,
             'histories' => $histories
         ];
    }

    function updateAvailable($request)
    {
        $inventory = Inventory::where('sku', $request['sku'])->first();
        $inventory->available = $inventory['available'] + $inventory['incoming'];
        $inventory->incoming = 0;
        $inventory->save();

        return $inventory['id'];
    }

    function update($request)
    {
        DB::transaction(function () use ($request) {
            $store = StoreFulfill::where('name', $request['store'])->first();
        
            $inventory = Inventory::find($request['id']);

            InventoryHistory::create([
                'inventory_id' => $request['id'], 
                'user_id' => Auth::id(),
                'hour' => $request['hour'],
                'incoming' => $request['incoming'],
                'available' => $request['available'],
                'previous_incoming' => $inventory->incoming,
                'previous_available' => $inventory->available,
                'start_at' => $request['start_at']
            ]);

            $inventory->store_fulfill_id = $store['id'] ?? null ;
            $inventory->available = $request['available'] ;
            $inventory->incoming = $request['incoming'] ;
            $inventory->save();

            return $inventory['id'];
        });
    }

    function updateHistory($request)
    {
        $history = InventoryHistory::find($request['id']);

        $history->hour = $request['hour'] ;
        $history->start_at = $request['start'] ;
        $history->available = $request['available'] ;
        $history->incoming = $request['incoming'] ;
        $history->user_id = Auth::id();
        $history->save();
    }
}