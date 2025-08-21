<?php

namespace App\Services\User;

use App\Models\ActionLogs;
use App\Models\Order;
use App\Models\OrderJourney;
use App\Models\PickupRequest;
use App\Models\User;
use App\Services\UserBaseServiceInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserPickupRequestService extends UserBaseService implements UserBaseServiceInterface
{


    public function indexStaff($request)
    {
        $date_from = $request['date_from'] ?? date('Y-m-d', strtotime('-1 month'));
        $date_to = $request['date_to'] ?? date('Y-m-d');

        $user = User::find(Auth::id());
        $pickups = PickupRequest::orderBy('created_at', 'DESC')->with('warehouses');

        if($date_from) {
            $pickups = $pickups->where('created_date', '>=', date('Y-m-d 00:00:00', strtotime($date_from)));
        }

        if($date_to) {
            $pickups = $pickups->where('created_date', '<=', date('Y-m-d 23:59:59', strtotime($date_to)));
        }

        //$pickups = $pickups->paginate();
        $pickups = $pickups->get();

        foreach ($pickups as $pickup) {
            $orderJourneys = OrderJourney::orderBy('created_at', 'DESC')->with([
                'pickupRequest'
            ])
                ->where('id_pickup_request', '=', $pickup->id)
                ->where('inout_type', '=', OrderJourney::INOUT_TYPE_CREATED)
                ->get()
                ->toArray();


            $totalKG = 0;
            $user_id = '';
            $user_name ='';
            foreach ($orderJourneys as $key => $orderJourney) {


                $order = Order::orderBy('created_at', 'ASC')->with([
                    'orderPackage'
                ])->find($orderJourney['order_id']);

                if (!!$order) {
                    $user_id = $order->user_id;
                    $user_name = $orderJourney['created_username'];
                    $totalKG = $totalKG + $order->orderPackage->weight;
                } else {
                    // dd($orderJourney,$order);
                    Log::info('ORDER_JOURNEY_ERROR: ' . json_encode($orderJourney));
                    unset($orderJourneys[$key]);
                }
            }
            $pickup['created_username'] = $user_name;
            $pickup['user_id'] = $user_id;
            $pickup['orderJourneys'] = $orderJourneys;
            $pickup['totalKG'] = $totalKG;
        }


        return [
            'pickups' => $pickups,
        ];
    }
    public function index($request)
    {
        $user = User::find(Auth::id());
        $pickups = PickupRequest::orderBy('created_at', 'DESC')
        ->where('partner_code', $user->partner_code)
        ->with('warehouses')->paginate();
        foreach ($pickups as $pickup) {
            $orderJourneys = OrderJourney::orderBy('created_at', 'DESC')->with([
                'pickupRequest'
            ])
                ->where('id_pickup_request', '=', $pickup->id)
                ->where('inout_type', '=', OrderJourney::INOUT_TYPE_CREATED)
                ->get()
                ->toArray();


            $totalKG = 0;
            $user_id = '';
            $user_name ='';
            foreach ($orderJourneys as $key => $orderJourney) {
                $order = Order::orderBy('created_at', 'ASC')->with([
                    'orderPackage'
                ])
                    ->find($orderJourney['order_id']);

                if ($order) {
                    $user_id = $order->user_id;
                    $user_name = $orderJourney['created_username'];
                    $totalKG = $totalKG + $order->orderPackage->weight;
                } else {
                    unset($orderJourneys[$key]);
                }
            }
            $pickup['created_username'] = $user_name;
            $pickup['user_id'] = $user_id;
            $pickup['orderJourneys'] = $orderJourneys;
            $pickup['totalKG'] = $totalKG;
        }


        return [
            'pickups' => $pickups,
        ];


        /*return [
            'pickups' => $pickups,
        ];*/
    }

    public function create()
    {
        $user = User::find(Auth::id());
        $orders = Order::orderBy('created_at', 'DESC')->with([
           'orderPackage',
        ])
            ->where('status', Order::STATUS_NEW)
            ->where('partner_code', $user->partner_code)
            ->get();
            /*->paginate();*/

        return  $orders;
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $now = Carbon::now();
            $user = User::where('role', User::ROLE_USER)->find(Auth::id());
            $pickup_request = PickupRequest::create([
                'pickup_code' =>  $this->generatePickupRequestCode($user->partner_code),
                'status' => PickupRequest::NEW,
                'created_date' => $now,
                'user_create' => Auth::id(),
                'partner_code' => $user->partner_code,
                'partner_id' => $user->partner_id,
            ]);
            foreach ($request['order_ids'] as $order_id) {
                $order = Order::where('id', $order_id)->first();
                $order_journey = [
                    'order_id' => $order_id,
                    'id_pickup_request' => $pickup_request->id,
                    'status' => OrderJourney::WAITTING,
                    'created_date' => $now,
                    'user_create' => Auth::id(),
                    'inout_type' => OrderJourney::INOUT_TYPE_CREATED,
                    'created_username' => $user->email,
                    'order_code' => $order->order_code,
                ];

                OrderJourney::create($order_journey);
                Order::where('id', $order_id)->update(['status' => Order::STATUS_INPROGRESS]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function show($id)
    {
        $orderJourneys = OrderJourney::with([
            'pickupRequest'
        ])
            ->where('id_pickup_request', '=', $id)
            ->where('inout_type', OrderJourney::INOUT_TYPE_CREATED)
            ->orderBy('created_at', 'DESC')
            //->paginate();
            ->get();
        foreach ($orderJourneys as $key => $orderJourney) {
            if (isset($orderJourney->order_id)) {
                $order = Order::with([
                    'orderProducts.product.category', 'orderPackage', 'orderTransaction',
                    'addressFrom', 'addressTo', 'orderTransaction.orderRate'
                ])->find($orderJourney->order_id);
                if (!!$order) {
                    $orderJourney['orders'] = $order;
                } else {
                    unset($orderJourneys[$key]);
                }
            }
        }

        return $orderJourneys;
    }

    public function listOrderJourneyByPickupId($id)
    {
        $pickup = PickupRequest::find($id);
        $orderJourneys = DB::select('select
        o1.order_code,
        o1.created_at ,
        IF( o2.created_at IS NOT NULL, o2.created_at, NULL ) AS pickup_date,
        o2.created_username,
        o1.to_warehouse
        FROM order_journey o1
        INNER JOIN orders odr
        ON o1.order_id = odr.id
        AND (odr.status <> 3 OR odr.deleted_at IS NULL)
        LEFT JOIN order_journey AS o2
        ON o1.id_pickup_request = o2.id_pickup_request
        AND o2.id != o1.id
        AND o2.inout_type = '.OrderJourney::INOUT_TYPE_PICKED.'
        AND o2.order_id = o1.order_id
        where o1.id_pickup_request = '.$id.' and o1.inout_type = '.OrderJourney::INOUT_TYPE_CREATED.' order by o2.created_at DESC;');

        return [
            'pickup' => $pickup,
            'orderJourneys' => $orderJourneys,
        ];
    }

    public function pickupStart($pickup_id)
    {
        $now = Carbon::now();
        $checkPickupStatus = PickupRequest::where('id', $pickup_id)->orWhere('pickup_code', $pickup_id)->first();
        if (!isset($checkPickupStatus)) {
            return [
                'message_code' => 'PICKUP_NOT_FOUND',
                'message_text' => 'pickup request not found',
            ];
        }


        if ($checkPickupStatus->status > PickupRequest::NEW) {
            return [
                'message_code' => 'PICKUP_IS_PICKING',
                'message_text' => 'pickup request is picking',
            ];
        }

        PickupRequest::where('id', $pickup_id)->orWhere('pickup_code', $pickup_id)->update([
            'status' => PickupRequest::PICKING,
        ]);

        return [
            'message_code' => 'SUCCESS',
            'message_text' => 'success',
        ];
    }

    public function pickupScanOrder($pickup_id, $order_id)
    {

        $checkpickup = PickupRequest::where('id', $pickup_id)->orWhere('pickup_code', $pickup_id)
        ->get()
        ->toArray();

        if (count($checkpickup) <= 0){
            return [
                'message_code' => 'PICKUP_NOT_FOUND',
                'message_text' => 'pickup request not found',
            ];
        }

        $pickup = PickupRequest::where('id', $pickup_id)->orWhere('pickup_code', $pickup_id)
        ->first();
        $checkOrderIdOut = OrderJourney::where('id_pickup_request', $pickup->id)
            ->where('order_code', $order_id)
            ->where('inout_type', OrderJourney::INOUT_TYPE_CREATED)
            ->get()
            ->toArray();

        if (count($checkOrderIdOut) <= 0) {
            return [
                'message_code' => 'ORDER_NOT_IN_PICKUP',
                'message_text' => 'Order not found',
            ];
        }

        $checkScan = OrderJourney::where('id_pickup_request', $pickup->id)
            ->where('order_code', $order_id)
            ->where('inout_type', OrderJourney::INOUT_TYPE_PICKED)
            ->get()
            ->toArray();

        if (count($checkScan) > 0) {
            return [
                'message_code'=> "DUPLICATED",
                'message_text'=> "Order duplicated"
            ];
        }
        $now = Carbon::now();
        $order = Order::where('id', $order_id)->orWhere('order_code', $order_id)->first();
        $user = User::find(Auth::id());

        $order_journey = [
            'order_id' => $order->id,
            'id_pickup_request' => $pickup->id,
            'status' => OrderJourney::PICKED,
            'user_create' => Auth::id(),
            'inout_type' => OrderJourney::INOUT_TYPE_PICKED,
            'created_date' => $now,
            'order_code'=> $order->order_code,
            'created_username' => $user->email
        ];
        OrderJourney::create($order_journey);

        $order_journey_in = count(OrderJourney::where('id_pickup_request', $pickup->id)
        ->where('inout_type', OrderJourney::INOUT_TYPE_PICKED)->get()->toArray());
        $order_journey_out = count(OrderJourney::where('id_pickup_request', $pickup->id)
        ->where('inout_type', OrderJourney::INOUT_TYPE_CREATED)->get()->toArray());

        if ($order_journey_in == $order_journey_out) {
            PickupRequest::where('id', $pickup->id)->update([
                'status' => PickupRequest::DONE,
                'finish_date' => $now,
                'finish_user' => Auth::id(),
            ]);
        }

        return [
            'message_code'=> 'SUCCESS',
            'message_text' => 'success',
            'data' => [
                'picked' => $order_journey_in,
                'created' => $order_journey_out,
            ],
        ];
    }

    public function pickupFinish($pickup_id)
    {
        $now = Carbon::now();
        $checkPickupStatus = PickupRequest::where('id', $pickup_id)->orWhere('pickup_code', $pickup_id)->first();
        if (!isset($checkPickupStatus)) {
            return [
                'message_code' => 'PICKUP_NOT_FOUND',
                'message_text' => 'pickup request not found',
            ];
        }

        if ($checkPickupStatus->status != PickupRequest::PICKING) {
            return [
                'message_code' => 'PICKUP_CANNOT_FINISH',
                'message_text' => 'pickup request cannot',
            ];
        }

        PickupRequest::where('id', $pickup_id)->orWhere('pickup_code', $pickup_id)->update([
            'status' => PickupRequest::DONE,
            'finish_date' => $now,
            'finish_user' => Auth::id(),
        ]);

        return [
            'message_code' => 'SUCCESS',
            'message_text' => 'success',
        ];
    }

    public function list()
    {
        $pickups = PickupRequest::orderBy('created_at', 'DESC')->get();

        if (count($pickups) <= 0) {
            return [
                'pickups' => [],
            ];
        }

        foreach ($pickups as $pickup) {
            $orderJourneys = OrderJourney::where('id_pickup_request', '=', $pickup->id)
                ->where('inout_type', OrderJourney::INOUT_TYPE_CREATED)
                ->get()
                ->toArray();
            $pickup['orders_count'] = count($orderJourneys);
        }

        return [
            'pickups' => $pickups,
        ];
    }

    public function getPickupRequestOrderJourneyInOut($pickup_id)
    {
        $count_picked =  count(OrderJourney::where('id_pickup_request', '=', $pickup_id)
            ->where('inout_type', OrderJourney::INOUT_TYPE_PICKED)
            ->get()
            ->toArray());

        $count_created = count(OrderJourney::where('id_pickup_request', '=', $pickup_id)
            ->where('inout_type', OrderJourney::INOUT_TYPE_CREATED)
            ->get()
            ->toArray());

            return [
                'message_code' => 'SUCCESS',
                'message_text' => 'success',
                'data' => [
                    'picked'=> $count_picked,
                    'created' => $count_created
                ]
            ];
    }

    public function cancel($pickup_id)
    {
        $pickup = PickupRequest::find($pickup_id);
        if (!isset($pickup)) {
            return [
                'message_code' => 'PICKUP_NOT_FOUND',
                'message_text' => 'pickup request not found',
                'data' => [
                    'pickup_id' => $pickup_id
                ]
            ];
        }

        if ($pickup->status != PickupRequest::NEW) {
            return [
                'message_code' => 'PICKUP_CANNOT_CANCEL',
                'message_text' => 'pickup request cannot cancel',
                'data' => [
                    'pickup_id' => $pickup_id
                ]
            ];
        }


        $orderJourneys = OrderJourney::orderBy('created_at', 'DESC')->where('id_pickup_request', $pickup_id)->get();
        foreach ($orderJourneys as $orderJourney) {
            OrderJourney::find($orderJourney->id)->forceDelete();
            Order::where('id', $orderJourney->order_id)->update([
                'status' => Order::STATUS_NEW
            ]);
        }

        $pickup->forceDelete();

        ActionLogs::create([
            'user_id' => Auth::id(),
            'action' => 'delete',
            'data' => $pickup->pickup_code,

        ]);
    }


    public function generatePickupRequestCode($partner_code)
    {
        $additional = str_split(uniqid(), 8);

        return strtoupper("pk" . $partner_code . $additional[0]);
    }
}
