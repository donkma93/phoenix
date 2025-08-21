<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserInvoice extends Model
{
    use SoftDeletes;

    const IMG_FOLDER = 'user_invoices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'month', 'year', 'file', 'inbound', 'outbound', 'relabel', 'repack', 'removal', 'return', 'storage', 'warehouse_labor', 'tax', 'balance'
    ];

    /**
     * Get the user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
