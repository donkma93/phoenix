<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestPackageTracking extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'request_package_group_id',
        'tracking_url'
    ];

    protected $perPage = 20;

    /**
     * Get the request package group.
     */
    public function packageGroup()
    {
        return $this->belongsTo(RequestPackageGroup::class);
    }
}
