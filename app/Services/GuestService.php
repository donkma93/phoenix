<?php

namespace App\Services;

use App\Services\GuestServiceInterface;
use App\Models\PricingRequest;
use App\Models\Order;
use App\Models\OrderJourney;
use App\Models\PackingList;
use App\Models\PickupRequest;
use Illuminate\Support\Facades\Log;
use Shippo;
use Shippo_Object;
use Illuminate\Http\Request;
use Shippo_Track;

class GuestService implements GuestServiceInterface
{

    public function sendRequest($request)
    {
        $data = [
            'email' => $request['email'],
            'company' => $request['company'],
            'name' => $request['name'],
            'phone' => $request['phone'],
            'note' => isset($request['note']) ? $request['note'] : "",
            'services' => $request['services']
        ];

        PricingRequest::create($data);
        
    }

    private function getJourneyDescription($journey) {
        $packingList = PackingList::where('id', $journey->id_packing_list)
        ->select(['packing_list_code', 'master_bill', 'status', 'finish_date', 'finish_user', 'from_warehouse', 'to_warehouse'])
        ->first();
        $pickup = PickupRequest::where('id', $journey->id_pickup_request)
        ->select(['pickup_code', 'status', 'finish_date', 'finish_user', 'id_warehouse'])
        ->first();
        return [
            'packingList' => $packingList,
            'pickup' => $pickup,
            'id_pickup_request' => $journey->id_pickup_request,
            'id_packing_list' => $journey->id_packing_list
        ];
    }

    public function searchEngine($code)
    {
        $order = Order::with([
            'orderTransaction',
        ]);

        $order = $order
        ->whereHas('orderTransaction', function ($query) use ($code) {
            $query->where('tracking_number', $code);
        })
        ->first();

        $orderJournies =  OrderJourney::
        where('order_id', $order->id)
        ->get();

        $tOrderJournies = [];

        foreach ($orderJournies as $journeyItem) {
            $tOrderJournies[] = 
                array(
                    "date" => $journeyItem->created_date,
                    "status" => OrderJourney::$inoutName[$journeyItem->inout_type],
                    "description" => $this->getJourneyDescription($journeyItem),
                );
        }

        $status_params = null;
        $shippoTrackingStatus = null;

        if ( $order->orderTransaction->orderRate) {
          $status_params = array(
            'id' => $code,
            'carrier' => $order->orderTransaction->orderRate->provider,
            'tracking_number' => $code
          );      
          $shippoTrackingStatus = Shippo_Track::get_status($status_params);
        }

        Log::info("Tracking status: ". $shippoTrackingStatus);
  
        return array(
            'order' => $order,
            'local_journies' => $tOrderJournies,
            'shippo_tracking_status' => $shippoTrackingStatus ? json_decode(strval($shippoTrackingStatus)) : null
        );
      
    }
}
