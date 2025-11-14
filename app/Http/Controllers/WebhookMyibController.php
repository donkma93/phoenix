<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookMyibController extends Controller
{
    /**
     * Handle webhook data from MyIB
     * @param Request $request
     * @return json
     */
    public function handleData(Request $request)
    {
        Log::info('----------START LOG WEBHOOK MYIB----------: \n' . $request->getContent());

        try {
            $data = json_decode($request->getContent());
            
            // Log full webhook data for debugging
            Log::info('MyIB Webhook Data:', ['data' => $data]);

            // Extract tracking number - MyIB might send it in different formats
            $trackingNumber = null;
            $trackingStatus = 'Unknown';
            $carrier = 'MYIB';
            
            // Try different possible structures
            if (isset($data->tracking_number)) {
                $trackingNumber = $data->tracking_number;
            } elseif (isset($data->data->tracking_number)) {
                $trackingNumber = $data->data->tracking_number;
            } elseif (isset($data->usps->tracking_numbers) && is_array($data->usps->tracking_numbers) && count($data->usps->tracking_numbers) > 0) {
                $trackingNumber = $data->usps->tracking_numbers[0];
            } elseif (isset($data->usps->tracking_numbers) && is_string($data->usps->tracking_numbers)) {
                $trackingNumber = $data->usps->tracking_numbers;
            } elseif (isset($data->request_id)) {
                // Fallback to request_id if tracking_number not found
                $trackingNumber = $data->request_id;
            }

            // Extract tracking status
            if (isset($data->status)) {
                $trackingStatus = $data->status;
            } elseif (isset($data->data->status)) {
                $trackingStatus = $data->data->status;
            } elseif (isset($data->tracking_status)) {
                $trackingStatus = is_object($data->tracking_status) ? ($data->tracking_status->status ?? 'Unknown') : $trackingStatus;
            }

            // Map MyIB status to internal tracking status codes
            $list_tracking_status = config('app.tracking_status', []);
            $trackingStatusCode = null;
            foreach ($list_tracking_status as $k => $v) {
                if (strtolower($v) === strtolower($trackingStatus)) {
                    $trackingStatusCode = $k;
                    break;
                }
            }

            Log::info('MyIB Webhook Extracted:', [
                'tracking_number' => $trackingNumber,
                'tracking_status' => $trackingStatus,
                'tracking_status_code' => $trackingStatusCode
            ]);

            if (!$trackingNumber) {
                Log::warning('MyIB Webhook: No tracking number found in webhook data');
                return response()->json(['status' => 'ok', 'message' => 'No tracking number found']);
            }

            // Find order by tracking number
            $rs = DB::select(
                "SELECT order_id, label_url FROM order_transactions WHERE tracking_number = ? AND shipping_provider = 'MYIB'",
                [$trackingNumber]
            );

            $orderId = null;
            $labelUrl = null;
            
            if (!empty($rs)) {
                $orderId = $rs[0]->order_id;
                $labelUrl = $rs[0]->label_url ?? null;

                // Update tracking status in orders table
                if ($orderId && is_int($trackingStatusCode)) {
                    DB::table('orders')
                        ->where('id', '=', $orderId)
                        ->update([
                            'picking_status' => $trackingStatusCode
                        ]);
                    
                    Log::info('MyIB Webhook: Updated order tracking status', [
                        'order_id' => $orderId,
                        'status' => $trackingStatusCode
                    ]);
                }

                // Forward webhook to user's webhook URL if exists
                if ($orderId) {
                    $resultData = DB::table('users as u')
                        ->select('u.webhook_url', 'o.order_number')
                        ->join('orders as o', 'u.id', 'o.user_id')
                        ->where('o.id', $orderId)
                        ->where('u.deleted_at', null)
                        ->where('o.deleted_at', null)
                        ->first();

                    if ($resultData && $resultData->webhook_url) {
                        // Prepare webhook data for user
                        $webhookData = (object) [
                            'event' => 'transaction_updated',
                            'status' => $trackingStatus,
                            'carrier' => 'myib',
                            'customers_order' => $resultData->order_number,
                            'label_url' => $labelUrl,
                            'data' => [
                                'tracking_number' => $trackingNumber,
                                'tracking_status' => [
                                    'status' => $trackingStatus
                                ],
                                'carrier' => 'myib',
                                'tracking_history' => isset($data->tracking_history) ? $data->tracking_history : []
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

                            Log::info('MyIB Webhook: Forwarded to user webhook', [
                                'order_id' => $orderId,
                                'webhook_url' => $resultData->webhook_url,
                                'response' => $response
                            ]);

                        } catch (\Exception $e) {
                            Log::error('MyIB Webhook: Error forwarding to user webhook: ' . $e->getMessage());
                        }
                    }
                }
            } else {
                Log::warning('MyIB Webhook: Order not found for tracking number', ['tracking_number' => $trackingNumber]);
            }

            // Handle tracking history if available (similar to Shippo)
            // Note: MyIB might have different structure, adjust accordingly
            if (isset($data->tracking_history) && is_array($data->tracking_history) && count($data->tracking_history) > 0) {
                // Check if stored procedure exists for MyIB tracking history
                // If not, you may need to create one similar to webhook_info_shippo
                try {
                    // Example: Check if tracking history exists
                    // $results = DB::select('call webhook_info_myib(?,?)', [$trackingNumber, $carrier]);
                    
                    // For now, just log the tracking history
                    Log::info('MyIB Webhook: Tracking history received', [
                        'tracking_number' => $trackingNumber,
                        'history_count' => count($data->tracking_history)
                    ]);
                } catch (\Exception $e) {
                    Log::error('MyIB Webhook: Error processing tracking history: ' . $e->getMessage());
                }
            }

            Log::info('----------END LOG WEBHOOK MYIB----------');

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            Log::error('----------LOG WEBHOOK MYIB EXCEPTION----------: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}

