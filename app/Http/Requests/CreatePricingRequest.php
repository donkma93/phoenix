<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePricingRequest extends FormRequest
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
            'email' => 'required|string|email|max:255',
            'name' => 'required|string|max:50',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'company' => 'required|string|max:50',
            'services' => 'required'
        ];
    }
}
