<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestWorkingTime extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_request_id', 'start_at', 'finish_at'
    ];

    protected $perPage = 20;

    /**
     * Get the user request.
     */
    public function userRequest()
    {
        return $this->belongsTo(UserRequest::class);
    }
}
