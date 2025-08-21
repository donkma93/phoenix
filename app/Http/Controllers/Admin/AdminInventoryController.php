<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\AdminInventoryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminInventoryController extends AdminBaseController
{
    protected $inventoryService;

    public function __construct(AdminInventoryService $inventoryService)
    {
        parent::__construct();

        $this->inventoryService = $inventoryService;
    }

    /**
     * View for listing inventory
     *
     * @return \Illuminate\View\View
     */
    public function list(Request $request)
    {       
        $input = $request->only('product', 'sku', 'store');
        $info = $this->inventoryService->list($input);
        
        return view('admin.inventory.list', $info);
    }

    /**
     * Detail inventory
     *
     * @return \Illuminate\View\View
     */
    public function detail($id)
    {  
        $info = $this->inventoryService->detail($id);
        
        return view('admin.inventory.detail', $info);
    }
}
