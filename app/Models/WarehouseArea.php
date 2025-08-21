<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseArea extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'warehouse_id', 'barcode'
    ];

    protected $perPage = 20;
    
    /**
     * Get the user's package location.
     */
    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    /**
     * Get the warehouse that owns warehouse area.
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the package history with area.
     */
    public function packageHistory()
    {
        return $this->hasMany(PackageHistory::class);
    }
}
