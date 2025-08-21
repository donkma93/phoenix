<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainfestDetail extends Model
{
    use HasFactory;
    protected $table = 'mainfest_detail';
    protected $fillable = [
       'mainfest_id',
        'order_id',
        'receive_date',
        'user_id'
    ];
}
