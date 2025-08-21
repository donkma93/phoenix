<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class PackageCreateWithGroupRequest extends FormRequest
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

        $groupType = $this->request->get('group_type');

        $rules = [
            'package.*.unit_number' => 'required|integer|min:1',
            'package.*.received_unit_number' => 'required|integer|min:1',
            'package.*.warehouse_area_id' => 'nullable|exists:warehouse_areas,id',
            'package.*.status' => 'required|integer|in:'. $statusName,
            'package.*.weight' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
            'package.*.width' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
            'package.*.length' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
            'package.*.height' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
        ];

        if($groupType == 'new') {
            $rules['name'] = 'required|max:255';
            $rules['barcode'] = 'nullable|unique:packages|unique:package_groups|unique:warehouse_areas';
            $rules['unit_width'] = 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/';
            $rules['unit_length'] = 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/';
            $rules['unit_height'] = 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/';
            $rules['unit_weight'] = 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/';
            $rules['file'] = 'nullable|mimes:jpeg,png,pdf|max:5120';
            $rules['email'] = 'required|exists:users,email';
        } else if($groupType == 'exited') {
            $rule['group_id'] = 'required|exists:package_groups';
            $rule['user_id'] = 'required|exists:users';
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
        return [
            'barcode' => 'group code',
        ];
    }
}
