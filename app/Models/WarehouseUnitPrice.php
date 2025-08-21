<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseUnitPrice extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'warehouse_id', 'm_unit_id', 'price'
    ];

    protected $perPage = 20;
    
    /**
     * Get the unit price
     */
    public function unit()
    {
        return $this->belongsTo(MUnit::class , 'm_unit_id');
    }

    /**
     * Get the warehouse
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
