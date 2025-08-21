<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tote extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Status value
     */
    const STATUS_IN_USE = 0;
    const STATUS_MAINTAIN = 1;

    /**
     * Mapping status name
     */
    public static $statusName = [
        self::STATUS_IN_USE => 'In use',
        self::STATUS_MAINTAIN => 'Maintain'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'warehouse_area_id', 'name', 'barcode', 'status'
    ];

    /**
     * Get the warehouse area id.
     */
    public function warehouseArea()
    {
        return $this->belongsTo(WarehouseArea::class);
    }
}
