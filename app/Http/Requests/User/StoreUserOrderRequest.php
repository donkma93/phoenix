<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;

class StoreUserOrderRequest extends FormRequest
{
    private $customAttributes = [
        'shipping_zip' => 'postal code / zip ',
        'product.*.id' => 'product',
        'product.*.unit_number' => 'unit number',
        'product.*.sku' => 'sku',
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $request = $this->request->all();

        $rules = [
            'shipping_name' => 'required|string|max:255|regex:/^[a-z0-9 .,\-\_]+$/i',
            'shipping_street' => 'required|string|max:35|regex:/^[a-z0-9 .,\-\_\/]+$/i',
            'shipping_address1' => 'nullable|string|max:35|regex:/^[a-z0-9 .,\-\_\/]+$/i',
            'shipping_address2' => 'nullable|string|max:35|regex:/^[a-z0-9 .,\-\_\/]+$/i',
            'shipping_company' => 'nullable|string|max:255|regex:/^[a-z0-9 .,\-\_]+$/i',
            'shipping_city' => 'required|string|max:255|regex:/^[a-z0-9 .,\-\_]+$/i',
            'shipping_zip' => 'required|string|max:255',
            'shipping_province' => 'required|string|max:255',
            'shipping_country' => 'required|string|max:255',
            // 'shipping_phone' => 'nullable|string|max:255',

            'order_number' => 'nullable|string|max:255',

            'product' => 'required|array|min:1',
            'product.*.id' => 'required|distinct',
            // 'product.*.id' => 'required|exists:products,id,deleted_at,NULL,user_id,' . Auth::id(). '|distinct',
            'product.*.unit_number' => 'required|integer|min:1',
            // 'product.*.sku' => 'required|string|max:255',
        ];

        $validator = Validator::make($request, [
            'product' => 'required|array|min:1',
            'product.*.id' => 'required|distinct',
        ]);
        $errors = $validator->errors();

        if ($errors->isNotEmpty()) {
            // $errorMessage = $errors->first('product');
            // $rules['product'] = [
            //     'required|array|min:1', function ($attribute, $values, $fail) use ($errorMessage) {
            //         $fail(__($errorMessage));
            //     }
            // ];
        } else {
            foreach ($request['product'] as $index => $product) {
                $productId = "product.{$index}.id";

                $validator = Validator::make(
                    $request,
                    [$productId => 'required|exists:products,id,deleted_at,NULL,user_id,' . Auth::id()],
                    [],
                    [$productId => 'product']
                );

                $errors = $validator->errors();
                $errorMessage = $errors->first($productId);

                if ($errors->has($productId)) {
                    $rules[$productId] = [
                        'required', function ($attribute, $values, $fail) use ($errorMessage) {
                            $fail(__($errorMessage));
                        }
                    ];
                } else {
                    $productSku = "product.{$index}.sku";
                    $rules[$productSku] = 'required|string|max:255|exists:inventories,sku,deleted_at,NULL,product_id,' . $product['id'];
                    $this->customAttributes[$productSku] = 'sku';
                }
            }
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return $this->customAttributes;
    }
}
