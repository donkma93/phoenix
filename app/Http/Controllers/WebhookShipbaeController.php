<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookShipbaeController extends Controller
{
    /**
     * Handle webhook / tracking updates from Shipbae (Gori).
     */
    public function handleData(Request $request)
    {
        Log::info('----------START LOG WEBHOOK SHIPBAE----------: \n' . $request->getContent());

        try {
            $data = json_decode($request->getContent());
            Log::info('Shipbae Webhook Data:', ['data' => $data]);

            $trackingNumber = null;
            $trackingStatus = 'Unknown';
            $carrier = 'SHIPBAE';

            if (isset($data->tracking_code)) {
                $trackingNumber = $data->tracking_code;
            } elseif (isset($data->tracking_number)) {
                $trackingNumber = $data->tracking_number;
            } elseif (isset($data->data->tracking_code)) {
                $trackingNumber = $data->data->tracking_code;
            } elseif (isset($data->data->tracking_number)) {
                $trackingNumber = $data->data->tracking_number;
            }

            if (isset($data->status)) {
                $trackingStatus = $data->status;
            } elseif (isset($data->data->status)) {
                $trackingStatus = $data->data->status;
            } elseif (isset($data->tracking_status)) {
                $trackingStatus = is_object($data->tracking_status)
                    ? ($data->tracking_status->status ?? 'Unknown')
                    : (string) $data->tracking_status;
            }

            // Map Gori statuses onto existing Shippo-style picking_status codes.
            $statusMap = [
                'delivered' => 1,
                'in_transit' => 2,
                'transit' => 2,
                'preparing' => 0,
                'unknown' => 0,
                'failed' => 3,
                'failure' => 3,
                'voided' => 4,
                'returned' => 4,
                'holding' => 5,
            ];

            $normalizedStatus = strtolower((string) $trackingStatus);
            $trackingStatusCode = $statusMap[$normalizedStatus] ?? null;

            if ($trackingStatusCode === null) {
                $listTrackingStatus = config('app.tracking_status', []);
                foreach ($listTrackingStatus as $k => $v) {
                    if (strtolower($v) === $normalizedStatus) {
                        $trackingStatusCode = $k;
                        break;
                    }
                }
            }

            Log::info('Shipbae Webhook Extracted:', [
                'tracking_number' => $trackingNumber,
                'tracking_status' => $trackingStatus,
                'tracking_status_code' => $trackingStatusCode,
            ]);

            if (!$trackingNumber) {
                Log::warning('Shipbae Webhook: No tracking number found in webhook data');
                return response()->json(['status' => 'ok', 'message' => 'No tracking number found']);
            }

            $rs = DB::select(
                "SELECT order_id, label_url FROM order_transactions WHERE tracking_number = ? AND shipping_provider = 'SHIPBAE'",
                [$trackingNumber]
            );

            $orderId = null;
            $labelUrl = null;

            if (!empty($rs)) {
                $orderId = $rs[0]->order_id;
                $labelUrl = $rs[0]->label_url ?? null;

                if ($orderId && is_int($trackingStatusCode)) {
                    DB::table('orders')
                        ->where('id', '=', $orderId)
                        ->update([
                            'picking_status' => $trackingStatusCode
                        ]);

                    Log::info('Shipbae Webhook: Updated order tracking status', [
                        'order_id' => $orderId,
                        'status' => $trackingStatusCode
                    ]);
                }

                if ($orderId) {
                    $resultData = DB::table('users as u')
                        ->select('u.webhook_url', 'o.order_number')
                        ->join('orders as o', 'u.id', 'o.user_id')
                        ->where('o.id', $orderId)
                        ->where('u.deleted_at', null)
                        ->where('o.deleted_at', null)
                        ->first();

                    if ($resultData && $resultData->webhook_url) {
                        $webhookData = (object) [
                            'event' => 'transaction_updated',
                            'status' => $trackingStatus,
                            'carrier' => 'shipbae',
                            'customers_order' => $resultData->order_number,
                            'label_url' => $labelUrl,
                            'data' => [
                                'tracking_number' => $trackingNumber,
                                'tracking_status' => [
                                    'status' => $trackingStatus
                                ],
                                'carrier' => 'shipbae',
                                'tracking_history' => isset($data->events) ? $data->events : (isset($data->tracking_history) ? $data->tracking_history : [])
                            ],
                            'date_updated' => now()->toIso8601String()
                        ];

                        try {
                            $curl = curl_init();
                            curl_setopt_array($curl, array(
                                CURLOPT_URL => $resultData->webhook_url,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 10,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'POST',
                                CURLOPT_POSTFIELDS => json_encode($webhookData),
                                CURLOPT_HTTPHEADER => array(
                                    'Content-Type: application/json'
                                ),
                            ));

                            $response = curl_exec($curl);
                            curl_close($curl);

                            Log::info('Shipbae Webhook: Forwarded to user webhook', [
                                'order_id' => $orderId,
                                'webhook_url' => $resultData->webhook_url,
                                'response' => $response
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Shipbae Webhook: Error forwarding to user webhook: ' . $e->getMessage());
                        }
                    }
                }
            } else {
                Log::warning('Shipbae Webhook: Order not found for tracking number', ['tracking_number' => $trackingNumber]);
            }

            Log::info('----------END LOG WEBHOOK SHIPBAE----------');

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('----------LOG WEBHOOK SHIPBAE EXCEPTION----------: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
