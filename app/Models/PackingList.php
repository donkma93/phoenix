<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackingList extends Model
{
    use HasFactory;
    protected $table = 'packing_list';
    protected $fillable = ['packing_list_code', 'master_bill', 'status', 'weight', 'quantity', 'finish_date','create_user', 'finish_user', 'created_date', 'from_warehouse', 'to_warehouse'];

    const CREATED = 1;
    const PROCESSING = 5;
    const PACKED = 10;
    const RECEIVING = 11;
    const DONE = 15;

    public static $statusName = [
        self::CREATED => "CREATED",
        self::PROCESSING => "PROCESSING",
        self::RECEIVING => "RECEIVING",
        self::PACKED => "PACKED",
        self::DONE => "DONE",
    ];
}
