<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'address', 'wh_code'
    ];

    protected $perPage = 20;

    /**
     * Get the warehouse price
     */
    public function warehousePrices()
    {
        return $this->hasMany(WarehouseUnitPrice::class);
    }

     /**
     * Get the warehouse price
     */
    public function areas()
    {
        return $this->hasMany(WarehouseArea::class);
    }
}
