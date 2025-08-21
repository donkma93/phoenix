<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePackageGroupRequest extends FormRequest
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
            'id' => 'required|exists:package_groups,id',
            'name' => 'required|max:255',
            'unit_width' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
            'unit_length' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
            'unit_height' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
            'unit_weight' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
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
            'name' => 'group name',
            'barcode' => 'group code',
            'unit_width' => 'unit width',
            'unit_height' => 'unit height',
            'unit_weight' => 'unit weight',
            'unit_length' => 'unit length',
        ];
    }
}
