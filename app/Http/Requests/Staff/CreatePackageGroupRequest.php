<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class CreatePackageGroupRequest extends FormRequest
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
            'name' => 'required|max:255',
            'unit_width' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
            'unit_length' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
            'unit_height' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
            'unit_weight' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
            'file' => 'nullable|mimes:jpeg,png,pdf|max:5120',
            'email' => 'required|exists:users,email',
            'barcode' => 'nullable|unique:packages|unique:package_groups|unique:warehouse_areas',
            'image' => 'nullable'
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
            'barcode' => 'group code',
        ];
    }
}
