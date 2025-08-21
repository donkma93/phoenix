<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements MustVerifyEmail, JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'role',
        'partner_id',
        'partner_code',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *2ee2
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $perPage = 20;

    /**
     * Role value
     */
    const ROLE_ADMIN = 0;
    const ROLE_STAFF = 1;
    const ROLE_USER = 2;
    const ROLE_PICKER = 3;
    const ROLE_PACKER = 4;
    const ROLE_RECEIVER = 5;

    /**
     * The path to the "home" route for your application.
     */
    public static $home = [
        self::ROLE_ADMIN => 'admin.dashboard',
        self::ROLE_STAFF => 'staff.dashboard',
        self::ROLE_RECEIVER => 'staff.dashboard',
        self::ROLE_PICKER => 'staff.dashboard',
        self::ROLE_PACKER => 'staff.dashboard',
        self::ROLE_USER => 'dashboard'
    ];

    /**
     * Name of roles
     */
    public static $roleName = [
        self::ROLE_ADMIN => 'admin',
        self::ROLE_STAFF => 'staff',
        self::ROLE_PICKER => 'picker',
        self::ROLE_PACKER => 'packer',
        self::ROLE_RECEIVER => 'receiver',
        self::ROLE_USER => 'user'
    ];

      /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }

    /**
     * Get the profile with the user.
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    // Warehouse
    public function warehouse()
    {
        return $this->hasOne(Warehouse::class);
    }

    // Partner
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Get the package with the user.
     */
    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    /**
     * Get the package history with the user.
     */
    public function packageHistory()
    {
        return $this->hasMany(PackageHistory::class);
    }

    /**
     * Get the request with the user.
     */
    public function requests()
    {
        return $this->hasMany(UserRequest::class);
    }

    /**
     * Get the address with the user.
     */
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    /**
     * Get the address with the user.
     */
    public function staffs()
    {
        return $this->hasMany(UserRequest::class, `staff_id`);
    }

    public function isUser()
    {
        return $this->role == self::ROLE_USER;
    }

    public function isStaff()
    {
        return in_array($this->role, [self::ROLE_PICKER, self::ROLE_PACKER, self::ROLE_RECEIVER, self::ROLE_STAFF]);
    }
}
