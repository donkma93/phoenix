<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePackageRequest extends FormRequest
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
            'id' => 'required|exists:packages',
            'weight' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
            'height' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
            'length' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
            'width' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
            'status' => 'required|integer|in:'. $statusName,
            'warehouse' => 'nullable|exists:warehouse_areas,name',
            'barcode' => 'required',
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
            'warehouse' => 'warehouse area',
            'status' => 'status',
            'barcode' => 'package code',
            'width' => 'package width',
            'length' => 'package length',
            'height' => 'package height',
            'weight' => 'package weight',
        ];
    }
}
