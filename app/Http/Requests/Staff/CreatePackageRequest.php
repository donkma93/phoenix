<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class CreatePackageRequest extends FormRequest
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
        $statusName = implode(',', array_keys(\App\Models\Package::$statusName));
       
        return [
            'package_group_id' => 'required|exists:package_groups,id',
            'status' => 'required|integer|in:'. $statusName,
            'user_id' => 'required|exists:users,id',
            'unit' => 'required|integer|min:1',
            'number' => 'required|integer|min:1',
            'weight' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
            'length' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
            'width' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
            'height' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'unit_received.max' => 'Unit received must not be greater than unit!'
        ];
    }
}
