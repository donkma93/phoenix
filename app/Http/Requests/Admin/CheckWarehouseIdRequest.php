<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CheckWarehouseIdRequest extends FormRequest
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
            'name' => 'required|unique:warehouse_areas',
            'warehouse' => 'required|exists:warehouses,name,deleted_at,NULL',
            'barcode' => 'required|unique:packages|unique:package_groups|unique:warehouse_areas',
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
            'barcode' => 'warehouse area code',
        ];
    }
}
