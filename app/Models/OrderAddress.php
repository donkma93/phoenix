<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class OrderAddress extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order_addresses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'object_id', 'city', 'company', 'country',
        'email', 'name', 'phone', 'state', 'street1', 'street2', 'street3', 'street_no',
        'zip', 'is_residential'
    ];

    /**
     * Get order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getShippingInfo()
    {
        return [
            'city' => $this->city,
            'company' => $this->company,
            "country" => $this->country,
            "email" => $this->email,
            'name' => $this->name,
            "phone" => $this->phone,
            "state" => $this->province,
            'street1' => $this->street1,
            'street2' => $this->street2,
            'street3' => $this->street3,
            "zip" => $this->zip,
        ];
    }

    public static function validateAddress($addressInfo)
    {
        $messages = [];

        try {
            $addressInfo['validate'] = true;
            $data = \Shippo_Address::create($addressInfo);

            if ($data->is_complete == false || $data->validation_results->is_valid == false) {
                foreach ($data->validation_results->messages as $error) {
                    $messages[] = $error->text;
                }
            }

            return [
                'value' => $data,
                'errorMsg' => $messages,
            ];
        } catch (\Exception $e) {
            Log::error('IMPORT ORDER ERROR: ' . json_encode($addressInfo));
            Log::error($e);

            if (isset($e->jsonBody)) {
                if (is_array($e->jsonBody)) {
                    foreach ($e->jsonBody as $fieldError) {
                        if (is_array($fieldError)) {
                            foreach ($fieldError as $errorMsg) {
                                $messages[] = $errorMsg;
                            }
                        } else {
                            $messages[] = $fieldError;
                        }
                    }
                } else {
                    $messages[] = $e->jsonBody;
                }
            } else {
                $messages[] = ["Address Information is invalid."];
            }

            return [
                'value' => null,
                'errorMsg' => $messages,
            ];
        }
    }
}
