<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePackageGroupRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:package_groups,name,NULL,id,deleted_at,NULL,user_id,' . Auth::id(),
            'unit_height' => 'required|numeric|min:0|not_in:0',
            'unit_width' => 'required|numeric|min:0|not_in:0',
            'unit_length' => 'required|numeric|min:0|not_in:0',
            'barcode' => 'required|string|max:255'
        ];
    }
}
