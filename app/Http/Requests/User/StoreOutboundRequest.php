<?php

namespace App\Http\Requests\User;

use App\Models\Package;
use App\Models\UserRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreOutboundRequest extends FormRequest
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
        $shipModes = implode(',', array_keys(UserRequest::$shipModes));

        $barcodeRule = 'nullable|string|max:255|distinct|unique:packages|unique:package_groups|unique:request_packages|unique:request_package_groups|unique:packages,unit_barcode';

        $rules = [
            'group' => 'required|array|min:1',

            'group.*.id' => 'required|exists:package_groups,id,deleted_at,NULL,user_id,' . $userId,
            'group.*.ship_mode' => 'required|integer|in:'. $shipModes,

            'group.*.file' => 'nullable|mimes:jpeg,bmp,png,gif,svg,pdf|max:5120',
            'group.*.barcode' => $barcodeRule,

            'group.*.insurance_fee' => 'nullable|integer|min:1',
            'group.*.pallet' => 'nullable|integer|min:1',

            'group.*.unit_number' => 'required|integer|min:1',
            'group.*.package_number' => 'required|integer|min:1',
        ];

        if ($this->request->has('group') && is_array($this->request->get('group'))) {
            $unitGroups = $this->request->get('group');
            foreach ($unitGroups as $groupIndex => $group) {
                if (isset($group['ship_mode'])) {
                    if ($group['ship_mode'] == UserRequest::SMALL_PARCEL) {
                        // $rules['group.' . $groupIndex . '.file'] = 'required|mimes:jpeg,bmp,png,gif,svg,pdf|max:5120';
                    } else {
                        $rules['group.' . $groupIndex . '.pallet'] = 'required|integer|min:1';
                    }
                }
            }
        }

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
            'group' => 'group',
            'group.*.id' => 'group',
            'group.*.ship_mode' => 'ship mode',
            'group.*.file' => 'file',
            'group.*.barcode' => 'barcode',
            'group.*.insurance_fee' => 'insurance_fee',
            'group.*.pallet' => 'pallet',

            'group.*.unit_number' => 'unit number',
            'group.*.package_number' => 'package number',
        ];
    }
}
