<?php

namespace App\Services\Admin;

use App\Services\AdminBaseServiceInterface;
use App\Models\PricingRequest;

class AdminBaseService implements AdminBaseServiceInterface
{
    function notification() {
        $totalPricingRequest = PricingRequest::where('is_done', 0)->count();

        return [ 
            'request' => $totalPricingRequest,
        ];
    }
}
