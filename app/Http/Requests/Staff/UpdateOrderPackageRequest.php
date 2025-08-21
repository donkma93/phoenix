<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderPackageRequest extends FormRequest
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
            'id' => 'required|exists:order_package,order_id',
            'length' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
            'width' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
            'height' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
            'weight' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
        ];
    }
}
