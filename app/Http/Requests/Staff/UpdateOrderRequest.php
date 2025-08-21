<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
        $paymentName = implode(',', array_keys(\App\Models\Order::$paymentName));
        $fulfillName = implode(',', array_keys(\App\Models\Order::$fulfillName));
        $pickingName = implode(',', array_keys(\App\Models\Order::$pickingName));
        
        return [
            'id' => 'required|exists:orders',
            'payment' => 'required|in:'. $paymentName,
            'fulfill_name' => 'required|in:'. $fulfillName,
            'ship_rate' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
            'picking_status' => 'required|in:'. $pickingName,
        ];
    }
}
