<?php

namespace App\Http\Requests\Staff;

use App\Models\UserRequest;
use Illuminate\Foundation\Http\FormRequest;

class AddPackageRequest extends FormRequest
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
        $statusName = implode(',', array_keys(\App\Models\Package::$statusName));
        
        $rules = [
            'user_id' => 'required|exists:users,id',
            'package_group_id' => 'required|exists:package_groups,id',
            'package.*.warehouse_area_id' => 'nullable|exists:warehouse_areas,id',
            'package.*.warehouse_area_name' => 'nullable|exists:warehouse_areas,name',
            'package.*.status' => 'required|integer|in:'. $statusName,
        ];
        
        $requestType = $this->request->get('type_name');
        $requestId = $this->request->get('user_request_id');
        
        if($requestType == 'add package') {
            $userRequest = UserRequest::with('requestPackage')->find($requestId);
            
            $rules['package.*.received_unit_number'] = 'required|integer|between:0,'.(int)$userRequest->requestPackage->unit_number;
            
        } else {
            $rules['package.*.received_unit_number'] = 'required|integer';
        }

        return $rules;
    }
}
