<?php

namespace App\Services\Staff;

use App\Imports\Staff\StaffOrdersImport;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderTransaction;
use App\Models\OrderPackage;
use App\Models\OrderProduct;
use App\Models\OrderRate;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\StaffBaseServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Excel;
use File;
use Response;
use Illuminate\Support\Facades\Storage;
use Exception;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use Illuminate\Support\Facades\Log;
use Shippo_Shipment;
use Shippo_Order;
use App\Imports\ImportPricesExcel;
use App\Models\PriceList;
use App\Imports\Staff\StaffLabelsImport;

class StaffOrderService extends StaffBaseService implements StaffBaseServiceInterface
{

    public function storePricesExcel($file, $table_id)
    {
        DB::beginTransaction();
        try {
            $import = new ImportPricesExcel();

            Excel::import($import, $file);

            $fileMove = $file->move(PriceList::FOLDER_DEFAULT, cleanName($file->getClientOriginalName()));

            if (count($import->errors)) {
                return [
                    'isValid' => false,
                    'errors' => $import->errors
                ];
            }

            $check_price_exist = DB::table('pnx_price_tabledetails')->where('id_price_table', $table_id)->get()->toArray();

            if (!!$check_price_exist) {
                return [
                    'isValid' => false,
                    'errors' => 'Price list already exists!'
                ];
            }

            foreach ($import->rows as $key => $row) {
                $result = DB::table('pnx_price_tabledetails')->insert([
                    'id_price_table' => $table_id,
                    'price' => $row['price'],
                    'weight' => $row['weight'],
                    'destination' => $row['destination'],
                ]);
            }

            DB::commit();

            return [
                'isValid' => true,
                'rawData' => $import->rows,
            ];
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }


    public function storeCsv($file, $request)
    {

        DB::beginTransaction();
        try {
            $import = new StaffOrdersImport($request['user_id']);

            Excel::import($import, $file);

            $fileMove = $file->move('imgs' . DIRECTORY_SEPARATOR . Order::IMG_FOLDER, cleanName($file->getClientOriginalName()));

            if (count($import->errors)) {
                return [
                    'isValid' => false,
                    'errors' => $import->errors
                ];
            }

            $orderNumbers = $orderIds = [];
            $orderProducts = $orderPackages = [];
            $products = $import->products;
            $now = Carbon::now();

            foreach ($import->rows as $key => $row) {
                $orderAddressTo = OrderAddress::create($import->addresses[$key]);

                if (!isset($row['order_number']) || !in_array($row['order_number'], $orderNumbers)) {
                    $newOrder = Order::create([
                        'date' => $row['created_at'] ?? null,  // TODO: need convert Timezone when save (?)
                        'order_address_to_id' => $orderAddressTo->id,
                        'order_number' => $row['order_number'] ?? null,

                        'item_quantity' => $row['lineitem_quantity'],
                        'item_name' => $row['lineitem_name'],
                        'item_price' => $row['lineitem_price'] ?? null,
                        'item_compare_at_price' => $row['lineitem_compare_at_price'] ?? null,
                        'item_sku' => $row['lineitem_sku'],
                        'item_requires_shipping' => $row['lineitem_requires_shipping'] ?? null,
                        'item_taxable' => $row['lineitem_taxable'] ?? null,
                        'item_fulfillment_status' => $row['lineitem_fulfillment_status'] ?? null,

                        'payment' => Order::PAYMENT_UNPAY,
                        'fulfillment' => isset($row['fulfillment_status']) ? Order::getFulfillmentStatus($row['fulfillment_status']) : Order::UNFULFILLED,
                        'status' => Order::STATUS_NEW,
                        'state' => Order::STATE_NONE,
                        'user_id' => $request['user_id'],

                        'content' => $row,
                        'file' => $fileMove,
                    ]);

                    if (isset($row['order_number'])) {
                        array_push($orderNumbers, $row['order_number']);
                        array_push($orderIds, $newOrder->id);
                    }

                    $orderId = $newOrder->id;
                } else {
                    $index = array_search($row['order_number'], $orderNumbers);
                    $orderId = $orderIds[$index];
                }

                $targetProduct = $products[$row['lineitem_name']];

                $orderProducts[] = [
                    'order_id' => $orderId,
                    'product_id' => $targetProduct->id,
                    'quantity' => $row['lineitem_quantity'],
                    'total_fee' => $row['lineitem_price'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $hasPackageInfo = isset($row['lineitem_quantity']) && $row['lineitem_quantity'] == 1;

                $orderPackages[] = [
                    'order_id' => $orderId,
                    'width' => $hasPackageInfo ? $targetProduct->packageGroupWithTrashed->unit_width ?? null : null,
                    'height' => $hasPackageInfo ? $targetProduct->packageGroupWithTrashed->unit_height ?? null : null,
                    'length' => $hasPackageInfo ? $targetProduct->packageGroupWithTrashed->unit_length ?? null : null,
                    'weight' => $hasPackageInfo ? $targetProduct->packageGroupWithTrashed->unit_weight ?? null : null,
                    'size_type' => $hasPackageInfo ? OrderPackage::SIZE_IN : null,
                    'weight_type' => $hasPackageInfo ? OrderPackage::WEIGHT_LB : null,

                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (count($orderProducts)) {
                OrderProduct::insert($orderProducts);
            }

            if (count($orderPackages)) {
                OrderPackage::insert($orderPackages);
            }

            DB::commit();

            return [
                'isValid' => true,
                'rawData' => $import->rows,
            ];
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }


    public function storeExcelG7($file, $request)
    {
        //DB::beginTransaction();
        try {
            $import = new StaffLabelsImport();

            Excel::import($import, $file);

            //$fileMove = $file->move('imgs' . DIRECTORY_SEPARATOR . Order::IMG_FOLDER, cleanName($file->getClientOriginalName()));

            if (count($import->errors)) {
                return [
                    'isValid' => false,
                    'message' => 'Validate failed',
                    'errors' => $import->errors
                ];
            }

            $now = Carbon::now();
            $ordersError = [];
            $ordersSkip = [];

            foreach ($import->rows as $key => $row) {
                $rs = DB::table('order_transactions')->where('order_id', $row['order_id'])->first();

                if (!!$rs) {
                    array_push($ordersSkip, $row['order_id']);
                    continue;
                }

                $order = $this->createLabel($row['order_id'])['order'];

                // Gọi API Login
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://g7logistics.com/agentapi/login',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => '{
                    "email": "' . config('app.g7_email') . '",
                    "password": "' . config('app.g7_password') . '"
                }',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json'
                    ),
                ));
                $response = curl_exec($curl);
                Log::info('IMPORT LABELS: ' . json_encode($response));

                curl_close($curl);

                $isLogin = (json_decode($response))->succeeded ?? false;

                if ($isLogin === true) { // Nếu login thành công
                    $token = (json_decode($response))->data->token;
                    $data = $row;
                    //dd($data);
                    $data['receiver_company'] = $order['addressTo']['company'] ?? '';
                    $data['receiver_name'] = $order['addressTo']['name'] ?? '';
                    $data['receiver_street'] = $order['addressTo']['street1'] ?? '';
                    $data['receiver_address1'] = $order['addressTo']['street2'] ?? '';
                    $data['receiver_address2'] = $order['addressTo']['street3'] ?? '';
                    $data['receiver_city'] = $order['addressTo']['city'] ?? '';
                    $data['receiver_province'] = $order['addressTo']['state'] ?? '';
                    $data['receiver_country'] = $order['addressTo']['country'] ?? '';
                    $data['receiver_zip'] = $order['addressTo']['zip'] ?? '';
                    $data['receiver_phone'] = $order['addressTo']['phone'] ?? '';
                    $data['item_name'] = $order->item_name ?? ($order->orderProducts[0]->product->name ?? '');

                    if (!isset($data['package_height']) || trim($data['package_height']) === '') {
                        $data['package_height'] = $order->orderPackage->height;
                    }

                    if (!isset($data['package_length']) || trim($data['package_length']) === '') {
                        $data['package_length'] = $order->orderPackage->length;
                    }

                    if (!isset($data['package_width']) || trim($data['package_width']) === '') {
                        $data['package_width'] = $order->orderPackage->width;
                    }

                    if (!isset($data['package_weight']) || trim($data['package_weight']) === '') {
                        $data['package_weight'] = $order->orderPackage->weight;
                    }

                    if (!isset($data['size_type']) || trim($data['size_type']) === '') {
                        $data['size_type'] = $order->orderPackage->size_type;
                    }

                    if (!isset($data['weight_type']) || trim($data['weight_type']) === '') {
                        $data['weight_type'] = $order->orderPackage->weight_type;
                    }

                    $orderDate = gmdate('Y-m-d\TH:i:s.u\Z');

                    //Quy đổi kích thước sang cm và trọng lượng sang kg
                    if ($data['size_type'] == 1) { // inch to cm
                        $data['package_height_new'] = $data['package_height'] * 2.54;
                        $data['package_length_new'] = $data['package_length'] * 2.54;
                        $data['package_width_new'] = $data['package_width'] * 2.54;
                    } else {
                        $data['package_height_new'] = $data['package_height'] * 1;
                        $data['package_length_new'] = $data['package_length'] * 1;
                        $data['package_width_new'] = $data['package_width'] * 1;
                    }

                    if ($data['weight_type'] == 1) { // Lb to kg
                        $data['package_weight_new'] = $data['package_weight'] * 0.45359237;
                    } else { // Oz to kg
                        $data['package_weight_new'] = $data['package_weight'] * 0.0283495231;
                    }

                    $curl = curl_init();

                    $remarks = '';
                    if (Auth::user()->email !== null && Auth::user()->email === 'kinhdoanh1@wce.vn') {
                        $remarks = 'WCE';
                    }

                    $body_data = '{
                      "shipmentId": "",
                      "order": {
                        "no": "",
                        "orderDate": "' . $orderDate . '",
                        "sender_companyName": "' . trim($data['shipping_company'], "' \"") . '",
                        "sender_name": "' . trim($data['shipping_name'], "' \"") . '",
                        "sender_givename": "",
                        "sender_address1": "' . trim($data['shipping_street'], "' \"") . '",
                        "sender_address2": "' . trim($data['shipping_address1'], "' \"") . '",
                        "sender_address3": "' . trim($data['shipping_address2'], "' \"") . '",
                        "sender_city": "' . trim($data['shipping_city'], "' \"") . '",
                        "sender_district": "",
                        "sender_country": "' . trim($data['shipping_country'], "' \"") . '",
                        "sender_postCode": "' . trim($data['shipping_zip'], "' \"") . '",
                        "sender_phone": "' . trim($data['shipping_phone'], "' \"") . '",
                        "sender_email": "",
                        "sender_state": "' . trim($data['shipping_province'], "' \"") . '",
                        "consignee_companyName": "' . trim($data['receiver_company'], "' \"") . '",
                        "consignee_name": "' . trim($data['receiver_name'], "' \"") . '",
                        "consignee_givename": "",
                        "consignee_address1": "' . trim($data['receiver_street'], "' \"") . '",
                        "consignee_address2": "' . trim($data['receiver_address1'], "' \"") . '",
                        "consignee_address3": "' . trim($data['receiver_address2'], "' \"") . '",
                        "consignee_city": "' . trim($data['receiver_city'], "' \"") . '",
                        "consignee_state": "' . trim($data['receiver_province'], "' \"") . '",
                        "consignee_district": "",
                        "consignee_country": "' . trim($data['receiver_country'], "' \"") . '",
                        "consignee_postCode": "' . trim($data['receiver_zip'], "' \"") . '",
                        "consignee_phone": "' . trim($data['receiver_phone'], "' \"") . '",
                        "consignee_email": "",
                        "packageDesc": "",
                        "serviceId": 11,
                        "kindOfGood": 0,
                        "packages": [
                          {
                            "netWeight": ' . $data['package_weight_new'] . ',
                            "height": ' . $data['package_height_new'] . ',
                            "length": ' . $data['package_length_new'] . ',
                            "width": ' . $data['package_width_new'] . '
                          }
                        ],
                        "goods": [
                          {
                            "descriptionGood": "' . addslashes($data['item_name']) . '",
                            "value": ' . rand(30, 50) . ',
                            "curValId": "USD",
                            "countryoforigin": "VN",
                            "sku": "",
                            "itemQuantity": 1,
                            "hscode": "73269099",
                            "packNo": 0,
                            "netWeight": ' . $data['package_weight_new'] . '
                          }
                        ],
                        "deliveryDate": "' . $orderDate . '",
                        "deliveryTime": "",
                        "remarks": "' . $remarks . '",
                        "phoneContact": "",
                        "bookingCode": "",
                        "isValid": true,
                        "notImport": true,
                        "sMessage": "",
                        "orderNo": "",
                        "storeAddress": ""
                      }
                    }';

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://g7logistics.com/agentapi/add-edit-order',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => $body_data,
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type: application/json',
                            'Authorization: Bearer ' . $token
                        ),
                    ));

                    $response = curl_exec($curl);
                    Log::info('IMPORT LABELS: ' . json_encode($response));

                    curl_close($curl);

                    $response_status = json_decode($response)->succeeded ?? false;

                    // Nếu tạo mã thành công thì sẽ cập nhật dữ liệu
                    if ($response_status === true) {
                        $shipmentId = json_decode($response)->data->shipmentId;
                        // Gọi api đăng ký bill với G7
                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://g7logistics.com/agentapi/send-order',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => '["' . $shipmentId . '"]',
                            CURLOPT_HTTPHEADER => array(
                                'Content-Type: application/json',
                                'Authorization: Bearer ' . $token
                            ),
                        ));

                        $response = curl_exec($curl);
                        Log::info('IMPORT LABELS: ' . json_encode($response));

                        curl_close($curl);

                        // Chuẩn bị data để cập nhật db
                        $user_id = auth()->user()->id;
                        $order_id = $data['order_id'];
                        $shipping_name = $data['shipping_name'];
                        $shipping_street = $data['shipping_street'];
                        $shipping_address1 = $data['shipping_address1'] ?? '';
                        $shipping_address2 = $data['shipping_address2'];
                        $shipping_company = $data['shipping_company'];
                        $shipping_city = $data['shipping_city'];
                        $shipping_zip = $data['shipping_zip'];
                        $shipping_province = $data['shipping_province'];
                        $shipping_country = $data['shipping_country'];
                        $shipping_phone = $data['shipping_phone'];
                        $amount = 0;
                        $currency = 'VND';
                        $label_url = ''; //$file_order_path
                        $tracking_provider = $shipmentId;
                        $tracking_number = '';
                        $shipping_carrier = 'PNX';
                        $shipping_provider = 'G7';
                        $width = $data['package_width'];
                        $height = $data['package_height'];
                        $length = $data['package_length'];
                        $weight = $data['package_weight'];
                        $size_type = $data['size_type'];
                        $weight_type = $data['weight_type'];


                        // { CALL phoenix.label_create_input(:p_user_id,:p_order_id,:p_shipping_name,:p_shipping_street,:p_shipping_address1,:p_shipping_address2,:p_shipping_company,:p_shipping_city,:p_shipping_zip,:p_shipping_province,:p_shipping_country,:p_shipping_phone,:p_amount,:p_currency,:p_label_url,:p_tracking_number,:p_shipping_carrier,:p_shipping_provider) }
                        DB::select('call label_create_input(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', [
                            $user_id,
                            $order_id,
                            $shipping_name,
                            $shipping_street,
                            $shipping_address1,
                            $shipping_address2,
                            $shipping_company,
                            $shipping_city,
                            $shipping_zip,
                            $shipping_province,
                            $shipping_country,
                            $shipping_phone,
                            $amount,
                            $currency,
                            $label_url,
                            $tracking_provider,
                            $tracking_number,
                            $shipping_carrier,
                            $shipping_provider,
                            $width,
                            $height,
                            $length,
                            $weight,
                            $size_type,
                            $weight_type,
                            null
                        ]);
                    } else {
                        array_push($ordersError, $row['order_id']);
                    }
                } else {
                    Log::error('IMPORT LABELS: Login G7 failed.');
                    return [
                        'isValid' => false,
                        'message' => 'Login G7 failed.'
                    ];
                }
            }

            //DB::commit();
            if (count($ordersSkip) > 0) {
                Log::info('IMPORT LABELS: ORDERS SKIP ' . implode(', ', $ordersSkip));
            }

            return [
                'isValid' => true,
                'rawData' => $import->rows,
                'ordersError' => $ordersError,
            ];
        } catch (Exception $e) {
            //DB::rollback();
            Log::error($e);
            throw $e;
        }
    }


    public function storeExcelShippo($file, $request)
    {
        //DB::beginTransaction();
        try {
            $import = new StaffLabelsImport();
            $errors = [];

            Excel::import($import, $file);

            //$fileMove = $file->move('imgs' . DIRECTORY_SEPARATOR . Order::IMG_FOLDER, cleanName($file->getClientOriginalName()));

            if (count($import->errors)) {
                return [
                    'isValid' => false,
                    'errors' => $import->errors
                ];
            }

            $now = Carbon::now();
            $ordersSkip = [];

            foreach ($import->rows as $key => $row) {
                $rs = DB::table('order_transactions')->where('order_id', $row['order_id'])->first();

                if (!!$rs) {
                    array_push($ordersSkip, $row['order_id']);
                    continue;
                }

                $data = $row;
                $orderId = $row['order_id'];
                $orderInput = $this->createLabel($orderId)['order'];

                /*$data['receiver_company'] = $orderInput['addressTo']['company'] ?? '';
                $data['receiver_name'] = $orderInput['addressTo']['name'] ?? '';
                $data['receiver_street'] = $orderInput['addressTo']['street1'] ?? '';
                $data['receiver_address1'] = $orderInput['addressTo']['street2'] ?? '';
                $data['receiver_address2'] = $orderInput['addressTo']['street3'] ?? '';
                $data['receiver_city'] = $orderInput['addressTo']['city'] ?? '';
                $data['receiver_province'] = $orderInput['addressTo']['state'] ?? '';
                $data['receiver_country'] = $orderInput['addressTo']['country'] ?? '';
                $data['receiver_zip'] = $orderInput['addressTo']['zip'] ?? '';
                $data['receiver_phone'] = $orderInput['addressTo']['phone'] ?? '';
                $data['item_name'] = $orderInput->item_name ?? ($orderInput->orderProducts[0]->product->name ?? '');*/

                if (!isset($data['package_height']) || trim($data['package_height']) === '') {
                    $data['package_height'] = $orderInput->orderPackage->height;
                }

                if (!isset($data['package_length']) || trim($data['package_length']) === '') {
                    $data['package_length'] = $orderInput->orderPackage->length;
                }

                if (!isset($data['package_width']) || trim($data['package_width']) === '') {
                    $data['package_width'] = $orderInput->orderPackage->width;
                }

                if (!isset($data['package_weight']) || trim($data['package_weight']) === '') {
                    $data['package_weight'] = $orderInput->orderPackage->weight;
                }

                if (!isset($data['size_type']) || trim($data['size_type']) === '') {
                    $data['size_type'] = $orderInput->orderPackage->size_type;
                }

                if (!isset($data['weight_type']) || trim($data['weight_type']) === '') {
                    $data['weight_type'] = $orderInput->orderPackage->weight_type;
                }

                if (!$data['package_width'] || !$data['package_height'] ||
                    !$data['package_length'] || !$data['package_weight'] ||
                    !isset($data['size_type']) || !isset($data['weight_type'])) {
                    $errors[$key] = ['Please check again, size or weight is not found.'];
                    Log::error('IMPORT LABELS: Please check again, size or weight is not found.');
                    return [
                        'isValid' => false,
                        'errorsArr' => $errors
                    ];
                }

                $order = Order::findOrFail($orderId);

                OrderPackage::where('order_id', $orderId)->update([
                    'width' => $data['package_width'],
                    'height' => $data['package_height'],
                    'length' => $data['package_length'],
                    'weight' => $data['package_weight'],
                    'size_type' => $data['size_type'],
                    'weight_type' => $data['weight_type']
                ]);

                $street = $data['shipping_street'];
                if ($data['shipping_address1']) {
                    $street .= ',' . $data['shipping_address1'];
                }

                if ($data['shipping_address2']) {
                    $street .= ',' . $data['shipping_address2'];
                }

                $addressFrom = [
                    'name' => $data['shipping_name'],
                    'company' => $data['shipping_company'],
                    //'street1' => $data['shipping_street'],
                    'street1' => $street,
                    'street2' => $data['shipping_address1'],
                    'street3' => $data['shipping_address2'],
                    'city' => $data['shipping_city'],
                    'state' => $data['shipping_province'],
                    'zip' => $data['shipping_zip'],
                    'country' => $data['shipping_country'],
                    'phone' => $data['shipping_phone'],
                    //'email' => 'warehouse_test@gmail.com',
                ];

                $dataFrom = Order::validateAddress($addressFrom);
                if (count($dataFrom['errorMsg'])) {
                    $errors[$key] = $dataFrom['errorMsg'];
                    return [
                        'isValid' => false,
                        'errorsArr' => $errors
                    ];
                }

                $addressFrom['street1'] = $data['shipping_street'];
                $addressFrom['user_id'] = $order->user_id;
                $addressFrom['object_id'] = $dataFrom['value']['object_id'];
                $orderAddressFrom = OrderAddress::create($addressFrom);

                $order->order_address_from_id = $orderAddressFrom->id;
                $order->save();

                $parcel = $order->orderPackage->getParcelInfo();

                $shipment = Shippo_Shipment::create([
                    'address_from' => $order->addressFrom->object_id,
                    'address_to' => $order->addressTo->object_id,
                    'parcels' => [$parcel],
                    'async' => false,
                ]);

                if ($shipment['status'] != "SUCCESS") {
                    $errorMsg = array_map(function ($error) {
                        return $error->text;
                    }, $shipment['messages']);

                    $errors[$key] = $errorMsg;

                    return [
                        'isValid' => false,
                        'errorsArr' => $errors
                    ];
                }

                $orderRates = $this->setRates($shipment['rates'], $orderId);
                if (!count($orderRates)) {
                    $errors[$key] = ['Rates Unavailable'];
                    return [
                        'isValid' => false,
                        'errorsArr' => $errors
                    ];
                }

                OrderRate::insert($orderRates);

                $rateId = DB::table('order_rates')->where('order_id', $orderId)
                    ->where('attributes', 'LIKE', '%CHEAPEST%')->pluck('id')
                    ->first();


                $order2 = Order::with(['orderProducts.product'])->findOrFail($orderId);
                $orderPackage = OrderPackage::where('order_id', $order2->id)->first();

                $shippoOrder = null;

                $addressFrom = OrderAddress::where('id', $order2->order_address_from_id)->first();
                $addressTo = OrderAddress::where('id', $order2->order_address_to_id)->first();

                //Log::info("STAFF_STORE_RATE:: country:" . $addressTo->country);

                if ($addressTo->country != 'US' && $addressTo->country != 'United States') {

                    $warehouse = Warehouse::where('type', 'B')->first();

                    $items = [];

                    foreach ($order2->orderProducts as $productItem) {
                        $items[] =
                            array(
                                "total_amount" => $productItem->quantity,
                                "weight_unit" => OrderPackage::$weightName[$orderPackage->weight_type],
                                "title" => $productItem->product->name
                            );
                    }

                    $payload = array(
                        "total_tax" => "0.00",
                        "from_address" => array(
                            "object_purpose" => "PURCHASE",
                            'name' => $addressFrom->name ?? $warehouse['sender_name'],
                            'company' => $addressFrom->company ?? $warehouse['sender_company'],
                            'street1' => $addressFrom->address1 ?? $warehouse['sender_street'],
                            'city' => $addressFrom->city ?? $warehouse['sender_city'],
                            'state' => $addressFrom->state ?? $warehouse['sender_province'],
                            'zip' => $addressFrom->zip ?? $warehouse['sender_zip'],
                            'country' => $addressFrom->country ?? $warehouse['sender_country']
                        ),
                        "to_address" => array(
                            "object_purpose" => "PURCHASE",
                            "city" => $addressTo->city ?? $order2->shipping_city,
                            "state" => $addressTo->state ?? $order2->shipping_state,
                            "name" => $addressTo->name ?? $order2->shipping_name,
                            "zip" => $addressTo->zip ?? $order2->shipping_zip,
                            "country" => $addressTo->country ?? $order2->shipping_country,
                            "street2" => $addressTo->address2 ?? $order2->shipping_address2,
                            "street1" => $addressTo->address1 ?? $order2->shipping_address1,
                            "company" => $addressTo->company ?? $order2->shipping_company,
                            "phone" => $addressTo->phone ?? $order2->shipping_phone
                        ),
                        "shipping_method" => null,
                        // $orderPackage['weight_type'] = OrderPackage::$weightName[$orderPackage->weight_type];
                        // $orderPackage['size_type'] = OrderPackage::$sizeName[$orderPackage->size_type];
                        "weight" => $orderPackage->weight,
                        "shop_app" => "Shippo",
                        "currency" => "USD",
                        "shipping_cost_currency" => "USD",
                        "shipping_cost" => null,
                        "subtotal_price" => "0",
                        "total_price" => "0",
                        "items" => $items,
                        "order_status" => "PAID",
                        "hidden" => false,
                        "order_number" => $order2->order_number,
                        "weight_unit" => OrderPackage::$weightName[$orderPackage->weight_type],
                        "placed_at" => $order2->created_at
                    );

                    log::info("Payload: " . json_encode($payload));

                    $shippoOrder = Shippo_Order::create($payload);
                    Log::info("STAFF_STORE_RATE: shippoOrder:" . json_encode($shippoOrder));
                }


                $orderRate = OrderRate::where('order_id', $orderId)->findOrFail($rateId);
                $transaction = OrderTransaction::createTransaction($orderRate->object_id, $shippoOrder);

                if (count($transaction['errorMsg'])) {
                    $errors[$key] = $transaction['errorMsg'];

                    return [
                        'isValid' => false,
                        'errorsArr' => $errors
                    ];
                }

                OrderTransaction::updateOrCreate([
                    'order_id' => $orderId,
                ], [
                    //'order_id' => $orderId,
                    'order_rate_id' => $rateId,
                    'transaction_id' => $transaction['value']['object_id'],
                    'label_url' => $transaction['value']['label_url'],
                    'tracking_number' => $transaction['value']['tracking_number'],
                    'tracking_status' => $transaction['value']['tracking_status'],
                    'tracking_url_provider' => $transaction['value']['tracking_url_provider'],
                ]);
            }

            //DB::commit();
            if (count($ordersSkip) > 0) {
                Log::info('IMPORT LABELS: ORDERS SKIP ' . implode(', ', $ordersSkip));
            }

            return [
                'isValid' => true,
                'rawData' => $import->rows,
            ];
        } catch (Exception $e) {
            //DB::rollback();
            Log::error($e);
            throw $e;
        }
    }

    public function new($id)
    {
        $users = User::where('role', User::ROLE_USER)->find($id);

        if (empty($users) || !$users) {
            throw new Exception('Incorrect id!');
        }

        $products = Product::where('user_id', $id)->get();

        // Lấy bảng giá
        // Từ id lấy ra partner_code trong bảng users, sau đó từ partner_code lấy ra id_price_table trong bảng partners
        $id_price_table = null;
        $partner_code = DB::table('users')->where('id', $id)->value('partner_code');

        if (!!$partner_code) {
            $id_price_table = DB::table('partners')->where('partner_code', $partner_code)->value('id_price_table');
        }

        return [
            'user_id' => $id,
            'products' => $products,
            'id_price_table' => $id_price_table
        ];
    }

    public function saveOrderFileUrl($orderId, $fileKey)
    {
        $order = Order::where('id', $orderId)->first();
        if ($order->file_urls) {
            $order->file_urls .= ',' . $fileKey;
        } else {
            $order->file_urls = $fileKey;
        }

        $order->save();

    }


    public function create($request)
    {
        DB::beginTransaction();

        try {
            $now = Carbon::now();
            $street = $request['shipping_street'];
            if ($request['shipping_address1']) {
                $street .= ',' . $request['shipping_address1'];
            }

            if ($request['shipping_address2']) {
                $street .= ',' . $request['shipping_address2'];
            }

            $address = [
                'name' => $request['shipping_name'],
                'company' => $request['shipping_company'] ?? null,
                //'street1' => $request['shipping_street'],
                'street1' => $street,
                'street2' => $request['shipping_address1'] ?? null,
                'street3' => $request['shipping_address2'] ?? null,
                'city' => $request['shipping_city'],
                'state' => $request['shipping_province'],
                'zip' => $request['shipping_zip'],
                'country' => $request['shipping_country'],
                'phone' => $request['shipping_phone'] ?? null,
            ];

            $validateAddress = OrderAddress::validateAddress($address);
            if (count($validateAddress['errorMsg'])) {
                return [
                    'request' => $request,
                    'errorMsg' => $validateAddress['errorMsg']
                ];
            }

            $address['street1'] = $request['shipping_street'];
            $address['user_id'] = $request['user_id'];
            $address['object_id'] = $validateAddress['value']['object_id'];
            $orderAddressTo = OrderAddress::create($address);

            $newOrder = Order::create([
                // 'date' => $now,
                'order_address_to_id' => $orderAddressTo->id,
                'order_number' => $request['order_number'] ?? null,

                'payment' => Order::PAYMENT_UNPAY,
                'fulfillment' => Order::UNFULFILLED,
                'status' => Order::STATUS_NEW,
                'state' => Order::STATE_NONE,
                'user_id' => $request['user_id'],
                'id_price_table' => $request['id_price_table'],
            ]);

            $orderProducts = [];
            $orderPackage = [
                'order_id' => $newOrder->id,
            ];

            foreach ($request['product'] as $product) {
                $orderProducts[] = [
                    'order_id' => $newOrder->id,
                    'product_id' => $product['id'],
                    'quantity' => $product['unit_number'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($request['product']) == 1 && $product['unit_number'] == 1) {
                    $targetProduct = Product::with('packageGroupWithTrashed')->find($product['id']);
                    $orderPackage = [
                        'order_id' => $newOrder->id,
                        'width' => $request['package_width'] ?? $targetProduct->packageGroupWithTrashed->unit_width,
                        'height' => $request['package_height'] ?? $targetProduct->packageGroupWithTrashed->unit_height,
                        'length' => $request['package_length'] ?? $targetProduct->packageGroupWithTrashed->unit_length,
                        'weight' => $request['package_weight'] ?? $targetProduct->packageGroupWithTrashed->unit_weight,
                        'size_type' => OrderPackage::SIZE_IN,
                        'weight_type' => OrderPackage::WEIGHT_LB,
                    ];
                } else {
                    $orderPackage = [
                        'order_id' => $newOrder->id,
                        'width' => $request['package_width'],
                        'height' => $request['package_height'],
                        'length' => $request['package_length'],
                        'weight' => $request['package_weight'],
                        'size_type' => OrderPackage::SIZE_IN,
                        'weight_type' => OrderPackage::WEIGHT_LB,
                    ];
                }
            }

            OrderProduct::insert($orderProducts);
            OrderPackage::create($orderPackage);

            // sync order code if null
            DB::statement("UPDATE orders SET order_code = CONCAT('ODR', LPAD(id, 10, 0)) where order_code is null");

            DB::commit();

            return [
                'errorMsg' => [],
            ];
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function list($request)
    {
        $orders = Order::with(['orderProducts.product', 'orderPackage', 'orderTransaction', 'addressFrom', 'addressTo', 'orderRates'])
            ->has('user');

        if (isset($request['email'])) {
            $orders->whereHas('user', function ($query) use ($request) {
                $query->where('email', 'like', '%' . $request['email'] . '%');
            });
        }
        if (isset($request['id'])) {
            $orders = $orders->where('id', $request['id']);
        }

        if (isset($request['status'])) {
            $orders = $orders->where('status', $request['status']);
        }
        if (isset($request['payment'])) {
            $orders = $orders->where('payment', $request['payment']);
        }
        if (isset($request['state'])) {
            $orders = $orders->where('state', $request['state']);
        }
        if (isset($request['picking_status'])) {
            $orders = $orders->where('picking_status', $request['picking_status']);
        }
        if (isset($request['bill_status']) && $request['bill_status'] != config('app.tracking_status_all')) { // Tận dụng trường picking_status để lưu bill_status
            $orders = $orders->where('picking_status', $request['bill_status']);
        }
        if (isset($request['fulfillment'])) {
            $orders = $orders->where('fulfillment', $request['fulfillment']);
        }


        if (isset($request['order_number'])) {
            $orders = $orders->where('order_number', $request['order_number']);
        }
        if (isset($request['order_code'])) {
            $orders = $orders->where('order_code', $request['order_code']);
        }


        if (isset($request['date_from'])) {
            $orders = $orders->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($request['date_from'])));
        }

        if (isset($request['date_to'])) {
            $orders = $orders->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($request['date_to'])));
        }

        $orders = $orders->orderByDesc('updated_at')
            ->paginate(100);

        $emails = User::where('role', User::ROLE_USER)->pluck('email')->toArray();
        $users = User::where('role', User::ROLE_USER)->get();

        return [
            'orders' => $orders,
            'oldInput' => $request,
            'emails' => $emails,
            'users' => $users
        ];
    }

    public function listByDate($dateFrom, $dateTo)
    {
        $orders = DB::select('call exportOrder(?, ?)', [$dateFrom, $dateTo]);
        return collect($orders);
    }

    //
    public function detail($id)
    {
        $order = Order::with([
            'orderProducts.product.category', 'orderPackage', 'user',
            'addressFrom', 'addressTo', 'orderTransaction.orderRate'
        ])->has('user')
            ->findOrFail($id);

        return [
            'order' => $order
        ];
    }

    public function updateStatus($request)
    {
        try {
            DB::beginTransaction();

            if (!isset($request['id'])) {
                throw new Exception('Missing order id');
            }

            if (!isset($request['status'])) {
                throw new Exception('Missing status');
            }

            $order = Order::find($request['id']);

            if ($request['status'] == Order::STATUS_NEW) {
                $order->status = Order::STATUS_INPROGRESS;
                $order->state = Order::STATE_ON_DOING;
            } else if ($request['status'] == Order::STATUS_CANCEL) {
                $order->status = Order::STATUS_CANCEL;
            } else {
                $order->fulfillment = Order::FULFILLED;
                $order->payment = Order::PAYMENT_PAY;
                $order->status = Order::STATUS_DONE;
                $order->state = Order::STATE_DONE;

                foreach ($order->orderProducts as $orderProduct) {
                    if (isset($orderProduct->product->inventory)) {
                        $inventory = $orderProduct->product->inventory;
                        $inventory->available = $inventory->available > $orderProduct->quantity ? $inventory->available - $orderProduct->quantity : 0;
                        $inventory->save();
                    }
                }
            }

            $order->save();
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function updateOrder($request)
    {
        try {
            DB::beginTransaction();

            $order = Order::find($request['id']);

            if ($order->fulfillment != Order::FULFILLED && $request['fulfill_name'] == Order::FULFILLED) {
                foreach ($order->orderProducts as $orderProduct) {
                    if (isset($orderProduct->product->inventory)) {
                        $inventory = $orderProduct->product->inventory;
                        $inventory->available = $inventory->available > $orderProduct->quantity ? $inventory->available - $orderProduct->quantity : 0;
                        $inventory->save();
                    }
                }
            }

            $order->payment = $request['payment'];
            $order->fulfillment = $request['fulfill_name'];
            $order->picking_status = $request['picking_status'];
            $order->state = $request['state'];
            if (isset($request['state_note'])) {
                $order->state_note = $request['state_note'];
            }
            $order->save();

            if ($order->orderTransaction) {
                if (isset($request['tracking_number'])) {
                    $order->orderTransaction->tracking_number = $request['tracking_number'];
                }

                if (isset($request['tracking_url_provider'])) {
                    $order->orderTransaction->tracking_url_provider = $request['tracking_url_provider'];
                }

                $order->orderTransaction->save();
            }

            if (isset($order->orderTransaction->orderRate)) {
                if (isset($request['ship_rate'])) {
                    $order->orderTransaction->orderRate->amount = round($request['ship_rate'], 2);
                    // $order->orderTransaction->orderRate->amount_local = $request['ship_rate'];

                    $order->orderTransaction->orderRate->save();
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function updatePackage($request)
    {
        $orderPackage = OrderPackage::where('order_id', $request['id'])->first();

        if (isset($request['length'])) {
            $orderPackage->length = $request['length'];
        }

        if (isset($request['width'])) {
            $orderPackage->width = $request['width'];
        }

        if (isset($request['height'])) {
            $orderPackage->height = $request['height'];
        }

        if (isset($request['weight'])) {
            $orderPackage->weight = $request['weight'];
        }

        $orderPackage->save();
    }

    // TODO
    public function createLabel($orderId)
    {
        $order = Order::doesntHave('orderTransaction')
            ->has('addressTo')
            ->with(['orderPackage', 'addressTo', 'orderProducts.product.category'])
            ->findOrFail($orderId);

        return [
            'order' => $order,
        ];
    }

    public function storeLabel($request, $orderId)
    {
        // Mua labelxxx 2
        DB::beginTransaction();

        try {
            // $order = Order::doesntHave('orderTransaction')->findOrFail($orderId);
            $order = Order::findOrFail($orderId);

            OrderPackage::where('order_id', $orderId)->update([
                'width' => $request['package_width'],
                'height' => $request['package_height'],
                'length' => $request['package_length'],
                'weight' => $request['package_weight'],
                'size_type' => $request['size_type'],
                'weight_type' => $request['weight_type']
            ]);

            $street = $request['shipping_street'];
            if ($request['shipping_address1']) {
                $street .= ',' . $request['shipping_address1'];
            }

            if ($request['shipping_address2']) {
                $street .= ',' . $request['shipping_address2'];
            }

            $addressFrom = [
                'name' => $request['shipping_name'],
                'company' => $request['shipping_company'],
                //'street1' => $request['shipping_street'],
                'street1' => $street,
                'street2' => $request['shipping_address1'],
                'street3' => $request['shipping_address2'],
                'city' => $request['shipping_city'],
                'state' => $request['shipping_province'],
                'zip' => $request['shipping_zip'],
                'country' => $request['shipping_country'],
                'phone' => $request['shipping_phone'],
                // 'email' => 'warehouse_test@gmail.com',
            ];

            $dataFrom = Order::validateAddress($addressFrom);
            if (count($dataFrom['errorMsg'])) {
                return [
                    'request' => $request,
                    'errorMsg' => $dataFrom['errorMsg']
                ];
            }

            $addressFrom['street1'] = $request['shipping_street'];
            $addressFrom['user_id'] = $order->user_id;
            $addressFrom['object_id'] = $dataFrom['value']['object_id'];
            $orderAddressFrom = OrderAddress::create($addressFrom);

            $order->order_address_from_id = $orderAddressFrom->id;
            $order->save();

            // $addressFrom = $order->addressFrom->getShippingInfo();
            // $addressTo = $order->addressTo->getShippingInfo();
            $parcel = $order->orderPackage->getParcelInfo();

            $shipment = Shippo_Shipment::create([
                'address_from' => $order->addressFrom->object_id,
                'address_to' => $order->addressTo->object_id,
                'parcels' => [$parcel],
                'async' => false,
            ]);

            if ($shipment['status'] != "SUCCESS") {
                $errorMsg = array_map(function ($error) {
                    return $error->text;
                }, $shipment['messages']);

                return [
                    'request' => $request,
                    'errorMsg' => $errorMsg
                ];
            }

            $orderRates = $this->setRates($shipment['rates'], $orderId);
            if (!count($orderRates)) {
                return [
                    'request' => $request,
                    'errorMsg' => [
                        'Rates Unavailable'
                    ]
                ];
            }

            OrderRate::insert($orderRates);
            DB::commit();

            return [
                'request' => $request,
                'rates' => $orderRates,
                'errorMsg' => []
            ];
        } catch (Exception $e) {
            DB::rollback();

            if (isset($e->jsonBody)) {
                return [
                    'request' => $request,
                    'errorMsg' => [$e->getMessage()]
                ];
            }

            throw $e;
        }
    }

    public function getOrderPackageApi($request)
    {
        $orderId = $request['order_id'];
        $order = Order::where('order_code', $orderId)->first();
        if (!isset($order)) {
            return [
                'message_code' => 'NOT_FOUND',
                'message_text' => [
                    'Not found'
                ]
            ];
        }
        $orderPackage = OrderPackage::where('order_id', $order->id)->first();
        if (!$orderPackage) {
            return [
                'message_code' => 'NOT_FOUND',
                'message_text' => [
                    'Not found'
                ]
            ];
        }

        $orderTransaction = OrderTransaction::with(['orderRate'])->where('order_id', $order->id)->first();
        if ($orderTransaction) {
            $orderPackage['label_print_url'] = $orderTransaction->label_url;
            $orderPackage['tracking_code'] = $orderTransaction->tracking_number;
            $orderPackage['brand_delivery'] = $orderTransaction->orderRate->provider;
        }
        $orderPackage['size_type'] = OrderPackage::$sizeName[$orderPackage->size_type];
        $orderPackage['weight_type'] = OrderPackage::$weightName[$orderPackage->weight_type];
        // $orderPackage['weight'] = number_format((float)$orderPackage->weight,1, '.', '');

        return [
            'message_code' => 'SUCCESS',
            'package' => $orderPackage
        ];
    }

    function kgToLb($val)
    {
        $weightLb = number_format($val * 2.20462, 3);
        return $weightLb;
    }

    public function createLabelPdaApi($request)
    {

        DB::beginTransaction();

        try {

            // type B: warehouse destination
            $warehouse = Warehouse::where('type', 'B')->first();
            $orderId = $request['order_id'];
            // $order = Order::doesntHave('orderTransaction')->findOrFail($orderId);
            $order = Order::findOrFail($orderId);

            // check transaction, return existing one

            $orderTransactionCheck = OrderTransaction::with(['orderRate'])->where('order_id', $orderId)->first();

            if ($orderTransactionCheck) {

                Log::info("NEW PRINT -- " . $orderTransactionCheck->tracking_number . " ORDER:" . $orderId);

                return [
                    'message_code' => 'SUCCESS',
                    'label_print_url' => $orderTransactionCheck->label_url,
                    'tracking_code' => $orderTransactionCheck->tracking_number,
                    'brand_delivery' => $orderTransactionCheck->orderRate->provider,
                    'errorMsg' => []
                ];
            }


            $lbs = $this->kgToLb($request['package_weight']);

            $orderPackage = OrderPackage::where('order_id', $orderId)->update([
                'width' => $request['package_width'],
                'height' => $request['package_height'],
                'length' => $request['package_length'],
                // 'weight' => $request['package_weight'],
                'weight' => $lbs,
                'weight' => $lbs,
                'size_type' => OrderPackage::SIZE_CM, // $request['size_type'] ??
                'weight_type' => OrderPackage::WEIGHT_LB // $request['weight_type'] ??
            ]);

            $addressFrom = [
                'name' => $warehouse['sender_name'],
                'company' => $warehouse['sender_company'],
                'street1' => $warehouse['sender_street'],
                'city' => $warehouse['sender_city'],
                'state' => $warehouse['sender_province'],
                'zip' => $warehouse['sender_zip'],
                'country' => $warehouse['sender_country']
            ];

            $dataFrom = Order::validateAddress($addressFrom);
            if (count($dataFrom['errorMsg'])) {
                return [
                    'message_code' => 'ADDRESS_CREATE_FAILED',
                    'errorMsg' => $dataFrom['errorMsg']
                ];
            }

            $addressFrom['user_id'] = $order->user_id;
            $addressFrom['object_id'] = $dataFrom['value']['object_id'];
            $orderAddressFrom = OrderAddress::create($addressFrom);

            $order->order_address_from_id = $orderAddressFrom->id;
            $order->save();

            // $addressFrom = $order->addressFrom->getShippingInfo();
            $addressTo = $order->addressTo->getShippingInfo();
            $parcel = $order->orderPackage->getParcelInfo();

            $shipment = Shippo_Shipment::create([
                'address_from' => $order->addressFrom->object_id,
                'address_to' => $order->addressTo->object_id,
                'parcels' => [$parcel],
                'async' => false,
            ]);

            if ($shipment['status'] != "SUCCESS") {


                Log::info('-- SHIPPO create new');

                $errorMsg = array_map(function ($error) {
                    return $error->text;
                }, $shipment['messages']);

                return [
                    'message_code' => 'CREATE_SM_FAILED',
                    'errorMsg' => $errorMsg
                ];
            }

            $orderRates = $this->setRates($shipment['rates'], $orderId);
            if (!count($orderRates)) {
                return [
                    'message_code' => 'NO_RATE',
                    'errorMsg' => [
                        'Rates Unavailable'
                    ]
                ];
            }

            OrderRate::insert($orderRates);
            $rate = null;
            // weight < 1 lbs => BEST VALUE
            if ($lbs < 1) {
                $rate = OrderRate::where('attributes', 'like', '%CHEAPEST%')
                    ->where('order_id', $orderId)->first();
                // auto select rate (best rate with CHEAPEST)
            } else {
                $rateBestCheap = OrderRate::where('attributes', 'like', '%BESTVALUE", "CHEAPEST"%')
                    ->where('order_id', $orderId)->first();
                if (isset($rateBestCheap)) {
                    $rate = $rateBestCheap;
                } else {
                    // select 2 cheapest
                    $rate = OrderRate::where('order_id', $orderId)
                        ->limit(1)->offset(1)->first();
                }
            }

            $this->storeRate($rate->id, $orderId);
            DB::commit();

            $orderTransaction = OrderTransaction::with(['orderRate'])->where('order_id', $orderId)->first();

            return [
                'message_code' => 'SUCCESS',
                'label_print_url' => $orderTransaction->label_url,
                'tracking_code' => $orderTransaction->tracking_number,
                'brand_delivery' => $orderTransaction->orderRate->provider,
                'errorMsg' => []
            ];


        } catch (Exception $e) {
            Log::info("OrderCreateLabelApi:Exeption " . $e->getMessage());
            DB::rollback();

            if (isset($e->jsonBody)) {
                Log::error($e);
                return [
                    'message_code' => 'CREATE_LABEL_FAILED',
                    'detail' => $e->jsonBody
                ];
            }

            return [
                'message_code' => 'CREATE_LABEL_FAILED',
                'message_text' => 'Order missing address or address invalid. Please take a look at order adress'
            ];

            // throw $e;
        }
    }

    public function getRates($orderId)
    {
        $orderRates = OrderRate::where('order_id', $orderId)->get();
        return $orderRates;
    }

    public function storeRate($rateId, $orderId)
    {
        try {
            // Mua labelxxx 4
            DB::beginTransaction();

            $order = Order::with(['orderProducts.product'])->findOrFail($orderId);
            $orderPackage = OrderPackage::where('order_id', $order->id)->first();

            $shippoOrder = null;

            $addressFrom = OrderAddress::where('id', $order->order_address_from_id)->first();
            $addressTo = OrderAddress::where('id', $order->order_address_to_id)->first();

            Log::info("STAFF_STORE_RATE:: country:" . $addressTo->country);

            if ($addressTo->country != 'US' && $addressTo->country != 'United States') {

                $warehouse = Warehouse::where('type', 'B')->first();

                $items = [];

                foreach ($order->orderProducts as $productItem) {
                    $items[] =
                        array(
                            "total_amount" => $productItem->quantity,
                            "weight_unit" => OrderPackage::$weightName[$orderPackage->weight_type],
                            "title" => $productItem->product->name
                        );
                }

                $payload = array(
                    "total_tax" => "0.00",
                    "from_address" => array(
                        "object_purpose" => "PURCHASE",
                        'name' => $addressFrom->name ?? $warehouse['sender_name'],
                        'company' => $addressFrom->company ?? $warehouse['sender_company'],
                        'street1' => $addressFrom->address1 ?? $warehouse['sender_street'],
                        'city' => $addressFrom->city ?? $warehouse['sender_city'],
                        'state' => $addressFrom->state ?? $warehouse['sender_province'],
                        'zip' => $addressFrom->zip ?? $warehouse['sender_zip'],
                        'country' => $addressFrom->country ?? $warehouse['sender_country']
                    ),
                    "to_address" => array(
                        "object_purpose" => "PURCHASE",
                        "city" => $addressTo->city ?? $order->shipping_city,
                        "state" => $addressTo->state ?? $order->shipping_state,
                        "name" => $addressTo->name ?? $order->shipping_name,
                        "zip" => $addressTo->zip ?? $order->shipping_zip,
                        "country" => $addressTo->country ?? $order->shipping_country,
                        "street2" => $addressTo->address2 ?? $order->shipping_address2,
                        "street1" => $addressTo->address1 ?? $order->shipping_address1,
                        "company" => $addressTo->company ?? $order->shipping_company,
                        "phone" => $addressTo->phone ?? $order->shipping_phone
                    ),
                    "shipping_method" => null,
                    // $orderPackage['weight_type'] = OrderPackage::$weightName[$orderPackage->weight_type];
                    // $orderPackage['size_type'] = OrderPackage::$sizeName[$orderPackage->size_type];
                    "weight" => $orderPackage->weight,
                    "shop_app" => "Shippo",
                    "currency" => "USD",
                    "shipping_cost_currency" => "USD",
                    "shipping_cost" => null,
                    "subtotal_price" => "0",
                    "total_price" => "0",
                    "items" => $items,
                    "order_status" => "PAID",
                    "hidden" => false,
                    "order_number" => $order->order_number,
                    "weight_unit" => OrderPackage::$weightName[$orderPackage->weight_type],
                    "placed_at" => $order->created_at
                );

                log::info("Payload:: " . json_encode($payload));

                $shippoOrder = Shippo_Order::create($payload);
                Log::info("STAFF_STORE_RATE:: shippoOrder:" . json_encode($shippoOrder));
            }


            $orderRate = OrderRate::where('order_id', $orderId)->findOrFail($rateId);
            $transaction = OrderTransaction::createTransaction($orderRate->object_id, $shippoOrder);
            Log::info('Admin create label, Tracking number: ' . ($transaction['value']['tracking_number'] ?? 'NULL'));

            if (count($transaction['errorMsg'])) {
                return [
                    'errorMsg' => $transaction['errorMsg'],
                ];
            }

            OrderTransaction::updateOrCreate([
                'order_id' => $orderId,
            ], [
                // 'order_id' => $orderId,
                'order_rate_id' => $rateId,
                'transaction_id' => $transaction['value']['object_id'],
                'label_url' => $transaction['value']['label_url'],
                'tracking_number' => $transaction['value']['tracking_number'],
                'tracking_status' => $transaction['value']['tracking_status'],
                'tracking_url_provider' => $transaction['value']['tracking_url_provider'],
            ]);

            DB::commit();

            // Check nếu người tạo order có webhook thì xử lý dữ liệu sau đó gửi vào webhook
            $webhook_url = DB::table('users as u')
                ->where('u.id', $order->user_id)
                ->where('u.deleted_at', null)
                ->pluck('webhook_url')->first();

            if ($webhook_url) {
                $addFrom = DB::table('order_addresses')->where('id', $order->order_address_from_id)->first();
                $addTo = DB::table('order_addresses')->where('id', $order->order_address_to_id)->first();
                $dataSendApi = (object) [
                    'event' => 'transaction_created',
                    'status' => $transaction['value']['status'],
                    'carrier' => 'shippo',
                    'customers_order' => $order->order_number,
                    'data' => [
                        'tracking_history' => [],
                        'tracking_status' => [
                            'status' => 'Unknown'
                        ],
                        'carrier' => '',
                        'tracking_number' => $transaction['value']['tracking_number'],
                        'address_from' => [
                            'country' => $addFrom->country ?? '',
                            'zip' => $addFrom->zip ?? '',
                            'state' => $addFrom->state ?? '',
                            'city' => $addFrom->city ?? '',
                        ],
                        'address_to' => [
                            'country' => $addTo->country ?? '',
                            'zip' => $addTo->zip ?? '',
                            'state' => $addTo->state ?? '',
                            'city' => $addTo->city ?? '',
                        ]
                    ],
                    'date_created' => $transaction['value']['object_created']
                ];


                try {
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $webhook_url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => json_encode($dataSendApi),
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type: application/json'
                        ),
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);
                    echo $response;

                } catch (Exception $e) {
                    Log::error($e->getMessage());
                    throw $e;
                }
            }

            return [
                'errorMsg' => []
            ];
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function getBestValueRateId($shipmentRates)
    {

    }

    public function setRates($shipmentRates, $orderId)
    {
        $orderRates = [];
        $now = Carbon::now();

        foreach ($shipmentRates as $rate) {
            $orderRates[] = [
                'order_id' => $orderId,
                'is_active' => false,
                'object_id' => $rate['object_id'],
                'object_owner' => $rate['object_owner'],
                'shipment' => $rate['shipment'],
                'attributes' => json_encode($rate['attributes']),
                'amount' => round($rate['amount'] * OrderRate::RATES, 2),
                'currency' => $rate['currency'],
                'amount_local' => round($rate['amount_local'] * OrderRate::RATES, 2),
                'currency_local' => $rate['currency_local'],
                'provider' => $rate['provider'],
                'provider_image_75' => $rate['provider_image_75'],
                'provider_image_200' => $rate['provider_image_200'],
                'service_name' => $rate['servicelevel']['name'],
                'messages' => json_encode($rate['messages']),
                'estimated_days' => $rate['estimated_days'],
                'duration_terms' => $rate['duration_terms'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        usort($orderRates, function ($first, $second) {
            return (float)$first['amount'] > (float)$second['amount'];
        });

        return $orderRates;
    }

    public function orderPrintMultiple($orderCodeIds)
    {

        $files = [];
        $outFile = public_path() . '/tmp/' . 'C_' . Carbon::now()->timestamp . Str::random(8) . '.pdf';

        Log::info("::count::" . json_encode($orderCodeIds));
        $orderTransactions = OrderTransaction::whereIn('order_id', $orderCodeIds)->get();

        Log::info("::count::" . count($orderTransactions));

        if (count($orderTransactions) == 0) {
            Log::info("::count::" . count($orderTransactions));
            return;
        }

        Log::info("::orderTransactions::" . json_encode($orderTransactions));
        $pdf = PDFMerger::init();

        foreach ($orderTransactions as $orderTransaction) {
            try {
                $filePath = public_path() . '/tmp/' . Carbon::now()->timestamp . Str::random(8) . '.pdf';
                $fileTracking = $orderTransaction['label_url'];
                $file = file_get_contents($fileTracking);
                if (!$file) continue;
                file_put_contents($filePath, $file);
                $pdf->addPDF($filePath, 'all');
                $files[] = $filePath;
            } catch (Exception $e) {
                Log::info($e->getMessage());
            }
        }
    }

}
