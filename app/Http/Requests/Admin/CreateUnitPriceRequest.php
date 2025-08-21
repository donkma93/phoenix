<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateUnitPriceRequest extends FormRequest
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
        $type = $this->request->get('type');
        $rules = [];

        if($type == 0) {
            $rules['month'] = 'required|numeric|min:1|max:12';
            $rules['price'] = 'required|regex:/^[0-9]+(\.[0-9]+)?$/';
        } else {
            $rules['min_unit'] = 'nullable|numeric|min:0';
            $rules['max_unit'] = 'nullable|numeric|min:1';
            $rules['hour'] = 'nullable|numeric|min:0';
            $rules['weight'] = 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/';
            $rules['length'] = 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/';
            $rules['price'] = 'required|regex:/^[0-9]+(\.[0-9]+)?$/';
        }

        return $rules;
    }
}
