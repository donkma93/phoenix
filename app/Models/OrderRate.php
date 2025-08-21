<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderRate extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order_rates';

    const RATES = 1.05;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id', 'is_active', 'object_id', 'object_owner', 'shipment', 'attributes',
        'amount', 'currency', 'amount_local', 'currency_local', 'provider', 'provider_image_75', 'provider_image_200',
        'service_name', 'messages', 'estimated_days', 'duration_terms'
    ];

    protected $casts = [
        'attributes' => 'array',
        'messages' => 'array',
    ];

    /**
     * Get order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Use this to display Rate value
     */
    public function getDisplayRate()
    {
        return round($this->amount, 2);
    }
}
