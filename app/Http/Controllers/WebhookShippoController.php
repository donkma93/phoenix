<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookShippoController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle_data(Request $request)
    {
        Log::info('----------START LOG WEBHOOK SHIPPO----------');

        $data = json_decode($request->getContent());
        if (!$data || !isset($data->data->tracking_number)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid payload'], 422);
        }

        $p_code = $data->data->tracking_number;
        $p_provider = $data->data->carrier ?? null;
        $tracking_status_label = $data->data->tracking_status->status ?? 'Unknown';

        // Map label → code for storage on order_transactions.tracking_status (NOT picking_status)
        $list_tracking_status = config('app.tracking_status', []);
        $tracking_status_code = null;
        foreach ($list_tracking_status as $k => $v) {
            if (strtolower((string) $v) === strtolower((string) $tracking_status_label)) {
                $tracking_status_code = $k;
                break;
            }
        }

        try {
            // Prepared statement — no string interpolation
            $rs = DB::select(
                'SELECT order_id, label_url FROM order_transactions WHERE tracking_number = ? AND shipping_provider = ?',
                [$p_code, 'SHIPPO']
            );

            $order_id = null;
            $label_url = null;
            if (!empty($rs)) {
                $order_id = $rs[0]->order_id;
                $label_url = $rs[0]->label_url;

                // Update tracking on transaction table only — do not overwrite warehouse picking_status
                DB::table('order_transactions')
                    ->where('order_id', $order_id)
                    ->where('tracking_number', $p_code)
                    ->update([
                        'tracking_status' => $tracking_status_label,
                        'updated_at' => now(),
                    ]);
            }

            if ($order_id) {
                $resultData = DB::table('users as u')
                    ->select('u.webhook_url', 'o.order_number')
                    ->join('orders as o', 'u.id', 'o.user_id')
                    ->where('o.id', $order_id)
                    ->whereNull('u.deleted_at')
                    ->whereNull('o.deleted_at')
                    ->first();

                if ($resultData && !empty($resultData->webhook_url)) {
                    $data->customers_order = $resultData->order_number;
                    $data->label_url = $label_url;
                    try {
                        $curl = curl_init();
                        curl_setopt_array($curl, [
                            CURLOPT_URL => $resultData->webhook_url,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_TIMEOUT => 10,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($data),
                            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                        ]);
                        $response = curl_exec($curl);
                        curl_close($curl);
                        Log::info('Shippo forward webhook', [
                            'order_id' => $order_id,
                            'response' => $response,
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Shippo forward webhook failed: ' . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Webhook Shippo exception: ' . $e->getMessage());
        }

        try {
            $results = DB::select('call webhook_info_shippo(?,?)', [
                $p_code,
                $p_provider,
            ]);

            if (isset($results[0]->COUNT) && isset($data->data->tracking_history) && is_array($data->data->tracking_history)) {
                $count_events_inserted = (int) $results[0]->COUNT;
                $total_current_events = array_reverse($data->data->tracking_history);
                $toInsert = count($total_current_events) - $count_events_inserted;
                for ($i = 0; $i < $toInsert; $i++) {
                    $event = $total_current_events[$i];
                    $location = $event->location ?? null;
                    $p_location = $location
                        ? (($location->city ?? '') . ', ' . ($location->state ?? '') . ', ' . ($location->country ?? ''))
                        : '';
                    DB::select('call webhook_trackupdate_shippo(?,?,?,?,?,?,?)', [
                        $p_code,
                        $event->status ?? null,
                        $event->status_details ?? null,
                        $p_location,
                        $location->city ?? null,
                        $location->country ?? null,
                        isset($event->status_date) ? date('Y-m-d H:i:s', strtotime($event->status_date)) : null,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Webhook Shippo history exception: ' . $e->getMessage());
        }

        Log::info('----------END LOG WEBHOOK SHIPPO----------');

        return response()->json(['status' => 'ok']);
    }
}
