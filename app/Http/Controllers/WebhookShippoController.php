<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookShippoController extends Controller
{
    /**
     * @param Request $request
     * @return json
     */
    public function handle_data(Request $request)
    {
        Log::info('----------START LOG WEBHOOK SHIPPO----------: \n' . $request->getContent());

        $data = json_decode($request->getContent());
        $p_code = $data->data->tracking_number; //tracking number carrier (ups,...)
        $p_provider = $data->data->carrier;
        $tracking_status = $data->data->tracking_status->status ?? 'Unknown';
        $list_tracking_status = config('app.tracking_status');
        foreach ($list_tracking_status as $k=>$v) {
            if (strtolower($v) === strtolower($tracking_status)) {
                $tracking_status = $k;
            }
        }

        // Cập nhật lại tracking status (dùng lại cột picking_status để lưu tracking status -> lamdt)
        try {
            Log::info('----------LOG WEBHOOK SHIPPO---------- (1)');
            $rs = DB::select("select order_id, label_url from order_transactions where tracking_number = '$p_code' and shipping_provider = 'SHIPPO'");
            $order_id = null;
            $label_url = null;
            if (!!$rs && is_int($tracking_status)) {
                $order_id = $rs[0]->order_id;
                $label_url = $rs[0]->label_url;

                DB::table('orders')
                    ->where('id', '=', $order_id)
                    ->update([
                        'picking_status' => $tracking_status
                    ]);
            }

            if ($order_id) {
                $resultData = DB::table('users as u')
                    ->select('u.webhook_url', 'o.order_number')
                    ->join('orders as o', 'u.id', 'o.user_id')
                    ->where('o.id', $order_id)
                    ->where('u.deleted_at', null)
                    ->where('o.deleted_at', null)
                    ->first();

                if ($resultData) {
                    $data->customers_order = $resultData->order_number;
                    $data->label_url = $label_url;
                    try {
                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => $resultData->webhook_url,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($data),
                            CURLOPT_HTTPHEADER => array(
                                'Content-Type: application/json'
                            ),
                        ));

                        $response = curl_exec($curl);

                        curl_close($curl);
                        //echo $response;
                        Log::info("OrderID: " . $order_id);
                        Log::info('Webhook Url: ' . $resultData->webhook_url);
                        Log::info($response);

                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                    }

                }
            }
            Log::info('----------LOG WEBHOOK SHIPPO---------- (2)');
        } catch (\Exception $e) {
            Log::error('----------LOG WEBHOOK SHIPPO---------- (3) Exception: ' . json_encode($e));
        }


        // Kiểm tra hành trình xem đã tồn tại chưa, và trả về số lượng hành trình
        // { CALL phoenix.webhook_info_shippo(:p_code,:p_provider) }
        $results = DB::select('call webhook_info_shippo(?,?)', [
            $p_code,
            $p_provider
        ]);

        //echo $results[0]->COUNT;

        // Nếu có hành trình hoặc hành trình thay đổi thì cập nhật hành trình
        if (isset($results[0]->COUNT)) {
            Log::info('----------LOG WEBHOOK SHIPPO---------- (4)');
            $count_events_inserted = $results[0]->COUNT;
            $total_current_events = $data->data->tracking_history;
            $total_current_events = array_reverse($total_current_events);
            for ($i = 0; $i < count($total_current_events) - $count_events_inserted; $i++) {
                $event = $total_current_events[$i];

                $p_bill_ref = $p_code;
                $p_status = $event->status;
                $p_note = $event->status_details;
                $p_location = $event->location->city . ', ' . $event->location->state . ', ' . $event->location->country;
                $p_city = $event->location->city;
                $p_country = $event->location->country;
                $p_date_journey = date('Y-m-d H:i:s', strtotime($event->status_date));

                // { CALL phoenix.webhook_trackupdate_shippo(:p_bill_ref,:p_status,:p_note,:p_location,:p_city,:p_country,:p_date_journey) }
                $rs = DB::select("call webhook_trackupdate_shippo(?,?,?,?,?,?,?)", [
                    $p_bill_ref,
                    $p_status,
                    $p_note,
                    $p_location,
                    $p_city,
                    $p_country,
                    $p_date_journey
                ]);
            }
            Log::info('----------END LOG WEBHOOK SHIPPO----------');
        }

        exit('End');
    }
}
