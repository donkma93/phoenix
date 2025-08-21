<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class StoreStaffOrderCsvRequest extends FormRequest
{
    /**
     * Determine if the staff is authorized to make this request.
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
            'order_file' => 'required|mimes:csv,txt,xlsx,xls',
            'user_id' => 'required|exists:users,id',
            // 'csv_package_length' => 'nullable|numeric|min:0|not_in:0',
        ];
    }
}
