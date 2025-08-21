<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitPrice extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'm_request_type_id', 'min_unit', 'max_unit', 'hour', 'weight', 'length', 'min_size_price', 'max_size_price'
    ];

    protected $perPage = 20;

    /**
     * Get the unit price
     */
    public function mRequestType()
    {
        return $this->belongsTo(MRequestType::class);
    }

    /**
     * Get the warehouse price
     */
    public function warehousePrices()
    {
        return $this->hasMany(WarehouseUnitPrice::class);
    }
}
