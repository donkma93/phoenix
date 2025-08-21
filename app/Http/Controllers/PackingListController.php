<?php

namespace App\Http\Controllers;

use App\Services\Staff\StaffPackingListService;
use App\Services\User\UserPickupRequestService;
use App\Helpers\Functions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\Staff\StaffOrderService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PackingListController extends Controller
{
    protected $packingListService;
    protected $pickupRequestService;
    protected $orderService;

    public function __construct(
        StaffPackingListService $packingListService,
        StaffOrderService $orderService,
        UserPickupRequestService $pickupRequestService
    ) {
        $this->packingListService = $packingListService;
        $this->pickupRequestService = $pickupRequestService;
        $this->orderService = $orderService;
    }


    public function pickupIndex(Request $request)
    {
        $data = $this->pickupRequestService->indexStaff($request);
        return view('packinglist.pickupindex', compact('data'));
    }

    public function pickupShow($pickup_id)
    {
        $data = $this->pickupRequestService->listOrderJourneyByPickupId($pickup_id);
        $orderJourneys = $data['orderJourneys'];

        $pickup_code = $data['pickup']->pickup_code;

        return view('packinglist.detail', compact('orderJourneys', 'pickup_code'));
    }

    public function orderPrintMultiple(Request $request)
    {
        $params = $request->only('label_list');
        $pdfFilePath = $this->orderService->orderPrintMultiple($params['label_list']);
        if ($pdfFilePath) {
            return response()->download($pdfFilePath)->deleteFileAfterSend(true);
        } else {
            return response("No file");
        }
    }

    public function list(Request $request)
    {
        $packing_list = $this->packingListService->list($request);
        return response()->json($packing_list);
    }

    public function listInboud()
    {
        $packing_list = $this->packingListService->listInboud();
        return response()->json($packing_list);
    }

    public function store(Request $request)
    {
        $message = $this->packingListService->create($request);
        if ($message['message_code'] != "SUCCESS") {
            return response($message, 400);
        }
        return response()->json($message);
    }

    public function storeWeb(Request $request)
    {
        $this->packingListService->create($request);

        return redirect()->route('staff.packing.outbound');
    }

    public function scan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'packing_list_id' => 'required',
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            $msg = [
                'message_code' => 'PACKING_LIST_VALIDATOR',
                'message_text' => "packing_list_id or order_id fields is required",
                "errors" => $validator->errors(),
            ];
            return response($msg, 422);
        }

        $message = $this->packingListService->scan($request['packing_list_id'], $request['order_id']);
        if ($message['message_code'] != "SUCCESS") {
            return response($message, 400);
        }
        return response()->json($message);
    }

    public function scanweb(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'packing_list_id' => 'required',
            'order_id' => 'required',
        ]);
        if ($validator->fails()) {
            $errormsg = [
                'message_code' => 'PACKING_LIST_VALIDATOR',
                'message_text' => "packing_list_id or order_id fields is required",
                "errors" => $validator->errors(),
            ];
            return redirect()->route('staff.packing.show', ['packing_id' => $request['packing_list_id']]);
        }

        $errormsg = $this->packingListService->scan($request['packing_list_id'], $request['order_id']);
        if ($errormsg['message_code'] != "SUCCESS") {
            return redirect()->route('staff.packing.show', ['packing_id' => $request['packing_list_id']]);
        }
        return redirect()->route('staff.packing.show', ['packing_id' => $request['packing_list_id']]);
    }

    public function start($picking_list_id)
    {
        $msg = $this->packingListService->start($picking_list_id);
        if ($msg['message_code'] != "SUCCESS") {
            return response($msg, 400);
        }

        return response()->json($msg);
    }

    public function finishApi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'packing_list_id' => 'required',
            'master_bill' => 'required',
        ]);

        if ($validator->fails()) {
            $msg = [
                'message_code' => 'PACKING_LIST_VALIDATOR',
                'message_text' => "packing_list_id or master_bill fields is required",
                "errors" => $validator->errors(),
            ];
            return response($msg, 422);
        }

        /*
        $master_bill = $request['master_bill'];
        $carrier_id = 100002; // Quét PA sẽ fix carrier là UPS
        */

        $msg = $this->packingListService->finish($request['packing_list_id'], $request['master_bill']);
        if ($msg['message_code'] != "SUCCESS") {
            return response($msg, 400);
        }

        /*
        // Gọi API đăng ký master bill với 17track
        try {
            Log::error('===== LOG finishApi (1): ' . '[{
                            "number": "' . $master_bill . '"
                        }]');

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.17track.net/track/v2/register',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '[
                        {
                            "number": "' . $master_bill . '",
                            "carrier": ' . $carrier_id . '
                        }
                    ]',
                CURLOPT_HTTPHEADER => array(
                    '17token: AD2D4178F7603172B880FCF685AAE1B5',
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            Log::error('========== LOG finishApi (2): ' . json_encode($response));

            curl_close($curl);

            //return redirect()->route('staff.packing.outbound')->with('success', 'Successful packaging!');
        } catch (\Exception $err) {
            Log::error('========== LOG finishApi Exception: ' . $err->getMessage());
            return response()->json($err->getMessage());
        }
        */

        return response()->json($msg);
    }

    public function receive(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'master_bill' => 'required',
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            $msg = [
                'message_code' => 'PACKING_LIST_VALIDATOR',
                'message_text' => "master bill or order_id fields is required",
                "errors" => $validator->errors(),
            ];
            return response($msg, 422);
        }

        $message = $this->packingListService->received($request['master_bill'], $request['order_id']);
        if ($message['message_code'] != "SUCCESS") {
            return response($message, 400);
        }
        return response()->json($message);
    }

    public function receiveFinish(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'master_bill' => 'required',
        ]);

        if ($validator->fails()) {
            $msg = [
                'message_code' => 'PACKING_LIST_VALIDATOR',
                'message_text' => "master bill fields is required",
                "errors" => $validator->errors(),
            ];
            return response($msg, 422);
        }
        $message = $this->packingListService->receiveFinish($request['master_bill']);
        if ($message['message_code'] != "SUCCESS") {
            return response($message, 400);
        }
        return response()->json($message);
    }

    public function outbound(Request $request)
    {
        $data = $this->packingListService->outbound($request->all());
        $request->flash();
        return view('packinglist.outbound', compact('data'));
    }

    public function inbound()
    {
        $data = $this->packingListService->inbound();
        return view('staff.packinglist.inbound', compact('data'));
    }

    public function finishView($packing_id)
    {
        $orderJourneys = $this->packingListService->show($packing_id);
        $carriers = config('app.list_carrier');
        return view('packinglist.finish-packinglist', compact('orderJourneys', 'packing_id', 'carriers'));
    }

    public function finishPackingListWithMasterBill(Request $request)
    {
        Log::error('============ LOG START finishPackingListWithMasterBill ==========================================');
        Log::error('===== LOG finishPackingListWithMasterBill (1): ' . json_encode($request));
        if (!isset($request['master_bill'])) {
            Log::error('===== LOG finishPackingListWithMasterBill (2)');
            return redirect()->route('staff.packing.outbound')->with('error', 'Master bill is Null!');
        }

        $master_bill = trim($request['master_bill']);
        $carrier_id = trim($request['carrier_id']);
        $packing_id = trim($request['packing_id']);

        $msg = $this->packingListService->finish($packing_id, $master_bill);
        Log::error('====== LOG finishPackingListWithMasterBill (3): ' . json_encode($msg));

        if ($msg['message_code'] != 'SUCCESS') {
            Log::error('===== LOG finishPackingListWithMasterBill (4)');
            return redirect()->route('staff.packing.outbound')->with('error', $msg['message_text']);
        }

        // Gọi API đăng ký master bill với 17track
        try {
            Log::error('===== LOG finishPackingListWithMasterBill (5): ' . '[{
                            "number": "' . $master_bill . '",
                            "carrier": ' . $carrier_id . '
                        }]');

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.17track.net/track/v2/register',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '[
                        {
                            "number": "' . $master_bill . '",
                            "carrier": ' . $carrier_id . '
                        }
                    ]',
                CURLOPT_HTTPHEADER => array(
                    '17token: AD2D4178F7603172B880FCF685AAE1B5',
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            Log::error('========== LOG finishPackingListWithMasterBill (6): ' . json_encode($response));

            curl_close($curl);

            //dd($response);

            return redirect()->route('staff.packing.outbound')->with('success', 'Successful packaging!');
        } catch (\Exception $err) {
            Log::error('========== LOG finishPackingListWithMasterBill Exception: ' . $err->getMessage());
            return redirect()->route('staff.packing.outbound')->with('error', 'Master bill registration error (17track)');
        }
    }


    public function packinglist_search($packing_code)
    {


        if (Auth::user()->role ==  1) {
            $packing_list = DB::select('call search_packinglist_detail(?)', [$packing_code]);
            return response()->json($packing_list);
        }
    }


    public function bill_search($bill_code)
    {

        $results = Functions::CallRaw('search_bill',[
            $bill_code
        ]);

        $packingList = $results[1]? $results[1][0]:'';

        $result = [
            'bill_info' => $results[0][0] ?? '',
            'bill_packinglist' => $packingList,
            'bill_journey' => collect($results[2]),
        ];


        echo json_encode($result);
    }
}
