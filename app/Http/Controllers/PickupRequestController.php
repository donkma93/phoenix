<?php

namespace App\Http\Controllers;

use App\Models\PickupRequest;
use App\Models\Order;
use App\Services\User\UserPickupRequestService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use PDF;

class PickupRequestController extends Controller
{

    protected $pickupRequestService;

    public function __construct(UserPickupRequestService $pickupRequestService)
    {
        $this->pickupRequestService = $pickupRequestService;
    }

    public function index(Request $request)
    {

        $dataPickup = $this->pickupRequestService->index($request->all());
        return view('user.pickup.list', ['data' => $dataPickup]);
    }

       /**
     * List for print
     *
     * @return \Illuminate\View\View
     */
    public function pickupDetailPrint(Request $request)
    {
        $input = $request->input('label_list');

        if (!$input) {
            return back();
        }

        $orders = Order::with([
             'addressTo',
             'orderPackage'
        ])->whereIn('order_code',  $input)->orderBy('order_code', 'asc')->get();

        return view('order-tracking.print', [
            'orders' => $orders
        ]);
    }

    public function generateBarcodePDF(Request $request)
    {
        $input = $request->input('label_list');

        if (!$input) {
            return back();
        }

        $orders = Order::with([
            'addressTo',
            'orderPackage'
        ])->whereIn('order_code',  $input)->orderBy('order_code', 'asc')->get();

        $pdf = PDF::loadView('print-barcode-pdf', [
            'orders' => $orders
        ]);
        //$pdf->setPaper('A4', 'landscape');

        return $pdf->download(date('YmdHis') . rand(100, 999) . '.pdf');
    }

    public function create()
    {
        $orders = $this->pickupRequestService->create();

        return view('user.pickup.create', ['orders' => $orders]);
    }

    public function store(Request $request)
    {

        $this->pickupRequestService->store($request);
        return redirect()->route('pickup.index');
    }

    public function show($id)
    {
        $orderJourneys = $this->pickupRequestService->show($id);

        return view('user.pickup.detail', compact('orderJourneys'));
    }

    public function scan(Request $request)
    {

        Log::info('PickupRequestControllerScan');
        $validator = Validator::make($request->all(), [
            'pickup_id' => 'required',
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            $msg = [
                'message_code'=> 'PICKUP_REQUIRED_VALIDATOR',
                'message_text'=> "pickup_id or order_id fields is required",
                "errors" => $validator->errors(),
            ];
            return response($msg, 422);
        }

        $pickup_id = $request['pickup_id'];
        $order_id = $request['order_id'];

        $inoutmsgs = $this->pickupRequestService->pickupScanOrder($pickup_id, $order_id);

        return response()->json($inoutmsgs);
    }

    public function start($pickup_id)
    {
        try {
            $msg = $this->pickupRequestService->pickupStart($pickup_id);
            if ($msg['message_code'] != 'SUCCESS') {
                return response($msg, 400);
            }

            return response()->json($msg);
        } catch (Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }


    }

    public function finish($pickup_id)
    {

        try {
            $msg = $this->pickupRequestService->pickupFinish($pickup_id);
            if ($msg['message_code'] != 'SUCCESS') {
                return response($msg, 400);
            }
            return response()->json($msg);
        } catch (Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    public function list()
    {
        try {
            $msg = $this->pickupRequestService->list();
            return response()->json([
                'messeage_code'=> 'SUCCESS',
                'message_text' => 'success',
                'data' => $msg
            ]);
        } catch (Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    public function getPickupOrderJourneyByID($pickup_id)
    {
        try {
            $msg = $this->pickupRequestService->getPickupRequestOrderJourneyInOut($pickup_id);
            return response()->json($msg);
        } catch (Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    public function destroy($pickup_id)
    {
      $this->pickupRequestService->cancel($pickup_id);
       return redirect()->route('pickup.index');
    }
}
