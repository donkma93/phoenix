<?php

namespace App\Http\Controllers\Staff;

use App\Services\Staff\StaffToteService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StaffToteController extends StaffBaseController
{
    protected $toteService;

    public function __construct(StaffToteService $toteService)
    {
        parent::__construct();

        $this->toteService = $toteService;
    }

    /**
     * View for listing tote
     *
     * @return \Illuminate\View\View
     */
    public function list(Request $request)
    {       
        $input = $request->only('name', 'warehouse', 'barcode', 'status', 'onlyDeleted');
        $info = $this->toteService->list($input);
        
        return view('staff.tote.list', $info);
    }

    /**
     * View for create new tote
     *
     * @return \Illuminate\View\View
     */
    public function new(Request $request)
    {       
        $info = $this->toteService->new();

        return view('staff.tote.new' , $info);
    }

    /**
     * Create tote
     *
     * @param App\Http\Requests\Staff\CreateProductRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {       
        try {
            $parameters = $request->all();
            
            $id = $this->toteService->create($parameters);
            
            return redirect()->route('staff.tote.detail', ['id' => $id])->with('success', "Create tote successfully!");
        } catch(Exception $e) {
            Log::error($e);

            return redirect()->back()->with('fail', "Create tote fail!");
        }
    }

    /**
     * Display a detail of tote
     * 
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function detail(Request $request, $id) 
    {
        $packages = $this->toteService->detail($id);

        return view('staff.tote.detail', $packages);
    }

    /**
     * Update Tote
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $parameters = $request->all();
            $this->toteService->update($parameters);

            return redirect()->back()->with('success', "Update tote successfully!");
        } catch(Exception $e) {
            Log::error($e);
            
            return redirect()->back()->with('fail', "Update tote fail!");
        }
    }
}
