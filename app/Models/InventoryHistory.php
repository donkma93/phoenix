<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryHistory extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inventory_id', 'user_id', 'hour', 'incoming', 'available', 'previous_incoming', 'previous_available', 'start_at'
    ];

    /**
     * Get products.
    */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    /**
     * Get store.
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
