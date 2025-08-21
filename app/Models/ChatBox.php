<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatBox extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
    ];

    protected $perPage = 20;

    /**
     * Get the user request.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user request.
     */
    public function detail()
    {
        return $this->hasMany(ChatBoxDetail::class);
    }
}
