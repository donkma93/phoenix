<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageGroupHistory extends Model
{
    use SoftDeletes;

    /**
     * Type value
     */
    const CREATE = 0;
    const UPDATE = 1;
    const DELETE = 2;
    const RESTORE = 3;

    /**
     * Mapping type name
     */
    public static $typeName = [
        self::CREATE => 'Create',
        self::UPDATE => 'Update',
        self::DELETE => 'Delete',
        self::RESTORE => 'Restore',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'package_group_id', 'previous_user_id', 'user_id', 'previous_name', 'name', 'previous_barcode', 'barcode', 'unit_size', 'unit_weight', 'unit_length', 'unit_height', 'unit_width', 'previous_unit_weight', 'previous_unit_height', 'previous_unit_length', 'previous_unit_size', 'previous_unit_width', 'staff_id', 'stage', 'type'
    ];

    
    /**
     * Get Group.
     */
    public function group()
    {
        return $this->hasMany(PackageGroup::class)->withTrashed();
    }

    /**
     * Get previous user.
    */
    public function previousUser()
    {
        return $this->belongsTo(User::class, 'previous_user_id')->withTrashed();
    }

    /**
     * Get user.
    */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Get staff.
    */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id')->withTrashed();
    }
}
