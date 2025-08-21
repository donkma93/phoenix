<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRequest extends Model
{
    use HasFactory, SoftDeletes;

    const IMG_FOLDER = 'user_requests';

    /**
     * Status value
     */
    const STATUS_NEW = 0;
    const STATUS_INPROGRESS = 1;
    const STATUS_DONE = 2;
    const STATUS_CANCEL = 3;

    const INSURANCE_PERCENT = 7;

    /**
     * Mapping status name
     */
    public static $statusName = [
        self::STATUS_NEW => 'New',
        self::STATUS_INPROGRESS => 'Inprogress',
        self::STATUS_DONE => 'Done',
        self::STATUS_CANCEL => 'Cancel',
    ];

    /**
     * Option value
     */
    const OPTION_TIME = 0;
    const OPTION_QUANTITY = 1;

     /**
     * Name of Option
     */
    public static $optionName = [
        self::OPTION_TIME => 'Time',
        self::OPTION_QUANTITY => 'Quantity'
    ];

    /**
     * Unit size type
     */
    const SIZE_INCH = 0;
    const SIZE_CM = 1;

     /**
     * Name of Unit size
     */
    public static $sizeName = [
        self::SIZE_INCH => 'Inch',
        self::SIZE_CM => 'Cm'
    ];

    /**
     * Unit weight type
     */
    const WEIGHT_KG = 0;
    const WEIGHT_POUND = 1;

    /**
     * Name of Unit weight
     */
    public static $weightName = [
        self::WEIGHT_KG => 'Kg',
        self::WEIGHT_POUND => 'Pound'
    ];

    /**
     * Shipmode
     */
    const SMALL_PARCEL = 0;
    const LESS_THAN_TRUCKLOAD = 1;
    const CONTAINER = 2;

    public static $shipModes = [
        self::SMALL_PARCEL => 'Small Parcel',
        self::LESS_THAN_TRUCKLOAD => "Less Than Truckload"
    ];

    public static $receivingShipModes = [
        self::SMALL_PARCEL => 'Small Parcel',
        self::LESS_THAN_TRUCKLOAD => "Less Than Truckload",
        self::CONTAINER => "Container"
    ];

    /**
     * Packing mode
     */
    const EVERY_BOX = 0;
    const MULTIPLE_SKU = 1;
    const ONE_SKU = 2;

    public static $packingTypes = [
        self::EVERY_BOX => 'Everything in one box',
        self::MULTIPLE_SKU => 'Multiple SKUs per box',
        self::ONE_SKU => 'One SKU per box',
    ];

    /**
     * Prep
     */

    const NO_PREP = 0;
    const HAS_PREP = 1;

    public static $prepTypes = [
        self::NO_PREP => 'No prep needed',
        self::HAS_PREP => 'Prep required',
    ];

    /**
     * Who label unit
     */

    const BY_SELLER = 0;
    const BY_WAREHOUSE = 1;

    public static $labelByTypes = [
        self::BY_SELLER => "By seller",
        self::BY_WAREHOUSE => "By Warehouse ($0.50 per unit fee)",
    ];

    const STORAGE = 0;
    const FULFILL = 1;

    public static $storeTypes = [
        self::STORAGE => "Storage",
        self::FULFILL => "Fulfill",
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'm_request_type_id',
        'user_id',
        'option',
        'note',
        'status',
        'file',
        'packages',
        'total_package',

        'address_from_id',
        'address_to_id',

        'packing_type',
        'prep_type',
        'label_by_type',
        'store_type',
        'ship_coming',
        'ship_mode',

        'is_insurance',
        'is_allow'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'start_at' => 'datetime',
        'finish_at' => 'datetime',
    ];

    protected $perPage = 20;

    /**
     * Get the profile with the user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the type of request.
     */
    public function mRequestType()
    {
        return $this->belongsTo(MRequestType::class);
    }

    /**
     * Get the staff id.
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Get the request package groups
     */
    public function requestPackageGroups()
    {
        return $this->hasMany(RequestPackageGroup::class);
    }

     /**
     * Get the request package
     */
    public function requestPackages()
    {
        return $this->hasMany(Package::class);
    }
}
