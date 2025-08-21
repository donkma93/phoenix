<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MRequestType extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * Get the request with the user.
     */
    public function requests()
    {
        return $this->hasMany(UserRequest::class);
    }

    /**
     * Unit Price
     */
    public function unitPrice()
    {
        return $this->hasMany(UnitPrice::class);
    }
}
