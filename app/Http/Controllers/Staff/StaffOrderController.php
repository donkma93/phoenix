<?php

namespace App\Http\Controllers\Staff;

use App\Http\Requests\Staff\StoreLabelRequest;
use App\Http\Requests\Staff\StoreStaffOrderCsvRequest;
use App\Http\Requests\Staff\StoreStaffOrderRequest;
use App\Http\Requests\Staff\UpdateOrderPackageRequest;
use App\Http\Requests\Staff\UpdateOrderRequest;
use App\Http\Requests\Staff\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Models\User;
use App\Services\Staff\StaffOrderService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Validator;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\User\OrderFilterExport;
use Dotenv\Validator as DotenvValidator;
use Nette\Utils\Json;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Functions;
use Shippo_Refund;
use Shippo_Transaction;

class StaffOrderController extends StaffBaseController
{
    protected $orderService;

    public function __construct(StaffOrderService $orderService)
    {
        parent::__construct();
        $this->orderService = $orderService;
    }

    public function list(Request $request)
    {
        try {
            $input_data = $request->all();
            $date_from = $input_data['date_from'] ?? date('Y-m-d', strtotime('-1 month'));
            $date_to = $input_data['date_to'] ?? date('Y-m-d');
            $order_status = $input_data['bill_status'] ?? 99; // 99 = all

            // Suggest box needs id + email only (same UX as before; lighter than full User models)
            $users = User::where('role', User::ROLE_USER)
                ->select('id', 'email')
                ->orderBy('email')
                ->get();

            $count_status = $this->orderService->getOrderStatusCounts($date_from, $date_to);
            // Empty-state flag only; rows are loaded by DataTable AJAX (same as before)
            $hasOrders = $this->orderService->hasOrdersInRange($date_from, $date_to, $order_status);

            $request->flash();

            return view('order.list', [
                'emails' => $users->pluck('email')->values()->all(),
                'users' => $users,
                'orders' => $hasOrders ? [1] : [],
                'tracking_status' => config('app.tracking_status'),
                'count_status' => $count_status,
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
            $input_data = $request->all();
            $date_from = $input_data['date_from'] ?? date('Y-m-d', strtotime('-1 month'));
            $date_to = $input_data['date_to'] ?? date('Y-m-d');
            $order_status = $input_data['bill_status'] ?? 99; // 99 = all

            $draw = intval($request->input('draw'));
            $start = intval($request->input('start', 0));
            $length = intval($request->input('length', 50));
            if ($length <= 0) {
                $length = 50;
            }
            // Cap page size so a single AJAX call cannot load the entire dataset
            $length = min($length, 100);

            $searchValue = trim($request->input('search.value', ''));

            // True SQL LIMIT/OFFSET pagination â€” never load the full date-range into PHP memory
            $result = $this->orderService->listForDataTable(
                $date_from,
                $date_to,
                $order_status,
                $searchValue,
                $start,
                $length
            );

            $data = collect($result['rows'])->map(function ($order) {
                $checkbox = '<input type="checkbox" class="order-checkbox" value="' . ($order->id ?? '') . '">';
                $customerHtml = '<div>' . e($order->order_number ?? '') . '</div>'
                    . '<div>' . e($order->user_email ?? '') . '</div>'
                    . '<div style="display: none;">' . e($order->partner_code ?? '') . '</div>';
                $receiverHtml = '<b>Name:</b> ' . e($order->name ?? '') . '<br>'
                    . '<b>Address:</b> ' . e($order->addr ?? '') . '<br>'
                    . '<b>Zip:</b> ' . e($order->zip ?? '');

                $sizeType = isset($order->size_type) ? ucfirst(\App\Models\OrderPackage::$sizeName[$order->size_type] ?? '') : '';
                $weightType = isset($order->weight_type) ? ucfirst(\App\Models\OrderPackage::$weightName[$order->weight_type] ?? '') : '';
                $lengthVal = $order->length ?? 'Unknown';
                $widthVal = $order->width ?? 'Unknown';
                $heightVal = $order->height ?? 'Unknown';
                $weightVal = $order->weight ?? 'Unknown';
                $itemHtml = '<div><b>Name:</b> ' . e($order->item ?? '') . '</div>'
                    . '<div><b>Quantity:</b> ' . e($order->quantity ?? '') . '</div>'
                    . '<div>' . e($weightVal) . ' <b>' . e($weightType) . '</b> '
                    . e($lengthVal) . ' x ' . e($widthVal) . ' x ' . e($heightVal) . ' <b>' . e($sizeType) . '</b></div>';

                $amountStr = isset($order->amount) ? number_format($order->amount) : '';

                $trackingHtml = '<div>' . e($order->tracking_number ?? '') . '</div>'
                    . '<div><b>' . e($order->provider ?? '') . '</b></div>';

                $previewBtn = '';
                if (isset($order->label_url) && ($order->picking_status ?? null) != 5) {
                    // Support both relative paths and absolute MyIB/storage URLs
                    $rawLabel = $order->label_url;
                    $labelUrl = preg_match('#^https?://#i', $rawLabel) ? $rawLabel : asset($rawLabel);
                    $pathForExt = parse_url($rawLabel, PHP_URL_PATH) ?: $rawLabel;
                    $extension = strtolower(pathinfo($pathForExt, PATHINFO_EXTENSION));
                    $imageExtensions = ['png', 'jpg', 'jpeg', 'gif', 'webp'];
                    if (in_array($extension, $imageExtensions)) {
                        $previewBtn = '<button type="button" class="fmus01 btn btn-sm btn-round btn-success btn-block" data-toggle="modal" data-target="#preview-label" onclick="previewImage(`' . $labelUrl . '`)">Preview</button>';
                    } else {
                        $previewBtn = '<button type="button" class="fmus01 btn btn-sm btn-round btn-success btn-block" data-toggle="modal" data-target="#preview-label" onclick="previewPDF(`' . $labelUrl . '`)">Preview</button>';
                    }
                }
                $detailBtn = '<a class="btn btn-warning btn-round btn-sm btn-block" href="' . route('staff.orders.detail', ['id' => $order->id]) . '">Detail</a>';

                $chooseBtn = '';
                $canChoose = !empty($order->order_address_to_id) && ($order->transactions_id ?? null) == null && ($order->picking_status ?? null) != 5;
                if ($canChoose) {
                    if (!empty($order->odr_rate_id) || is_numeric($order->odr_rate_id ?? null)) {
                        $chooseBtn = '<a class="btn btn-primary btn-round btn-block btn-sm" href="' . route('staff.orders.rates.create', ['orderId' => $order->id]) . '">Choose Rate</a>';
                    } else {
                        $chooseBtn = '<a class="btn btn-primary btn-round btn-block btn-sm" href="' . route('staff.orders.labels.create', ['orderId' => $order->id]) . '">Transaction</a>';
                    }
                }

                $deleteOrderBtn = '';
                if (!(($order->transactions_id ?? null) || ($order->odr_rate_id ?? null))) {
                    $deleteOrderBtn = '<button data-order-id="' . $order->id . '" class="delete-order btn btn-block btn-round btn-warning btn-sm" onclick="deleteOrder(' . $order->id . ')">Delete Order</button>';
                }

                $holdResumeBtn = '';
                if (!($order->picking_status ?? null)) {
                    $holdResumeBtn = '<button data-order-id="' . $order->id . '" class="hold-order btn btn-block btn-round btn-secondary btn-sm" onclick="holdOrder(' . $order->id . ')">Hold Order</button>';
                } elseif (($order->picking_status ?? null) == 5) {
                    $holdResumeBtn = '<button data-order-id="' . $order->id . '" class="resume-order btn btn-block btn-round btn-success btn-sm" onclick="resumeOrder(' . $order->id . ')">Resume Ord</button>';
                }

                $deleteLabelBtn = '';
                $inPackingList = ($order->count_pkl ?? 0) * 1 !== 0;
                if (((($order->transactions_id ?? null) || is_numeric($order->transactions_id ?? null)) && !$inPackingList)) {
                    $deleteLabelBtn = '<button data-order-id="' . $order->id . '" class="delete-label btn btn-block btn-round btn-info btn-sm" onclick="deleteLabel(' . $order->id . ')">Delete Label</button>';
                }

                $actionsHtml = $previewBtn . $detailBtn . $chooseBtn;
                $extraHtml = '<div>' . $deleteOrderBtn . $holdResumeBtn . $deleteLabelBtn . '</div>';

                return [
                    'checkbox' => $checkbox,
                    'id' => $order->id,
                    'order_code' => e($order->order_code ?? ''),
                    'customer' => $customerHtml,
                    'receiver' => $receiverHtml,
                    'item' => $itemHtml,
                    'amount' => $amountStr,
                    'created_at' => e($order->created_at ?? ''),
                    'tracking' => $trackingHtml,
                    'actions' => $actionsHtml,
                    'extra' => $extraHtml,
                ];
            })->values();

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $result['recordsTotal'],
                'recordsFiltered' => $result['recordsFiltered'],
                'data' => $data,
            ]);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'draw' => intval($request->input('draw', 0)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Unexpected error',
            ]);
        }
    }

    public function importPricesExcel(Request $request)
    {
        try {
            $table_id = $request->input('table_id');
            $data = $this->orderService->storePricesExcel($request->file('prices_file'), $table_id);

            if (!$data['isValid']) {
                return redirect()->route('staff.prices.list', ['id' => $table_id])
                    ->with('error', 'Invalid data, please check again!')
                    ->with('fail', "Something wrong with this file")
                    ->with('csvErrors', $data['errors']);
            }

            return redirect()->route('staff.prices.list', ['id' => $table_id])->with('success', "Imported successfully!");
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->route('staff.prices.list', ['id' => $table_id])->with('fail', "Import failed!");
        }
    }

    public function uploadFiles(Request $request)
    {

        $data = array();
        $orderId = $request->input('order_id');
        $validator = Validator::make($request->all(), [
            'file' => 'required|max:2048'
        ]);

        if ($validator->fails()) {

            $data['success'] = 0;
            $data['error'] = $validator->errors()->first('file'); // Error response

        } else {
            if ($request->file('file')) {

                $file = $request->file('file');
                $filename = time() . '_' . $file->getClientOriginalName();

                // File extension
                $extension = $file->getClientOriginalExtension();

                $date = Carbon::now()->toDateString();

                // File upload location
                $location = public_path() . '/imgs/documents/' . $date;

                File::isDirectory($location) or File::makeDirectory($location, 0777, true, true);

                // Upload file
                $file->move($location, $filename);

                // File path
                $fileKey = '/imgs/documents/' . $date . '/' . $filename;

                $this->orderService->saveOrderFileUrl($orderId, $fileKey);

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

    public function _uploadFiles(Request $request)
    {
        $orderId = $request->input('order_id');
        $data = array();
        //$http_host = request()->getSchemeAndHttpHost();
        $http_host = 'https://phoenixlogistics.vn';

        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'file' => 'required|file',
        ]);
        
        if ($validator->fails()) {

            $data['success'] = 0;
            $data['error'] = $validator->errors()->first('file'); // Error response

        } else {
            $pathImg = null;
            if ($request->file('file')) {

                $file = $request->file('file');


                // LÆ°u trÃªn host
                // // File extension
                // $extension = $file->getClientOriginalExtension();
                // $fileName = str_replace(" ", "_", pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                // $filename = $fileName . '_' . time() . '.' . $extension;

                // $date = Carbon::now()->toDateString();

                // // File upload location
                // $location = public_path() . '/documents/' . $date;

                // File::isDirectory($location) or File::makeDirectory($location, 0777, true, true);

                // // Upload file
                // $file->move($location, $filename);

                // $fileKey = '/documents/' . $date . '/' . $filename;

                // $this->orderService->saveOrderFileUrl($orderId, $fileKey);

                // //    // File path
                // //    $filepath = url($fileKey);


                // LÆ°u trÃªn S3
                // $ext = $file->extension();
                // $extension = $file->getClientOriginalExtension();
                // $old_name = $file->getClientoriginalName();
                // $new_name = 'PNX_LABEL/' . date('Ym') . '/' . time() . '_' . rand(100000, 999999) . '_' . $this->clean_str($old_name, '/[^0-9a-zA-Z._-]/');
                // $path = Storage::disk('s3')->put($new_name, file_get_contents($file), 'public');
                // $pathImg = 'https://leuleu-ffm.hn.ss.bfcplatform.vn' . '/' . $new_name;
                // $this->orderService->saveOrderFileUrl($orderId, $pathImg);

                // // Response
                // $data['success'] = 1;
                // $data['message'] = 'Uploaded Successfully!';
                // $data['order_id'] = $orderId;
                // $data['filepath'] = $pathImg;
                // //$data['filepath'] = $http_host . $fileKey;
                // //$data['label_url'] = request()->getHost() . $fileKey;
                // $data['extension'] = $extension;
                $ext = $file->extension(); // ÄuÃ´i file theo mime-type
$extension = $file->getClientOriginalExtension(); // ÄuÃ´i gá»‘c
$old_name = $file->getClientOriginalName(); // TÃªn gá»‘c

// Táº¡o tÃªn file má»›i vÃ  folder
$folder = 'uploads/PNX_LABEL/' . date('Ym');
$cleaned_name = $this->clean_str($old_name, '/[^0-9a-zA-Z._-]/');
$new_name = time() . '_' . rand(100000, 999999) . '_' . $cleaned_name;

// LÆ°u file vÃ o storage/app/public/...
$path = Storage::disk('public')->putFileAs($folder, $file, $new_name);

// Táº¡o Ä‘Æ°á»ng dáº«n URL public
$pathImg = asset('storage/' . $folder . '/' . $new_name);

// Gá»i service lÆ°u DB
$this->orderService->saveOrderFileUrl($orderId, $pathImg);

// Tráº£ vá» client
$data['success'] = 1;
$data['message'] = 'Uploaded Successfully!';
$data['order_id'] = $orderId;
$data['filepath'] = $pathImg;
$data['extension'] = $extension;
            } else {
                // Response
                $data['success'] = 2;
                $data['message'] = 'File not uploaded.';
            }
        }

        return response()->json($data);
    }

    function clean_str($string, $pattern)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

        return preg_replace($pattern, '', $string); // Removes special chars.
    }

    /*
    public function uploadOrderFile(Request $request)
    {
        $result = $this->_uploadFiles($request);
        $dataObj = json_decode($result->getContent());
        if ($dataObj->success == 1) {
            // Náº¿u upload file thÃ nh cÃ´ng
            $order_id = $dataObj->order_id;
            $filepath = $dataObj->filepath;
            $extension = $dataObj->extension;
            // Äang Ä‘á»‹nh xá»­ lÃ½ update filepath á»Ÿ Ä‘Ã¢y

            $data = $result->getContent();
        } else {
            $data['success'] = 2;
            $data['message'] = 'File not uploaded!';
        }
        return json_encode($data);
    }
    */


    public function exportExcel($datefrom, $dateto)
    {

        $data = $this->orderService->listByDate($datefrom, $dateto);
        $export = new OrderFilterExport($data);
        $fileName = date('YmdHis', strtotime(\Carbon\Carbon::now())) . '-Order.xls';

        return Excel::download($export, $fileName);
    }

    protected function doDeleteLabel($order_id)
    {
        try {
            Log::error('========== LOG START doDeleteLabel ===================================================================');
            $role = Auth::user()->role;
            Log::error('========== LOG doDeleteLabel (1): Order_id: ' . $order_id . ', role: ' . $role);

            if ($role == 0 || $role == 1) {
                $deleteSuccess = false;
                $result = [];
                Log::error('========== LOG doDeleteLabel (2)');
                DB::beginTransaction();

                $check_packing_list = DB::select('select count(*) as count_pkl from order_journey left join packing_list on packing_list.id = id_packing_list where order_id = ' . $order_id . ' and id_packing_list is not null and packing_list.status = 10 and DATEDIFF(order_journey.created_date, SYSDATE()) > 2');
                if (isset($check_packing_list[0]->count_pkl) && $check_packing_list[0]->count_pkl * 1 > 0) {
                    $result = [
                        'status' => 'error',
                        'message' => 'This label cannot be deleted!'
                    ];

                    Log::error('========== LOG doDeleteLabel: This label cannot be deleted!');
                    return $result;
                };

                $provider = DB::table('order_transactions')->where('order_id', $order_id)->value('shipping_provider');
                $tracking_provider = DB::table('order_transactions')->where('order_id', $order_id)->value('tracking_provider');
                $providerLower = $provider ? strtolower($provider) : '';

                // G7 integration removed — treat legacy G7 labels as local cleanup only
                if ($providerLower === 'g7') {
                    $deleteSuccess = true;
                } elseif ($providerLower === 'shippo') {
                    try {
                        $transaction_id = DB::table('order_transactions')->where('order_id', $order_id)->value('transaction_id');
                        $labelInfo = Shippo_Transaction::retrieve($transaction_id);
                        Log::info('LABEL INFO: ' . (is_string($labelInfo) ? $labelInfo : json_encode($labelInfo)));

                        $statusVal = null;
                        if (is_string($labelInfo)) {
                            $decoded = json_decode($labelInfo);
                            $statusVal = $decoded->status ?? null;
                        } elseif (is_object($labelInfo)) {
                            $statusVal = $labelInfo->status ?? null;
                        } elseif (is_array($labelInfo)) {
                            $statusVal = $labelInfo['status'] ?? null;
                        }

                        if ($statusVal && in_array(strtoupper($statusVal), ['REFUNDED', 'REFUNDPENDING'])) {
                            $deleteSuccess = true;
                        } else {
                            $refund = Shippo_Refund::create(["transaction" => $transaction_id, "async" => false]);
                            Log::info('Refund label shippo: ' . (is_string($refund) ? $refund : json_encode($refund)));

                            $refundStatus = null;
                            if (is_string($refund)) {
                                $decRefund = json_decode($refund);
                                $refundStatus = $decRefund->status ?? null;
                            } elseif (is_object($refund)) {
                                $refundStatus = $refund->status ?? null;
                            } elseif (is_array($refund)) {
                                $refundStatus = $refund['status'] ?? null;
                            }

                            if (strtoupper((string)$refundStatus) !== 'ERROR') {
                                $deleteSuccess = true;
                            } else {
                                $result = [
                                    'status' => 'error',
                                    'message' => 'Remove label failed, please check API!'
                                ];
                            }
                        }
                    } catch (Exception $e2){
                        Log::error('SHIPPO delete error: ' . $e2->getMessage());
                        $deleteSuccess = true;
                    }
                } else {
                    $deleteSuccess = true; // proceed local cleanup for unknown provider
                }

                if ($deleteSuccess) {
                    // Capture local label path before deleting the DB row, then free disk
                    $labelUrl = DB::table('order_transactions')
                        ->where('order_id', $order_id)
                        ->value('label_url');

                    DB::table('order_transactions')->where('order_id', $order_id)->delete();

                    DB::table('order_rates')->where('order_id', $order_id)->delete();

                    DB::table('orders')->where('id', $order_id)
                        ->update([
                            'order_address_from_id' => null,
                            'tracking' => null
                        ]);

                    DB::commit();

                    if ($labelUrl) {
                        deleteLocalMediaFile($labelUrl);
                    }

                    $result = [
                        'status' => 'success',
                        'message' => 'Remove label successfully!'
                    ];
                }

                Log::error('========== LOG doDeleteLabel (8): ' . json_encode($result));
                return $result;
            } else {
                $result = [
                    'status' => 'error',
                    'message' => 'You do not have permission to perform this function!'
                ];

                Log::error('========== LOG doDeleteLabel (9): You do not have permission to perform this function');
                return $result;
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('========== LOG doDeleteLabel Exception: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function detail($id)
    {
        try {
            $item = $this->orderService->detail($id);

            //return view('staff.order.detail', $item);
            return view('order.detail', $item);
        } catch (Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    public function new($id)
    {
        try {
            $data = $this->orderService->new($id);
            $countries = DB::table('sys_country')->get()->toArray();
            //$states = DB::table('sys_states')->get()->toArray();
            //$cities = DB::table('sys_cities')->get()->toArray();
            $data['countries'] = $countries ?? [];
            $data['states'] = $states ?? [];
            $data['cities'] = $cities ?? [];

            return view('order.new', $data);
        } catch (Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    public function deleteLabel($order_id)
    {
        $result = $this->doDeleteLabel($order_id);
        return response()->json($result);
    }

    public function deleteOrder($order_id)
    {
        try {
            $role = Auth::user()->role;
            if ($role === 0 || $role === 1) {
                DB::beginTransaction();

                $order = DB::table('orders AS odr')->leftJoin('order_transactions AS odr_tran', 'odr.id', '=', 'odr_tran.order_id')
                    ->leftJoin('order_rates AS odr_rate', 'odr_tran.order_rate_id', '=', 'odr_rate.id')
                    ->select('odr.id', 'odr_tran.id AS transactions_id', 'odr_rate.id AS odr_rate_id')
                    ->where('odr.id', $order_id)->first();

                if (!!$order->transactions_id || !!$order->odr_rate_id) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'You cannot delete this order!'
                    ]);
                    exit();
                }

                DB::table('orders')->where('id', $order_id)
                    ->update([
                        'deleted_at' => date('Y-m-d H:i:s'),
                        'status' => Order::STATUS_CANCEL
                    ]);

                DB::commit();
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Delete order successfully!'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'You do not have permission to perform this function!'
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
        exit();
    }

    public function holdOrder($order_id)
    {
        try {
            $role = Auth::user()->role;
            if ($role === 0 || $role === 1) {
                DB::beginTransaction();

                DB::table('orders')->where('id', $order_id)
                    ->update([
                        'updated_at' => date('Y-m-d H:i:s'),
                        'picking_status' => 5
                    ]);

                DB::commit();
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Hold order successfully!'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'You do not have permission to perform this function!'
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
        exit();
    }

    public function resumeOrder($order_id)
    {
        try {
            $role = Auth::user()->role;
            if ($role === 0 || $role === 1) {
                DB::beginTransaction();

                DB::table('orders')->where('id', $order_id)
                    ->update([
                        'updated_at' => date('Y-m-d H:i:s'),
                        'picking_status' => 0
                    ]);

                DB::commit();
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Resume order successfully!'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'You do not have permission to perform this function!'
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
        exit();
    }

    public function bulkDeleteLabels(Request $request)
    {
        $ids = $request->input('order_ids', []);
        if (is_string($ids)) {
            $decoded = json_decode($ids, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $ids = $decoded;
            }
        }
        if (!is_array($ids) || count($ids) === 0) {
            return response()->json(['status' => 'error', 'message' => 'No orders selected.'], 400);
        }

        $success = [];
        $failed = [];
        foreach ($ids as $id) {
            $res = $this->doDeleteLabel((int)$id);
            if (($res['status'] ?? 'error') === 'success') {
                $success[] = $id;
            } else {
                $failed[] = ['id' => $id, 'message' => $res['message'] ?? 'unknown'];
            }
        }

        return response()->json([
            'status' => 'success',
            'deleted' => $success,
            'failed' => $failed,
        ]);
    }

    /**
     * Store a new order from csv.
     *
     * @param App\Http\Requests\Staff\StoreStaffOrderCsvRequest $request
     * @return \Illuminate\Http\Response
     */
    public function storeCSV(StoreStaffOrderCsvRequest $request)
    {
        try {
            $data = $this->orderService->storeCsv(request()->file('order_file'), $request->all());

            if (!$data['isValid']) {
                return redirect()->route('staff.orders.new', ['id' => $request->user_id])
                    ->with('fail', "Something wrong with this file")
                    ->with('csvErrors', $data['errors']);
            }

            return redirect()->route('staff.orders.list')->with('success', "Create new order successed");
        } catch (Exception $e) {
            Log::error($e);

            return redirect()->route('staff.orders.list')->with('fail', "Create new order failed");
        }
    }


    public function importLabelShippo(Request $request)
    {
        try {
            $data = $this->orderService->storeExcelShippo(request()->file('label_file'), $request->all());

            if (!$data['isValid']) {
                return back()
                    ->with('error', $data['message'] ?? '')
                    ->with('csvErrorsShippo', $data['errors'] ?? [])
                    ->with('errorsForeachShippo', $data['errorsArr'] ?? []);
            }

            return redirect()->route('staff.orders.list')->with('success', "Create labels successful");
        } catch (Exception $e) {
            Log::error($e);

            return redirect()->route('staff.orders.list')->with('fail', "Create labels failed");
        }
    }

    public function importLabelMyib(Request $request)
    {
        try {
            $data = $this->orderService->storeExcelMyib(request()->file('label_file'), $request->all());

            if (!$data['isValid']) {
                return back()
                    ->with('error', $data['message'] ?? '')
                    ->with('csvErrorsMyib', $data['errors'] ?? [])
                    ->with('errorsForeachMyib', $data['errorsArr'] ?? []);
            }

            if (count($data['ordersError']) > 0) {
                Log::warning('IMPORT LABELS FAILED: ' . implode(', ', $data['ordersError']));

                return redirect()->route('staff.orders.list')->with('warning', 'Create failed: ' . implode(', ', $data['ordersError']));
            }

            return redirect()->route('staff.orders.list')->with('success', "Create labels successful");
        } catch (Exception $e) {
            Log::error($e);

            return redirect()->route('staff.orders.list')->with('error', "Create labels failed");
        }
    }

    public function importLabelShipbae(Request $request)
    {
        try {
            if (!$request->hasFile('label_file')) {
                return back()->with('error', 'Please upload an Excel file for Shipbae import.');
            }

            $data = $this->orderService->storeExcelShipbae(request()->file('label_file'), $request->all());

            if (!$data['isValid']) {
                return back()
                    ->with('error', $data['message'] ?? 'Validate failed')
                    ->with('csvErrorsShipbae', $data['errors'] ?? [])
                    ->with('errorsForeachShipbae', $data['errorsArr'] ?? []);
            }

            if (count($data['ordersError']) > 0) {
                $errorSummary = implode(' | ', $data['ordersError']);
                Log::warning('IMPORT LABELS SHIPBAE FAILED: ' . $errorSummary);

                return back()
                    ->with('error', 'Create failed: ' . $errorSummary)
                    ->with('errorsForeachShipbae', $data['ordersError']);
            }

            $skipCount = count($data['ordersSkip'] ?? []);
            $successMsg = 'Create labels successful';
            if ($skipCount > 0) {
                $successMsg .= '. Skipped (already labeled): ' . implode(', ', $data['ordersSkip']);
            }

            return redirect()->route('staff.orders.list')->with('success', $successMsg);
        } catch (Exception $e) {
            Log::error($e);

            return back()->with('error', 'Create labels failed: ' . $e->getMessage());
        }
    }

    /**
     * Create a new order.
     *
     * @param App\Http\Requests\Staff\StoreStaffOrderRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create(StoreStaffOrderRequest $request)
    {
        try {
            $data = $this->orderService->create($request->all());

            if (count($data['errorMsg'])) {
                Log::error($data);
                return redirect()->back()
                    ->with('fail', "Information is invalid.")
                    ->with('errorData', $data);
            }

            return redirect()->route('staff.orders.list')->with('success', "Create new order successed");
        } catch (Exception $e) {
            Log::error($e);

            return redirect()->route('staff.orders.list')->with('fail', "Create new order failed");
        }
    }

    /**
     * Update order status.
     *
     * @param App\Http\Requests\Staff\UpdateOrderStatusRequest $request
     */
    public function updateStatus(UpdateOrderStatusRequest $request)
    {
        try {
            $this->orderService->updateStatus($request->all());
        } catch (Exception $e) {
            Log::error($e);

            abort(500);
        }
    }

    /**
     * Update order package.
     *
     * @param App\Http\Requests\Staff\UpdateOrderPackageRequest $request
     * @return \Illuminate\Http\Response
     */
    public function updatePackage(UpdateOrderPackageRequest $request)
    {
        try {
            $this->orderService->updatePackage($request->all());

            return redirect()->back()->with('success', "Update order package successed");
        } catch (Exception $e) {
            Log::error($e);

            return redirect()->back()->with('fail', "Update order package failed");
        }
    }

    /**
     * Update order.
     *
     * @param App\Http\Requests\Staff\UpdateOrderRequest $request
     * @return \Illuminate\Http\Response
     */
    public function updateOrder(UpdateOrderRequest $request)
    {
        try {
            $this->orderService->updateOrder($request->all());

            return redirect()->back()->with('success', "Update order successed");
        } catch (Exception $e) {
            Log::error($e);

            return redirect()->back()->with('fail', "Update order failed");
        }
    }

    public function createLabel($orderId)
    {
        try {
            $data = $this->orderService->createLabel($orderId);
            $countries = DB::table('sys_country')->get()->toArray();
            //$states = DB::table('sys_states')->get()->toArray();
            //$cities = DB::table('sys_cities')->get()->toArray();
            $data['countries'] = $countries ?? [];
            $data['states'] = $states ?? [];
            $data['cities'] = $cities ?? [];

            return view('order.create_label', $data);
        } catch (Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    function getStatesByCountryId(Request $request)
    {
        $id = $request->input('id');
        if (!!$id) {
            $states = DB::table('sys_states')->where('country_id', $id)->get()->toArray();
            echo json_encode($states);
        }
        exit();
    }

    function getCitiesByStateId(Request $request)
    {
        $id = $request->input('id');
        if (!!$id) {
            $cities = DB::table('sys_cities')->where('id_state', $id)->get()->toArray();
            echo json_encode($cities);
        }
        exit();
    }

    public function createLabelPdaApi(Request $request)
    {
        try {
            $data = $this->orderService->createLabelPdaApi($request);
            if (($data['message_code'] ?? null) != 'SUCCESS') {
                return response()->json($data, 400);
            }
            return response()->json($data);
        } catch (Exception $e) {
            Log::error($e);

            return response()->json([
                'message_code' => 'UNEXPECTED_ERROR',
                'message_text' => $e->getMessage()
            ], 400);
        }
    }


    public function createLabelMyib(StoreLabelRequest $request)
    {
        Log::error('============ LOG START createLabelMyib Order code: ' . $request->get('order_code') . ' ============================================================');
        try {
            Log::error('===== LOG createLabelMyib (1)');

            $orderId = $request->get('order_id');
            $data = $this->orderService->storeLabelMyib($request->all(), $orderId);

            if (count($data['errorMsg'])) {
                Log::error('===== LOG createLabelMyib Error: ' . json_encode($data['errorMsg']));
                return redirect()->back()
                    ->with('fail', "Information is invalid.")
                    ->with('errorData', $data);
            }

            // Store provider in session to filter rates
            session(['label_provider' => 'myib']);

            return redirect()->route('staff.orders.rates.create', ['orderId' => $orderId, 'provider' => 'myib'])
                ->with('success', "Create successed. Please choose rate.");

        } catch (Exception $e) {
            Log::error('===== LOG createLabelMyib Exception: ' . $e->getMessage());
            return redirect()->route('staff.orders.list')->with('fail', "Create new label failed");
        }
    }

    public function createLabelShipbae(StoreLabelRequest $request)
    {
        Log::info('============ LOG START createLabelShipbae Order code: ' . $request->get('order_code') . ' ============================================================');
        try {
            $orderId = $request->get('order_id');
            $data = $this->orderService->storeLabelShipbae($request->all(), $orderId);

            if (count($data['errorMsg'])) {
                Log::error('===== LOG createLabelShipbae Error: ' . json_encode($data['errorMsg']));
                return redirect()->back()
                    ->with('fail', "Information is invalid.")
                    ->with('errorData', $data);
            }

            session(['label_provider' => 'shipbae']);

            return redirect()->route('staff.orders.rates.create', ['orderId' => $orderId, 'provider' => 'shipbae'])
                ->with('success', "Create successed. Please choose rate.");

        } catch (Exception $e) {
            Log::error('===== LOG createLabelShipbae Exception: ' . $e->getMessage());
            return redirect()->route('staff.orders.list')->with('fail', "Create new label failed");
        }
    }

    public function createLabelExcelView()
    {
        return view('order.import-create-label');
    }

    public function createLabelOther(Request $request)
    {
        Log::info('============ LOG START createLabelOther ==========================================');
        try {
            $data = $request->input();
            Log::info('===== LOG createLabelOther (1): ' . json_encode($data));

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
            $amount = $data['amount'] ?? 0;
            $currency = 'VND';
            $label_url = $data['label_url'];
            $tracking_provider = '';
            $tracking_number = $data['tracking_number'];
            $shipping_carrier = $data['shipping_carrier'] ?? 'PNX';
            $shipping_provider = '';
            $width = $data['package_width'];
            $height = $data['package_height'];
            $length = $data['package_length'];
            $weight = $data['package_weight'];
            $size_type = $data['size_type'];
            $weight_type = $data['weight_type'];


            // { CALL phoenix.label_create_input(:p_user_id,:p_order_id,:p_shipping_name,:p_shipping_street,:p_shipping_address1,:p_shipping_address2,:p_shipping_company,:p_shipping_city,:p_shipping_zip,:p_shipping_province,:p_shipping_country,:p_shipping_phone,:p_amount,:p_currency,:p_label_url,:p_tracking_number,:p_shipping_carrier,:p_shipping_provider) }
            $results = DB::select('call label_create_input(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', [
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



            // Check náº¿u ngÆ°á»i táº¡o order cÃ³ webhook thÃ¬ xá»­ lÃ½ dá»¯ liá»‡u sau Ä‘Ã³ gá»­i vÃ o webhook
            $orderData = DB::table('orders')->where('id', $data['order_id'])->first();
            $webhook_url = DB::table('users as u')
                ->where('u.id', $orderData->user_id)
                ->where('u.deleted_at', null)
                ->pluck('webhook_url')->first();

            if ($webhook_url) {
                $addFrom = DB::table('order_addresses')->where('id', $orderData->order_address_from_id)->first();
                $addTo = DB::table('order_addresses')->where('id', $orderData->order_address_to_id)->first();
                $dataSendApi = (object) [
                    'event' => 'transaction_created',
                    'status' => 'Unknown',
                    'carrier' => '',
                    'customers_order' => $orderData->order_number,
                    'data' => [
                        'tracking_history' => [],
                        'tracking_status' => [
                            'status' => 'Unknown'
                        ],
                        'carrier' => $data['shipping_carrier'],
                        'tracking_number' => $data['tracking_number'],
                        'address_from' => [
                            'country' => $addFrom->country ?? '',
                            'zip' => $addFrom->zip ?? '',
                            'state' => $addFrom->state ?? '',
                            'city' => $addFrom->city ?? '',
                            'email' => "thuynguyen@gmail.com",
                            
                        ],
                        'address_to' => [
                            'country' => $addTo->country ?? '',
                            'zip' => $addTo->zip ?? '',
                            'state' => $addTo->state ?? '',
                            'city' => $addTo->city ?? '',
                        ]
                    ],
                    'date_created' => date("Y-m-d H:i:s")
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





            Log::info('===== LOG createLabelOther (2): ' . json_encode($results));

            return redirect(route('staff.orders.list'))->with('success', 'Label create successful!');
        } catch (Exception $e) {
            Log::error('===== LOG createLabelOther Exception: ' . $e->getMessage());
            //TODO redirect to error page
            // abort(500);

            return response([
                'message_code' => 'UNEXPECTED_ERROR',
                'message_text' => $e->getMessage(),
                'file_error' => $e->getFile(),
                'line_error' => $e->getLine(),
            ], 400);
        }
        exit();
    }

    public function getOrderPackageApi(Request $request)
    {
        try {
            $data = $this->orderService->getOrderPackageApi($request);
            if (($data['message_code'] ?? null) != 'SUCCESS') {
                return response()->json($data, 400);
            }
            return response()->json($data);
        } catch (Exception $e) {
            Log::error($e);

            return response()->json([
                'message_code' => 'UNEXPECTED_ERROR',
                'message_text' => $e->getMessage()
            ], 400);
        }
    }

    public function storeLabel(StoreLabelRequest $request, $orderId)
    {
        try {
            // Mua labelxxx 1
            $data = $this->orderService->storeLabel($request->all(), $orderId);

            if (count($data['errorMsg'])) {
                Log::error($data);
                return redirect()->back()
                    ->with('fail', "Information is invalid.")
                    ->with('errorData', $data);
            }

            // Store provider in session to filter rates
            session(['label_provider' => 'shippo']);

            return redirect()->route('staff.orders.rates.create', ['orderId' => $orderId, 'provider' => 'shippo'])
                ->with('success', "Create successed. Please choose rate.");
        } catch (Exception $e) {
            Log::error($e);

            return redirect()->route('staff.orders.list')->with('fail', "Create new label failed");
        }
    }

    public function createRate(Request $request, $orderId)
    {
        try {
            // Get provider from session or request, default to null (show all)
            $provider = session('label_provider') ?? $request->get('provider');
            $rates = $this->orderService->getRates($orderId, $provider);

            return view('order.create_rate', [
                'orderId' => $orderId,
                'rates' => $rates,
                'provider' => $provider
            ]);
        } catch (Exception $e) {
            Log::error($e);

            return ["Something wrong! Please contact admin for more information!"];
        }
    }

    public function storeRate(Request $request, $orderId)
{
    try {
        $rate = $request->input('rate');
        if (!$rate) {
            return response()->json(['errors' => ['Rate ID is required']], 422);
        }

        $data = $this->orderService->storeRate($rate, $orderId);

        if (!empty($data['errorMsg'])) {
            $httpCode = $data['httpCode'] ?? 400;
            return response()->json(['errors' => $data['errorMsg']], $httpCode);
        }

        session()->forget('label_provider');
        return response()->json(['success' => true]);
       
    } catch (Exception $e) {
        Log::error('storeRate Exception', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'order_id' => $orderId
        ]);

        return response()->json(['errors' => ['Something wrong! Please contact admin for more information!']], 500);
    }
}

    public function orderPrintMultiple(Request $request)
    {
        $params = $request->only('order_ids');
        $pdfFilePath = $this->orderService->orderPrintMultiple($params['order_ids']);
        return response()->download($pdfFilePath)->deleteFileAfterSend(true);
    }

    public function checkTrackingExist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'tracking_number' => null,
                'errors' => $validator->errors()
            ], 422);
        }

        $order = \App\Support\ApiAccess::resolveOrder($request->input('order_id'));
        if (!$order || !\App\Support\ApiAccess::canAccessOrder(auth()->user(), $order)) {
            return response()->json([
                'status' => 'error',
                'tracking_number' => null,
                'message' => 'Order not found or access denied',
            ], 404);
        }

        $res = DB::table('order_transactions')->where('order_id', $order->id)->first();

        if ($res && $res->tracking_number) {
            return response()->json([
                'status' => 'success',
                'tracking_number' => $res->tracking_number,
                'order_id' => $order->id,
                'order_code' => $order->order_code,
            ]);
        }

        return response()->json([
            'status' => 'error',
            'tracking_number' => null,
            'message' => 'No tracking number for this order',
        ], 404);
    }

    public function getLabelUrlByOrderId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
                'label_url' => null,
            ], 422);
        }

        $order = \App\Support\ApiAccess::resolveOrder($request->input('order_id'));
        if (!$order || !\App\Support\ApiAccess::canAccessOrder(auth()->user(), $order)) {
            return response()->json([
                'status' => 'error',
                'order_id' => $request->input('order_id'),
                'label_url' => null,
                'message' => 'Order not found or access denied',
            ], 404);
        }

        $tx = DB::table('order_transactions')->where('order_id', $order->id)->first();
        if (!$tx || empty($tx->label_url)) {
            return response()->json([
                'status' => 'error',
                'order_id' => $order->id,
                'label_url' => null,
                'message' => 'Label not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'order_id' => $order->id,
            'order_code' => $order->order_code,
            'label_url' => $tx->label_url,
            'tracking_number' => $tx->tracking_number ?? null,
        ]);
    }

    public function updateTrackingInfoByOrderId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'tracking_number' => 'required|string|max:255',
            'shipping_carrier' => 'required|string|max:255',
            'tracking_status' => 'nullable|string|max:255',
            'label_url' => 'nullable|url|max:2000',
            'amount' => 'nullable|numeric',
            'currency' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $order = \App\Support\ApiAccess::resolveOrder($request->input('order_id'));
        if (!$order || !\App\Support\ApiAccess::canAccessOrder(auth()->user(), $order)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found or access denied',
            ], 404);
        }

        // Staff-only write (route also has jwt.role, double-check)
        if (!\App\Support\ApiAccess::isStaffRole(auth()->user())) {
            return \App\Support\ApiAccess::forbidden();
        }

        $orderId = $order->id;
        $orderTrans = DB::table('order_transactions')->where('order_id', $orderId)->first();

        if ($orderTrans && $orderTrans->tracking_number) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order already has a tracking number.',
            ], 409);
        }

        $orderPackage = DB::table('order_package')->where('order_id', $orderId)->first();
        if (!$orderPackage) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order package dimensions not found. Update package before setting tracking.',
            ], 422);
        }

        try {
            $data = $request->input();
            $user_id = auth()->user()->id;

            // Prefer warehouse / existing from-address over hard-coded ship-from when available
            $from = null;
            if ($order->order_address_from_id) {
                $from = DB::table('order_addresses')->where('id', $order->order_address_from_id)->first();
            }
            if (!$from) {
                $warehouse = DB::table('warehouses')->where('type', 'B')->first();
                if ($warehouse) {
                    $from = (object) [
                        'name' => $warehouse->name ?? 'Warehouse',
                        'street1' => $warehouse->address ?? '',
                        'street2' => null,
                        'street3' => null,
                        'company' => $warehouse->name ?? '',
                        'city' => $warehouse->city ?? '',
                        'zip' => $warehouse->zip ?? '',
                        'state' => $warehouse->state ?? '',
                        'country' => $warehouse->country ?? 'US',
                        'phone' => $warehouse->phone ?? null,
                    ];
                }
            }

            $shipping_name = $from->name ?? 'Warehouse';
            $shipping_street = $from->street1 ?? '';
            $shipping_address1 = $from->street2 ?? null;
            $shipping_address2 = $from->street3 ?? null;
            $shipping_company = $from->company ?? '';
            $shipping_city = $from->city ?? '';
            $shipping_zip = $from->zip ?? '';
            $shipping_province = $from->state ?? '';
            $shipping_country = $from->country ?? 'US';
            $shipping_phone = $from->phone ?? null;

            $amount = $data['amount'] ?? 0;
            $currency = $data['currency'] ?? 'USD';
            $label_url = $data['label_url'] ?? null;
            $tracking_provider = null;
            $tracking_number = $data['tracking_number'];
            $shipping_carrier = $data['shipping_carrier'];
            $shipping_provider = $data['shipping_provider'] ?? 'MANUAL';
            $width = $orderPackage->width;
            $height = $orderPackage->height;
            $length = $orderPackage->length;
            $weight = $orderPackage->weight;
            $size_type = $orderPackage->size_type;
            $weight_type = $orderPackage->weight_type;
            $tracking_status = $data['tracking_status'] ?? 'UNKNOWN';

            DB::select('call label_create_input(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', [
                $user_id,
                $orderId,
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
                $tracking_status
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Update label successful!',
                'order_id' => $orderId,
            ]);
        } catch (Exception $e) {
            Log::error('updateTrackingInfoByOrderId: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function downloadPreviews(Request $request)
    {
        try {
            $orderIds = $request->input('order_ids', []);

            // Allow JSON string from hidden input, or array from multiple inputs
            if (is_string($orderIds)) {
                $decoded = json_decode($orderIds, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $orderIds = $decoded;
                }
            }

            if (!is_array($orderIds) || count($orderIds) === 0) {
                return back()->with('error', 'Please select at least one order.');
            }

            // Normalize to list of integers
            $orderIds = array_values(array_filter(array_map('intval', $orderIds), function($v){ return $v > 0; }));
            if (count($orderIds) === 0) {
                return back()->with('error', 'Please select at least one order.');
            }

            $rows = DB::table('order_transactions')
                ->select('order_id', 'label_url')
                ->whereIn('order_id', $orderIds)
                ->whereNotNull('label_url')
                ->get();

            if ($rows->count() === 0) {
                return back()->with('error', 'No preview files found for selected orders.');
            }

            $zipFileName = 'previews-' . date('YmdHis') . '.zip';
            $zipFullPath = storage_path('app/' . $zipFileName);

            if (file_exists($zipFullPath)) {
                @unlink($zipFullPath);
            }

            $zip = new \ZipArchive();
            if ($zip->open($zipFullPath, \ZipArchive::CREATE) !== true) {
                return back()->with('error', 'Cannot create zip file.');
            }

            foreach ($rows as $row) {
                $labelUrl = $row->label_url;
                if (!$labelUrl) {
                    continue;
                }

                $pathPart = parse_url($labelUrl, PHP_URL_PATH) ?? $labelUrl;
                $ext = pathinfo($pathPart, PATHINFO_EXTENSION);
                if (!$ext) {
                    $ext = 'pdf';
                }
                $zipName = 'order_' . $row->order_id . '.' . $ext;

                // Remote URL
                if (stripos($labelUrl, 'http://') === 0 || stripos($labelUrl, 'https://') === 0) {
                    try {
                        $content = @file_get_contents($labelUrl);
                        if ($content !== false) {
                            $zip->addFromString($zipName, $content);
                        }
                    } catch (\Exception $e) {
                        // skip on error
                    }
                    continue;
                }

                // Local file under public
                $localPath = public_path(ltrim($labelUrl, '/'));
                if (file_exists($localPath)) {
                    $zip->addFile($localPath, $zipName);
                    continue;
                }
            }

            $zip->close();

            if (!file_exists($zipFullPath)) {
                return back()->with('error', 'Failed to create archive.');
            }

            return response()->download($zipFullPath)->deleteFileAfterSend(true);
        } catch (Exception $e) {
            Log::error($e);
            return back()->with('error', 'Unexpected error while preparing downloads.');
        }
    }
}
