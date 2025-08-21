<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    const IMG_FOLDER = 'product_images';

    /**
     * Staff Status value
     */
    const STATUS_ACTIVE = 0;
    const STATUS_DISABLE = 1;
    const STATUS_OUT_STOCK = 2;

    /**
     * Mapping status name
     */
    public static $statusName = [
        self::STATUS_ACTIVE => 'active',
        self::STATUS_DISABLE => 'disable',
        self::STATUS_OUT_STOCK => 'out stock'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'status', 'fulfillment_fee', 'extra_pick_fee', 'category_id', 'package_group_id',  'user_id', 'image_url', 'product_type_id'
    ];

    /**
     * Get the user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get category.
    */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get package group.
     */
    public function packageGroup()
    {
        return $this->belongsTo(PackageGroup::class);
    }

    /**
     * Get package group with trashed.
     */
    public function packageGroupWithTrashed()
    {
        return $this->belongsTo(PackageGroup::class, 'package_group_id')->withTrashed();
    }

    /**
     * Get inventory.
     */
    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }
}
