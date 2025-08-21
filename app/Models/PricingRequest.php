<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingRequest extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'note',
        'is_done',
        'email', 
        'company', 
        'name', 
        'phone',
        'services'
    ];

    /**
     * Get the user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
