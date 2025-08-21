<?php

namespace App\Http\Requests\User;

use App\Models\UserRequest;
use Illuminate\Foundation\Http\FormRequest;

class CancelUserRequestRequest extends FormRequest
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
            'id' => 'required|exists:user_requests,id,deleted_at,NULL,status,' . UserRequest::STATUS_NEW
        ];
    }

    public function attributes()
    {
        return [
            'id' => 'request'
        ];
    }
}
