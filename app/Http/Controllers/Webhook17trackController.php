<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Webhook17trackController extends Controller
{
    /**
     * @param Request $request
     * @return json
     */
    public function handleData(Request $request)
    {
        // We have access to the request body here
        // So, you can perform any logic with the data
        // In my own case, I will add the delay function

        Log::info('----------START LOG WEBHOOK 17TRACK----------: \n' . $request->getContent());

        $webhook_data = json_decode($request->getContent());

        $p_code = $webhook_data->data->number; // Master bill
        $carrier_id = $webhook_data->data->carrier;
        $carrier_name = $webhook_data->data->track_info->tracking->providers[0]->provider->name;
        $tracking_status = $webhook_data->data->track_info->latest_status->status;
        $tracking_status_code = 2; // Để mặc định là 2 - Transit

        try {
            Log::info('----------START LOG WEBHOOK 17TRACK---------- (1)');
            // Từ master bill vào bảng packing_list để lấy packing_list_code
            $packing_list_code = DB::table('packing_list')->where('master_bill', $p_code)->value('packing_list_code');

            // Lấy ra các order có trong packing_list_code đó
            $list_order = DB::select('call search_packinglist_detail(?)', [$packing_list_code]);
            Log::info('----------START LOG WEBHOOK 17TRACK---------- (2)');
        } catch (\Exception $e) {
            Log::info('----------START LOG WEBHOOK 17TRACK--- (3) Exception: ' . json_encode($e));
        }

        //echo json_encode($list_order); exit();

        foreach ($list_order as $order) {

            // Cập nhật lại status trong bảng orders (dùng lại cột picking_status để lưu tracking status -> lamdt)
            try {
                DB::table('orders')
                    ->where('id', '=', $order->id)
                    ->update([
                        'picking_status' => $tracking_status_code,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                // Kiểm tra hành trình xem đã tồn tại chưa, và trả về số lượng hành trình
                // { CALL phoenix.webhook_info_shippo(:p_code,:p_provider) }
                /*$results = DB::select('call webhook_info_shippo(?,?)', [
                    $p_code,
                    $carrier_name
                ]);*/

                $count_events_inserted = DB::table('order_tracking_journey')->where([
                    ['bill_code_ref', $p_code],
                    ['carrier', $carrier_name],
                    ['bill_code', $order->order_code]
                ])->count();

                // Nếu có hành trình hoặc hành trình thay đổi thì cập nhật hành trình
                $total_current_events = $webhook_data->data->track_info->tracking->providers[0]->events;
                //$total_current_events = array_reverse($total_current_events);

                for ($i = 0; $i < count($total_current_events) - $count_events_inserted; $i++) {
                    $event = $total_current_events[$i];

                    $p_bill_ref = $p_code;
                    $p_status = 'TRANSIT';
                    $p_note = $event->description;
                    $p_location = $event->address->city . ', ' . $event->address->state . ', ' . $event->address->country;
                    $p_city = $event->address->city;
                    $p_country = $event->address->country;
                    $p_date_journey = date('Y-m-d H:i:s', strtotime($event->time_utc));

                    $rs = DB::select("call webhook_trackupdate_17track(?,?,?,?,?,?,?,?,?)", [
                        $p_bill_ref,
                        $carrier_name,
                        $order->order_code,
                        $p_status,
                        $p_note,
                        $p_location,
                        $p_city,
                        $p_country,
                        $p_date_journey
                    ]);
                }
            } catch (\Exception $e) {
                Log::info('----------START LOG WEBHOOK 17TRACK--- (4) Exception: ' . json_encode($e));
            }
        }

        Log::info('----------END LOG WEBHOOK 17TRACK------------');
        exit('End');
    }
}
