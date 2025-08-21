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
          
            $shippoTransactionPayload = [
                'rate'=> $rateObjectId,
                'order_id' => $shippoOrder ? $shippoOrder->object_id : null,
                'label_file_type' => self::LABEL_FILE_TYPE,
                'async'=> false,
            ];

            log::info("shippoTransactionPayload:: ".json_encode($shippoTransactionPayload));

            $transaction = \Shippo_Transaction::create($shippoTransactionPayload);

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
