<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SendInvoiceRequest extends FormRequest
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
        $currentYear = date("Y");

        return [
            'target_month' => 'required|integer|min:1|max:12',
            'target_year' => 'required|integer|min:1990|max:' . $currentYear
        ];
    }

    public function attributes()
    {
        return [
            'target_month' => 'month',
            'target_year' => 'year'
        ];
    }
}
