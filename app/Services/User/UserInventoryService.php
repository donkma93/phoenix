<?php

namespace App\Services\User;

use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Models\Product;
use App\Services\UserBaseServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserInventoryService extends UserBaseService implements UserBaseServiceInterface
{
    function list($request)
    {
        $inventories = Inventory::whereHas('product', function ($query) {
            $query->where('user_id', Auth::id());
        })->with(['product' => function ($product) {
            $product->withTrashed();
        }, 'storeFulfill']);

        if(isset($request['product'])) {
            $inventories->whereHas('product', function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request['product'].'%');
            });
        }

        $inventories = $inventories->orderByDesc('updated_at');
        $inventories = $inventories->paginate()->withQueryString();

        $products = Product::where('user_id', Auth::id())
            ->pluck('name')->toArray();

        return [
            'oldInput' => $request,
            'inventories' => $inventories,
            'products' => $products,
        ];
    }

    function detail($id) {
        $inventory = Inventory::whereHas('product', function ($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($id);

        $histories = InventoryHistory::where('inventory_id', $id)->get();

        return [
            'inventory' => $inventory,
            'histories' => $histories
        ];
    }

    function remind($request) {
        $inventories = Inventory::whereHas('product', function ($query) {
            $query->where('user_id', Auth::id());
        })->with(['product' => function ($product) {
            $product->withTrashed();
        }, 'storeFulfill']);

        if(isset($request['product'])) {
            $inventories->whereHas('product', function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request['product'].'%');
            });
        }

        $inventories = $inventories->orderByDesc('updated_at');
        $inventories = $inventories->paginate()->withQueryString();

        $products = Product::where('user_id', Auth::id())
            ->pluck('name')->toArray();

        return [
            'oldInput' => $request,
            'inventories' => $inventories,
            'products' => $products,
        ];
    }

    function updateReminds($request) {
        DB::beginTransaction();

        try {
            foreach ($request['inventories'] as $inventory) {
                $this->updateRemind($inventory);
            }
            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    function updateRemind($request) {
        $inventory = Inventory::find($request['id']);
        $isRemind = isset($request['is_remind']) ? $request['is_remind'] : 0;
        $isRemind = filter_var($isRemind, FILTER_VALIDATE_BOOLEAN);
        if($inventory->min != $request['min'] || $inventory->is_remind != $isRemind) {
            $inventory->min = $request['min'];
            $inventory->is_remind = $isRemind ?? false;
    
            $inventory->save();
        }
    }

    function updateIncomming($request)
    {
        $inventory = Inventory::whereHas('product', function ($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($request['id']);

        $inventory->incoming = $request['incoming'];
        $inventory->save();

        InventoryHistory::create([
            'inventory_id' => $request['id'], 
            'user_id' => Auth::id(),
            'hour' => 0,
            'incoming' => $request['incoming'],
            'available' => $inventory->available,
            'previous_incoming' => $inventory->incoming,
            'previous_available' => $inventory->available,
            'start_at' => Carbon::now()
        ]);
    }
}
