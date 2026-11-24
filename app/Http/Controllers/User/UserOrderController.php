<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\StoreUserOrderCsvRequest;
use App\Http\Requests\User\StoreUserOrderRequest;
use App\Services\User\UserOrderService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Validator;
use Exception;
use File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Exports\User\SkuExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\User\OrderFilterExport;

class UserOrderController extends UserBaseController
{
    protected $orderService;

    public function __construct(UserOrderService $orderService)
    {
        parent::__construct();
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        try {
            $userId = auth()->user()->id;
            $dateFrom = trim((string) $request->input('date_from'));
            $dateTo = trim((string) $request->input('date_to'));
            if (!$dateTo) {
                $dateTo = date('Y-m-d');
            }
            if (!$dateFrom) {
                $dateFrom = date('Y-m-d', strtotime('-1 month'));
            }

            // Empty-state flag only; rows are paginated server-side via orders.datatable AJAX
            $hasOrders = $this->orderService->hasUserOrdersInRange($userId, $dateFrom, $dateTo);

            return view('user.order.index', [
                'orders' => $hasOrders ? [1] : [],
                'oldInput' => array_merge($request->input(), [
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                ]),
            ]);
        } catch (Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    public function listDataTable(Request $request)
    {
        try {
            $userId = auth()->user()->id;
            $dateFrom = trim((string) $request->input('date_from')) ?: date('Y-m-d', strtotime('-1 month'));
            $dateTo = trim((string) $request->input('date_to')) ?: date('Y-m-d');

            $draw = intval($request->input('draw'));
            $start = max(0, intval($request->input('start', 0)));
            $length = intval($request->input('length', 50));
            if ($length <= 0) {
                $length = 50;
            }
            $length = min($length, 100);
            $searchValue = trim((string) $request->input('search.value', ''));

            $result = $this->orderService->listForUserDataTable(
                $userId,
                $dateFrom,
                $dateTo,
                $searchValue,
                $start,
                $length
            );

            $data = collect($result['rows'])->map(function ($order) {
                $orderCode = e($order->order_code ?? '');
                $recipientHtml = '<div><b>Address: </b>' . e($order->ADDR ?? '') . '</div>'
                    . '<div><b>Zip: </b>' . e($order->zip ?? '') . '</div>';
                $packageHtml = '<div><b>Length: </b>' . e($order->length ?? '') . '</div>'
                    . '<div><b>Width: </b>' . e($order->width ?? '') . '</div>'
                    . '<div><b>Height: </b>' . e($order->height ?? '') . '</div>'
                    . '<div><b>Weight: </b>' . e($order->weight ?? '') . '</div>';
                $itemHtml = '<div><b>Name: </b>' . e($order->ITEM ?? '') . '</div>'
                    . '<div><b>Quantity: </b>' . e($order->quantity ?? '') . '</div>';
                $trackingHtml = '<div>' . e($order->tracking ?? '') . '</div>'
                    . '<b>' . e($order->shipping_carrier ?? '') . '</b>';
                $actionsHtml = '<div><a class="btn btn-round btn-sm btn-info btn-block" href="'
                    . route('orders.show', ['id' => $order->id]) . '">' . e(__('Detail')) . '</a></div>'
                    . '<div class="mt-2"><button class="btn btn-round btn-sm btn-success btn-block print-label" type="button" data-order-code="'
                    . $orderCode . '">' . e(__('Print')) . '</button></div>';
                $checkboxHtml = '<input id="' . $orderCode . '" class="form-group select_item_order" type="checkbox" name="label_list[]" value="' . $orderCode . '">';

                return [
                    'order_code' => $orderCode,
                    'order_number' => e($order->order_number ?? ''),
                    'receiver_name' => e($order->receiver_name ?? ''),
                    'recipient' => $recipientHtml,
                    'package_info' => $packageHtml,
                    'item' => $itemHtml,
                    'amount' => !empty($order->amount) ? number_format((float) $order->amount) : '',
                    'created_at' => e($order->created_at ?? ''),
                    'tracking' => $trackingHtml,
                    'actions' => $actionsHtml,
                    'checkbox' => $checkboxHtml,
                ];
            })->values()->all();

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $result['recordsTotal'],
                'recordsFiltered' => $result['recordsFiltered'],
                'data' => $data,
            ]);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Failed to load orders.',
            ], 500);
        }
    }

    public function uploadFiles(Request $request)
    {
        $orderId = $request->input('order_id');
        $data = array();

        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'file' => 'required',
        ]);

        if ($validator->fails()) {

            $data['success'] = 0;
            $data['error'] = $validator->errors()->first('file'); // Error response

        } else {
            if ($request->file('file')) {

                $file = $request->file('file');

                // File extension
                $extension = $file->getClientOriginalExtension();
                $fileName = str_replace(" ", "_", pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $filename = $fileName . '_' . time() . '.' . $extension;

                $date = Carbon::now()->toDateString();

                // File upload location
                $location = public_path() . '/imgs/documents/' . $date;

                File::isDirectory($location) or File::makeDirectory($location, 0777, true, true);

                // Upload file
                $file->move($location, $filename);

                $fileKey = '/imgs/documents/' . $date . '/' . $filename;

                $this->orderService->saveOrderFileUrl($orderId, $fileKey);

                //    // File path
                //    $filepath = url($fileKey);

                // Response
                $data['success'] = 1;
                $data['message'] = 'Uploaded Successfully!';
                $data['filepath'] = $fileKey;
                $data['extension'] = $extension;
            } else {
                // Response
                $data['success'] = 2;
                $data['message'] = 'File not uploaded.';
            }
        }

        return response()->json($data);
    }

    public function uploadTrackingInfo(Request $request)
    {
        $orderId = $request->input('order_id');
        $trackingCode = $request->input('tracking_code');
        $data = array();

        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'tracking_code' => 'required',
            'file' => 'required',
        ]);

        if ($validator->fails()) {

            $data['success'] = 0;
            $data['error'] = $validator->errors()->first('file'); // Error response

        } else {
            if ($request->file('file')) {

                $file = $request->file('file');

                // File extension
                $extension = $file->getClientOriginalExtension();
                $fileName = str_replace(" ", "_", pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $filename = $fileName . '_' . time() . '.' . $extension;

                $date = Carbon::now()->toDateString();

                // File upload location
                $location = public_path() . '/imgs/documents/' . $date;

                File::isDirectory($location) or File::makeDirectory($location, 0777, true, true);

                // Upload file
                $file->move($location, $filename);

                $fileKey = '/imgs/documents/' . $date . '/' . $filename;

                $this->orderService->saveTrackingInfo($orderId, $fileKey, $trackingCode);

                //    // File path
                //    $filepath = url($fileKey);

                // Response
                $data['success'] = 1;
                $data['message'] = 'Uploaded Successfully!';
                $data['filepath'] = $fileKey;
                $data['extension'] = $extension;
            } else {
                // Response
                $data['success'] = 2;
                $data['message'] = 'File not uploaded.';
            }
        }

        return response()->json($data);
    }


    public function show($id)
    {
        try {
            $data = $this->orderService->show($id);
            return view('user.order.show', $data);
        } catch (Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    public function create(Request $request)
    {
        try {
            $data = $this->orderService->create();
            $countries = DB::table('sys_country')->get()->toArray();
            //$states = DB::table('sys_states')->get()->toArray();
            //$cities = DB::table('sys_cities')->get()->toArray();
            $data['countries'] = $countries ?? [];
            $data['states'] = $states ?? [];
            $data['cities'] = $cities ?? [];

            return view('user.order.create', $data);
        } catch (Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * Store a new user request.
     *
     * @param  App\Http\Requests\User\StoreUserOrderCsvRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function storeCSV(StoreUserOrderCsvRequest $request)
    {
        try {
            $data = $this->orderService->storeCsv(request()->file('order_file'));

            if (!$data['isValid']) {
                return redirect()->route('orders.create')
                    ->with('fail', "Something wrong with this file")
                    ->with('csvErrors', $data['errors']);
            }

            return redirect()->route('orders.index')->with('success', "Create new order successed");
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->route('orders.index')->with('fail', "Create new order failed");
        }
    }



    public function exportSKUExcel()
    {

        $data = $this->orderService->exportSKU(auth()->user()->id);
        $export = new SkuExport($data);
        $fileName = date('YmdHis', strtotime(\Carbon\Carbon::now())) . '-SKU-sample.xls';

        return Excel::download($export, $fileName);
    }


    public function exportExcel($datefrom, $dateto)
    {

        $data = $this->orderService->listByDate($datefrom, $dateto);
        $export = new OrderFilterExport($data);
        $fileName = date('YmdHis', strtotime(\Carbon\Carbon::now())) . '-Order.xls';

        return Excel::download($export, $fileName);
    }


    /**
     * Store a new user request.
     *
     * @param  App\Http\Requests\User\StoreUserOrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserOrderRequest $request)
    {
        try {
            $data = $this->orderService->store($request->all());

            if (count($data['errorMsg'])) {
                Log::error($data);
                return redirect()->back()
                    ->with('fail', "Information is invalid.")
                    ->with('errorData', $data);
            }

            return redirect()->route('orders.index')->with('success', "Create new order successed");
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->route('orders.index')->with('fail', "Create new order failed");
        }
    }

    public function storeApi(Request $request) {
        $validator = Validator::make($request->all(), [
            'order_number' => 'nullable|string|max:255',
            'shipping_name' => 'required|string|max:255',
            'shipping_company' => 'nullable|string|max:255',
            'shipping_country' => 'required|string|max:255',
            'shipping_province' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_street' => 'required|string|max:35',
            'shipping_address1' => 'nullable|string|max:35',
            'shipping_address2' => 'nullable|string|max:35',
            'shipping_zip' => 'required|string|max:255',
            'shipping_phone' => 'nullable|string|max:30',
            'package_width' => 'nullable|numeric|gt:0',
            'package_height' => 'nullable|numeric|gt:0',
            'package_length' => 'nullable|numeric|gt:0',
            'package_weight' => 'nullable|numeric|gt:0',

            'product' => 'required|array|min:1',
            'product.*.id' => 'required|distinct|integer',
            'product.*.unit_number' => 'required|integer|min:1',
            'product.*.sku' => 'required|max:255',
        ], [], [
            'shipping_zip' => 'postal code / zip ',
            'product.*.id' => 'product',
            'product.*.unit_number' => 'unit number',
            'product.*.sku' => 'sku',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errorMsg' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Product ownership: only products belonging to the authenticated customer
        $productIds = collect($validated['product'])->pluck('id')->unique()->values();
        $ownedCount = DB::table('products')
            ->where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->whereIn('id', $productIds)
            ->count();
        if ($ownedCount !== $productIds->count()) {
            return response()->json([
                'status' => 'error',
                'message' => 'One or more products do not belong to this account.',
                'errorMsg' => ['product' => ['Invalid product id for current user']],
            ], 422);
        }

        // Multi-line or multi-qty requires explicit package dimensions
        $needsDims = count($validated['product']) > 1
            || collect($validated['product'])->sum('unit_number') > 1;
        if ($needsDims) {
            foreach (['package_width', 'package_height', 'package_length', 'package_weight'] as $dim) {
                if (empty($validated[$dim])) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Package dimensions and weight are required for multi-item orders.',
                        'errorMsg' => [$dim => ['Required for multi-item orders']],
                    ], 422);
                }
            }
        }

        $id_price_table = null;
        $partner_code = Auth::user()->partner_code;
        if ($partner_code) {
            $id_price_table = DB::table('partners')->where('partner_code', $partner_code)->value('id_price_table');
        }
        $validated['id_price_table'] = $id_price_table;

        try {
            $data = $this->orderService->store($validated);

            if (count($data['errorMsg'] ?? [])) {
                Log::error("Create order API error: " . json_encode($data));
                $data['status'] = 'error';
                return response()->json($data, 422);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Created order successfully.',
                'orderCode' => $data['orderCode'] ?? null,
            ]);
        } catch (Exception $e) {
            Log::error($e);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function saveWebhookUrl(Request $request) {
        $validator = Validator::make($request->all(), [
            'webhook_url' => 'required|url|max:255',
            //'shipping_zip' => 'required|string|max:255',
        ], [], [
            'webhook_url' => 'webhook URL',
            //'shipping_zip' => 'postal code / zip ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errorMsg' => $validator->errors() // get array errors
            ]);
        }

        // Retrieve the validated input...
        $validated = $validator->validated();

        try {
            DB::table('users')->where('id', auth()->user()->id)->update([
                'webhook_url' => $validated['webhook_url']
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Added webhook url successfully.'
            ]);
        } catch (Exception $e) {
            Log::error($e);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getOrderDetail(Request $request) {
        // Accept both customer_order_code (order_number) and order_code
        $customerOrderCode = trim((string) ($request->get('customer_order_code') ?: $request->get('order_code') ?: ''));
        if ($customerOrderCode === '') {
            return response()->json([
                'status' => 'error',
                'message' => 'Order code is required.'
            ], 422);
        }

        // Single merged query: orders + address_to + address_from + order_transactions (1 DB round-trip instead of 4)
        $userId = auth()->id();
        $primaryCol = stripos($customerOrderCode, 'ODR') === 0 ? 'o.order_code' : 'o.order_number';
        $fallbackCol = $primaryCol === 'o.order_code' ? 'o.order_number' : 'o.order_code';

        $baseQuery = function (string $col) use ($userId, $customerOrderCode) {
            return DB::table('orders as o')
                ->leftJoin('order_addresses as addr_to', function ($join) {
                    $join->on('addr_to.id', '=', 'o.order_address_to_id')
                        ->whereNull('addr_to.deleted_at');
                })
                ->leftJoin('order_addresses as addr_from', function ($join) {
                    $join->on('addr_from.id', '=', 'o.order_address_from_id')
                        ->whereNull('addr_from.deleted_at');
                })
                ->leftJoin('order_transactions as tx', function ($join) {
                    $join->on('tx.order_id', '=', 'o.id')
                        ->whereNull('tx.deleted_at');
                })
                ->select([
                    'o.id',
                    'o.order_code',
                    'o.order_address_to_id',
                    'o.order_address_from_id',
                    'addr_to.id as to_exists',
                    'addr_to.country as to_country',
                    'addr_to.state as to_state',
                    'addr_to.city as to_city',
                    'addr_to.street1 as to_street',
                    'addr_to.zip as to_zip',
                    'addr_to.name as to_name',
                    'addr_to.phone as to_phone',
                    'addr_to.email as to_email',
                    'addr_to.company as to_company',
                    'addr_from.id as from_exists',
                    'addr_from.country as from_country',
                    'addr_from.state as from_state',
                    'addr_from.city as from_city',
                    'addr_from.street1 as from_street',
                    'addr_from.zip as from_zip',
                    'addr_from.name as from_name',
                    'addr_from.phone as from_phone',
                    'addr_from.email as from_email',
                    'addr_from.company as from_company',
                    'tx.id as tx_exists',
                    'tx.label_url',
                    'tx.shipping_carrier',
                    'tx.tracking_number',
                    'tx.tracking_status',
                ])
                ->where('o.user_id', $userId)
                ->whereNull('o.deleted_at')
                ->where($col, $customerOrderCode)
                ->first();
        };

        $row = $baseQuery($primaryCol) ?: $baseQuery($fallbackCol);

        if (!$row) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found.'
            ], 404);
        }

        $response = [
            'status' => 'success',
            'customer_order_code' => $customerOrderCode,
            'order_code' => $row->order_code ?? null,
            'carrier' => null,
            'tracking_number' => null,
            'tracking_status' => null,
            'tracking_journey' => null,
            'label_url' => null,
            'address_to' => $row->to_exists ? (object) [
                'country' => $row->to_country,
                'state' => $row->to_state,
                'city' => $row->to_city,
                'street' => $row->to_street,
                'zip' => $row->to_zip,
                'name' => $row->to_name,
                'phone' => $row->to_phone,
                'email' => $row->to_email,
                'company' => $row->to_company,
            ] : null,
            'address_from' => $row->from_exists ? (object) [
                'country' => $row->from_country,
                'state' => $row->from_state,
                'city' => $row->from_city,
                'street' => $row->from_street,
                'zip' => $row->from_zip,
                'name' => $row->from_name,
                'phone' => $row->from_phone,
                'email' => $row->from_email,
                'company' => $row->from_company,
            ] : null,
        ];

        if ($row->tx_exists) {
            $response['label_url'] = $row->label_url;
            $response['carrier'] = strtoupper((string) $row->shipping_carrier);
            $response['tracking_number'] = $row->tracking_number;
            $response['tracking_status'] = $row->tracking_status;

            // Single indexed query on order_tracking_journey (eliminates duplicate carrier pluck query & SELECT *)
            if (!empty($row->tracking_number)) {
                $journey = DB::table('order_tracking_journey')
                    ->select(
                        'id',
                        'bill_code_ref',
                        'carrier',
                        'bill_code',
                        'status',
                        'note',
                        'location',
                        'city',
                        'country',
                        'date_journey',
                        'create_date',
                        'provider'
                    )
                    ->where('bill_code_ref', $row->tracking_number)
                    ->orderBy('date_journey', 'desc')
                    ->get()
                    ->toArray();

                foreach ($journey as $step) {
                    if (!empty($step->carrier)) {
                        $response['carrier'] = strtoupper($step->carrier);
                        break;
                    }
                }

                $response['tracking_journey'] = $journey;
            }
        }

        return response()->json($response);
    }
}
