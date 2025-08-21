<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
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
        $statusName = implode(',', array_keys(\App\Models\Product::$statusName));
       
        return [
            'category' => 'nullable|exists:categories,name',
            'email' => 'required|exists:users,email',
            'status' => 'required|integer|in:'. $statusName,
            'name' => 'required|max:255',
            'fulfillment_fee' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
            'extra_pick_fee' => 'nullable|regex:/^[0-9]+(\.[0-9]+)?$/',
        ];
    }
}
