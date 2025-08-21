<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KitComponent extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id', 'quantity', 'on_hand', 'component_id'
    ];

    /**
     * Get the product id.
     */
    public function product()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the component id.
     */
    public function component()
    {
        return $this->belongsTo(Product::class, 'component_id');
    }
}
