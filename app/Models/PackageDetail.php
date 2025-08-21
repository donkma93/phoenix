<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageDetail extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'package_id', 'package_group_id', 'unit_number', 'received_unit_number'
    ];

    /**
     * Get package .
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Get package group.
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
}
