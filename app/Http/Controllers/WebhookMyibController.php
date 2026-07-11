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
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleData(Request $request)
    {
        Log::info('----------START LOG WEBHOOK MYIB----------');

        try {
            $data = json_decode($request->getContent());
            if (!$data) {
                return response()->json(['status' => 'error', 'message' => 'Invalid payload'], 422);
            }

            $trackingNumber = null;
            $trackingStatus = 'Unknown';

            if (isset($data->tracking_number)) {
                $trackingNumber = $data->tracking_number;
            } elseif (isset($data->data->tracking_number)) {
                $trackingNumber = $data->data->tracking_number;
            } elseif (isset($data->usps->tracking_numbers) && is_array($data->usps->tracking_numbers) && count($data->usps->tracking_numbers) > 0) {
                $trackingNumber = $data->usps->tracking_numbers[0];
            } elseif (isset($data->usps->tracking_numbers) && is_string($data->usps->tracking_numbers)) {
                $trackingNumber = $data->usps->tracking_numbers;
            } elseif (isset($data->request_id)) {
                $trackingNumber = $data->request_id;
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

            if (!$trackingNumber) {
                Log::warning('MyIB Webhook: No tracking number found');
                return response()->json(['status' => 'ok', 'message' => 'No tracking number found']);
            }

            $rs = DB::select(
                'SELECT order_id, label_url FROM order_transactions WHERE tracking_number = ? AND shipping_provider = ?',
                [$trackingNumber, 'MYIB']
            );

            $orderId = null;
            $labelUrl = null;

            if (!empty($rs)) {
                $orderId = $rs[0]->order_id;
                $labelUrl = $rs[0]->label_url ?? null;

                // Update tracking status on transaction only — never overwrite warehouse picking_status
                DB::table('order_transactions')
                    ->where('order_id', $orderId)
                    ->where('tracking_number', $trackingNumber)
                    ->update([
                        'tracking_status' => $trackingStatus,
                        'updated_at' => now(),
                    ]);

                $resultData = DB::table('users as u')
                    ->select('u.webhook_url', 'o.order_number')
                    ->join('orders as o', 'u.id', 'o.user_id')
                    ->where('o.id', $orderId)
                    ->whereNull('u.deleted_at')
                    ->whereNull('o.deleted_at')
                    ->first();

                if ($resultData && !empty($resultData->webhook_url)) {
                    $webhookData = (object) [
                        'event' => 'transaction_updated',
                        'status' => $trackingStatus,
                        'carrier' => 'myib',
                        'customers_order' => $resultData->order_number,
                        'label_url' => $labelUrl,
                        'data' => [
                            'tracking_number' => $trackingNumber,
                            'tracking_status' => ['status' => $trackingStatus],
                            'carrier' => 'myib',
                            'tracking_history' => isset($data->tracking_history) ? $data->tracking_history : [],
                        ],
                        'date_updated' => now()->toIso8601String(),
                    ];

                    try {
                        $curl = curl_init();
                        curl_setopt_array($curl, [
                            CURLOPT_URL => $resultData->webhook_url,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_TIMEOUT => 10,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($webhookData),
                            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                        ]);
                        curl_exec($curl);
                        curl_close($curl);
                    } catch (\Exception $e) {
                        Log::error('MyIB Webhook forward failed: ' . $e->getMessage());
                    }
                }
            } else {
                Log::warning('MyIB Webhook: Order not found', ['tracking_number' => $trackingNumber]);
            }

            Log::info('----------END LOG WEBHOOK MYIB----------');
            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('MyIB Webhook exception: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Internal error'], 500);
        }
    }
}
