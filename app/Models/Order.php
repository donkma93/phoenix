<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    const IMG_FOLDER = 'orders';

    const RATES = 1.05;

    /**
     * Staff Status value
     */
    const STATUS_NEW = 0;
    const STATUS_INPROGRESS = 1;
    const STATUS_DONE = 2;
    const STATUS_CANCEL = 3;

    /**
     * Mapping status name
     */
    public static $statusName = [
        self::STATUS_NEW => 'new',
        self::STATUS_INPROGRESS => 'inprogress',
        self::STATUS_DONE => 'done',
        self::STATUS_CANCEL => 'cancel',
    ];

    /**
     * Payment Status value
     */
    const PAYMENT_PAY = 0;
    const PAYMENT_UNPAY = 1;
    const PAYMENT_PENDING = 2;

    /**
     * Mapping payment name
     */
    public static $paymentName = [
        self::PAYMENT_PAY => 'pay',
        self::PAYMENT_UNPAY => 'unpay',
        self::PAYMENT_PENDING => 'pending'
    ];

    // fulfillment
    const FULFILLED = 0;
    const UNFULFILLED = 1;
    const PENDING = 2;
    const READY_TO_SHIP = 3;
    const DUE_TODAY = 4;
    const SHIP_TODAY = 5;

    /**
     * Mapping fulfillment name
     */
    public static $fulfillName = [
        self::FULFILLED => 'fulfilled',
        self::UNFULFILLED => 'unfulfilled',
        self::PENDING => 'pending',
        self::READY_TO_SHIP => 'ready to ship',
        self::DUE_TODAY => 'due today',
        self::SHIP_TODAY => 'ship today',
    ];

    // picking
    const PICKING_NEW = 0;
    const PICKING_PENDING = 1;
    const PICKING_INTOTE = 2;
    const PICKING_FULFILLED = 3;

    /**
     * Mapping picking name
     */
    public static $pickingName = [
        self::PICKING_NEW => 'new',
        self::PICKING_PENDING => 'pending',
        self::PICKING_INTOTE => 'in tote',
        self::PICKING_FULFILLED => 'fulfilled',
    ];

    //state
    const STATE_NONE = 0;
    const STATE_ON_DOING = 1;
    const STATE_ON_HOLD = 2;
    const STATE_ON_HOLD_VIP = 3;
    const STATE_READY_TO_SHIP = 4;
    const STATE_BACK_ORDER = 5;
    const STATE_DONE = 6;

    /**
     * State name
     */
    public static $stateName = [
        self::STATE_NONE => 'none',
        self::STATE_ON_DOING => 'on doing',
        self::STATE_ON_HOLD => 'on hold',
        self::STATE_ON_HOLD_VIP => 'on hold(VIP)',
        self::STATE_READY_TO_SHIP => 'ready to ship',
        self::STATE_BACK_ORDER => 'back order',
        self::STATE_DONE => 'done',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'shipping_name', 'shipping_street', 'shipping_address1', 'shipping_address2', 'shipping_company', 'shipping_city', 'shipping_zip', 'shipping_province', 'shipping_country', 'shipping_phone',
        'item_quantity', 'item_name', 'item_price', 'item_compare_at_price', 'item_sku', 'item_requires_shipping', 'item_taxable', 'item_fulfillment_status',
        'payment', 'fulfillment', 'ship_rate', 'status', 'content', 'file', 'user_id',
        'order_address_from_id', 'order_address_to_id', 'order_number',
        'picking_status', 'state', 'state_note', 'partner_id', 'partner_code', 'order_code', 'id_price_table'
    ];

    protected $casts = [
        'content' => 'array'
    ];

    /**
     * Order Address From
     */
    public function addressFrom()
    {
        return $this->belongsTo(OrderAddress::class, 'order_address_from_id');
    }

    /**
     * Order Address To
     */
    public function addressTo()
    {
        return $this->belongsTo(OrderAddress::class, 'order_address_to_id');
    }

    /**
     * OrderRates
     */
    public function orderRates()
    {
        return $this->hasMany(OrderRate::class);
    }

    /**
     * Get the profile with the user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the products.
     */
    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    /**
     * Get the package.
     */
    public function orderPackage()
    {
        return $this->hasOne(OrderPackage::class);
    }

    /**
     * Get the label.
     */
    public function orderTransaction()
    {
        return $this->hasOne(OrderTransaction::class);
    }

    public static function getFulfillmentStatus($status)
    {
        switch (strtolower($status)) {
            case self::$fulfillName[self::FULFILLED]:
                return self::FULFILLED;
            case self::$fulfillName[self::UNFULFILLED]:
                return self::UNFULFILLED;
            case self::$fulfillName[self::PENDING]:
                return self::PENDING;
            default:
                return self::UNFULFILLED;
        }
    }

    public function getShippingInfo()
    {
        return [
            'name' => $this->shipping_name,
            'company' => $this->shipping_company,
            'street1' => $this->shipping_street,
            'street2' => $this->shipping_address1,
            'street3' => $this->shipping_address2,
            'city' => $this->shipping_city,
            "state" => $this->shipping_province,
            "zip" => $this->shipping_zip,
            "country" => $this->shipping_country,
            "phone" => $this->shipping_phone,
            // "email" => ''
        ];
    }

    public static function validateAddress($addressInfo)
    {
        $messages = [];

        try {
            $addressInfo['validate'] = true;
            Log::info("VALIDATE ADDR: ".json_encode($addressInfo));
            $data = \Shippo_Address::create($addressInfo);

            if ($data->is_complete == false || $data->validation_results->is_valid == false) {
                foreach ($data->validation_results->messages as $error) {
                    $messages[] = $error->text;
                }
            }

            return [
                'value' => $data,
                'errorMsg' => $messages,
            ];
        } catch (\Exception $e) {


            if (isset($e->jsonBody)) {
                foreach ($e->jsonBody as $fieldError) {
                    if (is_array($fieldError)) {
                        foreach ($fieldError as $errorMsg) {
                            $messages[] = is_string($errorMsg) ? $errorMsg : (string)$errorMsg;
                        }
                    } elseif (is_string($fieldError)) {
                        $messages[] = $fieldError;
                    }
                }
            } else {
                $messages[] = "Address Information is invalid.";
            }

            return [
                'value' => null,
                'errorMsg' => $messages,
            ];
        }
    }
}
