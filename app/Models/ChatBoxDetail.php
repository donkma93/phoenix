<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatBoxDetail extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'chat_box_id',
        'from_user_id',
        'message',
        'read_at',
        'user_get',
        'staff_get'
    ];

    protected $perPage = 20;

    /**
     * Get chat box .
     */
    public function chatBox()
    {
        return $this->belongsTo(ChatBox::class);
    }

    /**
     * Message from.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }
}
