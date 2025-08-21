<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductAttribute extends Model
{
    use HasFactory;
    protected $table = 'product_attribute';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'product_type_id',
        'name_attribute',
        'value_attribute',
        'user_id'
    ];
}
