<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestHistory extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'request_package_id',
        'staff_id',
        'package_number',
        'unit_number',
        'package_id',
    ];

    protected $perPage = 20;

    /**
     * Get the request package groups.
     */
    public function requestPackage()
    {
        return $this->belongsTo(RequestPackage::class);
    }

    /**
     * Get the staff id.
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
    
    /**
     * Get the package id.
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
