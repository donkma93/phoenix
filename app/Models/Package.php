<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Status value
     */
    const STATUS_SHIPPING = 0;
    const STATUS_INBOUND = 1;
    const STATUS_STORED = 2;
    const STATUS_OUTBOUND = 3;
    const STATUS_RECEIVED = 4;

    /**
     * Mapping status name
     */
    public static $statusName = [
        self::STATUS_SHIPPING => 'None',
        self::STATUS_INBOUND => 'Inbound',
        self::STATUS_STORED => 'Stored',
        self::STATUS_OUTBOUND => 'Outbound',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'user_id',
        'user_request_id',
        'package_group_id',
        'warehouse_area_id',
        'status',
        'barcode',
        'unit_number',
        'received_unit_number',
        'weight',
        'height',
        'width',
        'length',
        'unit_barcode',
        'weight_staff',
        'height_staff',
        'length_staff',
        'width_staff',
    ];

    /**
     * Get the package group that owns the package.
     */
    public function packageGroup()
    {
        return $this->belongsTo(PackageGroup::class);
    }

    /**
     * Get the package group with trashed.
     */
    public function packageGroupWithTrashed()
    {
        return $this->belongsTo(PackageGroup::class, 'package_group_id')->withTrashed();
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
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get package history.
    */
    public function histories()
    {
        return $this->hasMany(PackageHistory::class);
    }

    /**
     * Get package detail.
     */
    public function packageDetails()
    {
        return $this->hasMany(PackageDetail::class);
    }

    /**
     * Get outbound.
     */
    public function outbound()
    {
        return $this->hasOne(PackageOutbound::class);
    }

    /**
     * Get package detail.
    */
    public function details()
    {
        return $this->hasMany(PackageDetail::class);
    }
}
