<?php

namespace App\Http\Requests\User;

use App\Models\MRequestType;
use App\Models\UserRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUserRequestRequest extends FormRequest
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
        $userId = Auth::id();

        $rules = [
            // 'user_request_id' => 'required|exists:user_requests,id,deleted_at,NULL,user_id,' . $userId . ',status,' . UserRequest::STATUS_NEW,
            'user_request_id' => ['required', function ($attribute, $values, $fail) {
                $isValid = UserRequest::where('id', $values)
                    ->where('user_id', Auth::id())
                    ->whereHas('mRequestType', function ($query) {
                        $query->where('name', "add package");
                    })->exists();

                if (!$isValid) {
                    $fail(__('The request is invalid.'));
                }
            }],
            'package_group' => 'required|array|min:1',
            'package_group.*.rpgId' => 'required|exists:request_package_groups,id,deleted_at,NULL|distinct',
            'package_group.*.info' => 'required|array|min:1',
            'package_group.*.info.*.rpId' => 'required|exists:request_packages,id,deleted_at,NULL|distinct',
            'package_group.*.info.*.package_width' => 'nullable|numeric|min:0|not_in:0',
            'package_group.*.info.*.package_weight' => 'nullable|numeric|min:0|not_in:0',
            'package_group.*.info.*.package_height' => 'nullable|numeric|min:0|not_in:0',
            'package_group.*.info.*.package_length' => 'nullable|numeric|min:0|not_in:0',
            'package_group.*.info.*.unit_number' => 'required|integer|min:1',
            'package_group.*.info.*.package_number' => 'required|integer|min:1',
        ];

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
            'user_request_id' => 'request',
            'package_group' => 'package group',
            'package_group.*.id' => 'package group',
            'package_group.*.rpgId' => 'package group',

            'package_group.*.info' => 'package information',
            'package_group.*.info.*.rpId' => 'package',
            'package_group.*.info.*.package_width' => 'package width',
            'package_group.*.info.*.package_weight' => 'package weight',
            'package_group.*.info.*.package_height' => 'package height',
            'package_group.*.info.*.package_length' => 'package length',
            'package_group.*.info.*.unit_number' => 'number unit per package',
            'package_group.*.info.*.package_number' => 'number package',
        ];
    }
}
