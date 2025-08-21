<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageGroup extends Model
{
    use HasFactory, SoftDeletes;

    const FILE_FOLDER = 'package_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'user_id', 'barcode', 'file', 'unit_width', 'unit_height', 'unit_length', 'unit_size', 'unit_weight',
    ];


    /**
     * Get package history.
     */
    public function packages()
    {
        return $this->hasMany(Package::class);
    }

     /**
     * Get package detail.
     */
    public function packageDetails()
    {
        return $this->hasMany(PackageDetail::class);
    }

    /**
     * Get package history.
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

     /**
     * Get product.
     */
    public function product()
    {
        return $this->hasOne(Product::class);
    }
}
