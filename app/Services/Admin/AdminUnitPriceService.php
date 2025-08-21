<?php

namespace App\Services\Admin;

use App\Services\AdminBaseServiceInterface;
use App\Models\MRequestType;
use App\Models\MTax;
use App\Models\StoragePrice;
use App\Models\UnitPrice;
use Exception;
use Illuminate\Support\Facades\DB;

class AdminUnitPriceService extends AdminBaseService implements AdminBaseServiceInterface
{
    function list() {
        $mRequestType = MRequestType::all();

        return [
            'requestType' => $mRequestType
        ];
    }

    function detail($id) {
        $unitPrices = [];
        $mRequest = [];

        // tax
        if($id == MTax::TAX_ID) {
            $mtax = MTax::first();
            $unitPrices[] = $mtax->tax;
        } else if ($id == 0) {
            $unitPrices = StoragePrice::orderBy('month')
                ->orderBy('price')
                ->paginate()
                ->withQueryString();;
        } else {
            $mRequest = MRequestType::findOrFail($id);
            if(in_array($mRequest['name'], ['relabel', 'removal'])) {
                $unitPrices = UnitPrice::where('m_request_type_id', $id)
                    // ->orderBy('length')
                    // ->orderBy('weight')
                    ->orderBy('min_unit')
                    ->orderBy('max_unit')
                    ->paginate()
                    ->withQueryString();
            } else {
                $unitPrices = UnitPrice::where('m_request_type_id', $id)->first();
            }
        }

        return [
            'unitPrices' => $unitPrices,
            'mRequestType' => $mRequest,
            'id' => $id
        ];
    }

    function update($request) {
        DB::beginTransaction();

        try {
            if ($request['type'] == "tax") {
                MTax::first()->update(
                    ['tax' => $request['tax']]
                );
            }

            if ($request['type'] == "storage") {
                foreach($request['unit'] as $unit) {
                    StoragePrice::where('id', $unit['id'])->update([
                        'price' => $unit['price']
                    ]);
                }
            }

            if (in_array($request['type'], ['relabel', 'removal'])) {
                foreach($request['unit'] as $unit) {
                    UnitPrice::where('id', $unit['id'])->update([
                        'min_size_price' => $unit['min_size_price'],
                        'max_size_price' => $unit['max_size_price'],
                    ]);
                }
            }


            if (in_array($request['type'], ['return', 'repack', 'outbound', 'add package', 'warehouse labor'])) {
                $values = [
                    'min_size_price' => $request['min_size_price'],
                ];

                if ($request['type'] == "repack" || $request['type'] == "warehouse labor") {
                    $values['max_size_price'] = $request['max_size_price'];
                }

                UnitPrice::where('id', $request['id'])->update($values);
            }

            // if(in_array($request['type'], ['return', 'repack', 'outbound', 'add package'])) {
            //     $unitPrice = UnitPrice::whereHas('mRequestType', function ($query) use ($request) {
            //         $query->where('name', $request['type']);
            //     })->first();

            //     if(isset($unitPrice)) {
            //         $unitPrice->price = $request['price'];
            //         $unitPrice->save();
            //     } else {
            //         $mRequest = MRequestType::where('name', $request['type'])->first();
            //         UnitPrice::create([
            //             'm_request_type_id' => $mRequest->id,
            //             'price' => $request['price'],
            //         ]);
            //     }
            // } else {
            //     foreach($request['unit'] as $unit) {
            //         if($request['type'] == 'storage') {
            //             $storePrice = StoragePrice::find($unit['id']);
            //             $storePrice->month = $unit['month'];
            //             $storePrice->price = $unit['price'];
            //             $storePrice->save();
            //         } else {
            //             $unitPrice = UnitPrice::find($unit['id']);
            //             $unitPrice->min_unit = $unit['min_unit'] ?? null;
            //             $unitPrice->max_unit = $unit['max_unit'] ?? null;
            //             $unitPrice->hour = $unit['hour'] ?? null;
            //             $unitPrice->weight = $unit['weight'] ?? null;
            //             $unitPrice->length = $unit['length'] ?? null;
            //             $unitPrice->price = $unit['price'];
            //             $unitPrice->save();
            //         }
            //     }
            // }

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    function create($request) {
        if($request['type'] == 0) {
            StoragePrice::create([
                'month' => $request['month'],
                'price' => $request['price'],
            ]);
        } else {
            UnitPrice::create([
                'm_request_type_id' => $request['type'],
                'min_unit' => $request['min_unit'] ?? null,
                'max_unit' => $request['max_unit'] ?? null,
                'hour' => $request['hour'] ?? null,
                'weight' => $request['weight'] ?? null,
                'length' => $request['length'] ?? null,
                'price' => $request['price'],
            ]);
        }
    }
}
