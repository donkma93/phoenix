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
            // $data = $this->orderService->index($request->all());

            $data = [];
            $userId = auth()->user()->id;
            $dateFrom = trim($request->input('date_from'));
            $dateTo = trim($request->input('date_to'));
            if (!$dateTo) {
                $dateTo = date('Y-m-d');
            }
            if (!$dateFrom) {
                $dateFrom = date('Y-m-d', strtotime('-1 month'));
            }
            $results = DB::select('call customer_order_list(?,?,?)', [
                $dateFrom,
                $dateTo,
                $userId
            ]);

            $data['orders'] = $results;
            $data['oldInput'] = $request->input();

            return view('user.order.index', $data);
        } catch (Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
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
            'product.*.id' => 'required|distinct',
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
                'errorMsg' => $validator->errors() // get array errors
            ]);
        }

        // Retrieve the validated input...
        $validated = $validator->validated();

        // Lấy bảng giá
        // Từ id lấy ra partner_code trong bảng users, sau đó từ partner_code lấy ra id_price_table trong bảng partners
        $id_price_table = null;
        $partner_code = Auth::user()->partner_code;

        if (!!$partner_code) {
            $id_price_table = DB::table('partners')->where('partner_code', $partner_code)->value('id_price_table');
        }

        $validated['id_price_table'] = $id_price_table;

        try {
            $data = $this->orderService->store($validated);

            if (count($data['errorMsg'])) {
                Log::error("Create order API error: " . json_encode($data));
                $data['status'] = 'error';
                return response()->json($data);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Created order successfully.',
                'orderCode' => $data['orderCode'],
            ]);
        } catch (Exception $e) {
            Log::error($e);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
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
        $customerOrderCode = trim($request->get('customer_order_code'));
        if (!$customerOrderCode) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Order code is required.'
            ]);
        }

        $order = DB::table('orders')
            ->where('order_number', $customerOrderCode)
            ->where('user_id', auth()->id())
            ->whereNull('deleted_at')
            ->first();

        if (!$order) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Order not found.'
            ]);
        }

        $response = [
            'status' => 'succeed',
            'customer_order_code' => $customerOrderCode,
            'carrier' => null,
            'tracking_number' => null,
            'tracking_status' => null,
            'tracking_journey' => null,
            'label_url' => null,
            'address_to' => null,
            'address_from' => null
        ];

        if ($order->order_address_to_id) {
            $response['address_to'] = DB::table('order_addresses')
                ->select('country', 'state', 'city', 'street1 as street', 'zip', 'name', 'phone', 'email', 'company')
                ->where('id', $order->order_address_to_id)
                ->whereNull('deleted_at')
                ->first();
        }

        if ($order->order_address_from_id) {
            $response['address_from'] = DB::table('order_addresses')
                ->select('country', 'state', 'city', 'street1 as street', 'zip', 'name', 'phone', 'email', 'company')
                ->where('id', $order->order_address_from_id)
                ->whereNull('deleted_at')
                ->first();
        }

        $orderTransaction = DB::table('order_transactions')
            ->where('order_id', $order->id)
            ->whereNull('deleted_at')
            ->first();

        if ($orderTransaction) {
            $response['label_url'] = $orderTransaction->label_url;
            $response['carrier'] = strtoupper($orderTransaction->shipping_carrier);
            $response['tracking_number'] = $orderTransaction->tracking_number;
            $response['tracking_status'] = $orderTransaction->tracking_status;

            $carrier = DB::table('order_tracking_journey')->where('bill_code_ref', $orderTransaction->tracking_number)
                ->where('carrier', '!=', '')
                ->whereNotNull('carrier')
                ->pluck('carrier')->first();

            if ($carrier) {
                $response['carrier'] = strtoupper($carrier);
            }

            if ($orderTransaction->tracking_number) {
                $journey = DB::table('order_tracking_journey')
                    ->where('bill_code_ref', $orderTransaction->tracking_number)
                    ->orderBy('date_journey', 'desc')
                    ->get()->toArray();
                $response['tracking_journey'] = $journey;
            }
        }

        return response()->json($response);
    }
}
