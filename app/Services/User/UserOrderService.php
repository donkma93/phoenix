<?php

namespace App\Services\User;

use App\Imports\User\UserOrdersImport;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderPackage;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\OrderTransaction;
use App\Models\User;
use App\Services\UserBaseServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;
use Exception;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Illuminate\Support\Facades\Log;

class UserOrderService extends UserBaseService implements UserBaseServiceInterface
{

    public function saveOrderFileUrl($orderId, $fileKey)
    {
        $order = Order::where('id', $orderId)->first();
        if ($order->file_urls) {
            $order->file_urls  .= ',' . $fileKey;
        } else {
            $order->file_urls = $fileKey;
        }

        $order->save();
    }

    public function storeCsv($file)
    {

        DB::beginTransaction();
        try {
            $import = new UserOrdersImport();

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
            $user = User::where('role', User::ROLE_USER)->find(Auth::id());
            foreach ($import->rows as $key => $row) {
                $orderId = 0;
                $orderAddressTo = OrderAddress::create($import->addresses[$key]);
                
                if (!isset($row['order_number']) || !in_array($row['order_number'], $orderNumbers)) {
                    
                    $orderNumber = isset($row['order_number']) ? trim($row['order_number']) : null;
                    $newOrder = Order::create([
                        'date' => $row['created_at'] ?? null,  // TODO: need convert Timezone when save (?)
                        'order_address_to_id' => $orderAddressTo->id,
                        'order_number' => $orderNumber,

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
                        'user_id' => Auth::id(),
                        'partner_code' => $user->partner_code,
                        'partner_id' => $user->partner_id,


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
                    'weight' => $hasPackageInfo ? $targetProduct->packageGroupWithTrashed->unit_weight  ?? null : null,
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

            // sync order code if null
            DB::statement("UPDATE orders SET order_code = CONCAT('ODR', LPAD(id, 10, 0)) where order_code is null");

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


    public function exportSKU($p_id)
    {
        $orders = DB::select('call exportSKU(?)', [$p_id]);
        return  collect($orders);
    }

    public function listByDate($dateFrom, $dateTo)
    {
        $userId = Auth::id();
        $orders = DB::select('call customer_order_list(?,?,?)', [$dateFrom, $dateTo, $userId]);
        return  collect($orders);
    }

    public function saveTrackingInfo($orderId, $fileKey, $trackingCode)
    {
        OrderTransaction::updateOrCreate([
            'order_id' => $orderId,
        ], [
            // 'order_id' => $orderId,
            'order_rate_id' => null,
            'transaction_id' => null,
            'label_url' => $fileKey,
            'tracking_number' => $trackingCode,
            'tracking_status' => 'TRANSIT',
            'tracking_url_provider' => $fileKey,
        ]);
    }

    public function create()
    {
        $products = Product::has('inventory')
            ->where('user_id', Auth::id())
            ->orderBy('name')
            ->get();

        // Lấy bảng giá
        // Từ id lấy ra partner_code trong bảng users, sau đó từ partner_code lấy ra id_price_table trong bảng partners
        $id_price_table = null;
        $partner_code = Auth::user()->partner_code;

        if (!!$partner_code) {
            $id_price_table = DB::table('partners')->where('partner_code', $partner_code)->value('id_price_table');
        }

        return [
            'products' => $products,
            'id_price_table' => $id_price_table
        ];
    }

    public function store($request)
    {
        DB::beginTransaction();
        Log::info("User create order");
        try {
            $now = Carbon::now();
            $user = User::where('role', User::ROLE_USER)->find(Auth::id());
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
            $address['user_id'] = Auth::id();
            $address['object_id'] = $validateAddress['value']['object_id'];
            $orderAddressTo = OrderAddress::create($address);

            $newOrder = Order::create([
                // 'date' => $now,
                'order_address_to_id' => $orderAddressTo->id,
                'order_number' => $request['order_number'] ?? null,

                'payment' => Order::PAYMENT_UNPAY,
                'fulfillment' => Order::UNFULFILLED,
                'status' => Order::STATUS_NEW,
                'user_id' => Auth::id(),
                'partner_id' => $user->partner_id,
                'partner_code' => $user->partner_code,
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
                        'height' => $request['package_height'] ??  $targetProduct->packageGroupWithTrashed->unit_height,
                        'length' =>  $request['package_length'] ?? $targetProduct->packageGroupWithTrashed->unit_length,
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

            $orderCode = DB::table('orders')->where('id', $newOrder->id)->pluck('order_code')->first();
            return [
                'errorMsg' => [],
                'orderCode' => $orderCode,
            ];
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function index($request)
    {
        $orders = Order::with([
            'orderProducts.product', 'orderPackage', 'orderTransaction',
            'addressFrom', 'addressTo', 'orderRates'
        ])->where('user_id', Auth::id())->where('state', '<>', Order::STATE_ON_HOLD_VIP);

        if (isset($request['status'])) {
            $orders = $orders->where('status', $request['status']);
        }

        if (isset($request['payment'])) {
            $orders = $orders->where('payment', $request['payment']);
        }

        if (isset($request['fulfillment'])) {
            $orders = $orders->where('fulfillment', $request['fulfillment']);
        }

        $orders = $orders->orderByDesc('updated_at')
            ->paginate();

        return [
            'orders' => $orders,
            'oldInput' => $request,
        ];
    }

    public function show($id)
    {
        $order = Order::with([
            'orderProducts.product.category', 'orderPackage', 'orderTransaction',
            'addressFrom', 'addressTo', 'orderTransaction.orderRate'
        ])
            ->where('user_id', Auth::id())
            ->where('state', '<>', Order::STATE_ON_HOLD_VIP)
            ->findOrFail($id);

        return [
            'order' => $order
        ];
    }
}
