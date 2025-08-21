<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTypeAttr extends Model
{
    use HasFactory;

    protected $table = 'product_type_attr';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_type_id',
        'name_attribute',
        'user_id'
    ];
}
