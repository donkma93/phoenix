<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAddress extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'postcode', 'building', 'in_use', 'user_id'
    ];

    /**
     * Get the user that owns the package.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
