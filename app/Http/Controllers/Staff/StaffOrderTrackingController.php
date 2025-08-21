<?php

namespace App\Http\Controllers\Staff;

use App\Services\Staff\StaffOrderTrackingService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StaffOrderTrackingController extends StaffBaseController
{
    protected $service;

    public function __construct(StaffOrderTrackingService $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    /**
     * View for listing partner
     *
     * @return \Illuminate\View\View
     */
    public function list(Request $request)
    {       
        $input = $request->only('order_number', 'tracking_code', 'date_from', 'date_to');
        $info = $this->service->list($input);
        
        return view('staff.order-tracking.list', $info);
    }

    /**
     * List for print
     *
     * @return \Illuminate\View\View
     */
    public function print(Request $request)
    {       
        $input = $request->input('label_list');

        if (!$input) {
            return back();
        }
        // $input_list = explode(",", $input);
        
        return view('staff.order-tracking.print', [
            'label_list' => $input
        ]);
    }


    /**
     * View for create new partner
     *
     * @return \Illuminate\View\View
     */
    public function new(Request $request)
    {       
        $info = $this->service->new();

        return view('staff.order-tracking.new' , $info);
    }

    /**
     * Create partner
     *
     * @param App\Http\Requests\Admin\CreateProductRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {       
        try {
            $parameters = $request->all();
            
            $id = $this->service->create($parameters);
            
            return redirect()->route('staff.order-tracking.detail', ['id' => $id])->with('success', "Create partner successfully!");
        } catch(Exception $e) {
            Log::error($e);

            return redirect()->back()->with('fail', "Create partner fail!");
        }
    }

    /**
     * Display a detail of partner
     * 
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function detail(Request $request, $id) 
    {
        $packages = $this->service->detail($id);

        return view('staff.order-tracking.detail', $packages);
    }

    /**
     * Update Partner
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $parameters = $request->all();
            $this->service->update($parameters);

            return redirect()->back()->with('success', "Update partner successfully!");
        } catch(Exception $e) {
            Log::error($e);
            
            return redirect()->back()->with('fail', "Update partner fail!");
        }
    }

    /**
     * Delete Partner
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        try {
            $parameters = $request->all();
            $this->service->delete($parameters);

            return redirect()->back()->with('success', "Update partner successfully!");
        } catch(Exception $e) {
            Log::error($e);
            
            return redirect()->back()->with('fail', "Update partner fail!");
        }
    }
}
