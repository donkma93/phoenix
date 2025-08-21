<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class Order2Controller extends Controller
{
    public function create()
    {
        $data = [];
        $countries = DB::table('sys_country')->get()->toArray();
        //$states = DB::table('sys_states')->get()->toArray();
        $data['countries'] = $countries ?? [];
        $data['states'] = $states ?? [];

        return view('order2.create', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipper_company' => ['nullable', 'string', 'max:100'],
            'shipper_name' => ['required', 'string', 'max:50'],
            'shipper_phone' => ['required', 'string', 'alpha_dash', 'max:50'],
            'shipper_country' => ['required', 'string', 'max:50'],
            'shipper_province' => ['required', 'string', 'max:50'],
            'shipper_address' => ['required', 'string', 'max:255'],
            'pickup_address' => ['nullable', 'string', 'max:255'],
            'receiver_company' => ['nullable', 'string', 'max:100'],
            'receiver_name' => ['required', 'string', 'max:50'],
            'receiver_phone' => ['required', 'string', 'alpha_dash', 'max:50'],
            'receiver_country' => ['required', 'string', 'max:50'],
            'receiver_province' => ['required', 'string', 'max:50'],
            'receiver_address' => ['required', 'string', 'max:255'],
            'commodity' => ['required', 'string', 'max:10'],
            'service_of_order' => ['required', 'string', 'max:20'],
            'surcharge_name' => ['required_with:surcharge_fee'],
            'surcharge_fee' => ['required_with:surcharge_name'],
        ]);

        $shipper = DB::table('od_shippers')->where('shipper_phone', $validated['shipper_phone'])
            ->where('shipper_country', $validated['shipper_country'])
            ->where('shipper_province', $validated['shipper_province'])
            ->where('shipper_address', $validated['shipper_address'])
            ->first();
        $receiver = DB::table('od_receivers')->where('receiver_phone', $validated['receiver_phone'])
            ->where('receiver_country', $validated['receiver_country'])
            ->where('receiver_province', $validated['receiver_province'])
            ->where('receiver_address', $validated['receiver_address'])
            ->first();

        if (!$shipper) {
            $shipper_id = DB::table('od_shippers')->insertGetId([
                'shipper_company' => $validated['shipper_company'],
                'shipper_name' => $validated['shipper_name'],
                'shipper_phone' => $validated['shipper_phone'],
                'shipper_country' => $validated['shipper_country'],
                'shipper_province' => $validated['shipper_province'],
                'shipper_address' => $validated['shipper_address'],
                'pickup_address' => $validated['pickup_address'],
                'user_create' => auth()->user()->id
            ]);
        } else {
            $shipper_id = $shipper->shipper_id;
        }

        if (!$receiver) {
            $receiver_id = DB::table('od_receivers')->insertGetId([
                'receiver_company' => $validated['receiver_company'],
                'receiver_name' => $validated['receiver_name'],
                'receiver_phone' => $validated['receiver_phone'],
                'receiver_country' => $validated['receiver_country'],
                'receiver_province' => $validated['receiver_province'],
                'receiver_address' => $validated['receiver_address'],
                'user_create' => auth()->user()->id
            ]);
        } else {
            $receiver_id = $receiver->receiver_id;
        }

        $orderId = DB::table('od_orders')->insertGetId([
            'shipper_id' => $shipper_id,
            'receiver_id' => $receiver_id,
            'commodity' => $validated['commodity'],
            'service_of_order' => $validated['service_of_order'],
            'surcharge' => isset($validated['surcharge_name']) && $validated['surcharge_fee'] ? json_encode(['name' => $validated['surcharge_name'], 'fee' => $validated['surcharge_fee']]) : null,
            'user_create' => auth()->user()->id,
            'user_edit' => auth()->user()->id
        ]);

        return redirect()->route('staff.order2.addDetails', ['id' => $orderId])->with('success', 'Created order successfully.');
    }

    public function addDetails(Request $request, $orderId)
    {
        $orderDetails = DB::table('od_orders_details')->where('order_id', $orderId)->where('deleted_at', null)->get()->toArray();
        if ($orderDetails) {
            return redirect()->route('staff.order2.create')->with('warning', 'Order details already exists.');
        }

        $order = DB::table('od_orders')
            ->select('od_orders.*', 'u.email as user_create_email', 'u2.email as user_edit_email', 's.shipper_company', 's.shipper_name', 's.shipper_phone', 's.shipper_country', 's.shipper_province', 's.shipper_address', 's.pickup_address', 'r.receiver_company', 'r.receiver_name', 'r.receiver_phone', 'r.receiver_country', 'r.receiver_province', 'r.receiver_address')
            ->join('od_shippers as s', 'od_orders.shipper_id', 's.shipper_id')
            ->join('od_receivers as r', 'od_orders.receiver_id', 'r.receiver_id')
            ->join('users as u', 'od_orders.user_create', 'u.id')
            ->join('users as u2', 'od_orders.user_edit', 'u2.id')
            ->where('od_orders.order_id', $orderId)
            ->where('od_orders.deleted_at', null)
            ->first();

        return view('order2.create-details', compact('order'));
    }

    public function storeDetails(Request $request, $orderId)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer',
            'air_waybill' => [
                'nullable',
                'alpha_dash',
                'max:100',
                Rule::unique('od_orders')->where(function (Builder $query) {    // rule này đảm bảo rằng giá trị input air_waybill sẽ unique trong cột air_waybill của bảng od_orders và chỉ xét các dòng có giá trị deleted_at là null
                    return $query->where('deleted_at', null);
                }),
            ],
            'note_total' => 'nullable|string|max:1000',
            'surcharge_name' => ['required_with:surcharge_fee'],
            'surcharge_fee' => ['required_with:surcharge_name'],
            'details' => 'required|array|min:1',
            'details.*.bill_code_detail' => [
                'nullable',
                'alpha_dash',
                'max:100',
                Rule::unique('od_orders_details')->where(function (Builder $query) {
                    return $query->where('deleted_at', null);
                }),
            ],
            'details.*.type_of_commodity' => 'required|string|max:50',
            'details.*.length' => 'required|numeric|min:0.1',
            'details.*.width' => 'required|numeric|min:0.1',
            'details.*.height' => 'required|numeric|min:0.1',
            'details.*.pack_bill_weight' => 'required|numeric|min:0.01',
            'details.*.actual_weight' => 'required|numeric|min:0.01',
            'details.*.bulky_weight' => 'required|numeric|min:0.01',
            'details.*.billable_weight' => 'required|numeric|min:0.01',
            'details.*.image' => 'nullable|image',
            'details.*.note_detail' => 'nullable|string|max:1000',
            'details.*.quantity' => 'required|numeric|min:1',
            'details.*.unit_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Retrieve the validated input...
        $validated = $validator->validated();

        // Lấy ra mảng các bill code chi tiết
        $arrBillCode = array_column($validated['details'], 'bill_code_detail');
        // Lấy các giá trị unique
        $uniqueBillCode = array_unique($arrBillCode);
        // Nếu 2 mảng trên số phần tử khác nhau chứng tỏ có phần tử trùng mã => báo lỗi
        /*if (count($arrBillCode) !== count($uniqueBillCode)) {
            return back()->with('error', 'Bill code detail cannot be duplicated.');
        }*/

        if ($validated['order_id'] != $orderId) {
            return back()->with('error', 'Invalid ID, please check again!');
        }

        if ($validated['air_waybill'] || $validated['note_total'] || $validated['surcharge_name'] || $validated['surcharge_fee']) {
            DB::table('od_orders')->where('order_id', $validated['order_id'])->update([
                'air_waybill' => $validated['air_waybill'],
                'note_total' => $validated['note_total'],
                'surcharge' => isset($validated['surcharge_name']) && $validated['surcharge_fee'] ? json_encode(['name' => $validated['surcharge_name'], 'fee' => $validated['surcharge_fee']]) : null,
                'user_edit' => auth()->user()->id,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            DB::table('od_orders')->where('order_id', $validated['order_id'])->update([
                'user_edit' => auth()->user()->id,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        foreach ($validated['details'] as $detail) {
            $file = $detail['image'] ?? null;
            $pathImg = null;
            if ($file) {
                $ext = $file->extension();
                $old_name = $file->getClientoriginalName();
                $new_name = 'PNX_DWS/' . date('Ym') . '/' . time() . '_' . $this->clean_str($old_name, '/[^0-9a-zA-Z._-]/');
                $path = Storage::disk('s3')->put($new_name, file_get_contents($file), 'public');
                $pathImg = 'https://leuleu-ffm.hn.ss.bfcplatform.vn' . '/' . $new_name;
            }

            DB::table('od_orders_details')->insert([
                'order_id' => $validated['order_id'],
                'bill_code_detail' => $detail['bill_code_detail'] ?? null,
                'type_of_commodity' => $detail['type_of_commodity'],
                'length' => $detail['length'],
                'width' => $detail['width'],
                'height' => $detail['height'],
                'pack_bill_weight' => $detail['pack_bill_weight'] * 1000,
                'actual_weight' => $detail['actual_weight'] * 1000,
                'bulky_weight' => $detail['bulky_weight'] * 1000,
                'billable_weight' => $detail['billable_weight'] * 1000,
                'note_detail' => $detail['note_detail'],
                'quantity' => $detail['quantity'],
                'unit_price' => $detail['unit_price'],
                'img_url' => $pathImg,
            ]);
        }

        return redirect()->route('staff.order2.create')->with('success', 'Added order details successfully.');
    }

    function clean_str($string, $pattern)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

        return preg_replace($pattern, '', $string); // Removes special chars.
    }

    public function orderDetails($orderId)
    {
        $order = DB::table('od_orders')
            ->select('od_orders.*', 'u.email as user_create_email', 'u2.email as user_edit_email', 's.shipper_company', 's.shipper_name', 's.shipper_phone', 's.shipper_country', 's.shipper_province', 's.shipper_address', 's.pickup_address', 'r.receiver_company', 'r.receiver_name', 'r.receiver_phone', 'r.receiver_country', 'r.receiver_province', 'r.receiver_address')
            ->join('od_shippers as s', 'od_orders.shipper_id', 's.shipper_id')
            ->join('od_receivers as r', 'od_orders.receiver_id', 'r.receiver_id')
            ->join('users as u', 'od_orders.user_create', 'u.id')
            ->join('users as u2', 'od_orders.user_edit', 'u2.id')
            ->where('od_orders.order_id', $orderId)
            ->where('od_orders.deleted_at', null)
            ->first();

        $orderDetails = DB::table('od_orders_details as od')
            ->select('od.*')
            ->join('od_orders as o', 'od.order_id', 'o.order_id')
            ->where('od.order_id', $orderId)
            ->where('od.deleted_at', null)
            ->where('o.deleted_at', null)
            ->get()->toArray();

        return view('order2.order-details', compact('order', 'orderDetails'));
    }

    public function updateOrder(Request $request, $orderId)
    {
        if ($request->get('order_id') != $orderId) {
            return back()->with('error', 'Invalid ID, please check again!');
        }

        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer',
            'air_waybill' => [
                'nullable',
                'alpha_dash',
                'max:100',
                Rule::unique('od_orders')->ignore($orderId, 'order_id')->where(function (Builder $query) {    // rule này đảm bảo rằng giá trị input air_waybill sẽ unique trong cột air_waybill của bảng od_orders và chỉ xét các dòng có giá trị deleted_at là null, bỏ qua chính nó
                    return $query->where('deleted_at', null);
                }),
            ],
            'note_total' => 'nullable|string|max:1000',
            'surcharge_name' => ['required_with:surcharge_fee'],
            'surcharge_fee' => ['required_with:surcharge_name'],
            'details' => 'required|array|min:1',
            'details.*.bill_code_detail' => [
                'nullable',
                'alpha_dash',
                'max:100',
                Rule::unique('od_orders_details')->ignore($orderId, 'order_id')->where(function (Builder $query) {
                    return $query->where('deleted_at', null);
                }),
            ],
            'details.*.type_of_commodity' => 'required|string|max:50',
            'details.*.length' => 'required|numeric|min:0.1',
            'details.*.width' => 'required|numeric|min:0.1',
            'details.*.height' => 'required|numeric|min:0.1',
            'details.*.pack_bill_weight' => 'required|numeric|min:0.01',
            'details.*.actual_weight' => 'required|numeric|min:0.01',
            'details.*.bulky_weight' => 'required|numeric|min:0.01',
            'details.*.billable_weight' => 'required|numeric|min:0.01',
            'details.*.image' => 'nullable|image',
            'details.*.note_detail' => 'nullable|string|max:1000',
            'details.*.quantity' => 'required|numeric|min:1',
            'details.*.unit_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Retrieve the validated input...
        $validated = $validator->validated();

        // Lấy ra mảng các bill code chi tiết
        $arrBillCode = [];
        $arrBillCodeAll = array_column($validated['details'], 'bill_code_detail');
        foreach ($arrBillCodeAll as $item) {
            if ($item) {
                array_push($arrBillCode, $item);
            }
        }

        // Lấy các giá trị unique
        $uniqueBillCode = array_unique($arrBillCode);
        // Nếu 2 mảng trên số phần tử khác nhau chứng tỏ có phần tử trùng mã => báo lỗi
        if (count($arrBillCode) !== count($uniqueBillCode)) {
            return back()->with('error', 'Bill code detail cannot be duplicated.');
        }

        DB::table('od_orders')->where('order_id', $validated['order_id'])->update([
            'air_waybill' => $validated['air_waybill'],
            'note_total' => $validated['note_total'],
            'surcharge' => isset($validated['surcharge_name']) && $validated['surcharge_fee'] ? json_encode(['name' => $validated['surcharge_name'], 'fee' => $validated['surcharge_fee']]) : null,
            'user_edit' => auth()->user()->id,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        /*$orderDetails = DB::table('od_orders_details')->where('order_id', $orderId)->where('deleted_at', null)->get()->toArray();
        if ($orderDetails) {
            DB::table('od_orders_details')->where('order_id', $orderId)->where('deleted_at', null)->update([
                'deleted_at' => date('Y-m-d H:i:s')
            ]);
        }*/

        //dd($validated['details']);

        foreach ($validated['details'] as $id => $detail) {
            $file = $detail['image'] ?? null;
            $pathImg = null;
            if ($file) {
                $ext = $file->extension();
                $old_name = $file->getClientoriginalName();
                $new_name = 'PNX_DWS/' . date('Ym') . '/' . time() . '_' . rand(1000, 9999) . '_' . $this->clean_str($old_name, '/[^0-9a-zA-Z._-]/');
                $path = Storage::disk('s3')->put($new_name, file_get_contents($file), 'public');
                $pathImg = 'https://leuleu-ffm.hn.ss.bfcplatform.vn' . '/' . $new_name;
            } else {
                $pathImg = DB::table('od_orders_details')->where('id', $id)->pluck('img_url')->first();
            }

            DB::table('od_orders_details')->where('id', $id)
                ->update([
                    'order_id' => $validated['order_id'],
                    'bill_code_detail' => $detail['bill_code_detail'],
                    'type_of_commodity' => $detail['type_of_commodity'],
                    'length' => $detail['length'],
                    'width' => $detail['width'],
                    'height' => $detail['height'],
                    'pack_bill_weight' => $detail['pack_bill_weight'] * 1000,
                    'actual_weight' => $detail['actual_weight'] * 1000,
                    'bulky_weight' => $detail['bulky_weight'] * 1000,
                    'billable_weight' => $detail['billable_weight'] * 1000,
                    'img_url' => $pathImg,
                    'note_detail' => $detail['note_detail'],
                    'quantity' => $detail['quantity'],
                    'unit_price' => $detail['unit_price'],
                ]);
        }

        return back()->with('success', 'Updated order details successfully.');
    }

    public function getSuggestionShipper($keyword)
    {
        $data = DB::table('od_shippers')
            ->where('shipper_company', 'like', '%' . $keyword . '%')
            ->orWhere('shipper_name', 'like', '%' . $keyword . '%')
            ->orWhere('shipper_phone', 'like', '%' . $keyword . '%')
            ->orWhere('shipper_country', 'like', '%' . $keyword . '%')
            ->orWhere('shipper_province', 'like', '%' . $keyword . '%')
            ->orWhere('shipper_address', 'like', '%' . $keyword . '%')
            ->orWhere('pickup_address', 'like', '%' . $keyword . '%')
            ->get()->toArray();

        return response()->json($data);
    }

    public function getSuggestionReceiver($keyword)
    {
        $data = DB::table('od_receivers')
            ->where('receiver_company', 'like', '%' . $keyword . '%')
            ->orWhere('receiver_name', 'like', '%' . $keyword . '%')
            ->orWhere('receiver_phone', 'like', '%' . $keyword . '%')
            ->orWhere('receiver_country', 'like', '%' . $keyword . '%')
            ->orWhere('receiver_province', 'like', '%' . $keyword . '%')
            ->orWhere('receiver_address', 'like', '%' . $keyword . '%')
            ->get()->toArray();

        return response()->json($data);
    }

    public function listOrders()
    {
        $orders = DB::table('od_orders')
            ->select('od_orders.*', 's.shipper_company', 's.shipper_name', 's.shipper_phone', 's.shipper_country', 's.shipper_province', 's.shipper_address', 'r.receiver_company', 'r.receiver_name', 'r.receiver_phone', 'r.receiver_country', 'r.receiver_province', 'r.receiver_address')
            ->selectRaw('(SELECT COUNT(*) FROM `od_orders_details` WHERE `od_orders`.`order_id` = `od_orders_details`.`order_id` AND `od_orders_details`.`deleted_at` IS NULL AND `od_orders`.`deleted_at` IS NULL) AS count_details')
            ->join('od_shippers as s', 'od_orders.shipper_id', 's.shipper_id')
            ->join('od_receivers as r', 'od_orders.receiver_id', 'r.receiver_id')
            ->where('od_orders.deleted_at', null)
            ->orderBy('od_orders.created_at', 'desc')
            ->get()->toArray();

        return view('order2.list-orders', compact('orders'));
    }

    public function report(Request $request)
    {
        $input_data = $request->all();
        $date_from = $input_data['date_from'] ?? date('Y-m-d', strtotime('-1 month'));
        $date_to = $input_data['date_to'] ?? date('Y-m-d');
        if (strtotime($date_from) > strtotime($date_to)) {
            return redirect()->route('staff.order2.report')->with('error', 'From date cannot be greater than To date.');
        }

        $query = DB::table('od_orders')
            ->select('od_orders.*', 's.shipper_company', 's.shipper_name', 's.shipper_phone', 's.shipper_country', 's.shipper_province', 's.shipper_address',
                'r.receiver_company', 'r.receiver_name', 'r.receiver_phone', 'r.receiver_country', 'r.receiver_province', 'r.receiver_address', 'od.id as order_detail_id', 'od.bill_code_detail',
                'od.img_url', 'od.type_of_commodity', 'od.length', 'od.width', 'od.height', 'od.pack_bill_weight', 'od.actual_weight', 'od.bulky_weight', 'od.billable_weight', 'od.note_detail', 'od.quantity', 'od.unit_price')
            ->selectRaw('(SELECT COUNT(*) FROM `od_orders_details` WHERE `od_orders`.`order_id` = `od_orders_details`.`order_id` AND
            `od_orders_details`.`deleted_at` IS NULL AND `od_orders`.`deleted_at` IS NULL) AS count_details')
            ->selectRaw('(SELECT SUM(`od_orders_details`.`pack_bill_weight`) FROM `od_orders_details` WHERE `od_orders`.`order_id` = `od_orders_details`.`order_id` AND
            `od_orders_details`.`deleted_at` IS NULL AND `od_orders`.`deleted_at` IS NULL) AS total_bill_weight')
            ->join('od_shippers as s', 'od_orders.shipper_id', 's.shipper_id')
            ->join('od_receivers as r', 'od_orders.receiver_id', 'r.receiver_id')
            ->join('od_orders_details as od', 'od.order_id', 'od_orders.order_id')
            ->where('od_orders.deleted_at', null)
            ->where('od.deleted_at', null)
            ->where('s.deleted_at', null)
            ->where('r.deleted_at', null);

        if ($date_from) {
            $query = $query->where('od_orders.created_at', '>=', $date_from . ' 00:00:00');
        }

        if ($date_to) {
            $query = $query->where('od_orders.created_at', '<=', $date_to . ' 23:59:59');
        }

        $orders = $query->orderBy('od_orders.created_at', 'desc')->paginate(20);

        return view('order2.report', compact('orders'));
    }
}
