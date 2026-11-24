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

            // Read from uploaded temp file only — do NOT archive import xlsx/csv under public/imgs/orders
            Excel::import($import, $file);

            if (count($import->errors)) {
                return [
                    'isValid' => false,
                    'errors' => $import->errors
                ];
            }

            $orderMap = [];
            $createdOrderIds = [];
            $orderProducts = $orderPackages = [];
            $products = $import->products;

            $now = Carbon::now();
            $userId = Auth::id();
            $user = User::select('id', 'partner_code', 'partner_id')
                ->where('role', User::ROLE_USER)
                ->find($userId);

            foreach ($import->rows as $key => $row) {
                $orderNumber = isset($row['order_number']) && trim((string) $row['order_number']) !== ''
                    ? trim((string) $row['order_number'])
                    : null;

                if ($orderNumber === null || !isset($orderMap[$orderNumber])) {
                    // Only create OrderAddress when creating a new Order (prevents orphan duplicate addresses)
                    $orderAddressTo = OrderAddress::create($import->addresses[$key]);

                    $newOrder = Order::create([
                        'date' => $row['created_at'] ?? null,
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
                        'user_id' => $userId,
                        'partner_code' => $user ? $user->partner_code : null,
                        'partner_id' => $user ? $user->partner_id : null,

                        // Do not persist full Excel row JSON (was bloating DB) or source file path
                        'content' => null,
                        'file' => null,
                    ]);

                    $orderId = $newOrder->id;
                    $createdOrderIds[] = $orderId;

                    if ($orderNumber !== null) {
                        $orderMap[$orderNumber] = $orderId;
                    }
                } else {
                    $orderId = $orderMap[$orderNumber];
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
                foreach (array_chunk($orderProducts, 500) as $chunk) {
                    OrderProduct::insert($chunk);
                }
            }

            if (count($orderPackages)) {
                foreach (array_chunk($orderPackages, 500) as $chunk) {
                    OrderPackage::insert($chunk);
                }
            }

            // Targeted PK update for newly created orders only (avoids scanning entire orders table)
            if (count($createdOrderIds)) {
                foreach (array_chunk($createdOrderIds, 500) as $idChunk) {
                    DB::table('orders')
                        ->whereIn('id', $idChunk)
                        ->update(['order_code' => DB::raw("CONCAT('ODR', LPAD(id, 10, 0))")]);
                }
            }

            DB::commit();

            // Free in-memory import payload ASAP (can be large for multi-thousand-row sheets)
            $import->rows = [];
            $import->addresses = [];
            $import->products = [];

            return [
                'isValid' => true,
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

    /**
     * Lightweight existence check for /user/orders empty-state UI.
     */
    public function hasUserOrdersInRange($userId, $dateFrom, $dateTo): bool
    {
        $from = date('Y-m-d 00:00:00', strtotime($dateFrom));
        $toExclusive = date('Y-m-d 00:00:00', strtotime($dateTo . ' +1 day'));

        return DB::table('orders')
            ->where('user_id', $userId)
            ->whereNull('deleted_at')
            ->where('created_at', '>=', $from)
            ->where('created_at', '<', $toExclusive)
            ->limit(1)
            ->exists();
    }

    /**
     * Server-side DataTables for /user/orders (matches customer_order_list columns 100%,
     * uses fast 2-step indexed pagination so customers with 39k+ orders never freeze the browser).
     */
    public function listForUserDataTable($userId, $dateFrom, $dateTo, $searchValue, $start, $length): array
    {
        $from = date('Y-m-d 00:00:00', strtotime($dateFrom));
        $toExclusive = date('Y-m-d 00:00:00', strtotime($dateTo . ' +1 day'));
        $searchValue = trim((string) $searchValue);
        $hasSearch = $searchValue !== '';

        $thinBase = DB::table('orders as odr')
            ->where('odr.user_id', $userId)
            ->whereNull('odr.deleted_at')
            ->where('odr.created_at', '>=', $from)
            ->where('odr.created_at', '<', $toExclusive);

        $recordsTotal = (int) (clone $thinBase)->count();

        if (!$hasSearch) {
            $recordsFiltered = $recordsTotal;
            $orderIds = (clone $thinBase)
                ->select('odr.id')
                ->orderByDesc('odr.created_at')
                ->orderByDesc('odr.id')
                ->offset(max(0, (int) $start))
                ->limit(max(1, (int) $length))
                ->pluck('odr.id')
                ->all();
        } else {
            $filtered = (clone $thinBase)
                ->join('order_package as pkg', 'odr.id', '=', 'pkg.order_id')
                ->join('order_addresses as addr', 'addr.id', '=', 'odr.order_address_to_id')
                ->leftJoin('order_transactions as odr_tran', 'odr.id', '=', 'odr_tran.order_id')
                ->where(function ($q) use ($searchValue) {
                    if (stripos($searchValue, 'ODR') === 0 && strpos($searchValue, ' ') === false) {
                        $q->where('odr.order_code', '=', $searchValue)
                            ->orWhere('odr.order_code', 'like', $searchValue . '%');
                        return;
                    }
                    $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $searchValue) . '%';
                    $q->where('odr.order_code', 'like', $like)
                        ->orWhere('odr.order_number', 'like', $like)
                        ->orWhere('addr.name', 'like', $like)
                        ->orWhere('addr.street1', 'like', $like)
                        ->orWhere('addr.city', 'like', $like)
                        ->orWhere('addr.zip', 'like', $like)
                        ->orWhere('odr_tran.tracking_number', 'like', $like)
                        ->orWhere('odr_tran.shipping_carrier', 'like', $like);
                });

            $recordsFiltered = (int) (clone $filtered)->count(DB::raw('DISTINCT odr.id'));
            $orderIds = (clone $filtered)
                ->select('odr.id', 'odr.created_at')
                ->groupBy('odr.id', 'odr.created_at')
                ->orderByDesc('odr.created_at')
                ->offset(max(0, (int) $start))
                ->limit(max(1, (int) $length))
                ->pluck('odr.id')
                ->all();
        }

        if (empty($orderIds)) {
            return [
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'rows' => collect(),
            ];
        }

        $itemSub = "(SELECT GROUP_CONCAT(DISTINCT p.name ORDER BY p.name ASC SEPARATOR ';') "
            . 'FROM order_product op INNER JOIN products p ON p.id = op.product_id WHERE op.order_id = odr.id)';

        $rows = DB::table('orders as odr')
            ->join('order_package as pkg', 'odr.id', '=', 'pkg.order_id')
            ->join('order_addresses as addr', 'addr.id', '=', 'odr.order_address_to_id')
            ->leftJoin('order_transactions as odr_tran', 'odr.id', '=', 'odr_tran.order_id')
            ->leftJoin('order_rates as odr_rate', 'odr_tran.order_rate_id', '=', 'odr_rate.id')
            ->leftJoin('order_product as odr_pro', 'odr_pro.order_id', '=', 'odr.id')
            ->whereIn('odr.id', $orderIds)
            ->select([
                'odr.id',
                'odr.order_code',
                'odr.order_number',
                DB::raw('MAX(odr_rate.amount) as amount'),
                DB::raw('MAX(odr_tran.shipping_carrier) as shipping_carrier'),
                DB::raw("MAX(CASE WHEN odr_tran.tracking_number IS NOT NULL THEN CONCAT('\'', odr_tran.tracking_number) ELSE NULL END) as tracking"),
                DB::raw('MAX(odr_rate.provider) as provider'),
                'odr.partner_code',
                'addr.zip',
                'addr.name as receiver_name',
                DB::raw("CONCAT(addr.street1, ', ', addr.city, ',', addr.state, ',', addr.country) as ADDR"),
                DB::raw('MAX(pkg.weight) as weight'),
                DB::raw('MAX(pkg.width) as width'),
                DB::raw('MAX(pkg.height) as height'),
                DB::raw('MAX(pkg.length) as length'),
                DB::raw($itemSub . ' as ITEM'),
                'odr.created_at',
                DB::raw('MAX(odr_pro.quantity) as quantity'),
            ])
            ->groupBy(
                'odr.id',
                'odr.order_code',
                'odr.order_number',
                'odr.partner_code',
                'addr.zip',
                'addr.name',
                'addr.street1',
                'addr.city',
                'addr.state',
                'addr.country',
                'odr.created_at'
            )
            ->orderByDesc('odr.created_at')
            ->get();

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'rows' => $rows,
        ];
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
            $user = User::find(Auth::id());
            if (!$user) {
                return [
                    'request' => $request,
                    'errorMsg' => ['User not found'],
                ];
            }
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

            // Targeted PK update for the newly created order only (avoids scanning entire orders table)
            $orderCode = 'ODR' . str_pad((string) $newOrder->id, 10, '0', STR_PAD_LEFT);
            DB::table('orders')->where('id', $newOrder->id)->update(['order_code' => $orderCode]);
            DB::commit();

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
        // Avoid loading orderRates on list (can be many rows per order)
        $orders = Order::with([
            'orderProducts.product', 'orderPackage', 'orderTransaction',
            'addressFrom', 'addressTo',
        ])
            ->select([
                'id', 'order_code', 'order_number', 'status', 'payment', 'fulfillment',
                'state', 'user_id', 'partner_code',
                'order_address_from_id', 'order_address_to_id',
                'created_at', 'updated_at',
            ])
            ->where('user_id', Auth::id())
            ->where('state', '<>', Order::STATE_ON_HOLD_VIP);

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
            ->paginate(25);

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
