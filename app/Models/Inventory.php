<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id', 'sku', 'incoming', 'available', 'store_fulfill_id'
    ];

    /**
     * Get products.
    */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get store.
    */
    public function storeFulfill()
    {
        return $this->belongsTo(StoreFulfill::class);
    }
}
