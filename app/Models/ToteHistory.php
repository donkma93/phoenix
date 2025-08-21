<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ToteHistory extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_request_id', 'package_group_id', 'quantity', 'picker_id', 'packer_id'
    ];

    /**
     * Get the picker id.
     */
    public function picker()
    {
        return $this->belongsTo(User::class, 'picker_id');
    }

    /**
     * Get the packer id.
     */
    public function packer()
    {
        return $this->belongsTo(User::class, 'packer_id');
    }

    /**
     * Get the user request id.
     */
    public function userRequest()
    {
        return $this->belongsTo(UserRequest::class);
    }

    /**
     * Get the package group id.
     */
    public function packageGroup()
    {
        return $this->belongsTo(PackageGroup::class);
    }
}
