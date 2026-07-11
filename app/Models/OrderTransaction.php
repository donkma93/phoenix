<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class OrderTransaction extends Model
{
    use HasFactory, SoftDeletes;

    const LABEL_FILE_TYPE = 'PDF_4x6';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order_transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id', 'label_url', 'tracking_number', 'tracking_status', 'tracking_url_provider',
        'shipping_name', 'shipping_street', 'shipping_address1', 'shipping_address2', 'shipping_company',
        'shipping_city', 'shipping_zip', 'shipping_province' ,'shipping_country', 'shipping_phone',
        'amount', 'currency', 'transaction_id', 'rate_id', 'order_rate_id'
    ];

    /**
     * Get order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderRate()
    {
        return $this->belongsTo(OrderRate::class);
    }

    public static function createTransaction($rateObjectId, $shippoOrder)
    {
        try {
          
            // Helper để lấy thuộc tính an toàn từ object Shippo (hỗ trợ __get)
            $getProp = function($obj, $prop) {
                if (is_array($obj) && isset($obj[$prop])) return $obj[$prop];
                if (is_object($obj)) {
                    if (isset($obj->$prop)) return $obj->$prop;
                }
                return null;
            };

            // Hàm trích xuất Address an toàn
            $extractAddress = function($addr) use ($getProp) {
                if (is_string($addr)) return $addr;
                if (!$addr) return [];
                $arr = [
                    'name' => $getProp($addr, 'name'),
                    'company' => $getProp($addr, 'company'),
                    'street1' => $getProp($addr, 'street1'),
                    'street2' => $getProp($addr, 'street2'),
                    'city' => $getProp($addr, 'city'),
                    'state' => $getProp($addr, 'state'),
                    'zip' => $getProp($addr, 'zip'),
                    'country' => $getProp($addr, 'country'),
                    'phone' => $getProp($addr, 'phone'),
                    'email' => $getProp($addr, 'email'),
                    'object_id' => $getProp($addr, 'object_id'),
                ];
                return array_filter($arr, function($v) { return !is_null($v) && $v !== ''; });
            };

            // Hàm trích xuất Parcels an toàn
            $extractParcels = function($parcels) use ($getProp) {
                if (!$parcels) return [];
                if (is_string($parcels)) return [$parcels];
                
                $res = [];
                if (is_array($parcels) || $parcels instanceof \Traversable) {
                    foreach ($parcels as $parcel) {
                        if (is_string($parcel)) {
                            $res[] = $parcel;
                            continue;
                        }
                        $arr = [
                            'length' => $getProp($parcel, 'length'),
                            'width' => $getProp($parcel, 'width'),
                            'height' => $getProp($parcel, 'height'),
                            'distance_unit' => $getProp($parcel, 'distance_unit'),
                            'weight' => $getProp($parcel, 'weight'),
                            'mass_unit' => $getProp($parcel, 'mass_unit'),
                            'object_id' => $getProp($parcel, 'object_id'),
                        ];
                        $res[] = array_filter($arr, function($v) { return !is_null($v) && $v !== ''; });
                    }
                }
                return $res;
            };

            $rate = \Shippo_Rate::retrieve($rateObjectId);

            // Lấy thông tin address và parcels từ shippoOrder
            $fromAddress = $shippoOrder ? $getProp($shippoOrder, 'from_address') : null;
            $toAddress = $shippoOrder ? $getProp($shippoOrder, 'to_address') : null;
            
            $weight = $shippoOrder ? $getProp($shippoOrder, 'weight') : null;
            $weightUnit = $shippoOrder ? $getProp($shippoOrder, 'weight_unit') : null;
            $parcels = $weight ? [['weight' => (string)$weight, 'mass_unit' => $weightUnit ?: 'lb']] : [];

            // Fallback: Nếu shippoOrder thiếu thông tin, lấy từ shipment của rate
            if (!$fromAddress || !$toAddress || empty($parcels)) {
                $shipmentId = $getProp($rate, 'shipment');
                if ($shipmentId) {
                    $shipment = \Shippo_Shipment::retrieve($shipmentId);
                    $fromAddress = $fromAddress ?: $getProp($shipment, 'address_from');
                    $toAddress = $toAddress ?: $getProp($shipment, 'address_to');
                    if (empty($parcels)) {
                        $parcels = $getProp($shipment, 'parcels');
                    }
                }
            }

            // Trích xuất dữ liệu chuẩn xác
            $fromAddrArr = $extractAddress($fromAddress);
            $toAddrArr = $extractAddress($toAddress);
            $parcelsArr = $extractParcels($parcels);

            if (!is_array($fromAddrArr)) $fromAddrArr = [];
            if (!is_array($toAddrArr) && empty($toAddrArr)) $toAddrArr = [];

            $serviceLevel = $getProp($rate, 'servicelevel');
            $serviceLevelToken = $getProp($serviceLevel, 'token') ?: 'usps_priority';

 $transaction = null; 
            if (str_contains($serviceLevelToken, 'ups')) {
                $shippoTransactionPayload = [
                'rate'=> $rateObjectId,
                'order_id' => $shippoOrder ? $shippoOrder->object_id : null,
                'label_file_type' => self::LABEL_FILE_TYPE,
                'async'=> false,
                ];

            log::info("shippoTransactionPayload:: ".json_encode($shippoTransactionPayload));

            $transaction = \Shippo_Transaction::create($shippoTransactionPayload);
                
            }
             else {
                  // Xây dựng payload theo kiến trúc Instant Transaction
            $shippoTransactionPayload = array(
                'shipment' => array(
                    'address_from' => array_merge($fromAddrArr, array(
                        'name' => $fromAddrArr['name'] ?? 'Tracy',
                        'street1' => $fromAddrArr['street1'] ?? '123 Main St',
                        'city' => $fromAddrArr['city'] ?? 'San Francisco',
                        'state' => $fromAddrArr['state'] ?? 'CA',
                        'zip' => $fromAddrArr['zip'] ?? '94105',
                        'country' => $fromAddrArr['country'] ?? 'US',
                        'phone' => $fromAddrArr['phone'] ?? '5551234567', // Add this
                        'email' => $fromAddrArr['email'] ?? 'tracy@example.com' // And this
                    )),
                    'address_to' => isset($toAddrArr['object_id']) ? $toAddrArr['object_id'] : $toAddrArr,
                    'parcels' => $parcelsArr,
                ),
                'carrier_account' => $getProp($rate, 'carrier_account') ?? '...', 
                'servicelevel_token' => $serviceLevelToken,
                'label_file_type' => self::LABEL_FILE_TYPE,
                'async' => false,
            );

            $orderId = $shippoOrder ? $getProp($shippoOrder, 'object_id') : null;
            if ($orderId) {
                $shippoTransactionPayload['order'] = $orderId;
            }

            log::info("shippoTransactionPayload:: ".json_encode($shippoTransactionPayload));

            $transaction = \Shippo_Transaction::create($shippoTransactionPayload);
                 
             }
           

            if ($transaction['status'] != 'SUCCESS'){
                $errorMsg = array_map(function($error) {
                    return $error->text;
                }, $transaction['messages']);

                return [
                    'value' => null,
                    'errorMsg' =>  $errorMsg,
                ];
            }

            return [
                'value' => $transaction,
                'errorMsg' => [],
            ];
        } catch (\Exception $e) {
            Log::error($e);

            return [
                'value' => null,
                'errorMsg' => [$e->getMessage()],
            ];
        }
    }
}
