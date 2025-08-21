<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderJourney extends Model
{
    use HasFactory;
    protected $table = 'order_journey';
    protected $fillable = [
        'order_id',
        'id_pickup_request',
        'status',
        'id_packing_list',
        'from_warehouse',
        'to_warehouse',
        'user_create',
        'status',
        'inout_type',
        'created_date',
        'order_code',
        'created_username',
        'is_mainfest_status',
    ];

    // "STATUS
    // 10: waitting pickup
    // 15: picked
    // 20: packing
    // 30: receiving"
    const WAITTING = 10;
    const PICKED = 15;
    const PACKING = 20;
    const RECEIVING = 30;

    const INOUT_TYPE_CREATED = 1;
    const INOUT_TYPE_PICKED = 2;

    const MAINFEST_CREATED = 0;
    const MAINFEST_PROCESSING = 1;
    const MAINFEST_RECEIVED = 5;

    
    public static $statusName = [
        self::WAITTING => 'WAITTING',
        self::PICKED => 'PICKED',
        self::PACKING => 'PACKING',
        self::RECEIVING => 'RECEIVING',
    ];

    public static $inoutName = [
        self::INOUT_TYPE_CREATED => 'CREATED',
        self::INOUT_TYPE_PICKED => 'PICKED',
    ];

    public function pickupRequest(): HasOne
    {
        return $this->hasOne(PickupRequest::class, 'id', 'id_pickup_request');
    }
}
