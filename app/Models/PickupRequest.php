<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PickupRequest extends Model
{
    use HasFactory;
    protected $table = 'pickup_request';
    protected $fillable = ['pickup_code', 'status', 'user_create', 'id_warehouse', 'action_date', 'finish_user', 'created_date', 'partner_code', 'partner_id'];

    const NEW = 10;
    const PICKING = 11;
    const PICKED = 15;
    const DONE = 20;

    public static $statusName = [
        self::NEW => 'NEW',
        self::PICKING => 'PICKING',
        self::PICKED => 'PICKED',
        self::DONE => 'DONE',
    ];


    public function warehouses(): HasOne
    {
        return $this->hasOne(Warehouse::class, 'id');
    }
}
