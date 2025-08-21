<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderPackage extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order_package';


    // size type
    const SIZE_CM = 0; // Cm
    const SIZE_IN = 1; // Inch

    /**
     * Mapping size name
     */
    public static $sizeName = [
        self::SIZE_CM => 'cm',
        self::SIZE_IN => 'in',
    ];

    // size type
    // const WEIGHT_G = 0;
    // const WEIGHT_KG = 0;
    const WEIGHT_OZ = 0; // Ounce
    const WEIGHT_LB = 1; // Pounch

    /**
     * Mapping weight name
     */
    public static $weightName = [
        self::WEIGHT_OZ => 'oz',
        self::WEIGHT_LB => 'lb',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id', 'width', 'height', 'length', 'weight', 'size_type', 'weight_type'
    ];

    /**
     * Get order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function validateParce()
    {
        return $this->length != null && $this->width != null && $this->height != null && $this->size_type != null
            && $this->weight != null && $this->weight_type != null;
    }

    public function getParcelInfo()
    {
        return [
            "length" => $this->length,
            "width" => $this->width,
            "height" => $this->height,
            "distance_unit" => self::$sizeName[$this->size_type],
            "weight" => $this->weight,
            "mass_unit" => self::$weightName[$this->weight_type],
        ];
    }
}
