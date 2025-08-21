<?php

namespace App\Http\Controllers\Staff;

use App\Services\Staff\StaffScannerService;
use Illuminate\Http\Request;

class StaffScannerController extends StaffBaseController
{
    protected $scannerService; 

    public function __construct(StaffScannerService $scannerService)
    {
        parent::__construct();
        
        $this->scannerService = $scannerService;
    }

    /**
     * Display staff dashboard
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('staff.scanner.index');
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
                return redirect()->route('staff.package-group.detail', ['id' => $result['id']]);
            } elseif ($result['type'] == 'package'){
                return redirect()->route('staff.package.detail', ['id' => $result['id']]);
            } elseif ($result['type'] == 'store'){
                return redirect()->route('staff.storeFulfill.detail', ['id' => $result['id']]);
            } elseif ($result['type'] == 'tote') {
                return redirect()->route('staff.tote.detail', ['id' => $result['id']]);
            } else {
                return redirect()->route('staff.warehouseArea.detail', ['id' => $result['id']]);
            }
        }

        return redirect()->back()->with('fail', "Barcode not existed or had been delete!");
    }
}
