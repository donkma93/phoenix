<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
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
        $statusName = implode(',', array_keys(\App\Models\Order::$statusName));

        return [
            'id' => 'required|exists:orders',
            'status' => 'required|in:'. $statusName,
        ];
    }
}
