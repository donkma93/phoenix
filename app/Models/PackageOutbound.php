<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageOutbound extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'package_id', 'address'
    ];

    /**
     * Get the package id.
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
