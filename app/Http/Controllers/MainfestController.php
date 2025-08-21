<?php

namespace App\Http\Controllers;

use App\Services\Staff\StaffMainfestService;
use App\Services\User\UserPickupRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MainfestController extends Controller
{
    protected $mainfestService;

    public function __construct(StaffMainfestService $mainfestService)
    {
        $this->mainfestService = $mainfestService;
    }

    public function index(Request $requeset)
    {
        Log::info("Mainfest Controller Indexx");
        $data = $this->mainfestService->index($requeset);
        return view('staff.mainfest.index', compact('data'));
    }

    public function create()
    {
        $data = $this->mainfestService->create();
        $orders = $data['orders'];
        $warehouse = $data['warehouse'];
        return view('staff.mainfest.create', compact('orders', 'warehouse'));
    }

    public function store(Request $request)
    {
        if (count($request['order_ids']) <= 0) {
            return redirect()->route('staff.mainfest.create');
        }

        $this->mainfestService->store($request);
        return redirect()->route('staff.mainfest.index');
    }

    public function listDetail($mainfest_id)
    {
        $list = $this->mainfestService->getMNFDetail($mainfest_id);
        return view('staff.mainfest.mnfdetail', compact('list'));
    }
}
