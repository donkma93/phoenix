<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    use HasFactory;

    const TABLE_ACTIVE = 1;
    const TABLE_INACTIVE = 2;
    const FOLDER_DEFAULT = 'documents/price_tables';
    public static $price_table_status = [
        1 => 'active',
        2 => 'inactive'
    ];
}
