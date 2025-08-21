<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\AdminScannerService;
use Illuminate\Http\Request;

class AdminScannerController extends AdminBaseController
{
    protected $scannerService; 

    public function __construct(AdminScannerService $scannerService)
    {
        parent::__construct();
        
        $this->scannerService = $scannerService;
    }

    /**
     * Display admin dashboard
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('admin.scanner.index');
    }

    /**
     * Check barcode
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function checkBarcode(Request $request) 
    {
        $parameter =  $request->only('barcode');
        $result = $this->scannerService->scanner($request);
        
        if($result['status'] == 'success') {
            if($result['type'] == 'group') {
                return redirect()->route('admin.package-group.detail', ['id' => $result['id']]);
            } elseif ($result['type'] == 'package'){
                return redirect()->route('admin.package.detail', ['id' => $result['id']]);
            } elseif ($result['type'] == 'store'){
                return redirect()->route('admin.storeFulfill.detail', ['id' => $result['id']]);
            } elseif ($result['type'] == 'tote') {
                return redirect()->route('admin.tote.detail', ['id' => $result['id']]);
            } else {
                return redirect()->route('admin.warehouseArea.detail', ['id' => $result['id']]);
            }
        }

        return redirect()->back()->with('fail', "Barcode not existed or had been delete!");
    }
}
