<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestPackageGroup extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_request_id',
        'package_group_id',
        'barcode',
        'file',      // file barcode
        'quantity',   // for outbound inventory
        'is_insurance',
        'insurance_fee', // raw fee
        'ship_mode',
    ];

    protected $perPage = 20;

    /**
     * Get the user request.
     */
    public function userRequest()
    {
        return $this->belongsTo(UserRequest::class);
    }

    /**
     * Get the package group.
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
     * Get the request packages.
     */
    public function requestPackages()
    {
        return $this->hasMany(RequestPackage::class);
    }

    /**
     * Get the request package trackings.
     */
    public function requestPackageTrackings()
    {
        return $this->hasMany(RequestPackageTracking::class);
    }

    /**
     * Get the request package images.
     */
    public function requestPackageImages()
    {
        return $this->hasMany(RequestPackageImage::class);
    }
}
