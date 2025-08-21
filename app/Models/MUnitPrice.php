<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MUnitPrice extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'price', 'm_unit_id'
    ];

    /**
     * Get the unit of the price.
     */
    public function mUnit()
    {
        return $this->belongsTo(MUnit::class);
    }
}
