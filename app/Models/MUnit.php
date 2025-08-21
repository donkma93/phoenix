<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MUnit extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    protected $perPage = 20;

    /**
     * Get the unit price
     */
    public function unitPrices()
    {
        return $this->hasMany(MUnitPrice::class);
    }

    /**
     * Get the warehouse price
     */
    public function warehousePrices()
    {
        return $this->hasMany(WarehouseUnitPrice::class);
    }
}
