<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfile extends Model
{
    use HasFactory, SoftDeletes;

    const IMG_FOLDER = 'user_profiles';

    /**
     * The path to the "profile" route for your application.
     */
    public static $profileRoute = [
        User::ROLE_ADMIN => 'admin.dashboard',
        User::ROLE_STAFF => 'staff.setting.profile',
        User::ROLE_PICKER => 'staff.setting.profile',
        User::ROLE_PACKER => 'staff.setting.profile',
        User::ROLE_RECEIVER => 'staff.setting.profile',
        User::ROLE_USER => 'setting.profile.index'
    ];

    /**
     * Gender value
     */
    const GENDER_MALE = 0;
    const GENDER_FEMALE = 1;

    /**
     * Name of gender
     */
    public static $genderName = [
        self::GENDER_MALE => 'male',
        self::GENDER_FEMALE => 'female'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'birthday',  'avatar', 'phone', 'gender', 'company_name'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'birthday' => 'datetime',
        'membership_at' => 'datetime',
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
