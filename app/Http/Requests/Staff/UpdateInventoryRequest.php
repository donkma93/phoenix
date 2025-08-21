<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryRequest extends FormRequest
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
        return [
            'id' => 'required|exists:inventories,id',
            'store' => 'nullable|exists:store_fulfills,name',
            'available' => 'nullable|numeric|min:0',
            'incoming' => 'nullable|numeric|min:0',
            'hour' => 'nullable|numeric',
            'startDate' => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }
}
