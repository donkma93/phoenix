<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestPackage extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'request_package_group_id',
        'received_package_number',
        'received_unit_number',
        'package_number',
        'unit_number',
        'unit_length',
        'unit_weight',
        'barcode'
    ];

    protected $perPage = 20;

    /**
     * Get the request package groups.
     */
    public function requestPackageGroup()
    {
        return $this->belongsTo(RequestPackageGroup::class);
    }
}
