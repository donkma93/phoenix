<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderProduct extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order_product';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id', 'product_id', 'quantity', 'total_fee',
    ];

    /**
     * Get order.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get product.
    */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }
}
