<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'fist_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'phone' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'birthday' => ['nullable', 'regex:/^(0[1-9]|[12][0-9]|3[01])-(0[1-9]|1[012])-(19|20)\d\d$/'],
            'post_code' => ['nullable', 'regex:/^([A-Z][A-HJ-Y]?[0-9][A-Z0-9]? ?[0-9][A-Z]{2}|GIR ?0A{2})$/'],
        ];
    }
}
