<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainfestUs extends Model
{
    use HasFactory;
    protected $table = 'mainfest_us';
    const FILE_FOLDER = 'mainfest_us';

    const NEW = 0;
    const DONE = 5;

    public static $statusName = [
        self::NEW => "CREATED",
        self::DONE => "DONE",
    ];
    
    protected $fillable = [
        'code',
        'provider',
        'status',
        'user_create',
        'item_count',
        'id_warehouse',
        'file_mainfest'
    ];
}
