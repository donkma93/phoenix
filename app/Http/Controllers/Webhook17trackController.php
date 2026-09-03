<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Webhook17trackController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleData(Request $request)
    {
        Log::info('----------START LOG WEBHOOK 17TRACK----------');

        $webhook_data = json_decode($request->getContent());
        if (!$webhook_data || !isset($webhook_data->data->number)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid payload'], 422);
        }

        $p_code = $webhook_data->data->number; // Master bill
        $carrier_name = $webhook_data->data->track_info->tracking->providers[0]->provider->name
            ?? ($webhook_data->data->carrier ?? 'UNKNOWN');
        $tracking_status = $webhook_data->data->track_info->latest_status->status ?? 'TRANSIT';

        try {
            $packing_list_code = DB::table('packing_list')->where('master_bill', $p_code)->value('packing_list_code');
            if (!$packing_list_code) {
                Log::warning('17track webhook: packing list not found for master bill', ['bill' => $p_code]);
                return response()->json(['status' => 'ok', 'message' => 'Packing list not found']);
            }

            $list_order = DB::select('call search_packinglist_detail(?)', [$packing_list_code]);
        } catch (\Exception $e) {
            Log::error('17track webhook resolve packing list failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Internal error'], 500);
        }

        if (empty($list_order)) {
            return response()->json(['status' => 'ok', 'message' => 'No orders']);
        }

        $events = $webhook_data->data->track_info->tracking->providers[0]->events ?? [];

        foreach ($list_order as $order) {
            try {
                // Update carrier tracking on transactions — do NOT overwrite warehouse picking_status
                DB::table('order_transactions')
                    ->where('order_id', $order->id)
                    ->update([
                        'tracking_status' => $tracking_status,
                        'shipping_carrier' => $carrier_name,
                        'updated_at' => now(),
                    ]);

                $count_events_inserted = DB::table('order_tracking_journey')->where([
                    ['bill_code_ref', $p_code],
                    ['carrier', $carrier_name],
                    ['bill_code', $order->order_code],
                ])->count();

                $toInsert = count($events) - $count_events_inserted;
                for ($i = 0; $i < $toInsert; $i++) {
                    $event = $events[$i];
                    $address = $event->address ?? null;
                    $p_location = $address
                        ? (($address->city ?? '') . ', ' . ($address->state ?? '') . ', ' . ($address->country ?? ''))
                        : '';

                    DB::select('call webhook_trackupdate_17track(?,?,?,?,?,?,?,?,?)', [
                        $p_code,
                        $carrier_name,
                        $order->order_code,
                        'TRANSIT',
                        $event->description ?? null,
                        $p_location,
                        $address->city ?? null,
                        $address->country ?? null,
                        isset($event->time_utc) ? date('Y-m-d H:i:s', strtotime($event->time_utc)) : null,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('17track webhook order update failed: ' . $e->getMessage(), [
                    'order_id' => $order->id ?? null,
                ]);
            }
        }

        Log::info('----------END LOG WEBHOOK 17TRACK----------');
        return response()->json(['status' => 'ok']);
    }
}
