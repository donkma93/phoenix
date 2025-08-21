<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\AdminPartnerService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminPartnerController extends AdminBaseController
{
    protected $partnerService;

    public function __construct(AdminPartnerService $partnerService)
    {
        parent::__construct();

        $this->partnerService = $partnerService;
    }

    /**
     * View for listing partner
     *
     * @return \Illuminate\View\View
     */
    public function list(Request $request)
    {       
        $input = $request->only('partner_code', 'partner_name', 'phone');
        $info = $this->partnerService->list($input);
        
        return view('admin.partner.list', $info);
    }

    /**
     * View for create new partner
     *
     * @return \Illuminate\View\View
     */
    public function new(Request $request)
    {       
        $info = $this->partnerService->new();

        return view('admin.partner.new' , $info);
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
            
            $id = $this->partnerService->create($parameters);
            
            return redirect()->route('admin.partner.detail', ['id' => $id])->with('success', "Create partner successfully!");
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
        $packages = $this->partnerService->detail($id);

        return view('admin.partner.detail', $packages);
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
            $this->partnerService->update($parameters);

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
            $this->partnerService->delete($parameters);

            return redirect()->back()->with('success', "Update partner successfully!");
        } catch(Exception $e) {
            Log::error($e);
            
            return redirect()->back()->with('fail', "Update partner fail!");
        }
    }
}
