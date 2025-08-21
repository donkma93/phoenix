<?php

namespace App\Http\Requests\User;

use App\Models\UserRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreAddPackageRequest extends FormRequest
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
        $sizeType = implode(',', array_keys(UserRequest::$sizeName));
        $weightType = implode(',', array_keys(UserRequest::$weightName));
        $barcodeRule = 'nullable|string|max:255|distinct|unique:packages|unique:package_groups|unique:request_packages|unique:request_package_groups|unique:packages,unit_barcode';

        $rules = [
            'size_type' => 'required|integer|in:'. $sizeType,
            'weight_type' => 'required|integer|in:'. $weightType,

            // sender
            'sender.name' => 'required|string|max:255',
            'sender.company' => 'required|string|max:255',
            'sender.country' => 'required|string|max:255',
            'sender.street1' => 'required|string|max:255',
            'sender.state' => 'required|string|max:255',
            'sender.city' => 'required|string|max:255',
            'sender.zip' => 'required|string|max:255',
            'sender.phone' => 'required|string|max:255',
            'sender.email' => 'required|email',

            // recipient
            'recipient.name' => 'required|string|max:255',
            'recipient.company' => 'required|string|max:255',
            'recipient.country' => 'required|string|max:255',
            'recipient.street1' => 'required|string|max:255',
            'recipient.state' => 'required|string|max:255',
            'recipient.city' => 'required|string|max:255',
            'recipient.zip' => 'required|string|max:255',
            'recipient.phone' => 'nullable|string|max:255',
            'recipient.email' => 'nullable|email',
        ];

        $userId = Auth::id();


        $total = 0;
        if ($this->request->has('package_group') && is_array($this->request->get('package_group'))) {
            $total += count($this->request->get('package_group'));
        }
        if ($this->request->has('new_package_group') && is_array($this->request->get('new_package_group'))) {
            $total += count($this->request->get('new_package_group'));
        }

        if ($total) {
            $rules['package_group'] = "nullable|array";
        } else {
            $rules['package_group'] = "required|array|min:1";
        }

        $rules['package_group.*.id'] = 'required|exists:package_groups,id,deleted_at,NULL,user_id,' . $userId;
        // $rules['package_group.*.id'] = 'required|exists:package_groups,id,deleted_at,NULL,user_id,' . $userId . '|distinct';

        // $rules['package_group.*.tracking_url'] = 'nullable|array';
        // $rules['package_group.*.tracking_url.*'] = 'required|string|min:1';

        $rules['package.package_width'] = 'nullable|numeric|min:0|not_in:0';
        $rules['package.package_weight'] = 'nullable|numeric|min:0|not_in:0';
        $rules['package.package_height'] = 'nullable|numeric|min:0|not_in:0';
        $rules['package.package_length'] = 'nullable|numeric|min:0|not_in:0';
        $rules['package.package_number'] = 'required|integer|min:1';

        $rules['package_group.*.info'] = 'required|array|min:1';
        // $rules['package_group.*.info.*.size_type'] = 'required|integer|in:'. $sizeType;
        // $rules['package_group.*.info.*.weight_type'] = 'required|integer|in:'. $weightType;
        $rules['package_group.*.info.*.package_width'] = 'nullable|numeric|min:0|not_in:0';
        $rules['package_group.*.info.*.package_weight'] = 'nullable|numeric|min:0|not_in:0';
        $rules['package_group.*.info.*.package_height'] = 'nullable|numeric|min:0|not_in:0';
        $rules['package_group.*.info.*.package_length'] = 'nullable|numeric|min:0|not_in:0';

        // $rules['package_group.*.info.*.unit_number'] = 'required|integer|min:1';
        // $rules['package_group.*.info.*.package_number'] = 'required|integer|min:1';

        $rules['package_group.*.file_unit'] = 'nullable|array|max:3';
        $rules['package_group.*.file_unit.*'] = 'required|mimes:jpeg,png,jpg|max:5120';

        $rules['new_package_group'] = 'nullable|array';
        $rules['new_package_group.*.name'] = 'required|string|max:255|distinct|unique:package_groups,name,NULL,id,deleted_at,NULL,user_id,' . $userId;
        // $rules['new_package_group.*.size_type'] = 'required|integer|in:'. $sizeType;
        // $rules['new_package_group.*.weight_type'] = 'required|integer|in:'. $weightType;
        $rules['new_package_group.*.unit_width'] = 'nullable|numeric|min:0|not_in:0';
        $rules['new_package_group.*.unit_weight'] = 'nullable|numeric|min:0|not_in:0';
        $rules['new_package_group.*.unit_height'] = 'nullable|numeric|min:0|not_in:0';
        $rules['new_package_group.*.unit_length'] = 'nullable|numeric|min:0|not_in:0';

        $rules['new_package_group.*.info'] = 'required|array|min:1';
        // $rules['new_package_group.*.info.*.size_type'] = 'required|integer|in:'. $sizeType;
        // $rules['new_package_group.*.info.*.weight_type'] = 'required|integer|in:'. $weightType;
        $rules['new_package_group.*.info.*.package_width'] = 'nullable|numeric|min:0|not_in:0';
        $rules['new_package_group.*.info.*.package_weight'] = 'nullable|numeric|min:0|not_in:0';
        $rules['new_package_group.*.info.*.package_height'] = 'nullable|numeric|min:0|not_in:0';
        $rules['new_package_group.*.info.*.package_length'] = 'nullable|numeric|min:0|not_in:0';

        // $rules['new_package_group.*.info.*.unit_number'] = 'required|integer|min:1';
        // $rules['new_package_group.*.info.*.package_number'] = 'required|integer|min:1';

        // $rules['new_package_group.*.tracking_url'] = 'nullable|array';
        // $rules['new_package_group.*.tracking_url.*'] = 'required|string|min:1';
        // todo: check barcode exist in db ?
        $rules['new_package_group.*.barcode'] = $barcodeRule;
        $rules['new_package_group.*.file_barcode'] = 'nullable|mimes:jpeg,bmp,png,gif,svg,pdf|max:5120';
        $rules['new_package_group.*.file_unit'] = 'nullable|array|max:3';
        $rules['new_package_group.*.file_unit.*'] = 'required|mimes:jpeg,png,jpg|max:5120';

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
            'm_request_type_id' => 'request type',
            'packages.*' => 'packages',

            'unit_group' => 'package group',
            'unit_group.*' => 'package infomation',
            'unit_group.*.id' => 'package group',
            'unit_group.*.barcode' => 'QR code',
            'unit_group.*.file_barcode' => 'group file QR code',
            'unit_group.*.info' => 'package information',
            'unit_group.*.info.*.unit_number' => 'package unit number',
            'unit_group.*.info.*.package_number' => 'package number',

            'package_group.*.id' => 'package group',
            'package_group.*.info' => 'package information',
            'package_group.*.info.*.size_type' => 'package size type',
            'package_group.*.info.*.weight_type' => 'package weight type',
            'package_group.*.info.*.package_width' => 'package width',
            'package_group.*.info.*.package_weight' => 'package weight',
            'package_group.*.info.*.package_height' => 'package height',
            'package_group.*.info.*.package_length' => 'package length',
            'package_group.*.info.*.unit_number' => 'number unit per package',
            'package_group.*.info.*.package_number' => 'number package',
            'package_group.*.tracking_url' => 'tracking url',
            'package_group.*.tracking_url.*' => 'tracking url',
            'package_group.*.file_unit' => 'unit image',
            'package_group.*.file_unit.*' => 'unit image',

            'new_package_group.*.name' => 'package group name',
            'new_package_group.*.size_type' => 'unit size type',
            'new_package_group.*.weight_type' => 'unit weight type',
            'new_package_group.*.unit_width' => 'unit width',
            'new_package_group.*.unit_weight' => 'unit weight',
            'new_package_group.*.unit_height' => 'unit height',
            'new_package_group.*.unit_length' => 'unit length',
            'new_package_group.*.info' => 'package information',
            'new_package_group.*.info.*.size_type' => 'package size type',
            'new_package_group.*.info.*.weight_type' => 'package weight type',
            'new_package_group.*.info.*.package_width' => 'package width',
            'new_package_group.*.info.*.package_weight' => 'package weight',
            'new_package_group.*.info.*.package_height' => 'package height',
            'new_package_group.*.info.*.package_length' => 'package length',
            'new_package_group.*.info.*.unit_number' => 'number unit per package',
            'new_package_group.*.info.*.package_number' => 'number package',
            'new_package_group.*.tracking_url' => 'tracking url',
            'new_package_group.*.tracking_url.*' => 'tracking url',
            'new_package_group.*.barcode' => 'QR code',
            'new_package_group.*.file_barcode' => 'group file QR code',
            'new_package_group.*.file_unit' => 'unit image',
            'new_package_group.*.file_unit.*' => 'unit image',

            'removal_group.*.id' => 'package group',
            'removal_group.*.unit_number' => 'unit number',
            'removal_group.*.barcode' => 'QR code',
            'removal_group.*.unit_barcode' => 'unit QR code',
            'removal_group.*.file_unit_barcode' => 'unit file QR code',
            'removal_group.*.tracking_url' => 'tracking url',
            'removal_group.*.tracking_url.*' => 'tracking url',
            'removal_group.*.file_unit' => 'unit image',
            'removal_group.*.file_unit.*' => 'unit image',

            'removal_new_group.*.name' => 'package group name',
            'removal_new_group.*.size_type' => 'unit size type',
            'removal_new_group.*.weight_type' => 'unit weight type',
            'removal_new_group.*.barcode' => 'QR code',
            'removal_new_group.*.file_barcode' => 'group file QR code',
            'removal_new_group.*.unit_barcode' => 'unit QR code',
            'removal_new_group.*.file_unit_barcode' => 'group file QR code',
            'removal_new_group.*.package_number' => 'package number',
            'removal_new_group.*.unit_number' => 'unit number',
            'removal_new_group.*.unit_width' => 'unit width',
            'removal_new_group.*.unit_weight' => 'unit weight',
            'removal_new_group.*.unit_height' => 'unit height',
            'removal_new_group.*.unit_length' => 'unit length',
            'removal_new_group.*.tracking_url' => 'tracking url',
            'removal_new_group.*.tracking_url.*' => 'tracking url',
            'removal_new_group.*.file_unit' => 'unit image',
            'removal_new_group.*.file_unit.*' => 'unit image',

            'return_group.*.id' => 'package group',
            'return_group.*.unit_number' => 'unit number',
            'return_group.*.tracking_url' => 'tracking url',
            'return_group.*.tracking_url.*' => 'tracking url',
            'return_group.*.file_unit' => 'unit image',
            'return_group.*.file_unit.*' => 'unit image',
            'return_group.*.barcode' => 'barcode',
            'return_group.*.unit_barcode' => 'unit QR code',
            'return_group.*.file_unit_barcode' => 'unit file QR code',

            'return_new_group.*.name' => 'package group name',
            'return_new_group.*.size_type' => 'unit size type',
            'return_new_group.*.weight_type' => 'unit weight type',
            'return_new_group.*.unit_width' => 'unit width',
            'return_new_group.*.unit_weight' => 'unit weight',
            'return_new_group.*.unit_height' => 'unit height',
            'return_new_group.*.unit_length' => 'unit length',
            'return_new_group.*.barcode' => 'QR code',
            'return_new_group.*.file_barcode' => 'unit file QR code',
            'return_new_group.*.unit_barcode' => 'unit QR code',
            'return_new_group.*.file_unit_barcode' => 'group file QR code',
            'return_new_group.*.unit_number' => 'unit per package',
            'return_new_group.*.tracking_url' => 'tracking url',
            'return_new_group.*.tracking_url.*' => 'tracking url',
            'return_new_group.*.file_unit' => 'unit image',
            'return_new_group.*.file_unit.*' => 'unit image',
        ];
    }
}
