<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreStaffOrderRequest extends FormRequest
{
    /**
     * Determine if the staff is authorized to make this request.
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
        return [
            'shipping_name' => 'required|string|max:255|regex:/^[a-z0-9 .,\-\_]+$/i',
            'shipping_street' => 'required|string|max:35|regex:/^[a-z0-9 .,\-\_\/]+$/i',
            'shipping_address1' => 'nullable|string|max:35|regex:/^[a-z0-9 .,\-\_\/]+$/i',
            'shipping_address2' => 'nullable|string|max:35|regex:/^[a-z0-9 .,\-\_\/]+$/i',
            'shipping_company' => 'nullable|string|max:255|regex:/^[a-z0-9 .,\-\_]+$/i',
            'shipping_city' => 'required|string|max:255|regex:/^[a-z0-9 .,\-\_]+$/i',
            'shipping_zip' => 'required|string|max:255',
            'shipping_province' => 'required|string|max:255',
            'shipping_country' => 'required|string|max:255',
            'shipping_phone' => 'nullable|string|max:255',

            'order_number' => 'nullable|string|max:255',

            'product' => 'required|array|min:1',
            'product.*.id' => 'required|exists:products,id,deleted_at,NULL,user_id,' . $this->request->get('user_id'). '|distinct',
            'product.*.unit_number' => 'required|integer|min:1',
            'user_id' => 'required|exists:users,id',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'shipping_zip' => 'postal code / zip ',
            'product.*.id' => 'product',
            'product.*.unit_number' => 'unit number',
        ];
    }
}
