<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Package;

class UpdatePackageSavedRequest extends FormRequest
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
        $type = $this->request->get('type_name');

        $rule = [
            'package.*.warehouse' => 'nullable|exists:warehouse_areas,name',
            'package.*.status' => 'required|integer|in:'. $statusName,
            'package.*.barcode' => 'required',
        ];

        $packagesValidate = $this->request->get('package');
        foreach($packagesValidate as $key => $package){
            if(isset($package['barcode'])) {
                $getPackage = Package::find($key);
                if($package['barcode'] !=  $getPackage['barcode']) {
                    $rule['package.'. $key .'.barcode'] = 'required|unique:packages|unique:package_groups|unique:warehouse_areas';
                }
            } else {
                $rule['package.'. $key .'.barcode'] = 'required';
            }
            if(in_array($type, ["add package", "removal", "return"])) {
                $rule['package.'. $key .'.received_unit_number'] = 'required|integer|min:1|max:'.$package['unit_number'];
            } 
        }
        
        return $rule;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'package.*.warehouse' => 'warehouse area',
            'package.*.status' => 'status',
            'package.*.barcode' => 'package code',
        ];
    }
}
