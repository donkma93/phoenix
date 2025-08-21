<?php

namespace App\Http\Requests\Admin;

use App\Models\MRequestType;
use App\Models\UnitPrice;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUnitPriceRequest extends FormRequest
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

        $requestTypes = MRequestType::all()->pluck('name')->toArray();
        $requestTypes[] = "storage";
        $requestTypes[] = "tax";

        $rules = [
            'type' => 'required|string|in:' . implode(',', array_values($requestTypes))
        ];

        if($type == 'tax') {
            $rules['tax'] = 'required|numeric|min:0|not_in:0';
        }

        if($type == 'storage') {
            $rules['unit'] = 'required|array|min:1';
            $rules['unit.*.id'] = 'required|distinct|exists:storage_prices,id,deleted_at,NULL';
            // $rules['unit.*.month'] = 'required|distinct|numeric|min:1';
            $rules['unit.*.price'] = 'required|numeric|min:0|not_in:0';
            // $rules['unit.*.price'] = 'required|regex:/^[0-9]+(\.[0-9]+)?$/';
        }

        if (in_array($type, ['relabel', 'removal'])) {
            $rules['unit'] = 'required|array|min:1';

            $rules['unit.*.id'] = ['required', 'distinct', function ($attribute, $values, $fail) use ($type) {
                $isValid = UnitPrice::where('id', $values)
                    ->whereHas('mRequestType', function ($query) use ($type) {
                        $query->where('name', $type);
                    })->exists();

                if (!$isValid) {
                    $fail(__('The selected unit is invalid.'));
                }
            }];

            $rules['unit.*.min_size_price'] = 'required|numeric|min:0|not_in:0';
            $rules['unit.*.max_size_price'] = 'required|numeric|min:0|not_in:0';
        }

        if (in_array($type, ['return', 'repack', 'outbound', 'add package', 'warehouse labor'])) {
            $rules['id'] = ['required', 'distinct', function ($attribute, $values, $fail) use ($type) {
                $isValid = UnitPrice::where('id', $values)
                    ->whereHas('mRequestType', function ($query) use ($type) {
                        $query->where('name', $type);
                    })->exists();

                if (!$isValid) {
                    $fail(__('The selected unit is invalid.'));
                }
            }];

            $rules['min_size_price'] = 'required|numeric|min:0|not_in:0';
            if ($type == "repack" || $type == "warehouse labor") {
                $rules['max_size_price'] = 'required|numeric|min:0|not_in:0';
            }
        }
        return $rules;
    }

    public function attributes()
    {
        return [
            'type' => 'type',
            'unit.*.id' => 'selected unit',
            'unit.*.price' => 'price',
            'unit.*.min_size_price' => 'price',
            'unit.*.max_size_price' => 'price',
            'min_size_price' => 'price',
            'max_size_price' => 'price',
        ];
    }

}
