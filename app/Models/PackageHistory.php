<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageHistory extends Model
{
    use SoftDeletes;

    /**
     * Type value
     */
    const TYPE_NONE = 0;
    const TYPE_DELETE = 1;
    const TYPE_RESTORE = 2;

    /**
     * Mapping status name
     */
    public static $typeName = [
        self::TYPE_NONE => 'None',
        self::TYPE_DELETE => 'Delete',
        self::TYPE_RESTORE => 'Restore',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'package_id', 
        'status', 
        'warehouse_area_id', 
        'staff_id', 
        'unit_number', 
        'previous_status', 
        'weight', 
        'height', 
        'length', 
        'width', 
        'weight_staff', 
        'height_staff', 
        'length_staff', 
        'width_staff', 
        'barcode', 
        'previous_created_at', 
        'previous_barcode', 
        'type' , 
        'stage'
    ];

    /**
     * Get the package id.
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Get package location.
     */
    public function warehouseArea()
    {
        return $this->belongsTo(WarehouseArea::class);
    }

        /**
     * Get the profile with the user.
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
