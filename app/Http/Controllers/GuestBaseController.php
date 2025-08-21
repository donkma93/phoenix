<?php

namespace App\Http\Controllers;

use App\Services\GuestService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePricingRequest;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class GuestBaseController extends Controller
{
    protected $guestService;

    public function __construct(GuestService $guestService)
    {
        $this->guestService = $guestService;
    }

    /**
     * Update inventory
     *
     * @param App\Http\Requests\CreatePricingRequest $request
     * @return \Illuminate\Http\Response
     */
    public function sendRequest(CreatePricingRequest $request)
    {
        try {
            $parameters = $request->all();
            $this->guestService->sendRequest($parameters);

            return redirect()->back();
        } catch(Exception $e) {
            Log::error($e);
        }
    }

    public function searchEngine(Request $request)
    {
        try {
            Log::info("searchEngine: ");

            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:3',
            ]);
            if (! $token = auth('api')->attempt($validator->validated())) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            Log::info("LOGIN2: ". $token);
            $data = array();
            $type = $request->input('type');
            $code = $request->input('code');
            if ($type == 'tracking_number') {
                $data = $this->guestService->searchEngine($code);
            }
            
            $data['status'] = 'NOT_FOUND';
            return  response()->json($data);
        } catch(Exception $e) {
            Log::error($e);
        }
    }
}
