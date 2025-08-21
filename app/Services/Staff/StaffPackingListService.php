<?php

namespace App\Services\Staff;

use App\Models\MainfestDetail;
use App\Models\Order;
use App\Models\OrderJourney;
use App\Models\PackingList;
use App\Models\User;
use App\Services\StaffBaseServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StaffPackingListService extends StaffBaseService implements StaffBaseServiceInterface
{

    public function list($status)
    {
        $packings = PackingList::orderBy('created_at', 'DESC')->where('status', PackingList::CREATED)
            ->orWhere('status', PackingList::PROCESSING)
            ->orWhere('status', PackingList::PACKED)
            ->get();

            foreach($packings as $packing) {
              $packing['quantity'] =  count(OrderJourney::where('id_packing_list', $packing['id'])
              ->where('inout_type', OrderJourney::INOUT_TYPE_CREATED)
              ->get()
              ->toArray());

              $packing['actual_receive'] =  count(OrderJourney::where('id_packing_list', $packing['id'])
              ->where('inout_type', OrderJourney::INOUT_TYPE_PICKED)
              ->get()
              ->toArray());
            }

        if (count($packings) <= 0) {
            return [
                'message_code' => 'SUCCESS',
                'message_text' => 'success',
                'packing_list' => [],
            ];
        }

        return [
            'message_code' => 'SUCCESS',
            'message_text' => 'success',
            'packing_list' => $packings,
        ];
    }

    public function listInboud()
    {
        $packings = PackingList::orderBy('created_at', 'DESC')
            ->where('status', PackingList::RECEIVING)
            ->orWhere('status', PackingList::DONE)
            ->orWhere('status', PackingList::PACKED)
            ->get();

            foreach($packings as $packing) {
              $packing['quantity'] =  count(OrderJourney::where('id_packing_list', $packing['id'])
              ->where('inout_type', OrderJourney::INOUT_TYPE_CREATED)
              ->get()
              ->toArray());
              $packing['actual_receive'] =  count(OrderJourney::where('id_packing_list', $packing['id'])
              ->where('inout_type', OrderJourney::INOUT_TYPE_PICKED)
              ->get()
              ->toArray());

            }

        if (count($packings) <= 0) {
            return [
                'message_code' => 'SUCCESS',
                'message_text' => 'success',
                'packing_list' => [],
            ];
        }

        return [
            'message_code' => 'SUCCESS',
            'message_text' => 'success',
            'packing_list' => $packings,
        ];
    }


    public function show($packing_id)
    {
        $orderJourneys = OrderJourney::with([
            'pickupRequest'
        ])
            ->where('id_packing_list', '=', $packing_id)
            ->where('inout_type', OrderJourney::INOUT_TYPE_CREATED)
            ->orderBy('created_at', 'DESC')
            ->paginate();

        foreach ($orderJourneys as $orderJourney) {
            if (isset($orderJourney->order_id)) {
                $order = Order::with([
                    'orderPackage'
                ])->find($orderJourney->order_id);
                $orderJourney['orders'] = $order;
            }
        }

        return $orderJourneys;
    }


    public function create($request)
    {
        $now = Carbon::now();
        $packing_list = [
            "packing_list_code" => $this->generatePLID("VN01"),
            "status" => PackingList::CREATED,
            "created_date" => $now,
            "create_user" => Auth::id(),
            "from_warehouse" => $request["from_warehouse"],
            "to_warehouse" => $request["to_warehouse"]
        ];

        PackingList::create($packing_list);

        return [
            "message_code" => "SUCCESS",
            "message_text" => 'success',
            "packing_list_code" => $packing_list["packing_list_code"]
        ];

    }

    public function start($packing_list_id)
    {
        $packing_list = PackingList::where('id', $packing_list_id)->orWhere('packing_list_code', $packing_list_id)->first();
        if (!isset($packing_list)) {
            return [
                "message_code" => "PACKING_LIST_NOT_FOUND",
                "message_text" => 'packing list not found'
            ];
        }

        if ($packing_list->status > PackingList::CREATED) {
            return [
                "message_code" => "PACKING_LIST_IS_PROCESSING",
                "message_text" => 'packing list is proccessing'
            ];
        }

        PackingList::where('id', $packing_list_id)->orWhere('packing_list_code', $packing_list_id)->update(['status'=> PackingList::PROCESSING]);

        return [
            "message_code" => "SUCCESS",
            "message_text" => 'success'
        ];
    }

    public function finish($packing_list_id, $masterbill)
    {
        $packing_list = PackingList::where('id', $packing_list_id)->orWhere('packing_list_code', $packing_list_id)->first();
        if (!isset($packing_list)) {
            return [
                "message_code" => "PACKING_LIST_NOT_FOUND",
                "message_text" => 'packing list not found'
            ];
        }

        $packing_list_by_master_bill = PackingList::where('master_bill', $masterbill)
            ->get();
        if (count($packing_list_by_master_bill) > 0) {
            return [
                "message_code" => "MASTER_BILL_ALREADY_EXIST",
                "message_text" => 'master bill already exist'
            ];
        }

        if ($packing_list->status >= PackingList::PACKED) {
            return [
                "message_code" => "PACKING_LIST_IS_PACKED",
                "message_text" => 'packing list is packed before'
            ];
        }

        PackingList::where('id', $packing_list_id)->orWhere('packing_list_code', $packing_list_id)->update([
            'status'=> PackingList::PACKED,
            'master_bill' => $masterbill
        ]);

        return [
            "message_code" => "SUCCESS",
            "message_text" => 'success',
            "packing_list_code" => $packing_list->packing_list_code
        ];
    }

    public function scan($packing_list_id, $order_id)
    {
        $now = Carbon::now();
        $packing_list = PackingList::where('id', $packing_list_id)->orWhere('packing_list_code', $packing_list_id)->first();

        if(!isset($packing_list)){
            return [
                'message_code' => 'PACKING_LIST_NOT_FOUND',
                'message_text' => 'packing list not found',
            ];
        }
        if ($packing_list->status > PackingList::PROCESSING) {
            return [
                'message_code' => 'PACKING_LIST_WAS_PACKED',
                'message_text' => 'packing list was packed',
            ];
        }

        $order = Order::where('id', $order_id)->orWhere('order_code', $order_id)->first();

        $orderJourneys = OrderJourney::where('id_packing_list', $packing_list->id)
            ->where('order_id', $order->id)
            ->where('inout_type', OrderJourney::INOUT_TYPE_CREATED)
            ->get()
            ->toArray();


        if (count($orderJourneys) > 0) {
            return [
                "message_code" => "ORDER_ALREADY_IN_BOUND",
                "message_text" => 'order already in bound'
            ];
        }

        $user = User::find(Auth::id());
        $packing_order_journey = [
            "inout_type" => OrderJourney::INOUT_TYPE_CREATED,
            "status" => OrderJourney::PACKING,
            "id_packing_list" => $packing_list->id,
            "order_id" => $order->id,
            "created_date" => $now,
            "from_warehouse" => $packing_list['from_warehouse'],
            "to_warehouse" => $packing_list['to_warehouse'],
            "user_create" => Auth::id(),
            "created_username" => $user->email,
            "order_code" => $order->order_code
        ];

        OrderJourney::create($packing_order_journey);
        if($packing_list->status == PackingList::CREATED) {
            PackingList::where('id', $packing_list->id)->update(['status' => PackingList::PROCESSING]);
        }

        $count = count(OrderJourney::where('id_packing_list', $packing_list->id)
        ->where('inout_type', OrderJourney::INOUT_TYPE_CREATED)
        ->get()
        ->toArray());

        return [
            "message_code" => "SUCCESS",
            "message_text" => 'success',
            "data" => [
                "total_orders" => $count,
                "order_id" => $order_id
            ],
        ];
    }

    public function received($master_bill, $order_id)
    {
        $now = Carbon::now();
        $packing_list = PackingList::where('master_bill', $master_bill)
        ->first();

        $packing_list_id = $packing_list['id'];

        if (!isset($packing_list)){
            return [
                'message_code' => 'PACKING_LIST_NOT_FOUND',
                'message_text' => 'packing list not found',
            ];
        }

        $order = Order::where('id', $order_id)->orWhere('order_code', $order_id)->first();
        if (!$order) {
            return [
                "message_code" => "NOT_FOUND",
                "message_text" => 'Order not found'
            ];
        }
        $orderJourneys = OrderJourney::where('order_id', $order->id)
            ->where('id_packing_list', $packing_list_id)
            ->where('inout_type', OrderJourney::INOUT_TYPE_PICKED)
            ->get()
            ->toArray();

        if (count($orderJourneys) > 0) {
            return [
                "message_code" => "ORDER_ALREADY_RECEIVED",
                "message_text" => 'order already received'
            ];
        }

        $packing_order_journey = [
            "inout_type" => OrderJourney::INOUT_TYPE_PICKED,
            "status" => OrderJourney::RECEIVING,
            "id_packing_list" => $packing_list_id,
            "order_id" => $order->id,
            "created_date" => $now,
            "from_warehouse" => $packing_list['from_warehouse'],
            "to_warehouse" => $packing_list['to_warehouse'],
            "user_create" => Auth::id()
        ];


        OrderJourney::create($packing_order_journey);
        if ($packing_list['status'] == PackingList::PACKED) {
            PackingList::where('id', $packing_list_id)->update(['status'=> PackingList::RECEIVING]);
        }

        MainfestDetail::where('order_id', $order->id)->update(['receive_date' => $now]);

        $count_in = count(OrderJourney::where('id_packing_list', $packing_list_id)
            ->where('inout_type', OrderJourney::INOUT_TYPE_PICKED)
            ->get()
            ->toArray());

        $count_out = count(OrderJourney::where('id_packing_list', $packing_list_id)
            ->where('inout_type', OrderJourney::INOUT_TYPE_CREATED)
            ->get()
            ->toArray());

        if ($count_in == $count_out) {
            PackingList::where('id', $packing_list_id)->update(['status'=> PackingList::DONE]);
        }

        return [
            "message_code" => "SUCCESS",
            "message_text" => 'success',
            "data" => [
                "total_order_in" => $count_in,
                "total_order_out" => $count_out,
                "order_id" => $order_id
            ],
        ];
    }

    public function receiveFinish($master_bill)
    {
        $packing_list = PackingList::where('master_bill', $master_bill)
        ->first();

        $packing_list_id = $packing_list['id'];

        if (!isset($packing_list)){
            return [
                'message_code' => 'PACKING_LIST_NOT_FOUND',
                'message_text' => 'packing list not found',
            ];
        }
        if ($packing_list['status'] == PackingList::DONE) {
            return [
                'message_code' => 'PACKING_LIST_WAS_DONE',
                'message_text' => 'packing list was done',
            ];
        }

        PackingList::where('id', $packing_list_id)->update(['status'=> PackingList::DONE]);
        return [
            "message_code" => "SUCCESS",
            "message_text" => 'success',
            "packing_list_code" => $packing_list['packing_list_code']
        ];
    }

    public function outbound($request)
    {
        $date_from = $request['date_from'] ?? date('Y-m-d', strtotime('-1 week'));
        $date_to = $request['date_to'] ?? date('Y-m-d');
        /*
        $packing_list = PackingList::orderBy('created_date', 'DESC')
            ->where('status', PackingList::CREATED)
            ->orWhere('status', PackingList::PROCESSING)
            ->orWhere('status', PackingList::PACKED)
            ->paginate();
        */

        $packing_list = PackingList::orderBy('created_date', 'DESC')
            ->where(function ($packing_list){
                $packing_list   ->where('status', PackingList::CREATED)
                                ->orwhere('status', PackingList::PROCESSING)
                                ->orwhere('status', PackingList::PACKED);
            });

        if(isset($request['keyword'])) {
            $keyword = $request['keyword'];
            $packing_list = $packing_list->where(function ($packing_list) use($keyword) {
                $packing_list   ->where('packing_list_code', 'like', '%' . $keyword . '%')
                                ->orwhere('master_bill', 'like', '%' . $keyword . '%')
                                ->orwhere('created_at', 'like', '%' . $keyword . '%')
                                ->orwhere('updated_at', 'like', '%' . $keyword . '%');
            });
        }

        if($date_from) {
            $packing_list = $packing_list->where('created_date', '>=', date('Y-m-d 00:00:00', strtotime($date_from)));
        }

        if($date_to) {
            $packing_list = $packing_list->where('created_date', '<=', date('Y-m-d 23:59:59', strtotime($date_to)));
        }

        //$packing_list = $packing_list->paginate();
        $packing_list = $packing_list->get();

        foreach ($packing_list as $packing) {
            $orderJourneys = OrderJourney::where('status', OrderJourney::PACKING)
            ->where('id_packing_list', $packing->id)
            ->where('inout_type', OrderJourney::INOUT_TYPE_CREATED)
            ->get()->toArray();
            $packing['quantity'] = count($orderJourneys);
        }

        return [
            "packing_list" => $packing_list
        ];
    }

    public function inbound()
    {
        $packing_list = PackingList::orderBy('created_date', 'DESC')
            ->where('status', PackingList::RECEIVING)
            ->orWhere('status', PackingList::DONE)
            ->orWhere('status', PackingList::PACKED)
            ->paginate();

        foreach ($packing_list as $packing) {
            $orderJourneys = OrderJourney::where('status', OrderJourney::PACKING)
            ->where('id_packing_list', $packing->id)
            ->where('inout_type', OrderJourney::INOUT_TYPE_CREATED)
            ->get()->toArray();
            $packing['quantity'] = count($orderJourneys);
            $packing['received'] = count(OrderJourney::where('id_packing_list', $packing->id)
            ->where('inout_type', OrderJourney::INOUT_TYPE_PICKED)
            ->get()->toArray());
        }

        return [
            "packing_list" => $packing_list
        ];
    }

    public function generatePLID($warehouse_code) {
        $additional = str_split(uniqid(), 8);
        return strtoupper("PL" . $warehouse_code . $additional[0]);
    }
}
