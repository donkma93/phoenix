<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestPackageImage extends Model
{
    use HasFactory, SoftDeletes;

    const IMG_FOLDER = 'request_package_images';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'request_package_group_id',
        'image_url'
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
