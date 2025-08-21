<?php

namespace App\Http\Requests\User;

use App\Models\MRequestType;
use App\Models\UserRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateListRemindRequest extends FormRequest
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

        $rules = [
            'inventories.*.id' => 'required|exists:inventories,id',
            'inventories.*.min' => 'nullable|numeric|min:0',
        ];

        return $rules;
    }
}
