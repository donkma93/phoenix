<?php

namespace App\Services\Staff;

use App\Models\ProductType;
use App\Models\ProductTypeAttr;
use App\Models\User;
use App\Services\Staff\StaffBaseService;
use App\Services\StaffBaseServiceInterface;
use Illuminate\Support\Facades\Auth;


class ProductTypeService extends StaffBaseService implements StaffBaseServiceInterface
{
    public function index($request) {
        $productTypes = ProductType::where('user_id', Auth::id())->get();

        foreach ($productTypes as $productType) {
            $productTypeAttrs = ProductTypeAttr::orderBy('created_at', 'DESC')->where('product_type_id', $productType->id)->get();
            $productType['productTypeAttrs'] = $productTypeAttrs;
        }
        return $productTypes;
    }

    public function store($request)
    {
        $data = ProductType::create([
            'name_attribute' => $request['name'],
            'order_no' => $request['order_no'],
            'user_id' => Auth::id()
        ]);
        return $data;
    }

    public function createAttr($product_type_id)
    {
        $productTypeAttr = $this->getProductTypeAttrByProductTypeID($product_type_id);

        return [
            'productTypeAttr' => $productTypeAttr,
            'product_type_id' => $product_type_id
        ];
    }


    public function storeAttr($request)
    {
        $product_type_id = $request['product_type_id'];

        if (count($request['product_type_attr_names']) > 0) {
            foreach ($request['product_type_attr_names'] as $product_attr_name) {
                $product_attr = [
                    'name_attribute' => $product_attr_name,
                    'product_type_id' => $product_type_id,
                    'user_id' => Auth::id(),
                ];

                ProductTypeAttr::create($product_attr);
            }
        }


        return [
            'product_type_id' => $product_type_id
        ];
    }


    public function getProductTypeAttrByProductTypeID($product_type_id)
    {
        $productTypeAttr = ProductTypeAttr::orderBy('created_at', 'DESC')->where('product_type_id', $product_type_id)->get();
        return $productTypeAttr;
    }

}
