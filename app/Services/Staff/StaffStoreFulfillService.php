<?php

namespace App\Services\Staff;

use App\Services\StaffBaseServiceInterface;
use App\Models\StoreFulfill;

class StaffStoreFulfillService extends StaffBaseService implements StaffBaseServiceInterface
{
    public function list($input)
    {
        $stores = StoreFulfill::orderByDesc('created_at');

        if(isset($input['onlyDeleted'])) {
            if($input['onlyDeleted'] == 1) {
                $stores = StoreFulfill::onlyTrashed();
            } else {
                $stores = StoreFulfill::withTrashed();
            }
        }

        if(isset($input['name'])) {
            $stores = $stores->where('name', 'like', '%'.$input['name'].'%');
        }

        if(isset($input['code'])) {
            $stores = $stores->where('code', 'like', '%'.$input['code'].'%');
        }

        $stores = $stores->paginate()->withQueryString();

        $storeNames = StoreFulfill::withTrashed()->pluck('name')->toArray();

        return [
            'stores' => $stores,
            'oldInput' => $input,
            'storeNames' => $storeNames
        ];
    }

    public function detail($id)
    {
        $store = StoreFulfill::withTrashed()->find($id);

        return [
            'store' => $store,
        ];
    }
}
