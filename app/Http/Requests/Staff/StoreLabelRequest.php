<?php

namespace App\Http\Requests\Staff;

use App\Models\OrderPackage;
use Illuminate\Foundation\Http\FormRequest;

class StoreLabelRequest extends FormRequest
{
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
        $sizeType = implode(',', array_keys(OrderPackage::$sizeName));
        $weightType = implode(',', array_keys(OrderPackage::$weightName));

        return [
            'shipping_name' => 'required|string|max:255',
            'shipping_street' => 'required|string|max:255',
            'shipping_address1' => 'nullable|string|max:255',
            'shipping_address2' => 'nullable|string|max:255',
            'shipping_company' => 'nullable|string|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_zip' => 'required|string|max:255',
            'shipping_province' => 'required|string|max:255',
            'shipping_country' => 'required|string|max:255',
            'shipping_phone' => 'nullable|string|max:255',

            'package_length' => 'required|numeric|min:0|not_in:0',
            'package_width' => 'required|numeric|min:0|not_in:0',
            'package_height' => 'required|numeric|min:0|not_in:0',
            'size_type' => 'required|integer|in:'. $sizeType,

            'package_weight' => 'required|numeric|min:0|not_in:0',
            'weight_type' => 'required|integer|in:'. $weightType,
        ];
    }
}
